<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerVisit;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

/**
 * Göztepe Ayrıcalık: dijital üye kartı (dönen QR), işletmede okutma, damga kartı, kademeler.
 *
 * QR: "GT1.{kullanıcı}.{pencere}.{imza}" — 30 sn'lik pencere, uygulama anahtarıyla HMAC.
 * Bir önceki pencere de kabul edilir (≈1 dk), böylece ekran görüntüsü başkasına verilip kullanılamaz.
 */
class PartnerProgramService
{
    private const WINDOW = 30;

    /** Aynı işletmede iki damga arası en az süre (arka arkaya okutup damga toplanmasın). */
    private const STAMP_COOLDOWN_HOURS = 3;

    /** Kademe: son 12 aydaki işletme ziyareti. */
    public const TIERS = [
        ['key' => 'sari', 'label' => 'Sarı Taraftar', 'min' => 0],
        ['key' => 'kirmizi', 'label' => 'Kırmızı Taraftar', 'min' => 10],
        ['key' => 'efsane', 'label' => 'Efsane Taraftar', 'min' => 30],
    ];

    // ════════════════ Dijital kart ════════════════

    public function cardToken(User $user): array
    {
        $window = intdiv(time(), self::WINDOW);

        return [
            'qr' => 'GT1.'.$user->id.'.'.$window.'.'.$this->sign($user->id, $window),
            'expires_in' => self::WINDOW - (time() % self::WINDOW),
        ];
    }

    public function userFromToken(string $token): ?User
    {
        if (! preg_match('/^GT1\.(\d+)\.(\d+)\.([a-f0-9]{20})$/', trim($token), $m)) {
            return null;
        }
        [$uid, $window, $sig] = [(int) $m[1], (int) $m[2], $m[3]];
        $now = intdiv(time(), self::WINDOW);
        if ($window < $now - 1 || $window > $now || ! hash_equals($this->sign($uid, $window), $sig)) {
            return null;
        }

        return User::with('member')->find($uid);
    }

    private function sign(int $uid, int $window): string
    {
        return substr(hash_hmac('sha256', "gt-kart|{$uid}|{$window}", (string) config('app.key')), 0, 20);
    }

    public function tier(User $user): array
    {
        $visits = PartnerVisit::where('user_id', $user->id)->where('type', 'ziyaret')
            ->where('created_at', '>=', now()->subYear())->count();

        $current = self::TIERS[0];
        $next = null;
        foreach (self::TIERS as $t) {
            if ($visits >= $t['min']) {
                $current = $t;
            } elseif (! $next) {
                $next = $t;
            }
        }

        return [
            'key' => $current['key'],
            'label' => $current['label'],
            'visits' => $visits,
            'next_label' => $next['label'] ?? null,
            'next_in' => $next ? $next['min'] - $visits : null,
        ];
    }

    // ════════════════ İşletmede okutma ════════════════

    /** QR okutuldu: üyeliği doğrula, ziyareti kaydet, gerekiyorsa damga ver. */
    public function scan(Partner $partner, string $token): array
    {
        $user = $this->userFromToken($token);
        if (! $user) {
            return ['ok' => false, 'reason' => 'Kart geçersiz ya da süresi dolmuş. Üyeden uygulamadaki kartını açıp tekrar göstermesini isteyin.'];
        }
        if (! $user->member || $user->member->status !== 'aktif') {
            return [
                'ok' => false,
                'reason' => 'Bu kişinin üyeliği aktif değil; indirim uygulanmaz.',
                'name' => $this->maskName($user->name),
            ];
        }

        $recent = PartnerVisit::where('partner_id', $partner->id)->where('user_id', $user->id)
            ->where('stamped', true)->where('created_at', '>=', now()->subHours(self::STAMP_COOLDOWN_HOURS))->exists();
        $stamp = $partner->hasStampCard() && ! $recent;

        PartnerVisit::create(['partner_id' => $partner->id, 'user_id' => $user->id, 'type' => 'ziyaret', 'stamped' => $stamp]);

        $stamps = $partner->hasStampCard() ? $partner->stampsFor($user->id) : null;
        $ready = $partner->hasStampCard() && $stamps >= $partner->stamp_goal;

        return [
            'ok' => true,
            'name' => $this->maskName($user->name),
            'member_no' => $user->member->member_no,
            'tier' => $this->tier($user)['label'],
            'discount' => $partner->discount_text,
            'stamp_given' => $stamp,
            'stamp_note' => $partner->hasStampCard() && ! $stamp ? 'Son '.self::STAMP_COOLDOWN_HOURS.' saatte damga verildi; bu ziyarette damga yok.' : null,
            'stamps' => $stamps,
            'stamp_goal' => $partner->stamp_goal,
            'reward' => $partner->stamp_reward,
            'reward_ready' => $ready,
            // Hediye teslimi için kısa ömürlü bilet (QR süresi dolsa da kasada teslim edilebilsin)
            'reward_ticket' => $ready ? Crypt::encryptString(json_encode(['p' => $partner->id, 'u' => $user->id, 'e' => time() + 600])) : null,
        ];
    }

    /** Damga kartı doldu: hediyeyi teslim et, damgalar sıfırlanır. */
    public function giveReward(Partner $partner, string $ticket): array
    {
        try {
            $t = json_decode(Crypt::decryptString($ticket), true);
        } catch (\Throwable) {
            return ['ok' => false, 'reason' => 'Geçersiz işlem. Kartı yeniden okutun.'];
        }
        if (($t['p'] ?? null) !== $partner->id || ($t['e'] ?? 0) < time()) {
            return ['ok' => false, 'reason' => 'Sürenin dolduğu bir işlem. Kartı yeniden okutun.'];
        }
        if ($partner->stampsFor($t['u']) < $partner->stamp_goal) {
            return ['ok' => false, 'reason' => 'Bu hediye zaten teslim edilmiş.'];
        }

        PartnerVisit::create(['partner_id' => $partner->id, 'user_id' => $t['u'], 'type' => 'hediye', 'stamped' => false]);

        return ['ok' => true, 'message' => 'Hediye teslim edildi: '.$partner->stamp_reward.'. Damga kartı sıfırlandı.'];
    }

    /** İşletme ve admin için dönem özeti. */
    public function stats(Partner $partner, ?\Carbon\Carbon $from = null): array
    {
        $from ??= now()->startOfMonth();
        $q = PartnerVisit::where('partner_id', $partner->id)->where('type', 'ziyaret')->where('created_at', '>=', $from);
        $visits = (clone $q)->count();

        return [
            'visits' => $visits,
            'members' => (clone $q)->distinct('user_id')->count('user_id'),
            'rewards' => PartnerVisit::where('partner_id', $partner->id)->where('type', 'hediye')->where('created_at', '>=', $from)->count(),
            'contribution' => $partner->kasa_contribution ? round($visits * $partner->kasa_contribution, 2) : null,
            'today' => PartnerVisit::where('partner_id', $partner->id)->where('type', 'ziyaret')->whereDate('created_at', today())->count(),
        ];
    }

    private function maskName(string $name): string
    {
        $p = preg_split('/\s+/u', trim($name)) ?: [];

        return count($p) < 2 ? ($p[0] ?? 'Üye') : $p[0].' '.mb_strtoupper(mb_substr(end($p), 0, 1)).'.';
    }
}
