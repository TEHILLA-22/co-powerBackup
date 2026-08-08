// database/migrations/2025_01_01_000005_create_products_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->string('ean', 20)->unique();
            $table->string('sku', 50)->unique();
            $table->string('upc', 20)->nullable();

            // Basic info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();

            // Classification
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // ============ MOQ CONFIGURATION ============
            // Base MOQ for the product (applies to all variants unless overridden)
            $table->integer('moq')->default(1)->comment('Minimum Order Quantity for this product');
            $table->boolean('moq_enforced')->default(true)->comment('Whether MOQ is enforced');
            $table->integer('moq_increment')->default(1)->comment('Order quantity must be multiple of this number');
            $table->json('tier_moq')->nullable()->comment('Tier-specific MOQs: {"tier_id": moq}');
            // ==========================================

            // Media
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->timestamp('sale_start_date')->nullable();
            $table->timestamp('sale_end_date')->nullable();

            // SEO
            $table->json('seo_tags')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ean');
            $table->index('sku');
            $table->index('moq');
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
