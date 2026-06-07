<?php

namespace Database\Seeders;

use App\Models\HistoryEvent;
use Illuminate\Database\Seeder;

/**
 * "Şanlı Tarihimiz" sayfasının zaman tüneli + galerisini varsayılan içerikle doldurur (idempotent).
 * Çalıştır: php artisan db:seed --class=HistorySeeder --force
 */
class HistorySeeder extends Seeder
{
    public function run(): void
    {
        // [year, title, tag, description, caption, image (img/tarih/...), in_timeline, in_gallery, sort]
        $events = [
            ['1925', 'Bir Çınarın Doğuşu', 'Kuruluş',
                '14 Haziran 1925\'te Göztepe semtinde, vapur iskelesi yanındaki Mez Gazinosu\'nda toplanan kıdemli futbolcular ve gençler kulübü resmen kurdu. İzmir\'in gür sesi böyle yükseldi.',
                'Kuruluş yılları — Göztepe semti', 'img/tarih/1925-kurulus.jpg', true, true, 1],

            ['1950', 'Türkiye Şampiyonu', 'Şampiyonluk',
                'Profesyonel lig öncesi dönemin en büyük başarısı: Göztepe, Türkiye Futbol Şampiyonası\'nda zirveye çıkarak adını Anadolu\'dan tüm yurda duyurdu.',
                'Türkiye Şampiyonu kadro', 'img/tarih/1950-sampiyon.jpg', true, true, 2],

            ['1967', 'Alsancak\'ta Atletico Destanı', 'Avrupa',
                'Fuar Şehirleri Kupası\'nda Alsancak Stadyumu\'nda Atletico Madrid 3-0 devrildi. Avrupa, İzmir tribünlerinin gücünü öğrendi.',
                'Alsancak\'ta Atletico Madrid zaferi', 'img/tarih/1967-atletico.jpg', true, true, 3],

            ['1968-69', 'Avrupa\'da Yarı Final & İlk Kupa', 'Tarih Yazıldı',
                'Marsilya, Argeş, OFK Beograd ve Hamburg elenerek Fuar Şehirleri Kupası\'nda yarı finale çıkıldı — Avrupa kupalarında bu aşamayı gören ilk Türk takımı. Aynı sezon Galatasaray\'ı geçerek ilk Türkiye Kupası müzeye taşındı.',
                'İlk Türkiye Kupası sevinci', 'img/tarih/1969-kupa.jpg', true, true, 4],

            ['1968-69', 'Avrupa Yolculuğu', 'Avrupa',
                'Kıtanın dört bir yanında sarı-kırmızı bayrak dalgalandı; yarı final yolculuğu Türk futbolunun gururu oldu.',
                'Avrupa yarı finali yolculuğu', 'img/tarih/1969-avrupa.jpg', false, true, 5],

            ['1969-70', 'Üst Üste Zafer', 'İkinci Kupa',
                'Eskişehirspor\'u final serisinde geçerek ikinci Türkiye Kupası kazanıldı; Kupa Galipleri Kupası\'nda çeyrek finale yükselindi.',
                'İkinci kupa, üst üste zafer', 'img/tarih/1970-kupa.jpg', true, true, 6],

            ['1970', 'Cumhurbaşkanlığı Kupası', 'Süper Kupa',
                'Türk futbolunun prestijli Cumhurbaşkanlığı (Süper) Kupası da Göztepe\'nin oldu. Sarı-kırmızı çağın altın yılları.',
                'Altın çağın efsane kadrosu', 'img/tarih/efsane-kadro.jpg', true, true, 7],

            ['Tribün', 'Alsancak\'ın Sesi', 'Tribün',
                'Her maç bir bayram; Alsancak tribünleri yıllar boyu takımın 12. adamı oldu.',
                'Alsancak tribünlerinden bir kare', 'img/tarih/alsancak-tribun.jpg', false, true, 8],

            ['2020', 'Gürsel Aksel Stadyumu', 'Yeni Ev',
                '26 Ocak 2020\'de kapılarını açan modern Gürsel Aksel Stadyumu, efsane kaptanın adıyla yeni neslin evi oldu.',
                'Gürsel Aksel Stadyumu', 'img/tarih/gursel-aksel.jpg', true, true, 9],

            ['2024', 'Süper Lig\'e Dönüş', 'Bugün',
                'Göztepe, taraftarının gücüyle yeniden Süper Lig\'e yükseldi. Çınar büyümeye, tribünler haykırmaya devam ediyor.',
                'Süper Lig\'e dönüş coşkusu', null, true, false, 10],
        ];

        foreach ($events as [$year, $title, $tag, $desc, $caption, $img, $inTl, $inGal, $sort]) {
            HistoryEvent::updateOrCreate(
                ['sort' => $sort],
                [
                    'year' => $year, 'title' => $title, 'tag' => $tag,
                    'description' => $desc, 'caption' => $caption, 'image_path' => $img,
                    'in_timeline' => $inTl, 'in_gallery' => $inGal, 'is_active' => true,
                ],
            );
        }
    }
}
