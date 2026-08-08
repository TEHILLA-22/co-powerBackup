// database/migrations/2025_01_01_000009_create_quotes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Customer snapshot
            $table->string('customer_company');
            $table->string('customer_email');
            $table->string('customer_tier')->nullable();

            // Quote status
            $table->enum('status', [
                'draft',           // Customer building quote
                'submitted',       // Customer submitted for review
                'processing',      // Admin processing review
                'approved',        // Quote approved
                'rejected',        // Quote rejected
                'expired',         // Quote expired
                'converted',       // Quote converted to order
            ])->default('draft');

            // Quote items (JSON snapshot)
            $table->json('items');

            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            // Validity
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            // Review
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Conversion
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('quote_number');
            $table->index('valid_until');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
};
