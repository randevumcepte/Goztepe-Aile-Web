<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('purpose');  // aidat | bagis | satis
            $table->string('provider')->nullable(); // iyzico | paytr
            $table->string('provider_ref')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('bekliyor'); // bekliyor | basarili | iade | iptal
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete(); // Onaylanınca üretilen kasa kaydı
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
