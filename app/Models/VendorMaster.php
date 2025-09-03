<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorMaster extends Model
{
    use HasFactory, SoftDeletes;

    // Explicit table name since it's not the default plural form
    protected $table = 'vendor_master';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];
}
