<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerVisit;
use App\Services\PartnerProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function __construct(private PartnerProgramService $program) {}

    /** Taraftar Kartım: dönen QR + kademe. Uygulama ~25 sn'de bir yeniler. */
    public function card(Request $request): JsonResponse
    {
        $user = $request->user()->load('member');
        $m = $user->member;

        return response()->json($this->program->cardToken($user) + [
            'name' => $user->name,
            'member_no' => $m?->member_no,
            'category' => $m?->category?->label(),
            'status' => $m?->status,
            'active' => $m?->status === 'aktif',
            'tier' => $this->program->tier($user),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $uid = $request->user()->id;

        return response()->json([
            'categories' => collect(Partner::CATEGORIES)->map(fn ($l, $k) => ['key' => $k, 'label' => $l])->values(),
            'items' => Partner::active()->get()->map(fn (Partner $p) => $this->card_($p, $uid))->values(),
        ]);
    }

    public function show(Request $request, Partner $partner): JsonResponse
    {
        abort_unless($partner->status === 'aktif', 404);
        $uid = $request->user()->id;

        return response()->json($this->card_($partner, $uid) + [
            'description' => $partner->description,
            'conditions' => $partner->conditions,
            'address' => $partner->address,
            'phone' => $partner->phone,
            'hours' => $partner->hours,
            'lat' => $partner->lat,
            'lng' => $partner->lng,
            'my_visits' => $partner->visits()->where('user_id', $uid)->where('type', 'ziyaret')->count(),
        ]);
    }

    /** Ayrıcalık Cüzdanım: hediye ilerlemesi + nerede ne kullanıldı. */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $visits = PartnerVisit::with('partner')->where('user_id', $user->id)->latest('created_at')->latest('id')->limit(200)->get()
            ->filter(fn ($v) => $v->partner);

        // Damga kartı olan ve üyenin uğradığı işletmeler: hazır hediyeler en üstte
        $rewards = $visits->pluck('partner')->unique('id')->filter(fn (Partner $p) => $p->status === 'aktif' && $p->hasStampCard())
            ->map(function (Partner $p) use ($user) {
                $s = $p->stampsFor($user->id);

                return [
                    'partner_id' => $p->id, 'partner' => $p->name, 'logo' => $p->logoUrl(), 'category_label' => $p->categoryLabel(),
                    'stamps' => min($s, $p->stamp_goal), 'goal' => $p->stamp_goal, 'reward' => $p->stamp_reward, 'ready' => $s >= $p->stamp_goal,
                ];
            })
            ->sortBy(fn ($r) => [$r['ready'] ? 0 : 1, $r['goal'] - $r['stamps']])->values();

        return response()->json([
            'summary' => [
                'visits' => $visits->where('type', 'ziyaret')->count(),
                'partners' => $visits->pluck('partner_id')->unique()->count(),
                'rewards_received' => $visits->where('type', 'hediye')->count(),
                'rewards_ready' => $rewards->where('ready', true)->count(),
                'tier' => $this->program->tier($user),
            ],
            'rewards' => $rewards,
            'history' => $visits->map(fn (PartnerVisit $v) => [
                'date' => $v->created_at->toIso8601String(),
                'partner_id' => $v->partner_id,
                'partner' => $v->partner->name,
                'category_label' => $v->partner->categoryLabel(),
                'type' => $v->type,
                'discount' => $v->type === 'ziyaret' ? $v->partner->discount_text : null,
                'stamped' => $v->stamped,
                'reward' => $v->type === 'hediye' ? $v->partner->stamp_reward : null,
            ])->values(),
        ]);
    }

    /** "İşletmeni ekle": başvuru admin onayına düşer. */
    public function apply(Request $request): JsonResponse
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(Partner::CATEGORIES))],
            'discount_text' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'applicant_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'stamp_goal' => ['nullable', 'integer', 'min:2', 'max:50'],
            'stamp_reward' => ['nullable', 'string', 'max:120'],
        ]);

        Partner::create($v + [
            'status' => 'beklemede',
            'applicant_phone' => $v['phone'],
            'applicant_user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Başvurun alındı! Tribün yönetimi inceleyip seninle iletişime geçecek. 💛❤️'], 201);
    }

    private function card_(Partner $p, int $uid): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'category' => $p->category,
            'category_label' => $p->categoryLabel(),
            'discount_text' => $p->discount_text,
            'district' => $p->district,
            'logo' => $p->logoUrl(),
            'cover' => $p->coverUrl(),
            'stamp_goal' => $p->hasStampCard() ? $p->stamp_goal : null,
            'stamp_reward' => $p->hasStampCard() ? $p->stamp_reward : null,
            'my_stamps' => $p->hasStampCard() ? $p->stampsFor($uid) : null,
        ];
    }
}
