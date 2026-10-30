<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Unutulmazlar — Göztepe'ye iz bırakmış futbolcular, hocalar, maçlar, anlar
        Schema::create('memorables', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30)->default('futbolcu'); // futbolcu|teknik_direktor|mac|an|diger
            $table->string('name');                              // "Rômulo Cardoso" / "Beşiktaş 1-3 Göztepe"
            $table->string('subtitle')->nullable();              // "Forvet · Brezilya"
            $table->string('era')->nullable();                   // "2024–2025"
            $table->string('summary', 300)->nullable();          // kart kısa metni
            $table->text('bio')->nullable();                     // detay sayfası özgeçmiş
            $table->json('stats')->nullable();                   // [{label, value}]
            $table->string('cover_path')->nullable();            // kapak (yükleme yolu ya da tam adres)
            $table->json('gallery')->nullable();                 // [{url, caption}]
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bestelerimiz — Göztepe için yapılmış besteler (dokununca çalar)
        Schema::create('chants', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('note')->nullable();                  // "Tribün klasiği · 1980'ler"
            $table->text('lyrics')->nullable();
            $table->string('audio_path')->nullable();            // yüklenen dosya ya da tam adres
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chants');
        Schema::dropIfExists('memorables');
    }
};
