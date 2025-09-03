<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test doctor if not exists
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@ayurcare.com'],
            [
                'name' => 'Dr. Sharma',
                'email' => 'doctor@ayurcare.com',
                'password' => bcrypt('password'),
                'role_id' => 2, // Assuming 2 is doctor role
            ]
        );

        // Create sample patients
        $patients = [
            [
                'uhid' => 'AYR-2025-0001',
                'full_name' => 'Rajesh Kumar',
                'email' => 'rajesh@example.com',
                'gender' => 'Male',
                'age' => 35,
                'mobile' => '9876543210',
                'address' => '123 Main Street, Delhi',
                'allergies' => 'None',
                'prakriti' => 'Vata-Pitta',
                'registration_date' => now()->subDays(30),
                'registration_type' => 'OPD',
            ],
            [
                'uhid' => 'AYR-2025-0002',
                'full_name' => 'Meera Patel',
                'email' => 'meera@example.com',
                'gender' => 'Female',
                'age' => 28,
                'mobile' => '9876543211',
                'address' => '456 Park Avenue, Mumbai',
                'allergies' => 'Dairy',
                'prakriti' => 'Kapha',
                'registration_date' => now()->subDays(15),
                'registration_type' => 'OPD',
            ],
            [
                'uhid' => 'AYR-2025-0003',
                'full_name' => 'Arjun Desai',
                'email' => 'arjun@example.com',
                'gender' => 'Male',
                'age' => 42,
                'mobile' => '9876543212',
                'address' => '789 Lake Road, Bangalore',
                'allergies' => 'None',
                'prakriti' => 'Pitta-Kapha',
                'registration_date' => now()->subDays(7),
                'registration_type' => 'OPD',
            ],
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::firstOrCreate(
                ['uhid' => $patientData['uhid']],
                $patientData
            );

            // Create a sample prescription for each patient
            $prescription = Prescription::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'prescription_date' => now()->subDays(5),
                ],
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'prescription_date' => now()->subDays(5),
                    'chief_complaint' => 'General wellness',
                    'diagnosis' => 'Maintenance of health',
                    'notes' => 'Regular ayurvedic supplements',
                    'status' => 1, // Active
                ]
            );

            // Create prescription items
            $prescriptionItems = [
                [
                    'name' => 'Ashwagandha Tablets',
                    'dosage' => '1 tablet',
                    'frequency' => 'twice daily',
                    'duration' => '30 days',
                    'instructions' => 'Take after meals',
                ],
                [
                    'name' => 'Tulsi Capsules',
                    'dosage' => '1 capsule',
                    'frequency' => 'once daily',
                    'duration' => '30 days',
                    'instructions' => 'Take in the morning',
                ],
            ];

            foreach ($prescriptionItems as $itemData) {
                PrescriptionItem::firstOrCreate(
                    [
                        'prescription_id' => $prescription->id,
                        'name' => $itemData['name'],
                    ],
                    array_merge(['prescription_id' => $prescription->id], $itemData)
                );
            }
        }

        $this->command->info('Sample patients and prescriptions seeded successfully!');
    }
}
