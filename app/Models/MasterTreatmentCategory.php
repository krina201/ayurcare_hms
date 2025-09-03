<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTreatmentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_treatment_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
