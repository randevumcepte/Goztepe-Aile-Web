<?php

namespace App\Services\Notifications;

use App\Models\User;

interface NotificationChannel
{
    public function key(): string; // in_app | push | sms | email | whatsapp

    /** Bildirimi bu kanaldan gönderir. */
    public function send(User $user, array $payload): void;
}
