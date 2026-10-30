<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Services\PartnerProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

/**
 * Anlaşmalı işletmenin okutma sayfası (/isletme): uygulama gerektirmez, telefon tarayıcısında açılır.
 * Giriş: işletme kodu + PIN (admin panelinden verilir).
 */
class PartnerScanController extends Controller
{
    public function __construct(private PartnerProgramService $program) {}

    public function show(Request $request): View
    {
        $partner = $this->partner($request);

        return view('partner.scan', [
            'partner' => $partner,
            'stats' => $partner ? $this->program->stats($partner) : null,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:12'], 'pin' => ['required', 'string', 'max:8']]);

        $key = 'isletme-giris:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 6)) {
            return back()->withErrors(['code' => 'Çok fazla deneme. Birkaç dakika sonra tekrar deneyin.']);
        }

        $partner = Partner::where('code', strtoupper(trim($data['code'])))->first();
        if (! $partner || ! $partner->pin_hash || ! Hash::check($data['pin'], $partner->pin_hash)) {
            RateLimiter::hit($key, 300);

            return back()->withErrors(['code' => 'İşletme kodu ya da PIN hatalı.'])->withInput(['code' => $data['code']]);
        }
        if ($partner->status !== 'aktif') {
            return back()->withErrors(['code' => 'İşletme henüz aktif değil. Tribün yönetimiyle görüşün.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->put('partner_id', $partner->id);

        return redirect('/isletme');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('partner_id');

        return redirect('/isletme');
    }

    public function scan(Request $request): JsonResponse
    {
        $partner = $this->partner($request);
        abort_unless($partner, 401);
        $data = $request->validate(['qr' => ['required', 'string', 'max:120']]);

        return response()->json($this->program->scan($partner, $data['qr']) + ['today' => $this->program->stats($partner)['today']]);
    }

    public function reward(Request $request): JsonResponse
    {
        $partner = $this->partner($request);
        abort_unless($partner, 401);
        $data = $request->validate(['ticket' => ['required', 'string']]);

        return response()->json($this->program->giveReward($partner, $data['ticket']));
    }

    private function partner(Request $request): ?Partner
    {
        $id = $request->session()->get('partner_id');
        $p = $id ? Partner::find($id) : null;

        return $p && $p->status === 'aktif' ? $p : null;
    }
}
