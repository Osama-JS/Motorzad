<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailConfigService
{
    /**
     * Dynamically apply SMTP configuration from database settings, overrides, or environment.
     * Enforces real SMTP sending across all environments.
     */
    public static function applySettings(?array $customOverrides = null): void
    {
        try {
            $host = $customOverrides['mail_host'] ?? Setting::get('mail_host', config('mail.mailers.smtp.host', env('MAIL_HOST', '127.0.0.1')));
            $port = (int) ($customOverrides['mail_port'] ?? Setting::get('mail_port', config('mail.mailers.smtp.port', env('MAIL_PORT', 587))));
            $username = $customOverrides['mail_username'] ?? Setting::get('mail_username', config('mail.mailers.smtp.username', env('MAIL_USERNAME')));
            $password = $customOverrides['mail_password'] ?? Setting::get('mail_password', config('mail.mailers.smtp.password', env('MAIL_PASSWORD')));
            $encryption = $customOverrides['mail_encryption'] ?? Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls')));

            // Determine sender address - never use example.com as mail servers reject nullMX
            $fromAddress = $customOverrides['mail_from_address'] ?? Setting::get('mail_from_address');
            if (empty($fromAddress) || str_contains(strtolower($fromAddress), 'example.com')) {
                if (!empty($username) && filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $fromAddress = $username;
                } else {
                    $envFrom = env('MAIL_FROM_ADDRESS');
                    $fromAddress = (!empty($envFrom) && !str_contains(strtolower($envFrom), 'example.com')) ? $envFrom : 'noreply@motorzad.com';
                }
            }

            $fromName = $customOverrides['mail_from_name'] ?? Setting::get('mail_from_name', env('MAIL_FROM_NAME', config('app.name', 'Motorzad')));
            if (empty($fromName)) {
                $fromName = config('app.name', 'Motorzad');
            }

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
