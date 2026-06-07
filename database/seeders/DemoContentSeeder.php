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
            ['Tribünden gür bir başlangıç', 'tribun', 'Yeni sezona taraftarımızın coşkusuyla giriyoruz. Tribünler bu hafta yine tıklım tıklım.'],
            ['Deplasman organizasyonu açıklandı', 'duyuru', 'Bu haftaki deplasman maçı için otobüs ve bilet organizasyonu başladı. Üyelerimize öncelik tanınacak.'],
            ['Büyük tifo için hazırlıklar sürüyor', 'tribun', 'Derbi haftasına özel hazırladığımız koreografi için çalışmalar tüm hızıyla devam ediyor.'],
            ['Şeffaf kasa yayında', 'duyuru', 'Artık her kuruşun nereye gittiğini faturasına kadar görebilirsin. Güven, camianın temelidir.'],
            ['Sosyal yardım kampanyamıza destek', 'haber', 'Camiamızın dayanışma ruhuyla başlattığımız yardım kampanyasına katılım rekor seviyede.'],
            ['Genel kurul tarihi belli oldu', 'basin', 'Olağan genel kurul toplantımızın tarihi ve gündemi üyelerimizle paylaşıldı.'],
            ['Maç günü buluşması', 'mac', 'Maç öncesi geleneksel buluşmamız yine aynı noktada. Tüm taraftarlar davetlidir.'],
        ];

        foreach ($haberler as $i => [$title, $cat, $excerpt]) {
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => $cat,
                    'excerpt' => $excerpt,
                    'body' => $excerpt."\n\nGöztepe Tribünleri olarak camiamızın gücünü her geçen gün büyütüyoruz. "
                        ."Detaylar ve gelişmeler için bizi takip etmeye devam edin.\n\nİzmir'in gür sesi, sahada ve tribünde tek yürek.",
                    'is_published' => true,
                    'published_at' => Carbon::now()->subDays($i),
                ],
            );
        }

        $this->command->info('Örnek haberler eklendi.');
    }
}
