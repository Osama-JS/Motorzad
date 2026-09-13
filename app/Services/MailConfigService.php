<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailConfigService
{
    /**
     * Dynamically apply SMTP configuration from database settings or environment.
     * Enforces real SMTP sending across all environments.
     */
    public static function applySettings(): void
    {
        try {
            $host = Setting::get('mail_host', config('mail.mailers.smtp.host', env('MAIL_HOST', '127.0.0.1')));
            $port = (int) Setting::get('mail_port', config('mail.mailers.smtp.port', env('MAIL_PORT', 587)));
            $username = Setting::get('mail_username', config('mail.mailers.smtp.username', env('MAIL_USERNAME')));
            $password = Setting::get('mail_password', config('mail.mailers.smtp.password', env('MAIL_PASSWORD')));
            $encryption = Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls')));
            $fromAddress = Setting::get('mail_from_address', config('mail.from.address', env('MAIL_FROM_ADDRESS', 'noreply@motorzad.com')));
            $fromName = Setting::get('mail_from_name', config('mail.from.name', env('MAIL_FROM_NAME', config('app.name', 'Motorzad'))));

            // Force SMTP driver for real email delivery in all environments
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', $port);
            Config::set('mail.mailers.smtp.encryption', ($encryption === 'none' || empty($encryption)) ? null : strtolower($encryption));
            Config::set('mail.mailers.smtp.username', $username);
            Config::set('mail.mailers.smtp.password', $password);
            Config::set('mail.from.address', $fromAddress);
            Config::set('mail.from.name', $fromName);

            // Purge cached mail instance so new credentials take effect immediately
            Mail::purge('smtp');
        } catch (\Throwable $e) {
            \Log::warning('Failed to apply dynamic mail configuration: ' . $e->getMessage());
        }
    }

    /**
     * Check if SMTP credentials have been provided.
     */
    public static function isConfigured(): bool
    {
        $host = Setting::get('mail_host', env('MAIL_HOST'));
        $username = Setting::get('mail_username', env('MAIL_USERNAME'));
        return !empty($host) && !empty($username);
    }
}
