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
        Schema::create('treatment_tracker', function (Blueprint $table) {
            $table->id();
            $table->integer('therapist_assignment_id');
            $table->date('session_date');
            $table->time('session_time');
            $table->integer('room_id');
            $table->text('therapist_notes')->nullable();
            $table->text('patient_feedback')->nullable();
            $table->json('materials_used')->nullable(); // Store materials used in session
            $table->json('vital_signs')->nullable(); // Store vital signs data
            $table->json('tracker_images')->nullable(); // Store uploaded image paths
            $table->tinyInteger('status')->default(0); // 0=Pending, 1=In Progress, 2=Completed, 3=Cancelled
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
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
        Schema::dropIfExists('treatment_tracker');
    }
};
