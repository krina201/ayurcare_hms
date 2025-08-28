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
}
