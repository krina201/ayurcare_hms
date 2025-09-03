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
        Schema::create('medicine_dispenses', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('prescription_id');
            $table->integer('dispensed_by'); // user_id
            $table->date('dispense_date');
            $table->integer('payment_mode'); // master_payment_mode table
            $table->enum('dispense_status', ['ready', 'partial', 'out_of_stock']);
            $table->text('special_instructions')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('gst_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('receipt_number')->unique(); // RX-2025-XXXX
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_dispenses');
    }
};
