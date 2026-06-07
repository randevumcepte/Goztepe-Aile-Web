<?php

namespace App\Enums;

enum TransactionDirection: string
{
    case Gelir = 'gelir';
    case Gider = 'gider';

    public function label(): string
    {
        return $this === self::Gelir ? 'Gelir' : 'Gider';
    }
}
