<?php

namespace App\Models;

use App\Enums\TransactionDirection;
use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'fund_id', 'direction', 'category', 'amount', 'occurred_at',
        'description', 'visibility', 'member_id', 'invoice_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'direction' => TransactionDirection::class,
            'visibility' => Visibility::class,
            'amount' => 'decimal:2',
            'occurred_at' => 'date',
        ];
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** Sadece herkese açık hareketler (ziyaretçi/üye görünümü). */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', Visibility::Public->value);
    }
}
