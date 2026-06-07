<?php

namespace Database\Seeders;

use App\Enums\MemberCategory;
use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Her rol için test hesabı oluşturur (tek tek kontrol için).
 * Tüm şifreler: Test1234
 * Çalıştır: php artisan db:seed --class=DemoUsersSeeder --force
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Test1234');

        $hesaplar = [
            ['Süper Admin', 'superadmin@goztepe.test', UserRole::SuperAdmin],
            ['Yönetim', 'admin@goztepe.test', UserRole::Admin],
            ['Muhasebe', 'muhasebe@goztepe.test', UserRole::Muhasebe],
            ['Denetçi', 'denetci@goztepe.test', UserRole::Denetci],
            ['Test Üye', 'uye@goztepe.test', UserRole::Uye],
        ];

        foreach ($hesaplar as [$name, $email, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => $password, 'role' => $role],
            );

            // "uye" rolüne üye kaydı da ekle (panel ve üye kartı için)
            if ($role === UserRole::Uye) {
                Member::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'member_no' => 'GT-2026-9001',
                        'category' => MemberCategory::Standart->value,
                        'status' => 'aktif',
                        'joined_at' => now(),
                        'kvkk_consent_at' => now(),
                        'commercial_consent' => true,
                    ],
                );
            }
        }

        $this->command->info('Demo hesaplar hazır. Tüm şifreler: Test1234');
    }
}
