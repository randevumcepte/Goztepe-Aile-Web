<?php

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Göztepe Ayrıcalık tanıtımı için ÖRNEK (hayali) işletmeler + demo üyenin (uye@goztepe.test) ziyaretleri.
 * Hepsi is_demo=1; silmek için: php artisan ayricalik:demo-sil (ziyaretler de birlikte silinir).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false)->after('status');
        });

        $user = User::where('email', 'uye@goztepe.test')->first();
        if (Partner::where('is_demo', true)->exists()) {
            return;
        }

        $base = ['status' => 'aktif', 'is_demo' => true, 'district' => 'Göztepe / Konak', 'address' => 'Göztepe, Konak / İzmir'];
        $p = fn (array $a) => Partner::create($base + $a);

        $kahve = $p(['name' => 'Tribün Kahvecisi', 'category' => 'kafe', 'discount_text' => '%15 indirim', 'stamp_goal' => 5,
            'stamp_reward' => '1 filtre kahve bizden', 'hours' => 'Her gün 08:00–23:00', 'kasa_contribution' => 5, 'sort' => 1,
            'description' => 'Maç öncesi buluşma noktası. Maç günleri tribün indirimi geçerli.',
            'conditions' => 'Diğer kampanyalarla birleşmez. Paket serviste geçerli değildir.']);
        $burger = $p(['name' => 'Sarı Kırmızı Burger', 'category' => 'restoran', 'discount_text' => '%10 indirim', 'stamp_goal' => 6,
            'stamp_reward' => 'Menü yanında patates bizden', 'hours' => '11:00–24:00', 'kasa_contribution' => 3, 'sort' => 2]);
        $pide = $p(['name' => 'Eşrefpaşa Pide Salonu', 'category' => 'restoran', 'discount_text' => '%10 + çay ikramı', 'stamp_goal' => 5,
            'stamp_reward' => '1 kıymalı pide bizden', 'hours' => '10:00–22:00', 'sort' => 3]);
        $berber = $p(['name' => 'Kıyı Berber Salonu', 'category' => 'berber', 'discount_text' => '%15 indirim', 'stamp_goal' => 4,
            'stamp_reward' => 'Sakal tıraşı bizden', 'hours' => 'Pazar hariç 09:00–20:00', 'sort' => 4]);
        $giyim = $p(['name' => '1925 Spor Giyim', 'category' => 'giyim', 'discount_text' => '%20 indirim (forma hariç)', 'sort' => 5,
            'conditions' => 'Lisanslı formalarda geçerli değildir.']);
        $spor = $p(['name' => 'Fit Göztepe Spor Salonu', 'category' => 'spor', 'discount_text' => 'Aylık üyelikte %25', 'sort' => 6]);

        if (! $user) {
            return;
        }

        // Kronolojik ekleme (damga sayımı kimlik sırasına bakar): [işletme, kaç gün önce, tür]
        $plan = [
            [$kahve, 58, 'z'], [$spor, 55, 'z'], [$berber, 52, 'z'], [$kahve, 50, 'z'], [$pide, 45, 'z'], [$kahve, 44, 'z'],
            [$berber, 38, 'z'], [$kahve, 37, 'z'], [$pide, 35, 'z'], [$burger, 33, 'z'], [$kahve, 30, 'z'], [$kahve, 29, 'h'],
            [$berber, 24, 'z'], [$kahve, 20, 'z'], [$giyim, 18, 'z'], [$pide, 15, 'z'], [$kahve, 12, 'z'], [$berber, 9, 'z'],
            [$burger, 6, 'z'], [$kahve, 4, 'z'], [$pide, 2, 'z'],
        ];
        foreach ($plan as [$partner, $days, $type]) {
            $at = now()->subDays($days)->setTime(rand(10, 21), rand(0, 59));
            DB::table('partner_visits')->insert([
                'partner_id' => $partner->id,
                'user_id' => $user->id,
                'type' => $type === 'h' ? 'hediye' : 'ziyaret',
                'stamped' => $type === 'z' && $partner->stamp_goal > 0,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }

    public function down(): void
    {
        Partner::where('is_demo', true)->delete();
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('is_demo');
        });
    }
};
