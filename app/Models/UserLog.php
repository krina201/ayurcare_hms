<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    protected $table = 'user_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'action',
        'data',
        'ip_address',
        'action_time'
    ];

    protected $casts = [
        'data' => 'array',
        'action_time' => 'datetime',
    ];
}
