<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
    }

    public function showAidat(Request $request): View
    {
        return view('member.aidat', ['amount' => $this->aidatAmount($request)]);
    }

    public function showBagis(): View
    {
        return view('member.bagis');
    }

    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'purpose' => ['required', 'in:aidat,bagis'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $payment = $this->payments->create(
            member: $request->user()->member,
            purpose: $data['purpose'],
            amount: (float) $data['amount'],
            provider: config('services.payment.driver', 'fake'),
        );

        return redirect()->away($this->payments->checkoutUrl($payment));
    }

    /** Ödeme sağlayıcısından geri dönüş. */
    public function callback(Request $request, Payment $payment): RedirectResponse
    {
        $ok = $this->payments->confirm($payment, $request->all());

        return redirect()->route('uye.dashboard')->with(
            'status',
            $ok ? 'Ödemen başarıyla alındı, teşekkürler!' : 'Ödeme tamamlanamadı, lütfen tekrar dene.',
        );
    }

    /** Aidat tutarı (kategoriye göre — örnek değerler). */
    private function aidatAmount(Request $request): float
    {
        return match ($request->user()->member?->category?->value) {
            'ogrenci' => 250.0,
            'destekci' => 1000.0,
            'vip' => 2500.0,
            'asil' => 1500.0,
            default => 500.0, // standart
        };
    }
}
