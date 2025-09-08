<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTreatmentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_treatment_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Get therapists (doctors) who can perform this treatment category
     */
    public function therapists()
    {
        return \App\Models\Doctor::where('status', 1)
            ->whereJsonContains('panchkarma_treatments', $this->id)
            ->get();
    }

    /**
     * Scope to get active treatment categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
