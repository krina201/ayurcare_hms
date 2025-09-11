<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';
    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'appointment_id',
        'prescription_id',
        'treatment_plan_id',
        'dispense_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'payment_method',
        'payment_reference',
        'notes',
        'bill_items',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'bill_items' => 'array',
    ];

    // Accessor to ensure bill_items is always an array
    public function getBillItemsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 2); // 2 = paid
    }

    public function scopePending($query)
    {
        return $query->where('status', 1); // 1 = pending
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 3); // 3 = overdue
    }

    public function scopeToday($query)
    {
        return $query->whereDate('invoice_date', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('invoice_date', Carbon::now()->month);
    }

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function dispense(): BelongsTo
    {
        return $this->belongsTo(MedicineDispense::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(MasterPaymentMode::class, 'payment_method');
    }

    // Accessors
    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            0 => 'bg-gray-100 text-gray-800', // draft
            1 => 'bg-ayur-yellow-100 text-ayur-yellow-800', // pending
            2 => 'bg-ayur-green-100 text-ayur-green-800', // paid
            3 => 'bg-red-100 text-red-800', // overdue
            4 => 'bg-red-100 text-red-800', // cancelled
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedTotalAmountAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getFormattedPaidAmountAttribute()
    {
        return '₹' . number_format($this->paid_amount, 2);
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getFormattedOutstandingAmountAttribute()
    {
        return '₹' . number_format($this->outstanding_amount, 2);
    }



    public function getIsOverdueAttribute()
    {
        return $this->due_date < Carbon::today() && $this->status !== 2; // 2 = paid
    }

    // Methods
    public function updateOutstandingAmount()
    {
        $outstandingAmount = $this->total_amount - $this->paid_amount;

        if ($outstandingAmount <= 0) {
            $this->status = 2; // 2 = paid
        } elseif ($this->is_overdue) {
            $this->status = 3; // 3 = overdue
        } else {
            $this->status = 1; // 1 = pending
        }

        $this->save();
    }

    public function addPayment($amount, $method = null, $reference = null)
    {
        $this->paid_amount += $amount;
        if ($method !== null) {
            $this->payment_method = $method;
        }
        $this->payment_reference = $reference;
        $this->updateOutstandingAmount();
    }
}
