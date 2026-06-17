<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Legend;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegendController extends Controller
{
    public function index(): JsonResponse
    {
        $legends = Legend::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $legends->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('legends', 'uploads');
        }
        $legend = Legend::create($data);

        return response()->json([
            'message' => 'Efsane eklendi.',
            'item' => $this->payload($legend),
        ], 201);
    }

    public function update(Request $request, Legend $legend): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('legends', 'uploads');
        }
        $legend->update($data);

        return response()->json([
            'message' => 'Efsane güncellendi.',
            'item' => $this->payload($legend),
        ]);
    }

    public function destroy(Legend $legend): JsonResponse
    {
        $legend->delete();

        return response()->json([
            'message' => 'Efsane silindi.',
        ]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:80'],
            'nickname' => ['nullable', 'string', 'max:80'],
            'era' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function payload(Legend $legend): array
    {
        return [
            'id' => $legend->id,
            'name' => $legend->name,
            'role' => $legend->role,
            'nickname' => $legend->nickname,
            'era' => $legend->era,
            'note' => $legend->note,
            'bio' => $legend->bio,
            'photo' => $legend->imageUrl(),
            'sort' => $legend->sort,
            'is_active' => $legend->is_active,
        ];
    }
}
