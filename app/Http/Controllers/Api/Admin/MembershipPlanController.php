<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = MembershipPlan::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $plans->map(fn (MembershipPlan $m) => $this->payload($m))->values(),
        ]);
    }

    public function update(Request $request, MembershipPlan $plan): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:120'],
            'card_features' => ['nullable', 'string'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Satır satır girilen kart maddelerini diziye çevir
        $features = collect(preg_split('/\r\n|\r|\n/', (string) ($data['card_features'] ?? '')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();

        $plan->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'card_features' => $features,
            'sort' => $data['sort'] ?? 0,
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Tek "popüler" plan olsun
        if ($request->boolean('is_popular')) {
            MembershipPlan::where('id', '!=', $plan->id)->update(['is_popular' => false]);
        }

        return response()->json([
            'message' => 'Plan güncellendi.',
            'item' => $this->payload($plan),
        ]);
    }

    private function payload(MembershipPlan $m): array
    {
        return [
            'id' => $m->id,
            'key' => $m->key,
            'name' => $m->name,
            'price' => $m->price,
            'description' => $m->description,
            'card_features' => $m->card_features ?? [],
            'is_popular' => $m->is_popular,
            'is_active' => $m->is_active,
            'sort' => $m->sort,
        ];
    }
}
