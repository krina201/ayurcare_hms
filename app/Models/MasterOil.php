<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterOil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_oils';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
