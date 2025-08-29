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
        'email',
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

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Relationship with prescriptions
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    // Get active prescriptions
    public function activePrescriptions()
    {
        return $this->prescriptions()->where('status', 'active');
    }

    // Get recent prescriptions
    public function recentPrescriptions($limit = 5)
    {
        return $this->prescriptions()
            ->with('doctor')
            ->orderBy('prescription_date', 'desc')
            ->limit($limit);
    }

    // Get current medications from active prescriptions
    public function getCurrentMedications()
    {
        return $this->activePrescriptions()
            ->with('items')
            ->get()
            ->flatMap(function ($prescription) {
                return $prescription->items;
            });
    }

    // Check if patient has any active prescriptions
    public function hasActivePrescriptions()
    {
        return $this->activePrescriptions()->exists();
    }
}
