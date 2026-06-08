<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('bidder');
    }

    public function test_user_is_registered_as_unverified(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        // Check if OTP was cached
        $cachedOtp = Cache::get('email_verify_john@example.com');
        $this->assertNotNull($cachedOtp);
    }

    public function test_user_can_verify_email_with_correct_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        Sanctum::actingAs($user);

        // Cache the OTP code
        Cache::put('email_verify_john@example.com', '123456', now()->addMinutes(15));

        $response = $this->postJson('/api/auth/email/verify', [
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'Email verified successfully.',
                'data' => [
                    'user' => [
                        'email' => 'john@example.com',
                        'email_verified' => true,
                    ]
                ]
            ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull(Cache::get('email_verify_john@example.com')); // cache cleared
    }

    public function test_user_cannot_verify_email_with_incorrect_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        Sanctum::actingAs($user);

        Cache::put('email_verify_john@example.com', '123456', now()->addMinutes(15));

        $response = $this->postJson('/api/auth/email/verify', [
            'otp' => '654321',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => true,
                'message' => 'Invalid or expired OTP code.',
            ]);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_can_resend_verification_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        Sanctum::actingAs($user);

        // Clear existing cache just in case
        Cache::forget('email_verify_john@example.com');

        $response = $this->postJson('/api/auth/email/resend');

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'Verification OTP sent to your email.',
            ]);

        $cachedOtp = Cache::get('email_verify_john@example.com');
        $this->assertNotNull($cachedOtp);
    }

    public function test_otp_registration_automatically_verifies_email(): void
    {
        // Cache the login OTP first
        Cache::put('otp_john@example.com', '123456', now()->addMinutes(5));

        $response = $this->postJson('/api/otp/verify', [
            'email' => 'john@example.com',
            'code' => '123456',
        ]);

        $response->assertStatus(200);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        // Automatically marked as verified!
        $this->assertNotNull($user->email_verified_at);
    }
}
