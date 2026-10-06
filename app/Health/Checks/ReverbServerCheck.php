<?php

namespace App\Health\Checks;

use Exception;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class ReverbServerCheck extends Check
{
    protected ?string $host = null;
    protected ?int $port = null;
    protected float $timeout = 1.5;

    public function host(string $host): static
    {
        $this->host = $host;
        return $this;
    }

    public function port(int $port): static
    {
        $this->port = $port;
        return $this;
    }

    public function timeout(float $seconds): static
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        $broadcaster = config('broadcasting.default');
        $wsEnabled = env('ENABLE_WEBSOCKETS', true);

        $host = $this->host 
            ?: (config('broadcasting.connections.reverb.options.host') ?: env('REVERB_HOST', '127.0.0.1'));
        
        // If host is 'localhost' on windows, resolve to 127.0.0.1 for faster socket ping
        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }

        $port = $this->port 
            ?: (int) (config('broadcasting.connections.reverb.options.port') ?: env('REVERB_PORT', 8080));

        $result->meta([
            'broadcaster' => $broadcaster,
            'host' => $host,
            'port' => $port,
            'websockets_enabled' => $wsEnabled,
        ]);

        if (!$wsEnabled || $broadcaster === 'null' || $broadcaster === 'log') {
            return $result->ok('خدمة البث المباشر (WebSockets) معطلة أو مضبوطة على وضع الاختبار');
        }

        // Check socket connection
        try {
            $errno = 0;
            $errstr = '';
            $fp = @fsockopen($host, $port, $errno, $errstr, $this->timeout);

            if ($fp) {
                fclose($fp);
                return $result->ok("خادم البث اللحظي Reverb يعمل بنشاط على المنفذ ({$port})");
            }

            // Could not open socket
            if (app()->environment('local')) {
                return $result->warning("خادم Reverb غير نشط على المنفذ ({$port}). للمزايدات الحية شغّل: php artisan reverb:start");
            }

            return $result->failed("خادم بث المزادات Reverb متوقف! العدادات التنازلية والمزايدة الحية معطلة للمستخدمين حالياً.");
        } catch (Exception $e) {
            return $result->warning("تعذر اختبار اتصال Reverb: " . $e->getMessage());
        }
    }
}
