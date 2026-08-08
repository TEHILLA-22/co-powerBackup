// database/migrations/2025_01_01_000008_create_order_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot of product at order time
            $table->string('product_name');
            $table->string('product_sku', 50);
            $table->string('product_ean', 20);
            $table->string('variant_type', 20);

            // Quantities and pricing
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);

            // Applied rules
            $table->json('applied_discounts')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->integer('shipped_quantity')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
