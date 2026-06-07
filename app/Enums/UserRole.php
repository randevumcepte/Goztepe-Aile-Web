<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin'; // Kurucu — her şey
    case Admin = 'admin';            // Dernek yönetimi — operasyon
    case Muhasebe = 'muhasebe';      // Sadece muhasebe modülü
    case Denetci = 'denetci';        // Read-only denetim
    case Uye = 'uye';                // Normal üye

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Süper Admin (Kurucu)',
            self::Admin => 'Yönetim',
            self::Muhasebe => 'Muhasebe',
            self::Denetci => 'Denetçi',
            self::Uye => 'Üye',
        };
    }

    /** Yönetim paneline erişebilen roller. */
    public function isStaff(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin, self::Muhasebe, self::Denetci], true);
    }
}
