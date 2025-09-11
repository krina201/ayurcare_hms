<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'therapist_id',
        'treatment_plan_id',
        'symptom_assessments',
        'dosha_balance',
        'patient_satisfaction',
        'staff_behavior_rating',
        'facility_cleanliness_rating',
        'recommendation_rating',
        'patient_comments',
        'doctor_assessment',
        'follow_up_recommendations',
        'next_followup_date',
        'overall_outcome',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'symptom_assessments' => 'array',
        'dosha_balance' => 'array',
        'patient_satisfaction' => 'array',
        'follow_up_recommendations' => 'array',
        'next_followup_date' => 'date'
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Doctor::class, 'therapist_id');
    }

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
