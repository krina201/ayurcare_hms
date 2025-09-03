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
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('created_by');
            $table->integer('treatment_category');
            $table->string('procedure_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('dosha_report');
            $table->json('oils_required')->nullable();
            $table->json('herbs_required')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('consent_file_path')->nullable();
            $table->string('recommended_therapist')->nullable();
            $table->integer('room_allocation')->nullable();
            $table->json('day_wise_schedule')->nullable();
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=pending,1=active,2=completed,3=cancelled');
            $table->tinyInteger('type')
                ->default(0)
                ->comment('0=draft,1=save');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
