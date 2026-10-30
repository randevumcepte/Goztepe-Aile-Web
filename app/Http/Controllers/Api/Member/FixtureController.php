<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\MatchPrediction;
use App\Services\FixtureAutoSyncService;
use App\Services\LiveMatchService;
use App\Services\MatchInsightService;
use App\Services\MatchReminderService;
use App\Services\PredictionGameService;
use App\Services\TeamBadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function index(Request $request, TeamBadgeService $badges, MatchInsightService $insights, FixtureAutoSyncService $sync, PredictionGameService $game): JsonResponse
    {
        $sync->sync();
        // Canlı maç: cron yoksa yedek tetikleyici (yanıttan sonra, üyeyi bekletmez)
        dispatch(fn () => app(LiveMatchService::class)->tick())->afterResponse();
        $gameOn = PredictionGameService::installed();
        if ($gameOn) {
            $game->settlePending();
            app(MatchReminderService::class)->tick();
        }

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

        // Üyenin kendi tahminleri (kartta "Tahminin: 1-2" / puan)
        $mine = $gameOn
            ? MatchPrediction::query()->where('user_id', $request->user()->id)
                ->whereIn('fixture_id', $upcoming->concat($played)->pluck('id'))->get()->keyBy('fixture_id')
            : collect();
        $my = fn ($f) => ['my_prediction' => isset($mine[$f->id]) ? $game->predPayload($mine[$f->id]) : null];

        return response()->json([
            'goztepe_badge' => $badges->goztepeBadgeUrl(),
            // Yaklaşan maçlara tahmin yüzdeleri (veri yoksa null — kartta gösterilmez)
            'upcoming' => $upcoming->map(fn ($f) => $this->payload($f) + ['prediction' => $insights->summary($f)] + $my($f))->values(),
            'played' => $played->map(fn ($f) => $this->payload($f) + $my($f))->values(),
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
