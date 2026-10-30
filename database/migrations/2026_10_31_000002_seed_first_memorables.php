<?php

use App\Models\Memorable;
use Illuminate\Database\Migrations\Migration;

/**
 * İlk Unutulmazlar: Rômulo, Mateusz Lis, Beto.
 * Bilgiler: Wikipedia (EN) sayfaları + kulüp/spor basını (Ekim 2026). Fotoğraflar Wikimedia
 * Commons'tan serbest lisanslı; lisans gereği altyazıda sahibi ve lisansı yazar.
 * Admin panelinden düzenlenebilir; aynı adla kayıt varsa dokunmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $w = 'https://upload.wikimedia.org/wikipedia/commons/';

        $items = [
            [
                'name' => 'Rômulo Cardoso',
                'subtitle' => 'Forvet · Brezilya',
                'era' => '2024–2025',
                'summary' => '1. Lig’den Süper Lig’e uzanan yolculuğun golcüsü. Beşiktaş deplasmanındaki kupa hat-trick’iyle hafızalara kazındı.',
                'stats' => [
                    ['label' => 'Resmi maç', 'value' => '46'],
                    ['label' => 'Gol', 'value' => '22'],
                    ['label' => 'Süper Lig golü (2024-25)', 'value' => '13'],
                    ['label' => 'Türkiye Kupası golü', 'value' => '4'],
                ],
                'bio' => "Rômulo José Cardoso da Cruz, 8 Şubat 2002’de Brezilya’nın Paraná eyaletindeki Marialva’da doğdu. Futbolu Athletico Paranaense altyapısında öğrendi; 2021’de Copa Sudamericana’yı kazanan Athletico kadrosunda yer aldı.\n\n"
                    ."7 Şubat 2024’te, Göztepe henüz TFF 1. Lig’deyken, satın alma opsiyonlu kiralık olarak İzmir’e geldi. 11 Şubat’taki ilk maçında Bandırmaspor deplasmanında oyuna girdiği 27 dakikada bir gol attı, bir asist yaptı; Göztepe sahadan 2-0 galip ayrıldı. Sezonun kalanında 13 lig maçında 5 gol atarak Süper Lig’e dönüşe ortak oldu.\n\n"
                    ."Süper Lig’deki ilk golünü 17 Ağustos 2024’te Fenerbahçe karşısında 90+5’te attı ve maçı 2-2’ye getirdi. Ocak 2025’te Göztepe satın alma opsiyonunu kullanarak onu 2028’e kadar kadrosuna kattı. 2024-25 sezonunu Süper Lig’de 29 maçta 13 golle kapattı; Türkiye Kupası’nda ise 4 maçta 4 gol attı. Bu gollerin üçü, 3 Nisan 2025’te Beşiktaş deplasmanındaki çeyrek finalde geldi: Rômulo’nun hat-trick’iyle Göztepe o geceyi 3-1 kazandı.\n\n"
                    ."Göztepe formasıyla 46 resmi maçta 22 gol attı. Ağustos 2025’te, kulüp tarihinin rekor bonservisi olarak bildirilen bir transferle Almanya’nın RB Leipzig takımına beş yıllık sözleşmeyle imza attı.",
                'gallery' => [
                    ['url' => $w.'thumb/e/e8/S%C3%89RIE_A_-_PALMEIRAS_0_X_2_ATHLETICO_PARANAENSE_-_R%C3%B4mulo_%28cropped%29.jpg/960px-S%C3%89RIE_A_-_PALMEIRAS_0_X_2_ATHLETICO_PARANAENSE_-_R%C3%B4mulo_%28cropped%29.jpg',
                        'caption' => 'Athletico Paranaense formasıyla, 2022 · Foto: SOCCER DIGITAL (kamu malı)'],
                    ['url' => $w.'thumb/a/ac/Romulo-Athletico-Palmeiras-jul-2022.jpg/960px-Romulo-Athletico-Palmeiras-jul-2022.jpg',
                        'caption' => 'Palmeiras maçında, Temmuz 2022 · Foto: SOCCER DIGITAL (kamu malı)'],
                    ['url' => $w.'thumb/b/b2/S%C3%89RIE_A_-_PALMEIRAS_0_X_2_ATHLETICO_PARANAENSE_-_R%C3%B4mulo_Cardoso.jpg/960px-S%C3%89RIE_A_-_PALMEIRAS_0_X_2_ATHLETICO_PARANAENSE_-_R%C3%B4mulo_Cardoso.jpg',
                        'caption' => 'Brezilya Serie A, 2022 · Foto: SOCCER DIGITAL (kamu malı)'],
                ],
                'sort' => 10,
            ],
            [
                'name' => 'Mateusz Lis',
                'subtitle' => 'Kaleci · Polonya',
                'era' => '2023–2026',
                'summary' => 'Süper Lig’e dönüşün kalecisi. 2025-26’da 16 maçta kalesini gole kapatarak ligin en çok gol yemeyen kalecisi oldu.',
                'stats' => [
                    ['label' => 'Lig maçı', 'value' => '97'],
                    ['label' => 'Gol yemediği maç (2025-26)', 'value' => '16'],
                    ['label' => 'Sezon', 'value' => '3'],
                ],
                'bio' => "Mateusz Lis, 27 Şubat 1997’de Polonya’nın Żary kentinde doğdu. Lech Poznań altyapısından yetişti; Raków Częstochowa ve Wisła Kraków’da forma giydi. Türkiye’yi ilk kez 2021-22 sezonunda Altay formasıyla tanıdı, ardından Southampton’a transfer oldu ve Fransa’da Troyes’ta kiralık oynadı.\n\n"
                    ."11 Temmuz 2023’te Southampton’ın kardeş kulübü Göztepe’ye kiralandı. O sezon 31 lig maçında kaleyi korudu; Göztepe TFF 1. Lig’i ikinci bitirerek Süper Lig’e döndü. 22 Ağustos 2024’te üç yıllık sözleşmeyle kalıcı olarak Göztepe’ye imza attı.\n\n"
                    ."2025-26 sezonunda ligin 34 maçının tamamında oynadı ve 16 maçta kalesini gole kapatarak Süper Lig’in en çok gol yemeyen kalecisi oldu. Göztepe formasıyla 97 lig maçına çıktıktan sonra Haziran 2026’da kariyerine başladığı Lech Poznań’a döndü.",
                'gallery' => [
                    ['url' => $w.'6/65/Mateusz_Lis_2022.jpg',
                        'caption' => 'Troyes formasıyla, 2022 · Foto: ESTAC Troyes (CC BY 3.0)'],
                ],
                'sort' => 20,
            ],
            [
                'name' => 'Beto',
                'subtitle' => 'Kaleci · Portekiz',
                'era' => '2017–2020',
                'summary' => 'Üç UEFA Avrupa Ligi kupası kazanmış bir tecrübeyle geldi; kaptanlık yaptı, penaltı kurtarışlarıyla tribünün gönlünde yer etti.',
                'stats' => [
                    ['label' => 'Resmi maç', 'value' => '95'],
                    ['label' => 'Portekiz Milli Takımı', 'value' => '16 maç'],
                    ['label' => 'UEFA Avrupa Ligi', 'value' => '3 kupa'],
                ],
                'bio' => "Asıl adı António Alberto Bastos Pimparel olan Beto, 1 Mayıs 1982’de Portekiz’in Loures kentinde doğdu. Kariyerinde Porto ile 2011’de, Sevilla ile 2014 ve 2015’te olmak üzere üç kez UEFA Avrupa Ligi kazandı. Portekiz Milli Takımı’nda 16 kez forma giydi; üç Dünya Kupası ve Euro 2012 kadrosunda yer aldı.\n\n"
                    ."29 Temmuz 2017’de Sporting’den Göztepe’ye transfer oldu ve Süper Lig’e yeni dönmüş takıma büyük maç tecrübesi getirdi. Kısa sürede takımın kaptanlarından biri oldu. Penaltı kurtarışlarıyla öne çıktı; Beşiktaş karşısında Oğuzhan Özyakup’un penaltısını çıkarışı hâlâ hatırlanır. 2018-19 sezonunda 33 maçta 2.965 dakikayla takımın en çok süre alan oyuncusu oldu ve Göztepe’nin ligde kalmasında büyük pay sahibi oldu.\n\n"
                    ."Göztepe formasıyla 95 resmi maça çıktıktan sonra Ağustos 2020’de sözleşmesinin bitimiyle ayrıldı ve Portekiz’e, Leixões’e döndü.",
                'gallery' => [
                    ['url' => $w.'thumb/7/72/Ant%C3%B3nio_Alberto_Bastos_%22Beto%22.jpg/960px-Ant%C3%B3nio_Alberto_Bastos_%22Beto%22.jpg',
                        'caption' => 'Sevilla döneminde · Foto: Gabriel Corbacho (CC BY-SA 3.0)'],
                    ['url' => $w.'thumb/9/9b/Ant%C3%B3nio_Alberto_Bastos_Pimparel.JPG/960px-Ant%C3%B3nio_Alberto_Bastos_Pimparel.JPG',
                        'caption' => 'Foto: Ultraslansi (CC BY-SA 3.0)'],
                ],
                'sort' => 30,
            ],
        ];

        foreach ($items as $item) {
            Memorable::firstOrCreate(['name' => $item['name']], $item + ['category' => 'futbolcu', 'is_active' => true]);
        }
    }

    public function down(): void
    {
        Memorable::whereIn('name', ['Rômulo Cardoso', 'Mateusz Lis', 'Beto'])->delete();
    }
};
