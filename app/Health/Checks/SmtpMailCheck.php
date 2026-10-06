<?php

namespace App\Health\Checks;

use App\Models\Setting;
use App\Services\MailConfigService;
use Exception;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class SmtpMailCheck extends Check
{
    protected float $timeoutSeconds = 2.5;

    public function timeout(float $seconds): static
    {
        $this->timeoutSeconds = $seconds;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        // Ensure dynamic settings are applied
        MailConfigService::applySettings();

        $mailer = config('mail.default', 'smtp');
        $host = Setting::get('mail_host', config('mail.mailers.smtp.host', env('MAIL_HOST')));
        $port = (int) Setting::get('mail_port', config('mail.mailers.smtp.port', env('MAIL_PORT', 587)));
        $from = config('mail.from.address');

        $result->meta([
            'mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'from_address' => $from,
        ]);

        // If mailer is set to 'log' or 'array'
        if ($mailer === 'log' || $mailer === 'array') {
            return $result->ok("خدمة البريد تعمل في وضع السجل المحلي (Log Mode)");
        }

        if (empty($host)) {
            return $result->warning("إعدادات خادم البريد SMTP غير مكتملة في الإعدادات");
        }

        // Test TCP connection to SMTP host & port
        try {
            $errno = 0;
            $errstr = '';
            $fp = @fsockopen($host, $port, $errno, $errstr, $this->timeoutSeconds);

            if (!$fp) {
                if (app()->environment('local')) {
                    return $result->warning("تعذر الاتصال بخادم البريد ({$host}:{$port}): {$errstr}");
                }
                return $result->failed("خادم البريد SMTP غير متاح حالياً ({$host}:{$port})!");
            }

            // Read the initial SMTP banner (usually '220 ...')
            stream_set_timeout($fp, 2);
            $banner = @fgets($fp, 512);
            fclose($fp);

            if ($banner && str_starts_with(trim($banner), '220')) {
                return $result->ok("خادم البريد SMTP متصل وجاهز لإرسال الرسائل ({$host}:{$port})");
            }

            return $result->ok("خادم البريد SMTP متصل ويستجيب بنجاح ({$host}:{$port})");
        } catch (Exception $e) {
            return $result->warning("تعذر اختبار اتصال خادم البريد: " . $e->getMessage());
        }
    }
}
