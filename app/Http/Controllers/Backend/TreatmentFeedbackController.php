<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TreatmentFeedback;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\TreatmentPlan;
use App\Models\TherapistAssignment;
use App\Services\PatientSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreatmentFeedbackController extends Controller
{
    /**
     * Display a listing of treatment feedbacks
     */
    public function index(Request $request)
    {
        $pagename = 'Treatment Feedback';
        $breadcrumb = 'Treatment Feedback';

        return view('backend.treatment_plan.feedback', compact(
            'pagename',
            'breadcrumb'
        ));
    }

    /**
     * Store a newly created feedback
     */
    public function store(Request $request)
    {
        $feedback = new TreatmentFeedback();
        $feedback->patient_id = $request->patient_id;
        $feedback->therapist_id = $request->doctor_id ?: Auth::id(); // Use current user if no doctor specified
        $feedback->treatment_plan_id = $request->treatment_plan_id ?: null;
        $feedback->symptom_assessments = json_decode($request->symptom_assessments, true);
        $feedback->dosha_balance = json_decode($request->dosha_balance, true);
        $feedback->patient_satisfaction = json_decode($request->patient_satisfaction, true);
        $feedback->staff_behavior_rating = $request->staff_behavior_rating;
        $feedback->facility_cleanliness_rating = $request->facility_cleanliness_rating;
        $feedback->recommendation_rating = $request->recommendation_rating;
        $feedback->patient_comments = $request->patient_comments;
        $feedback->doctor_assessment = $request->doctor_assessment;
        $feedback->follow_up_recommendations = json_decode($request->follow_up_recommendations, true);
        $feedback->next_followup_date = $request->next_followup_date;
        $feedback->overall_outcome = $request->overall_outcome;
        $feedback->created_by = Auth::id();
        $feedback->updated_by = Auth::id();

        if ($feedback->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Feedback saved successfully!']);
            }
            return redirect()->route('treatment-plan.feedback')->with('success', 'Feedback saved successfully!');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save feedback. Please try again.']);
            }
            return redirect()->back()->with('error', 'Failed to save feedback. Please try again.');
        }
    }

    /**
     * Search patients using PatientSearchService
     */
    public function searchPatient(Request $request)
    {
        $patientSearchService = new PatientSearchService();
        return $patientSearchService->searchPatients($request, 'treatment-feedback');
    }

    /**
     * Get patient details for feedback form
     */
    public function getPatientDetails(Request $request)
    {
        $patientId = $request->patient_id;

        $patient = Patient::with(['treatmentPlans.createdBy'])
            ->where('id', $patientId)
            ->first();

        if ($patient) {
            return response()->json([
                'success' => true,
                'patient' => $patient
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Patient not found'
        ]);
    }

    /**
     * Get treatment plan details
     */
    public function getTreatmentPlanDetails(Request $request)
    {
        $treatmentPlanId = $request->treatment_plan_id;

        $treatmentPlan = TreatmentPlan::with(['patient', 'createdBy'])
            ->where('id', $treatmentPlanId)
            ->first();

        if ($treatmentPlan) {
            return response()->json([
                'success' => true,
                'treatmentPlan' => $treatmentPlan
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Treatment plan not found'
        ]);
    }

    /**
     * Get previous evaluations for a patient
     */
    public function getPreviousEvaluations(Request $request)
    {
        $patientId = $request->patient_id;

        $evaluations = TreatmentFeedback::with(['patient', 'therapist', 'treatmentPlan'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'evaluations' => $evaluations
        ]);
    }
}
