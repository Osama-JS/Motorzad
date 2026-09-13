<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $purpose;
    public int $expiresInMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $purpose = 'verification', int $expiresInMinutes = 5)
    {
        $this->otp = $otp;
        $this->purpose = $purpose;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->purpose) {
            'password_reset' => 'رمز استعادة كلمة المرور - Password Reset Code',
            'login'          => 'رمز تسجيل الدخول - Login Verification Code',
            default          => 'رمز التحقق من الحساب - Account Verification Code',
        };

        return new Envelope(
            subject: $subject . ' | ' . config('app.name', 'Motorzad')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
                'purpose' => $this->purpose,
                'expiresInMinutes' => $this->expiresInMinutes,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
