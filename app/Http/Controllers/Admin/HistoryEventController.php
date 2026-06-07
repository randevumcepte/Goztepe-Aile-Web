<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoryEvent;
use Database\Seeders\HistorySeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryEventController extends Controller
{
    public function index(): View
    {
        // Tablo boşsa varsayılan tarihi otomatik doldur (manuel seed gerekmez)
        try {
            if (HistoryEvent::query()->doesntExist()) {
                app(HistorySeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        $events = HistoryEvent::orderBy('sort')->orderBy('id')->get();

        return view('admin.history.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.history.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('history', 'uploads');
        }
        HistoryEvent::create($data);

        return redirect()->route('admin.history.index')->with('status', 'Tarih kaydı eklendi.');
    }

    public function edit(HistoryEvent $history): View
    {
        return view('admin.history.edit', ['event' => $history]);
    }

    public function update(Request $request, HistoryEvent $history): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('history', 'uploads');
        }
        $history->update($data);

        return redirect()->route('admin.history.index')->with('status', 'Tarih kaydı güncellendi.');
    }

    public function destroy(HistoryEvent $history): RedirectResponse
    {
        $history->delete();

        return back()->with('status', 'Tarih kaydı silindi.');
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
}
