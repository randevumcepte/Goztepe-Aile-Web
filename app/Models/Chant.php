<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Chant extends Model
{
    protected $fillable = ['title', 'note', 'lyrics', 'audio_path', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function audioUrl(): ?string
    {
        return Memorable::url($this->audio_path);
    }
}
