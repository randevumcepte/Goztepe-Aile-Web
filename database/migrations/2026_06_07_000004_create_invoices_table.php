<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();      // Fatura/fiş görseli (PDF/JPG)
            $table->string('supplier_masked')->nullable(); // Üyeye gösterilen maskeli ad
            $table->string('supplier_full')->nullable();   // Tam ad — sadece yönetim
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('issued_at')->nullable();
            $table->boolean('is_public')->default(true);   // Maskeli hali herkese açık mı
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
