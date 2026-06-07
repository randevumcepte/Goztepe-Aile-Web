<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Bildirim merkezi listesi. */
    public function index(Request $request): JsonResponse
    {
        $items = $request->user()->notifications()
            ->where('channel', 'in_app')
            ->latest()
            ->limit(50)
            ->get(['id', 'title', 'body', 'data', 'read_at', 'created_at']);

        return response()->json([
            'unread' => $request->user()->notifications()->whereNull('read_at')->count(),
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $request->user()->notifications()->where('id', $id)->update(['read_at' => now()]);

        return response()->json(['message' => 'Okundu olarak işaretlendi.']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['message' => 'Tümü okundu.']);
    }

    /** Tercih güncelleme (örn. push kapat). */
    public function updatePreference(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string'],
            'channel' => ['required', 'in:in_app,push,sms,email,whatsapp'],
            'enabled' => ['required', 'boolean'],
        ]);

        $request->user()->notificationPreferences()->updateOrCreate(
            ['category' => $data['category'], 'channel' => $data['channel']],
            ['enabled' => $data['enabled']],
        );

        return response()->json(['message' => 'Tercih güncellendi.']);
    }
}
