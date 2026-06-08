<?php

namespace Database\Seeders;

use App\Models\Legend;
use Illuminate\Database\Seeder;

/**
 * "Şanlı Tarihimiz" efsane futbolcular bölümünü varsayılan içerikle doldurur (idempotent).
 * Çalıştır: php artisan db:seed --class=LegendSeeder --force
 */
class LegendSeeder extends Seeder
{
    public function run(): void
    {
        // [name, role, nickname, era, note, bio, sort]
        $legends = [
            ['Ali Artuner', 'Kaleci', 'Moskova Panteri', '1961–1976',
                'Türk kaleciliğinin zirvesi; refleksleri ve blokajlarıyla efsane.',
                'İzmir doğumlu Ali Artuner (1944–2001), 17 yaşında okulu bırakıp 1961\'de Göztepe kalesine geçti ve 15 sezon üst üste 1. Lig\'de forma giydi. 1968-69 ve 1969-70 Türkiye Kupaları ile 1970 Süper Kupası\'nın kalesindeki güvencesiydi. A Milli Takım\'da 47 maça çıktı, tam 33 kez kaptanlık yaptı. 16 Ekim 1966\'da Moskova\'daki Lenin Stadyumu\'nda SSCB\'yi 2-0 yendikleri maçta sergilediği olağanüstü performansla basın ona "Moskova Panteri" lakabını taktı. İri yapısına rağmen müthiş reflekslere ve yan toplara hâkimiyete sahipti; Türk kaleciliğinin en büyük isimlerinden biri olarak anılır.',
                1],

            ['Halil Kiraz', 'Sağ Bek / Açık', 'Bombacı', '1960\'lar',
                '"Bombacı Halil" — Atletico zaferinin kahramanlarından.',
                'Asıl mevkii sol açık olan Halil Kiraz\'ı, teknik direktör Adnan Süvari\'nin vizyonuyla sağ bek olarak da oynattı; Göztepe formasıyla neredeyse her mevkide görev yaptı. Sert ve gümbür gümbür şutları nedeniyle "Bombacı" lakabıyla anıldı. 1967-68 Fuar Şehirleri Kupası\'nda İzmir\'de Atletico Madrid\'i 3-0 yendikleri tarihî maçın kahramanlarından biriydi ve İspanyol devine attığı golle adını efsaneler arasına yazdırdı.',
                2],

            ['Fevzi Zemzem', 'Forvet / Orta Saha', 'Gol Makinesi', '1960–1974',
                'Altın neslin yaratıcı beyni ve golcüsü.',
                'Fevzi Zemzem (1941–2022), 14 yıllık profesyonel kariyerinin tamamını Göztepe\'de geçirdi. Sarı-kırmızılı formayla 1. Lig\'de 144 gol attı; 1967-68 sezonunda ligin gol kralı oldu. 2 Türkiye Kupası ve 1 Cumhurbaşkanlığı Kupası kazandı. Hem bitiriciliği hem de oyun kuruculuğuyla altın neslin en parlak isimlerinden biriydi. 2022\'de kaybettiğimiz Zemzem, İzmir\'de gönüllerde bir efsane olarak yaşıyor.',
                3],

            ['Gürsel Aksel', 'Kaptan', 'Koca Kaptan', '1955–1973',
                'Stadın adını taşıyan ölümsüz kaptan.',
                '1937 Uzunköprü (Edirne) doğumlu Gürsel Aksel, 1955\'ten itibaren Göztepe formasını bir daha hiç çıkarmadı. 1959\'da başlayan Süper Lig\'de 14 sezon boyunca 390 ardışık maça çıkarak sadakatin ve kaptanlığın simgesi oldu. Tüm kariyerini tek bir kulübe adayan "Koca Kaptan", camianın gönlünde öyle bir yer edindi ki 2020\'de açılan modern stadyuma onun adı verildi: Gürsel Aksel Stadyumu.',
                4],

            ['Nevzat Güzelırmak', 'Defans', 'İngiliz', '1960\'lar',
                'Savunmanın güvenilir kalesi, zarif oyuncu.',
                'Top kontrolü, pas dağıtımı ve sahadaki zarif konumlanışıyla döneminde "İngiliz" lakabıyla anıldı. Adnan Süvari\'nin altın neslinde savunmanın güveni oldu; İzmir\'de Atletico Madrid\'i 3-0 yendikleri tarihî maçın ilk on birinde yer aldı. Soğukkanlı ve centilmen oyunuyla Türk futbolunda saygın bir iz bıraktı; 2020\'de aramızdan ayrıldı.',
                5],

            ['Adnan Süvari', 'Teknik Direktör', 'Altın Neslin Mimarı', '1960\'lar–70\'ler',
                'Avrupa yarı finalinin ve kupaların mimarı.',
                'Göztepe\'nin altın çağının teknik direktörü Adnan Süvari, takımı hem Türkiye Kupalarına hem de Avrupa\'da yarı finale taşıyan vizyoner isimdi. Oyuncularını doğru mevkilerde değerlendiren (Halil Kiraz\'ı sağ bek oynatması gibi) cesur kararlarıyla tanındı. Marsilya, Hamburg gibi devleri eleyip Fuar Şehirleri Kupası\'nda yarı finale çıkan o efsane kadronun arkasındaki akıldı.',
                6],
        ];

        foreach ($legends as [$name, $role, $nick, $era, $note, $bio, $sort]) {
            Legend::updateOrCreate(
                ['sort' => $sort],
                [
                    'name' => $name, 'role' => $role, 'nickname' => $nick, 'era' => $era,
                    'note' => $note, 'bio' => $bio, 'is_active' => true,
                ],
            );
        }
    }
}
