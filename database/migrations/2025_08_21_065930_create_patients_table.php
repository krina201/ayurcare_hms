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
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('uhid')->unique();
            $table->string('full_name');
            $table->string('gender', 10);
            $table->unsignedInteger('age');
            $table->string('mobile', 20);
            $table->string('emergency_contact', 20)->nullable();
            $table->string('aadhaar_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('allergies')->nullable();
            $table->string('prakriti')->nullable();
            $table->json('doshas')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('registration_type', 10)->nullable();
            $table->string('photo_path')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
