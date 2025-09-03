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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Medicine code (AYM-001)
            $table->integer('medicine_type_id'); // master_medicine_type
            $table->integer('medicine_category_id'); // master_medicine_category
            $table->integer('manufacturer_id'); // master_manufacturer
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('supplier_email')->nullable();
            $table->string('strength_dosage')->nullable();
            $table->integer('measurement_id')->nullable(); // master_measurement
            $table->text('main_ingredients')->nullable();
            $table->text('indications_usage')->nullable();
            $table->string('batch_number'); // BATCH-2025-001
            $table->date('manufacturing_date');
            $table->date('expiry_date');
            $table->integer('initial_stock_quantity');
            $table->integer('minimum_stock_level')->nullable();
            $table->string('storage_location')->nullable(); // Shelf A-12
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->text('storage_instructions')->nullable();
            $table->text('side_effects_precautions')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=deactivate,1=active');
            $table->tinyInteger('track_expiry')->default(1)->comment('0=false,1=true');
            $table->tinyInteger('save_type')->default(1)->comment('0=draft,1=submitted');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
