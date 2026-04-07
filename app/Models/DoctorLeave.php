<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorLeave extends BaseModel
{
    protected $fillable = [
        'doctor_id',
        'leave_date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'leave_date' => 'date',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    protected $auditInclude = ['doctor_id', 'leave_date', 'reason'];
    protected $auditExclude = ['created_at', 'updated_at'];
}
