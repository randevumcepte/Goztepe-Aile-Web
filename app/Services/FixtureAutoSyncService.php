<?php

namespace App\Services;

use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fikstürü TheSportsDB'den (ücretsiz) kendiliğinden tamamlar:
 *  - sıradaki Göztepe maçı yoksa ekler (admin sonradan düzenleyebilir)
 *  - biten maçın skoru girilmemişse doldurur
 * Admin'in girdiği maçlara dokunmaz; aynı gün (±36 saat) maç varsa eklemez.
 * Dış istek en fazla 3 saatte bir yapılır.
 */
class FixtureAutoSyncService
{
    private const BASE = 'https://www.thesportsdb.com/api/v1/json/123';

    /** TheSportsDB'de Göztepe'nin takım kimliği. */
    private const GOZTEPE_ID = '135891';

    private const SUPER_LIG_ID = '4339';

    public function sync(): void
    {
        if (! Cache::add('fixture_autosync:lock', 1, now()->addHours(3))) {
            return;
        }

        try {
            foreach ($this->events('eventsnext.php', 'events') as $e) {
                $this->addUpcoming($e);
            }
            foreach ($this->events('eventslast.php', 'results') as $e) {
                $this->fillScore($e);
            }
        } catch (\Throwable $e) {
            Log::warning('Fikstür otomatik eşitleme hatası: '.$e->getMessage());
        }
    }

    private function events(string $endpoint, string $key): array
    {
        $res = Http::timeout(8)->get(self::BASE.'/'.$endpoint, ['id' => self::GOZTEPE_ID]);
        $list = $res->ok() ? $res->json($key) : null;

        return is_array($list) ? $list : [];
    }

    private function addUpcoming(array $e): void
    {
        $kickoff = $this->kickoff($e);
        if (! $kickoff || $kickoff->isPast()) {
            return;
        }
        if ($this->near($kickoff)->exists()) {
            return;
        }

        $isHome = ($e['idHomeTeam'] ?? null) === self::GOZTEPE_ID;

        Fixture::create([
            'opponent' => $isHome ? $e['strAwayTeam'] : $e['strHomeTeam'],
            'opponent_logo_path' => ($isHome ? $e['strAwayTeamBadge'] ?? null : $e['strHomeTeamBadge'] ?? null) ?: null,
            'competition' => ($e['idLeague'] ?? null) === self::SUPER_LIG_ID ? 'Trendyol Süper Lig' : ($e['strLeague'] ?? 'Maç'),
            'is_home' => $isHome,
            'kickoff_at' => $kickoff,
            'venue' => ($e['strVenue'] ?? null) ?: null,
            'broadcast' => ($e['strTVStation'] ?? null) ?: null,
            'is_active' => true,
        ]);
    }

    private function fillScore(array $e): void
    {
        $kickoff = $this->kickoff($e);
        if (! $kickoff || ! is_numeric($e['intHomeScore'] ?? null) || ! is_numeric($e['intAwayScore'] ?? null)) {
            return;
        }

        // Fixture skorları ev sahibi - deplasman sırasında tutulur (kart da böyle gösterir)
        $this->near($kickoff)
            ->whereNull('home_score')
            ->get()
            ->each(fn (Fixture $f) => $f->update([
                'home_score' => (int) $e['intHomeScore'],
                'away_score' => (int) $e['intAwayScore'],
            ]));
    }

    /** TheSportsDB zamanları UTC'dir. */
    private function kickoff(array $e): ?Carbon
    {
        $ts = $e['strTimestamp'] ?? (($e['dateEvent'] ?? null) ? $e['dateEvent'].'T'.($e['strTime'] ?? '00:00:00') : null);
        if (! $ts) {
            return null;
        }

        try {
            return Carbon::parse($ts, 'UTC')->setTimezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }

    private function near(Carbon $kickoff)
    {
        return Fixture::query()->whereBetween('kickoff_at', [
            $kickoff->copy()->subHours(36),
            $kickoff->copy()->addHours(36),
        ]);
    }
}
