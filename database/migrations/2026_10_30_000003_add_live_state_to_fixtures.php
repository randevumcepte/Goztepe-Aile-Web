<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Canlı maç takibi: ESPN maç kimliği, son durum, bildirimi gönderilmiş olaylar
        Schema::table('fixtures', function (Blueprint $table) {
            $table->json('live_state')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn('live_state');
        });
    }
};
