// database/migrations/2025_01_01_000002_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Personal details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            
            // Company
            $table->string('company_name');
            $table->string('company_registration_number')->nullable();
            $table->string('vat_number')->nullable();
            
            // Tiers
            $table->foreignId('customer_tier_id')->nullable()->constrained()->nullOnDelete();
            
            // ============ VERIFICATION STATES ============
            // OTP / Email verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Admin verification (business validation)
            $table->boolean('is_admin_verified')->default(false);
            $table->timestamp('admin_verified_at')->nullable();
            $table->foreignId('admin_verified_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Account status
            $table->boolean('is_active')->default(true);
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            
            // =============================================
            
            // Preferences
            $table->string('language', 2)->default('en');
            $table->string('currency', 3)->default('GBP');
            $table->string('timezone')->default('UTC');
            
            // Security
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // OTP fields
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('otp_attempts')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('is_verified');
            $table->index('is_admin_verified');
            $table->index('is_active');
            $table->index('otp_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};