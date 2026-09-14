<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    /**
     * Send the given push notification via Firebase Cloud Messaging (HTTP v1).
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $fcmToken = $notifiable->routeNotificationFor('fcm', $notification);

        if (!$fcmToken) {
            return;
        }

        $message = $notification->toFcm($notifiable);
        $credentialsPath = config('services.firebase.credentials', storage_path('app/firebase_credentials.json'));

        if (!file_exists($credentialsPath)) {
            Log::info("FCM push skipped: Service account file not found at [{$credentialsPath}]. Add firebase_credentials.json to enable mobile push notifications.");
            return;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        if (!$credentials || empty($credentials['private_key']) || empty($credentials['client_email'])) {
            Log::warning("FCM push skipped: Invalid service account file format at [{$credentialsPath}].");
            return;
        }

        $projectId = config('services.firebase.project_id') ?: ($credentials['project_id'] ?? null);
        if (!$projectId) {
            Log::warning('FCM push skipped: Firebase project_id not configured in services.firebase or credentials file.');
            return;
        }

        $accessToken = $this->getOAuth2AccessToken($credentials);
        if (!$accessToken) {
            Log::warning('FCM push skipped: Unable to acquire OAuth2 access token.');
            return;
        }

        // Format data payload strings only (Firebase v1 requires string values for data map)
        $dataPayload = [];
        if (!empty($message['data']) && is_array($message['data'])) {
            foreach ($message['data'] as $k => $v) {
                $dataPayload[(string)$k] = is_scalar($v) ? (string)$v : json_encode($v);
            }
        }

        try {
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $message['title'] ?? config('app.name'),
                            'body'  => $message['body'] ?? '',
                        ],
                        'data' => (object)$dataPayload,
                        'android' => [
                            'priority' => 'HIGH',
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'motorzad_alerts',
                            ]
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1
                                ]
                            ]
                        ]
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('FCM HTTP v1 Send Failed: [' . $response->status() . '] ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('FCM Send Exception: ' . $e->getMessage());
        }
    }

    /**
     * Acquire or return cached OAuth2 Bearer Access Token using Service Account JWT.
     */
    private function getOAuth2AccessToken(array $credentials): ?string
    {
        return Cache::remember('fcm_oauth2_access_token', 3300, function () use ($credentials) {
            try {
                $now = time();
                $header = [
                    'alg' => 'RS256',
                    'typ' => 'JWT',
                ];

                $claimSet = [
                    'iss'   => $credentials['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud'   => 'https://oauth2.googleapis.com/token',
                    'exp'   => $now + 3600,
                    'iat'   => $now,
                ];

                $encodedHeader = $this->base64UrlEncode(json_encode($header));
                $encodedClaims = $this->base64UrlEncode(json_encode($claimSet));
                $signatureInput = $encodedHeader . '.' . $encodedClaims;

                $privateKey = openssl_pkey_get_private($credentials['private_key']);
                if (!$privateKey) {
                    Log::error('FCM OAuth2 Error: Failed to parse private key from service account.');
                    return null;
                }

                $signature = '';
                $signed = openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
                if (!$signed) {
                    Log::error('FCM OAuth2 Error: Failed to sign JWT assertion.');
                    return null;
                }

                $jwtAssertion = $signatureInput . '.' . $this->base64UrlEncode($signature);

                $response = Http::asForm()
                    ->timeout(10)
                    ->post('https://oauth2.googleapis.com/token', [
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion'  => $jwtAssertion,
                    ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('FCM OAuth2 Token Exchange Failed: ' . $response->body());
                return null;
            } catch (\Throwable $e) {
                Log::error('FCM OAuth2 Token Generation Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Helper for URL-safe base64 encoding without padding.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
