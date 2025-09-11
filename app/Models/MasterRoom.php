<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_rooms';
    protected $primaryKey = 'id';
    protected $fillable = [
        'room_number',
        'room_type',
        'capacity',
        'charges',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'capacity' => 'integer',
        'charges' => 'decimal:2',
    ];

    // Relationships
    public function assignments(): HasMany
    {
        return $this->hasMany(TherapistAssignment::class, 'room_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 1);
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 2);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            1 => 'Available',
            2 => 'Maintenance',
            0 => 'Inactive',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            1 => 'bg-success',
            2 => 'bg-warning',
            0 => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 1;
    }

    public function isUnderMaintenance()
    {
        return $this->status === 2;
    }

    public function getCurrentAssignment($date = null)
    {
        $date = $date ?: today();

        return $this->assignments()
            ->whereDate('assignment_date', $date)
            ->where('status', 1) // In Progress
            ->with(['patient', 'therapist'])
            ->first();
    }

    public function getNextAssignment($date = null)
    {
        $date = $date ?: today();

        return $this->assignments()
            ->whereDate('assignment_date', $date)
            ->where('status', 0) // Pending
            ->with(['patient', 'therapist'])
            ->orderBy('start_time')
            ->first();
    }

    public function getTodaysAssignments($date = null)
    {
        $date = $date ?: today();

        return $this->assignments()
            ->whereDate('assignment_date', $date)
            ->with(['patient', 'therapist'])
            ->orderBy('start_time')
            ->get();
    }
}
