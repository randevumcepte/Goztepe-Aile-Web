<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\MatchIntel;
use App\Models\MatchPrediction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Tribün Tahmin Oyunu — PARASIZ: ödül yalnız puan, rozet ve sıralama.
 *
 * Puan: tam skor 5 · sonuç + gol farkı 3 · yalnız sonuç 2 · diğer 0.
 * Öğrenme: her biten maçta AI modelinin ve tribünün olasılıkları Brier skoruyla
 * karşılaştırılır; tribünün "birleşik tahmin"deki ağırlığı bu isabete göre güncellenir.
 * Tribün içinde de isabetli tahmin geçmişi olan üyenin oyu daha ağır sayılır.
 */
class PredictionGameService
{
    public const FACTORS = [
        'sakatlik' => 'Sakatlık / ceza',
        'form' => 'Form durumu',
        'yorgunluk' => 'Yorgunluk / rotasyon',
        'deplasman' => 'İç saha / deplasman',
        'motivasyon' => 'Motivasyon / derbi',
        'taktik' => 'Taktik / hoca',
        'hakem' => 'Hakem',
        'hava' => 'Hava / zemin',
    ];

    /** Tribün Muhbiri soruları ({rakip} uygulamada rakip adıyla değişir). */
    public const INTEL_QUESTIONS = [
        'rakip_eksik' => '{rakip} kadrosunda sakat ya da cezalı eksik var mı?',
        'bizde_eksik' => 'Göztepe kadrosunda sakat ya da cezalı eksik var mı?',
        'surpriz_11' => 'İlk 11\'de sürpriz ya da rotasyon bekliyor musun?',
    ];

    public const INTEL_CHOICES = ['evet', 'hayir', 'bilmiyorum'];

    /** Hiç maç bitmemişken tribünün birleşik tahmindeki ağırlığı. */
    private const PRIOR_CROWD_WEIGHT = 0.25;

    /** Önsel ağırlığın kaç maçlık kanıt kadar direndiği. */
    private const PRIOR_MATCHES = 3;

    /** Birleşik tahmin / öğrenme için bir maçta gereken en az tahmin. */
    private const MIN_CROWD = 3;

    public function __construct(private AiTextService $ai) {}

    /** Tablolar kurulu mu? (kod çekilip migrate henüz çalışmadıysa oyun sessizce devre dışı) */
    public static function installed(): bool
    {
        static $ok = null;

        return $ok ??= Schema::hasTable('match_intel');
    }

    // ════════════════ Tahmin kaydı + puanlama ════════════════

    public function save(User $user, Fixture $fixture, int $home, int $away, ?string $reason, array $factors): MatchPrediction
    {
        if (! $fixture->isPredictionOpen()) {
            throw ValidationException::withMessages(['home_goals' => 'Maç başladı; bu maç için tahmin kapandı.']);
        }

        $reason = trim((string) $reason);

        return MatchPrediction::updateOrCreate(
            ['user_id' => $user->id, 'fixture_id' => $fixture->id],
            [
                'home_goals' => $home,
                'away_goals' => $away,
                'reason' => $reason === '' ? null : mb_substr($reason, 0, 280),
                'factors' => array_values(array_intersect(array_keys(self::FACTORS), $factors)) ?: null,
            ],
        );
    }

    public static function points(int $ph, int $pa, int $h, int $a): int
    {
        if ($ph === $h && $pa === $a) {
            return 5;
        }
        if (($ph <=> $pa) !== ($h <=> $a)) {
            return 0;
        }

        return $ph - $pa === $h - $a ? 3 : 2;
    }

    /** Skoru girilmiş maçlardaki puanlanmamış tahminleri puanlar (ucuz; her istekte çağrılabilir). */
    public function settlePending(): void
    {
        MatchPrediction::query()
            ->whereNull('points')
            ->whereHas('fixture', fn ($q) => $q->whereNotNull('home_score')->whereNotNull('away_score'))
            ->with('fixture')
            ->chunkById(200, function ($preds) {
                foreach ($preds as $p) {
                    $f = $p->fixture;
                    $pts = self::points($p->home_goals, $p->away_goals, $f->home_score, $f->away_score);
                    $aiPts = $f->ai_home_goals === null
                        ? null
                        : self::points($f->ai_home_goals, $f->ai_away_goals, $f->home_score, $f->away_score);
                    $p->update(['points' => $pts, 'beat_ai' => $aiPts === null ? null : $pts > $aiPts]);
                }
            });
    }

    // ════════════════ Tribün Muhbiri ════════════════

    public function saveIntel(User $user, Fixture $fixture, array $answers): MatchIntel
    {
        if (! $fixture->isPredictionOpen()) {
            throw ValidationException::withMessages(['answers' => 'Maç başladı; muhbir soruları kapandı.']);
        }

        $clean = [];
        foreach (self::INTEL_QUESTIONS as $key => $_) {
            $a = $answers[$key] ?? null;
            if (! is_array($a) || ! in_array($a['choice'] ?? null, self::INTEL_CHOICES, true)) {
                continue;
            }
            $text = trim((string) ($a['text'] ?? ''));
            $clean[$key] = [
                'choice' => $a['choice'],
                'text' => $a['choice'] === 'evet' && $text !== '' ? mb_substr($text, 0, 160) : null,
            ];
        }
        if (! $clean) {
            throw ValidationException::withMessages(['answers' => 'En az bir soruyu cevapla.']);
        }

        return MatchIntel::updateOrCreate(
            ['user_id' => $user->id, 'fixture_id' => $fixture->id],
            ['answers' => $clean],
        );
    }

    /** Muhbir soruları + (cevapladıysa ya da maç başladıysa) tribünün cevap dağılımı. */
    public function intelData(Fixture $fixture, User $user): array
    {
        $all = $fixture->intel()->get();
        $mine = $all->firstWhere('user_id', $user->id);
        $visible = $mine || ! $fixture->isPredictionOpen();

        $questions = [];
        foreach (self::INTEL_QUESTIONS as $key => $q) {
            $votes = $all->map(fn ($i) => $i->answers[$key]['choice'] ?? null)->filter();
            $questions[] = [
                'key' => $key,
                'question' => str_replace('{rakip}', $fixture->opponent, $q),
                'counts' => $visible
                    ? collect(self::INTEL_CHOICES)->mapWithKeys(fn ($c) => [$c => $votes->filter(fn ($v) => $v === $c)->count()])
                    : null,
                'total' => $votes->count(),
            ];
        }

        return [
            'open' => $fixture->isPredictionOpen(),
            'count' => $all->count(),
            'visible' => $visible,
            'questions' => $questions,
            'mine' => $mine?->answers,
        ];
    }

    // ════════════════ Tribün ne diyor ════════════════

    public function crowd(Fixture $fixture, User $user): array
    {
        $this->settlePending();

        $preds = $fixture->predictions()->get();
        $mine = $preds->firstWhere('user_id', $user->id);
        $open = $fixture->isPredictionOpen();

        $base = [
            'open' => $open,
            'count' => $preds->count(),
            'my_prediction' => $mine ? $this->predPayload($mine) : null,
            'factors' => collect(self::FACTORS)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values(),
            'ai_pick' => $fixture->ai_home_goals === null ? null : ['home' => $fixture->ai_home_goals, 'away' => $fixture->ai_away_goals],
            'intel' => $this->intelData($fixture, $user),
        ];

        // Kopya tahmini önlemek için: dağılım ancak kendi tahminini yapınca (ya da maç başlayınca) görünür
        if ($open && ! $mine) {
            return $base + ['visible' => false];
        }

        $crowdProbs = $this->crowdProbs($fixture, $preds, weighted: true);
        $popular = $preds->countBy(fn ($p) => $p->home_goals.'-'.$p->away_goals)->sortDesc();

        $out = $base + [
            'visible' => true,
            'distribution' => $preds->isEmpty() ? null : $this->percentages($this->crowdProbs($fixture, $preds, weighted: false)),
            'weighted' => $crowdProbs ? $this->percentages($crowdProbs) : null,
            'popular_score' => $popular->isEmpty() ? null : ['score' => $popular->keys()->first(), 'count' => $popular->first()],
            'top_factors' => $this->topFactors($preds),
            'summary' => $this->crowdSummary($fixture, $preds),
            'blend' => $this->blend($fixture, $crowdProbs, $preds->count()),
        ];

        if ($fixture->isPlayed()) {
            $judged = $preds->whereNotNull('beat_ai');
            $out['result'] = [
                'home' => $fixture->home_score,
                'away' => $fixture->away_score,
                'beat_ai_percent' => $judged->isEmpty() ? null : (int) round($judged->where('beat_ai', true)->count() / $judged->count() * 100),
                'exact_count' => $preds->where('points', 5)->count(),
            ];
        }

        return $out;
    }

    /** Göztepe gözünden tribün olasılıkları (0..1); ağırlıklıysa isabetli üyeler daha çok sayılır. */
    private function crowdProbs(Fixture $fixture, Collection $preds, bool $weighted): ?array
    {
        if ($preds->isEmpty()) {
            return null;
        }

        $rel = $weighted ? $this->reliability($preds->pluck('user_id')->all()) : [];
        $sum = ['goztepe_win' => 0.0, 'draw' => 0.0, 'opponent_win' => 0.0];
        foreach ($preds as $p) {
            $sum[$this->goztepeOutcome($fixture, $p->home_goals, $p->away_goals)] += $rel[$p->user_id] ?? 1.0;
        }
        $total = array_sum($sum);

        return array_map(fn ($v) => $v / $total, $sum);
    }

    /**
     * Üye güvenilirliği: (doğru sonuç + 1) / (biten tahmin + 2) × 2 → yeni üye 1,
     * hep bilen 2'ye, hep yanılan 0'a yaklaşır. Troller kendiliğinden etkisizleşir.
     */
    private function reliability(array $userIds): array
    {
        return MatchPrediction::query()
            ->whereIn('user_id', $userIds)
            ->whereNotNull('points')
            ->selectRaw('user_id, COUNT(*) as n, SUM(CASE WHEN points >= 2 THEN 1 ELSE 0 END) as ok')
            ->groupBy('user_id')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->user_id => 2 * ($r->ok + 1) / ($r->n + 2)])
            ->all();
    }

    private function topFactors(Collection $preds): array
    {
        $counts = [];
        foreach ($preds as $p) {
            foreach ($p->factors ?? [] as $f) {
                $counts[$f] = ($counts[$f] ?? 0) + 1;
            }
        }
        arsort($counts);
        $acc = $this->factorAccuracy();

        return collect(array_slice($counts, 0, 4, true))->map(fn ($n, $key) => [
            'key' => $key,
            'label' => self::FACTORS[$key] ?? $key,
            'count' => $n,
            // Geçmişte bu gerekçeyle tahmin yapanların sonucu bilme oranı (öğrenme göstergesi)
            'past_accuracy' => $acc[$key] ?? null,
        ])->values()->all();
    }

    /** Etken → geçmiş isabet % (en az 5 biten tahmin varsa). */
    private function factorAccuracy(): array
    {
        return Cache::remember('prediction_game:factor_accuracy', now()->addMinutes(10), function () {
            $stats = [];
            MatchPrediction::query()->whereNotNull('points')->whereNotNull('factors')
                ->get(['factors', 'points'])
                ->each(function ($p) use (&$stats) {
                    foreach ($p->factors ?? [] as $f) {
                        $stats[$f]['n'] = ($stats[$f]['n'] ?? 0) + 1;
                        $stats[$f]['ok'] = ($stats[$f]['ok'] ?? 0) + ($p->points >= 2 ? 1 : 0);
                    }
                });

            return collect($stats)->filter(fn ($s) => $s['n'] >= 5)
                ->map(fn ($s) => (int) round($s['ok'] / $s['n'] * 100))->all();
        });
    }

    /** Gerekçelerin özeti: 3+ gerekçe varsa Haiku (en çok 20 dk'da bir), yoksa etken sayımı. */
    private function crowdSummary(Fixture $fixture, Collection $preds): ?string
    {
        $reasons = $preds->filter(fn ($p) => $p->reason)->sortBy('id');
        $intel = $fixture->intel()->orderBy('id')->get();
        $tips = $intel->flatMap(fn ($i) => collect($i->answers)
            ->filter(fn ($a) => ($a['choice'] ?? null) === 'evet')
            ->map(fn ($a, $k) => '- [muhbir: '.str_replace('{rakip}', $fixture->opponent, self::INTEL_QUESTIONS[$k] ?? $k).'] '.($a['text'] ?: 'evet'))
            ->values())
            ->values();

        if ($reasons->count() + $tips->count() < self::MIN_CROWD || ! $this->ai->available()) {
            return $this->ruleSummary($fixture, $preds, $intel);
        }

        $hash = md5($reasons->map(fn ($p) => $p->id.':'.$p->reason)->join('|').'#'.$tips->join('|'));
        $fresh = $fixture->crowd_summary_at && $fixture->crowd_summary_at->gt(now()->subMinutes(20));
        if ($fixture->crowd_summary && ($fixture->crowd_summary_hash === $hash || $fresh)) {
            return $fixture->crowd_summary;
        }

        $lines = $reasons->take(-60)->map(fn ($p) => '- ['.$this->goztepeOutcome($fixture, $p->home_goals, $p->away_goals).'] '
            .str_replace(["\n", "\r"], ' ', $p->reason))
            ->concat($tips->take(-60)->map(fn ($t) => str_replace(["\n", "\r"], ' ', $t)))
            ->join("\n");

        $text = $this->ai->write(
            'Göztepe taraftar uygulamasında üyelerin maç tahmini gerekçelerini özetliyorsun. '
            .'2-3 kısa Türkçe cümleyle, maçın sonucunu etkileyebilecek SOMUT iddiaları (sakatlık, ceza, form, rotasyon, '
            .'motivasyon, taktik vb.) ve bunları kaç üyenin dile getirdiğini yaz. Bunlar doğrulanmamış tribün iddialarıdır, '
            .'kesin bilgi gibi sunma. Hakaret, küfür, alakasız ya da kişisel içerikleri tamamen yok say ve tekrarlama. '
            .'Bahis/oran/kupon dili yok. Başlık, madde işareti, emoji yok.',
            'Maç: '.($fixture->is_home ? 'Göztepe - '.$fixture->opponent : $fixture->opponent.' - Göztepe')
            ."\nGerekçeler ve muhbir notları (köşeli parantezde üyenin tahmini ya da cevapladığı muhbir sorusu):\n".$lines,
        );
        if (! $text) {
            return $fixture->crowd_summary;
        }

        $fixture->forceFill(['crowd_summary' => $text, 'crowd_summary_hash' => $hash, 'crowd_summary_at' => now()])->saveQuietly();

        return $text;
    }

    private function ruleSummary(Fixture $fixture, Collection $preds, Collection $intel): ?string
    {
        $parts = [];
        $top = $this->topFactors($preds);
        if ($top) {
            $parts[] = 'Tribünün öne çıkardığı etkenler: '.collect($top)->map(fn ($t) => "{$t['label']} ({$t['count']} üye)")->join(', ').'.';
        }
        $whats = [
            'rakip_eksik' => $fixture->opponent.' kadrosunda eksik olduğunu',
            'bizde_eksik' => 'Göztepe kadrosunda eksik olduğunu',
            'surpriz_11' => 'sürpriz ilk 11 beklediğini',
        ];
        foreach ($whats as $k => $what) {
            $yes = $intel->filter(fn ($i) => ($i->answers[$k]['choice'] ?? null) === 'evet')->count();
            if ($yes > 0) {
                $parts[] = "{$yes} muhbir {$what} söylüyor.";
            }
        }

        return $parts ? implode(' ', $parts) : null;
    }

    // ════════════════ Öğrenme: AI + tribün birleşik tahmin ════════════════

    /** Biten maçlardaki isabete göre tribün ağırlığı (Brier skoru, düşük = iyi). */
    public function learning(): array
    {
        return Cache::remember('prediction_game:learning', now()->addMinutes(10), function () {
            $fixtures = Fixture::query()->whereNotNull('home_score')->whereNotNull('away_score')
                ->whereNotNull('ai_probs')->with('predictions')->get()
                ->filter(fn ($f) => $f->predictions->count() >= self::MIN_CROWD);

            $bm = $bc = 0.0;
            $crowdBetter = 0;
            foreach ($fixtures as $f) {
                $actual = $this->goztepeOutcome($f, $f->home_score, $f->away_score);
                $model = array_map(fn ($v) => $v / 100, $f->ai_probs);
                $crowd = $this->crowdProbs($f, $f->predictions, weighted: false);
                // Laplace yumuşatma: tek tip tahminde tribün 0/1'e saplanmasın
                $n = $f->predictions->count();
                $crowd = array_map(fn ($v) => ($v * $n + 1) / ($n + 3), $crowd);
                $m = $this->brier($model, $actual);
                $c = $this->brier($crowd, $actual);
                $bm += $m;
                $bc += $c;
                $crowdBetter += $c < $m ? 1 : 0;
            }

            $n = $fixtures->count();
            $target = $n > 0 && ($bm + $bc) > 0 ? $bm / ($bm + $bc) : self::PRIOR_CROWD_WEIGHT;
            $weight = (self::PRIOR_CROWD_WEIGHT * self::PRIOR_MATCHES + $n * $target) / (self::PRIOR_MATCHES + $n);

            return [
                'matches' => $n,
                'crowd_weight' => round(max(0.1, min(0.7, $weight)), 2),
                'crowd_better' => $crowdBetter,
                'model_brier' => $n ? round($bm / $n, 3) : null,
                'crowd_brier' => $n ? round($bc / $n, 3) : null,
            ];
        });
    }

    private function blend(Fixture $fixture, ?array $crowd, int $count): ?array
    {
        if (! $crowd || ! $fixture->ai_probs || $count < self::MIN_CROWD) {
            return null;
        }

        $l = $this->learning();
        $w = $l['crowd_weight'];
        $mixed = [];
        foreach ($crowd as $k => $v) {
            $mixed[$k] = (1 - $w) * (($fixture->ai_probs[$k] ?? 0) / 100) + $w * $v;
        }

        $why = $l['matches'] === 0
            ? 'Henüz biten maç yok; tribünün ağırlığı başlangıç değerinde.'
            : "Son {$l['matches']} maçın {$l['crowd_better']}'inde tribün AI'dan isabetliydi; ağırlık buna göre ayarlandı.";

        return [
            'probabilities' => $this->percentages($mixed),
            'crowd_weight' => (int) round($w * 100),
            'learned_from' => $l['matches'],
            'explanation' => $why,
        ];
    }

    private function brier(array $p, string $actual): float
    {
        $s = 0.0;
        foreach (['goztepe_win', 'draw', 'opponent_win'] as $k) {
            $s += (($p[$k] ?? 0) - ($k === $actual ? 1 : 0)) ** 2;
        }

        return $s;
    }

    // ════════════════ Sıralama + rozetler ════════════════

    /** Sezonluk tribün sıralaması + AI satırı + kullanıcının kendi durumu. */
    public function board(?User $user): array
    {
        $this->settlePending();

        [$start, $end, $label] = $this->season();
        $settled = MatchPrediction::query()->whereNotNull('points')
            ->whereHas('fixture', fn ($q) => $q->whereBetween('kickoff_at', [$start, $end]))
            ->with('fixture:id,kickoff_at')
            ->get()
            ->groupBy('user_id');

        $settledFixtures = Fixture::query()->whereNotNull('home_score')->whereNotNull('away_score')
            ->whereBetween('kickoff_at', [$start, $end])->orderBy('kickoff_at')->get();

        $users = User::query()->whereIn('id', $settled->keys())->get(['id', 'name'])->keyBy('id');
        $intelCounts = MatchIntel::query()
            ->whereHas('fixture', fn ($q) => $q->whereBetween('kickoff_at', [$start, $end]))
            ->selectRaw('user_id, COUNT(*) as n')->groupBy('user_id')->pluck('n', 'user_id');

        $rows = $settled->map(fn ($preds, $uid) => [
            'user_id' => (int) $uid,
            'name' => $this->displayName($users[$uid]->name ?? 'Üye'),
            'points' => (int) $preds->sum('points'),
            'exact' => $preds->where('points', 5)->count(),
            'correct' => $preds->where('points', '>=', 2)->count(),
            'played' => $preds->count(),
            'beat_ai' => $preds->where('beat_ai', true)->count(),
            'badges' => collect($this->badges($preds, $settledFixtures, null, (int) ($intelCounts[$uid] ?? 0)))->where('earned', true)->pluck('key')->values(),
        ])->sort(fn ($a, $b) => [$b['points'], $b['exact'], $a['played']] <=> [$a['points'], $a['exact'], $b['played']])
            ->values();

        // Sıra: aynı puan + aynı tam skor sayısı = aynı sıra
        $rank = 0;
        $prev = null;
        $rows = $rows->map(function ($r, $i) use (&$rank, &$prev) {
            $key = $r['points'].'|'.$r['exact'];
            if ($key !== $prev) {
                $rank = $i + 1;
                $prev = $key;
            }

            return ['rank' => $rank] + $r;
        });

        $me = $user ? $rows->firstWhere('user_id', $user->id) : null;
        $myPreds = $user
            ? MatchPrediction::query()->where('user_id', $user->id)->with('fixture')->latest('id')->limit(30)->get()
            : collect();

        return [
            'season' => $label,
            'players' => $rows->count(),
            'rows' => $rows->take(100)->values(),
            'me' => [
                'row' => $me,
                'badges' => $this->badges($settled->get($user?->id, collect()), $settledFixtures, $myPreds, (int) ($intelCounts[$user?->id] ?? 0)),
                'history' => $myPreds->map(fn ($p) => $this->historyPayload($p))->values(),
            ],
            'ai' => $this->aiRow($settledFixtures),
            'learning' => $this->learning(),
            'scoring' => 'Tam skor 5 · Sonuç + gol farkı 3 · Sadece sonuç 2 puan',
        ];
    }

    private function aiRow(Collection $fixtures): array
    {
        $pts = $exact = $n = 0;
        foreach ($fixtures as $f) {
            if ($f->ai_home_goals === null) {
                continue;
            }
            $p = self::points($f->ai_home_goals, $f->ai_away_goals, $f->home_score, $f->away_score);
            $pts += $p;
            $exact += $p === 5 ? 1 : 0;
            $n++;
        }

        return ['name' => 'AI Model', 'points' => $pts, 'exact' => $exact, 'played' => $n];
    }

    /**
     * Rozetler. $settled: kullanıcının bu sezon biten tahminleri.
     * $all (opsiyonel): gerekçe sayımı için tüm tahminleri.
     */
    private function badges(Collection $settled, Collection $settledFixtures, ?Collection $all = null, int $intelCount = 0): array
    {
        $ordered = $settled->sortBy(fn ($p) => $p->fixture?->kickoff_at);
        $exact = $settled->where('points', 5)->count();
        $beat = $settled->where('beat_ai', true)->count();

        $streak = $best = 0;
        foreach ($ordered as $p) {
            $streak = $p->points >= 2 ? $streak + 1 : 0;
            $best = max($best, $streak);
        }

        // Sadık: ilk tahmininden beri biten her maça tahmin yaptı (en az 5 maç)
        $first = $ordered->first()?->fixture?->kickoff_at;
        $since = $first ? $settledFixtures->filter(fn ($f) => $f->kickoff_at->gte($first)) : collect();
        $loyal = $since->count() >= 5 && $since->pluck('id')->diff($settled->pluck('fixture_id'))->isEmpty();

        $reasons = ($all ?? $settled)->filter(fn ($p) => $p->reason)->count();
        $any = ($all ?? $settled)->count();

        return [
            ['key' => 'ilk_tahmin', 'label' => 'Tribüne Hoş Geldin', 'desc' => 'İlk tahminini yap', 'earned' => $any >= 1, 'progress' => min($any, 1).'/1'],
            ['key' => 'kahin', 'label' => 'Kâhin', 'desc' => '3 maçta tam skoru bil', 'earned' => $exact >= 3, 'progress' => min($exact, 3).'/3'],
            ['key' => 'seri', 'label' => 'Seri', 'desc' => 'Üst üste 5 maçın sonucunu bil', 'earned' => $best >= 5, 'progress' => min($best, 5).'/5'],
            ['key' => 'ai_avcisi', 'label' => 'AI Avcısı', 'desc' => '3 maçta AI\'dan çok puan al', 'earned' => $beat >= 3, 'progress' => min($beat, 3).'/3'],
            ['key' => 'tribunun_sesi', 'label' => 'Tribünün Sesi', 'desc' => '5 tahmine gerekçe yaz', 'earned' => $reasons >= 5, 'progress' => min($reasons, 5).'/5'],
            ['key' => 'sadik', 'label' => 'Sadık Tribün', 'desc' => 'Hiç maç kaçırmadan 5 maç tahmin et', 'earned' => $loyal, 'progress' => $loyal ? '5/5' : min($settled->count(), 4).'/5'],
            ['key' => 'muhbir', 'label' => 'Muhbir', 'desc' => '3 maçta muhbir sorularını cevapla', 'earned' => $intelCount >= 3, 'progress' => min($intelCount, 3).'/3'],
        ];
    }

    // ════════════════ Yardımcılar ════════════════

    public function predPayload(MatchPrediction $p): array
    {
        return [
            'home' => $p->home_goals,
            'away' => $p->away_goals,
            'reason' => $p->reason,
            'factors' => $p->factors ?? [],
            'points' => $p->points,
            'beat_ai' => $p->beat_ai,
        ];
    }

    private function historyPayload(MatchPrediction $p): array
    {
        $f = $p->fixture;

        return [
            'fixture_id' => $f->id,
            'opponent' => $f->opponent,
            'is_home' => $f->is_home,
            'kickoff_at' => $f->kickoff_at?->toIso8601String(),
            'prediction' => ['home' => $p->home_goals, 'away' => $p->away_goals],
            'result' => $f->isPlayed() ? ['home' => $f->home_score, 'away' => $f->away_score] : null,
            'ai_pick' => $f->ai_home_goals === null ? null : ['home' => $f->ai_home_goals, 'away' => $f->ai_away_goals],
            'points' => $p->points,
            'beat_ai' => $p->beat_ai,
        ];
    }

    private function goztepeOutcome(Fixture $f, int $h, int $a): string
    {
        $o = $h <=> $a;
        if ($o === 0) {
            return 'draw';
        }

        return ($o === 1) === (bool) $f->is_home ? 'goztepe_win' : 'opponent_win';
    }

    /** 0..1 olasılıkları toplamı 100 olan tam yüzdelere çevirir. */
    private function percentages(array $p): array
    {
        $raw = array_map(fn ($v) => $v * 100, $p);
        $out = array_map(fn ($v) => (int) floor($v), $raw);
        $frac = array_map(fn ($v) => $v - floor($v), $raw);
        arsort($frac);
        $rest = 100 - array_sum($out);
        foreach (array_keys($frac) as $k) {
            if ($rest-- <= 0) {
                break;
            }
            $out[$k]++;
        }

        return $out;
    }

    /** "Ferdi Korkmaz" → "Ferdi K." (sıralamada gizlilik). */
    private function displayName(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [];
        if (count($parts) < 2) {
            return $parts[0] ?? 'Üye';
        }

        return $parts[0].' '.mb_strtoupper(mb_substr(end($parts), 0, 1)).'.';
    }

    /** Sezon: 1 Temmuz – 30 Haziran. */
    private function season(): array
    {
        $y = now()->month >= 7 ? now()->year : now()->year - 1;

        return [Carbon::create($y, 7, 1), Carbon::create($y + 1, 6, 30, 23, 59, 59), $y.'–'.substr((string) ($y + 1), -2)];
    }
}
