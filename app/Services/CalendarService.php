<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Get calendar data for a specific date range and view type.
     */
    public function getCalendarData(string $date, string $view = 'week', ?int $departmentId = null): array
    {
        $startDate = $this->getStartDate($date, $view);
        $endDate = $this->getEndDate($date, $view);

        $appointments = $this->getAppointments($startDate, $endDate, $departmentId);
        $doctors = $this->getDoctors($departmentId);
        $overlaps = $this->detectOverlaps($appointments);

        return [
            'appointments' => $this->formatAppointmentsForCalendar($appointments),
            'doctors' => $doctors,
            'overlaps' => $overlaps,
            'dateRange' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'view' => $view
            ]
        ];
    }

    /**
     * Get start date based on view type.
     */
    private function getStartDate(string $date, string $view): Carbon
    {
        $carbonDate = Carbon::parse($date);

        return match($view) {
            'day' => $carbonDate->copy(),
            'week' => $carbonDate->copy()->startOfWeek(),
            'month' => $carbonDate->copy()->startOfMonth(),
            default => $carbonDate->copy()->startOfWeek()
        };
    }

    /**
     * Get end date based on view type.
     */
    private function getEndDate(string $date, string $view): Carbon
    {
        $carbonDate = Carbon::parse($date);

        return match($view) {
            'day' => $carbonDate->copy(),
            'week' => $carbonDate->copy()->endOfWeek(),
            'month' => $carbonDate->copy()->endOfMonth(),
            default => $carbonDate->copy()->endOfWeek()
        };
    }

    /**
     * Get appointments for a date range.
     */
    private function getAppointments(Carbon $startDate, Carbon $endDate, ?int $departmentId = null): Collection
    {
        $query = Appointment::with(['patient', 'doctor', 'department'])
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');

        if ($departmentId) {
            $query->whereHas('doctor', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->get();
    }

    /**
     * Get doctors for calendar display.
     */
    private function getDoctors(?int $departmentId = null): Collection
    {
        $query = Doctor::with('department')->orderBy('full_name');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get();
    }

    /**
     * Detect appointment overlaps.
     */
    private function detectOverlaps(Collection $appointments): Collection
    {
        $overlaps = collect();

        // Group appointments by doctor and date
        $groupedAppointments = $appointments->groupBy(function($appointment) {
            return $appointment->doctor_id . '_' . $appointment->appointment_date;
        });

        foreach ($groupedAppointments as $groupKey => $doctorAppointments) {
            if ($doctorAppointments->count() > 1) {
                // Check for time overlaps
                $sortedAppointments = $doctorAppointments->sortBy('appointment_time');
                
                for ($i = 0; $i < $sortedAppointments->count() - 1; $i++) {
                    $current = $sortedAppointments[$i];
                    $next = $sortedAppointments[$i + 1];

                    // Check if appointments overlap (assuming 30-minute slots)
                    $currentEnd = Carbon::parse($current->appointment_time)->addMinutes(30);
                    $nextStart = Carbon::parse($next->appointment_time);

                    if ($currentEnd > $nextStart) {
                        $overlaps->push([
                            'doctor_id' => $current->doctor_id,
                            'doctor_name' => $current->doctor->full_name ?? 'N/A',
                            'date' => $current->appointment_date,
                            'overlapping_appointments' => [
                                [
                                    'id' => $current->id,
                                    'patient_name' => $current->patient->full_name ?? 'N/A',
                                    'time' => $current->appointment_time,
                                    'type' => $current->appointment_type ?? 'N/A'
                                ],
                                [
                                    'id' => $next->id,
                                    'patient_name' => $next->patient->full_name ?? 'N/A',
                                    'time' => $next->appointment_time,
                                    'type' => $next->appointment_type ?? 'N/A'
                                ]
                            ]
                        ]);
                    }
                }
            }
        }

        return $overlaps;
    }

    /**
     * Format appointments for calendar display.
     */
    private function formatAppointmentsForCalendar(Collection $appointments): Collection
    {
        return $appointments->map(function($appointment) {
            return [
                'id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'patient_name' => $appointment->patient->full_name ?? 'N/A',
                'patient_uhid' => $appointment->patient->uhid ?? 'N/A',
                'appointment_type' => $appointment->appointment_type ?? 'Consultation',
                'mode' => $appointment->mode ?? 'OPD',
                'status' => $appointment->status ?? 'Waiting',
                'department' => $appointment->doctor->department->name ?? 'N/A',
                'doctor_name' => $appointment->doctor->full_name ?? 'N/A'
            ];
        });
    }

    /**
     * Get available time slots for a doctor on a specific date.
     */
    public function getAvailableTimeSlots(int $doctorId, string $date): array
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return [];
        }

        // Get doctor's schedule
        $schedule = $this->getDoctorSchedule($doctor);
        
        // Get booked appointments for the date
        $bookedAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_time')
            ->toArray();

        // Generate time slots
        $timeSlots = $this->generateTimeSlots($schedule);
        
        // Mark slots as available/unavailable
        return $this->markSlotAvailability($timeSlots, $bookedAppointments);
    }

    /**
     * Get doctor's schedule.
     */
    private function getDoctorSchedule(Doctor $doctor): array
    {
        // This would typically come from a doctor_schedules table
        // For now, return default schedule
        return [
            'morning_start' => '09:00',
            'morning_end' => '12:00',
            'afternoon_start' => '14:00',
            'afternoon_end' => '17:00',
            'slot_duration' => 30 // minutes
        ];
    }

    /**
     * Generate time slots based on schedule.
     */
    private function generateTimeSlots(array $schedule): array
    {
        $slots = [];
        
        // Morning slots
        $morningStart = Carbon::parse($schedule['morning_start']);
        $morningEnd = Carbon::parse($schedule['morning_end']);
        
        while ($morningStart < $morningEnd) {
            $slots[] = $morningStart->format('H:i');
            $morningStart->addMinutes($schedule['slot_duration']);
        }

        // Afternoon slots
        $afternoonStart = Carbon::parse($schedule['afternoon_start']);
        $afternoonEnd = Carbon::parse($schedule['afternoon_end']);
        
        while ($afternoonStart < $afternoonEnd) {
            $slots[] = $afternoonStart->format('H:i');
            $afternoonStart->addMinutes($schedule['slot_duration']);
        }

        return $slots;
    }

    /**
     * Mark slot availability.
     */
    private function markSlotAvailability(array $timeSlots, array $bookedAppointments): array
    {
        return array_map(function($slot) use ($bookedAppointments) {
            return [
                'time' => $slot,
                'formatted_time' => Carbon::parse($slot)->format('g:i A'),
                'is_available' => !in_array($slot, $bookedAppointments),
                'is_booked' => in_array($slot, $bookedAppointments)
            ];
        }, $timeSlots);
    }

    /**
     * Get calendar statistics.
     */
    public function getCalendarStats(string $date, ?int $departmentId = null): array
    {
        $startDate = Carbon::parse($date)->startOfWeek();
        $endDate = Carbon::parse($date)->endOfWeek();

        $query = Appointment::whereBetween('appointment_date', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('doctor', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $totalAppointments = $query->count();
        $completedAppointments = $query->where('status', 'Completed')->count();
        $waitingAppointments = $query->where('status', 'Waiting')->count();
        $cancelledAppointments = $query->where('status', 'Cancelled')->count();

        return [
            'total' => $totalAppointments,
            'completed' => $completedAppointments,
            'waiting' => $waitingAppointments,
            'cancelled' => $cancelledAppointments,
            'completion_rate' => $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100, 2) : 0
        ];
    }

    /**
     * Get appointments for a specific date range with filters (public method for API).
     */
    public function getAppointmentsWithFilters(string $startDate, string $endDate, ?int $doctorId = null, ?int $departmentId = null, ?string $status = null): Collection
    {
        $query = Appointment::with(['patient', 'doctor', 'department'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($departmentId) {
            $query->whereHas('doctor', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get doctor schedule for a specific date (public method for API).
     */
    public function getDoctorSchedulePublic(int $doctorId, string $date): array
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return [];
        }

        // This would typically come from a doctor_schedules table
        // For now, return default schedule
        return [
            'morning_start' => '09:00',
            'morning_end' => '12:00',
            'afternoon_start' => '14:00',
            'afternoon_end' => '17:00',
            'slot_duration' => 30, // minutes
            'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'breaks' => [
                ['start' => '12:00', 'end' => '14:00', 'type' => 'Lunch']
            ]
        ];
    }

    /**
     * Detect appointment overlaps for a date range (public method for API).
     */
    public function detectOverlapsForDateRange(string $startDate, string $endDate, ?int $doctorId = null): Collection
    {
        $appointments = $this->getAppointmentsWithFilters($startDate, $endDate, $doctorId);
        return $this->detectOverlaps($appointments);
    }
}
