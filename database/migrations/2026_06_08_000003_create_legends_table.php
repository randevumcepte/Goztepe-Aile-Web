<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legends', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();        // mevki (Kaleci, Forvet…)
            $table->string('nickname')->nullable();     // lakap ("Moskova Panteri")
            $table->string('era')->nullable();          // dönem ("1961–1976")
            $table->string('note')->nullable();         // kart kısa metni
            $table->text('bio')->nullable();            // popup detaylı biyografi
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legends');
    }
};
