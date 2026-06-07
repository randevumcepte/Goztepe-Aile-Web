<?php

use App\Enums\Visibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained()->cascadeOnDelete();
            $table->string('direction'); // gelir | gider
            $table->string('category');  // aidat | bağış | sponsor | satış | koreografi | deplasman | idari ...
            $table->decimal('amount', 14, 2);
            $table->date('occurred_at');
            $table->string('description')->nullable();
            $table->string('visibility')->default(Visibility::Public->value);
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete(); // Kim yatırdı
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fund_id', 'direction']);
            $table->index(['visibility', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
