<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memorable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Web paneli: Unutulmazlar (mobil uygulamada Keşfet → Unutulmazlar).
 * Galeri: mevcut fotoğraflar tek tek açıklama/sıra/silme ile yönetilir,
 * çoklu yükleme ve "adres | açıklama" satırlarıyla yeni fotoğraf eklenir.
 */
class MemorableController extends Controller
{
    public function index(): View
    {
        $items = Memorable::orderBy('sort')->orderBy('id')->get();

        return view('admin.memorables.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.memorables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Memorable::create($this->data($request, null));

        return redirect()->route('admin.memorables.index')->with('status', 'Unutulmaz eklendi.');
    }

    public function edit(Memorable $memorable): View
    {
        return view('admin.memorables.edit', compact('memorable'));
    }

    public function update(Request $request, Memorable $memorable): RedirectResponse
    {
        $memorable->update($this->data($request, $memorable));

        return redirect()->route('admin.memorables.index')->with('status', 'Unutulmaz güncellendi.');
    }

    public function destroy(Memorable $memorable): RedirectResponse
    {
        $memorable->delete();

        return back()->with('status', 'Unutulmaz silindi.');
    }

    private function data(Request $request, ?Memorable $current): array
    {
        $v = $request->validate([
            'category' => ['required', Rule::in(array_keys(Memorable::CATEGORIES))],
            'name' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'era' => ['nullable', 'string', 'max:60'],
            'summary' => ['nullable', 'string', 'max:300'],
            'bio' => ['nullable', 'string', 'max:20000'],
            'stats_text' => ['nullable', 'string', 'max:2000'],
            'cover' => ['nullable', 'image', 'max:6144'],
            'cover_remove' => ['nullable', 'boolean'],
            'gallery_captions' => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string', 'max:255'],
            'gallery_delete' => ['nullable', 'array'],
            'gallery_order' => ['nullable', 'array'],
            'gallery_order.*' => ['nullable', 'integer'],
            'gallery_new' => ['nullable', 'array'],
            'gallery_new.*' => ['image', 'max:6144'],
            'gallery_urls' => ['nullable', 'string', 'max:10000'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'gallery_new.*.image' => 'Galeriye yalnızca resim dosyası yüklenebilir.',
            'gallery_new.*.max' => 'Her galeri fotoğrafı en fazla 6 MB olabilir.',
        ], [
            'category' => 'kategori',
            'name' => 'ad / başlık',
            'subtitle' => 'alt başlık',
            'era' => 'dönem',
            'summary' => 'kart özeti',
            'bio' => 'hikâye',
            'stats_text' => 'istatistikler',
            'cover' => 'kapak fotoğrafı',
            'gallery_urls' => 'adresle eklenen fotoğraflar',
            'sort' => 'sıra',
        ]);

        $data = [
            'category' => $v['category'],
            'name' => $v['name'],
            'subtitle' => $v['subtitle'] ?? null,
            'era' => $v['era'] ?? null,
            'summary' => $v['summary'] ?? null,
            'bio' => $v['bio'] ?? null,
            'stats' => $this->parseStats($v['stats_text'] ?? ''),
            'sort' => $v['sort'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'gallery' => $this->buildGallery($request, $current),
        ];

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('memorables', 'uploads');
        } elseif ($request->boolean('cover_remove')) {
            $data['cover_path'] = null;
        }

        return $data;
    }

    /** Mevcut galeri (sil/açıklama/sıra) + yeni yüklemeler + adresle eklenenler. */
    private function buildGallery(Request $request, ?Memorable $current): array
    {
        $captions = (array) $request->input('gallery_captions', []);
        $deletes = (array) $request->input('gallery_delete', []);
        $orders = (array) $request->input('gallery_order', []);

        $gallery = collect($current?->gallery ?? [])
            ->filter(fn ($p) => ! empty($p['url']))
            ->map(fn ($p, $i) => [
                'i' => $i,
                'order' => is_numeric($orders[$i] ?? null) ? (int) $orders[$i] : $i,
                'url' => $p['url'], // tam adresler ve göreli yollar olduğu gibi kalır
                'caption' => array_key_exists($i, $captions)
                    ? (trim((string) $captions[$i]) !== '' ? trim((string) $captions[$i]) : null)
                    : ($p['caption'] ?? null),
            ])
            ->reject(fn ($p) => ! empty($deletes[$p['i']]))
            ->sortBy([['order', 'asc'], ['i', 'asc']])
            ->map(fn ($p) => ['url' => $p['url'], 'caption' => $p['caption']])
            ->values()->all();

        foreach ((array) $request->file('gallery_new', []) as $file) {
            if ($file && $file->isValid()) {
                $gallery[] = ['url' => $file->store('memorables', 'uploads'), 'caption' => null];
            }
        }

        return array_merge($gallery, $this->parseUrls((string) $request->input('gallery_urls', '')));
    }

    /** "Maç: 46" satırları → [{label, value}] */
    private function parseStats(string $text): array
    {
        return collect(preg_split('/\r?\n/', $text))
            ->map(fn ($l) => array_map('trim', explode(':', $l, 2)))
            ->filter(fn ($p) => count($p) === 2 && $p[0] !== '' && $p[1] !== '')
            ->map(fn ($p) => ['label' => $p[0], 'value' => $p[1]])
            ->values()->all();
    }

    /** "adres | açıklama" satırları → [{url, caption}] */
    private function parseUrls(string $text): array
    {
        $uploads = rtrim(asset('uploads'), '/').'/';

        return collect(preg_split('/\r?\n/', $text))
            ->map(fn ($l) => array_map('trim', explode('|', $l, 2)))
            ->filter(fn ($p) => ($p[0] ?? '') !== '')
            ->map(fn ($p) => [
                // Kendi sunucumuzdaki yüklemeyi göreli yola çevir (alan adı değişirse bozulmasın)
                'url' => str_starts_with($p[0], $uploads) ? substr($p[0], strlen($uploads)) : $p[0],
                'caption' => ($p[1] ?? '') !== '' ? $p[1] : null,
            ])
            ->values()->all();
    }
}
