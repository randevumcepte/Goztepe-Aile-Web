<?php

use App\Enums\StudentVerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_verifications', function (Blueprint $table) {
            $table->id();
            // Her üyenin tek doğrulama kaydı olur; red/yenilemede aynı satır güncellenir.
            $table->foreignId('member_id')->unique()->constrained()->cascadeOnDelete();

            // Okul içi öğrenci numarası — aynı numarayla ikinci kez kayıt engellenir.
            $table->string('student_no')->unique();
            $table->string('school')->nullable();               // Üniversite / okul adı
            $table->string('tc_no', 11)->nullable();            // Belge doğrulama anahtarı (KVKK: hassas)
            $table->string('document_barcode')->nullable()->unique(); // e-Devlet belge barkod no
            $table->string('document_path');                    // Yüklenen belge (özel disk)

            $table->string('status')->default(StudentVerificationStatus::Beklemede->value);
            $table->date('valid_until')->nullable();            // Belge geçerlilik (dönem sonu) — onayda set edilir
            $table->string('rejection_reason')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_verifications');
    }
};
