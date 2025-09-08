<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TherapistAssignment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MasterRoom;
use App\Models\TreatmentPlan;
use Carbon\Carbon;

class TherapistAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some doctors (therapists)
        $doctors = Doctor::where('status', 1)->take(3)->get();
        if ($doctors->isEmpty()) {
            $this->command->warn('No active doctors found. Please seed doctors first.');
            return;
        }

        // Get some patients
        $patients = Patient::take(5)->get();
        if ($patients->isEmpty()) {
            $this->command->warn('No patients found. Please seed patients first.');
            return;
        }

        // Get some rooms
        $rooms = MasterRoom::where('status', 1)->take(3)->get();
        if ($rooms->isEmpty()) {
            $this->command->warn('No active rooms found. Please seed rooms first.');
            return;
        }

        // Get or create a treatment plan
        $treatmentPlan = TreatmentPlan::first();
        if (!$treatmentPlan) {
            $treatmentPlan = TreatmentPlan::create([
                'patient_id' => $patients->first()->id,
                'doctor_id' => $doctors->first()->id,
                'treatment_details' => 'Test Treatment Plan',
                'status' => 1,
                'created_by' => 1
            ]);
        }

        // Create sample therapist assignments for today and tomorrow
        $dates = [today(), today()->addDay()];

        foreach ($dates as $date) {
            foreach ($doctors as $doctor) {
                // Create 2-3 assignments per therapist per day
                $assignmentCount = rand(2, 3);

                for ($i = 0; $i < $assignmentCount; $i++) {
                    $startHour = 9 + ($i * 2); // 9 AM, 11 AM, 1 PM, etc.
                    $startTime = Carbon::createFromTime($startHour, 0);
                    $endTime = $startTime->copy()->addHour();

                    TherapistAssignment::create([
                        'treatment_plan_id' => $treatmentPlan->id,
                        'patient_id' => $patients->random()->id,
                        'therapist_id' => $doctor->id,
                        'room_id' => $rooms->random()->id,
                        'assigned_by' => 1,
                        'assignment_date' => $date->format('Y-m-d'),
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                        'duration_minutes' => 60,
                        'treatment_details' => 'Sample treatment - ' . ['Massage', 'Therapy', 'Consultation'][rand(0, 2)],
                        'materials_required' => 'Basic treatment materials',
                        'special_instructions' => 'Follow standard procedure',
                        'status' => rand(0, 2) // Random status: 0=Pending, 1=In Progress, 2=Completed
                    ]);
                }
            }
        }

        $this->command->info('Therapist assignments seeded successfully!');
    }
}
