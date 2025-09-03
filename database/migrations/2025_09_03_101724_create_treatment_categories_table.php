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
        Schema::create('treatment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Category name (e.g., Panchakarma, Surgery, etc.)
            $table->string('code')->nullable(); // Short code for category
            $table->text('description')->nullable(); // Extra details
            $table->tinyInteger('status')->default(1)->comment('0=deactivate,1=active');  // 1 = active, 0 = inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_categories');
    }
};
