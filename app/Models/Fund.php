<?php

namespace App\Models;

use App\Enums\FundType;
use App\Enums\TransactionDirection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fund extends Model
{
    protected $fillable = ['name', 'type', 'is_public', 'description'];

    protected function casts(): array
    {
        return [
            'type' => FundType::class,
            'is_public' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** Anlık bakiye: SUM(gelir) - SUM(gider). Asla elle tutulmaz. */
    public function balance(): float
    {
        $gelir = (float) $this->transactions()->where('direction', TransactionDirection::Gelir->value)->sum('amount');
        $gider = (float) $this->transactions()->where('direction', TransactionDirection::Gider->value)->sum('amount');

        return $gelir - $gider;
    }
}
