<?php

namespace App\Services\Notifications\Channels;

use App\Models\AppNotification;
use App\Models\User;
use App\Services\Notifications\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Push bildirim kanalı.
 *  - Mobil: FCM (Firebase)  -> config('services.fcm.server_key')
 *  - Web:   OneSignal        -> config('services.onesignal.*')
 *
 * Kullanıcının kayıtlı device_tokens'larına gönderir. Anahtarlar
 * tanımlı değilse sessizce loglar (geliştirme ortamı çökmez).
 */
class PushChannel implements NotificationChannel
{
    public function key(): string
    {
        return 'push';
    }

    public function send(User $user, array $payload): void
    {
        $tokens = $user->deviceTokens()->pluck('token', 'platform');

        if ($tokens->isEmpty()) {
            return;
        }

        $fcmKey = config('services.fcm.server_key');

        // DB'ye de log düş (gönderim kaydı)
        AppNotification::create([
            'user_id' => $user->id,
            'template_key' => $payload['template_key'] ?? null,
            'channel' => 'push',
            'type' => $payload['type'] ?? 'islemsel',
            'title' => $payload['title'],
            'body' => $payload['body'] ?? null,
            'data' => $payload['data'] ?? null,
            'status' => $fcmKey ? 'sent' : 'queued',
            'sent_at' => $fcmKey ? now() : null,
        ]);

        if (! $fcmKey) {
            Log::info('[Push] FCM anahtarı yok, bildirim kuyruğa alındı.', ['user' => $user->id]);

            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'key='.$fcmKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'registration_ids' => array_values($tokens->all()),
                'notification' => [
                    'title' => $payload['title'],
                    'body' => $payload['body'] ?? '',
                ],
                'data' => $payload['data'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::warning('[Push] Gönderim hatası: '.$e->getMessage());
        }
    }
}
