<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;
    protected $table = 'prescription_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }

    // Helper method to get medicine name (from relationship or stored name)
    public function getMedicineNameAttribute()
    {
        return $this->medicine ? $this->medicine->name : $this->name;
    }

    // Helper method to get full medicine details with strength
    public function getFullMedicineNameAttribute()
    {
        if ($this->medicine) {
            return $this->medicine->name . ($this->medicine->strength_dosage ? ' (' . $this->medicine->strength_dosage . ')' : '');
        }
        return $this->name;
    }
}
