<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Rakip takım armasını TheSportsDB'den (ücretsiz) otomatik çeker.
 * Takım adı verilir, arma (badge) URL'i döner. Sonuç kalıcı önbelleğe alınır.
 */
class TeamBadgeService
{
    private const BASE = 'https://www.thesportsdb.com/api/v1/json/3/searchteams.php';

    /** Göztepe'nin TheSportsDB armasının yedeği (API erişilemezse). */
    private const GOZTEPE_FALLBACK = 'https://r2.thesportsdb.com/images/media/team/badge/9jwk7o1513952059.png';

    /** Göztepe armasını TheSportsDB'den çeker (önbellekli), erişilemezse yedeği döner. */
    public function goztepeBadgeUrl(): string
    {
        return $this->badgeUrl('Göztepe') ?? self::GOZTEPE_FALLBACK;
    }

    public function badgeUrl(string $team): ?string
    {
        $team = trim($team);
        if ($team === '') {
            return null;
        }

        $cacheKey = 'team_badge:'.md5(mb_strtolower($team));

        // Sadece başarılı sonuç önbelleğe alınır; bulunamayan takım bir sonraki kayıtta tekrar denenir.
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(6)->get(self::BASE, ['t' => $team]);
            if (! $response->ok()) {
                return null;
            }

            $teams = $response->json('teams');
            if (! is_array($teams) || empty($teams)) {
                return null;
            }

            // Tercihen Türk ligindeki kaydı seç, yoksa ilk sonucu al.
            $match = collect($teams)->first(
                fn ($t) => str_contains(mb_strtolower($t['strLeague'] ?? ''), 'turkish'),
                $teams[0]
            );

            $badge = $match['strBadge'] ?? $match['strTeamBadge'] ?? null;

            if ($badge) {
                Cache::forever($cacheKey, $badge);

                return $badge;
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
