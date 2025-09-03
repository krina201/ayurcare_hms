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

        Schema::create('accounting', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');  //master_transaction_categories table 
            $table->integer('payment_mode'); //master_payment_mode table
            $table->string('transaction_id')->unique();
            $table->tinyInteger('transaction_type')->comment('0=receipt,1=payment');
            $table->date('transaction_date');
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->string('patient_vendor')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting');
    }
};
