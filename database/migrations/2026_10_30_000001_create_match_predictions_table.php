<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tribün Tahmin Oyunu — üyenin maç skor tahmini (parasız; yalnız puan/rozet)
        Schema::create('match_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('home_goals');            // ev sahibi - deplasman sırasıyla
            $table->unsignedTinyInteger('away_goals');
            $table->string('reason', 280)->nullable();             // üyenin gerekçesi (tribün özetine katkı)
            $table->json('factors')->nullable();                   // seçilen etkenler (sakatlik, form ...)
            $table->unsignedTinyInteger('points')->nullable();     // maç bitince hesaplanır
            $table->boolean('beat_ai')->nullable();                // AI'dan çok puan aldı mı?
            $table->timestamps();

            $table->unique(['user_id', 'fixture_id']);
            $table->index(['fixture_id', 'points']);
        });

        // Maç başlarken AI'ın tahmini dondurulur ("AI'ı Yen" + öğrenme için) ve tribün özeti saklanır
        Schema::table('fixtures', function (Blueprint $table) {
            $table->json('ai_probs')->nullable();
            $table->unsignedTinyInteger('ai_home_goals')->nullable();
            $table->unsignedTinyInteger('ai_away_goals')->nullable();
            $table->text('crowd_summary')->nullable();
            $table->string('crowd_summary_hash', 32)->nullable();
            $table->timestamp('crowd_summary_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['ai_probs', 'ai_home_goals', 'ai_away_goals', 'crowd_summary', 'crowd_summary_hash', 'crowd_summary_at']);
        });
        Schema::dropIfExists('match_predictions');
    }
};
