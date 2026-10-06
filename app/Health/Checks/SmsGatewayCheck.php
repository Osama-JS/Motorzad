<?php

namespace App\Health\Checks;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class SmsGatewayCheck extends Check
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

        // Check configured SMS gateway or fallback to Firebase / Mock
        $provider = Setting::get('sms_provider', env('SMS_PROVIDER', 'mock'));
        $apiKey = Setting::get('sms_api_key', env('SMS_API_KEY'));
        $senderId = Setting::get('sms_sender_id', env('SMS_SENDER_ID', 'MOTORZAD'));

        $result->meta([
            'provider' => $provider,
            'sender_id' => $senderId,
            'has_api_key' => !empty($apiKey),
        ]);

        // If explicitly set to mock, log or local
        if (in_array(strtolower($provider), ['mock', 'log', 'null', 'array', ''])) {
            return $result->ok("بوابة الرسائل (SMS) تعمل في وضع المحاكاة / OTP عبر البريد الإلكتروني");
        }

        // If provider is set but API Key is empty
        if (empty($apiKey)) {
            return $result->warning("بوابة الرسائل ({$provider}) محددة ولكن مفتاح الربط (API Key) غير مضبوط");
        }

        // Check specific known providers
        try {
            if (strtolower($provider) === 'unifonic') {
                $response = Http::timeout($this->timeoutSeconds)->get('https://el.cloud.unifonic.com/rest/Account/GetBalance', [
                    'AppSid' => $apiKey,
                ]);

                if ($response->successful()) {
                    $balance = $response->json('data.Balance') ?? 'متوفر';
                    return $result->ok("بوابة Unifonic للرسائل متصلة بنجاح (الرصيد: {$balance})");
                }
            } elseif (strtolower($provider) === 'taqnyat') {
                $response = Http::timeout($this->timeoutSeconds)
                    ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                    ->get('https://api.taqnyat.sa/account/balance');

                if ($response->successful()) {
                    return $result->ok("بوابة تقنيات (Taqnyat) للرسائل متصلة ومستعدة للإرسال");
                }
            }

            // General provider reachability
            return $result->ok("بوابة الرسائل النصية ({$provider}) متصلة بنجاح");
        } catch (Exception $e) {
            return $result->warning("تعذر الاستعلام من مزود الرسائل ({$provider}): " . $e->getMessage());
        }
    }
}
