<?php

namespace App\Http\Controllers\Member;

use App\Enums\StudentVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\StudentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentVerificationController extends Controller
{
    /** Öğrenci belgesi yükleme / durum sayfası. */
    public function show(Request $request): View
    {
        $member = $request->user()->member;
        $verification = $member?->studentVerification;

        return view('member.ogrenci-dogrulama', compact('member', 'verification'));
    }

    /** Belge + öğrenci bilgilerini kaydeder; doğrulama "İnceleniyor"a düşer. */
    public function store(Request $request): RedirectResponse
    {
        $member = $request->user()->member;
        abort_unless($member, 403);

        $existing = $member->studentVerification;

        // Onaylı ve hâlâ geçerli bir kayıt varken yeniden yüklemeye gerek yok.
        if ($existing && $existing->isValid()) {
            return back()->with('status', 'Öğrenciliğin zaten onaylı. Yeni belgeye gerek yok.');
        }

        $data = $request->validate([
            'student_no' => [
                'required', 'string', 'max:50',
                Rule::unique('student_verifications', 'student_no')->ignore($existing?->id),
            ],
            'school' => ['required', 'string', 'max:255'],
            'tc_no' => ['required', 'digits:11'],
            'document_barcode' => [
                'nullable', 'string', 'max:100',
                Rule::unique('student_verifications', 'document_barcode')->ignore($existing?->id),
            ],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ], [
            'student_no.unique' => 'Bu öğrenci numarası sistemde kayıtlı. Her öğrenci numarası yalnızca bir kez kullanılabilir.',
            'document_barcode.unique' => 'Bu belge barkod numarası daha önce kullanılmış.',
            'tc_no.digits' => 'T.C. kimlik numarası 11 haneli olmalıdır.',
        ]);

        // Eski belgeyi temizle (yeniden yüklemede).
        if ($existing && $existing->document_path) {
            Storage::disk('local')->delete($existing->document_path);
        }

        // Özel diske kaydet — belge KVKK kapsamında hassas veridir, herkese açık DEĞİL.
        $path = $request->file('document')->store('student-docs/'.$member->id, 'local');

        $member->studentVerification()->updateOrCreate(
            ['member_id' => $member->id],
            [
                'student_no' => $data['student_no'],
                'school' => $data['school'],
                'tc_no' => $data['tc_no'],
                'document_barcode' => $data['document_barcode'] ?? null,
                'document_path' => $path,
                'status' => StudentVerificationStatus::Beklemede,
                'rejection_reason' => null,
                'valid_until' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        return redirect()->route('uye.ogrenci-dogrulama')
            ->with('status', 'Belgen alındı. Yönetim onayından sonra öğrenci avantajların aktifleşecek.');
    }
}
