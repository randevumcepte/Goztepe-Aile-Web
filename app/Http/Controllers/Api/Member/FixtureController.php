<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\TeamBadgeService;
use Illuminate\Http\JsonResponse;

class FixtureController extends Controller
{
    public function index(TeamBadgeService $badges): JsonResponse
    {
        $upcoming = Fixture::upcoming()->get();
        $played = Fixture::played()->limit(20)->get();

        // Eksik rakip logolarını otomatik tamamla (web ile aynı davranış)
        foreach ($upcoming->concat($played) as $f) {
            if (! $f->opponent_logo_path) {
                $url = $badges->badgeUrl($f->opponent);
                if ($url) {
                    $f->forceFill(['opponent_logo_path' => $url])->save();
                }
            }
        }

        return response()->json([
            'goztepe_badge' => $badges->goztepeBadgeUrl(),
            'upcoming' => $upcoming->map(fn ($f) => $this->payload($f))->values(),
            'played' => $played->map(fn ($f) => $this->payload($f))->values(),
        ]);
    }

    private function payload(Fixture $f): array
    {
        return [
            'id' => $f->id,
            'opponent' => $f->opponent,
            'opponent_logo' => $f->opponentLogoUrl(),
            'competition' => $f->competition,
            'is_home' => $f->is_home,
            'home_away_label' => $f->homeAwayLabel(),
            'kickoff_at' => $f->kickoff_at?->toIso8601String(),
            'venue' => $f->venue,
            'broadcast' => $f->broadcast,
            'home_score' => $f->home_score,
            'away_score' => $f->away_score,
            'is_played' => $f->isPlayed(),
        ];
    }
}
