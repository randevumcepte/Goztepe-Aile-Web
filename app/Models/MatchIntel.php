<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchIntel extends Model
{
    protected $table = 'match_intel';

    protected $fillable = ['user_id', 'fixture_id', 'answers'];

    protected function casts(): array
    {
        return ['answers' => 'array'];
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
