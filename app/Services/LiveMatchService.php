<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Canlı maç bildirimleri: başladı, gol, kırmızı kart, devre arası, maç sonu.
 *
 * Veri: ESPN'in herkese açık skor servisi (anahtarsız; Süper Lig = "tur.1").
 * Resmî/belgelenmiş bir API değildir; biçimi değişirse sessizce durur (log düşer).
 * Maç saatinde 10 saniyede bir kontrol edilir — bunun için sunucuda cron
 * (php artisan schedule:run, her dakika) GEREKLİ; uygulama istekleri yalnız yedek tetikleyicidir.
 * Her olay maç başına bir kez gönderilir (fixtures.live_state).
 */
class LiveMatchService
{
    private const SCOREBOARD = 'https://site.api.espn.com/apis/site/v2/sports/soccer/tur.1/scoreboard';

    /** Bildirim tercih kategorisi (üye ayarlardan kapatabilir). */
    private const CATEGORY = 'canli';

    /** Takip edilen takım (normalize ad). Test için değiştirilebilir. */
    public string $team = 'goztepe';

    public function __construct(
        private NotificationService $notifications,
        private PredictionGameService $game,
    ) {}

    public static function installed(): bool
    {
        static $ok = null;

        return $ok ??= Schema::hasColumn('fixtures', 'live_state');
    }

    /** Maç penceresindeki (başlamadan 15 dk önce → başladıktan 3 saat sonra) maçları kontrol eder. */
    public function tick(): void
    {
        if (! self::installed() || ! Cache::add('live_match:lock', 1, now()->addSeconds(8))) {
            return;
        }
        // Yönetim panelindeki "cron çalışıyor mu" göstergesi
        Cache::forever('live_match:last_tick', now()->toIso8601String());

        Fixture::query()->where('is_active', true)
            ->where(fn ($q) => $q
                ->whereBetween('kickoff_at', [now()->subHours(3), now()->addMinutes(15)])
                // Telafi: takip kaçırıldıysa (cron yoksa / kimse uygulamayı açmadıysa) son 7 günde
                // skoru boş kalmış maçları ESPN'den kapat
                ->orWhere(fn ($q) => $q->whereNull('home_score')
                    ->whereBetween('kickoff_at', [now()->subDays(7), now()->subHours(3)])))
            ->get()
            ->each(function (Fixture $f) {
                try {
                    $this->sync($f);
                } catch (\Throwable $e) {
                    Log::warning("[Canlı] maç {$f->id} takip hatası: ".$e->getMessage());
                }
            });
    }

    public function sync(Fixture $f): void
    {
        $state = $f->live_state ?? [];
        if (! empty($state['done'])) {
            return;
        }

        $event = $this->findEvent($f, $state['espn_id'] ?? null);
        if (! $event) {
            return;
        }

        $comp = $event['competitions'][0] ?? [];
        $status = $event['status']['type'] ?? [];
        $teams = $this->teams($comp);
        if (! $teams) {
            return;
        }

        $first = empty($state);
        $state['espn_id'] = $event['id'];
        $state['seen'] ??= [];
        $state['sent'] ??= [];
        $clock = (int) floor(($event['status']['clock'] ?? 0) / 60);

        $score = fn () => "{$teams['home']['name']} {$teams['home']['score']}-{$teams['away']['score']} {$teams['away']['name']}";
        $isUs = fn (?string $teamId) => $teamId !== null && $teamId === $teams['us']['id'];

        // Takip maç ortasında başladıysa (ör. cron geç kuruldu) eski olayları sessizce işaretle
        $silent = $first && ($status['state'] ?? '') !== 'pre' && $clock > 15;

        // 1) Maç başladı
        if (($status['state'] ?? '') === 'in' && ! in_array('basladi', $state['sent'], true)) {
            $state['sent'][] = 'basladi';
            if (! $silent) {
                $this->broadcast($f, '🔴 Maç başladı!', $score().' · Haydi Göztepe! 💛❤️');
            }
        }

        // 2) Goller ve kırmızı kartlar — skor gol sırasına göre adım adım (o anki skor)
        $run = ['home' => 0, 'away' => 0];
        foreach ($comp['details'] ?? [] as $d) {
            $goal = ! empty($d['scoringPlay']);
            $red = ! empty($d['redCard']);
            if ($goal) {
                $side = ($d['team']['id'] ?? null) === $teams['home']['id'] ? 'home' : 'away';
                $run[$side] += (int) ($d['scoreValue'] ?? 1);
            }
            if (! $goal && ! $red) {
                continue;
            }
            $at = "{$teams['home']['name']} {$run['home']}-{$run['away']} {$teams['away']['name']}";
            $key = ($d['type']['id'] ?? '').':'.($d['clock']['value'] ?? '').':'.($d['team']['id'] ?? '').':'.($d['athletesInvolved'][0]['id'] ?? '');
            if (in_array($key, $state['seen'], true)) {
                continue;
            }
            $state['seen'][] = $key;
            if ($silent) {
                continue;
            }

            $who = $d['athletesInvolved'][0]['displayName'] ?? null;
            $min = $d['clock']['displayValue'] ?? '';
            $teamName = ($d['team']['id'] ?? null) === $teams['home']['id'] ? $teams['home']['name'] : $teams['away']['name'];

            if ($goal) {
                // Kendi kalesine gol: ESPN golü lehine yazılan takıma işler
                $tag = ! empty($d['ownGoal']) ? ' (kendi kalesine)' : (! empty($d['penaltyKick']) ? ' (penaltı)' : '');
                $line = $at.' · '.trim("{$min} {$who}").$tag;
                $isUs($d['team']['id'] ?? null)
                    ? $this->broadcast($f, '⚽ GOOOOL GÖZTEPE! 💛❤️', $line, ['kanal' => 'goztepe_gol'])
                    : $this->broadcast($f, "Gol: {$teamName}", $line);
            } else {
                $this->broadcast($f, "🟥 Kırmızı kart: {$teamName}", trim("{$min} {$who}").' · '.$at);
            }
        }

        // 3) Devre arası
        if (($status['name'] ?? '') === 'STATUS_HALFTIME' && ! in_array('devre', $state['sent'], true)) {
            $state['sent'][] = 'devre';
            if (! $silent) {
                $this->broadcast($f, '⏸️ Devre arası', $score());
            }
        }

        // 4) Maç sonu: skoru hemen yaz (tahmin puanları ve sonuç bildirimleri bunu bekler)
        if (($status['state'] ?? '') === 'post' && ! empty($status['completed'])) {
            $state['done'] = true;
            $f->forceFill(['home_score' => $teams['home']['score'], 'away_score' => $teams['away']['score']]);

            // Saatler sonra telafiyle kapanan maçta "maç bitti" yayını bayat olur; puan bildirimi yine gider
            if (! ($first && $f->kickoff_at->lt(now()->subHours(4)))) {
                $us = $teams['us']['score'];
                $them = $teams['them']['score'];
                $title = match (true) {
                    $us > $them => '🏁 Kazandık! 💛❤️',
                    $us === $them => '🏁 Maç berabere bitti',
                    default => '🏁 Maç bitti',
                };
                $this->broadcast($f, $title, $score());
            }
        }

        $f->forceFill(['live_state' => $state])->saveQuietly();

        if (! empty($state['done'])) {
            // Puanları hemen hesapla; kişisel sonuç bildirimi hatırlatma servisinden gider
            $this->game->settlePending();
            Cache::forget('match_reminders:lock');
            app(MatchReminderService::class)->tick();
        }
    }

    /** Fikstür maçının ESPN karşılığı: o günün maçlarında Göztepe'nin oynadığı maç. */
    private function findEvent(Fixture $f, ?string $espnId): ?array
    {
        $day = Carbon::parse($f->kickoff_at)->utc();
        foreach ([$day, $day->copy()->subDay()] as $d) {
            foreach ($this->scoreboard($d->format('Ymd')) as $e) {
                if ($espnId ? (string) $e['id'] === (string) $espnId : $this->teams($e['competitions'][0] ?? []) !== null) {
                    return $e;
                }
            }
        }

        return null;
    }

    private function scoreboard(string $ymd): array
    {
        return Cache::remember("live_match:espn:{$ymd}", now()->addSeconds(8), function () use ($ymd) {
            $res = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0 GoztepeTribunleri'])
                ->get(self::SCOREBOARD, ['dates' => $ymd]);
            if (! $res->ok()) {
                Log::warning("[Canlı] ESPN yanıtı {$res->status()}");

                return [];
            }

            return $res->json('events') ?? [];
        });
    }

    /** Ev/deplasman + bizim takım; maçta takip ettiğimiz takım yoksa null. */
    private function teams(array $comp): ?array
    {
        $t = [];
        foreach ($comp['competitors'] ?? [] as $c) {
            $t[$c['homeAway'] ?? ''] = [
                'id' => (string) ($c['team']['id'] ?? ''),
                'name' => $this->displayName($c['team']['displayName'] ?? '?'),
                'score' => (int) ($c['score'] ?? 0),
                'key' => $this->norm($c['team']['displayName'] ?? ''),
            ];
        }
        if (! isset($t['home'], $t['away'])) {
            return null;
        }
        foreach (['home' => 'away', 'away' => 'home'] as $us => $them) {
            if (str_contains($t[$us]['key'], $this->team)) {
                return $t + ['us' => $t[$us], 'them' => $t[$them]];
            }
        }

        return null;
    }

    /** ESPN İngilizce yazımını Türkçeleştirir (Fenerbahce → Fenerbahçe). */
    private function displayName(string $name): string
    {
        return strtr($name, [
            'Fenerbahce' => 'Fenerbahçe', 'Goztepe' => 'Göztepe', 'Besiktas' => 'Beşiktaş',
            'Kasimpasa' => 'Kasımpaşa', 'Basaksehir' => 'Başakşehir', 'Eyupspor' => 'Eyüpspor',
            'Genclerbirligi' => 'Gençlerbirliği', 'Caykur' => 'Çaykur', 'Corum' => 'Çorum',
        ]);
    }

    private function norm(string $s): string
    {
        $s = strtr($s, ['İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ç' => 'c', 'ç' => 'c', 'Ğ' => 'g', 'ğ' => 'g',
            'Ö' => 'o', 'ö' => 'o', 'Ş' => 's', 'ş' => 's', 'Ü' => 'u', 'ü' => 'u']);

        return preg_replace('/[^a-z0-9]/', '', mb_strtolower($s));
    }

    private function broadcast(Fixture $f, string $title, string $body, array $data = []): void
    {
        $this->notifications->sendToUsers(
            User::query()->get(), $title, $body, self::CATEGORY,
            data: $data + ['fixture_id' => (string) $f->id],
        );
    }
}
