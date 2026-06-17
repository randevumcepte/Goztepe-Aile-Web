<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\TeamBadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function index(): JsonResponse
    {
        $fixtures = Fixture::orderByDesc('kickoff_at')->get();

        return response()->json([
            'items' => $fixtures->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request, TeamBadgeService $badges): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('opponent_logo')) {
            $data['opponent_logo_path'] = $request->file('opponent_logo')->store('fixtures', 'uploads');
        } else {
            // Logo elle yüklenmediyse rakip adından otomatik çek (TheSportsDB)
            $data['opponent_logo_path'] = $badges->badgeUrl($data['opponent']);
        }
        $fixture = Fixture::create($data);

        return response()->json([
            'message' => 'Maç eklendi.',
            'item' => $this->payload($fixture),
        ], 201);
    }

    public function update(Request $request, Fixture $fixture, TeamBadgeService $badges): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('opponent_logo')) {
            $data['opponent_logo_path'] = $request->file('opponent_logo')->store('fixtures', 'uploads');
        } elseif (! $fixture->opponent_logo_path || $fixture->opponent !== $data['opponent']) {
            // Logosu yoksa ya da rakip değiştiyse otomatik yeniden çek
            $data['opponent_logo_path'] = $badges->badgeUrl($data['opponent']);
        }
        $fixture->update($data);

        return response()->json([
            'message' => 'Maç güncellendi.',
            'item' => $this->payload($fixture),
        ]);
    }

    public function destroy(Fixture $fixture): JsonResponse
    {
        $fixture->delete();

        return response()->json([
            'message' => 'Maç silindi.',
        ]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'opponent' => ['required', 'string', 'max:120'],
            'competition' => ['required', 'string', 'max:80'],
            'is_home' => ['nullable', 'boolean'],
            'kickoff_at' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:160'],
            'broadcast' => ['nullable', 'string', 'max:80'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'opponent_logo' => ['nullable', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_home'] = $request->boolean('is_home');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function payload(Fixture $fixture): array
    {
        return [
            'id' => $fixture->id,
            'opponent' => $fixture->opponent,
            'opponent_logo' => $fixture->opponentLogoUrl(),
            'competition' => $fixture->competition,
            'is_home' => $fixture->is_home,
            'home_away_label' => $fixture->homeAwayLabel(),
            'kickoff_at' => $fixture->kickoff_at?->toIso8601String(),
            'venue' => $fixture->venue,
            'broadcast' => $fixture->broadcast,
            'home_score' => $fixture->home_score,
            'away_score' => $fixture->away_score,
            'is_played' => $fixture->isPlayed(),
            'is_active' => $fixture->is_active,
        ];
    }
}
