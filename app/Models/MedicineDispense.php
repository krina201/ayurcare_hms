<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDispense extends Model
{
    use HasFactory;

    protected $table = 'medicine_dispenses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'prescription_id',
        'dispensed_by',
        'dispense_date',
        'payment_mode',
        'dispense_status',
        'special_instructions',
        'subtotal',
        'gst_amount',
        'total_amount',
        'receipt_number',
    ];

    protected $casts = [
        'dispense_date' => 'date',
        'subtotal' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function paymentMode()
    {
        return $this->belongsTo(MasterPaymentMode::class, 'payment_mode');
    }

    public function items()
    {
        return $this->hasMany(MedicineDispenseItem::class, 'dispense_id');
    }

    // Accessors
    public function getPaymentMethodTextAttribute()
    {
        return $this->paymentMode ? $this->paymentMode->name : 'N/A';
    }

    public function getDispenseStatusTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->dispense_status));
    }

    public function getDispenseStatusClassAttribute()
    {
        return match ($this->dispense_status) {
            'ready' => 'bg-green-100 text-green-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'out_of_stock' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }
}
