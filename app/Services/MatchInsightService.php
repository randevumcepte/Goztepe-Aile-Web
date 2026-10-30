<?php

namespace App\Services;

use App\Models\Fixture;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Maç analizi + skor tahmini — ücretsiz veri, kendi modelimiz.
 *
 * Veri: Wikipedia'daki güncel Süper Lig sezon sayfası (kaynağı TFF):
 *  - puan tablosu (G/B/M, atılan/yenen gol)
 *  - iç saha / deplasman sonuç matrisi (her oynanmış maçın skoru)
 * Model: Poisson — her takımın iç saha ve deplasman hücum/savunma gücü,
 *  küçük örnekte genel güce, genel güç de lig ortalamasına çekilir.
 * Yorum: Anthropic anahtarı + SDK varsa Haiku, yoksa kural tabanlı metin.
 */
class MatchInsightService
{
    private const WIKI_API = 'https://en.wikipedia.org/w/api.php';

    /** Skor matrisinin üst sınırı (0..7 gol). */
    private const MAX_GOALS = 7;

    /** Genel gücü lig ortalamasına (1) çeken sanal maç sayısı. */
    private const SHRINK = 4;

    /** İç saha/deplasman gücünü genel güce çeken sanal maç sayısı (yön örneği çok küçük). */
    private const SIDE_SHRINK = 8;

    /** Ortalamaya dönüş: güç katsayısı bu üsle sönümlenir (erken sezon uç değerlerini yumuşatır). */
    private const DAMP = 0.8;

    /** Düşük skorlarda beraberliği hafif artıran Dixon-Coles düzeltmesi. */
    private const RHO = -0.08;

    /** Lig verisi henüz azsa kullanılan tipik Süper Lig gol ortalamaları. */
    private const DEFAULT_HOME_GOALS = 1.45;
    private const DEFAULT_AWAY_GOALS = 1.15;

    /** Önbellek/şema sürümü — çıktı biçimi değişince artır. */
    private const VERSION = 'v1';

    // ════════════════ Dış API ════════════════

    /**
     * Fikstür kartı için kısa özet (yüzdeler) — yalnız hesap, AI metni yok.
     *
     * @return array{goztepe_win:int, draw:int, opponent_win:int}|null
     */
    public function summary(Fixture $fixture): ?array
    {
        $p = $this->predict($fixture);

        return $p ? $p['probabilities'] : null;
    }

    /** Tam maç analizi (olasılıklar + olası skorlar + karşılaştırma + yorum). */
    public function analyze(Fixture $fixture): array
    {
        $p = $this->predict($fixture);

        if (! $p) {
            return [
                'available' => false,
                'reason' => $fixture->isPlayed()
                    ? 'Bu maç oynandı; tahmin yalnızca oynanmamış maçlar için gösterilir.'
                    : 'Bu rakip için Süper Lig verisi bulunamadı (kupa/hazırlık maçı olabilir).',
            ];
        }

        $p['available'] = true;
        $p['insights'] = $this->insights($p);
        $p['preview'] = $this->previewText($fixture, $p);

        return $p;
    }

    // ════════════════ Tahmin motoru ════════════════

    /** Göztepe gözünden Poisson tahmini; veri yoksa null. */
    private function predict(Fixture $fixture): ?array
    {
        if ($fixture->isPlayed()) {
            return null;
        }

        $league = $this->leagueData($this->seasonStartYear($fixture));
        if (! $league) {
            return null;
        }

        $goz = $this->findTeam($league['teams'], 'Göztepe');
        $opp = $this->findTeam($league['teams'], $fixture->opponent);
        if (! $goz || ! $opp || $goz['code'] === $opp['code']) {
            return null;
        }

        [$home, $away] = $fixture->is_home ? [$goz, $opp] : [$opp, $goz];
        $muH = $league['avg_home'];
        $muA = $league['avg_away'];
        $muAll = ($muH + $muA) / 2;

        $lambdaHome = $muH * $this->strength($home, 'home', 'att', $muH, $muAll)
            * $this->strength($away, 'away', 'def', $muH, $muAll);
        $lambdaAway = $muA * $this->strength($away, 'away', 'att', $muA, $muAll)
            * $this->strength($home, 'home', 'def', $muA, $muAll);

        // Futbolda maç başı beklenen gol nadiren 3,5'i aşar
        $lambdaHome = max(0.25, min(3.5, $lambdaHome));
        $lambdaAway = max(0.25, min(3.5, $lambdaAway));

        $m = $this->scoreMatrix($lambdaHome, $lambdaAway);

        $pHome = $pDraw = $pAway = $over25 = $btts = 0.0;
        $scores = [];
        foreach ($m as $h => $row) {
            foreach ($row as $a => $v) {
                if ($h > $a) {
                    $pHome += $v;
                } elseif ($h === $a) {
                    $pDraw += $v;
                } else {
                    $pAway += $v;
                }
                if ($h + $a >= 3) {
                    $over25 += $v;
                }
                if ($h > 0 && $a > 0) {
                    $btts += $v;
                }
                $scores[] = ['home' => $h, 'away' => $a, 'p' => $v];
            }
        }
        usort($scores, fn ($x, $y) => $y['p'] <=> $x['p']);

        [$gWin, $oWin] = $fixture->is_home ? [$pHome, $pAway] : [$pAway, $pHome];
        [$gXg, $oXg] = $fixture->is_home ? [$lambdaHome, $lambdaAway] : [$lambdaAway, $lambdaHome];

        // AI'ın oyundaki skor tahmini: en olası sonucun içindeki en olası skor
        $best = array_search(max($pHome, $pDraw, $pAway), ['H' => $pHome, 'D' => $pDraw, 'A' => $pAway], true);
        $pick = collect($scores)->first(fn ($s) => ($s['home'] <=> $s['away']) === ['H' => 1, 'D' => 0, 'A' => -1][$best]);

        $probabilities = $this->percentages($gWin, $pDraw, $oWin);
        $this->snapshotAi($fixture, $probabilities, $pick);

        return [
            'probabilities' => $probabilities,
            'ai_pick' => ['home' => $pick['home'], 'away' => $pick['away']],
            'expected_goals' => ['goztepe' => round($gXg, 2), 'opponent' => round($oXg, 2)],
            // Skorlar kartla aynı sırada: ev sahibi - deplasman
            'likely_scores' => array_map(fn ($s) => [
                'home' => $s['home'],
                'away' => $s['away'],
                'percent' => (int) round($s['p'] * 100),
            ], array_slice($scores, 0, 3)),
            'over_25' => (int) round($over25 * 100),
            'btts' => (int) round($btts * 100),
            'confidence' => $this->confidence(min($goz['played'], $opp['played'])),
            'goztepe' => $this->teamPayload($goz),
            'opponent' => $this->teamPayload($opp),
            'source' => [
                'name' => 'Süper Lig '.$league['season'].' (Wikipedia / TFF)',
                'updated' => $league['updated'],
                'matches' => $league['matches'],
            ],
            'model' => 'Poisson (iç saha/deplasman hücum-savunma gücü)',
        ];
    }

    /**
     * Maç başlayana kadar AI tahminini maça yazar; başladıktan sonra donar.
     * Tahmin oyunundaki "AI'ı Yen" ve tribün/AI isabet karşılaştırması buna dayanır.
     */
    private function snapshotAi(Fixture $fixture, array $probabilities, array $pick): void
    {
        if (! $fixture->exists || ! $fixture->isPredictionOpen() || ! PredictionGameService::installed()) {
            return;
        }

        $fixture->forceFill([
            'ai_probs' => $probabilities,
            'ai_home_goals' => $pick['home'],
            'ai_away_goals' => $pick['away'],
        ]);
        if ($fixture->isDirty()) {
            $fixture->saveQuietly();
        }
    }

    /**
     * Takımın bir yöndeki (iç saha/deplasman) hücum veya savunma katsayısı.
     * Yön verisi azsa genel güce, genel güç de 1'e (lig ortalaması) çekilir.
     */
    private function strength(array $t, string $side, string $kind, float $muSide, float $muAll): float
    {
        $played = max(0, $t['played']);
        $overallRaw = $played > 0
            ? (($kind === 'att' ? $t['gf'] : $t['ga']) / $played) / $muAll
            : 1.0;
        $overall = ($played * $overallRaw + self::SHRINK * 1.0) / ($played + self::SHRINK);

        $n = $t[$side]['p'];
        if ($n === 0) {
            return $overall ** self::DAMP;
        }
        $raw = (($kind === 'att' ? $t[$side]['gf'] : $t[$side]['ga']) / $n) / $muSide;

        return (($n * $raw + self::SIDE_SHRINK * $overall) / ($n + self::SIDE_SHRINK)) ** self::DAMP;
    }

    /** Dixon-Coles düzeltmeli, normalize edilmiş skor olasılık matrisi. */
    private function scoreMatrix(float $lh, float $la): array
    {
        $m = [];
        $sum = 0.0;
        for ($h = 0; $h <= self::MAX_GOALS; $h++) {
            for ($a = 0; $a <= self::MAX_GOALS; $a++) {
                $v = $this->poisson($h, $lh) * $this->poisson($a, $la) * $this->tau($h, $a, $lh, $la);
                $m[$h][$a] = max(0.0, $v);
                $sum += $m[$h][$a];
            }
        }
        foreach ($m as $h => $row) {
            foreach ($row as $a => $v) {
                $m[$h][$a] = $v / $sum;
            }
        }

        return $m;
    }

    private function poisson(int $k, float $lambda): float
    {
        $fact = 1;
        for ($i = 2; $i <= $k; $i++) {
            $fact *= $i;
        }

        return exp(-$lambda) * $lambda ** $k / $fact;
    }

    private function tau(int $h, int $a, float $lh, float $la): float
    {
        return match (true) {
            $h === 0 && $a === 0 => 1 - $lh * $la * self::RHO,
            $h === 0 && $a === 1 => 1 + $lh * self::RHO,
            $h === 1 && $a === 0 => 1 + $la * self::RHO,
            $h === 1 && $a === 1 => 1 - self::RHO,
            default => 1.0,
        };
    }

    /** Üç olasılığı toplamı tam 100 olan tam sayılara çevirir. */
    private function percentages(float $g, float $d, float $o): array
    {
        $raw = ['goztepe_win' => $g * 100, 'draw' => $d * 100, 'opponent_win' => $o * 100];
        $out = array_map(fn ($v) => (int) floor($v), $raw);
        $rest = 100 - array_sum($out);
        $frac = $raw;
        foreach ($frac as $k => $v) {
            $frac[$k] = $v - floor($v);
        }
        arsort($frac);
        foreach (array_keys($frac) as $k) {
            if ($rest-- <= 0) {
                break;
            }
            $out[$k]++;
        }

        return $out;
    }

    private function confidence(int $minPlayed): string
    {
        return match (true) {
            $minPlayed < 5 => 'düşük',
            $minPlayed < 12 => 'orta',
            default => 'yüksek',
        };
    }

    private function teamPayload(array $t): array
    {
        return [
            'name' => $t['name'],
            'rank' => $t['rank'],
            'played' => $t['played'],
            'win' => $t['w'],
            'draw' => $t['d'],
            'loss' => $t['l'],
            'goals_for' => $t['gf'],
            'goals_against' => $t['ga'],
            'points' => $t['w'] * 3 + $t['d'],
            'home' => ['played' => $t['home']['p'], 'goals_for' => $t['home']['gf'], 'goals_against' => $t['home']['ga']],
            'away' => ['played' => $t['away']['p'], 'goals_for' => $t['away']['gf'], 'goals_against' => $t['away']['ga']],
        ];
    }

    // ════════════════ Lig verisi (Wikipedia) ════════════════

    /** Sezon başlangıç yılı: Temmuz ve sonrası yeni sezon. */
    private function seasonStartYear(Fixture $fixture): int
    {
        $d = $fixture->kickoff_at ?? now();

        return $d->month >= 7 ? $d->year : $d->year - 1;
    }

    /**
     * Sezonun puan tablosu + sonuç matrisini çekip çözümler (6 saat önbellek).
     * Başarısız çekimler önbelleğe alınmaz; son başarılı veri yedekte tutulur.
     */
    private function leagueData(int $startYear): ?array
    {
        $season = $startYear.'–'.substr((string) ($startYear + 1), -2);
        $key = 'match_insight:league:'.self::VERSION.':'.$startYear;
        $backupKey = $key.':last_good';

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $data = $this->fetchLeague($season);
        if ($data) {
            Cache::put($key, $data, now()->addHours(6));
            Cache::forever($backupKey, $data);

            return $data;
        }

        return Cache::get($backupKey);
    }

    private function fetchLeague(string $season): ?array
    {
        try {
            $res = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'GoztepeTribunleri/1.0 (goztepeaile.randevumcepte.com.tr)'])
                ->get(self::WIKI_API, [
                    'action' => 'parse',
                    'page' => $season.' Süper Lig',
                    'prop' => 'wikitext',
                    'format' => 'json',
                    'formatversion' => 2,
                    'redirects' => 1,
                ]);
            $wiki = $res->ok() ? $res->json('parse.wikitext') : null;
            if (! is_string($wiki) || $wiki === '') {
                return null;
            }

            return $this->parseLeague($wiki, $season);
        } catch (\Throwable $e) {
            Log::warning('MatchInsight lig verisi alınamadı: '.$e->getMessage());

            return null;
        }
    }

    /** Wikitext → takımlar (tablo + iç saha/deplasman ayrımı) ve lig ortalamaları. */
    private function parseLeague(string $wiki, string $season): ?array
    {
        $tableStart = stripos($wiki, '{{#invoke:Sports table');
        if ($tableStart === false) {
            return null;
        }
        $resultsStart = stripos($wiki, '{{#invoke:sports results');
        $table = substr($wiki, $tableStart, $resultsStart !== false && $resultsStart > $tableStart
            ? $resultsStart - $tableStart
            : 20000);

        // Takım adları: | name_GÖZ = [[Göztepe S.K.|Göztepe]]
        preg_match_all('/\|\s*name_([^\s=|]+)\s*=\s*\[\[([^\]]+)\]\]/u', $wiki, $nm, PREG_SET_ORDER);
        $names = [];
        foreach ($nm as $n) {
            if (isset($names[$n[1]])) {
                continue;
            }
            $parts = explode('|', $n[2]);
            $names[$n[1]] = ['name' => trim(end($parts)), 'link' => trim($parts[0])];
        }

        // Sıralama: |team_order = GAL, AME, ...
        $order = [];
        if (preg_match('/team_order\s*=\s*([^\n]+)/u', $table, $om)) {
            $order = array_values(array_filter(array_map('trim', explode(',', $om[1]))));
        }

        preg_match_all('/\|\s*(win|draw|loss|gf|ga)_([^\s=|]+)\s*=\s*(\d+)/u', $table, $st, PREG_SET_ORDER);
        $stats = [];
        foreach ($st as $s) {
            $stats[$s[2]][$s[1]] = (int) $s[3];
        }
        if (count($stats) < 10) {
            return null;
        }

        $teams = [];
        foreach ($stats as $code => $s) {
            $w = $s['win'] ?? 0;
            $d = $s['draw'] ?? 0;
            $l = $s['loss'] ?? 0;
            $rank = array_search($code, $order, true);
            $teams[$code] = [
                'code' => $code,
                'name' => $names[$code]['name'] ?? $code,
                'link' => $names[$code]['link'] ?? $code,
                'rank' => $rank === false ? null : $rank + 1,
                'played' => $w + $d + $l,
                'w' => $w, 'd' => $d, 'l' => $l,
                'gf' => $s['gf'] ?? 0,
                'ga' => $s['ga'] ?? 0,
                'home' => ['p' => 0, 'gf' => 0, 'ga' => 0],
                'away' => ['p' => 0, 'gf' => 0, 'ga' => 0],
            ];
        }

        // Sonuç matrisi: | match_ALA_GÖZ = 2–2 (ev sahibi önce)
        $homeGoals = $awayGoals = $matches = 0;
        if ($resultsStart !== false) {
            preg_match_all('/\|\s*match_([^\s_=|]+)_([^\s=|]+)\s*=\s*(\d+)\s*[–\-−]\s*(\d+)/u',
                substr($wiki, $resultsStart), $rm, PREG_SET_ORDER);
            foreach ($rm as $r) {
                [$hc, $ac, $hg, $ag] = [$r[1], $r[2], (int) $r[3], (int) $r[4]];
                if (! isset($teams[$hc], $teams[$ac])) {
                    continue;
                }
                $teams[$hc]['home']['p']++;
                $teams[$hc]['home']['gf'] += $hg;
                $teams[$hc]['home']['ga'] += $ag;
                $teams[$ac]['away']['p']++;
                $teams[$ac]['away']['gf'] += $ag;
                $teams[$ac]['away']['ga'] += $hg;
                $homeGoals += $hg;
                $awayGoals += $ag;
                $matches++;
            }
        }

        $updated = preg_match('/\|\s*update\s*=\s*([^\n|]+)/u', $table, $um) ? trim($um[1]) : null;

        return [
            'season' => $season,
            'updated' => $updated,
            'matches' => $matches,
            'avg_home' => $matches >= 10 ? $homeGoals / $matches : self::DEFAULT_HOME_GOALS,
            'avg_away' => $matches >= 10 ? $awayGoals / $matches : self::DEFAULT_AWAY_GOALS,
            'teams' => $teams,
        ];
    }

    /** Admin'in yazdığı serbest takım adını lig kaydıyla eşleştirir. */
    private function findTeam(array $teams, string $name): ?array
    {
        $q = $this->norm($name);
        if ($q === '') {
            return null;
        }

        foreach ($teams as $t) {
            if ($this->norm($t['name']) === $q || $this->norm($t['link']) === $q) {
                return $t;
            }
        }
        foreach ($teams as $t) {
            foreach ([$this->norm($t['name']), $this->norm($t['link'])] as $c) {
                if (strlen($c) >= 4 && strlen($q) >= 4 && (str_contains($c, $q) || str_contains($q, $c))) {
                    return $t;
                }
            }
        }
        $best = null;
        $bestPct = 0.0;
        foreach ($teams as $t) {
            similar_text($q, $this->norm($t['name']), $pct);
            if ($pct > $bestPct) {
                [$best, $bestPct] = [$t, $pct];
            }
        }

        return $bestPct >= 75 ? $best : null;
    }

    /** Türkçe karakter + kulüp ekleri (spor, S.K., F.K. ...) temizlenmiş anahtar. */
    private function norm(string $s): string
    {
        $s = strtr($s, ['İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ç' => 'c', 'ç' => 'c', 'Ğ' => 'g', 'ğ' => 'g',
            'Ö' => 'o', 'ö' => 'o', 'Ş' => 's', 'ş' => 's', 'Ü' => 'u', 'ü' => 'u']);
        $s = mb_strtolower($s);
        $s = preg_replace('/\(.*?\)/u', ' ', $s);
        $s = preg_replace('/\b(s\.?\s?f\.?\s?k|s\.?\s?k|f\.?\s?k|j\.?\s?k|a\.?\s?s|football|club)\b\.?/u', ' ', $s);
        $s = preg_replace('/[^a-z0-9]/', '', $s);

        return preg_replace('/spor$/', '', $s);
    }

    // ════════════════ Yorum ════════════════

    /** Kısa, sayıya dayalı öne çıkanlar (kural tabanlı, bedava). */
    private function insights(array $p): array
    {
        $g = $p['goztepe'];
        $o = $p['opponent'];
        $out = [];

        if ($g['rank'] && $o['rank']) {
            $out[] = "Puan durumu: Göztepe {$g['rank']}. ({$g['points']} puan), {$o['name']} {$o['rank']}. ({$o['points']} puan).";
        }

        $gAtt = $g['played'] ? $g['goals_for'] / $g['played'] : 0;
        $oDef = $o['played'] ? $o['goals_against'] / $o['played'] : 0;
        $out[] = sprintf('Göztepe maç başı %s gol atıyor; %s maç başı %s gol yiyor.',
            $this->num($gAtt), $o['name'], $this->num($oDef));

        $oAtt = $o['played'] ? $o['goals_for'] / $o['played'] : 0;
        $gDef = $g['played'] ? $g['goals_against'] / $g['played'] : 0;
        $out[] = sprintf('%s maç başı %s gol atıyor; Göztepe maç başı %s gol yiyor.',
            $o['name'], $this->num($oAtt), $this->num($gDef));

        $s = $p['likely_scores'][0] ?? null;
        if ($s) {
            $out[] = "En olası skor {$s['home']}-{$s['away']} (%{$s['percent']}).";
        }

        $out[] = $p['over_25'] >= 55
            ? "Gollü maç beklentisi yüksek: 2,5 üst ihtimali %{$p['over_25']}."
            : ($p['over_25'] <= 40
                ? "Az gollü maç beklentisi: 2,5 üst ihtimali yalnızca %{$p['over_25']}."
                : "Gol beklentisi orta: 2,5 üst ihtimali %{$p['over_25']}.");

        return $out;
    }

    /** Önizleme metni: önce önbellek, sonra Haiku, olmazsa kural metni. */
    private function previewText(Fixture $fixture, array $p): array
    {
        $fallback = ['text' => $this->ruleText($fixture, $p), 'by' => 'kural'];

        $ai = app(AiTextService::class);
        if (! $ai->available()) {
            return $fallback;
        }

        $key = 'match_insight:preview:'.self::VERSION.':'.$fixture->id.':'
            .md5(json_encode($p['probabilities']).$p['source']['updated'].$fixture->crowd_summary_hash);

        $cached = Cache::get($key);
        if ($cached) {
            return $cached;
        }

        $text = $ai->write($this->previewSystemPrompt(), "Maç verisi (JSON):\n".$this->previewFacts($fixture, $p));
        if (! $text) {
            return $fallback;
        }

        $out = ['text' => $text, 'by' => 'ai'];
        Cache::put($key, $out, now()->addHours(12));

        return $out;
    }

    private function previewFacts(Fixture $fixture, array $p): string
    {
        return json_encode([
            'mac' => ($fixture->is_home ? 'Göztepe - '.$fixture->opponent : $fixture->opponent.' - Göztepe'),
            'goztepe_ic_saha_mi' => $fixture->is_home,
            'organizasyon' => $fixture->competition,
            'olasiliklar_yuzde' => $p['probabilities'],
            'beklenen_gol' => $p['expected_goals'],
            'olasi_skorlar_ev_dep' => $p['likely_scores'],
            'ust_2_5_yuzde' => $p['over_25'],
            'karsilikli_gol_yuzde' => $p['btts'],
            'goztepe' => $p['goztepe'],
            'rakip' => $p['opponent'],
            // Üyelerin tahmin gerekçelerinden çıkarılan, DOĞRULANMAMIŞ notlar
            'tribun_notlari' => $fixture->crowd_summary,
        ], JSON_UNESCAPED_UNICODE);
    }

    private function previewSystemPrompt(): string
    {
        return 'Göztepe taraftar uygulamasında maç önizlemesi yazıyorsun. Okurlar Göztepe tribün üyeleri. '
            .'Sana verilen istatistik ve model olasılıklarına dayanarak 3-4 cümlelik, akıcı Türkçe bir önizleme yaz. '
            .'Sıcak, tribüne yakın ama abartısız bir dil kullan; sayıları doğru aktar, verilmeyen bilgi uydurma '
            .'(sakatlık, kadro, hoca sözü gibi). Bunun bir olasılık tahmini olduğunu hissettir. '
            .'"tribun_notlari" doluysa bunlar üyelerin doğrulanmamış iddialarıdır; en fazla bir cümleyle '
            .'"tribünden gelen notlara göre" diyerek an, kesin bilgi gibi sunma. '
            .'Bahis, iddaa, oran, kupon, kazanç gibi kelimeler ve bahis tavsiyesi kesinlikle yok. '
            .'Başlık, madde işareti veya emoji kullanma; yalnız düz paragraf yaz.';
    }

    /** AI yoksa: sayılardan kurulan sade Türkçe önizleme. */
    private function ruleText(Fixture $fixture, array $p): string
    {
        $pr = $p['probabilities'];
        $o = $p['opponent'];
        $where = $fixture->is_home ? 'evimizde' : 'deplasmanda';

        $open = match (true) {
            $pr['goztepe_win'] >= 50 => "Göztepe, {$where} {$o['name']} karşısında kağıt üzerinde favori.",
            $pr['opponent_win'] >= 50 => "{$o['name']} kağıt üzerinde önde; Göztepe için {$where} zorlu bir sınav.",
            default => "Göztepe ile {$o['name']} arasında {$where} dengeli bir maç bekleniyor.",
        };

        $s = $p['likely_scores'][0] ?? null;
        $score = $s ? " Model en olası skoru {$s['home']}-{$s['away']} olarak görüyor." : '';

        return $open
            ." Modelimize göre Göztepe'nin kazanma ihtimali %{$pr['goztepe_win']}, beraberlik %{$pr['draw']}, "
            ."{$o['name']} galibiyeti %{$pr['opponent_win']}."
            .$score
            .' Beklenen gol: Göztepe '.$this->num($p['expected_goals']['goztepe'])
            .', '.$o['name'].' '.$this->num($p['expected_goals']['opponent']).'.'
            .($fixture->crowd_summary ? ' Tribünden gelen notlar: '.$fixture->crowd_summary : '');
    }

    private function num(float $v): string
    {
        return number_format($v, 1, ',', '');
    }
}
