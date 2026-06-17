<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoryEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryEventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = HistoryEvent::orderBy('sort')->orderBy('id')->get();

        return response()->json([
            'items' => $events->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('history', 'uploads');
        }
        $event = HistoryEvent::create($data);

        return response()->json([
            'message' => 'Tarih kaydı eklendi.',
            'item' => $this->payload($event),
        ], 201);
    }

    public function update(Request $request, HistoryEvent $history): JsonResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('history', 'uploads');
        }
        $history->update($data);

        return response()->json([
            'message' => 'Tarih kaydı güncellendi.',
            'item' => $this->payload($history),
        ]);
    }

    public function destroy(HistoryEvent $history): JsonResponse
    {
        $history->delete();

        return response()->json([
            'message' => 'Tarih kaydı silindi.',
        ]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:40'],
            'title' => ['required', 'string', 'max:160'],
            'tag' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:1000'],
            'caption' => ['nullable', 'string', 'max:200'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'in_timeline' => ['nullable', 'boolean'],
            'in_gallery' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['in_timeline'] = $request->boolean('in_timeline');
        $data['in_gallery'] = $request->boolean('in_gallery');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function payload(HistoryEvent $event): array
    {
        return [
            'id' => $event->id,
            'year' => $event->year,
            'title' => $event->title,
            'tag' => $event->tag,
            'description' => $event->description,
            'caption' => $event->caption,
            'image' => $event->imageUrl(),
            'in_timeline' => $event->in_timeline,
            'in_gallery' => $event->in_gallery,
            'sort' => $event->sort,
            'is_active' => $event->is_active,
        ];
    }
}
