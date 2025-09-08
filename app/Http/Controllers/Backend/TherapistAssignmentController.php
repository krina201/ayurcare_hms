<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TherapistAssignment;
use App\Models\TreatmentPlan;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\MasterRoom;
use App\Models\MasterTreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\LogHelper;

class TherapistAssignmentController extends Controller
{
    /**
     * Display the therapist assignment dashboard
     */
    public function index(Request $request)
    {
        $pagename = 'Therapist Assignment';
        $breadcrumb = 'Assignment Dashboard';

        try {
            // Get pending treatments from treatment plans with detailed patient info
            $pendingTreatments = TreatmentPlan::with([
                'patient' => function ($query) {
                    $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'prakriti', 'doshas', 'allergies', 'photo_path');
                },
                'treatmentCategory'
            ])
                ->where('status', 1) // Active treatment plans
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get today's active assignments
            $activeAssignments = TherapistAssignment::with(['patient', 'therapist', 'room', 'treatmentPlan'])
                ->whereDate('assignment_date', today())
                ->whereIn('status', [0, 1]) // Pending, In Progress
                ->orderBy('start_time')
                ->get();

            // Get available therapists with their department
            $therapists = Doctor::with(['department'])
                ->where('status', 1)
                ->get();

            // Get available rooms
            $rooms = MasterRoom::where('status', 1)->get();

            // Get treatment categories
            $treatmentCategories = MasterTreatmentCategory::where('status', 1)->get();

            return view('backend.doctor.therapist_assignment', compact(
                'pagename',
                'breadcrumb',
                'pendingTreatments',
                'activeAssignments',
                'therapists',
                'rooms',
                'treatmentCategories'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Therapist Assignment Index Error: ' . $e->getMessage());

            // Return with empty data to prevent complete failure
            return view('backend.doctor.therapist_assignment', [
                'pagename' => $pagename,
                'breadcrumb' => $breadcrumb,
                'pendingTreatments' => collect(),
                'activeAssignments' => collect(),
                'therapists' => collect(),
                'rooms' => collect(),
                'treatmentCategories' => collect()
            ]);
        }
    }

    /**
     * Store a new therapist assignment
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'treatment_plan_id' => 'required|exists:treatment_plans,id',
                'therapist_id' => 'required|exists:doctors,id',
                'room_id' => 'nullable|exists:master_rooms,id',
                'assignment_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'treatment_details' => 'required|string',
                'materials_required' => 'nullable|string',
                'special_instructions' => 'nullable|string'
            ],
            [
                // Custom validation error messages
                'treatment_plan_id.required' => 'Treatment Plan selection is required.',
                'treatment_plan_id.exists' => 'Selected treatment plan does not exist.',

                'therapist_id.required' => 'Therapist selection is required.',
                'therapist_id.exists' => 'Selected therapist does not exist.',

                'room_id.exists' => 'Selected room does not exist.',

                'assignment_date.required' => 'Assignment date is required.',
                'assignment_date.date' => 'Please provide a valid assignment date.',

                'start_time.required' => 'Start time is required.',
                'start_time.date_format' => 'Please provide a valid start time.',

                'end_time.required' => 'End time is required.',
                'end_time.date_format' => 'Please provide a valid end time.',
                'end_time.after' => 'End time must be after start time.',

                'treatment_details.required' => 'Treatment details field is required.',
                'treatment_details.string' => 'Treatment details must be a valid string.'
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Get treatment plan and patient info
        $treatmentPlan = TreatmentPlan::with('patient')->findOrFail($validatedData['treatment_plan_id']);

        // Calculate duration
        $startTime = Carbon::createFromFormat('H:i', $validatedData['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $validatedData['end_time']);
        $durationMinutes = $endTime->diffInMinutes($startTime);

        // Create new assignment instance
        $assignment = new TherapistAssignment();
        $assignment->treatment_plan_id = $validatedData['treatment_plan_id'];
        $assignment->patient_id = $treatmentPlan->patient_id;
        $assignment->therapist_id = $validatedData['therapist_id'];
        $assignment->room_id = $validatedData['room_id'];
        $assignment->assigned_by = Auth::id();
        $assignment->assignment_date = $validatedData['assignment_date'];
        $assignment->start_time = $validatedData['start_time'];
        $assignment->end_time = $validatedData['end_time'];
        $assignment->duration_minutes = $durationMinutes;
        $assignment->treatment_details = $validatedData['treatment_details'];
        $assignment->materials_required = $validatedData['materials_required'];
        $assignment->special_instructions = $validatedData['special_instructions'];
        $assignment->status = 0; // Pending

        // Save the assignment
        if ($assignment->save()) {
            LogHelper::logActivity('Insert', $assignment->id, 'therapist_assignment', $assignment->toArray());

            return redirect()->route('doctor.therapist-assignment')->with('success', 'Therapist assigned successfully');
        } else {
            return redirect()->route('doctor.therapist-assignment')->with('error', 'Something went wrong while saving the assignment');
        }
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, TherapistAssignment $assignment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1,2,3,4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment status updated successfully.',
            'assignment' => $assignment->fresh()
        ]);
    }

    /**
     * Get detailed patient and therapist information for assignment
     */
    public function getAssignmentDetails(Request $request)
    {
        try {
            $treatmentId = $request->get('treatment_id');
            $therapistId = $request->get('therapist_id');

            $data = [];

            // Get detailed treatment and patient information
            if ($treatmentId) {
                $treatment = TreatmentPlan::with([
                    'patient' => function ($query) {
                        $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'prakriti', 'doshas', 'allergies', 'photo_path', 'mobile', 'address');
                    },
                    'treatmentCategory'
                ])->find($treatmentId);

                if ($treatment) {
                    // Add oils data to treatment
                    $treatment->oils = $treatment->oils ?? [];
                    $data['treatment'] = $treatment;
                    $data['patient'] = $treatment->patient;
                }
            }

            // Get detailed therapist information with categories
            if ($therapistId) {
                $therapist = Doctor::with(['department'])
                    ->find($therapistId);

                if ($therapist) {
                    $data['therapist'] = $therapist;
                    $data['therapist_categories'] = $therapist->panchkarmaTreatmentCategories();
                    $data['therapist_expertise'] = $therapist->expertiseAreas();
                }
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Get Assignment Details Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch assignment details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get therapists filtered by treatment category
     */
    public function getTherapistsByTreatmentCategory(Request $request)
    {
        try {
            $treatmentCategoryId = $request->get('treatment_category_id');

            if (!$treatmentCategoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Treatment category ID is required'
                ], 400);
            }

            // Get therapists whose panchkarma_treatments or expertise_areas contain this treatment category
            $therapists = Doctor::with(['department'])
                ->where('status', 1)
                ->where(function ($query) use ($treatmentCategoryId) {
                    $query->whereJsonContains('panchkarma_treatments', (int)$treatmentCategoryId)
                        ->orWhereJsonContains('expertise_areas', (int)$treatmentCategoryId);
                })
                ->get();

            // Add categories and expertise to each therapist
            $therapists->each(function ($therapist) {
                $therapist->panchkarma_categories = $therapist->panchkarmaTreatmentCategories();
                $therapist->expertise_areas = $therapist->expertiseAreas();
            });

            return response()->json([
                'success' => true,
                'therapists' => $therapists
            ]);
        } catch (\Exception $e) {
            Log::error('Get Therapists by Treatment Category Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch therapists',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending treatments for assignment
     */
    public function getPendingTreatments(Request $request)
    {
        $query = $request->get('query', '');

        $treatments = TreatmentPlan::with(['patient', 'treatmentCategory'])
            ->where('status', 1)
            ->when($query, function ($q) use ($query) {
                $q->whereHas('patient', function ($patientQuery) use ($query) {
                    $patientQuery->where('full_name', 'LIKE', "%{$query}%")
                        ->orWhere('uhid', 'LIKE', "%{$query}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($treatments);
    }
}
