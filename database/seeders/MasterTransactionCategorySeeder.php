<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterTransactionCategory;

class MasterTransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Patient Consultation', 'description' => 'Fees for doctor consultations'],
            ['name' => 'Medicine Sale', 'description' => 'Revenue from medicine sales'],
            ['name' => 'Panchkarma Treatment', 'description' => 'Revenue from Panchkarma therapies'],
            ['name' => 'Laboratory Tests', 'description' => 'Revenue from lab tests and diagnostics'],
            ['name' => 'Accommodation', 'description' => 'Revenue from patient accommodation'],
            ['name' => 'Equipment Rental', 'description' => 'Revenue from medical equipment rental'],
            ['name' => 'Other Services', 'description' => 'Miscellaneous patient services'],
            ['name' => 'Salary & Wages', 'description' => 'Employee salary payments'],
            ['name' => 'Medicine Purchase', 'description' => 'Purchase of medicines and supplies'],
            ['name' => 'Equipment Purchase', 'description' => 'Purchase of medical equipment'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, gas bills'],
            ['name' => 'Rent', 'description' => 'Building and property rent'],
            ['name' => 'Maintenance', 'description' => 'Building and equipment maintenance'],
            ['name' => 'Insurance', 'description' => 'Business insurance payments'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotional expenses'],
            ['name' => 'Other Expenses', 'description' => 'Miscellaneous business expenses'],
        ];

        foreach ($categories as $category) {
            MasterTransactionCategory::create($category);
        }
    }
}

// php artisan db:seed --class=MasterTransactionCategorySeeder