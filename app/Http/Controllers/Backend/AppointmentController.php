<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Services\PatientSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Added Log facade
use Illuminate\Support\Facades\DB; // Added DB facade


class AppointmentController extends Controller
{
    protected $patientSearchService;

    public function __construct(PatientSearchService $patientSearchService)
    {
        $this->patientSearchService = $patientSearchService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagename = 'Appointment Booking';
        $breadcrumb = 'Appointment Booking';

        $today = now()->toDateString();
        $todayAppointments = Appointment::with(['patient', 'doctor', 'department'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        // Get waiting appointments count
        $waitingAppointments = Appointment::where('status', 'Waiting')->get();

        // Get completed appointments count
        $completedAppointments = Appointment::where('status', 'Completed')->get();

        $departments = Department::where('is_active', 1)->orderBy('name')->get();
        $doctors = Doctor::where('status', 1)->orderBy('full_name')->get();
        $patients = Patient::orderBy('full_name')->get();

        // Get patient data if uhid is provided
        $selectedPatient = null;
        if ($request->has('uhid')) {
            $selectedPatient = Patient::where('uhid', $request->uhid)->first();
        }

        return view('backend.appointment.index', compact('pagename', 'breadcrumb', 'todayAppointments', 'waitingAppointments', 'completedAppointments', 'departments', 'doctors', 'patients', 'selectedPatient'));
    }

    /**
     * Display the appointment calendar view.
     */
    public function calendar(Request $request)
    {
        // If it's an AJAX request, return JSON data
        if ($request->ajax()) {
            return $this->getCalendarData($request);
        }

        $pagename = 'Appointment Calendar';
        $breadcrumb = 'Appointment Calendar';

        // Get today's date for default calendar view
        $today = now()->toDateString();

        // Get appointments for the current week
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weekAppointments = Appointment::with(['patient', 'doctor', 'department'])
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get all doctors for the calendar
        $doctors = Doctor::with('department')->orderBy('full_name')->get();

        // Get all departments for filtering
        $departments = Department::orderBy('name')->get();

        return view('backend.appointment.calendar', compact(
            'pagename',
            'breadcrumb',
            'today',
            'weekAppointments',
            'doctors',
            'departments'
        ));
    }

    /**
     * Get calendar data for AJAX requests.
     */
    private function getCalendarData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $department = $request->get('department');
        $view = $request->get('view', 'week');

        $query = Appointment::with(['patient', 'doctor', 'department']);

        // Apply date filter based on view type
        if ($view === 'day') {
            $query->whereDate('appointment_date', $date);
        } elseif ($view === 'week') {
            $startOfWeek = \Carbon\Carbon::parse($date)->startOfWeek();
            $endOfWeek = \Carbon\Carbon::parse($date)->endOfWeek();
            $query->whereBetween('appointment_date', [$startOfWeek, $endOfWeek]);
        } elseif ($view === 'month') {
            $startOfMonth = \Carbon\Carbon::parse($date)->startOfMonth();
            $endOfMonth = \Carbon\Carbon::parse($date)->endOfMonth();
            $query->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
        }

        // Apply department filter
        if ($department) {
            $query->whereHas('doctor', function ($q) use ($department) {
                $q->where('department_id', $department);
            });
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get today's appointments for the bottom table
        $todayAppointments = Appointment::with(['patient', 'doctor', 'department'])
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        // Format appointments for calendar display
        $formattedAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_time' => $appointment->appointment_time,
                'patient_name' => $appointment->patient->full_name ?? 'N/A',
                'appointment_type' => $appointment->appointment_type ?? 'Consultation',
                'mode' => $appointment->mode ?? 'OPD',
                'status' => $appointment->status ?? 'Waiting'
            ];
        });

        // Format today's appointments for the table
        $formattedTodayAppointments = $todayAppointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'appointment_time' => $appointment->appointment_time,
                'patient_name' => $appointment->patient->full_name ?? 'N/A',
                'patient_uhid' => $appointment->patient->uhid ?? 'N/A',
                'patient_photo' => $appointment->patient->photo_path,
                'doctor_name' => $appointment->doctor->full_name ?? 'N/A',
                'appointment_type' => $appointment->appointment_type ?? 'N/A',
                'status' => $appointment->status ?? 'N/A'
            ];
        });

        return response()->json([
            'appointments' => $formattedAppointments,
            'todayAppointments' => $formattedTodayAppointments,
            'date' => $date,
            'view' => $view
        ]);
    }

    public function create()
    {
        return redirect()->route('appointment');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        try {
            $appointment->load(['patient', 'doctor', 'department']);

            return view('backend.appointment.show', compact('appointment'));
        } catch (\Exception $e) {
            Log::error('Error showing appointment: ' . $e->getMessage());
            return redirect()->route('appointment')->with('error', 'Appointment not found or error occurred.');
        }
    }

    public function searchPatient(Request $request)
    {
        return $this->patientSearchService->searchPatients($request, 'appointment');
    }


    public function getDoctorsByDepartment(Request $request)
    {
        $departmentId = $request->get('department_id');

        if (!$departmentId) {
            return response()->json(['error' => 'Department ID is required']);
        }

        // Get doctors where specialty matches the department ID
        $doctors = Doctor::where('specialty', $departmentId)
            ->where('status', 1) // Only active doctors
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'specialty', 'qualification']);

        return response()->json([
            'doctors' => $doctors,
            'department_id' => $departmentId
        ]);
    }

    public function getDoctorFees(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $appointmentType = $request->get('appointment_type');

        if (!$doctorId) {
            return response()->json(['error' => 'Doctor ID is required']);
        }

        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found']);
        }

        $fee = $appointmentType === 'Follow Up'
            ? ($doctor->followup_fee ?? $doctor->consultation_fee)
            : $doctor->consultation_fee;

        return response()->json([
            'consultation_fee' => $doctor->consultation_fee,
            'followup_fee' => $doctor->followup_fee,
            'calculated_fee' => $fee
        ]);
    }

    public function getDoctorTimeSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $date = $request->get('date');

        if (!$doctorId || !$date) {
            return response()->json(['error' => 'Doctor ID and date are required']);
        }

        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found']);
        }

        // Get day of week (0 = Sunday, 1 = Monday, etc.)
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        // Debug logging
        Log::info("=== DOCTOR TIME SLOTS DEBUG ===");
        Log::info("Doctor ID: {$doctorId}");
        Log::info("Date: {$date}");
        Log::info("Day of week: {$dayOfWeek}");
        Log::info("Doctor available_days: " . json_encode($doctor->available_days));
        Log::info("Doctor morning_from: {$doctor->morning_from}");
        Log::info("Doctor morning_to: {$doctor->morning_to}");
        Log::info("Doctor evening_from: {$doctor->evening_from}");
        Log::info("Doctor evening_to: {$doctor->evening_to}");
        Log::info("Doctor time_per_consultation: {$doctor->time_per_consultation}");

        // Check if doctor is available on this day
        $availableDays = $doctor->available_days ?? [];

        // If no available days are set, assume doctor is available all days
        if (empty($availableDays)) {
            Log::info("No available days set, using default all days");
            $availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        }

        Log::info("Final available days: " . json_encode($availableDays));
        Log::info("Checking if {$dayOfWeek} is in available days: " . (in_array($dayOfWeek, $availableDays) ? 'YES' : 'NO'));


        // Get existing appointments for this doctor on this date
        $existingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_time')
            ->toArray();

        Log::info("Existing appointments: " . json_encode($existingAppointments));

        // Generate time slots based on doctor's schedule
        $timeSlots = [];
        $consultationTime = $doctor->time_per_consultation ?? 30; // Default 30 minutes

        // Morning slots - if not set, use default morning hours
        if ($doctor->morning_from && $doctor->morning_to) {
            Log::info("Using doctor's morning schedule: {$doctor->morning_from} to {$doctor->morning_to}");
            $morningSlots = $this->generateTimeSlots(
                $doctor->morning_from,
                $doctor->morning_to,
                $consultationTime,
                $existingAppointments
            );
            $timeSlots['morning'] = $morningSlots;
        } else {
            Log::info("Using default morning schedule: 09:00 to 12:00");
            // Default morning slots if not set
            $morningSlots = $this->generateTimeSlots(
                '09:00:00',
                '12:00:00',
                $consultationTime,
                $existingAppointments
            );
            $timeSlots['morning'] = $morningSlots;
        }

        // Evening slots - if not set, use default evening hours
        if ($doctor->evening_from && $doctor->evening_to) {
            Log::info("Using doctor's evening schedule: {$doctor->evening_from} to {$doctor->evening_to}");
            $eveningSlots = $this->generateTimeSlots(
                $doctor->evening_from,
                $doctor->evening_to,
                $consultationTime,
                $existingAppointments
            );
            $timeSlots['evening'] = $eveningSlots;
        } else {
            Log::info("Using default evening schedule: 16:00 to 19:00");
            // Default evening slots if not set
            $eveningSlots = $this->generateTimeSlots(
                '16:00:00',
                '19:00:00',
                $consultationTime,
                $existingAppointments
            );
            $timeSlots['evening'] = $eveningSlots;
        }

        Log::info("Final time slots: " . json_encode($timeSlots));
        Log::info("=== END DEBUG ===");

        return response()->json([
            'time_slots' => $timeSlots,
            'consultation_time' => $consultationTime,
            'doctor_schedule' => [
                'morning_from' => $doctor->morning_from ?? '09:00:00',
                'morning_to' => $doctor->morning_to ?? '12:00:00',
                'evening_from' => $doctor->evening_from ?? '16:00:00',
                'evening_to' => $doctor->evening_to ?? '19:00:00',
                'available_days' => $availableDays,
                'selected_day' => $dayOfWeek
            ]
        ]);
    }


    private function generateTimeSlots($startTime, $endTime, $interval, $bookedSlots)
    {
        $slots = [];
        $current = strtotime($startTime);
        $end = strtotime($endTime);

        // Debug logging
        Log::info("Generating time slots from {$startTime} to {$endTime} with interval {$interval}");
        Log::info("Current: " . date('H:i:s', $current) . ", End: " . date('H:i:s', $end));

        while ($current <= $end) {
            $timeSlot = date('H:i', $current);
            $isBooked = in_array($timeSlot, $bookedSlots);

            $slots[] = [
                'time' => $timeSlot,
                'formatted_time' => date('g:i A', $current),
                'is_booked' => $isBooked,
                'is_available' => !$isBooked
            ];

            $current = strtotime("+{$interval} minutes", $current);
        }

        Log::info("Generated " . count($slots) . " time slots");

        return $slots;
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'patient_id' => 'required',
                'doctor_id' => 'required|exists:doctors,id',
                'department_id' => 'nullable|exists:departments,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|date_format:H:i',
                'appointment_type' => 'required|in:First Visit,Follow Up',
                'mode' => 'required|in:OPD,Panchkarma',
                'chief_complaint' => 'nullable|string|max:2000',
            ],
            [
                // Custom validation error messages
                'patient_id.required' => 'Please select a patient',

                'doctor_id.required' => 'Please select a doctor',
                'doctor_id.exists' => 'Selected doctor does not exist',

                'department_id.exists' => 'Selected department does not exist',

                'appointment_date.required' => 'Please select an appointment date',
                'appointment_date.date' => 'Please provide a valid date',
                'appointment_date.after_or_equal' => 'Appointment date cannot be in the past',

                'appointment_time.required' => 'Please select an appointment time slot',
                'appointment_time.date_format' => 'Please provide a valid time format',

                'appointment_type.required' => 'Please select appointment type',
                'appointment_type.in' => 'Please select a valid appointment type',

                'mode.required' => 'Please select appointment mode',
                'mode.in' => 'Please select a valid appointment mode',

                'chief_complaint.string' => 'Chief complaint must be a valid text',
                'chief_complaint.max' => 'Chief complaint cannot exceed 2000 characters',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $fee = $validated['appointment_type'] === 'Follow Up'
            ? ($doctor->followup_fee ?? $doctor->consultation_fee)
            : $doctor->consultation_fee;

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'department_id' => $validated['department_id'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'appointment_type' => $validated['appointment_type'],
            'mode' => $validated['mode'],
            'status' => 'Waiting',
            'fee' => $fee,
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Create bill record for the appointment
        $this->createBillForAppointment($appointment, $fee);

        return redirect()->route('appointment')->with('success', 'Appointment booked successfully.');
    }

    /**
     * Create bill record for appointment
     */
    private function createBillForAppointment($appointment, $fee)
    {
        $invoiceNumber = $this->generateInvoiceNumber();

        $bill = Bill::create([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $fee,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $fee,
            'paid_amount' => 0,
            'status' => 1, // 1 = pending (as per migration comment)
            'bill_items' => json_encode([
                [
                    'description' => $appointment->appointment_type . ' Consultation - ' . $appointment->doctor->full_name,
                    'quantity' => 1,
                    'unit_price' => $fee,
                    'total' => $fee
                ]
            ])
        ]);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastBill = Bill::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:Waiting,In Progress,Completed,Cancelled'
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $request->status;
        $appointment->save();

        // If appointment is completed and there's no existing bill, create one
        if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            $existingBill = Bill::where('appointment_id', $appointment->id)->first();

            if (!$existingBill) {
                $this->createBillForAppointment($appointment, $appointment->fee);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully',
            'status' => $appointment->status
        ]);
    }
}
