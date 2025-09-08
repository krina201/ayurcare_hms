<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPrakriti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_prakriti';

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];
}
