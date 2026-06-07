<?php

namespace App\Enums;

enum MemberCategory: string
{
    case Ogrenci = 'ogrenci';
    case Standart = 'standart';
    case Destekci = 'destekci';
    case Vip = 'vip';
    case Asil = 'asil'; // Oy hakkı olan çekirdek — yalnız yönetim atar

    public function label(): string
    {
        return match ($this) {
            self::Ogrenci => 'Öğrenci',
            self::Standart => 'Standart',
            self::Destekci => 'Destekçi',
            self::Vip => 'VIP Üye',
            self::Asil => 'Asıl Üye',
        };
    }

    /** Genel kurulda oy hakkı yalnız asıl üyede. */
    public function hasVote(): bool
    {
        return $this === self::Asil;
    }

    /**
     * Halka açık kayıtta seçilebilen kategoriler.
     * "Asıl Üye" (oy hakkı) buraya dahil DEĞİL — sadece yönetim atar.
     *
     * @return array<int, self>
     */
    public static function publicCases(): array
    {
        return [self::Ogrenci, self::Standart, self::Destekci, self::Vip];
    }
}
