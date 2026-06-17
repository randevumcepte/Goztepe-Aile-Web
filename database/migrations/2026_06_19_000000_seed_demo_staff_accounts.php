<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

/**
 * Süper Admin test hesabını canlıda da garanti eder.
 * Deploy yalnız migration çalıştırdığı için seeder yerine migration ile ekliyoruz.
 * Giriş: superadmin@goztepe.test / Test1234
 *
 * İdempotent — tekrar çalışsa da veriyi bozmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@goztepe.test'],
            [
                'name' => 'Süper Admin',
                'password' => Hash::make('Test1234'),
                'role' => UserRole::SuperAdmin,
            ],
        );
    }

    public function down(): void
    {
        User::where('email', 'superadmin@goztepe.test')->delete();
    }
};
