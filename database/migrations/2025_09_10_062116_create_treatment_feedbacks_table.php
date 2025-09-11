<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treatment_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('therapist_id');
            $table->integer('treatment_plan_id');

            // Symptom assessments (JSON)
            $table->json('symptom_assessments')->nullable();

            // Dosha balance evaluation (JSON)
            $table->json('dosha_balance')->nullable();

            // Patient satisfaction ratings
            $table->json('patient_satisfaction')->nullable();
            $table->decimal('staff_behavior_rating', 3, 2)->nullable();
            $table->decimal('facility_cleanliness_rating', 3, 2)->nullable();
            $table->decimal('recommendation_rating', 3, 2)->nullable();

            // Patient feedback
            $table->text('patient_comments')->nullable();

            // Doctor's evaluation
            $table->text('doctor_assessment')->nullable();
            $table->json('follow_up_recommendations')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->enum('overall_outcome', ['excellent', 'good', 'moderate', 'poor'])->nullable();

            // Audit fields
            $table->integer('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_feedbacks');
    }
};
