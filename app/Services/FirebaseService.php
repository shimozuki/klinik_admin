<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
                storage_path('app/firebase/klink-789d3-firebase-adminsdk-fbsvc-475dc48554.json')
            ), true)
        );

        $token = $credentials->fetchAuthToken();

        return $token['access_token'];
    }

    /**
     * Send FCM notification with error handling
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => array_map('strval', $data), // 👈 Convert semua data ke string
                        'android' => [
                            'priority' => 'high', // 👈 Priority tinggi untuk Android
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'chat_channel', // 👈 Sesuaikan dengan channel di Flutter
                            ]
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1,
                                ]
                            ]
                        ]
                    ]
                ]);

            // 🔥 Check response status
            if ($response->successful()) {
                Log::info('FCM Notification Sent Successfully', [
                    'token' => substr($fcmToken, 0, 20) . '...', // Partial token untuk security
                    'title' => $title,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'response' => $response->json()
                ];
            } else {
                // Handle error response
                $errorBody = $response->json();
                $errorCode = $errorBody['error']['code'] ?? null;
                $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';

                Log::warning('FCM Notification Failed', [
                    'token' => substr($fcmToken, 0, 20) . '...',
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'status' => $response->status()
                ]);

                // 🔥 Handle invalid token (INVALID_ARGUMENT, NOT_FOUND, UNREGISTERED)
                if (
                    in_array($errorCode, [400, 404]) ||
                    str_contains($errorMessage, 'not a valid FCM registration token') ||
                    str_contains($errorMessage, 'Requested entity was not found')
                ) {

                    $this->removeInvalidToken($fcmToken);

                    Log::info('Invalid FCM token removed from database', [
                        'token' => substr($fcmToken, 0, 20) . '...'
                    ]);
                }

                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Log::error('FCM Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple tokens
     */
    public function sendMulticast(array $fcmTokens, string $title, string $body, array $data = [])
    {
        $results = [];

        foreach ($fcmTokens as $token) {
            $results[] = $this->sendNotification($token, $title, $body, $data);
        }

        return $results;
    }

    /**
     * Remove invalid FCM token from database
     */
    protected function removeInvalidToken(string $token): void
    {
        DB::table('users')
            ->where('fcm_token', $token)
            ->update(['fcm_token' => null]);
    }

    /**
     * Update user FCM token
     */
    public function updateToken(int $userId, string $newToken): bool
    {
        try {
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'fcm_token' => $newToken,
                    'updated_at' => now()
                ]);

            Log::info('FCM Token Updated', [
                'user_id' => $userId,
                'token' => substr($newToken, 0, 20) . '...'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update FCM token', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send data-only message (silent notification)
     */
    public function sendDataMessage(string $fcmToken, array $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                    'message' => [
                        'token' => $fcmToken,
                        'data' => array_map('strval', $data),
                        'android' => [
                            'priority' => 'high',
                        ]
                    ]
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM Data Message Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
