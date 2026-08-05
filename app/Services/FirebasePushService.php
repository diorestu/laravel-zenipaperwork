<?php

namespace App\Services;

use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebasePushService
{
    /**
     * Send push notification to all active devices of a company.
     */
    public function sendToCompany(int $companyId, string $title, string $body, array $data = []): int
    {
        $tokens = UserDeviceToken::forCompany($companyId)->pluck('token')->unique()->all();

        if (empty($tokens)) {
            return 0;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to an array of tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): int
    {
        $serverKey = config('services.firebase.server_key');
        $projectId = config('services.firebase.project_id');

        if (! $serverKey && ! $projectId) {
            Log::info('Firebase Push Notification dipanggil tetapi FIREBASE_SERVER_KEY / FIREBASE_PROJECT_ID belum diatur di .env', [
                'title' => $title,
                'tokens_count' => count($tokens),
            ]);

            return 0;
        }

        $sentCount = 0;

        if ($serverKey) {
            foreach (array_chunk($tokens, 500) as $chunk) {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'key='.$serverKey,
                    'Content-Type' => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $chunk,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                    'data' => array_merge($data, [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'title' => $title,
                        'body' => $body,
                    ]),
                    'priority' => 'high',
                ]);

                if ($response->successful()) {
                    $sentCount += count($chunk);
                } else {
                    Log::warning('Gagal mengirim Firebase Push Notification via Legacy API', ['response' => $response->body()]);
                }
            }
        }

        return $sentCount;
    }
}
