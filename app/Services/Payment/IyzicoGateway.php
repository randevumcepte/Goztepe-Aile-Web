<?php

namespace App\Services\Payment;

use App\Models\Payment;

/**
 * iyzico entegrasyonu (iskelet).
 *
 * Gerçek kullanım için:
 *   composer require iyzico/iyzipay-php
 * ve .env içine:
 *   IYZICO_API_KEY=...
 *   IYZICO_SECRET_KEY=...
 *   IYZICO_BASE_URL=https://sandbox-api.iyzipay.com   (prod: https://api.iyzipay.com)
 *
 * checkout(): iyzico CheckoutFormInitialize ile ödeme formu/token üretip
 * paymentPageUrl döndürür. verify(): callback'teki token ile
 * CheckoutForm::retrieve çağırıp paymentStatus == 'SUCCESS' kontrol eder.
 */
class IyzicoGateway implements PaymentGateway
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $baseUrl,
    ) {
    }

    public function checkout(Payment $payment): string
    {
        // TODO: iyzipay SDK — CheckoutFormInitialize::create(...)
        // $request->setCallbackUrl(route('odeme.callback'));
        // return $checkoutForm->getPaymentPageUrl();
        throw new \RuntimeException(
            'IyzicoGateway henüz tamamlanmadı. iyzipay-php kurulup checkout() doldurulmalı.'
        );
    }

    public function verify(array $payload): bool
    {
        // TODO: CheckoutForm::retrieve(token) -> getPaymentStatus() === 'SUCCESS'
        return false;
    }
}
