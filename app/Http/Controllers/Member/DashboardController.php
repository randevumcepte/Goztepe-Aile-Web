<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $member = $user->member;

        $payments = $member
            ? $member->payments()->latest()->limit(20)->get()
            : collect();

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return view('member.dashboard', compact('user', 'member', 'payments', 'unreadCount'));
    }
}
