<?php

namespace App\Services\Payment;

use App\Models\Payment;

interface PaymentGateway
{
    /**
     * Ödemeyi başlatır ve kullanıcının yönlendirileceği URL'i döner
     * (iyzico ödeme sayfası, ya da geliştirmede sahte başarı sayfası).
     */
    public function checkout(Payment $payment): string;

    /**
     * Sağlayıcıdan gelen geri dönüş/webhook verisini doğrular.
     * Ödeme başarılıysa true döner.
     */
    public function verify(array $payload): bool;
}
