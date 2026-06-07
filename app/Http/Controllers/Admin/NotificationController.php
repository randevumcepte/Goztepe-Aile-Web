<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function create(): View
    {
        return view('admin.notifications.create', [
            'categories' => MemberCategory::cases(),
        ]);
    }

    public function send(Request $request): RedirectResponse
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

        return back()->with('status', "Bildirim $count üyeye gönderildi.");
    }
}
