<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TreatmentTracker extends Model
{
    use HasFactory;

    protected $table = 'treatment_tracker';
    protected $primaryKey = 'id';

    protected $fillable = [
        'therapist_assignment_id',
        'session_date',
        'session_time',
        'room_id',
        'therapist_notes',
        'patient_feedback',
        'materials_used',
        'vital_signs',
        'tracker_images',
        'status',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'session_date' => 'date',
        'session_time' => 'datetime',
        'materials_used' => 'array',
        'vital_signs' => 'array',
        'tracker_images' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => 'integer',
    ];

    // Relationships
    public function therapistAssignment(): BelongsTo
    {
        return $this->belongsTo(TherapistAssignment::class, 'therapist_assignment_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MasterRoom::class, 'room_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
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

    public function scopeCancelled($query)
    {
        return $query->where('status', 3);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('session_date', $date);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->whereHas('therapistAssignment', function ($q) use ($therapistId) {
            $q->where('therapist_id', $therapistId);
        });
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            0 => 'bg-yellow-100 text-yellow-800',
            1 => 'bg-blue-100 text-blue-800',
            2 => 'bg-green-100 text-green-800',
            3 => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    public function getDurationTextAttribute()
    {
        $duration = $this->duration;
        if ($duration === null) {
            return 'N/A';
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    public function getFormattedSessionTimeAttribute()
    {
        return $this->session_time ? Carbon::parse($this->session_time)->format('H:i') : 'N/A';
    }

    public function getMaterialsUsedTextAttribute()
    {
        if (!$this->materials_used || !is_array($this->materials_used)) {
            return 'None';
        }

        return collect($this->materials_used)->map(function ($material) {
            return $material['name'] . ' (' . $material['quantity'] . ' ' . $material['unit'] . ')';
        })->implode(', ');
    }

    public function getVitalSignsTextAttribute()
    {
        if (!$this->vital_signs || !is_array($this->vital_signs)) {
            return 'Not recorded';
        }

        $vitals = [];
        if (isset($this->vital_signs['bp'])) {
            $vitals[] = 'BP: ' . $this->vital_signs['bp'];
        }
        if (isset($this->vital_signs['pulse'])) {
            $vitals[] = 'Pulse: ' . $this->vital_signs['pulse'];
        }
        if (isset($this->vital_signs['temperature'])) {
            $vitals[] = 'Temp: ' . $this->vital_signs['temperature'];
        }
        if (isset($this->vital_signs['weight'])) {
            $vitals[] = 'Weight: ' . $this->vital_signs['weight'];
        }

        return empty($vitals) ? 'Not recorded' : implode(', ', $vitals);
    }

    // Helper methods
    public function canStart()
    {
        return $this->status === 0; // Pending
    }

    public function canComplete()
    {
        return $this->status === 1; // In Progress
    }

    public function canCancel()
    {
        return in_array($this->status, [0, 1]); // Pending, In Progress
    }

    public function isCompleted()
    {
        return $this->status === 2;
    }

    public function isCancelled()
    {
        return $this->status === 3;
    }

    public function isInProgress()
    {
        return $this->status === 1;
    }

    public function isPending()
    {
        return $this->status === 0;
    }

    // Get patient through therapist assignment
    public function getPatientAttribute()
    {
        return $this->therapistAssignment?->patient;
    }

    // Get therapist through therapist assignment
    public function getTherapistAttribute()
    {
        return $this->therapistAssignment?->therapist;
    }

    // Get treatment plan through therapist assignment
    public function getTreatmentPlanAttribute()
    {
        return $this->therapistAssignment?->treatmentPlan;
    }
}
