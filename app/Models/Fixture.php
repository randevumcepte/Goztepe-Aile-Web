<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $fillable = [
        'opponent', 'opponent_logo_path', 'competition', 'is_home',
        'kickoff_at', 'venue', 'broadcast', 'home_score', 'away_score', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_home' => 'boolean',
            'is_active' => 'boolean',
            'kickoff_at' => 'datetime',
        ];
    }

    /** Sıradaki (henüz oynanmamış) maçlar — en yakın önce. */
    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where('kickoff_at', '>=', now()->subHours(3)) // maç günü 3 saat boyunca "sıradaki" kalsın
            ->orderBy('kickoff_at');
    }

    /** Oynanmış maçlar — en yeni önce. */
    public function scopePlayed(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->whereNotNull('home_score')
            ->orderByDesc('kickoff_at');
    }

    public function opponentLogoUrl(): ?string
    {
        if (! $this->opponent_logo_path) {
            return null;
        }

        return str_starts_with($this->opponent_logo_path, 'http')
            ? $this->opponent_logo_path
            : asset('uploads/'.$this->opponent_logo_path);
    }

    /** Ev sahibi / deplasman etiketi. */
    public function homeAwayLabel(): string
    {
        return $this->is_home ? 'Ev Sahibi' : 'Deplasman';
    }

    /** Skor girilmiş mi? */
    public function isPlayed(): bool
    {
        return $this->home_score !== null && $this->away_score !== null;
    }
}
