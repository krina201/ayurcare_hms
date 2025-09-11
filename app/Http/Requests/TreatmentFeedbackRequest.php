<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TreatmentFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'treatment_plan_id' => 'nullable|exists:treatment_plans,id',
            'doctor_id' => 'required|exists:doctors,id',
            'evaluation_date' => 'required|date',

            // Symptom Assessment
            'symptoms' => 'nullable|array',
            'symptoms.*.name' => 'required_with:symptoms|string|max:255',
            'symptoms.*.before' => 'required_with:symptoms|numeric|min:0|max:10',
            'symptoms.*.after' => 'required_with:symptoms|numeric|min:0|max:10',

            // Dosha Balance
            'dosha_vata' => 'nullable|numeric',
            'dosha_pitta' => 'nullable|numeric',
            'dosha_kapha' => 'nullable|numeric',

            // Patient Ratings
            'overall_satisfaction' => 'nullable|numeric|min:1|max:5',
            'staff_behavior_rating' => 'nullable|numeric|min:1|max:5',
            'facility_cleanliness_rating' => 'nullable|numeric|min:1|max:5',
            'recommendation_rating' => 'nullable|numeric|min:1|max:5',
            'patient_comments' => 'nullable|string',

            // Doctor's Evaluation
            'doctor_assessment' => 'nullable|string',
            'followup_recommendations' => 'nullable|array',
            'next_followup_date' => 'nullable|date|after_or_equal:today',

            // Treatment Progress
            'total_days' => 'nullable|integer|min:0',
            'completed_days' => 'nullable|integer|min:0',

            'status' => 'required|in:draft,completed'
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient selection is required.',
            'patient_id.exists' => 'Selected patient is invalid.',
            'doctor_id.required' => 'Doctor selection is required.',
            'doctor_id.exists' => 'Selected doctor is invalid.',
            'evaluation_date.required' => 'Evaluation date is required.',
            'evaluation_date.date' => 'Evaluation date must be a valid date.',
            'symptoms.array' => 'Symptoms must be provided as an array.',
            'symptoms.*.name.required_with' => 'Symptom name is required.',
            'symptoms.*.before.required_with' => 'Before rating is required.',
            'symptoms.*.before.numeric' => 'Before rating must be a number.',
            'symptoms.*.before.min' => 'Before rating must be at least 0.',
            'symptoms.*.before.max' => 'Before rating must not exceed 10.',
            'symptoms.*.after.required_with' => 'After rating is required.',
            'symptoms.*.after.numeric' => 'After rating must be a number.',
            'symptoms.*.after.min' => 'After rating must be at least 0.',
            'symptoms.*.after.max' => 'After rating must not exceed 10.',
            'overall_satisfaction.numeric' => 'Overall satisfaction must be a number.',
            'overall_satisfaction.min' => 'Overall satisfaction must be at least 1.',
            'overall_satisfaction.max' => 'Overall satisfaction must not exceed 5.',
            'staff_behavior_rating.numeric' => 'Staff behavior rating must be a number.',
            'staff_behavior_rating.min' => 'Staff behavior rating must be at least 1.',
            'staff_behavior_rating.max' => 'Staff behavior rating must not exceed 5.',
            'facility_cleanliness_rating.numeric' => 'Facility cleanliness rating must be a number.',
            'facility_cleanliness_rating.min' => 'Facility cleanliness rating must be at least 1.',
            'facility_cleanliness_rating.max' => 'Facility cleanliness rating must not exceed 5.',
            'recommendation_rating.numeric' => 'Recommendation rating must be a number.',
            'recommendation_rating.min' => 'Recommendation rating must be at least 1.',
            'recommendation_rating.max' => 'Recommendation rating must not exceed 5.',
            'next_followup_date.date' => 'Next follow-up date must be a valid date.',
            'next_followup_date.after_or_equal' => 'Next follow-up date must be today or later.',
            'total_days.integer' => 'Total days must be a number.',
            'total_days.min' => 'Total days must be at least 0.',
            'completed_days.integer' => 'Completed days must be a number.',
            'completed_days.min' => 'Completed days must be at least 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either draft or completed.'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'patient_id' => 'patient',
            'treatment_plan_id' => 'treatment plan',
            'doctor_id' => 'doctor',
            'evaluation_date' => 'evaluation date',
            'overall_satisfaction' => 'overall satisfaction',
            'staff_behavior_rating' => 'staff behavior rating',
            'facility_cleanliness_rating' => 'facility cleanliness rating',
            'recommendation_rating' => 'recommendation rating',
            'patient_comments' => 'patient comments',
            'doctor_assessment' => 'doctor assessment',
            'followup_recommendations' => 'follow-up recommendations',
            'next_followup_date' => 'next follow-up date',
            'total_days' => 'total days',
            'completed_days' => 'completed days',
        ];
    }
}
