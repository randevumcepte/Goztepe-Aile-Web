<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_events', function (Blueprint $table) {
            $table->id();
            $table->string('year');                       // "1925", "1968-69"
            $table->string('title');
            $table->string('tag')->nullable();            // rozet (örn. "Kuruluş")
            $table->text('description')->nullable();      // zaman tüneli metni
            $table->string('caption')->nullable();        // galeri fotoğraf başlığı
            $table->string('image_path')->nullable();     // yüklenen fotoğraf
            $table->boolean('in_timeline')->default(true);
            $table->boolean('in_gallery')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_events');
    }
};
