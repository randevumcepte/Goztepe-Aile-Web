<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'categories' => array_map(fn (MemberCategory $c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ], MemberCategory::cases()),
            'types' => [
                ['value' => 'islemsel'],
                ['value' => 'ticari'],
            ],
            'channels' => ['in_app', 'push'],
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'in:islemsel,ticari'],
            'segment_kategori' => ['nullable', 'in:'.implode(',', array_column(MemberCategory::cases(), 'value'))],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['in:in_app,push'],
        ]);

        $segment = ['sadece_uyeler' => true];
        if (! empty($data['segment_kategori'])) {
            $segment['kategori'] = $data['segment_kategori'];
        }

        $count = $this->notifications->sendToSegment(
            segment: $segment,
            title: $data['title'],
            body: $data['body'] ?? null,
            type: $data['type'],
            channels: $data['channels'],
            templateKey: 'admin_manuel',
        );

        return response()->json([
            'message' => "Bildirim $count üyeye gönderildi.",
            'count' => $count,
        ]);
    }
}
