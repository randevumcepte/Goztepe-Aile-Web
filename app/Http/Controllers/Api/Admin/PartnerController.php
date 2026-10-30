<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Services\PartnerProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/** Anlaşmalı işletmeler: ekle/düzenle, başvuru onayı (durum), okutma PIN'i, aylık özet. */
class PartnerController extends Controller
{
    public function __construct(private PartnerProgramService $program) {}

    public function index(): JsonResponse
    {
        $items = Partner::orderByRaw("CASE status WHEN 'beklemede' THEN 0 WHEN 'aktif' THEN 1 ELSE 2 END")
            ->orderBy('sort')->orderBy('name')->get();

        return response()->json(['items' => $items->map(fn ($p) => $this->payload($p))->values()]);
    }

    public function store(Request $request): JsonResponse
    {
        $p = Partner::create($this->data($request));

        return response()->json(['message' => 'İşletme eklendi. Okutma kodu: '.$p->code, 'item' => $this->payload($p)], 201);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $partner->update($this->data($request));

        return response()->json(['message' => 'İşletme güncellendi.', 'item' => $this->payload($partner)]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $partner->delete();

        return response()->json(['message' => 'İşletme silindi.']);
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(Partner::CATEGORIES))],
            'status' => ['required', Rule::in(['beklemede', 'aktif', 'pasif'])],
            'discount_text' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'conditions' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:80'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'hours' => ['nullable', 'string', 'max:120'],
            'stamp_goal' => ['nullable', 'integer', 'min:0', 'max:50'],
            'stamp_reward' => ['nullable', 'string', 'max:120'],
            'kasa_contribution' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'pin' => ['nullable', 'digits_between:4,8'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover' => ['nullable', 'image', 'max:6144'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = collect($v)->except(['pin', 'logo', 'cover'])->all();
        $data['sort'] = $v['sort'] ?? 0;
        $data['stamp_goal'] = ($v['stamp_goal'] ?? 0) ?: null;
        if (! empty($v['pin'])) {
            $data['pin_hash'] = Hash::make($v['pin']);
        }
        foreach (['logo' => 'logo_path', 'cover' => 'cover_path'] as $field => $col) {
            if ($request->hasFile($field)) {
                $data[$col] = $request->file($field)->store('partners', 'uploads');
            }
        }

        return $data;
    }

    private function payload(Partner $p): array
    {
        return $p->only([
            'id', 'name', 'category', 'status', 'discount_text', 'description', 'conditions', 'address', 'district',
            'lat', 'lng', 'phone', 'hours', 'stamp_goal', 'stamp_reward', 'kasa_contribution', 'code', 'sort',
            'applicant_name', 'applicant_phone',
        ]) + [
            'category_label' => $p->categoryLabel(),
            'logo' => $p->logoUrl(),
            'cover' => $p->coverUrl(),
            'has_pin' => (bool) $p->pin_hash,
            'is_demo' => (bool) $p->is_demo,
            'month' => $this->program->stats($p),
            'scan_url' => url('/isletme'),
        ];
    }
}
