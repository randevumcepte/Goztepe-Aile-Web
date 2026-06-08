<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Legend extends Model
{
    protected $fillable = [
        'name', 'role', 'nickname', 'era', 'note', 'bio', 'photo_path', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http')) {
            return $this->photo_path;
        }

        if (str_starts_with($this->photo_path, 'img/')) {
            return asset($this->photo_path);
        }

        return asset('uploads/'.$this->photo_path);
    }
}
