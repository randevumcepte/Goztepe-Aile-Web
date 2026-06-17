<?php

namespace Database\Seeders;

use App\Models\Fixture;
use Illuminate\Database\Seeder;

/**
 * Ana sayfadaki "Haftanın Maçı" kartı boş görünmesin diye örnek bir maç ekler.
 * Maç tablosu boşken otomatik çalışır (HomeController).
 * Manuel: php artisan db:seed --class=DemoFixturesSeeder --force
 */
class DemoFixturesSeeder extends Seeder
{
    public function run(): void
    {
        Fixture::create([
            'opponent' => 'Fenerbahçe',
            'opponent_logo_path' => 'https://r2.thesportsdb.com/images/media/team/badge/twxxvs1448199691.png',
            'competition' => 'Süper Lig',
            'is_home' => true,
            'kickoff_at' => now()->addDays(5)->setTime(19, 0),
            'venue' => 'Gürsel Aksel Stadyumu',
            'broadcast' => 'beIN Sports 1',
            'is_active' => true,
        ]);

        $this->command?->info('Örnek maç eklendi (Haftanın Maçı kartı için).');
    }
}
