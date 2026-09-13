<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_can_verify_email_via_otp_on_web(): void
    {
        \Spatie\Permission\Models\Role::findOrCreate('bidder');
        $user = User::factory()->unverified()->create([
            'status' => 'pending',
        ]);
        $user->assignRole('bidder');

        \Illuminate\Support\Facades\Cache::put('email_verify_' . $user->email, '654321', now()->addMinutes(15));

        $response = $this->actingAs($user)->post('/verify-email-otp', [
            'otp' => '654321',
        ]);

        $response->assertRedirect(route('bidder.dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertEquals('active', $user->fresh()->status);
        $this->assertNull(\Illuminate\Support\Facades\Cache::get('email_verify_' . $user->email));
    }

    public function test_user_cannot_verify_with_invalid_otp_on_web(): void
    {
        $user = User::factory()->unverified()->create();
        \Illuminate\Support\Facades\Cache::put('email_verify_' . $user->email, '654321', now()->addMinutes(15));

        $response = $this->actingAs($user)->post('/verify-email-otp', [
            'otp' => '000000',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_can_resend_otp_on_web(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertSessionHas('status', 'verification-otp-sent');
        $this->assertNotNull(\Illuminate\Support\Facades\Cache::get('email_verify_' . $user->email));
    }
}
