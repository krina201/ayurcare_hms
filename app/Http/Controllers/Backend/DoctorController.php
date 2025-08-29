<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; // add at top


class DoctorController extends Controller
{
    public function index()
    {
        $pagename = 'Doctor';
        $breadcrumb = 'Doctor';
        $doctor = Doctor::all();

        return view('backend.doctor.index', compact('breadcrumb', 'pagename', 'doctor'));
    }

    public function create()
    {
        return view('backend.doctor.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'full_name'             => 'required|string|max:255',
                'gender'                => 'required|in:male,female,other',
                'email'                 => 'required|email|unique:doctors,email',
                'mobile'                => 'required|digits:10',
                'dob'                   => 'required|date|before:today',
                'address'               => 'required|string|max:500',
                'specialty'             => 'required|string',
                'qualification'         => 'required|string|max:255',
                'experience'            => 'required|integer|min:0|max:50',
                'registration_number'   => 'required|string|unique:doctors,registration_number',
                'consultation_fee'      => 'required|numeric|min:0|max:10000',
                'followup_fee'          => 'required|numeric|min:0|max:10000',
                'commission_type'       => 'required|in:fixed,percentage',
                'commission_value'      => 'required|numeric|min:0',
                'available_days'        => 'required|array|min:1',
                'available_days.*'      => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'morning_from'          => 'required|date_format:H:i',
                'morning_to'            => 'required|date_format:H:i|after:morning_from',
                'evening_from'          => 'required|date_format:H:i',
                'evening_to'            => 'required|date_format:H:i|after:evening_from',
                'time_per_consultation' => 'required|integer|in:15,20,30,45,60',
                'expertise_areas'       => 'required|array|min:1',
                'expertise_areas.*'     => 'required|string',
                'panchkarma_treatments' => 'required|array|min:1',
                'panchkarma_treatments.*' => 'required|string',
                'degree_certificate'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'registration_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'photo'                 => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Additional validation for commission value based on type
        if ($validated['commission_type'] == 'percentage' && $validated['commission_value'] > 100) {
            return back()->withErrors(['commission_value' => 'Percentage cannot exceed 100%'])->withInput();
        }

        // Additional validation for schedule timing
        if ($validated['morning_to'] <= $validated['morning_from']) {
            return back()->withErrors(['morning_to' => 'Morning end time must be after start time'])->withInput();
        }

        if ($validated['evening_to'] <= $validated['evening_from']) {
            return back()->withErrors(['evening_to' => 'Evening end time must be after start time'])->withInput();
        }

        // Auto-generate Doctor ID
        $validated['doctor_id'] = 'DOC-' . now()->year . '-' . strtoupper(Str::random(6));
        $validated['status'] = 1;

        // Convert arrays to JSON for database storage
        $validated['available_days'] = json_encode($validated['available_days']);
        $validated['expertise_areas'] = json_encode($validated['expertise_areas']);
        $validated['panchkarma_treatments'] = json_encode($validated['panchkarma_treatments']);

        // Create upload directories if they don't exist
        $this->ensureDirectoryExists('backend-assets/media/uploads/doctors/photos');
        $this->ensureDirectoryExists('backend-assets/media/uploads/doctors/docs/degree_certificate');
        $this->ensureDirectoryExists('backend-assets/media/uploads/doctors/docs/registration_certificate');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_photo_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend-assets/media/uploads/doctors/photos'), $filename);
            $validated['photo'] = 'backend-assets/media/uploads/doctors/photos/' . $filename;
        }

        // Handle degree certificate upload
        if ($request->hasFile('degree_certificate')) {
            $file = $request->file('degree_certificate');
            $filename = time() . '_degree_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend-assets/media/uploads/doctors/docs/degree_certificate'), $filename);
            $validated['degree_certificate'] = 'backend-assets/media/uploads/doctors/docs/degree_certificate/' . $filename;
        }

        // Handle registration certificate upload
        if ($request->hasFile('registration_certificate')) {
            $file = $request->file('registration_certificate');
            $filename = time() . '_registration_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend-assets/media/uploads/doctors/docs/registration_certificate'), $filename);
            $validated['registration_certificate'] = 'backend-assets/media/uploads/doctors/docs/registration_certificate/' . $filename;
        }

        // Create doctor record
        Doctor::create($validated);

        return redirect()->route('doctor')->with('success', 'Doctor profile created successfully!');
    }

    private function ensureDirectoryExists($path)
    {
        $fullPath = public_path($path);
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true); // recursive mkdir
        }
    }

    public function dashboard()
    {
        $pagename = 'Doctor Dashboard';
        $breadcrumb = 'Doctor Dashboard';

        // Find the doctor with ID 1 or show a 404 error if not found.
        $doctor = Doctor::findOrFail(1);

        return view('backend.doctor.dashboard', compact('breadcrumb', 'pagename', 'doctor'));
    }
}
