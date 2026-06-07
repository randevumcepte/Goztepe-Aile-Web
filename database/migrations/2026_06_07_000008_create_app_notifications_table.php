<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Uygulama içi bildirim merkezi (zil ikonu) + gönderim logu
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('template_key')->nullable(); // aidat_hatirlatma, odeme_onay ...
            $table->string('channel')->default('in_app'); // in_app | push | sms | email | whatsapp
            $table->string('type')->default('islemsel');  // islemsel | ticari
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();             // tıklanınca gidilecek ekran vb.
            $table->string('status')->default('sent');    // queued | sent | delivered | failed
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
