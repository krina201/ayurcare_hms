<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterManufacturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_manufacturer';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'contact_person',
        'address',
        'description',
        'status',
    ];
}
