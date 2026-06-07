<?php

namespace App\Services;

use App\Enums\MemberCategory;
use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MembershipService
{
    /**
     * Yeni kullanıcı + üye kaydı oluşturur.
     * Destekçi/standart/öğrenci üye olarak başlar; "asıl üye" (oy hakkı)
     * sonradan yönetim onayıyla yükseltilir.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => UserRole::Uye,
            ]);

            Member::create([
                'user_id' => $user->id,
                'member_no' => $this->generateMemberNo(),
                'category' => $data['category'] ?? MemberCategory::Standart->value,
                'status' => 'aktif',
                'joined_at' => now(),
                'kvkk_consent_at' => ! empty($data['kvkk_consent']) ? now() : null,
                'commercial_consent' => ! empty($data['commercial_consent']),
            ]);

            return $user->load('member');
        });
    }

    /** GT-2026-0001 formatında benzersiz üye numarası. */
    public function generateMemberNo(): string
    {
        $year = now()->year;
        $count = Member::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('GT-%d-%04d', $year, $count);
    }
}
