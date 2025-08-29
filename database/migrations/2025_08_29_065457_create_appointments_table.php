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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->integer('patient_id');
            $table->integer('doctor_id');
            $table->integer('department_id');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('appointment_mode', ['OPD', 'Panchkarma'])->default('OPD');
            $table->enum('consultation_type', ['First Visit', 'Follow Up'])->default('First Visit');
            // $table->enum('status', ['Scheduled', 'Waiting', 'In Progress', 'Completed', 'Cancelled', 'No Show'])->default('Scheduled');
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=Waiting,1=In Progress,2=Completed,3=Cancelled,4=Scheduled');

            $table->text('chief_complaint')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('consultation_fee', 8, 2);
            // $table->enum('payment_status', ['Pending', 'Paid', 'Refunded'])->default('Pending');
            $table->tinyInteger('payment_status')
                ->default(0)
                ->comment('0=Pending,1=Paid,2=Refunded');
            $table->integer('queue_number')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->integer('cancelled_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
