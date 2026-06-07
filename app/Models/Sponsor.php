<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = ['name', 'logo_path', 'url', 'tier', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return str_starts_with($this->logo_path, 'http')
            ? $this->logo_path
            : asset('uploads/'.$this->logo_path);
    }

    public function tierLabel(): string
    {
        return match ($this->tier) {
            'ana' => 'Ana Sponsor',
            'resmi' => 'Resmi Sponsor',
            default => 'Destekçi',
        };
    }
}
