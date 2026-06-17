<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index(): JsonResponse
    {
        $sponsors = Sponsor::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $sponsors->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('sponsors', 'uploads');
        }
        $sponsor = Sponsor::create($data);

        return response()->json([
            'message' => 'Sponsor eklendi.',
            'item' => $this->payload($sponsor),
        ], 201);
    }

    public function update(Request $request, Sponsor $sponsor): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('sponsors', 'uploads');
        }
        $sponsor->update($data);

        return response()->json([
            'message' => 'Sponsor güncellendi.',
            'item' => $this->payload($sponsor),
        ]);
    }

    public function destroy(Sponsor $sponsor): JsonResponse
    {
        $sponsor->delete();

        return response()->json(['message' => 'Sponsor silindi.']);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:255'],
            'tier' => ['required', 'in:ana,resmi,destekci'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function payload(Sponsor $sponsor): array
    {
        return [
            'id' => $sponsor->id,
            'name' => $sponsor->name,
            'url' => $sponsor->url,
            'tier' => $sponsor->tier,
            'tier_label' => $sponsor->tierLabel(),
            'logo' => $sponsor->logoUrl(),
            'sort' => (int) $sponsor->sort,
            'is_active' => (bool) $sponsor->is_active,
        ];
    }
}
