<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MasterRoom;
use App\Models\TherapistAssignment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Services\PatientSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TreatmentRoomController extends Controller
{
    protected $patientSearchService;

    public function __construct(PatientSearchService $patientSearchService)
    {
        $this->patientSearchService = $patientSearchService;
    }
    /**
     * Display the treatment room overview page
     */
    public function index(Request $request)
    {
        $pagename = 'Treatment Rooms';
        $breadcrumb = 'Treatment Rooms';

        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Get room availability data
        $roomData = $this->getRoomAvailabilityData($selectedDate);

        // Get room counts for display
        $roomCounts = [
            'available' => $roomData->where('status', 'available')->count(),
            'occupied' => $roomData->where('status', 'occupied')->count(),
            'maintenance' => $roomData->where('status', 'maintenance')->count(),
        ];

        // Get current bookings
        $currentBookings = $this->getCurrentBookings($selectedDate);

        // Get room utilization stats
        $utilizationStats = $this->getUtilizationStats($selectedDate);

        // Get all rooms for features accordion
        $allRooms = MasterRoom::where('status', 1)->get();

        return view('backend.doctor.treatment_room', compact(
            'roomData',
            'roomCounts',
            'currentBookings',
            'utilizationStats',
            'allRooms',
            'selectedDate',
            'pagename',
            'breadcrumb'
        ));
    }

    /**
     * Get room availability data for the selected date
     */
    private function getRoomAvailabilityData($date)
    {
        $rooms = MasterRoom::where('status', 1)->get();
        $assignments = TherapistAssignment::with(['patient', 'therapist', 'room'])
            ->whereDate('assignment_date', $date)
            ->get();

        $roomData = [];

        foreach ($rooms as $room) {
            // Filter assignments for this room
            $roomAssignments = $assignments->filter(function ($assignment) use ($room) {
                return $assignment->room_id == $room->id;
            });

            // Find current assignment (In Progress)
            $currentAssignment = $roomAssignments->first(function ($assignment) {
                return $assignment->status == 1;
            });

            // Find next assignment (Pending)
            $nextAssignment = $roomAssignments->first(function ($assignment) {
                return $assignment->status == 0;
            });

            $roomData[] = [
                'room' => $room,
                'status' => $this->getRoomStatus($room, $currentAssignment, $nextAssignment),
                'currentAssignment' => $currentAssignment,
                'nextAssignment' => $nextAssignment,
                'allAssignments' => $roomAssignments
            ];
        }

        return collect($roomData);
    }

    /**
     * Determine room status based on assignments
     */
    private function getRoomStatus($room, $currentAssignment, $nextAssignment)
    {
        if ($currentAssignment) {
            return 'occupied';
        } elseif ($room->status == 2) { // Maintenance status
            return 'maintenance';
        } else {
            return 'available';
        }
    }

    /**
     * Get current bookings for the selected date
     */
    private function getCurrentBookings($date)
    {
        return TherapistAssignment::with(['patient', 'therapist', 'room'])
            ->whereDate('assignment_date', $date)
            ->whereIn('therapist_assignments.status', [0, 1, 4]) // Pending, In Progress, Preparing
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get room utilization statistics
     */
    private function getUtilizationStats($date)
    {
        $totalRooms = MasterRoom::where('status', 1)->count();
        $occupiedRooms = TherapistAssignment::whereDate('assignment_date', $date)
            ->where('therapist_assignments.status', 1)
            ->distinct('room_id')
            ->count();

        $totalTreatments = TherapistAssignment::whereDate('assignment_date', $date)
            ->whereIn('therapist_assignments.status', [1, 2])
            ->count();

        $avgDuration = TherapistAssignment::whereDate('assignment_date', $date)
            ->where('therapist_assignments.status', 2)
            ->avg('duration_minutes');

        $totalRevenue = TherapistAssignment::whereDate('assignment_date', $date)
            ->where('therapist_assignments.status', 2)
            ->join('master_rooms', 'therapist_assignments.room_id', '=', 'master_rooms.id')
            ->sum('master_rooms.charges');

        return [
            'overallUtilization' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0,
            'treatmentsToday' => $totalTreatments,
            'avgTreatmentTime' => $avgDuration ? round($avgDuration / 60, 1) . 'h' : '0h',
            'revenueToday' => '₹' . number_format($totalRevenue, 0)
        ];
    }

    /**
     * Get room booking form data
     */
    public function getBookingFormData(Request $request)
    {
        $roomId = $request->get('room_id');
        $date = $request->get('date', today()->format('Y-m-d'));

        $room = MasterRoom::findOrFail($roomId);

        // Get available therapists for this room type
        $therapists = Doctor::where('status', 1)
            ->where('role', 'therapist')
            ->get();

        // Get available time slots for the room
        $availableSlots = $this->getAvailableTimeSlots($roomId, $date);

        return response()->json([
            'room' => $room,
            'therapists' => $therapists,
            'availableSlots' => $availableSlots
        ]);
    }

    /**
     * Get available time slots for a room
     */
    private function getAvailableTimeSlots($roomId, $date)
    {
        $existingAssignments = TherapistAssignment::where('room_id', $roomId)
            ->whereDate('assignment_date', $date)
            ->whereIn('therapist_assignments.status', [0, 1, 4])
            ->get();

        $slots = [];
        $startTime = Carbon::parse($date . ' 08:00');
        $endTime = Carbon::parse($date . ' 18:00');

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addHour();

            $conflict = $existingAssignments->filter(function ($assignment) use ($startTime, $slotEnd) {
                $assignmentStart = Carbon::parse($assignment->assignment_date->format('Y-m-d') . ' ' . $assignment->start_time->format('H:i'));
                $assignmentEnd = Carbon::parse($assignment->assignment_date->format('Y-m-d') . ' ' . $assignment->end_time->format('H:i'));

                return $assignmentStart->lt($slotEnd) && $assignmentEnd->gt($startTime);
            })->count() == 0;

            if ($conflict) {
                $slots[] = [
                    'start' => $startTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'display' => $startTime->format('H:i') . ' - ' . $slotEnd->format('H:i')
                ];
            }

            $startTime->addHour();
        }

        return $slots;
    }

    /**
     * Store a new room booking
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:master_rooms,id',
            'patient_id' => 'required|exists:patients,id',
            'therapist_id' => 'required|exists:doctors,id',
            'assignment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'treatment_details' => 'required|string',
            'materials_required' => 'nullable|string',
            'special_instructions' => 'nullable|string'
        ]);

        // Check for conflicts
        $conflict = TherapistAssignment::where('room_id', $request->room_id)
            ->whereDate('assignment_date', $request->assignment_date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->whereIn('therapist_assignments.status', [0, 1, 4])
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Room is not available for the selected time slot.'
            ], 422);
        }

        $assignment = TherapistAssignment::create([
            'treatment_plan_id' => null, // Can be linked later
            'patient_id' => $request->patient_id,
            'therapist_id' => $request->therapist_id,
            'room_id' => $request->room_id,
            'assigned_by' => Auth::id(),
            'assignment_date' => $request->assignment_date,
            'start_time' => $request->assignment_date . ' ' . $request->start_time,
            'end_time' => $request->assignment_date . ' ' . $request->end_time,
            'duration_minutes' => Carbon::parse($request->start_time)->diffInMinutes(Carbon::parse($request->end_time)),
            'treatment_details' => $request->treatment_details,
            'materials_required' => $request->materials_required,
            'special_instructions' => $request->special_instructions,
            'status' => 0 // Pending
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room booking created successfully.',
            'assignment' => $assignment->load(['patient', 'therapist', 'room'])
        ]);
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, TherapistAssignment $assignment)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4'
        ]);

        $assignment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'assignment' => $assignment->load(['patient', 'therapist', 'room'])
        ]);
    }

    /**
     * Search patients for booking
     */
    public function searchPatients(Request $request)
    {
        // Convert the request to match the service expectations
        $request->merge(['query' => $request->get('q')]);
        return $this->patientSearchService->searchPatients($request, 'treatment-room');
    }

    /**
     * Get room details
     */
    public function getRoomDetails(MasterRoom $room)
    {
        $room->load(['assignments' => function ($query) {
            $query->whereDate('assignment_date', today())
                ->with(['patient', 'therapist']);
        }]);

        return response()->json($room);
    }

    /**
     * Get room filter options
     */
    public function getFilterOptions()
    {
        $roomTypes = MasterRoom::distinct('room_type')->pluck('room_type');
        $roomStatuses = ['available', 'occupied', 'maintenance'];

        return response()->json([
            'roomTypes' => $roomTypes,
            'roomStatuses' => $roomStatuses
        ]);
    }

    /**
     * Filter rooms based on criteria
     */
    public function filterRooms(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $status = $request->get('status', []);
        $roomType = $request->get('room_type', []);

        $query = MasterRoom::where('status', 1);

        if (!empty($roomType)) {
            $query->whereIn('room_type', $roomType);
        }

        $rooms = $query->get();

        // Apply status filter
        if (!empty($status)) {
            $roomData = $this->getRoomAvailabilityData(Carbon::parse($date));
            $filteredRooms = collect($roomData)->filter(function ($room) use ($status) {
                return in_array($room['status'], $status);
            });

            return response()->json([
                'rooms' => $filteredRooms->pluck('room'),
                'roomData' => $filteredRooms
            ]);
        }

        return response()->json([
            'rooms' => $rooms,
            'roomData' => $this->getRoomAvailabilityData(Carbon::parse($date))
        ]);
    }
}
