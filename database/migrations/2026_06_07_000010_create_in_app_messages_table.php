<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reklam pop-up / in-app kampanya sistemi
        Schema::create('in_app_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('modal'); // modal | banner | fullscreen | card
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('media_path')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('segment')->nullable();          // hedef kitle kuralları
            $table->boolean('is_commercial')->default(false); // reklam ise rıza gerekir
            $table->unsignedInteger('frequency_cap')->default(1); // kişiye toplam kaç kez
            $table->unsignedInteger('priority')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaign_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('in_app_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('shown_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();

            $table->index(['in_app_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_impressions');
        Schema::dropIfExists('in_app_messages');
    }
};
