<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineRestock extends Model
{
    protected $table = 'medicine_restocks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'purchase_id',
        'purchase_date',
        'invoice_number',
        'invoice_date',
        'vendor_id',
        'vendor_name',
        'vendor_contact',
        'gstin',
        'payment_mode_id',
        'notes',
        'invoice_file_path',
        'subtotal',
        'discount',
        'gst_amount',
        'total_amount',
        'status',
        'created_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorMaster::class, 'vendor_id');
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(MasterPaymentMode::class, 'payment_mode_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicineRestockItem::class, 'restock_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 0);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 1);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 3);
    }

    // Helper methods
    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->purchase_date->format('d-M-Y');
    }
}
