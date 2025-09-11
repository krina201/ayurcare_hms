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
        Schema::create('medicine_restock_items', function (Blueprint $table) {
            $table->id();
            $table->integer('restock_id');
            $table->integer('medicine_id');
            $table->string('batch_number');
            $table->date('manufacturing_date');
            $table->date('expiry_date');
            $table->integer('quantity');
            $table->integer('measurement_id');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_restock_items');
    }
};
