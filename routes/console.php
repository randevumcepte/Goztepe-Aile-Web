<?php

use Illuminate\Foundation\Inspiring;
use App\Services\FixtureAutoSyncService;
use App\Models\User;
use App\Services\MatchReminderService;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fikstür eşitleme + tahmin oyunu hatırlatmaları. Cron yoksa da çalışır (uygulama istekleri tetikler);
// cron varsa ( * * * * * php artisan schedule:run ) uygulama açılmasa da zamanında gider.
Schedule::call(function () {
    app(FixtureAutoSyncService::class)->sync();
    app(MatchReminderService::class)->tick();
})->everyTenMinutes()->name('mac-hatirlatmalari')->withoutOverlapping();

// Push testi: php artisan push:test uye@ornek.com
Artisan::command('push:test {email}', function (string $email) {
    $user = User::where('email', $email)->first();
    if (! $user) {
        return $this->error('Kullanıcı bulunamadı.');
    }
    $this->info('Kayıtlı cihaz: '.$user->deviceTokens()->count());
    $this->info('Servis hesabı: '.(is_file(config('services.fcm.credentials')) ? 'VAR' : 'YOK -> '.config('services.fcm.credentials')));
    app(NotificationService::class)->send($user, 'Göztepe Tribünleri', 'Bildirimler çalışıyor! 💛❤️', null, 'sistem', ['push']);
    $this->info('Gönderildi; telefona gelmediyse storage/logs/laravel.log içinde [Push] satırlarına bak.');
})->purpose('Bir kullanıcıya test push bildirimi gönderir');
