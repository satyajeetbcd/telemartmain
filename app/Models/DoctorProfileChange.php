<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorProfileChange extends BaseModel
{
    protected $fillable = [
        'doctor_id',
        'changes',
        'original_values',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'original_values' => 'array',
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

    protected $auditInclude = [
        'status',
        'rejection_reason',
        'approved_by',
    ];

    protected $auditExclude = ['changes', 'original_values', 'created_at', 'updated_at'];
}
