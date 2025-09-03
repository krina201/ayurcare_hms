<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMedicineType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_medicine_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];
}
