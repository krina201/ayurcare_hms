<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'full_name' => 'Dr. Rahul Sharma',
                'gender' => 'Male',
                'email' => 'rahul.sharma@ayurcare.com',
                'mobile' => '9876543210',
                'dob' => '1980-05-15',
                'address' => '123 Ayurveda Lane, Mumbai',
                'doctor_id' => 'DOC-2025-0001',
                'specialty' => 'General Ayurveda',
                'qualification' => 'BAMS, MD (Ayurveda)',
                'experience' => 15,
                'registration_number' => 'AYR001',
                'consultation_fee' => 800.00,
                'followup_fee' => 500.00,
                'commission_type' => 'Percentage',
                'commission_value' => 20.00,
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'morning_from' => '09:00:00',
                'morning_to' => '12:00:00',
                'evening_from' => '16:00:00',
                'evening_to' => '19:00:00',
                'time_per_consultation' => 30,
                'expertise_areas' => ['Kayachikitsa', 'Panchakarma', 'Rasayana'],
                'panchkarma_treatments' => ['Abhyanga', 'Shirodhara', 'Basti'],
                'status' => true,
            ],
            [
                'full_name' => 'Dr. Ananya Patel',
                'gender' => 'Female',
                'email' => 'ananya.patel@ayurcare.com',
                'mobile' => '9876543211',
                'dob' => '1985-08-22',
                'address' => '456 Wellness Street, Delhi',
                'doctor_id' => 'DOC-2025-0002',
                'specialty' => 'Kayachikitsa',
                'qualification' => 'BAMS, MD (Kayachikitsa)',
                'experience' => 12,
                'registration_number' => 'AYR002',
                'consultation_fee' => 1000.00,
                'followup_fee' => 600.00,
                'commission_type' => 'Percentage',
                'commission_value' => 25.00,
                'available_days' => ['monday', 'wednesday', 'friday', 'saturday'],
                'morning_from' => '08:00:00',
                'morning_to' => '11:00:00',
                'evening_from' => '15:00:00',
                'evening_to' => '18:00:00',
                'time_per_consultation' => 45,
                'expertise_areas' => ['Kayachikitsa', 'Rasayana', 'Vajikarana'],
                'panchkarma_treatments' => ['Virechana', 'Nasya', 'Raktamokshana'],
                'status' => true,
            ],
            [
                'full_name' => 'Dr. Vikram Singh',
                'gender' => 'Male',
                'email' => 'vikram.singh@ayurcare.com',
                'mobile' => '9876543212',
                'dob' => '1978-12-10',
                'address' => '789 Healing Road, Bangalore',
                'doctor_id' => 'DOC-2025-0003',
                'specialty' => 'Panchakarma',
                'qualification' => 'BAMS, MD (Panchakarma)',
                'experience' => 20,
                'registration_number' => 'AYR003',
                'consultation_fee' => 1200.00,
                'followup_fee' => 800.00,
                'commission_type' => 'Fixed',
                'commission_value' => 300.00,
                'available_days' => ['tuesday', 'thursday', 'saturday'],
                'morning_from' => '10:00:00',
                'morning_to' => '13:00:00',
                'evening_from' => '17:00:00',
                'evening_to' => '20:00:00',
                'time_per_consultation' => 60,
                'expertise_areas' => ['Panchakarma', 'Kayachikitsa', 'Shalya'],
                'panchkarma_treatments' => ['Basti', 'Shirodhara', 'Abhyanga', 'Virechana'],
                'status' => true,
            ],
        ];

        foreach ($doctors as $doctorData) {
            Doctor::create($doctorData);
        }
    }
}
