<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TreatmentTracker;
use App\Models\TherapistAssignment;
use App\Models\MasterRoom;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TreatmentTrackerController extends Controller
{
    /**
     * Display the treatment tracker for a specific treatment plan
     */
    public function show($treatmentPlanId)
    {
        $pagename = 'Panchkarma Treatment Tracker';
        $breadcrumb = 'Panchkarma Treatment Tracker';

        $treatmentPlan = \App\Models\TreatmentPlan::with([
            'patient',
            'treatmentCategory',
            'therapistAssignments.therapist',
            'therapistAssignments.room',
            'therapistAssignments.treatmentSessions'
        ])->findOrFail($treatmentPlanId);

        $assignments = $treatmentPlan->therapistAssignments->sortBy('assignment_date');
        $rooms = MasterRoom::where('status', 1)->get();

        // Get required medicines from treatment plan (oils and herbs)
        $requiredMedicines = collect();

        // Get oils
        if ($treatmentPlan->oils_required && is_array($treatmentPlan->oils_required)) {
            $oils = Medicine::whereIn('id', $treatmentPlan->oils_required)
                ->where('medicine_type_id', 3) // Oil type
                ->with('medicineType', 'measurement')
                ->get();
            $requiredMedicines = $requiredMedicines->merge($oils);
        }

        // Get herbs
        if ($treatmentPlan->herbs_required && is_array($treatmentPlan->herbs_required)) {
            $herbs = Medicine::whereIn('id', $treatmentPlan->herbs_required)
                ->where('medicine_type_id', 2) // Herb type
                ->with('medicineType', 'measurement')
                ->get();
            $requiredMedicines = $requiredMedicines->merge($herbs);
        }

        // Calculate progress from assignments
        $totalDays = $assignments->count();
        $completedDays = $assignments->where('status', 2)->count();
        $progressPercentage = $totalDays > 0 ? round(($completedDays / $totalDays) * 100, 1) : 0;

        // Get today's assignment
        $todaysAssignment = $assignments->where('assignment_date', today())->first();

        // Get previous sessions (completed ones)
        $previousSessions = $assignments->where('status', 2)->take(5);

        return view('backend.treatment_plan.treatment_tracker', compact(
            'treatmentPlan',
            'assignments',
            'rooms',
            'requiredMedicines',
            'totalDays',
            'completedDays',
            'progressPercentage',
            'todaysAssignment',
            'previousSessions',
            'pagename',
            'breadcrumb'
        ));
    }

    /**
     * Store treatment session data
     */
    public function store(Request $request)
    {
        // Determine validation rules based on action
        $action = $request->action;

        // For start action, minimal validation
        if ($action === 'start') {
            $validator = Validator::make(
                $request->all(),
                [
                    'therapist_assignment_id' => 'required|exists:therapist_assignments,id',
                    'action' => 'required|in:start,save_draft,complete',
                    'session_date' => 'nullable|date',
                    'session_time' => 'nullable',
                    'room_id' => 'nullable|exists:master_rooms,id',
                    'therapist_notes' => 'nullable|string',
                    'patient_feedback' => 'nullable|string',
                    'materials_used' => 'nullable|array',
                    'materials_used.*.quantity' => 'nullable|numeric|min:0',
                    'vital_signs' => 'nullable|array',
                    'vital_signs.bp' => 'nullable|string',
                    'vital_signs.pulse' => 'nullable|string',
                    'vital_signs.temperature' => 'nullable|string',
                    'vital_signs.weight' => 'nullable|string',
                    'tracker_images' => 'nullable|array',
                    'tracker_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                ],
                [
                    // Custom validation error messages for start action
                    'therapist_assignment_id.required' => 'Therapist assignment is required.',
                    'therapist_assignment_id.exists' => 'Selected therapist assignment is invalid.',
                    'action.required' => 'Action field is required.',
                    'action.in' => 'Invalid action selected.',
                    'session_date.date' => 'Session date must be a valid date.',
                    'room_id.exists' => 'Selected room is invalid.',
                    'therapist_notes.string' => 'Therapist notes must be a valid text.',
                    'patient_feedback.string' => 'Patient feedback must be a valid text.',
                    'materials_used.array' => 'Materials used must be a valid array.',
                    'materials_used.*.quantity.numeric' => 'Material quantity must be a number.',
                    'materials_used.*.quantity.min' => 'Material quantity must be at least 0.',
                    'vital_signs.array' => 'Vital signs must be a valid array.',
                    'vital_signs.bp.string' => 'Blood pressure must be a valid text.',
                    'vital_signs.pulse.string' => 'Pulse rate must be a valid text.',
                    'vital_signs.temperature.string' => 'Temperature must be a valid text.',
                    'vital_signs.weight.string' => 'Weight must be a valid text.',
                    'tracker_images.array' => 'Images must be a valid array.',
                    'tracker_images.*.image' => 'Uploaded file must be an image.',
                    'tracker_images.*.mimes' => 'Image must be of type: jpeg, png, jpg, gif.',
                    'tracker_images.*.max' => 'Image size must not exceed 5MB.',
                ]
            );
        } else {
            // For save_draft and complete actions, require all fields
            $validator = Validator::make(
                $request->all(),
                [
                    'therapist_assignment_id' => 'required|exists:therapist_assignments,id',
                    'action' => 'required|in:start,save_draft,complete',
                    'session_date' => 'required|date',
                    'session_time' => 'required',
                    'room_id' => 'required|exists:master_rooms,id',
                    'therapist_notes' => 'required|string|min:10',
                    'patient_feedback' => 'nullable|string',
                    'materials_used' => 'nullable|array',
                    'materials_used.*.quantity' => 'nullable|numeric|min:0',
                    'vital_signs' => 'nullable|array',
                    'vital_signs.bp' => 'nullable|string',
                    'vital_signs.pulse' => 'nullable|string',
                    'vital_signs.temperature' => 'nullable|string',
                    'vital_signs.weight' => 'nullable|string',
                    'tracker_images' => 'nullable|array',
                    'tracker_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                ],
                [
                    // Custom validation error messages for save_draft and complete actions
                    'therapist_assignment_id.required' => 'Therapist assignment is required.',
                    'therapist_assignment_id.exists' => 'Selected therapist assignment is invalid.',
                    'action.required' => 'Action field is required.',
                    'action.in' => 'Invalid action selected.',
                    'session_date.required' => 'Session date field is required.',
                    'session_date.date' => 'Session date must be a valid date.',
                    'session_time.required' => 'Session time field is required.',
                    'room_id.required' => 'Room selection is required.',
                    'room_id.exists' => 'Selected room is invalid.',
                    'therapist_notes.required' => 'Therapist notes field is required.',
                    'therapist_notes.string' => 'Therapist notes must be a valid text.',
                    'therapist_notes.min' => 'Therapist notes must be at least 10 characters long.',
                    'patient_feedback.string' => 'Patient feedback must be a valid text.',
                    'materials_used.array' => 'Materials used must be a valid array.',
                    'materials_used.*.quantity.numeric' => 'Material quantity must be a number.',
                    'materials_used.*.quantity.min' => 'Material quantity must be at least 0.',
                    'vital_signs.array' => 'Vital signs must be a valid array.',
                    'vital_signs.bp.string' => 'Blood pressure must be a valid text.',
                    'vital_signs.pulse.string' => 'Pulse rate must be a valid text.',
                    'vital_signs.temperature.string' => 'Temperature must be a valid text.',
                    'vital_signs.weight.string' => 'Weight must be a valid text.',
                    'tracker_images.array' => 'Images must be a valid array.',
                    'tracker_images.*.image' => 'Uploaded file must be an image.',
                    'tracker_images.*.mimes' => 'Image must be of type: jpeg, png, jpg, gif.',
                    'tracker_images.*.max' => 'Image size must not exceed 5MB.',
                ]
            );
        }

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $assignment = TherapistAssignment::findOrFail($request->therapist_assignment_id);

        // Check if assignment belongs to current user or user has permission
        if (Auth::user()->role_id != 1 && $assignment->therapist_id != Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to update this assignment');
        }

        // Handle file uploads
        $uploadedImages = [];
        if ($request->hasFile('tracker_images')) {
            foreach ($request->file('tracker_images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('backend-assets/media/uploads/treatment_tracker', $filename, 'public');
                $uploadedImages[] = $path;
            }
        }

        // Prepare materials used data
        $materialsUsed = [];
        if ($request->materials_used) {
            foreach ($request->materials_used as $medicineId => $material) {
                if (isset($material['quantity']) && $material['quantity'] > 0) {
                    $medicine = Medicine::find($medicineId);
                    if ($medicine) {
                        $materialsUsed[] = [
                            'medicine_id' => $medicineId,
                            'name' => $medicine->name,
                            'quantity' => $material['quantity'],
                            'unit' => $medicine->measurement->name ?? 'unit',
                            'type' => $medicine->medicineType->name ?? 'Medicine'
                        ];
                    }
                }
            }
        }

        // Prepare vital signs data
        $vitalSigns = [];
        if ($request->vital_signs) {
            foreach ($request->vital_signs as $key => $value) {
                if (!empty($value)) {
                    $vitalSigns[$key] = $value;
                }
            }
        }

        // Determine status based on action
        $status = match ($request->action) {
            'start' => 1, // In Progress
            'save_draft' => 0, // Pending
            'complete' => 2, // Completed
            default => 0
        };

        // Set default values for start action if fields are empty
        $sessionDate = $request->session_date ?: today();
        $sessionTime = $request->session_time ?: now()->format('H:i');
        $roomId = $request->room_id ?: $assignment->room_id;
        $therapistNotes = $request->therapist_notes ?: 'Session started';

        // Check if tracker already exists for this assignment
        $tracker = TreatmentTracker::where('therapist_assignment_id', $request->therapist_assignment_id)
            ->where('session_date', $sessionDate)
            ->first();

        if ($tracker) {
            // Update existing tracker
            $tracker->update([
                'session_time' => Carbon::parse($sessionDate . ' ' . $sessionTime),
                'room_id' => $roomId,
                'therapist_notes' => $therapistNotes,
                'patient_feedback' => $request->patient_feedback,
                'materials_used' => $materialsUsed,
                'vital_signs' => $vitalSigns,
                'tracker_images' => array_merge($tracker->tracker_images ?? [], $uploadedImages),
                'status' => $status,
                'updated_by' => Auth::id(),
            ]);

            // Update timestamps based on action
            if ($request->action === 'start' && !$tracker->started_at) {
                $tracker->update(['started_at' => now()]);
            } elseif ($request->action === 'complete') {
                $tracker->update([
                    'completed_at' => now(),
                    'started_at' => $tracker->started_at ?? now()
                ]);
            }
        } else {
            // Create new tracker
            $tracker = TreatmentTracker::create([
                'therapist_assignment_id' => $request->therapist_assignment_id,
                'session_date' => $sessionDate,
                'session_time' => Carbon::parse($sessionDate . ' ' . $sessionTime),
                'room_id' => $roomId,
                'therapist_notes' => $therapistNotes,
                'patient_feedback' => $request->patient_feedback,
                'materials_used' => $materialsUsed,
                'vital_signs' => $vitalSigns,
                'tracker_images' => $uploadedImages,
                'status' => $status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Set timestamps based on action
            if ($request->action === 'start') {
                $tracker->update(['started_at' => now()]);
            } elseif ($request->action === 'complete') {
                $tracker->update([
                    'started_at' => now(),
                    'completed_at' => now()
                ]);
            }
        }

        // Update assignment status if completing session
        if ($request->action === 'complete') {
            $assignment->update(['status' => 2]); // Completed
        } elseif ($request->action === 'start') {
            $assignment->update(['status' => 1]); // In Progress
        }

        $message = match ($request->action) {
            'start' => 'Treatment session started successfully',
            'save_draft' => 'Treatment session saved as draft',
            'complete' => 'Treatment session completed successfully',
            default => 'Treatment session updated successfully'
        };

        // Save the tracker
        if ($tracker->save()) {
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Something went wrong while saving the treatment session');
        }
    }

    /**
     * Get treatment session details
     */
    public function getSessionDetails($trackerId)
    {
        $tracker = TreatmentTracker::with([
            'therapistAssignment.therapist',
            'therapistAssignment.patient',
            'therapistAssignment.treatmentPlan',
            'room'
        ])->findOrFail($trackerId);

        return response()->json([
            'success' => true,
            'data' => [
                'tracker' => $tracker,
                'materials_used_text' => $tracker->materials_used_text,
                'vital_signs_text' => $tracker->vital_signs_text,
                'duration_text' => $tracker->duration_text
            ]
        ]);
    }

    /**
     * Cancel treatment session
     */
    public function cancel(Request $request, $trackerId)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tracker = TreatmentTracker::findOrFail($trackerId);

        // Check authorization
        if (Auth::user()->role_id != 1 && $tracker->therapistAssignment->therapist_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to cancel this session'
            ], 403);
        }

        $tracker->update([
            'status' => 3, // Cancelled
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
            'updated_by' => Auth::id()
        ]);

        // Update assignment status
        $tracker->therapistAssignment->update(['status' => 3]); // Cancelled

        return response()->json([
            'success' => true,
            'message' => 'Treatment session cancelled successfully'
        ]);
    }

    /**
     * Delete uploaded image
     */
    public function deleteImage(Request $request, $trackerId)
    {
        $tracker = TreatmentTracker::findOrFail($trackerId);

        // Check authorization
        if (Auth::user()->role_id != 1 && $tracker->therapistAssignment->therapist_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this image'
            ], 403);
        }

        $imagePath = $request->image_path;
        $images = $tracker->tracker_images ?? [];

        if (($key = array_search($imagePath, $images)) !== false) {
            unset($images[$key]);
            $tracker->update(['tracker_images' => array_values($images)]);

            // Delete file from storage
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
