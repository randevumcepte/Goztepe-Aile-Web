<?php

namespace App\Services\Notifications;

use App\Models\Member;
use App\Models\User;
use App\Services\Notifications\Channels\InAppChannel;
use App\Services\Notifications\Channels\PushChannel;
use Illuminate\Support\Collection;

/**
 * Bildirim motorunun beyni.
 *
 * Görevi:
 *  - Üyenin kanal tercihini uygula (notification_preferences)
 *  - Ticari ileti ise rıza (commercial_consent / İYS) kontrolü
 *  - Uygun kanallara dağıt (in-app, push, ...)
 *  - Segment'e toplu gönderim
 */
class NotificationService
{
    /** @var array<string, NotificationChannel> */
    private array $channels;

    public function __construct()
    {
        $this->channels = [
            'in_app' => new InAppChannel(),
            'push' => new PushChannel(),
            // 'sms' => new SmsChannel(), 'email' => ..., 'whatsapp' => ...
        ];
    }

    /**
     * Tek kullanıcıya bildirim gönderir.
     *
     * @param  array<int, string>  $channels
     */
    public function send(
        User $user,
        string $title,
        ?string $body = null,
        ?string $templateKey = null,
        string $type = 'islemsel',
        array $channels = ['in_app', 'push'],
        array $data = [],
    ): void {
        // Ticari ileti ise rıza zorunlu (İYS / KVKK)
        if ($type === 'ticari' && ! $this->hasCommercialConsent($user)) {
            return;
        }

        $payload = compact('title', 'body', 'templateKey', 'type', 'data');
        $payload['template_key'] = $templateKey;

        foreach ($channels as $channelKey) {
            if (! isset($this->channels[$channelKey])) {
                continue;
            }
            if (! $this->isChannelEnabled($user, $type, $channelKey)) {
                continue;
            }
            $this->channels[$channelKey]->send($user, $payload);
        }
    }

    /**
     * Bir kullanıcı koleksiyonuna (segment) toplu gönderim.
     * Büyük kitlelerde queue ile parça parça çağrılmalıdır.
     *
     * @param  Collection<int, User>  $users
     */
    public function sendToUsers(
        Collection $users,
        string $title,
        ?string $body = null,
        string $type = 'islemsel',
        array $channels = ['in_app', 'push'],
        ?string $templateKey = null,
    ): int {
        $sent = 0;
        foreach ($users as $user) {
            $this->send($user, $title, $body, $templateKey, $type, $channels);
            $sent++;
        }

        return $sent;
    }

    /**
     * Segment kurallarına göre hedef kitleyi çözer ve gönderir.
     * segment örn: ['kategori' => 'asil'] | ['borclu' => true] | ['all' => true]
     */
    public function sendToSegment(array $segment, string $title, ?string $body, string $type, array $channels, ?string $templateKey = null): int
    {
        $query = User::query()->with('member');

        if (! empty($segment['kategori'])) {
            $query->whereHas('member', fn ($q) => $q->where('category', $segment['kategori']));
        }
        if (! empty($segment['sadece_uyeler'])) {
            $query->whereHas('member');
        }
        if ($type === 'ticari') {
            $query->whereHas('member', fn ($q) => $q->where('commercial_consent', true));
        }

        return $this->sendToUsers($query->get(), $title, $body, $type, $channels, $templateKey);
    }

    private function hasCommercialConsent(User $user): bool
    {
        return (bool) ($user->member?->commercial_consent);
    }

    private function isChannelEnabled(User $user, string $category, string $channel): bool
    {
        $pref = $user->notificationPreferences()
            ->where('category', $category)
            ->where('channel', $channel)
            ->first();

        // Tercih yoksa varsayılan açık (işlemsel için). Üye kapatırsa kapanır.
        return $pref?->enabled ?? true;
    }
}
