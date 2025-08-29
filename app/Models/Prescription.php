<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    protected $table = 'prescriptions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'prescription_date',
        'chief_complaint',
        'diagnosis',
        'notes',
        'special_notes',
        'follow_up_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
