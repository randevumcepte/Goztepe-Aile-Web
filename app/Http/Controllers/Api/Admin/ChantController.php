<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Bestelerimiz yönetimi: ses dosyası yükle ya da bir ses adresi gir. */
class ChantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'items' => Chant::orderBy('sort')->orderBy('id')->get()->map(fn ($c) => $this->payload($c))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $c = Chant::create($this->data($request));

        return response()->json(['message' => 'Beste eklendi.', 'item' => $this->payload($c)], 201);
    }

    public function update(Request $request, Chant $chant): JsonResponse
    {
        $chant->update($this->data($request));

        return response()->json(['message' => 'Beste güncellendi.', 'item' => $this->payload($chant)]);
    }

    public function destroy(Chant $chant): JsonResponse
    {
        $chant->delete();

        return response()->json(['message' => 'Beste silindi.']);
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
            'sort' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
        } elseif (! empty($v['audio_url'])) {
            $data['audio_path'] = $v['audio_url'];
        }

        return $data;
    }

    private function payload(Chant $c): array
    {
        return [
            'id' => $c->id,
            'title' => $c->title,
            'note' => $c->note,
            'lyrics' => $c->lyrics,
            'audio_url' => $c->audioUrl(),
            'has_audio' => (bool) $c->audio_path,
            'sort' => $c->sort,
            'is_active' => $c->is_active,
        ];
    }
}
