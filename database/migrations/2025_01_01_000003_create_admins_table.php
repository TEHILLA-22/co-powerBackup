// database/migrations/2025_01_01_000003_create_admins_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
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
            
            // ============ ADMIN ROLE ============
            $table->enum('role', ['admin', 'super_admin'])->default('admin');
            // ====================================
            
            // Account status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // Two Factor Authentication
            $table->string('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            
            // Security
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('force_password_change')->default(false);
            
            // Preferences
            $table->string('language', 2)->default('en');
            $table->string('timezone')->default('UTC');
            
            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
};