// database/migrations/2025_01_01_000001_create_customer_tiers_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Standard, Premium, VIP
            $table->string('slug')->unique();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('priority_shipping')->default(false);
            $table->boolean('dedicated_account_manager')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_tiers');
    }
};