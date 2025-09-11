<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TherapistAssignment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MasterRoom;
use App\Models\MasterTreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\LogHelper;

class TherapistScheduleController extends Controller
{
    /**
     * Display the therapist schedule dashboard
     */
    public function index(Request $request)
    {
        $pagename = 'Therapist Schedule';
        $breadcrumb = 'Therapist Schedule';

        // Get selected date from request or default to today
        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($selectedDate);

        // Get all active therapists (doctors)
        $therapists = Doctor::with(['department'])
            ->where('status', 1)
            ->orderBy('full_name')
            ->get();

        // Get assignments for the selected date
        $assignments = TherapistAssignment::with([
            'patient' => function ($query) {
                $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'photo_path');
            },
            'therapist' => function ($query) {
                $query->select('id', 'full_name', 'photo');
            },
            'room' => function ($query) {
                $query->select('id', 'room_number', 'room_type');
            },
            'treatmentPlan' => function ($query) {
                $query->select('id', 'patient_id', 'procedure_name', 'treatment_category');
            },
            'treatmentPlan.treatmentCategory' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->whereDate('assignment_date', $selectedDate)
            ->orderBy('start_time')
            ->get();

        // Get all appointments for today (for the "Today's Appointments" section)
        $todaysAppointments = TherapistAssignment::with([
            'patient' => function ($query) {
                $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'photo_path');
            },
            'therapist' => function ($query) {
                $query->select('id', 'full_name', 'photo');
            },
            'room' => function ($query) {
                $query->select('id', 'room_number', 'room_type');
            },
            'treatmentPlan' => function ($query) {
                $query->select('id', 'patient_id', 'procedure_name', 'treatment_category');
            },
            'treatmentPlan.treatmentCategory' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->whereDate('assignment_date', today())
            ->orderBy('start_time')
            ->get();

        // If no assignments for selected date, show the most recent date with data
        if ($assignments->isEmpty()) {
            $latestAssignment = TherapistAssignment::orderBy('assignment_date', 'desc')->first();
            if ($latestAssignment) {
                $selectedDate = $latestAssignment->assignment_date->format('Y-m-d');
                $carbonDate = Carbon::parse($selectedDate);

                // Re-fetch assignments for the latest date
                $assignments = TherapistAssignment::with([
                    'patient' => function ($query) {
                        $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'photo_path');
                    },
                    'therapist' => function ($query) {
                        $query->select('id', 'full_name', 'photo');
                    },
                    'room' => function ($query) {
                        $query->select('id', 'room_number', 'room_type');
                    },
                    'treatmentPlan' => function ($query) {
                        $query->select('id', 'procedure_name');
                    }
                ])
                    ->whereDate('assignment_date', $selectedDate)
                    ->orderBy('start_time')
                    ->get();
            }
        }

        // Group assignments by therapist and time slot
        $scheduleData = $this->buildScheduleData($therapists, $assignments, $selectedDate);

        // Get appointment statistics for the selected date
        $stats = $this->getScheduleStats(today());

        // Get available rooms
        $rooms = MasterRoom::where('status', 1)->get();

        // Get treatment categories
        $treatmentCategories = MasterTreatmentCategory::where('status', 1)->get();

        return view('backend.doctor.therapist_schedule', compact(
            'pagename',
            'breadcrumb',
            'therapists',
            'assignments',
            'todaysAppointments',
            'scheduleData',
            'stats',
            'selectedDate',
            'carbonDate',
            'rooms',
            'treatmentCategories'
        ));
    }

    /**
     * Build schedule data structure for the calendar view
     */
    private function buildScheduleData($therapists, $assignments, $selectedDate)
    {
        $scheduleData = [];
        $timeSlots = $this->generateTimeSlots();

        foreach ($therapists as $therapist) {
            $therapistAssignments = $assignments->where('therapist_id', $therapist->id);

            $scheduleData[$therapist->id] = [
                'therapist' => $therapist,
                'timeSlots' => []
            ];

            foreach ($timeSlots as $timeSlot) {
                $slotAssignments = $therapistAssignments->filter(function ($assignment) use ($timeSlot) {
                    // Handle different time formats
                    $startTime = $assignment->start_time;
                    if (is_string($startTime)) {
                        $startTime = Carbon::parse($startTime)->format('H:i');
                    } else {
                        $startTime = $startTime->format('H:i');
                    }

                    // Match exact time or within the hour slot
                    $slotHour = intval($timeSlot['time']);
                    $assignmentHour = intval($startTime);

                    return $startTime === $timeSlot['time'] || $assignmentHour === $slotHour;
                });

                $scheduleData[$therapist->id]['timeSlots'][$timeSlot['time']] = [
                    'time' => $timeSlot['time'],
                    'formatted_time' => $timeSlot['formatted_time'],
                    'assignments' => $slotAssignments,
                    'is_lunch_break' => $this->isLunchBreak($timeSlot['time']),
                    'is_available' => $slotAssignments->isEmpty() && !$this->isLunchBreak($timeSlot['time'])
                ];
            }
        }

        return $scheduleData;
    }

    /**
     * Generate time slots for the day
     */
    private function generateTimeSlots()
    {
        $slots = [];
        $startTime = Carbon::createFromTime(9, 0); // 9:00 AM
        $endTime = Carbon::createFromTime(17, 0); // 5:00 PM

        while ($startTime < $endTime) {
            $slots[] = [
                'time' => $startTime->format('H:i'),
                'formatted_time' => $startTime->format('g:i A')
            ];
            $startTime->addHour();
        }

        return $slots;
    }

    /**
     * Check if time slot is lunch break
     */
    private function isLunchBreak($time)
    {
        return $time === '12:00';
    }

    /**
     * Get schedule statistics for the selected date
     */
    private function getScheduleStats($selectedDate)
    {
        $total = TherapistAssignment::whereDate('assignment_date', $selectedDate)->count();
        $active = TherapistAssignment::whereDate('assignment_date', $selectedDate)
            ->where('status', 1)
            ->count();
        $pending = TherapistAssignment::whereDate('assignment_date', $selectedDate)
            ->where('status', 0)
            ->count();
        $completed = TherapistAssignment::whereDate('assignment_date', $selectedDate)
            ->where('status', 2)
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'completed' => $completed
        ];
    }

    /**
     * Get therapist availability status
     */
    public function getTherapistStatus(Request $request)
    {
        try {
            $therapistId = $request->get('therapist_id');
            $date = $request->get('date', today()->format('Y-m-d'));

            $therapist = Doctor::find($therapistId);
            if (!$therapist) {
                return response()->json(['error' => 'Therapist not found'], 404);
            }

            // Get current assignments
            $currentAssignments = TherapistAssignment::where('therapist_id', $therapistId)
                ->whereDate('assignment_date', $date)
                ->whereIn('status', [0, 1]) // Pending, In Progress
                ->count();

            // Get next available time
            $nextAvailable = $this->getNextAvailableTime($therapistId, $date);

            // Determine status
            $status = 'available';
            $statusText = 'Available';
            $statusClass = 'bg-green-100 text-green-800';

            if ($currentAssignments > 0) {
                $status = 'busy';
                $statusText = 'Busy';
                $statusClass = 'bg-red-100 text-red-800';
            }

            return response()->json([
                'success' => true,
                'therapist' => [
                    'id' => $therapist->id,
                    'name' => $therapist->full_name,
                    'photo' => $therapist->photo,
                    'status' => $status,
                    'status_text' => $statusText,
                    'status_class' => $statusClass,
                    'appointments_today' => $currentAssignments,
                    'next_available' => $nextAvailable,
                    'specialization' => $therapist->specialty ?? 'General'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get Therapist Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get therapist status'
            ], 500);
        }
    }

    /**
     * Get next available time for therapist
     */
    private function getNextAvailableTime($therapistId, $date)
    {
        $timeSlots = $this->generateTimeSlots();
        $currentTime = Carbon::now();

        foreach ($timeSlots as $slot) {
            $slotTime = Carbon::parse($date . ' ' . $slot['time']);

            // Skip lunch break
            if ($this->isLunchBreak($slot['time'])) {
                continue;
            }

            // Check if slot is available
            $hasAssignment = TherapistAssignment::where('therapist_id', $therapistId)
                ->whereDate('assignment_date', $date)
                ->whereTime('start_time', $slot['time'])
                ->exists();

            if (!$hasAssignment && $slotTime->isAfter($currentTime)) {
                return $slot['formatted_time'];
            }
        }

        return 'No availability';
    }

    /**
     * Get appointments for a specific date
     */
    public function getAppointments(Request $request)
    {
        try {
            $date = $request->get('date', today()->format('Y-m-d'));

            $appointments = TherapistAssignment::with([
                'patient' => function ($query) {
                    $query->select('id', 'uhid', 'full_name', 'age', 'gender', 'photo_path');
                },
                'therapist' => function ($query) {
                    $query->select('id', 'full_name', 'photo');
                },
                'room' => function ($query) {
                    $query->select('id', 'room_number', 'room_type');
                },
                'treatmentPlan' => function ($query) {
                    $query->select('id', 'procedure_name');
                }
            ])
                ->whereDate('assignment_date', $date)
                ->orderBy('start_time')
                ->get();

            return response()->json([
                'success' => true,
                'appointments' => $appointments
            ]);
        } catch (\Exception $e) {
            Log::error('Get Appointments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get appointments'
            ], 500);
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

        LogHelper::logActivity('Update', $assignment->id, 'therapist_assignment', $assignment->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Assignment status updated successfully.',
            'assignment' => $assignment->fresh()
        ]);
    }

    /**
     * Get schedule data for calendar view
     */
    public function getScheduleData(Request $request)
    {
        try {
            $date = $request->get('date', today()->format('Y-m-d'));

            // Get all active therapists
            $therapists = Doctor::where('status', 1)->orderBy('full_name')->get();

            // Get assignments for the date
            $assignments = TherapistAssignment::with(['patient', 'therapist', 'room', 'treatmentPlan'])
                ->whereDate('assignment_date', $date)
                ->orderBy('start_time')
                ->get();

            // Build schedule data
            $scheduleData = $this->buildScheduleData($therapists, $assignments, $date);

            return response()->json([
                'success' => true,
                'scheduleData' => $scheduleData,
                'therapists' => $therapists,
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('Get Schedule Data Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get schedule data'
            ], 500);
        }
    }
}
