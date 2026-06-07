<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MembershipFeature extends Model
{
    protected $fillable = ['name', 'values', 'is_active', 'sort'];

    protected function casts(): array
    {
        return [
            'values' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    /** Bir plan için bu avantazın değeri: 'yes' | 'no' | metin. */
    public function valueFor(string $planKey): string
    {
        return $this->values[$planKey] ?? 'no';
    }
}
