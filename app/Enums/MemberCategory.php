<?php

namespace App\Enums;

enum MemberCategory: string
{
    case Ogrenci = 'ogrenci';
    case Standart = 'standart';
    case Destekci = 'destekci';
    case Asil = 'asil'; // Oy hakkı olan çekirdek

    public function label(): string
    {
        return match ($this) {
            self::Ogrenci => 'Öğrenci',
            self::Standart => 'Standart',
            self::Destekci => 'Destekçi',
            self::Asil => 'Asıl Üye',
        };
    }

    /** Genel kurulda oy hakkı yalnız asıl üyede. */
    public function hasVote(): bool
    {
        return $this === self::Asil;
    }
}
