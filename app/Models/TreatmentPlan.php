<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'treatment_plans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'created_by',
        'treatment_category',
        'procedure_name',
        'start_date',
        'end_date',
        'dosha_report',
        'oils_required',
        'herbs_required',
        'special_instructions',
        'consent_file_path',
        'recommended_therapist',
        'room_allocation',
        'day_wise_schedule',
        'status',
        'type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'oils_required' => 'array',
        'herbs_required' => 'array',
        'day_wise_schedule' => 'array',
        'status' => 'integer',
        'type' => 'integer',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function treatmentCategory()
    {
        return $this->belongsTo(MasterTreatmentCategory::class, 'treatment_category');
    }

    public function room()
    {
        return $this->belongsTo(MasterRoom::class, 'room_allocation');
    }

    // Accessors for oils and herbs
    public function getOilsAttribute()
    {
        if (!$this->oils_required || !is_array($this->oils_required)) {
            return collect();
        }
        return MasterOil::whereIn('id', $this->oils_required)->get();
    }

    public function getHerbsAttribute()
    {
        if (!$this->herbs_required || !is_array($this->herbs_required)) {
            return collect();
        }
        return MasterHerb::whereIn('id', $this->herbs_required)->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 0);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 2);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 3);
    }

    public function scopeSaved($query)
    {
        return $query->where('type', 1);
    }

    public function scopeDraftType($query)
    {
        return $query->where('type', 0);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'Active',
            2 => 'Completed',
            3 => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            0 => 'bg-gray-200 text-gray-700',
            1 => 'bg-ayur-green-100 text-ayur-green-800',
            2 => 'bg-blue-100 text-blue-800',
            3 => 'bg-red-100 text-red-800',
            default => 'bg-gray-200 text-gray-700',
        };
    }

    public function getTypeTextAttribute()
    {
        return match ($this->type) {
            0 => 'Draft',
            1 => 'Saved',
            default => 'Unknown',
        };
    }

    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            return $start->diffInDays($end) + 1;
        }
        return 0;
    }

    public function getDurationTextAttribute()
    {
        $duration = $this->duration;
        return $duration . ' day' . ($duration > 1 ? 's' : '');
    }

    public function getOilsTextAttribute()
    {
        return $this->oils->pluck('name')->implode(', ');
    }

    public function getHerbsTextAttribute()
    {
        return $this->herbs->pluck('name')->implode(', ');
    }

    // Relationship with therapist assignments
    public function therapistAssignments()
    {
        return $this->hasMany(TherapistAssignment::class);
    }
}
