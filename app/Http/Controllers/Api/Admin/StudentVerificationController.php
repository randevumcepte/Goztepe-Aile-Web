<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\MemberCategory;
use App\Enums\StudentVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\StudentVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentVerificationController extends Controller
{
    /** Doğrulama talepleri — varsayılan: bekleyenler önce. */
    public function index(Request $request): JsonResponse
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

        return response()->json([
            'items' => $verifications->getCollection()->map(fn (StudentVerification $v) => $this->payload($v))->values(),
            'current_page' => $verifications->currentPage(),
            'last_page' => $verifications->lastPage(),
            'total' => $verifications->total(),
            'statuses' => array_map(fn (StudentVerificationStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ], StudentVerificationStatus::cases()),
            'active_status' => $status,
        ]);
    }

    /** Onayla / reddet. Onayda belge geçerlilik tarihi (dönem sonu) zorunlu. */
    public function update(Request $request, StudentVerification $verification): JsonResponse
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

            return response()->json([
                'message' => 'Öğrencilik onaylandı.',
                'item' => $this->payload($verification->fresh('member.user', 'reviewer')),
            ]);
        }

        $verification->update([
            'status' => StudentVerificationStatus::Reddedildi,
            'rejection_reason' => $data['rejection_reason'],
            'valid_until' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Öğrencilik talebi reddedildi.',
            'item' => $this->payload($verification->fresh('member.user', 'reviewer')),
        ]);
    }

    private function payload(StudentVerification $v): array
    {
        return [
            'id' => $v->id,
            'member_id' => $v->member_id,
            'member_name' => $v->member?->user?->name,
            'member_no' => $v->member?->member_no,
            'student_no' => $v->student_no,
            'school' => $v->school,
            'tc_no' => $v->tc_no,
            'document_barcode' => $v->document_barcode,
            'status' => $v->status?->value,
            'status_label' => $v->status?->label(),
            'valid_until' => $v->valid_until?->toDateString(),
            'rejection_reason' => $v->rejection_reason,
            'reviewed_at' => $v->reviewed_at?->toIso8601String(),
            'created_at' => $v->created_at?->toIso8601String(),
        ];
    }
}
