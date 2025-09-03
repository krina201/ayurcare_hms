<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDispenseItem extends Model
{
    use HasFactory;

    protected $table = 'medicine_dispense_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dispense_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function dispense()
    {
        return $this->belongsTo(MedicineDispense::class, 'dispense_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    // Accessors
    public function getFormattedUnitPriceAttribute()
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return '₹' . number_format($this->total_price, 2);
    }
}
