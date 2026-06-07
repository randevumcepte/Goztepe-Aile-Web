<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\LedgerService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): View
    {
        $funds = Fund::all()->map(fn (Fund $f) => [
            'name' => $f->name,
            'balance' => $this->ledger->fundBalance($f),
            'is_public' => $f->is_public,
        ]);

        $stats = [
            'members' => Member::count(),
            'active_members' => Member::where('status', 'aktif')->count(),
            'payments_total' => (float) Payment::where('status', 'basarili')->sum('amount'),
            'tx_count' => Transaction::count(),
        ];

        $recent = Transaction::with(['fund', 'invoice'])->latest()->limit(10)->get();

        return view('admin.dashboard', compact('funds', 'stats', 'recent'));
    }
}
