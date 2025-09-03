<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentMode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_payment_mode';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function accountings()
    {
        return $this->hasMany(Accounting::class, 'payment_mode');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
