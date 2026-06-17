<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use App\Models\Post;
use App\Services\LedgerService;
use App\Services\TeamBadgeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly LedgerService $ledger,
        private readonly TeamBadgeService $badges,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $member = $user->member;

        $payments = $member
            ? $member->payments()->latest()->limit(20)->get()
            : collect();

        // Toplam katkı (yalnız başarılı ödemeler) ve bu yılki katkı.
        $successful = $payments->where('status', 'basarili');
        $totalContribution = (float) $successful->sum('amount');
        $thisYearContribution = (float) $successful
            ->filter(fn ($p) => $p->created_at?->year === now()->year)
            ->sum('amount');

        // Üyenin kategorisine karşılık gelen plan anahtarı.
        // "Asıl Üye" planlar arasında yok; en üst (vip) avantajlarına sahip kabul edilir.
        $planKey = $member?->category?->value;
        $featurePlanKey = $planKey === 'asil' ? 'vip' : $planKey;

        // Üyelik avantajları (karşılaştırma tablosu) ve üyenin sahip oldukları.
        $features = $featurePlanKey
            ? MembershipFeature::active()->get()
            : collect();

        // Daha üst kategoriye geçiş için öneri (varsa).
        $currentPlan = $planKey ? MembershipPlan::where('key', $planKey)->first() : null;
        $upgradePlans = $currentPlan
            ? MembershipPlan::active()->where('sort', '>', $currentPlan->sort)->get()
            : collect();

        // Yaklaşan etkinlikler / duyurular (maç ve duyuru kategorileri).
        $announcements = Post::published()
            ->whereIn('category', ['duyuru', 'mac'])
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        // Şeffaf kasa özeti (vitrindeki public rakamlar).
        $kasa = $this->ledger->publicSummary()['totals'];

        // Maç günü: sıradaki maç. Logosu eksikse rakip armasını otomatik çek.
        $nextMatch = Fixture::upcoming()->first();
        if ($nextMatch && ! $nextMatch->opponent_logo_path) {
            try {
                if ($badge = $this->badges->badgeUrl($nextMatch->opponent)) {
                    $nextMatch->update(['opponent_logo_path' => $badge]);
                }
            } catch (\Throwable $e) {
                // sessiz geç
            }
        }
        $goztepeBadge = $this->badges->goztepeBadgeUrl();

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return view('member.dashboard', compact(
            'user', 'member', 'payments', 'unreadCount',
            'totalContribution', 'thisYearContribution',
            'features', 'featurePlanKey', 'currentPlan', 'upgradePlans',
            'announcements', 'kasa', 'nextMatch', 'goztepeBadge',
        ));
    }
}
