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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('doctor_id');
            $table->date('prescription_date');
            $table->string('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('special_notes')->nullable();
            $table->date('follow_up_date')->nullable();
            // $table->string('priority')->default('Normal');
            // $table->string('status')->default('active');
            $table->enum('priority', ['Normal', 'Urgent', 'High Priority'])->default('Normal');
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=draft,1=active,2=completed,3=cancelled');

            // $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
