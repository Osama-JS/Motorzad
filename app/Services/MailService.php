<?php

namespace App\Services;

class MailService
{
    /**
     * Send an OTP to the given email address.
     */
    public function sendOtp($email, $otp)
    {
        // Implementation for sending OTP via email
    }

    /**
     * Send email verification OTP to the given email address.
     */
    public function sendVerificationOtp($email, $otp)
    {
        // Implementation for sending verification OTP
        // Mail::to($email)->send(new VerificationOtpMail($otp));
    }
}
