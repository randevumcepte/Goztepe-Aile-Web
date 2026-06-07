<?php

namespace Database\Seeders;

use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

/**
 * Üyelik planları + avantaj tablosunu mevcut içerikle doldurur (idempotent).
 * Çalıştır: php artisan db:seed --class=MembershipSeeder --force
 */
class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['ogrenci', 'Öğrenci', '250', 'Genç taraftar', false, 1,
                ['Dijital üye kartı', 'Şeffaf kasa erişimi', 'Üyelere özel iletişim grubu', 'Mağaza %5 indirim']],
            ['standart', 'Standart', '500', 'En çok tercih edilen', true, 2,
                ['Öğrencinin tüm hakları', 'Maç biletinde öncelikli satış', 'Deplasman otobüsünde öncelik', 'Mağaza %10 indirim']],
            ['destekci', 'Destekçi', '1.000', 'Daha çok destek ol', false, 3,
                ['Standardın tüm hakları', 'Deplasman biletinde öncelik', 'Koreografi ekibine katılım', 'Sınırlı ürünlere erken erişim', 'Hoş geldin paketi + doğum günü', 'Mağaza %15 indirim']],
            ['vip', 'VIP Üye', '2.500', 'En üst destek', false, 4,
                ['Destekçinin tüm hakları', 'Oyuncu buluşması / imza günü', 'Etkinliklerde özel VIP alan', 'İsim plaketi / onur duvarı', 'Mağaza %20 indirim']],
        ];

        foreach ($plans as [$key, $name, $price, $desc, $pop, $sort, $features]) {
            MembershipPlan::updateOrCreate(
                ['key' => $key],
                ['name' => $name, 'price' => $price, 'description' => $desc,
                 'is_popular' => $pop, 'is_active' => true, 'sort' => $sort, 'card_features' => $features],
            );
        }

        // Karşılaştırma tablosu: [ad, [ogrenci, standart, destekci, vip]]
        $features = [
            ['Dijital üye kartı (QR)', ['yes', 'yes', 'yes', 'yes']],
            ['Şeffaf kasa erişimi', ['yes', 'yes', 'yes', 'yes']],
            ['Etkinlik & duyuru bildirimleri', ['yes', 'yes', 'yes', 'yes']],
            ['Üyelere özel iletişim grubu', ['yes', 'yes', 'yes', 'yes']],
            ['Maç biletinde öncelikli satış', ['no', 'yes', 'yes', 'yes']],
            ['Mağaza indirimi', ['%5', '%10', '%15', '%20']],
            ['Deplasman otobüsünde öncelik', ['no', 'yes', 'yes', 'yes']],
            ['Deplasman biletinde öncelik', ['no', 'no', 'yes', 'yes']],
            ['Koreografi ekibine katılım önceliği', ['no', 'no', 'yes', 'yes']],
            ['Sınırlı üretim ürünlere erken erişim', ['no', 'no', 'yes', 'yes']],
            ['Hoş geldin paketi (atkı + rozet)', ['no', 'no', 'yes', 'yes']],
            ['Doğum günü hediyesi', ['no', 'no', 'yes', 'yes']],
            ['Oyuncu buluşması / imza günü', ['no', 'no', 'no', 'yes']],
            ['Etkinliklerde özel VIP alan', ['no', 'no', 'no', 'yes']],
            ['İsim plaketi / dijital onur duvarı', ['no', 'no', 'no', 'yes']],
        ];

        foreach ($features as $i => [$name, $vals]) {
            MembershipFeature::updateOrCreate(
                ['name' => $name],
                [
                    'values' => ['ogrenci' => $vals[0], 'standart' => $vals[1], 'destekci' => $vals[2], 'vip' => $vals[3]],
                    'is_active' => true,
                    'sort' => $i + 1,
                ],
            );
        }

        $this->command->info('Üyelik planları ve avantajlar yüklendi.');
    }
}
