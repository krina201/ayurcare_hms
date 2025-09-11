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

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->integer('patient_id');
            $table->integer('appointment_id')->nullable();
            $table->integer('dispense_id')->nullable();
            $table->integer('prescription_id')->nullable();
            $table->integer('treatment_plan_id')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0)->nullable();
            $table->tinyInteger('status')->default(1)
                ->comment('0=draft,1=pending,2=paid,3=overdue,4=cancelled');
            $table->integer('payment_method')->default('pending'); //master_payment_mode table
            $table->string('payment_reference')->nullable();
            $table->json('bill_items')->nullable(); // Store bill items as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
