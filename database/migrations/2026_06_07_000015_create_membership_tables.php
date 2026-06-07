<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Üyelik kademeleri (ana sayfa kartları)
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();          // ogrenci | standart | destekci | vip
            $table->string('name');
            $table->string('price');                  // '250' (gösterimde ₺ eklenir)
            $table->string('description')->nullable();
            $table->json('card_features')->nullable(); // kartta görünen kısa madde listesi
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Karşılaştırma tablosu satırları (avantajlar)
        Schema::create('membership_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('values')->nullable();        // { ogrenci:'yes'|'no'|'%5', standart:..., ... }
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_features');
        Schema::dropIfExists('membership_plans');
    }
};
