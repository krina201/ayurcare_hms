<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'description',
        'head_doctor_name',
        'status'
    ];



    public function activeDoctors()
    {
        return $this->hasMany(Doctor::class)->where('is_active', true);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
