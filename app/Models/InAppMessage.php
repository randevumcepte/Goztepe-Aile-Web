<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class InAppMessage extends Model
{
    protected $fillable = [
        'type', 'title', 'content', 'media_path', 'cta_label', 'cta_url',
        'segment', 'is_commercial', 'frequency_cap', 'priority',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'segment' => 'array',
            'is_commercial' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(CampaignImpression::class);
    }

    /** Şu an yayında olan kampanyalar. */
    public function scopeLive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderByDesc('priority');
    }
}
