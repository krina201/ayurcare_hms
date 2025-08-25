<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


class PatientController extends Controller
{
    // Controller constructor to apply middleware for permission-based access control.
    public function __construct()
    {
        $this->middleware('permission:view-patient')->only('index');
        $this->middleware('permission:create-patient')->only('store');
        $this->middleware('permission:edit-patient')->only('edit');
        $this->middleware('permission:delete-patient')->only('destroy');
    }


    //  Display the patient registration page.
    public function index(Request $request)
    {
        $patients = Patient::latest()->limit(10)->get();
        $stats = $this->getDashboardStats();

        // Build search results (optional filters)
        $searchQuery = Patient::query();

        if ($request->filled('uhid')) {
            $searchQuery->where('uhid', 'like', '%' . $request->input('uhid') . '%');
        }
        if ($request->filled('full_name')) {
            $searchQuery->where('full_name', 'like', '%' . $request->input('full_name') . '%');
        }
        if ($request->filled('mobile')) {
            $searchQuery->where('mobile', 'like', '%' . $request->input('mobile') . '%');
        }
        if ($request->filled('visit_date')) {
            $searchQuery->whereDate('registration_date', $request->input('visit_date'));
        }
        $types = (array) $request->input('types', []);
        if (!empty($types)) {
            $searchQuery->whereIn('registration_type', $types);
        }

        $perPage = (int) $request->input('per_page', 25);
        if ($perPage < 1) {
            $perPage = 25;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }
        $searchResults = $searchQuery->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('backend.patients.index', compact('patients', 'stats', 'searchResults'));
    }

    //   Store a newly created patient in storage.
    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'age' => 'required|integer|min:0|max:150',
                'mobile' => 'required|digits:10',
                'emergency_contact' => 'required|digits:10',
                'aadhaar_number' => 'required|digits:12',
                'address' => 'required|string',
                'allergies' => 'required|string|max:255',
                'prakriti' => 'required|string|in:Vata,Pitta,Kapha,Vata-Pitta,Pitta-Kapha,Vata-Kapha,Vata-Pitta-Kapha',
                'doshas' => 'required|array|min:1',
                'doshas.*' => 'required|in:Vata,Pitta,Kapha',
                'registration_date' => 'required|date',
                'registration_type' => 'required|in:OPD,IPD',
                'photo' => 'required|image|max:2048',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Store photo under public/backend-assets/media/uploads/products
        $photo = $request->file('photo');
        $destinationPath = public_path('backend-assets/media/uploads/products');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $photoFilename = 'patient_' . now()->format('YmdHis') . '_' . Str::random(6) . '.' . $photo->getClientOriginalExtension();
        $photo->move($destinationPath, $photoFilename);
        $photoPath = 'backend-assets/media/uploads/products/' . $photoFilename;

        $uhid = $this->generateUhid();

        $patient = Patient::create([
            'uhid' => $uhid,
            'full_name' => $validated['full_name'],
            'gender' => $validated['gender'],
            'age' => $validated['age'],
            'mobile' => $validated['mobile'],
            'emergency_contact' => $validated['emergency_contact'],
            'aadhaar_number' => $validated['aadhaar_number'],
            'address' => $validated['address'],
            'allergies' => $validated['allergies'],
            'prakriti' => $validated['prakriti'],
            'doshas' => $validated['doshas'],
            'registration_date' => $validated['registration_date'],
            'registration_type' => $validated['registration_type'],
            'photo_path' => $photoPath,
            'created_by' => Auth::id(),

        ]);

        // Save the user
        if ($patient->save()) {
            LogHelper::logActivity('Insert', $patient->id, 'patient', $patient->toArray());  // For Insert 

            return redirect()->route('patients')->with('success', 'Patient added successfully');
        } else {
            return redirect()->route('patients')->with('error', 'Something went wrong while saving the Patient');
        }
    }

    //  Show the form for editing the specified patient.
    public function edit(Patient $patient)
    {
        $patients = Patient::latest()->limit(10)->get();
        $stats = $this->getDashboardStats();

        return view('backend.patients.index', compact('patients', 'stats', 'patient'));
    }

    //  Update the specified patient in storage.
    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'age' => 'required|integer|min:0|max:150',
                'mobile' => 'required|digits:10',
                'emergency_contact' => 'required|digits:10',
                'aadhaar_number' => 'required|digits:12',
                'address' => 'required|string',
                'allergies' => 'required|string|max:255',
                'prakriti' => 'required|string|in:Vata,Pitta,Kapha,Vata-Pitta,Pitta-Kapha,Vata-Kapha,Vata-Pitta-Kapha',
                'doshas' => 'required|array|min:1',
                'doshas.*' => 'required|in:Vata,Pitta,Kapha',
                'registration_date' => 'required|date',
                'registration_type' => 'required|in:OPD,IPD',
                'photo' => 'nullable|image|max:2048',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $updateData = [
            'full_name' => $validated['full_name'],
            'gender' => $validated['gender'],
            'age' => $validated['age'],
            'mobile' => $validated['mobile'],
            'emergency_contact' => $validated['emergency_contact'],
            'aadhaar_number' => $validated['aadhaar_number'],
            'address' => $validated['address'],
            'allergies' => $validated['allergies'],
            'prakriti' => $validated['prakriti'],
            'doshas' => $validated['doshas'],
            'registration_date' => $validated['registration_date'],
            'registration_type' => $validated['registration_type'],
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo from public path if exists
            if ($patient->photo_path && File::exists(public_path($patient->photo_path))) {
                File::delete(public_path($patient->photo_path));
            }

            $newPhoto = $request->file('photo');
            $destinationPath = public_path('backend-assets/media/uploads/products');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $photoFilename = 'patient_' . now()->format('YmdHis') . '_' . Str::random(6) . '.' . $newPhoto->getClientOriginalExtension();
            $newPhoto->move($destinationPath, $photoFilename);
            $updateData['photo_path'] = 'backend-assets/media/uploads/products/' . $photoFilename;
        }

        $patient->update($updateData);

        // Save the user
        if ($patient->save()) {
            LogHelper::logActivity('Update', $patient->id, 'patient', $patient->toArray());  // For Insert 

            return redirect()->route('patients')->with('success', 'Patient updated successfully');
        } else {
            return redirect()->route('patients')->with('error', "Something Wrong On Data Save");
        }
    }

    //  delete the specified patient.
    public function destroy(Patient $patient)
    {
        // Delete photo if exists
        if ($patient->photo_path && File::exists(public_path($patient->photo_path))) {
            File::delete(public_path($patient->photo_path));
        }

        // delete user from database
        if ($patient->delete()) {
            LogHelper::logActivity('Delete', $patient->id, 'patient', $patient->toArray());  // For delete 

            return redirect()->route('patients')->with('success', 'Patient deleted Successfully');
        }
        return redirect()->route('patients')->with('error', 'Failed to delete patient');
    }


    // Generate a new UHID code.
    protected function generateUhid(): string
    {
        $year = now()->year;
        $last = Patient::whereYear('created_at', $year)->orderByDesc('id')->first();
        $sequence = 1;

        if ($last && preg_match('/AYR-' . $year . '-(\d{4})/', $last->uhid, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return sprintf('AYR-%d-%04d', $year, $sequence);
    }

    /**
     * Get statistics for the patient dashboard.
     *
     * @return array
     */
    private function getDashboardStats(): array
    {
        $todayRegistrations = Patient::whereDate('registration_date', today())->count();
        $yesterdayRegistrations = Patient::whereDate('registration_date', today()->subDay())->count();

        $todayTrendType = 'flat';
        if ($todayRegistrations > $yesterdayRegistrations) {
            $todayTrendType = 'up';
        } elseif ($todayRegistrations < $yesterdayRegistrations) {
            $todayTrendType = 'down';
        }

        $todayTrendText = $yesterdayRegistrations > 0
            ? (abs(round((($todayRegistrations - $yesterdayRegistrations) / $yesterdayRegistrations) * 100, 1)) . '% vs yesterday')
            : ($todayRegistrations > 0 ? 'New activity' : 'No change');

        return [
            'todayRegistrations' => $todayRegistrations,
            'todayRegistrationsTrend' => [
                'type' => $todayTrendType,
                'text' => $todayTrendText,
            ],
            'activeOpdCount' => Patient::where('registration_type', 'OPD')->count(),
            'activeOpdTrend' => ['type' => 'flat', 'text' => 'Stable'], // Placeholder
            'activeIpdCount' => Patient::where('registration_type', 'IPD')->count(),
            'activeIpdTrend' => ['type' => 'flat', 'text' => 'Stable'], // Placeholder
            'panchkarmaTreatments' => 15, // Placeholder
        ];
    }
}
