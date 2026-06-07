<?php

namespace App\Enums;

enum FundType: string
{
    case DernekFonu = 'dernek_fonu';        // Üyeye açık, şeffaf
    case IktisadiIsletme = 'iktisadi_isletme'; // Ticari, gizli

    public function label(): string
    {
        return match ($this) {
            self::DernekFonu => 'Dernek Fonu',
            self::IktisadiIsletme => 'İktisadi İşletme',
        };
    }
}
