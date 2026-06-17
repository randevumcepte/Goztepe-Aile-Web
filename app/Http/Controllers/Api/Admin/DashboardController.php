<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): JsonResponse
    {
        $funds = Fund::all()->map(fn (Fund $f) => [
            'id' => $f->id,
            'name' => $f->name,
            'balance' => (float) $this->ledger->fundBalance($f),
            'is_public' => (bool) $f->is_public,
        ])->values();

        $stats = [
            'members' => Member::count(),
            'active_members' => Member::where('status', 'aktif')->count(),
            'payments_total' => (float) Payment::where('status', 'basarili')->sum('amount'),
            'tx_count' => Transaction::count(),
        ];

        $recent = Transaction::with(['fund', 'invoice'])->latest()->limit(10)->get()
            ->map(fn (Transaction $t) => [
                'id' => $t->id,
                'fund_name' => $t->fund?->name,
                'direction' => $t->direction?->value,
                'category' => $t->category,
                'amount' => (float) $t->amount,
                'occurred_at' => $t->occurred_at?->toDateString(),
                'description' => $t->description,
            ])->values();

        return response()->json([
            'funds' => $funds,
            'stats' => $stats,
            'recent' => $recent,
        ]);
    }
}
