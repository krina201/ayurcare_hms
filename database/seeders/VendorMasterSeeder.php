<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorMaster;

class VendorMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            'Ayurvedic Herbs Pvt Ltd',
            'Himalaya Wellness',
            'Dabur Ayurveda',
            'Patanjali Ayurved',
            'Kerala Ayurveda Ltd',
        ];

        foreach ($vendors as $vendor) {
            VendorMaster::updateOrCreate(
                ['name' => $vendor],
                ['status' => 1]
            );
        }
    }
}

// php artisan db:seed --class=VendorMasterSeeder