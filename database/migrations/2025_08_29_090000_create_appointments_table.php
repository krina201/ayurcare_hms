<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('doctor_id');
            $table->integer('department_id');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('appointment_type', ['First Visit', 'Follow Up'])->default('First Visit');
            $table->enum('mode', ['OPD', 'Panchkarma'])->default('OPD');
            $table->enum('status', ['Waiting', 'In Progress', 'Completed', 'Cancelled'])->default('Waiting');
            $table->decimal('fee', 8, 2)->nullable();
            $table->text('chief_complaint')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->index(['appointment_date', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
