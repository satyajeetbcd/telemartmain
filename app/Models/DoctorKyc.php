<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorKyc extends BaseModel
{
    protected $table = 'doctor_kyc_documents';

    protected $fillable = [
        'doctor_id',
        'document_type',
        'document_name',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'aadhar_front' => 'Aadhar Card (Front)',
            'aadhar_back' => 'Aadhar Card (Back)',
            'degree' => 'Degree Certificate',
            'pan' => 'PAN Card',
            default => ucfirst($this->document_type),
        };
    }

    protected $auditInclude = [
        'document_type',
        'document_name',
        'status',
        'rejection_reason',
        'approved_by',
    ];

    protected $auditExclude = ['file_path', 'created_at', 'updated_at'];
}
