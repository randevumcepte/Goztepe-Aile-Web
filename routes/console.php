<?php

use Illuminate\Foundation\Inspiring;
use App\Services\FixtureAutoSyncService;
use App\Services\MatchReminderService;
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
