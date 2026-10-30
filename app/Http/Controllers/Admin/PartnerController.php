<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerVisit;
use App\Services\PartnerProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Göztepe Ayrıcalık — anlaşmalı işletmeler web yönetimi (başvuru onayı, PIN, okutma bilgileri, istatistik). */
class PartnerController extends Controller
{
    public function __construct(private PartnerProgramService $program) {}

    public function index(Request $request): View
    {
        $durum = in_array($request->query('durum'), ['basvuru', 'aktif', 'pasif', 'tumu'], true) ? $request->query('durum') : 'tumu';
        $monthStart = now()->startOfMonth();

        $all = Partner::withCount([
            'visits as month_visits' => fn ($q) => $q->where('type', 'ziyaret')->where('created_at', '>=', $monthStart),
        ])
            ->orderByRaw("CASE status WHEN 'beklemede' THEN 0 WHEN 'aktif' THEN 1 ELSE 2 END")
            ->orderBy('sort')->orderBy('name')->get();

        foreach ($all as $p) {
            $p->month_contribution = $p->kasa_contribution ? round($p->month_visits * $p->kasa_contribution, 2) : 0;
        }

        $summary = [
            'active' => $all->where('status', 'aktif')->count(),
            'pending' => $all->where('status', 'beklemede')->count(),
            'visits' => $all->sum('month_visits'),
            'contribution' => $all->sum('month_contribution'),
        ];
        $counts = [
            'tumu' => $all->count(),
            'basvuru' => $summary['pending'],
            'aktif' => $summary['active'],
            'pasif' => $all->where('status', 'pasif')->count(),
        ];

        $pending = $all->where('status', 'beklemede')->values();
        $partners = match ($durum) {
            'basvuru' => collect(),
            'aktif' => $all->where('status', 'aktif')->values(),
            'pasif' => $all->where('status', 'pasif')->values(),
            default => $all->where('status', '!=', 'beklemede')->values(),
        };
        $demoCount = $all->where('is_demo', true)->count();

        return view('admin.partners.index', compact('durum', 'summary', 'counts', 'pending', 'partners', 'demoCount'));
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $partner = Partner::create($this->data($request));

        return redirect()->route('admin.partners.show', $partner)->with('status', 'İşletme eklendi. Okutma kodu: '.$partner->code);
    }

    public function show(Partner $partner): View
    {
        $thisMonth = $this->program->stats($partner, now()->startOfMonth());
        $sixMonths = $this->program->stats($partner, now()->subMonths(5)->startOfMonth());

        $lastFrom = now()->subMonthNoOverflow()->startOfMonth();
        $lastTo = now()->startOfMonth();
        $lastQ = PartnerVisit::where('partner_id', $partner->id)->whereBetween('created_at', [$lastFrom, $lastTo->copy()->subSecond()]);
        $lastVisits = (clone $lastQ)->where('type', 'ziyaret')->count();
        $lastMonth = [
            'visits' => $lastVisits,
            'members' => (clone $lastQ)->where('type', 'ziyaret')->distinct('user_id')->count('user_id'),
            'rewards' => (clone $lastQ)->where('type', 'hediye')->count(),
            'contribution' => $partner->kasa_contribution ? round($lastVisits * $partner->kasa_contribution, 2) : null,
        ];

        // Son 6 ay aylık ziyaret (veritabanından bağımsız: PHP'de grupla)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonthsNoOverflow($i)->startOfMonth();
            $months[$m->format('Y-m')] = ['label' => $m->translatedFormat('M Y'), 'count' => 0];
        }
        PartnerVisit::where('partner_id', $partner->id)->where('type', 'ziyaret')
            ->where('created_at', '>=', now()->subMonthsNoOverflow(5)->startOfMonth())
            ->pluck('created_at')
            ->each(function ($d) use (&$months) {
                $k = $d->format('Y-m');
                if (isset($months[$k])) {
                    $months[$k]['count']++;
                }
            });
        $maxMonth = max(1, max(array_column($months, 'count')));

        $visits = $partner->visits()->with('user.member')->latest('id')->limit(50)->get();

        $scanUrl = url('/isletme');
        $message = "Merhaba, Göztepe Ayrıcalık programına hoş geldiniz!\n"
            ."Telefonunuzdan bu adresi açın: {$scanUrl}\n"
            ."İşletme kodunuz: {$partner->code}\n"
            ."Kodu ve size verilen PIN'i girin, ardından üyenin uygulamadaki Taraftar Kartı QR'ını okutun. "
            .'Sistem üyeliği doğrular, indirimi ve varsa damgayı gösterir.';

        return view('admin.partners.show', compact('partner', 'thisMonth', 'lastMonth', 'sixMonths', 'months', 'maxMonth', 'visits', 'scanUrl', 'message'));
    }

    public function edit(Partner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $partner->update($this->data($request));

        return redirect()->route('admin.partners.show', $partner)->with('status', 'İşletme güncellendi.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('status', 'İşletme silindi.');
    }

    public function destroyDemo(): RedirectResponse
    {
        $ids = Partner::where('is_demo', true)->pluck('id');
        PartnerVisit::whereIn('partner_id', $ids)->delete(); // cascade'e ek güvence (sqlite FK kapalı olabilir)
        $n = Partner::whereIn('id', $ids)->delete();

        return redirect()->route('admin.partners.index')->with('status', $n.' örnek (demo) işletme ve ziyaretleri silindi.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(Partner::CATEGORIES))],
            'status' => ['required', Rule::in(['beklemede', 'aktif', 'pasif'])],
            'discount_text' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'conditions' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:80'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'hours' => ['nullable', 'string', 'max:120'],
            'stamp_goal' => ['nullable', 'integer', 'min:0', 'max:50'],
            'stamp_reward' => ['nullable', 'string', 'max:120'],
            'kasa_contribution' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'pin' => ['nullable', 'digits_between:4,8'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover' => ['nullable', 'image', 'max:6144'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = collect($v)->except(['pin', 'logo', 'cover'])->all();
        $data['sort'] = $v['sort'] ?? 0;
        $data['stamp_goal'] = ($v['stamp_goal'] ?? 0) ?: null;
        if (! empty($v['pin'])) {
            $data['pin_hash'] = Hash::make($v['pin']);
        }
        foreach (['logo' => 'logo_path', 'cover' => 'cover_path'] as $field => $col) {
            if ($request->hasFile($field)) {
                $data[$col] = $request->file($field)->store('partners', 'uploads');
            }
        }

        return $data;
    }
}
