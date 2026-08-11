<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\CustomerTier;
use App\Services\QuoteBasketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuoteBasketPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(): User
    {
        static $userCount = 0;
        $userCount++;

        $tier = CustomerTier::create([
            'name' => 'Standard ' . $userCount,
            'slug' => 'standard-' . $userCount,
            'discount_percentage' => 0,
            'is_active' => true,
        ]);

        return User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test' . $userCount . '@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Test Co',
            'is_active' => true,
            'is_verified' => true,
            'customer_tier_id' => $tier->id,
        ]);
    }

    protected function makeVariant(): ProductVariant
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-SKU-1',
            'ean' => '1234567890123',
            'is_active' => true,
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'TEST-SKU-1',
            'variant_type' => 'unit',
            'base_price' => 10.00,
            'stock_quantity' => 100,
            'allow_backorder' => false,
            'in_stock' => true,
            'is_active' => true,
        ]);
    }

    public function test_quote_items_persist_across_logout_and_login(): void
    {
        $user = $this->makeUser();
        $variant = $this->makeVariant();

        // Add item while logged in
        $this->actingAs($user)
            ->post(route('customer.product.add-to-quote', $variant->product->slug), [
                'quantity' => 5,
                'variant_type' => 'unit',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('quote_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
        ]);

        // Log out - session wiped, DB rows must survive
        $this->post('/logout');
        $this->assertGuest();

        $this->assertDatabaseCount('quote_items', 1);

        // Log back in - basket must be restored
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $basket = app(QuoteBasketService::class);
        $this->assertEquals(1, $basket->count());
        $this->assertArrayHasKey((string) $variant->id, $basket->items());
        $this->assertEquals(5, $basket->items()[(string) $variant->id]['quantity']);
    }

    public function test_quote_items_are_scoped_to_the_owning_user(): void
    {
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $variant = $this->makeVariant();

        $this->actingAs($userA)
            ->post(route('customer.product.add-to-quote', $variant->product->slug), ['quantity' => 3, 'variant_type' => 'unit'])
            ->assertRedirect();

        // User B sees an empty basket
        $basketB = app(QuoteBasketService::class);
        $this->actingAs($userB);
        $this->assertEquals(0, $basketB->count());
    }

    public function test_clear_removes_quote_items(): void
    {
        $user = $this->makeUser();
        $variant = $this->makeVariant();

        $this->actingAs($user)
            ->post(route('customer.product.add-to-quote', $variant->product->slug), ['quantity' => 3, 'variant_type' => 'unit'])
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('quote.clear'))
            ->assertRedirect();

        $this->assertDatabaseCount('quote_items', 0);
    }
}
