<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uhid',
        'full_name',
        'gender',
        'age',
        'mobile',
        'emergency_contact',
        'aadhaar_number',
        'address',
        'allergies',
        'prakriti',
        'doshas',
        'registration_date',
        'registration_type',
        'photo_path',
        'created_by',
    ];

    protected $casts = [
        'doshas' => 'array',
        'registration_date' => 'date',
    ];

    // Use default route key name 'id' for implicit binding

    // No self-referencing relation needed here
}
