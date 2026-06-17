<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\TransactionDirection;
use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Models\Fund;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Transaction::with(['fund', 'invoice'])->latest()->paginate(25);

        return response()->json([
            'items' => $transactions->getCollection()->map(fn (Transaction $t) => $this->payload($t))->values(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'total' => $transactions->total(),
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'funds' => Fund::all()->map(fn (Fund $f) => [
                'id' => $f->id,
                'name' => $f->name,
            ])->values(),
            'directions' => array_map(fn (TransactionDirection $d) => [
                'value' => $d->value,
                'label' => $d->label(),
            ], TransactionDirection::cases()),
            'visibilities' => array_map(fn (Visibility $v) => [
                'value' => $v->value,
                'label' => $v->label(),
            ], Visibility::cases()),
        ]);
    }

    public function store(Request $request): JsonResponse
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

        $transaction = Transaction::create([
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

        $transaction->load(['fund', 'invoice']);

        return response()->json([
            'message' => 'Kayıt eklendi ve şeffaf kasaya işlendi.',
            'item' => $this->payload($transaction),
        ], 201);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Kayıt silindi.',
        ]);
    }

    private function payload(Transaction $t): array
    {
        return [
            'id' => $t->id,
            'fund_id' => $t->fund_id,
            'fund_name' => $t->fund?->name,
            'direction' => $t->direction?->value,
            'direction_label' => $t->direction?->label(),
            'category' => $t->category,
            'amount' => (float) $t->amount,
            'occurred_at' => $t->occurred_at?->toDateString(),
            'description' => $t->description,
            'visibility' => $t->visibility?->value,
            'invoice' => $t->invoice ? [
                'supplier_masked' => $t->invoice->supplier_masked,
                'amount' => (float) $t->invoice->amount,
            ] : null,
            'created_at' => $t->created_at?->toIso8601String(),
        ];
    }
}
