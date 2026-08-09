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
        Admin::firstOrCreate(
            ['email' => 'odjojitehilla@gmail.com'],
            [
                'first_name' => 'Copower',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'force_password_change' => true,
            ]
        );
    }
}