<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\MatchInsightService;
use App\Services\TeamBadgeService;
use Illuminate\Http\JsonResponse;

class MatchInsightController extends Controller
{
    /** Tek maçın AI analizi: olasılıklar, olası skorlar, karşılaştırma, önizleme metni. */
    public function show(Fixture $fixture, MatchInsightService $insights, TeamBadgeService $badges): JsonResponse
    {
        abort_unless($fixture->is_active, 404);

        return response()->json([
            'fixture' => [
                'id' => $fixture->id,
                'opponent' => $fixture->opponent,
                'opponent_logo' => $fixture->opponentLogoUrl(),
                'goztepe_logo' => $badges->goztepeBadgeUrl(),
                'competition' => $fixture->competition,
                'is_home' => $fixture->is_home,
                'kickoff_at' => $fixture->kickoff_at?->toIso8601String(),
                'venue' => $fixture->venue,
                // Tribün liderinin doğrulanmış kadro notu
                'leader_note' => $fixture->leader_note,
                'leader_note_at' => $fixture->leader_note_at?->toIso8601String(),
            ],
            'analysis' => $insights->analyze($fixture),
            'disclaimer' => 'Bu analiz geçmiş maç verilerine dayalı istatistiksel bir tahmindir, kesinlik içermez ve bahis tavsiyesi değildir.',
        ]);
    }
}
