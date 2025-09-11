<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineRestockItem extends Model
{
    protected $table = 'medicine_restock_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'restock_id',
        'medicine_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'quantity',
        'measurement_id',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function restock(): BelongsTo
    {
        return $this->belongsTo(MedicineRestock::class, 'restock_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(MasterMeasurement::class, 'measurement_id');
    }

    // Helper methods
    public function getFormattedUnitPriceAttribute()
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return '₹' . number_format($this->total_price, 2);
    }
}
