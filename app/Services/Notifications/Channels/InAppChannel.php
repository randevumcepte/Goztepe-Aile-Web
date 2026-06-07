<?php

namespace App\Services\Notifications\Channels;

use App\Models\AppNotification;
use App\Models\User;
use App\Services\Notifications\NotificationChannel;

/** Uygulama içi bildirim merkezi (zil ikonu) — DB'ye yazar. */
class InAppChannel implements NotificationChannel
{
    public function key(): string
    {
        return 'in_app';
    }

    public function send(User $user, array $payload): void
    {
        AppNotification::create([
            'user_id' => $user->id,
            'template_key' => $payload['template_key'] ?? null,
            'channel' => 'in_app',
            'type' => $payload['type'] ?? 'islemsel',
            'title' => $payload['title'],
            'body' => $payload['body'] ?? null,
            'data' => $payload['data'] ?? null,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
