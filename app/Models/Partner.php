<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Partner extends Model
{
    public const CATEGORIES = [
        'kafe' => 'Kafe',
        'restoran' => 'Restoran',
        'giyim' => 'Giyim & Mağaza',
        'berber' => 'Berber & Bakım',
        'spor' => 'Spor & Sağlık',
        'diger' => 'Diğer',
    ];

    protected $fillable = [
        'name', 'category', 'discount_text', 'description', 'conditions', 'address', 'district', 'lat', 'lng',
        'phone', 'hours', 'logo_path', 'cover_path', 'stamp_goal', 'stamp_reward', 'kasa_contribution',
        'code', 'pin_hash', 'status', 'applicant_name', 'applicant_phone', 'applicant_user_id', 'sort',
    ];

    protected $hidden = ['pin_hash'];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'stamp_goal' => 'integer',
            'kasa_contribution' => 'float',
        ];
    }

    protected static function booted(): void
    {
        // Okutma sayfası giriş kodu: kısa, karışmayan harf/rakam
        static::creating(function (Partner $p) {
            if (! $p->code) {
                do {
                    $code = strtoupper(Str::random(6));
                    $code = strtr($code, ['0' => 'X', 'O' => 'Y', '1' => 'Z', 'I' => 'K', 'L' => 'M']);
                } while (self::where('code', $code)->exists());
                $p->code = $code;
            }
        });
    }

    public function visits(): HasMany
    {
        return $this->hasMany(PartnerVisit::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'aktif')->orderBy('sort')->orderBy('name');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Diğer';
    }

    public function hasStampCard(): bool
    {
        return $this->stamp_goal > 0 && $this->stamp_reward;
    }

    /** Üyenin bu işletmedeki damga sayısı (son hediye tesliminden bu yana). */
    public function stampsFor(int $userId): int
    {
        $lastReward = $this->visits()->where('user_id', $userId)->where('type', 'hediye')->max('id') ?? 0;

        return $this->visits()->where('user_id', $userId)->where('stamped', true)->where('id', '>', $lastReward)->count();
    }

    public function logoUrl(): ?string
    {
        return Memorable::url($this->logo_path);
    }

    public function coverUrl(): ?string
    {
        return Memorable::url($this->cover_path);
    }
}
