<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $table = 'doctors';
    protected $primaryKey = 'id';

    protected $fillable = [
        'full_name',
        'gender',
        'email',
        'mobile',
        'dob',
        'address',
        'doctor_id',
        'specialty',
        'qualification',
        'experience',
        'registration_number',
        'consultation_fee',
        'followup_fee',
        'commission_type',
        'commission_value',
        'available_days',
        'morning_from',
        'morning_to',
        'evening_from',
        'evening_to',
        'time_per_consultation',
        'expertise_areas',
        'panchkarma_treatments',
        'degree_certificate',
        'registration_certificate',
        'photo',
        'status'
    ];

    protected $casts = [
        'available_days' => 'array',
        'expertise_areas' => 'array',
        'panchkarma_treatments' => 'array',
    ];

    // Relationship with department
    public function department()
    {
        return $this->belongsTo(Department::class, 'specialty', 'id');
    }

    // Relationship with appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Get panchkarma treatment categories
    public function panchkarmaTreatmentCategories()
    {
        if (empty($this->panchkarma_treatments) || !is_array($this->panchkarma_treatments)) {
            return collect();
        }

        return MasterTreatmentCategory::whereIn('id', $this->panchkarma_treatments)->get();
    }

    // Get expertise areas
    public function expertiseAreas()
    {
        if (empty($this->expertise_areas) || !is_array($this->expertise_areas)) {
            return collect();
        }

        return MasterExpertiseArea::whereIn('id', $this->expertise_areas)->get();
    }

    // Expertise areas accessor
    public function getExpertiseAreasAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Expertise areas mutator
    public function setExpertiseAreasAttribute($value)
    {
        $this->attributes['expertise_areas'] = is_array($value) ? json_encode($value) : $value;
    }

    // Panchkarma treatments accessor
    public function getPanchkarmaTreatmentsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Panchkarma treatments mutator
    public function setPanchkarmaTreatmentsAttribute($value)
    {
        $this->attributes['panchkarma_treatments'] = is_array($value) ? json_encode($value) : $value;
    }
}
