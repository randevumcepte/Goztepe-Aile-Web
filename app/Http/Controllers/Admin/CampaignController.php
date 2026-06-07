<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = InAppMessage::withCount('impressions')->latest()->paginate(20);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('admin.campaigns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:modal,banner,fullscreen,card'],
            'title' => ['required', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:1000'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'url'],
            'is_commercial' => ['nullable', 'boolean'],
            'frequency_cap' => ['required', 'integer', 'min:1', 'max:100'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        InAppMessage::create([
            ...$data,
            'is_commercial' => $request->boolean('is_commercial'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.campaigns.index')->with('status', 'Kampanya oluşturuldu.');
    }

    public function toggle(InAppMessage $campaign): RedirectResponse
    {
        $campaign->update(['is_active' => ! $campaign->is_active]);

        return back()->with('status', 'Kampanya durumu güncellendi.');
    }
}
