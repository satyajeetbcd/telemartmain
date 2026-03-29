<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'consultation_number',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'is_followup',
        'chief_complaints',
        'patient_history',
        'personal_history',
        'family_history',
        'allergies',
        'medications',
        'query',
        'location_preference',
        'state',
        'opd',
        'health_records',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_followup' => 'boolean',
            'chief_complaints' => 'array',
            'patient_history' => 'array',
            'personal_history' => 'array',
            'family_history' => 'array',
            'allergies' => 'array',
            'medications' => 'array',
            'health_records' => 'array',
        ];
    }

    protected $auditInclude = [
        'status', 'doctor_id', 'appointment_id',
    ];

    protected $auditExclude = ['created_at', 'updated_at'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public static function generateConsultationNumber(): string
    {
        do {
            $number = 'CON-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('consultation_number', $number)->exists());

        return $number;
    }
}
