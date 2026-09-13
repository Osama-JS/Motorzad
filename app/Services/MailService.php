<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Send an OTP to the given email address via real SMTP.
     *
     * @param string $email
     * @param string $otp
     * @param string $purpose ('verification', 'password_reset', 'login')
     * @param int $expiresInMinutes
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendOtp(string $email, string $otp, string $purpose = 'verification', int $expiresInMinutes = 5): array
    {
        try {
            MailConfigService::applySettings();

            Mail::to($email)->send(new OtpMail($otp, $purpose, $expiresInMinutes));

            Log::info("OTP email sent successfully via SMTP to {$email} (Purpose: {$purpose})");

            return [
                'success' => true,
                'message' => __('OTP sent successfully to your email.'),
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to send OTP email to {$email}: " . $e->getMessage(), [
                'exception' => $e,
                'email'     => $email,
                'purpose'   => $purpose,
            ]);

            return [
                'success' => false,
                'message' => __('Failed to send email. Please check SMTP settings or try again later.') . ' (' . $e->getMessage() . ')',
            ];
        }
    }

    /**
     * Send email verification OTP to the given email address.
     */
    public function sendVerificationOtp(string $email, string $otp): array
    {
        return $this->sendOtp($email, $otp, 'verification', 5);
    }

    /**
     * Send password reset OTP to the given email address.
     */
    public function sendPasswordResetOtp(string $email, string $otp): array
    {
        return $this->sendOtp($email, $otp, 'password_reset', 15);
    }

    /**
     * Send a test email to verify SMTP configuration.
     *
     * @param string $recipientEmail
     * @param array|null $customOverrides
     * @return array
     */
    public function sendTestEmail(string $recipientEmail, ?array $customOverrides = null): array
    {
        try {
            MailConfigService::applySettings($customOverrides);

            Mail::to($recipientEmail)->send(new TestMail());

            Log::info("Test SMTP email sent successfully to {$recipientEmail}");

            return [
                'success' => true,
                'message' => __('Test email sent successfully! Connection to mail server is active.'),
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to send test email to {$recipientEmail}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
