<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\MatchPrediction;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Tahmin oyunu hatırlatmaları ("Etkinlik / maç günü" kategorisi; üye ayarlardan kapatabilir):
 *  - acildi:    maç fikstüre girince herkese "tahmin açık"
 *  - son_cagri: başlamaya 3 saat kala, henüz tahmin yapmayanlara
 *  - sonuc:     maç bitince tahmin yapan herkese kişisel puan + sıra
 * Her biri maç başına bir kez gider (fixtures.reminders_sent). Cron yoksa
 * fikstür/ana sayfa istekleri tetikler; en çok 10 dakikada bir çalışır.
 */
class MatchReminderService
{
    private const CATEGORY = 'etkinlik';

    public function __construct(
        private NotificationService $notifications,
        private PredictionGameService $game,
    ) {}

    public function tick(): void
    {
        if (! PredictionGameService::installed() || ! Cache::add('match_reminders:lock', 1, now()->addMinutes(10))) {
            return;
        }

        try {
            $this->game->settlePending();

            Fixture::query()->where('is_active', true)->whereNull('home_score')
                ->whereBetween('kickoff_at', [now(), now()->addDays(7)])
                ->get()
                ->each(function (Fixture $f) {
                    if ($f->kickoff_at->gt(now()->addHours(3))) {
                        $this->once($f, 'acildi', fn () => $this->opened($f));
                    } else {
                        $this->once($f, 'son_cagri', fn () => $this->lastCall($f));
                    }
                });

            Fixture::query()->where('is_active', true)->whereNotNull('home_score')->whereNotNull('away_score')
                ->where('kickoff_at', '>=', now()->subDays(3))
                ->whereHas('predictions')
                ->whereDoesntHave('predictions', fn ($q) => $q->whereNull('points'))
                ->get()
                ->each(fn (Fixture $f) => $this->once($f, 'sonuc', fn () => $this->results($f)));
        } catch (\Throwable $e) {
            Log::warning('Maç hatırlatma hatası: '.$e->getMessage());
        }
    }

    /** Önce işaretle, sonra gönder: aynı hatırlatma iki kez gitmez. */
    private function once(Fixture $f, string $key, callable $send): void
    {
        $sent = $f->reminders_sent ?? [];
        if (isset($sent[$key])) {
            return;
        }
        $sent[$key] = now()->toIso8601String();
        $f->forceFill(['reminders_sent' => $sent])->saveQuietly();

        $run = function () use ($send, $key, $f) {
            try {
                $send();
            } catch (\Throwable $e) {
                Log::warning("Maç hatırlatması ({$key}, maç {$f->id}) gönderilemedi: ".$e->getMessage());
            }
        };

        // Web isteğinde üyeyi bekletmemek için yanıttan sonra; zamanlayıcıda (CLI) hemen
        app()->runningInConsole() ? $run() : dispatch($run)->afterResponse();
    }

    private function opened(Fixture $f): void
    {
        $this->notifications->sendToUsers(
            User::query()->get(),
            "⚽ {$this->title($f)}: tahminler açıldı!",
            'Skorunu tahmin et, Tribün Muhbiri sorularını cevapla. Tam skor 5 puan — AI\'ı yenebilecek misin?',
            self::CATEGORY,
        );
    }

    private function lastCall(Fixture $f): void
    {
        $done = MatchPrediction::query()->where('fixture_id', $f->id)->pluck('user_id');

        $this->notifications->sendToUsers(
            User::query()->whereNotIn('id', $done)->get(),
            "⏰ Son 3 saat: {$this->title($f)}",
            'Henüz tahminini yapmadın. Maç başlayınca tahminler kilitlenir!',
            self::CATEGORY,
        );
    }

    private function results(Fixture $f): void
    {
        $ranks = collect($this->game->board(null)['rows'])->keyBy('user_id');
        $score = $f->is_home
            ? "Göztepe {$f->home_score}-{$f->away_score} {$f->opponent}"
            : "{$f->opponent} {$f->home_score}-{$f->away_score} Göztepe";

        MatchPrediction::query()->where('fixture_id', $f->id)->with('user')->get()
            ->each(function (MatchPrediction $p) use ($ranks, $score) {
                if (! $p->user) {
                    return;
                }
                $rank = $ranks[$p->user_id]['rank'] ?? null;
                $lead = match (true) {
                    $p->points === 5 => '🎯 Tam skoru bildin!',
                    $p->points > 0 => 'Sonucu bildin!',
                    default => 'Bu sefer olmadı.',
                };
                $this->notifications->send(
                    $p->user,
                    "{$lead} +{$p->points} puan",
                    "{$score}. Tahminin {$p->home_goals}-{$p->away_goals}"
                        .($p->beat_ai ? ', AI\'ı yendin' : '')
                        .($rank ? ". Tribün sıralamasında {$rank}. sıradasın." : '.'),
                    null,
                    self::CATEGORY,
                );
            });
    }

    private function title(Fixture $f): string
    {
        return $f->is_home ? "Göztepe - {$f->opponent}" : "{$f->opponent} - Göztepe";
    }
}
