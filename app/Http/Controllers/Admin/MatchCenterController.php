<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\MatchInsightService;
use App\Services\PredictionGameService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Maç Merkezi — AI maç analizi, Tribün Tahmin Oyunu, muhbir cevapları,
 * lider notu, hatırlatma ve canlı takip durumunu tek yerden izler.
 */
class MatchCenterController extends Controller
{
    public function index(MatchInsightService $insight, PredictionGameService $game): View
    {
        $installed = PredictionGameService::installed();

        $upcoming = Fixture::upcoming()->limit(10)->get();
        $played = Fixture::played()->limit(10)->get();

        $all = $upcoming->concat($played);
        if ($installed && $all->isNotEmpty()) {
            $all->each->loadCount(['predictions', 'intel']);
        }

        // AI olasılıkları: önce maça yazılmış anlık görüntü, yoksa (oynanmamışsa) hesap
        $probs = [];
        foreach ($all as $f) {
            $p = $f->ai_probs;
            if (! $p && ! $f->isPlayed()) {
                try {
                    $p = $insight->summary($f);
                } catch (\Throwable $e) {
                    $p = null;
                }
            }
            $probs[$f->id] = $p;
        }

        $board = null;
        if ($installed) {
            try {
                $board = $game->board(null);
            } catch (\Throwable $e) {
                $board = null;
            }
        }

        return view('admin.match-center.index', [
            'installed' => $installed,
            'upcoming' => $upcoming,
            'played' => $played,
            'probs' => $probs,
            'board' => $board,
            'status' => self::systemStatus(),
        ]);
    }

    public function show(Fixture $fixture, MatchInsightService $insight, PredictionGameService $game): View
    {
        $installed = PredictionGameService::installed();

        $analysis = null;
        if (! $fixture->isPlayed()) {
            try {
                $analysis = $insight->analyze($fixture);
            } catch (\Throwable $e) {
                $analysis = ['available' => false, 'reason' => 'Analiz hesaplanamadı: '.$e->getMessage()];
            }
            $fixture->refresh(); // analyze() AI anlık görüntüsünü maça yazmış olabilir
        }

        $crowd = null;
        $predictions = collect();
        $intel = collect();
        if ($installed) {
            try {
                $crowd = $this->adminCrowd($game, $fixture);
            } catch (\Throwable $e) {
                $crowd = null;
            }
            $predictions = $fixture->predictions()->with('user:id,name,email')->latest('updated_at')->get();
            $intel = $fixture->intel()->with('user:id,name,email')->latest('updated_at')->get();
        }

        return view('admin.match-center.show', [
            'fixture' => $fixture,
            'installed' => $installed,
            'analysis' => $analysis,
            'crowd' => $crowd,
            'predictions' => $predictions,
            'intel' => $intel,
            'factors' => PredictionGameService::FACTORS,
            'questions' => collect(PredictionGameService::INTEL_QUESTIONS)
                ->map(fn ($q) => str_replace('{rakip}', $fixture->opponent, $q)),
        ]);
    }

    public function saveLeaderNote(Request $request, Fixture $fixture): RedirectResponse
    {
        if (! PredictionGameService::installed()) {
            return back()->with('status', 'Tahmin oyunu tabloları kurulu değil (migrate çalıştırılmalı).');
        }

        $data = $request->validate([
            'leader_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $note = trim((string) ($data['leader_note'] ?? ''));
        $note = $note === '' ? null : $note;

        if ($note === $fixture->leader_note) {
            return back()->with('status', 'Lider notu değişmedi.');
        }

        $fixture->forceFill([
            'leader_note' => $note,
            'leader_note_at' => now(),
        ])->save();

        return back()->with('status', $note ? 'Lider notu kaydedildi. AI önizlemesi bu notu kullanacak.' : 'Lider notu silindi.');
    }

    public function refreshSummary(Fixture $fixture, PredictionGameService $game): RedirectResponse
    {
        if (! PredictionGameService::installed()) {
            return back()->with('status', 'Tahmin oyunu tabloları kurulu değil (migrate çalıştırılmalı).');
        }

        $fixture->forceFill([
            'crowd_summary' => null,
            'crowd_summary_hash' => null,
            'crowd_summary_at' => null,
        ])->saveQuietly();

        try {
            $crowd = $this->adminCrowd($game, $fixture->fresh());
            $msg = match (true) {
                (bool) $fixture->fresh()->crowd_summary => 'Tribün özeti AI ile yeniden oluşturuldu.',
                ! empty($crowd['summary']) => 'Tribün özeti yeniden oluşturuldu (kural tabanlı; AI özeti için en az 3 gerekçe/muhbir notu ve AI anahtarı gerekir).',
                default => 'Özet temizlendi; yeterli gerekçe/muhbir notu olmadığı için yeni özet oluşmadı.',
            };
        } catch (\Throwable $e) {
            $msg = 'Özet temizlendi ancak yeniden oluşturulamadı: '.$e->getMessage();
        }

        return back()->with('status', $msg);
    }

    /**
     * crowd() kopya tahmini önlemek için, tahmin yapmamış kişiye maç başlayana kadar dağılımı gizler.
     * Yönetici her zaman tam görünümü görmeli: gerekirse tahmin yapmış bir üyenin gözünden çağrılır
     * ("my_prediction" alanı bu ekranda kullanılmaz).
     */
    private function adminCrowd(PredictionGameService $game, Fixture $fixture): array
    {
        $crowd = $game->crowd($fixture, auth()->user());
        if (empty($crowd['visible'])) {
            $viewer = $fixture->predictions()->with('user')->first()?->user;
            if ($viewer) {
                $crowd = $game->crowd($fixture, $viewer);
            }
        }

        return $crowd;
    }

    /**
     * Zamanlanmış görevlerin son çalışma durumu (Sistem Araçları da kullanır).
     *
     * @return array<string, array{label:string, at:?Carbon, ago:?string, ok:bool, threshold:int}>
     */
    public static function systemStatus(): array
    {
        $items = [
            'live' => ['Canlı maç takibi', 'live_match:last_tick', 2],
            'reminders' => ['Maç hatırlatmaları', 'match_reminders:last_tick', 15],
        ];

        $out = [];
        foreach ($items as $key => [$label, $cacheKey, $minutes]) {
            $at = null;
            try {
                $raw = Cache::get($cacheKey);
                $at = $raw ? Carbon::parse($raw) : null;
            } catch (\Throwable $e) {
                $at = null;
            }
            $out[$key] = [
                'label' => $label,
                'at' => $at,
                'ago' => $at ? self::ago($at) : null,
                'ok' => $at !== null && $at->gt(now()->subMinutes($minutes)),
                'threshold' => $minutes,
            ];
        }

        return $out;
    }

    private static function ago(Carbon $at): string
    {
        $s = max(0, (int) $at->diffInSeconds(now()));
        if ($s < 60) {
            return $s.' sn önce';
        }
        if ($s < 3600) {
            return intdiv($s, 60).' dk önce';
        }
        if ($s < 86400) {
            return intdiv($s, 3600).' saat önce';
        }

        return intdiv($s, 86400).' gün önce';
    }
}
