<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Web paneli: Bestelerimiz — ses dosyası yükle ya da bir ses adresi gir. */
class ChantController extends Controller
{
    public function index(): View
    {
        $chants = Chant::orderBy('sort')->orderBy('id')->get();

        return view('admin.chants.index', compact('chants'));
    }

    public function create(): View
    {
        return view('admin.chants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Chant::create($this->data($request));

        return redirect()->route('admin.chants.index')->with('status', 'Beste eklendi.');
    }

    public function edit(Chant $chant): View
    {
        return view('admin.chants.edit', compact('chant'));
    }

    public function update(Request $request, Chant $chant): RedirectResponse
    {
        $chant->update($this->data($request));

        return redirect()->route('admin.chants.index')->with('status', 'Beste güncellendi.');
    }

    public function destroy(Chant $chant): RedirectResponse
    {
        $chant->delete();

        return back()->with('status', 'Beste silindi.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'note' => ['nullable', 'string', 'max:160'],
            'lyrics' => ['nullable', 'string', 'max:10000'],
            // mp3 bazı sunucularda "mpga" olarak tanınır
            'audio' => ['nullable', 'file', 'mimes:mp3,mpga,m4a,mp4,aac,ogg,oga,wav', 'max:20480'],
            'audio_url' => ['nullable', 'url', 'max:500'],
            'audio_remove' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'beste adı',
            'note' => 'not',
            'lyrics' => 'sözler',
            'audio' => 'ses dosyası',
            'audio_url' => 'ses adresi',
            'sort' => 'sıra',
        ]);

        $data = [
            'title' => $v['title'],
            'note' => $v['note'] ?? null,
            'lyrics' => $v['lyrics'] ?? null,
            'sort' => $v['sort'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
        if ($request->hasFile('audio')) {
            $data['audio_path'] = $request->file('audio')->store('chants', 'uploads');
        } elseif ($request->boolean('audio_remove')) {
            $data['audio_path'] = null;
        } elseif (! empty($v['audio_url'])) {
            $data['audio_path'] = $v['audio_url'];
        }

        return $data;
    }
}
