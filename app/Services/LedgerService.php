<?php

namespace App\Services;

use App\Enums\FundType;
use App\Enums\TransactionDirection;
use App\Enums\Visibility;
use App\Models\Fund;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Collection;

/**
 * Şeffaf Kasa'nın beyni.
 *
 * Tek doğru kaynak `transactions` tablosudur; bakiye asla elle tutulmaz,
 * her zaman SUM(gelir) - SUM(gider) ile hesaplanır. Bu sayede vitrindeki
 * rakam ile gerçek defterin uyuşmaması imkânsızdır.
 */
class LedgerService
{
    /**
     * Ziyaretçi/üyeye gösterilecek public şeffaf kasa özeti.
     *
     * @return array{
     *   funds: array<int, array<string, mixed>>,
     *   totals: array{gelir: float, gider: float, bakiye: float},
     *   transactions: Collection<int, Transaction>
     * }
     */
    public function publicSummary(int $transactionLimit = 100): array
    {
        $funds = Fund::query()->where('is_public', true)->get();

        $fundData = $funds->map(fn (Fund $fund) => [
            'id' => $fund->id,
            'name' => $fund->name,
            'description' => $fund->description,
            'balance' => $this->fundBalance($fund),
            'gelir' => $this->fundSum($fund, TransactionDirection::Gelir),
            'gider' => $this->fundSum($fund, TransactionDirection::Gider),
        ])->all();

        $fundIds = $funds->pluck('id');

        $transactions = Transaction::query()
            ->public()
            ->whereIn('fund_id', $fundIds)
            ->with(['fund', 'invoice'])
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->limit($transactionLimit)
            ->get();

        $totalGelir = array_sum(array_column($fundData, 'gelir'));
        $totalGider = array_sum(array_column($fundData, 'gider'));

        return [
            'funds' => $fundData,
            'totals' => [
                'gelir' => $totalGelir,
                'gider' => $totalGider,
                'bakiye' => $totalGelir - $totalGider,
            ],
            'transactions' => $transactions,
        ];
    }

    public function fundBalance(Fund $fund): float
    {
        return $this->fundSum($fund, TransactionDirection::Gelir)
            - $this->fundSum($fund, TransactionDirection::Gider);
    }

    /**
     * Başarılı bir ödemeyi şeffaf kasaya gelir olarak işler.
     * Aidat/bağış Dernek Fonu'na yazılır (üyeye açık).
     */
    public function recordFromPayment(Payment $payment): Transaction
    {
        $fund = Fund::query()->where('type', FundType::DernekFonu->value)->firstOrFail();

        $transaction = Transaction::create([
            'fund_id' => $fund->id,
            'direction' => TransactionDirection::Gelir,
            'category' => $payment->purpose, // aidat | bagis
            'amount' => $payment->amount,
            'occurred_at' => now(),
            'description' => ucfirst($payment->purpose).' ödemesi',
            'visibility' => Visibility::Public,
            'member_id' => $payment->member_id,
        ]);

        $payment->update(['transaction_id' => $transaction->id]);

        return $transaction;
    }

    private function fundSum(Fund $fund, TransactionDirection $direction): float
    {
        return (float) $fund->transactions()
            ->where('direction', $direction->value)
            ->sum('amount');
    }
}
