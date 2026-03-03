<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'prescription_number',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'diagnosis',
        'notes',
        'prescription_date',
        'valid_until',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'prescription_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    protected $auditInclude = [
        'diagnosis', 'status', 'prescription_date', 'valid_until',
    ];

    protected $auditExclude = ['created_at', 'updated_at', 'prescription_number'];

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

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class)->orderBy('sort_order');
    }

    public static function generatePrescriptionNumber(): string
    {
        do {
            $number = 'RX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('prescription_number', $number)->exists());

        return $number;
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public static function getRouteOptions(): array
    {
        return [
            'oral' => 'Oral',
            'topical' => 'Topical',
            'injection' => 'Injection',
            'inhaler' => 'Inhaler',
            'drops' => 'Drops',
            'sublingual' => 'Sublingual',
            'rectal' => 'Rectal',
            'intravenous' => 'Intravenous (IV)',
            'other' => 'Other',
        ];
    }
}
