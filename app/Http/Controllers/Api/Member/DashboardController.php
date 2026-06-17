<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\Post;
use App\Services\LedgerService;
use App\Services\TeamBadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Üye ana ekran özeti (mobil). Web member dashboard ile aynı veriyi sağlar.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly LedgerService $ledger,
        private readonly TeamBadgeService $badges,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $member = $user->member;

        $payments = $member
            ? $member->payments()->latest()->limit(20)->get()
            : collect();

        $successful = $payments->where('status', 'basarili');
        $totalContribution = (float) $successful->sum('amount');
        $thisYearContribution = (float) $successful
            ->filter(fn ($p) => $p->created_at?->year === now()->year)
            ->sum('amount');

        $announcements = Post::published()
            ->whereIn('category', ['duyuru', 'mac'])
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $kasa = $this->ledger->publicSummary()['totals'];

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

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return response()->json([
            'member' => $member ? [
                'member_no' => $member->member_no,
                'category' => $member->category->value,
                'category_label' => $member->category->label(),
                'status' => $member->status,
                'has_vote' => $member->hasVote(),
            ] : null,
            'total_contribution' => $totalContribution,
            'this_year_contribution' => $thisYearContribution,
            'unread_count' => $unreadCount,
            'kasa' => [
                'gelir' => (float) ($kasa['gelir'] ?? 0),
                'gider' => (float) ($kasa['gider'] ?? 0),
                'bakiye' => (float) ($kasa['bakiye'] ?? 0),
            ],
            'next_match' => $nextMatch ? [
                'opponent' => $nextMatch->opponent,
                'opponent_logo' => $nextMatch->opponentLogoUrl(),
                'competition' => $nextMatch->competition,
                'is_home' => $nextMatch->is_home,
                'home_away_label' => $nextMatch->homeAwayLabel(),
                'kickoff_at' => $nextMatch->kickoff_at?->toIso8601String(),
                'venue' => $nextMatch->venue,
                'broadcast' => $nextMatch->broadcast,
            ] : null,
            'goztepe_badge' => $this->badges->goztepeBadgeUrl(),
            'announcements' => $announcements->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'category_label' => $p->categoryLabel(),
                'cover' => $p->coverUrl(),
                'published_at' => $p->published_at?->toIso8601String(),
            ])->values(),
            'recent_payments' => $payments->map(fn ($p) => [
                'id' => $p->id,
                'purpose' => $p->purpose,
                'amount' => (float) $p->amount,
                'status' => $p->status,
                'created_at' => $p->created_at?->toIso8601String(),
            ])->values(),
        ]);
    }
}
