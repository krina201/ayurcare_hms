<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientLog extends Model
{
    use HasFactory;

    protected $table = 'patient_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'user_id',
        'action',
        'data',
        'ip_address',
        'action_time'
    ];

    protected $casts = [
        'data' => 'array',
        'action_time' => 'datetime',
    ];
}
