<?php

namespace App\Enums;

enum Visibility: string
{
    case Public = 'public';   // Herkes (ziyaretçi dahil)
    case Members = 'members'; // Sadece giriş yapmış üyeler
    case Admin = 'admin';     // Sadece yönetim/muhasebe/denetçi

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Herkese Açık',
            self::Members => 'Üyelere Açık',
            self::Admin => 'Yönetime Özel',
        };
    }
}
