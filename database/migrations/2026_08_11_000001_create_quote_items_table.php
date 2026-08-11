<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('basket_key'); // matches the array key used by the quote basket logic
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity');
            $table->string('variant_type', 20)->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'basket_key']);

            $table->index('product_variant_id');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_items');
    }
};