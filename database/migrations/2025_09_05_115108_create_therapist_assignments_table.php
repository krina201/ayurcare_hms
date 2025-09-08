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
        Schema::create('therapist_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('treatment_plan_id');
            $table->integer('patient_id');
            $table->integer('therapist_id');
            $table->integer('room_id');
            $table->integer('assigned_by');
            $table->date('assignment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->text('treatment_details');
            $table->text('materials_required')->nullable();
            $table->text('special_instructions')->nullable();
            $table->tinyInteger('status')->default(1)
                ->comment('0=Pending, 1=In Progress ,2=Completed,3=Cancelled,4=Preparing');
            // $table->enum('priority', ['Normal', 'High', 'Urgent'])->default('Normal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_assignments');
    }
};
