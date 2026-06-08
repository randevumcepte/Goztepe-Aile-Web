<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Legend;
use Database\Seeders\LegendSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegendController extends Controller
{
    public function index(): View
    {
        try {
            if (Legend::query()->doesntExist()) {
                app(LegendSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        $legends = Legend::orderBy('sort')->orderBy('id')->get();

        return view('admin.legends.index', compact('legends'));
    }

    public function create(): View
    {
        return view('admin.legends.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('legends', 'uploads');
        }
        Legend::create($data);

        return redirect()->route('admin.legends.index')->with('status', 'Efsane eklendi.');
    }

    public function edit(Legend $legend): View
    {
        return view('admin.legends.edit', compact('legend'));
    }

    public function update(Request $request, Legend $legend): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('legends', 'uploads');
        }
        $legend->update($data);

        return redirect()->route('admin.legends.index')->with('status', 'Efsane güncellendi.');
    }

    public function destroy(Legend $legend): RedirectResponse
    {
        $legend->delete();

        return back()->with('status', 'Efsane silindi.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:80'],
            'nickname' => ['nullable', 'string', 'max:80'],
            'era' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
