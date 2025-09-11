<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TherapistAssignment extends Model
{
    use HasFactory;

    protected $table = 'therapist_assignments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'treatment_plan_id',
        'patient_id',
        'therapist_id',
        'room_id',
        'assigned_by',
        'assignment_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'treatment_details',
        'materials_required',
        'special_instructions',
        'status',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'materials_required' => 'array'
    ];

    // Relationships
    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'therapist_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MasterRoom::class, 'room_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function treatmentSessions()
    {
        return $this->hasMany(TreatmentTracker::class, 'therapist_assignment_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('assignment_date', today());
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 1);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 2);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('assignment_date', $date);
    }

    // Accessors
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            0 => 'bg-yellow-100 text-yellow-800', // Pending
            1 => 'bg-green-100 text-green-800',   // In Progress
            2 => 'bg-blue-100 text-blue-800',    // Completed
            3 => 'bg-red-100 text-red-800',      // Cancelled
            4 => 'bg-blue-100 text-blue-800',    // Preparing
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Preparing',
            default => 'Unknown',
        };
    }



    public function getFormattedTimeSlotAttribute()
    {
        return Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
    }

    public function getDurationTextAttribute()
    {
        return $this->duration_minutes . ' mins';
    }

    // Helper methods
    public function canComplete()
    {
        return $this->status === 1; // In Progress
    }

    public function canCancel()
    {
        return in_array($this->status, [0, 4]); // Pending, Preparing
    }

    // Get treatment category name with proper validation
    public function getTreatmentCategoryNameAttribute()
    {
        // Check if treatment plan exists and matches patient_id
        if ($this->treatmentPlan && $this->treatmentPlan->patient_id == $this->patient_id) {
            return $this->treatmentPlan->treatmentCategory->name ?? $this->treatment_details;
        }

        // Fallback to treatment_details if no valid treatment plan
        return $this->treatment_details;
    }

    // Safe accessor for materials_required
    public function getMaterialsUsedTextAttribute()
    {
        if (!$this->materials_required || !is_array($this->materials_required)) {
            return 'None';
        }

        $materialsUsed = $this->materials_required['materials_used'] ?? [];

        if (empty($materialsUsed)) {
            return 'None';
        }

        return collect($materialsUsed)->map(function ($material) {
            return $material['name'] . ' (' . $material['quantity'] . ' ' . $material['unit'] . ')';
        })->implode(', ');
    }

    // Safe accessor for treatment_details
    public function getTreatmentDetailsTextAttribute()
    {
        if (empty($this->treatment_details)) {
            return 'No details available';
        }

        return Str::limit($this->treatment_details, 100);
    }
}
