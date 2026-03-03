<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorReview extends BaseModel
{
    protected $table = 'doctor_reviews';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_id',
        'rating',
        'comment',
        'doctor_reply',
        'replied_at',
        'is_visible',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_visible' => 'boolean',
            'replied_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if review has a reply
     */
    public function hasReply(): bool
    {
        return !empty($this->doctor_reply);
    }

    /**
     * Get average rating for a doctor (only approved reviews)
     */
    public static function getAverageRating($doctorId): float
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->avg('rating') ?? 0;
    }

    /**
     * Get total review count for a doctor (only approved reviews)
     */
    public static function getReviewCount($doctorId): int
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->count();
    }

    /**
     * Get rating distribution for a doctor (only approved reviews)
     */
    public static function getRatingDistribution($doctorId): array
    {
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = self::where('doctor_id', $doctorId)
                ->where('rating', $i)
                ->where('is_visible', true)
                ->where('approval_status', 'approved')
                ->count();
        }
        return $distribution;
    }

    /**
     * Check if review is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if review is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if review is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    protected $auditInclude = [
        'rating',
        'comment',
        'doctor_reply',
        'is_visible',
    ];

    protected $auditExclude = ['created_at', 'updated_at'];
}
