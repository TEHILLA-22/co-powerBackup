<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_verified_by']);
            $table->foreign('admin_verified_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_verified_by']);
            $table->foreign('admin_verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};