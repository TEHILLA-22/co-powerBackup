<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpVerificationMail;
use App\Models\CustomerTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CustomerTier::create([
            'id' => 1,
            'name' => 'Standard',
            'slug' => 'standard',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Efe',
            'last_name' => 'Test',
            'email' => 'idem@example.com',
            'mobile' => '+2341111111111',
            'company_name' => 'Test Ltd',
            'address_line_1' => '10 Test Street',
            'city' => 'London',
            'postal_code' => 'E1 1AA',
            'country_code' => 'GB',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
        ], $overrides);
    }

    private function idempotencyKey(array $payload): string
    {
        return 'idempotency:' . sha1(
            'POST|' . url('/register') . '|' . http_build_query($payload),
        );
    }

    public function test_a_request_matching_a_completed_replay_is_served_without_re_executing(): void
    {
        Mail::fake();

        Cache::put($this->idempotencyKey($this->payload()), [
            'url' => url('/verify-otp'),
            'flashes' => ['success' => 'Your account has been created.'],
        ], 600);

        $this->post('/register', $this->payload())
            ->assertRedirect(url('/verify-otp'));

        $this->assertSame(0, User::count());
        Mail::assertNothingQueued();
    }

    public function test_retry_after_success_does_not_create_a_duplicate_account(): void
    {
        Mail::fake();

        $this->post('/register', $this->payload())
            ->assertRedirect(url('/verify-otp'));

        // Simulate the timeout scenario: the session the first request wrote is gone,
        // so the retry reaches the middleware as a guest with the identical payload.
        Auth::logout();

        $this->post('/register', $this->payload())
            ->assertRedirect(url('/verify-otp'));

        $this->assertSame(1, User::count());
        Mail::assertQueued(OtpVerificationMail::class, 1);
    }

    public function test_a_different_payload_registers_fresh_after_a_replay(): void
    {
        Mail::fake();

        $this->post('/register', $this->payload());
        Auth::logout();

        $this->post('/register', $this->payload([
            'email' => 'second@example.com',
            'mobile' => '+2342222222222',
            'company_name' => 'Second Ltd',
        ]))->assertRedirect(url('/verify-otp'));

        $this->assertSame(2, User::count());
    }
}