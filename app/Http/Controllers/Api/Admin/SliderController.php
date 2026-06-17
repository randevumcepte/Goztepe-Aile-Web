<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        $sliders = Slider::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $sliders->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('sliders', 'uploads');
        }
        $slider = Slider::create($data);

        return response()->json([
            'message' => 'Slider eklendi.',
            'item' => $this->payload($slider),
        ], 201);
    }

    public function update(Request $request, Slider $slider): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('sliders', 'uploads');
        }
        $slider->update($data);

        return response()->json([
            'message' => 'Slider güncellendi.',
            'item' => $this->payload($slider),
        ]);
    }

    public function destroy(Slider $slider): JsonResponse
    {
        $slider->delete();

        return response()->json(['message' => 'Slider silindi.']);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function payload(Slider $slider): array
    {
        return [
            'id' => $slider->id,
            'title' => $slider->title,
            'subtitle' => $slider->subtitle,
            'image' => $slider->imageUrl(),
            'cta_label' => $slider->cta_label,
            'cta_url' => $slider->cta_url,
            'sort' => (int) $slider->sort,
            'is_active' => (bool) $slider->is_active,
        ];
    }
}
