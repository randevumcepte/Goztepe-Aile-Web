<?php

use App\Enums\MemberCategory;
use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

/**
 * Test üye hesabını canlıda da garanti eder (deploy yalnız migration çalıştırdığı için).
 * Giriş: uye@goztepe.test / Test1234
 * İdempotent — tekrar çalışsa da veriyi bozmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'uye@goztepe.test'],
            [
                'name' => 'Test Üye',
                'password' => Hash::make('Test1234'),
                'role' => UserRole::Uye,
            ],
        );

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

    public function down(): void
    {
        User::where('email', 'uye@goztepe.test')->delete();
    }
};
