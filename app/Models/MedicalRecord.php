<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'record_number',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'record_type',
        'title',
        'description',
        'diagnosis',
        'symptoms',
        'vitals',
        'prescription',
        'treatment_plan',
        'notes',
        'attachments',
        'record_date',
        'follow_up_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'follow_up_date' => 'date',
            'vitals' => 'array',
            'attachments' => 'array',
        ];
    }

    protected $auditInclude = [
        'record_type', 'title', 'diagnosis', 'symptoms',
        'prescription', 'treatment_plan', 'status',
    ];

    protected $auditExclude = ['created_at', 'updated_at', 'record_number'];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateRecordNumber(): string
    {
        do {
            $number = 'MR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('record_number', $number)->exists());

        return $number;
    }

    public static function getRecordTypes(): array
    {
        return [
            'consultation' => 'Consultation',
            'lab_report' => 'Lab Report',
            'prescription' => 'Prescription',
            'diagnosis' => 'Diagnosis',
            'discharge_summary' => 'Discharge Summary',
            'imaging' => 'Imaging',
            'vaccination' => 'Vaccination',
            'surgical' => 'Surgical',
            'follow_up' => 'Follow Up',
            'other' => 'Other',
        ];
    }
}
