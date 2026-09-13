<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Mail\TestMail;
use App\Models\Setting;
use App\Models\User;
use App\Services\MailConfigService;
use App\Services\MailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailAndOtpSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::findOrCreate('admin');
        \Spatie\Permission\Models\Role::findOrCreate('bidder');
    }

    /**
     * Test admin can update SMTP settings in database.
     */
    public function test_admin_can_update_smtp_settings_in_database()
    {
        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('admin');

        $payload = [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.mailgun.org',
            'mail_port' => 587,
            'mail_username' => 'postmaster@mg.motorzad.com',
            'mail_password' => 'secret_smtp_pass_123',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@motorzad.com',
            'mail_from_name' => 'Motorzad Platform',
        ];

        $response = $this->actingAs($admin)->post('/admin/settings', $payload);

        $response->assertStatus(302);
        $this->assertEquals('smtp.mailgun.org', Setting::get('mail_host'));
        $this->assertEquals(587, (int) Setting::get('mail_port'));
        $this->assertEquals('postmaster@mg.motorzad.com', Setting::get('mail_username'));
        $this->assertEquals('secret_smtp_pass_123', Setting::get('mail_password'));
        $this->assertEquals('tls', Setting::get('mail_encryption'));
        $this->assertEquals('noreply@motorzad.com', Setting::get('mail_from_address'));
    }

    /**
     * Test MailConfigService applies settings dynamically to Laravel mail configuration.
     */
    public function test_mail_config_service_applies_settings_dynamically()
    {
        Setting::set('mail_host', 'smtp.sendgrid.net');
        Setting::set('mail_port', '465');
        Setting::set('mail_username', 'apikey');
        Setting::set('mail_password', 'SG.test_key');
        Setting::set('mail_encryption', 'ssl');
        Setting::set('mail_from_address', 'support@motorzad.com');
        Setting::set('mail_from_name', 'Motorzad Support');

        MailConfigService::applySettings();

        $this->assertEquals('smtp', Config::get('mail.default'));
        $this->assertEquals('smtp.sendgrid.net', Config::get('mail.mailers.smtp.host'));
        $this->assertEquals(465, Config::get('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', Config::get('mail.mailers.smtp.encryption'));
        $this->assertEquals('apikey', Config::get('mail.mailers.smtp.username'));
        $this->assertEquals('SG.test_key', Config::get('mail.mailers.smtp.password'));
        $this->assertEquals('support@motorzad.com', Config::get('mail.from.address'));
        $this->assertEquals('Motorzad Support', Config::get('mail.from.name'));
    }

    /**
     * Test OTP email is actually sent via Mailable when sendOtp is called.
     */
    public function test_otp_mail_is_sent_when_send_otp_requested()
    {
        Mail::fake();
        Cache::flush();

        $email = 'bidder_test@motorzad.com';

        $response = $this->postJson('/api/otp/send', [
            'email' => $email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
        ]);

        // Assert OtpMail was actually sent to the specified email address
        Mail::assertSent(OtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email)
                && strlen($mail->otp) === 6
                && is_numeric($mail->otp);
        });

        // Verify OTP is stored in Cache for 5 minutes
        $this->assertTrue(Cache::has('otp_' . $email));
    }

    /**
     * Test OTP rate limiting prevents spamming within 60 seconds.
     */
    public function test_otp_rate_limiting_prevents_spam()
    {
        Mail::fake();
        Cache::flush();

        $email = 'spam_test@motorzad.com';

        // First request: succeeds
        $firstResponse = $this->postJson('/api/otp/send', ['email' => $email]);
        $firstResponse->assertStatus(200);

        // Immediate second request: rejected with 429 Too Many Requests
        $secondResponse = $this->postJson('/api/otp/send', ['email' => $email]);
        $secondResponse->assertStatus(429);
        $secondResponse->assertJson(['error' => true]);
    }

    /**
     * Test OTP verification flow and one-time consumption.
     */
    public function test_otp_verification_flow_and_one_time_consumption()
    {
        Cache::flush();
        $email = 'verify_flow@motorzad.com';
        $code = '849201';

        Cache::put('otp_' . $email, $code, now()->addMinutes(5));

        // Wrong code: fails
        $wrongResponse = $this->postJson('/api/otp/verify', [
            'email' => $email,
            'code'  => '000000',
        ]);
        $wrongResponse->assertStatus(400);

        // Correct code: succeeds
        $correctResponse = $this->postJson('/api/otp/verify', [
            'email' => $email,
            'code'  => $code,
        ]);
        $correctResponse->assertStatus(200);

        // Code should be consumed and removed from cache
        $this->assertFalse(Cache::has('otp_' . $email));

        // Attempting to reuse same code immediately fails
        $reuseResponse = $this->postJson('/api/otp/verify', [
            'email' => $email,
            'code'  => $code,
        ]);
        $reuseResponse->assertStatus(400);
    }

    /**
     * Test admin test-email endpoint triggers TestMail.
     */
    public function test_admin_test_email_endpoint()
    {
        Mail::fake();

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->postJson('/admin/settings/test-email', [
            'test_email' => 'admin_receiver@motorzad.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        Mail::assertSent(TestMail::class, function ($mail) {
            return $mail->hasTo('admin_receiver@motorzad.com');
        });
    }
}
