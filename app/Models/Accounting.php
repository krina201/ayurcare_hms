<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Accounting extends Model
{
    use HasFactory;

    protected $table = 'accounting';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
        'payment_mode',
        'transaction_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'reference_number',
        'patient_vendor',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'transaction_type' => 'integer',
    ];

    // Scopes
    public function scopeReceipts($query)
    {
        return $query->where('transaction_type', 0);
    }

    public function scopePayments($query)
    {
        return $query->where('transaction_type', 1);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', Carbon::now()->month);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(MasterTransactionCategory::class, 'category_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(MasterPaymentMode::class, 'payment_mode');
    }



    // Accessors
    public function getTransactionTypeTextAttribute()
    {
        return $this->transaction_type == 0 ? 'Receipt' : 'Payment';
    }

    public function getTransactionTypeClassAttribute()
    {
        return $this->transaction_type == 0 ? 'bg-ayur-green-100 text-ayur-green-800' : 'bg-ayur-yellow-100 text-ayur-yellow-800';
    }

    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }
}
