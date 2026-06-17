<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('opponent');                       // Rakip takım adı
            $table->string('opponent_logo_path')->nullable(); // Rakip logosu (yüklenebilir)
            $table->string('competition')->default('Süper Lig'); // Lig / kupa
            $table->boolean('is_home')->default(true);        // Ev sahibi miyiz?
            $table->dateTime('kickoff_at');                   // Maç tarih-saati
            $table->string('venue')->nullable();              // Stat
            $table->string('broadcast')->nullable();          // Yayıncı (TV)
            $table->unsignedTinyInteger('home_score')->nullable(); // Oynandıysa skor
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'kickoff_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
