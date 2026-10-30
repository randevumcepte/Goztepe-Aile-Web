<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Göztepe Ayrıcalık — anlaşmalı işletmeler (kafe, restoran, mağaza…)
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 30)->default('kafe');       // kafe|restoran|giyim|berber|spor|diger
            $table->string('discount_text')->nullable();            // "%15 indirim"
            $table->string('description', 500)->nullable();
            $table->text('conditions')->nullable();                 // "Kampanyalarla birleşmez" vb.
            $table->string('address')->nullable();
            $table->string('district', 80)->nullable();             // "Göztepe / Konak"
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('hours', 120)->nullable();               // "Her gün 08:00–23:00"
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->unsignedSmallInteger('stamp_goal')->nullable(); // damga kartı: kaç ziyarette hediye
            $table->string('stamp_reward')->nullable();             // "1 kahve bizden"
            $table->decimal('kasa_contribution', 8, 2)->nullable(); // ziyaret başı kasaya katkı taahhüdü (TL)
            $table->string('code', 12)->unique();                   // işletme giriş kodu (okutma sayfası)
            $table->string('pin_hash')->nullable();                 // okutma PIN'i (bcrypt)
            $table->string('status', 20)->default('beklemede');     // beklemede|aktif|pasif
            $table->string('applicant_name')->nullable();           // başvuru yapan
            $table->string('applicant_phone', 40)->nullable();
            $table->foreignId('applicant_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['status', 'category']);
        });

        // Her okutma: indirim kullanımı (+damga) ya da hediye teslimi
        Schema::create('partner_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10)->default('ziyaret');          // ziyaret | hediye
            $table->boolean('stamped')->default(false);              // bu ziyaret damga verdi mi
            $table->timestamps();

            $table->index(['partner_id', 'created_at']);
            $table->index(['user_id', 'partner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_visits');
        Schema::dropIfExists('partners');
    }
};
