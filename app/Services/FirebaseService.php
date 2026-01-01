<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    private string $projectId;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
    }

    private function getAccessToken(): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials(
            $scopes,
            json_decode(file_get_contents(
                storage_path('app/firebase/service-account.json')
            ), true)
        );

        $token = $credentials->fetchAuthToken();

        return $token['access_token'];
    }

    public function sendNotification(string $fcmToken, string $title, string $body, array $data = [])
    {
        $accessToken = $this->getAccessToken();

        Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $data,
                ]
            ]);
    }
}
