// database/migrations/2025_01_01_000007_create_orders_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Customer snapshot
            $table->string('customer_company');
            $table->string('customer_email');
            $table->string('customer_tier')->nullable();
            
            // ============ ORDER STATUS ============
            $table->enum('status', [
                'draft',          // Customer building cart
                'submitted',      // Customer submitted order
                'processing',     // Admin is reviewing
                'approved',       // Admin approved
                'rejected',       // Admin rejected
                'processing_fulfillment', // Being fulfilled
                'shipped',        // Shipped to customer
                'delivered',      // Delivered
                'cancelled',      // Cancelled
            ])->default('draft');
            
            // ============ REVIEW TRACKING ============
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            // ======================================
            
            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            
            // Shipping
            $table->text('shipping_address');
            $table->text('billing_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_weight', 10, 2)->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            
            // Payment
            $table->enum('payment_method', ['credit_account', 'bank_transfer', 'cheque', 'card'])->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Fulfillment dates
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('order_number');
            $table->index('submitted_at');
            $table->index(['status', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};