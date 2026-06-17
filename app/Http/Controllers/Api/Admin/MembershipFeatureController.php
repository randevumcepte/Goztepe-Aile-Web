<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipFeatureController extends Controller
{
    public function index(): JsonResponse
    {
        $features = MembershipFeature::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $features->map(fn (MembershipFeature $m) => $this->payload($m))->values(),
            'plans' => MembershipPlan::active()->get()->map(fn (MembershipPlan $p) => [
                'key' => $p->key,
                'name' => $p->name,
            ])->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $feature = MembershipFeature::create($this->validateData($request));

        return response()->json([
            'message' => 'Avantaj eklendi.',
            'item' => $this->payload($feature),
        ], 201);
    }

    public function update(Request $request, MembershipFeature $feature): JsonResponse
    {
        $feature->update($this->validateData($request));

        return response()->json([
            'message' => 'Avantaj güncellendi.',
            'item' => $this->payload($feature),
        ]);
    }

    public function destroy(MembershipFeature $feature): JsonResponse
    {
        $feature->delete();

        return response()->json([
            'message' => 'Avantaj silindi.',
        ]);
    }

    private function validateData(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'values' => ['nullable', 'array'],
        ]);

        // Her plan için girilen değeri normalize et: var / yok / metin
        $values = [];
        foreach ((array) $request->input('values', []) as $key => $raw) {
            $values[$key] = $this->normalize($raw);
        }

        return [
            'name' => $request->string('name'),
            'sort' => (int) $request->input('sort', 0),
            'is_active' => $request->boolean('is_active'),
            'values' => $values,
        ];
    }

    private function normalize(?string $raw): string
    {
        $v = mb_strtolower(trim((string) $raw));

        if (in_array($v, ['', 'yok', '-', 'hayır', 'hayir', 'no', 'x'], true)) {
            return 'no';
        }
        if (in_array($v, ['var', 'evet', 'yes', '✓', '+', 'true', '1'], true)) {
            return 'yes';
        }

        return trim((string) $raw); // örn. "%5"
    }

    private function payload(MembershipFeature $m): array
    {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'values' => $m->values ?? [],
            'is_active' => $m->is_active,
            'sort' => $m->sort,
        ];
    }
}
