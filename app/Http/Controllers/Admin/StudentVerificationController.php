<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberCategory;
use App\Enums\StudentVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\StudentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class StudentVerificationController extends Controller
{
    /** Doğrulama talepleri — varsayılan: bekleyenler önce. */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $verifications = StudentVerification::with('member.user', 'reviewer')
            ->when(
                in_array($status, array_column(StudentVerificationStatus::cases(), 'value'), true),
                fn ($q) => $q->where('status', $status)
            )
            ->orderByRaw("CASE status WHEN 'beklemede' THEN 0 WHEN 'reddedildi' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.student-verifications.index', [
            'verifications' => $verifications,
            'statuses' => StudentVerificationStatus::cases(),
            'activeStatus' => $status,
        ]);
    }

    /** Yüklenen belgeyi yalnızca yetkili personele gösterir (özel diskten). */
    public function document(StudentVerification $verification): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($verification->document_path), 404);

        return Storage::disk('local')->response($verification->document_path);
    }

    /** Onayla / reddet. Onayda belge geçerlilik tarihi (dönem sonu) zorunlu. */
    public function update(Request $request, StudentVerification $verification): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:onayla,reddet'],
            'valid_until' => ['required_if:decision,onayla', 'nullable', 'date', 'after:today'],
            'rejection_reason' => ['required_if:decision,reddet', 'nullable', 'string', 'max:255'],
        ], [
            'valid_until.required_if' => 'Onaylamak için belgenin geçerlilik (dönem sonu) tarihini gir.',
            'valid_until.after' => 'Geçerlilik tarihi bugünden ileri olmalı.',
            'rejection_reason.required_if' => 'Reddetmek için bir sebep gir.',
        ]);

        if ($data['decision'] === 'onayla') {
            $verification->update([
                'status' => StudentVerificationStatus::Onayli,
                'valid_until' => $data['valid_until'],
                'rejection_reason' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            // Onaylanınca üye öğrenci kategorisine çekilir ve aktifleşir.
            $verification->member?->update([
                'category' => MemberCategory::Ogrenci,
                'status' => 'aktif',
            ]);

            return back()->with('status', 'Öğrencilik onaylandı.');
        }

        $verification->update([
            'status' => StudentVerificationStatus::Reddedildi,
            'rejection_reason' => $data['rejection_reason'],
            'valid_until' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Öğrencilik talebi reddedildi.');
    }
}
