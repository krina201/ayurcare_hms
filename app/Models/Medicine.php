<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'medicine_type_id',
        'medicine_category_id',
        'manufacturer_id',
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'strength_dosage',
        'measurement_id',
        'main_ingredients',
        'indications_usage',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'initial_stock_quantity',
        'minimum_stock_level',
        'storage_location',
        'purchase_price',
        'selling_price',
        'mrp',
        'tax_rate',
        'storage_instructions',
        'side_effects_precautions',
        'notes',
        'status',
        'track_expiry',
        'save_type'
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'status' => 'integer',
        'track_expiry' => 'integer',
        'save_type' => 'integer'
    ];

    // Relationships
    public function medicineType()
    {
        return $this->belongsTo(MasterMedicineType::class, 'medicine_type_id');
    }

    public function medicineCategory()
    {
        return $this->belongsTo(MasterMedicineCategory::class, 'medicine_category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(MasterManufacturer::class, 'manufacturer_id');
    }

    public function measurement()
    {
        return $this->belongsTo(MasterMeasurement::class, 'measurement_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
            ->where('track_expiry', 1);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('initial_stock_quantity <= minimum_stock_level');
    }

    public function scopeDraft($query)
    {
        return $query->where('save_type', 0);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('save_type', 1);
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getIsLowStockAttribute()
    {
        return $this->minimum_stock_level && $this->initial_stock_quantity <= $this->minimum_stock_level;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price > 0) {
            return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
        }
        return 0;
    }

    public function getSaveTypeTextAttribute()
    {
        return $this->save_type == 0 ? 'Draft' : 'Submitted';
    }
}
