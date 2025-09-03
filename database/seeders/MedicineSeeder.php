<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MasterMedicineType;
use App\Models\MasterMedicineCategory;
use App\Models\MasterManufacturer;
use App\Models\MasterMeasurement;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create master data if it doesn't exist
        $medicineType = MasterMedicineType::firstOrCreate(['name' => 'Tablet']);
        $medicineCategory = MasterMedicineCategory::firstOrCreate(['name' => 'Ayurvedic']);
        $manufacturer = MasterManufacturer::firstOrCreate(['name' => 'AyurCare Pharmaceuticals']);
        $measurement = MasterMeasurement::firstOrCreate(['name' => 'Tablets']);

        // Create sample medicines
        $medicines = [
            [
                'name' => 'Ashwagandha Tablets',
                'code' => 'ASH-001',
                'medicine_type_id' => $medicineType->id,
                'medicine_category_id' => $medicineCategory->id,
                'manufacturer_id' => $manufacturer->id,
                'measurement_id' => $measurement->id,
                'strength_dosage' => '500mg',
                'initial_stock_quantity' => 100,
                'minimum_stock_level' => 10,
                'purchase_price' => 50.00,
                'selling_price' => 80.00,
                'batch_number' => 'BATCH-001',
                'manufacturing_date' => now()->subMonths(2),
                'expiry_date' => now()->addYears(2),
                'track_expiry' => true,
                'status' => 1,
            ],
            [
                'name' => 'Tulsi Capsules',
                'code' => 'TUL-002',
                'medicine_type_id' => $medicineType->id,
                'medicine_category_id' => $medicineCategory->id,
                'manufacturer_id' => $manufacturer->id,
                'measurement_id' => $measurement->id,
                'strength_dosage' => '250mg',
                'initial_stock_quantity' => 150,
                'minimum_stock_level' => 15,
                'purchase_price' => 30.00,
                'selling_price' => 60.00,
                'batch_number' => 'BATCH-002',
                'manufacturing_date' => now()->subMonths(1),
                'expiry_date' => now()->addYears(3),
                'track_expiry' => true,
                'status' => 1,
            ],
            [
                'name' => 'Neem Tablets',
                'code' => 'NEE-003',
                'medicine_type_id' => $medicineType->id,
                'medicine_category_id' => $medicineCategory->id,
                'manufacturer_id' => $manufacturer->id,
                'measurement_id' => $measurement->id,
                'strength_dosage' => '300mg',
                'initial_stock_quantity' => 80,
                'minimum_stock_level' => 8,
                'purchase_price' => 40.00,
                'selling_price' => 70.00,
                'batch_number' => 'BATCH-003',
                'manufacturing_date' => now()->subMonths(3),
                'expiry_date' => now()->addYears(2),
                'track_expiry' => true,
                'status' => 1,
            ],
            [
                'name' => 'Ginger Powder',
                'code' => 'GIN-004',
                'medicine_type_id' => $medicineType->id,
                'medicine_category_id' => $medicineCategory->id,
                'manufacturer_id' => $manufacturer->id,
                'measurement_id' => $measurement->id,
                'strength_dosage' => '100g',
                'initial_stock_quantity' => 50,
                'minimum_stock_level' => 5,
                'purchase_price' => 25.00,
                'selling_price' => 45.00,
                'batch_number' => 'BATCH-004',
                'manufacturing_date' => now()->subMonths(1),
                'expiry_date' => now()->addYears(1),
                'track_expiry' => true,
                'status' => 1,
            ],
            [
                'name' => 'Turmeric Capsules',
                'code' => 'TUR-005',
                'medicine_type_id' => $medicineType->id,
                'medicine_category_id' => $medicineCategory->id,
                'manufacturer_id' => $manufacturer->id,
                'measurement_id' => $measurement->id,
                'strength_dosage' => '400mg',
                'initial_stock_quantity' => 120,
                'minimum_stock_level' => 12,
                'purchase_price' => 35.00,
                'selling_price' => 65.00,
                'batch_number' => 'BATCH-005',
                'manufacturing_date' => now()->subMonths(2),
                'expiry_date' => now()->addYears(2),
                'track_expiry' => true,
                'status' => 1,
            ],
        ];

        foreach ($medicines as $medicineData) {
            Medicine::firstOrCreate(
                ['code' => $medicineData['code']],
                $medicineData
            );
        }

        $this->command->info('Sample medicines seeded successfully!');
    }
}
