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
        Schema::create('medicine_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_id')->unique();
            $table->date('purchase_date');
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->integer('vendor_id')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->string('gstin')->nullable();
            $table->integer('payment_mode_id');
            $table->text('notes')->nullable();
            $table->string('invoice_file_path')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1)
                ->comment('0=draft,1=Completed,3=Cancelled');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_restocks');
    }
};
