<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memorable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Unutulmazlar yönetimi. Mobil admin formu düz alanlarla çalıştığı için:
 *  - istatistikler: her satır "Etiket: değer"
 *  - galeri: her satır "adres | açıklama" (satırı silmek fotoğrafı galeriden çıkarır)
 *  - gallery_add_1..3: yüklenen fotoğraflar galerinin sonuna eklenir
 */
class MemorableController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => Memorable::orderBy('sort')->orderBy('id')->get()->map(fn ($m) => $this->payload($m))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $m = Memorable::create($this->data($request, null));

        return response()->json(['message' => 'Unutulmaz eklendi.', 'item' => $this->payload($m)], 201);
    }

    public function update(Request $request, Memorable $memorable): JsonResponse
    {
        $memorable->update($this->data($request, $memorable));

        return response()->json(['message' => 'Unutulmaz güncellendi.', 'item' => $this->payload($memorable)]);
    }

    public function destroy(Memorable $memorable): JsonResponse
    {
        $memorable->delete();

        return response()->json(['message' => 'Unutulmaz silindi.']);
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
            'gallery_text' => ['nullable', 'string', 'max:10000'],
            'cover' => ['nullable', 'image', 'max:6144'],
            'gallery_add_1' => ['nullable', 'image', 'max:6144'],
            'gallery_add_2' => ['nullable', 'image', 'max:6144'],
            'gallery_add_3' => ['nullable', 'image', 'max:6144'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
        ];

        $gallery = $request->has('gallery_text')
            ? $this->parseGallery($v['gallery_text'] ?? '')
            : ($current?->gallery ?? []);
        foreach (['gallery_add_1', 'gallery_add_2', 'gallery_add_3'] as $f) {
            if ($request->hasFile($f)) {
                $gallery[] = ['url' => $request->file($f)->store('memorables', 'uploads'), 'caption' => null];
            }
        }
        $data['gallery'] = $gallery;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('memorables', 'uploads');
        }

        return $data;
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

    /** "adres | açıklama" satırları → [{url, caption}] (yükleme yolu da kabul) */
    private function parseGallery(string $text): array
    {
        $uploads = rtrim(asset('uploads'), '/').'/';

        return collect(preg_split('/\r?\n/', $text))
            ->map(fn ($l) => array_map('trim', explode('|', $l, 2)))
            ->filter(fn ($p) => ($p[0] ?? '') !== '')
            ->map(fn ($p) => [
                // Kendi sunucumuzdaki yüklemeyi yeniden göreli yola çevir (alan adı değişirse bozulmasın)
                'url' => str_starts_with($p[0], $uploads) ? substr($p[0], strlen($uploads)) : $p[0],
                'caption' => ($p[1] ?? '') !== '' ? $p[1] : null,
            ])
            ->values()->all();
    }

    private function payload(Memorable $m): array
    {
        return [
            'id' => $m->id,
            'category' => $m->category,
            'category_label' => $m->categoryLabel(),
            'name' => $m->name,
            'subtitle' => $m->subtitle,
            'era' => $m->era,
            'summary' => $m->summary,
            'bio' => $m->bio,
            'cover' => $m->coverUrl(),
            'stats_text' => collect($m->stats ?? [])->map(fn ($s) => "{$s['label']}: {$s['value']}")->join("\n"),
            'gallery_text' => collect($m->photos())->map(fn ($p) => $p['url'].($p['caption'] ? ' | '.$p['caption'] : ''))->join("\n"),
            'photo_count' => count($m->photos()),
            'sort' => $m->sort,
            'is_active' => $m->is_active,
        ];
    }
}
