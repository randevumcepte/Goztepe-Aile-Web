<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionDirection;
use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Models\Fund;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with(['fund', 'invoice'])->latest()->paginate(25);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        return view('admin.transactions.create', [
            'funds' => Fund::all(),
            'directions' => TransactionDirection::cases(),
            'visibilities' => Visibility::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fund_id' => ['required', 'exists:funds,id'],
            'direction' => ['required', 'in:gelir,gider'],
            'category' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'occurred_at' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,members,admin'],
            // Fatura (opsiyonel — gider için önerilir)
            'invoice_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'supplier_masked' => ['nullable', 'string', 'max:120'],
            'supplier_full' => ['nullable', 'string', 'max:160'],
        ]);

        $invoiceId = null;
        if ($request->hasFile('invoice_file') || $request->filled('supplier_masked')) {
            $path = $request->hasFile('invoice_file')
                ? $request->file('invoice_file')->store('invoices', 'uploads')
                : null;

            $invoiceId = Invoice::create([
                'file_path' => $path,
                'supplier_masked' => $data['supplier_masked'] ?? null,
                'supplier_full' => $data['supplier_full'] ?? null,
                'amount' => $data['amount'],
                'issued_at' => $data['occurred_at'],
                'is_public' => $data['visibility'] === 'public',
            ])->id;
        }

        Transaction::create([
            'fund_id' => $data['fund_id'],
            'direction' => $data['direction'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'occurred_at' => $data['occurred_at'],
            'description' => $data['description'] ?? null,
            'visibility' => $data['visibility'],
            'invoice_id' => $invoiceId,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.transactions.index')->with('status', 'Kayıt eklendi ve şeffaf kasaya işlendi.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('status', 'Kayıt silindi.');
    }
}
