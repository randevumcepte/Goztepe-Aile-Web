<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;

/**
 * Şeffaf Kasa — mobil uygulamanın (Flutter) tükettiği public JSON ucu.
 * Web sayfası ile AYNI LedgerService'i kullanır → tek beyin.
 */
class SeffafKasaController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): JsonResponse
    {
        $summary = $this->ledger->publicSummary();

        return response()->json([
            'totals' => $summary['totals'],
            'funds' => $summary['funds'],
            'transactions' => $summary['transactions']->map(fn ($t) => [
                'id' => $t->id,
                'tarih' => $t->occurred_at?->toDateString(),
                'aciklama' => $t->description,
                'kategori' => $t->category,
                'yon' => $t->direction->value,
                'tutar' => (float) $t->amount,
                'fatura' => $t->invoice ? [
                    'tedarikci' => $t->invoice->supplier_masked,
                    'tutar' => (float) $t->invoice->amount,
                ] : null,
            ])->values(),
        ]);
    }
}
