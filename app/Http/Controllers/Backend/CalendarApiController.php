<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CalendarApiController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Get calendar data for AJAX requests.
     */
    public function getCalendarData(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'view' => 'nullable|in:day,week,month',
            'department' => 'nullable|integer|exists:departments,id'
        ]);

        $date = $request->get('date');
        $view = $request->get('view', 'week');
        $departmentId = $request->get('department');

        try {
            $calendarData = $this->calendarService->getCalendarData($date, $view, $departmentId);
            
            return response()->json([
                'success' => true,
                'data' => $calendarData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading calendar data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available time slots for a doctor on a specific date.
     */
    public function getAvailableTimeSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        try {
            $timeSlots = $this->calendarService->getAvailableTimeSlots(
                $request->get('doctor_id'),
                $request->get('date')
            );

            return response()->json([
                'success' => true,
                'data' => $timeSlots
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading time slots: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get calendar statistics.
     */
    public function getCalendarStats(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'department' => 'nullable|integer|exists:departments,id'
        ]);

        try {
            $stats = $this->calendarService->getCalendarStats(
                $request->get('date'),
                $request->get('department')
            );

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading calendar statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointments for a specific date range.
     */
    public function getAppointments(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'doctor_id' => 'nullable|integer|exists:doctors,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'status' => 'nullable|string|in:Waiting,In Progress,Completed,Cancelled'
        ]);

        try {
            $appointments = $this->calendarService->getAppointmentsWithFilters(
                $request->get('start_date'),
                $request->get('end_date'),
                $request->get('doctor_id'),
                $request->get('department_id'),
                $request->get('status')
            );

            return response()->json([
                'success' => true,
                'data' => $appointments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctor schedule for a specific date.
     */
    public function getDoctorSchedule(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date'
        ]);

        try {
            $schedule = $this->calendarService->getDoctorSchedulePublic(
                $request->get('doctor_id'),
                $request->get('date')
            );

            return response()->json([
                'success' => true,
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading doctor schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for appointment overlaps.
     */
    public function checkOverlaps(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'doctor_id' => 'nullable|integer|exists:doctors,id'
        ]);

        try {
            $overlaps = $this->calendarService->detectOverlapsForDateRange(
                $request->get('start_date'),
                $request->get('end_date'),
                $request->get('doctor_id')
            );

            return response()->json([
                'success' => true,
                'data' => $overlaps
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking overlaps: ' . $e->getMessage()
            ], 500);
        }
    }
}
