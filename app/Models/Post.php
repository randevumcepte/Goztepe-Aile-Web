<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'category', 'excerpt',
        'body', 'cover_path', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->where(fn ($x) => $x->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /** Kategori etiketi (Türkçe). */
    public function categoryLabel(): string
    {
        return match ($this->category) {
            'mac' => 'Maç',
            'tribun' => 'Tribün',
            'duyuru' => 'Duyuru',
            'basin' => 'Basın',
            default => 'Haber',
        };
    }

    public function coverUrl(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        // Dış URL (örn. demo görseller) doğrudan; yerel yüklemeler storage'dan
        return str_starts_with($this->cover_path, 'http')
            ? $this->cover_path
            : asset('uploads/'.$this->cover_path);
    }
}
