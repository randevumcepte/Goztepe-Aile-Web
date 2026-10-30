<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Taraftar grupları (mobil admin). */
class FanGroupController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => FanGroup::withCount('users')->orderBy('sort')->orderBy('name')->get()
                ->map(fn ($g) => $this->payload($g))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $g = FanGroup::create(self::data($request));

        return response()->json(['message' => 'Grup eklendi.', 'item' => $this->payload($g->loadCount('users'))], 201);
    }

    public function update(Request $request, FanGroup $fanGroup): JsonResponse
    {
        $fanGroup->update(self::data($request));

        return response()->json(['message' => 'Grup güncellendi.', 'item' => $this->payload($fanGroup->loadCount('users'))]);
    }

    public function destroy(FanGroup $fanGroup): JsonResponse
    {
        $fanGroup->delete(); // üyelerin grubu boşalır (nullOnDelete)

        return response()->json(['message' => 'Grup silindi; üyeleri bağımsız oldu.']);
    }

    /** Web ve mobil admin ortak doğrulama. */
    public static function data(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'tribune_area' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = collect($v)->except('logo')->all();
        $data['sort'] = $v['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('fan-groups', 'uploads');
        }

        return $data;
    }

    private function payload(FanGroup $g): array
    {
        return [
            'id' => $g->id,
            'name' => $g->name,
            'description' => $g->description,
            'tribune_area' => $g->tribune_area,
            'logo' => $g->logoUrl(),
            'member_count' => $g->users_count ?? 0,
            'sort' => $g->sort,
            'is_active' => $g->is_active,
        ];
    }
}
