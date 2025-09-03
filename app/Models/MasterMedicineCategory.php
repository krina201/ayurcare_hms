<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMedicineCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_medicine_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];
}
