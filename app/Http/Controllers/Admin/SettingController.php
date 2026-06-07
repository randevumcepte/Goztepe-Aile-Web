<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** Yönetilen ayar anahtarları. */
    private array $keys = [
        'site_name', 'slogan', 'topbar_text',
        'phone', 'email', 'address',
        'instagram_url', 'x_url', 'youtube_url',
    ];

    public function edit(): View
    {
        return view('admin.settings.edit', ['s' => Setting::map()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:120'],
            'slogan' => ['nullable', 'string', 'max:160'],
            'topbar_text' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:200'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'x_url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        foreach ($this->keys as $key) {
            Setting::put($key, $data[$key] ?? null);
        }

        // Logo yüklenirse storage'a kaydet ve yolunu sakla
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site', 'uploads');
            Setting::put('logo_path', $path);
        }

        return back()->with('status', 'Site ayarları kaydedildi.');
    }
}
