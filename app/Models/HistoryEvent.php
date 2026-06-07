<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HistoryEvent extends Model
{
    protected $fillable = [
        'year', 'title', 'tag', 'description', 'caption', 'image_path',
        'in_timeline', 'in_gallery', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'in_timeline' => 'boolean',
            'in_gallery' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        // Eski tarih klasörü (public/img/tarih/...) ile geriye uyumluluk
        if (str_starts_with($this->image_path, 'img/')) {
            return asset($this->image_path);
        }

        return asset('uploads/'.$this->image_path);
    }
}
