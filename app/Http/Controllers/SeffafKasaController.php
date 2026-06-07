<?php

namespace App\Http\Controllers;

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

        return view('seffaf-kasa', $summary);
    }
}
