<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    /**
     * Send the given notification.
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

        // TODO: For FCM HTTP v1 API, you need to generate an OAuth2 token using Google Application Default Credentials.
        // For demonstration, we assume a method exists or use a placeholder token.
        // Using a Service Account JSON file (firebase_credentials.json) is recommended.
        
        $projectId = config('services.firebase.project_id', 'your-project-id');
        $accessToken = $this->getAccessToken(); // Placeholder for OAuth2 token generator

        if (!$accessToken) {
            Log::warning('FCM Access Token not configured. Cannot send push notification.');
            return;
        }

        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $message['title'] ?? '',
                        'body' => $message['body'] ?? '',
                    ],
                    'data' => $message['data'] ?? [],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('FCM Notification Failed: ' . $response->body());
        }
    }

    /**
     * Placeholder method to get OAuth2 Access Token for Firebase HTTP v1 API.
     */
    private function getAccessToken()
    {
        // This requires the google/auth package.
        // Example:
        // $credentialsFilePath = storage_path('app/firebase_credentials.json');
        // $client = new \Google_Client();
        // $client->setAuthConfig($credentialsFilePath);
        // $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        // $client->fetchAccessTokenWithAssertion();
        // return $client->getAccessToken()['access_token'];
        
        return env('FCM_SERVER_KEY', ''); // Temporary fallback
    }
}
