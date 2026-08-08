<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        Admin::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@copower.com',
            'password' => Hash::make('SuperAdmin2025!'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        // Create Regular Admin
        Admin::create([
            'first_name' => 'Sales',
            'last_name' => 'Admin',
            'email' => 'sales@copower.com',
            'password' => Hash::make('SalesAdmin2025!'),
            'role' => 'admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);
    }
}