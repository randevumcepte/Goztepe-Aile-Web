<?php

namespace App\Models;

use App\Enums\MemberCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'user_id', 'member_no', 'category', 'status',
        'joined_at', 'kvkk_consent_at', 'commercial_consent',
    ];

    protected function casts(): array
    {
        return [
            'category' => MemberCategory::class,
            'joined_at' => 'date',
            'kvkk_consent_at' => 'datetime',
            'commercial_consent' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Genel kurulda oy hakkı var mı (yalnız asıl üye). */
    public function hasVote(): bool
    {
        return $this->category instanceof MemberCategory && $this->category->hasVote();
    }
}
