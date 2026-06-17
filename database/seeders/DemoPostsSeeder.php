<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Ana sayfadaki "İkincil Haberler" ve "Son Haberler" bölümleri
 * boş görünmesin diye örnek haberler ekler.
 * Haber tablosu boşken otomatik çalışır (HomeController).
 * Manuel: php artisan db:seed --class=DemoPostsSeeder --force
 */
class DemoPostsSeeder extends Seeder
{
    public function run(): void
    {
        $haberler = [
            ['Sarı-Kırmızı Rüzgarı Sahaya İndi', 'mac', 'Taraftarın coşkusuyla dolan tribünler, takıma 12. adam olmaya devam ediyor.'],
            ['Deplasmana Çıkarma: Yollar Sarı-Kırmızı', 'tribun', 'Bu hafta deplasmanda takımımızın yanındayız. Otobüs ve bilet organizasyonu tamam.'],
            ['Yeni Sezon Koreografisi İçin Hazırlıklar Başladı', 'tribun', 'Tribünün yeni koreografisi için gönüllü buluşması yapıldı; emek ve heyecan dorukta.'],
            ['Camiamızdan Anlamlı Dayanışma', 'duyuru', 'İhtiyaç sahibi kardeşlerimiz için başlatılan kampanyaya taraftarımız tam destek verdi.'],
            ['Efsane Kadro Anıldı', 'haber', 'Şanlı tarihimize damga vuran efsanelerimiz, tribünlerde sevgiyle yad edildi.'],
            ['Üyelik Kampanyası Sürüyor', 'duyuru', 'Sen de tribünün resmi parçası ol; aidatın doğrudan tribünün gücüne dönüşsün.'],
            ['Şeffaf Kasa Güncellendi', 'basin', 'Aidat, bağış ve harcamaların tamamı faturalarına kadar açık şekilde yayınlandı.'],
        ];

        foreach ($haberler as $i => [$title, $category, $excerpt]) {
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => $category,
                    'excerpt' => $excerpt,
                    'body' => $excerpt."\n\nBu bir örnek haber metnidir. Web Yönetimi → Haberler bölümünden düzenleyebilir veya silebilirsiniz.",
                    'is_published' => true,
                    'published_at' => now()->subDays($i),
                ],
            );
        }

        $this->command?->info('Örnek haberler eklendi ('.count($haberler).' adet).');
    }
}
