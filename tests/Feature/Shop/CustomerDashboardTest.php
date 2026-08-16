<?php

namespace Tests\Feature\Shop;

use App\Models\CustomerTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(): User
    {
        $tier = CustomerTier::create([
            'name' => 'Standard Tier',
            'slug' => 'standard-tier',
            'discount_percentage' => 0,
            'is_active' => true,
        ]);

        return User::create([
            'first_name' => 'Daisy',
            'last_name' => 'Smith',
            'email' => 'daisy@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Smith Retail Ltd',
            'company_registration_number' => '12345678',
            'vat_number' => 'GB123456789',
            'mobile' => '+44 7700 900123',
            'is_active' => true,
            'is_verified' => true,
            'customer_tier_id' => $tier->id,
        ]);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_for_logged_in_user(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee('My Account')
            ->assertSee('Welcome back')
            ->assertSee('Daisy Smith')
            ->assertSee('Smith Retail Ltd')
            ->assertSee('daisy@example.com')
            ->assertSee('Standard Tier')
            ->assertSee('12345678')
            ->assertSee('GB123456789')
            ->assertSee('+44 7700 900123');
    }
}
