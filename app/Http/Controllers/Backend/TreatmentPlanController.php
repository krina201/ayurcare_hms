<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TreatmentPlan;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\MasterRoom;
use App\Models\MasterTreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class TreatmentPlanController extends Controller
{
    /**
     * Display a listing of treatment plans
     */
    public function index(Request $request)
    {
        $pagename = 'Treatment Plan';
        $breadcrumb = 'Treatment Plan List';

        // Get recent treatment plans for the table with soft deleted patients
        $recentTreatmentPlans = TreatmentPlan::with(['patient' => function ($query) {
            $query->withTrashed(); // Include soft deleted patients
        }, 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get master data for dropdowns
        $treatmentCategories = MasterTreatmentCategory::where('status', 1)->get();
        $oils = Medicine::where('status', 1)->where('medicine_type_id', 3)->get(); // Oils from medicines table
        $herbs = Medicine::where('status', 1)->where('medicine_type_id', 11)->get(); // Herbs from medicines table
        $rooms = MasterRoom::where('status', 1)->get();
        $therapists = Doctor::where('status', 1)->get();
        $patients = Patient::all();

        return view('backend.treatment_plan.index', compact(
            'pagename',
            'breadcrumb',
            'recentTreatmentPlans',
            'treatmentCategories',
            'oils',
            'herbs',
            'rooms',
            'therapists',
            'patients'
        ));
    }

    /**
     * Store a newly created treatment plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'treatment_category' => 'required|exists:master_treatment_categories,id',
            'procedure_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'dosha_report' => 'required|string',
            'oils_required' => 'nullable|array',
            'oils_required.*' => 'exists:medicines,id',
            'herbs_required' => 'nullable|array',
            'herbs_required.*' => 'exists:medicines,id',
            'special_instructions' => 'nullable|string',
            'recommended_therapist' => 'nullable|exists:doctors,id',
            'room_allocation' => 'nullable|exists:master_rooms,id',
            'day_wise_schedule' => 'nullable',
            'consent_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ], [
            'patient_id.required' => 'Patient selection is required.',
            'patient_id.exists' => 'Selected patient does not exist.',
            'treatment_category.required' => 'Treatment category is required.',
            'treatment_category.exists' => 'Selected treatment category does not exist.',
            'procedure_name.required' => 'Procedure name is required.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'dosha_report.required' => 'Dosha report is required.',
            'recommended_therapist.exists' => 'Selected therapist does not exist.',
            'room_allocation.exists' => 'Selected room does not exist.',
            'consent_file.mimes' => 'Consent file must be PDF, JPG, JPEG, or PNG.',
            'consent_file.max' => 'Consent file size must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();

            // Handle consent file upload
            $consentFilePath = null;
            if ($request->hasFile('consent_file')) {
                $file = $request->file('consent_file');
                $fileName = 'consent_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                // Move file directly into public/backend-assets/media/uploads
                $consentFilePath = $file->move(public_path('backend-assets/media/uploads/treatment'), $fileName);

                // If you want to store only relative path in DB
                $consentFilePath = 'backend-assets/media/uploads/treatment/' . $fileName;
            }


            // Handle arrays - they come as arrays from FormData
            $oilsRequired = $data['oils_required'] ?? [];
            $herbsRequired = $data['herbs_required'] ?? [];

            // Handle day_wise_schedule - it might come as JSON string
            $dayWiseSchedule = [];
            if (!empty($data['day_wise_schedule'])) {
                if (is_string($data['day_wise_schedule'])) {
                    $dayWiseSchedule = json_decode($data['day_wise_schedule'], true) ?? [];
                } else {
                    $dayWiseSchedule = $data['day_wise_schedule'];
                }
            }

            // Create treatment plan
            $treatmentPlan = TreatmentPlan::create([
                'patient_id' => $data['patient_id'],
                'created_by' => Auth::id(),
                'treatment_category' => $data['treatment_category'],
                'procedure_name' => $data['procedure_name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'dosha_report' => $data['dosha_report'],
                'oils_required' => $oilsRequired,
                'herbs_required' => $herbsRequired,
                'special_instructions' => $data['special_instructions'] ?? null,
                'recommended_therapist' => $data['recommended_therapist'] ?? null,
                'room_allocation' => $data['room_allocation'] ?? null,
                'day_wise_schedule' => $dayWiseSchedule,
                'consent_file_path' => $consentFilePath,
                'status' => $request->has('save_as_draft') ? 0 : 1, // 0=draft, 1=active
                'type' => $request->has('save_as_draft') ? 0 : 1, // 0=draft, 1=saved
            ]);

            DB::commit();

            $message = $request->has('save_as_draft')
                ? 'Treatment plan saved as draft successfully.'
                : 'Treatment plan created successfully.';

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'treatment_plan' => $treatmentPlan->load('patient')
                ]);
            }

            // For non-AJAX requests, redirect with session message
            return redirect()->route('treatment-plan')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Treatment plan creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create treatment plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit form on the same index page
     */
    public function edit(TreatmentPlan $treatmentPlan)
    {
        $pagename = 'Treatment Plan';
        $breadcrumb = 'Treatment Plan List';

        // Get recent treatment plans for the table with soft deleted patients
        $recentTreatmentPlans = TreatmentPlan::with(['patient' => function ($query) {
            $query->withTrashed(); // Include soft deleted patients
        }, 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get master data for dropdowns
        $treatmentCategories = MasterTreatmentCategory::where('status', 1)->get();
        $oils = Medicine::where('status', 1)->where('medicine_type_id', 3)->get(); // Oils from medicines table
        $herbs = Medicine::where('status', 1)->where('medicine_type_id', 11)->get(); // Herbs from medicines table
        $rooms = MasterRoom::where('status', 1)->get();
        $therapists = Doctor::where('status', 1)->get();
        $patients = Patient::all();

        // Pass the treatment plan to edit
        $editTreatmentPlan = $treatmentPlan;

        return view('backend.treatment_plan.index', compact(
            'pagename',
            'breadcrumb',
            'recentTreatmentPlans',
            'treatmentCategories',
            'oils',
            'herbs',
            'rooms',
            'therapists',
            'patients',
            'editTreatmentPlan'
        ));
    }

    /**
     * Update the specified treatment plan
     */
    public function update(Request $request, TreatmentPlan $treatmentPlan)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'treatment_category' => 'required|exists:master_treatment_categories,id',
            'procedure_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'dosha_report' => 'required|string',
            'oils_required' => 'nullable|array',
            'oils_required.*' => 'exists:medicines,id',
            'herbs_required' => 'nullable|array',
            'herbs_required.*' => 'exists:medicines,id',
            'special_instructions' => 'required|string',
            'recommended_therapist' => 'required|exists:doctors,id',
            'room_allocation' => 'required|exists:master_rooms,id',
            'day_wise_schedule' => 'nullable',
            'consent_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        $data = $validator->validated();

        // Handle consent file upload
        $consentFilePath = $treatmentPlan->consent_file_path; // Keep existing file by default
        if ($request->hasFile('consent_file')) {
            // Delete old file if exists
            if ($treatmentPlan->consent_file_path && file_exists(public_path($treatmentPlan->consent_file_path))) {
                unlink(public_path($treatmentPlan->consent_file_path));
            }

            $file = $request->file('consent_file');
            $fileName = 'consent_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend-assets/media/uploads/treatment'), $fileName);
            $consentFilePath = 'backend-assets/media/uploads/treatment/' . $fileName;
        }

        // Handle arrays
        $oilsRequired = $data['oils_required'] ?? [];
        $herbsRequired = $data['herbs_required'] ?? [];

        // Handle day_wise_schedule
        $dayWiseSchedule = [];
        if (!empty($data['day_wise_schedule'])) {
            if (is_string($data['day_wise_schedule'])) {
                $dayWiseSchedule = json_decode($data['day_wise_schedule'], true) ?? [];
            } else {
                $dayWiseSchedule = $data['day_wise_schedule'];
            }
        }

        // Update treatment plan
        $treatmentPlan->update([
            'patient_id' => $data['patient_id'],
            'treatment_category' => $data['treatment_category'],
            'procedure_name' => $data['procedure_name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'dosha_report' => $data['dosha_report'],
            'oils_required' => $oilsRequired,
            'herbs_required' => $herbsRequired,
            'special_instructions' => $data['special_instructions'],
            'recommended_therapist' => $data['recommended_therapist'],
            'room_allocation' => $data['room_allocation'],
            'day_wise_schedule' => $dayWiseSchedule,
            'consent_file_path' => $consentFilePath,
            'status' => $request->has('save_as_draft') ? 0 : 1, // 0=draft, 1=active
            'type' => $request->has('save_as_draft') ? 0 : 1, // 0=draft, 1=saved
        ]);

        DB::commit();

        $message = $request->has('save_as_draft')
            ? 'Treatment plan draft updated successfully.'
            : 'Treatment plan updated successfully.';

        return redirect()->route('treatment-plan')->with('success', $message);
    }

    // Delete the specified treatment plan
    public function destroy(TreatmentPlan $treatmentPlan)
    {
        // Delete consent file if exists
        if ($treatmentPlan->consent_file_path && file_exists(public_path($treatmentPlan->consent_file_path))) {
            unlink(public_path($treatmentPlan->consent_file_path));
        }

        // delete treatment plan from database
        if ($treatmentPlan->delete()) {

            return redirect()->route('treatment-plan')->with('success', 'treatment plan deleted Successfully');
        }
        return redirect()->route('treatment-plan')->with('error', 'Failed to delete treatment plan');
    }


    //  Search patient by UHID or name
    public function searchPatient(Request $request)
    {
        try {
            // Log the incoming request
            Log::info("Treatment plan patient search request received", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                Log::info("Treatment plan patient search: Query too short or empty", ['query' => $query]);
                return response()->json([]);
            }

            // Sanitize the query to prevent SQL injection
            $query = strip_tags($query);

            // Log the search attempt for debugging
            Log::info("Treatment plan patient search attempt", [
                'query' => $query,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            // Check if Patient model exists and is accessible
            if (!class_exists(Patient::class)) {
                Log::error("Patient model not found");
                throw new \Exception("Patient model not accessible");
            }

            // Check database connection
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                Log::error("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }

            // Search for patients with better error handling
            $patients = Patient::where(function ($q) use ($query) {
                $q->where('uhid', 'LIKE', "%{$query}%")
                    ->orWhere('full_name', 'LIKE', "%{$query}%")
                    ->orWhere('mobile', 'LIKE', "%{$query}%");
            })
                ->select([
                    'id',
                    'uhid',
                    'full_name',
                    'gender',
                    'age',
                    'mobile',
                    'prakriti',
                    'allergies',
                    'photo_path'
                ])
                ->limit(10)
                ->get();

            // Log the search results for debugging
            Log::info("Treatment plan patient search results", [
                'query' => $query,
                'count' => $patients->count(),
                'results' => $patients->toArray()
            ]);

            // If no results found, return empty array
            if ($patients->isEmpty()) {
                Log::info("Treatment plan patient search: No results found", ['query' => $query]);
                return response()->json([]);
            }

            // Format the results
            $formattedPatients = $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'uhid' => $patient->uhid ?? 'N/A',
                    'full_name' => $patient->full_name ?? 'N/A',
                    'gender' => $patient->gender ?? 'N/A',
                    'age' => $patient->age ?? 'N/A',
                    'mobile' => $patient->mobile ?? 'N/A',
                    'prakriti' => $patient->prakriti ?? 'N/A',
                    'allergies' => $patient->allergies ?? 'None',
                    'photo_path' => $patient->photo_path ?? null
                ];
            });

            Log::info("Treatment plan patient search: Returning formatted results", [
                'query' => $query,
                'formatted_count' => $formattedPatients->count()
            ]);

            return response()->json($formattedPatients);
        } catch (\Exception $e) {
            Log::error('Treatment plan patient search error: ' . $e->getMessage(), [
                'query' => $request->get('query'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'An error occurred while searching patients',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }



    /**
     * Get therapists by treatment category
     */
    public function getTherapistsByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');

        if (!$categoryId) {
            return response()->json([]);
        }

        // Get doctors who have this treatment category in their panchkarma_treatments
        $therapists = Doctor::where('status', 1)
            ->whereJsonContains('panchkarma_treatments', (int)$categoryId)
            ->select('id', 'full_name', 'specialty')
            ->get();

        return response()->json($therapists);
    }

    /**
     * Update treatment plan status
     */
    public function updateStatus(Request $request, TreatmentPlan $treatmentPlan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2,3', // 0=pending, 1=active, 2=completed, 3=cancelled
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value'
            ], 422);
        }

        try {
            $treatmentPlan->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment plan status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
