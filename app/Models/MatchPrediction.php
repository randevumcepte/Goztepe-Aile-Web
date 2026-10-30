<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchPrediction extends Model
{
    protected $fillable = [
        'user_id', 'fixture_id', 'home_goals', 'away_goals', 'reason', 'factors', 'points', 'beat_ai',
    ];

    protected function casts(): array
    {
        return [
            'factors' => 'array',
            'beat_ai' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }
}
