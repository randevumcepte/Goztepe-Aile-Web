<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Ana sayfa boş görünmesin diye örnek haberler.
 * Çalıştır: php artisan db:seed --class=DemoContentSeeder --force
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $haberler = [
            ['Tribünden gür bir başlangıç', 'tribun', 'Yeni sezona taraftarımızın coşkusuyla giriyoruz. Tribünler bu hafta yine tıklım tıklım.', 'stadium,crowd'],
            ['Deplasman organizasyonu açıklandı', 'duyuru', 'Bu haftaki deplasman maçı için otobüs ve bilet organizasyonu başladı. Üyelerimize öncelik tanınacak.', 'football,fans'],
            ['Büyük tifo için hazırlıklar sürüyor', 'tribun', 'Derbi haftasına özel hazırladığımız koreografi için çalışmalar tüm hızıyla devam ediyor.', 'football,supporters'],
            ['Şeffaf kasa yayında', 'duyuru', 'Artık her kuruşun nereye gittiğini faturasına kadar görebilirsin. Güven, camianın temelidir.', 'stadium'],
            ['Sosyal yardım kampanyamıza destek', 'haber', 'Camiamızın dayanışma ruhuyla başlattığımız yardım kampanyasına katılım rekor seviyede.', 'football,team'],
            ['Genel kurul tarihi belli oldu', 'basin', 'Olağan genel kurul toplantımızın tarihi ve gündemi üyelerimizle paylaşıldı.', 'soccer,stadium'],
            ['Maç günü buluşması', 'mac', 'Maç öncesi geleneksel buluşmamız yine aynı noktada. Tüm taraftarlar davetlidir.', 'football,match'],
        ];

        foreach ($haberler as $i => [$title, $cat, $excerpt, $keywords]) {
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => $cat,
                    'excerpt' => $excerpt,
                    'body' => $excerpt."\n\nGöztepe Tribünleri olarak camiamızın gücünü her geçen gün büyütüyoruz. "
                        ."Detaylar ve gelişmeler için bizi takip etmeye devam edin.\n\nİzmir'in gür sesi, sahada ve tribünde tek yürek.",
                    // Demo futbol temalı görsel (üretimde admin'den gerçek foto yüklenir)
                    'cover_path' => "https://loremflickr.com/1200/800/{$keywords}?lock=".($i + 11),
                    'is_published' => true,
                    'published_at' => Carbon::now()->subDays($i),
                ],
            );
        }

        $this->command->info('Örnek haberler eklendi.');
    }
}
