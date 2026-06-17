<?php

namespace App\Http\Controllers\Api\Member;

use App\Enums\StudentVerificationStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentVerificationController extends Controller
{
    /** Mevcut öğrenci doğrulama durumu. */
    public function show(Request $request): JsonResponse
    {
        $member = $request->user()->member;
        $v = $member?->studentVerification;

        return response()->json([
            'verification' => $v ? [
                'status' => $v->status->value,
                'status_label' => $v->status->label(),
                'student_no' => $v->student_no,
                'school' => $v->school,
                'valid_until' => $v->valid_until?->toDateString(),
                'rejection_reason' => $v->rejection_reason,
                'is_valid' => $v->isValid(),
                'created_at' => $v->created_at?->toIso8601String(),
            ] : null,
        ]);
    }

    /** Belge + öğrenci bilgilerini yükle; durum "İnceleniyor"a düşer. */
    public function store(Request $request): JsonResponse
    {
        $member = $request->user()->member;
        abort_unless($member, 403);

        $existing = $member->studentVerification;

        if ($existing && $existing->isValid()) {
            return response()->json(['message' => 'Öğrenciliğin zaten onaylı. Yeni belgeye gerek yok.']);
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

        if ($existing && $existing->document_path) {
            Storage::disk('local')->delete($existing->document_path);
        }

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

        return response()->json(['message' => 'Belgen alındı. Yönetim onayından sonra öğrenci avantajların aktifleşecek.'], 201);
    }
}
