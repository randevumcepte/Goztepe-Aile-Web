<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()
            ->where('channel', 'in_app')
            ->latest()
            ->paginate(20);

        // Görüntülenenleri okundu say
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('member.notifications', compact('notifications'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }
}
