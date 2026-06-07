<?php

namespace App\Models;

use App\Enums\StudentVerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentVerification extends Model
{
    protected $fillable = [
        'member_id', 'student_no', 'school', 'tc_no', 'document_barcode',
        'document_path', 'status', 'valid_until', 'rejection_reason',
        'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => StudentVerificationStatus::class,
            'valid_until' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === StudentVerificationStatus::Beklemede;
    }

    /** Onaylı ve geçerlilik süresi geçmemiş mi? */
    public function isValid(): bool
    {
        return $this->status === StudentVerificationStatus::Onayli
            && (! $this->valid_until || ! $this->valid_until->isPast());
    }

    /** Onaylıydı ama dönem geçerliliği doldu — yeniden belge istenir. */
    public function isExpired(): bool
    {
        return $this->status === StudentVerificationStatus::Onayli
            && $this->valid_until && $this->valid_until->isPast();
    }
}
