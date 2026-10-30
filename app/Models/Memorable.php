<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Memorable extends Model
{
    public const CATEGORIES = [
        'futbolcu' => 'Futbolcu',
        'teknik_direktor' => 'Teknik Direktör',
        'mac' => 'Unutulmaz Maç',
        'an' => 'Tarihi An',
        'diger' => 'Diğer',
    ];

    protected $fillable = [
        'category', 'name', 'subtitle', 'era', 'summary', 'bio', 'stats', 'cover_path', 'gallery', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'gallery' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Diğer';
    }

    /** Yükleme yolu ya da tam adres → herkese açık adres. */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset('uploads/'.$path);
    }

    /** Galeri, adresleri çözülmüş halde: [{url, caption}] */
    public function photos(): array
    {
        return collect($this->gallery ?? [])
            ->filter(fn ($p) => ! empty($p['url']))
            ->map(fn ($p) => ['url' => self::url($p['url']), 'caption' => $p['caption'] ?? null])
            ->values()
            ->all();
    }

    /** Kapak yoksa galerinin ilk fotoğrafı. */
    public function coverUrl(): ?string
    {
        return self::url($this->cover_path) ?? ($this->photos()[0]['url'] ?? null);
    }
}
