<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\PredictionGameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PredictionGameController extends Controller
{
    public function __construct(private PredictionGameService $game)
    {
        abort_unless(PredictionGameService::installed(), 503, 'Tahmin oyunu kuruluyor, birazdan tekrar dene.');
    }

    /** Skor tahmini kaydet/güncelle (maç başlayana kadar). */
    public function store(Request $request, Fixture $fixture): JsonResponse
    {
        abort_unless($fixture->is_active, 404);

        $data = $request->validate([
            'home_goals' => ['required', 'integer', 'min:0', 'max:15'],
            'away_goals' => ['required', 'integer', 'min:0', 'max:15'],
            'reason' => ['nullable', 'string', 'max:280'],
            'factors' => ['nullable', 'array', 'max:4'],
            'factors.*' => ['string', Rule::in(array_keys(PredictionGameService::FACTORS))],
        ]);

        $p = $this->game->save(
            $request->user(), $fixture,
            (int) $data['home_goals'], (int) $data['away_goals'],
            $data['reason'] ?? null, $data['factors'] ?? [],
        );

        return response()->json([
            'message' => 'Tahminin kaydedildi. Maç başlayana kadar değiştirebilirsin.',
            'prediction' => $this->game->predPayload($p),
            'crowd' => $this->game->crowd($fixture, $request->user()),
        ]);
    }

    /** Tribün ne diyor: dağılım, öne çıkan etkenler, gerekçe özeti, AI + tribün birleşik tahmin. */
    public function crowd(Request $request, Fixture $fixture): JsonResponse
    {
        abort_unless($fixture->is_active, 404);

        return response()->json($this->game->crowd($fixture, $request->user()));
    }

    /** Tribün sıralaması + rozetlerim + tahmin geçmişim. */
    public function board(Request $request): JsonResponse
    {
        return response()->json($this->game->board($request->user()));
    }
}
