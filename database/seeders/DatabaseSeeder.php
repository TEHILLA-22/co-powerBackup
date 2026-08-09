<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerTier;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create default customer tiers
        $tiers = [
            ['name' => 'Standard', 'slug' => 'standard', 'discount_percentage' => 0, 'display_order' => 1],
            ['name' => 'Premium', 'slug' => 'premium', 'discount_percentage' => 5, 'display_order' => 2],
            ['name' => 'VIP', 'slug' => 'vip', 'discount_percentage' => 10, 'display_order' => 3],
        ];
        foreach ($tiers as $tier) {
            CustomerTier::create($tier);
        }

        // Create an admin user (Sian admin)
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Sian',
            'email' => 'odjojitehilla@gmail.com',
            'password' => Hash::make('password'),
            'company_name' => 'Copower Wholesale Ltd',
            'is_verified' => true,
            'verified_at' => now(),
            'customer_tier_id' => 1,
        ]);
         // We'll create roles later

        // Create some demo categories
        Category::create(['name' => 'Baby Care', 'slug' => 'baby-care', 'is_active' => true]);
        Category::create(['name' => 'Hair Products', 'slug' => 'hair-products', 'is_active' => true]);
        Category::create(['name' => 'Cosmetics', 'slug' => 'cosmetics', 'is_active' => true]);
        Category::create(['name' => 'Oral Hygiene', 'slug' => 'oral-hygiene', 'is_active' => true]);
        Category::create(['name' => 'Skin Care', 'slug' => 'skin-care', 'is_active' => true]);

        // Create the super admin
        $this->call(AdminSeeder::class);
    }
}