<?php

use App\Enums\MemberCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('member_no')->unique();
            $table->string('category')->default(MemberCategory::Standart->value);
            $table->string('status')->default('beklemede'); // beklemede | aktif | pasif
            $table->date('joined_at')->nullable();
            $table->timestamp('kvkk_consent_at')->nullable();      // KVKK aydınlatma onayı
            $table->boolean('commercial_consent')->default(false); // Ticari ileti (İYS) rızası
            $table->timestamps();

            $table->index(['category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
