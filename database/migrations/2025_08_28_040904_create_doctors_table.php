<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('email')->unique();
            $table->string('mobile', 15);
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('doctor_id')->unique(); // e.g., DOC-2025-0012
            $table->string('specialty')->nullable();
            $table->string('qualification')->nullable();
            $table->integer('experience')->default(0);
            $table->string('registration_number');
            $table->decimal('consultation_fee', 8, 2)->default(0);
            $table->decimal('followup_fee', 8, 2)->nullable();
            $table->enum('commission_type', ['Fixed', 'Percentage'])->default('Percentage');
            $table->decimal('commission_value', 8, 2)->nullable();
            $table->json('available_days')->nullable();
            $table->time('morning_from')->nullable();
            $table->time('morning_to')->nullable();
            $table->time('evening_from')->nullable();
            $table->time('evening_to')->nullable();
            $table->integer('time_per_consultation')->default(20);
            $table->json('expertise_areas')->nullable();
            $table->json('panchkarma_treatments')->nullable();
            $table->string('degree_certificate')->nullable();
            $table->string('registration_certificate')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
