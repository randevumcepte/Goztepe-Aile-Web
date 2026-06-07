<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
    }

    /** Mobil: ödeme başlat, ödeme sayfası URL'i dön. */
    public function start(Request $request): JsonResponse
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

        return response()->json([
            'payment_id' => $payment->id,
            'checkout_url' => $this->payments->checkoutUrl($payment),
        ], 201);
    }

    /** Sağlayıcı webhook'u (sunucudan sunucuya doğrulama). */
    public function webhook(Request $request, Payment $payment): JsonResponse
    {
        $ok = $this->payments->confirm($payment, $request->all());

        return response()->json(['ok' => $ok]);
    }
}
