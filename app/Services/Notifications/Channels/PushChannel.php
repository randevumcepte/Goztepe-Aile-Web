<?php

namespace App\Services\Notifications\Channels;

use App\Models\AppNotification;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\Notifications\NotificationChannel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Push bildirim kanalı — FCM HTTP v1 (Firebase).
 *
 * Eski "server key" API'si (fcm/send) Google tarafından Haziran 2024'te kapatıldı;
 * v1 bir servis hesabı ister: Firebase Console → Proje ayarları → Hizmet hesapları →
 * "Yeni özel anahtar oluştur" ile inen JSON, sunucuda
 * config('services.fcm.credentials') yoluna (varsayılan storage/app/fcm-service-account.json).
 * Dosya yoksa sessizce loglar (geliştirme ortamı çökmez).
 */
class PushChannel implements NotificationChannel
{
    /** Son gönderimin cihaz bazında sonucu (push:test teşhisi için). */
    public static array $lastReport = [];

    public function key(): string
    {
        return 'push';
    }

    public function send(User $user, array $payload): void
    {
        self::$lastReport = [];
        $tokens = $user->deviceTokens()->pluck('token')->filter()->unique()->values();

        if ($tokens->isEmpty()) {
            return;
        }

        $account = $this->serviceAccount();

        // DB'ye de log düş (gönderim kaydı)
        $log = AppNotification::create([
            'user_id' => $user->id,
            'template_key' => $payload['template_key'] ?? null,
            'channel' => 'push',
            'type' => $payload['type'] ?? 'islemsel',
            'title' => $payload['title'],
            'body' => $payload['body'] ?? null,
            'data' => $payload['data'] ?? null,
            'status' => $account ? 'sent' : 'queued',
            'sent_at' => $account ? now() : null,
        ]);

        if (! $account) {
            Log::info('[Push] FCM servis hesabı yok, bildirim kuyruğa alındı.', ['user' => $user->id]);

            return;
        }

        $accessToken = $this->accessToken($account);
        if (! $accessToken) {
            self::$lastReport[] = 'Google yetki belirteci ALINAMADI (laravel.log [Push] satırına bak)';
            $log->update(['status' => 'failed']);

            return;
        }

        $ok = 0;
        foreach ($tokens as $token) {
            $ok += $this->sendOne($account['project_id'], $accessToken, $token, $payload) ? 1 : 0;
        }
        if ($ok === 0) {
            $log->update(['status' => 'failed']);
        }
    }

    private function sendOne(string $projectId, string $accessToken, string $token, array $payload): bool
    {
        try {
            $data = array_map('strval', array_filter((array) ($payload['data'] ?? []), 'is_scalar'));
            $message = [
                'token' => $token,
                'notification' => [
                    'title' => $payload['title'],
                    'body' => $payload['body'] ?? '',
                ],
                // Uygulamadaki sesli/yüksek öncelikli kanal (goztepe_bildirim) + varsayılan ses
                'android' => [
                    'priority' => 'high',
                    'notification' => ['channel_id' => 'goztepe_bildirim', 'sound' => 'default'],
                ],
                'apns' => ['payload' => ['aps' => ['sound' => 'default']]],
            ];
            // v1'de data değerleri yalnız string olabilir; boşsa hiç gönderme
            if ($data) {
                $message['data'] = $data;
            }

            $res = Http::withToken($accessToken)->timeout(10)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", ['message' => $message]);

            self::$lastReport[] = 'cihaz '.substr($token, 0, 12).'… → HTTP '.$res->status().' '.($res->successful() ? 'KABUL ('.$res->json('name').')' : $res->body());
            if ($res->successful()) {
                return true;
            }

            // Uygulaması silinmiş / geçersiz cihaz: kaydı temizle
            $code = $res->json('error.details.0.errorCode') ?? $res->json('error.status');
            if ($code === 'UNREGISTERED' || $res->status() === 404) {
                DeviceToken::where('token', $token)->delete();
            }
            Log::warning('[Push] FCM reddetti: '.$res->status().' '.$res->body());
        } catch (\Throwable $e) {
            self::$lastReport[] = 'cihaz '.substr($token, 0, 12).'… → HATA '.$e->getMessage();
            Log::warning('[Push] Gönderim hatası: '.$e->getMessage());
        }

        return false;
    }

    private function serviceAccount(): ?array
    {
        $path = config('services.fcm.credentials') ?: storage_path('app/fcm-service-account.json');
        if (! is_file($path)) {
            return null;
        }
        $json = json_decode((string) file_get_contents($path), true);

        return isset($json['client_email'], $json['private_key'], $json['project_id']) ? $json : null;
    }

    /** Servis hesabıyla imzalı JWT → Google OAuth erişim belirteci (≈55 dk önbellek). */
    private function accessToken(array $account): ?string
    {
        $key = 'fcm_v1_access_token:'.md5($account['client_email']);
        if ($cached = Cache::get($key)) {
            return $cached;
        }

        $now = time();
        $b64 = fn (string $s) => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
        $unsigned = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])).'.'.$b64(json_encode([
            'iss' => $account['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));
        if (! openssl_sign($unsigned, $signature, $account['private_key'], 'sha256WithRSAEncryption')) {
            Log::warning('[Push] JWT imzalanamadı (servis hesabı anahtarı bozuk olabilir).');

            return null;
        }

        try {
            $res = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $unsigned.'.'.$b64($signature),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[Push] Google erişim belirteci isteği başarısız: '.$e->getMessage());

            return null;
        }
        if (! $res->successful() || ! $res->json('access_token')) {
            Log::warning('[Push] Google erişim belirteci alınamadı: '.$res->body());

            return null;
        }

        Cache::put($key, $res->json('access_token'), now()->addMinutes(55));

        return $res->json('access_token');
    }
}
