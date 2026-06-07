<?php

namespace App\Enums;

enum StudentVerificationStatus: string
{
    case Beklemede = 'beklemede';    // Belge yüklendi, yönetici incelemesi bekliyor
    case Onayli = 'onayli';          // Yönetici belgeyi e-Devlet'ten doğruladı
    case Reddedildi = 'reddedildi';  // Belge geçersiz / sahte / okunmuyor

    public function label(): string
    {
        return match ($this) {
            self::Beklemede => 'İnceleniyor',
            self::Onayli => 'Onaylandı',
            self::Reddedildi => 'Reddedildi',
        };
    }

    /** Rozet renkleri (Tailwind sınıf parçası). */
    public function badge(): string
    {
        return match ($this) {
            self::Beklemede => 'bg-amber-100 text-amber-700',
            self::Onayli => 'bg-emerald-100 text-emerald-700',
            self::Reddedildi => 'bg-rose-100 text-rose-700',
        };
    }
}
