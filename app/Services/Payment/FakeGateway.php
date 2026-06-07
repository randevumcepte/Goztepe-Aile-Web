<?php

namespace App\Services\Payment;

use App\Models\Payment;

/**
 * Geliştirme/test ödeme sağlayıcısı.
 * Gerçek tahsilat yapmaz; kullanıcıyı doğrudan "başarılı" geri dönüş
 * sayfasına yönlendirir. Prod'da IyzicoGateway ile değiştirilir.
 */
class FakeGateway implements PaymentGateway
{
    public function checkout(Payment $payment): string
    {
        return route('odeme.callback', ['payment' => $payment->id, 'status' => 'ok']);
    }

    public function verify(array $payload): bool
    {
        return ($payload['status'] ?? null) === 'ok';
    }
}
