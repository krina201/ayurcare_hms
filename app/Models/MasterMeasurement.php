<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMeasurement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_measurement';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'status',
    ];
}
