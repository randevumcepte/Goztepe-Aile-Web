<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\LedgerService;
use Illuminate\View\View;

class SeffafKasaController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): View
    {
        $summary = $this->ledger->publicSummary();

        return view('member.seffaf-kasa', $summary);
    }
}
