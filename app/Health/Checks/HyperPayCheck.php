<?php

namespace App\Health\Checks;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class HyperPayCheck extends Check
{
    protected int $timeoutSeconds = 3;

    public function timeout(int $seconds): static
    {
        $this->timeoutSeconds = $seconds;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        // 1. Check if HyperPay is enabled in settings
        $isEnabled = (bool) Setting::get('hyperpay_enabled', '0');
        $mode = Setting::get('hyperpay_mode', 'test');
        $baseUrl = $mode === 'live' 
            ? Setting::get('hyperpay_live_base_url', 'https://oppwa.com')
            : Setting::get('hyperpay_test_base_url', 'https://eu-test.oppwa.com');

        $result->meta([
            'enabled' => $isEnabled,
            'mode' => $mode,
            'base_url' => $baseUrl,
        ]);

        if (!$isEnabled) {
            return $result->ok('بوابة الدفع HyperPay معطلة حالياً في إعدادات المنصة');
        }

        // 2. Check if access token is present
        $accessToken = Setting::get('hyperpay_access_token');
        if (empty($accessToken)) {
            return $result->warning("بوابة HyperPay مفعلة ولكن رمز الوصول (Access Token) مفقود في الإعدادات");
        }

        // 3. Ping HyperPay servers to verify network reachability & SSL handshake
        try {
            $response = Http::timeout($this->timeoutSeconds)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->get(rtrim($baseUrl, '/') . '/v1/version');

            // Even if endpoint returns 404 or 401 or 200, the server is reached and responding
            if ($response->status() >= 500) {
                return $result->failed("خوادم بوابة HyperPay تعاني من خلل داخلي (رمز الاستجابة: {$response->status()})");
            }

            return $result->ok("بوابة الدفع HyperPay متصلة وتستجيب بنجاح ({$mode})");
        } catch (Exception $e) {
            return $result->failed("تعذر الوصول لخوادم بوابة HyperPay ({$baseUrl}): " . $e->getMessage());
        }
    }
}
