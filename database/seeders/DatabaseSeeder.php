<?php

namespace Database\Seeders;

use App\Enums\FundType;
use App\Enums\TransactionDirection;
use App\Enums\UserRole;
use App\Enums\Visibility;
use App\Models\Fund;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1) Süper Admin (kurucu) — yazılım/sistem kontrolü burada
        User::query()->updateOrCreate(
            ['email' => 'admin@goztepetribunleri.test'],
            [
                'name' => 'Kurucu',
                'password' => Hash::make('sifre1234'),
                'role' => UserRole::SuperAdmin,
            ],
        );

        // 2) İki kasa: Dernek Fonu (şeffaf) + İktisadi İşletme (gizli)
        $dernekFonu = Fund::query()->updateOrCreate(
            ['type' => FundType::DernekFonu->value],
            [
                'name' => 'Dernek Fonu',
                'is_public' => true,
                'description' => 'Aidat ve bağışların toplandığı, üyeye tamamen açık şeffaf kasa.',
            ],
        );

        Fund::query()->updateOrCreate(
            ['type' => FundType::IktisadiIsletme->value],
            [
                'name' => 'İktisadi İşletme',
                'is_public' => false,
                'description' => 'Sponsorluk ve ürün satışı gibi ticari gelir (üyeye kapalı).',
            ],
        );

        // 3) Örnek faturalı gider + birkaç gelir (sayfa boş görünmesin)
        $fatura = Invoice::query()->create([
            'supplier_masked' => 'B*** Tekstil',
            'supplier_full' => 'Bayrak Tekstil Ltd. Şti.',
            'amount' => 12500.00,
            'issued_at' => Carbon::now()->subDays(10),
            'is_public' => true,
        ]);

        $hareketler = [
            ['direction' => TransactionDirection::Gelir, 'category' => 'aidat', 'amount' => 25000, 'gun' => 30, 'aciklama' => 'Aylık üye aidatları'],
            ['direction' => TransactionDirection::Gelir, 'category' => 'bagis', 'amount' => 8000, 'gun' => 20, 'aciklama' => 'Tribün Fonu bağışları'],
            ['direction' => TransactionDirection::Gider, 'category' => 'koreografi', 'amount' => 12500, 'gun' => 10, 'aciklama' => 'Deplasman koreografisi malzemesi', 'invoice' => $fatura->id],
            ['direction' => TransactionDirection::Gelir, 'category' => 'bagis', 'amount' => 5000, 'gun' => 5, 'aciklama' => 'Genel bağış'],
            ['direction' => TransactionDirection::Gider, 'category' => 'deplasman', 'amount' => 6000, 'gun' => 2, 'aciklama' => 'Deplasman otobüs desteği'],
        ];

        foreach ($hareketler as $h) {
            Transaction::query()->create([
                'fund_id' => $dernekFonu->id,
                'direction' => $h['direction'],
                'category' => $h['category'],
                'amount' => $h['amount'],
                'occurred_at' => Carbon::now()->subDays($h['gun']),
                'description' => $h['aciklama'],
                'visibility' => Visibility::Public,
                'invoice_id' => $h['invoice'] ?? null,
            ]);
        }
    }
}
