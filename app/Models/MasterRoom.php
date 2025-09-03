<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_rooms';
    protected $primaryKey = 'id';
    protected $fillable = [
        'room_number',
        'room_type',
        'capacity',
        'charges',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'capacity' => 'integer',
        'charges' => 'decimal:2',
    ];
}
