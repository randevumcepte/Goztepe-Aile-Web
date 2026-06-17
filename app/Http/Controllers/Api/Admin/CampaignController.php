<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InAppMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(): JsonResponse
    {
        $campaigns = InAppMessage::withCount('impressions')->latest()->paginate(20);

        return response()->json([
            'items' => $campaigns->getCollection()->map(fn (InAppMessage $m) => $this->payload($m))->values(),
            'current_page' => $campaigns->currentPage(),
            'last_page' => $campaigns->lastPage(),
            'total' => $campaigns->total(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:modal,banner,fullscreen,card'],
            'title' => ['required', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:1000'],
            'media' => ['nullable', 'image', 'max:5120'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'string', 'max:255'], // tam link ya da /panel/bagis gibi iç adres
            'is_commercial' => ['nullable', 'boolean'],
            'frequency_cap' => ['required', 'integer', 'min:1', 'max:100'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        unset($data['media']);
        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('campaigns', 'uploads');
        }

        $campaign = InAppMessage::create([
            ...$data,
            'is_commercial' => $request->boolean('is_commercial'),
            'is_active' => true,
        ]);

        $campaign->loadCount('impressions');

        return response()->json([
            'message' => 'Kampanya oluşturuldu.',
            'item' => $this->payload($campaign),
        ], 201);
    }

    public function toggle(InAppMessage $campaign): JsonResponse
    {
        $campaign->update(['is_active' => ! $campaign->is_active]);

        return response()->json([
            'message' => 'Kampanya durumu güncellendi.',
            'is_active' => $campaign->is_active,
        ]);
    }

    private function payload(InAppMessage $m): array
    {
        return [
            'id' => $m->id,
            'type' => $m->type,
            'title' => $m->title,
            'content' => $m->content,
            'media' => $this->mediaUrl($m->media_path),
            'cta_label' => $m->cta_label,
            'cta_url' => $m->cta_url,
            'is_commercial' => $m->is_commercial,
            'frequency_cap' => $m->frequency_cap,
            'priority' => $m->priority,
            'starts_at' => $m->starts_at?->toIso8601String(),
            'ends_at' => $m->ends_at?->toIso8601String(),
            'is_active' => $m->is_active,
            'impressions_count' => $m->impressions_count ?? 0,
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('uploads/'.$path);
    }
}
