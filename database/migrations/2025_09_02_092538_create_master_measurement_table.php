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
        Schema::create('master_measurement', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // e.g., Milligram, Gram, Milliliter
            $table->string('short_name')->unique(); // e.g., mg, g, ml
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_measurement');
    }
};
