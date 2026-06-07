<?php

namespace App\Services\Payment;

use App\Models\Member;
use App\Models\Payment;
use App\Services\LedgerService;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly LedgerService $ledger,
        private readonly NotificationService $notifications,
    ) {
    }

    /** Bekleyen bir ödeme kaydı oluşturur. */
    public function create(?Member $member, string $purpose, float $amount, string $provider = 'fake'): Payment
    {
        return Payment::create([
            'member_id' => $member?->id,
            'purpose' => $purpose,       // aidat | bagis | satis
            'provider' => $provider,
            'amount' => $amount,
            'status' => 'bekliyor',
        ]);
    }

    /** Ödeme sağlayıcısına yönlendirme URL'i. */
    public function checkoutUrl(Payment $payment): string
    {
        return $this->gateway->checkout($payment);
    }

    /** Sağlayıcı doğrulamasını yapıp ödemeyi tamamlar (idempotent). */
    public function confirm(Payment $payment, array $payload): bool
    {
        if ($payment->status === 'basarili') {
            return true; // zaten işlendi
        }

        if (! $this->gateway->verify($payload)) {
            return false;
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'basarili', 'paid_at' => now()]);

            // Başarılı ödeme -> şeffaf kasaya gelir kaydı
            $this->ledger->recordFromPayment($payment);

            // Üyeye onay bildirimi
            if ($payment->member?->user) {
                $this->notifications->send(
                    $payment->member->user,
                    title: 'Ödeme alındı',
                    body: number_format((float) $payment->amount, 2, ',', '.').' ₺ '.$payment->purpose.' ödemen başarıyla alındı. Teşekkürler!',
                    templateKey: 'odeme_onay',
                    type: 'islemsel',
                );
            }
        });

        return true;
    }
}
