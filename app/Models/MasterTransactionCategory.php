<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTransactionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_transaction_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function accountings()
    {
        return $this->hasMany(Accounting::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
