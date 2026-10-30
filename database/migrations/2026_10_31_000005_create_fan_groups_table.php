<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Taraftar grupları — yalnız admin tanımlar; üye kendi grubunu seçer (isteğe bağlı, açık rızayla)
        Schema::create('fan_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('logo_path')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('tribune_area', 120)->nullable(); // "Kale arkası, Blok C"
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('fan_group_id')->nullable()->constrained('fan_groups')->nullOnDelete();
            $table->timestamp('fan_group_consent_at')->nullable(); // KVKK açık rıza zamanı
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fan_group_id');
            $table->dropColumn('fan_group_consent_at');
        });
        Schema::dropIfExists('fan_groups');
    }
};
