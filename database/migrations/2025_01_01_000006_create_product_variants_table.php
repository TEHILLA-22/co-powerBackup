// database/migrations/2025_01_01_000006_create_product_variants_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Variant types: unit, case, layer, pallet
            $table->enum('variant_type', ['unit', 'case', 'layer', 'pallet']);
            $table->string('variant_name')->nullable();

            // Quantity
            $table->integer('quantity_per_unit')->default(1);
            $table->integer('units_per_case')->nullable();
            $table->integer('cases_per_layer')->nullable();
            $table->integer('layers_per_pallet')->nullable();

            // ============ VARIANT MOQ ============
            // Override product MOQ for this variant
            $table->integer('moq')->nullable()->comment('Override product MOQ');
            $table->integer('moq_increment')->nullable()->comment('Override product increment');
            // ====================================

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();

            // Stock
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->timestamp('last_stock_update')->nullable();

            // Shipping
            $table->decimal('weight_kg', 10, 3)->nullable();
            $table->decimal('length_cm', 10, 2)->nullable();
            $table->decimal('width_cm', 10, 2)->nullable();
            $table->decimal('height_cm', 10, 2)->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('in_stock')->default(false);
            $table->boolean('allow_backorder')->default(false);
            $table->integer('min_order_quantity')->default(1);
            $table->integer('max_order_quantity')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['product_id', 'variant_type']);
            $table->index('moq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
