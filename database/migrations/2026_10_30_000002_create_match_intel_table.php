<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tribün Muhbiri — maç öncesi kısa sorular (eksik oyuncu, sürpriz kadro);
        // bedava kaynakta olmayan kadro bilgisini tribünden toplar.
        Schema::create('match_intel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->json('answers');                 // {soru: {choice: evet|hayir|bilmiyorum, text: ?}}
            $table->timestamps();

            $table->unique(['user_id', 'fixture_id']);
        });

        Schema::table('fixtures', function (Blueprint $table) {
            // Tribün liderinin doğrulanmış kadro notu (AI bunu kesin bilgi sayar)
            $table->text('leader_note')->nullable();
            $table->timestamp('leader_note_at')->nullable();
            // Gönderilen hatırlatmalar (tekrar gönderilmesin): {acildi: ts, son_cagri: ts, sonuc: ts}
            $table->json('reminders_sent')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['leader_note', 'leader_note_at', 'reminders_sent']);
        });
        Schema::dropIfExists('match_intel');
    }
};
