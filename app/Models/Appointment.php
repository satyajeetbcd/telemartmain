<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends BaseModel
{
    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'status',
        'reason',
        'notes',
        'prescription',
        'consultation_fee',
        'payment_status',
        'cancellation_reason',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'zoom_meeting_id',
        'zoom_meeting_uuid',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_meeting_password',
        'zoom_meeting_created_at',
        'zoom_meeting_status',
        'zoom_meeting_started_at',
        'zoom_meeting_ended_at',
        'zoom_participant_doctor_joined_at',
        'zoom_participant_patient_joined_at',
        'zoom_meeting_duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'consultation_fee' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'zoom_meeting_created_at' => 'datetime',
            'zoom_meeting_started_at' => 'datetime',
            'zoom_meeting_ended_at' => 'datetime',
            'zoom_participant_doctor_joined_at' => 'datetime',
            'zoom_participant_patient_joined_at' => 'datetime',
            'zoom_meeting_duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the patient for this appointment
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the review for this appointment
     */
    public function review()
    {
        return $this->hasOne(DoctorReview::class, 'appointment_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get all payment attempts for this appointment
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the most recent payment attempt for this appointment
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Generate unique appointment number
     */
    public static function generateAppointmentNumber(): string
    {
        do {
            $number = 'APT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('appointment_number', $number)->exists());

        return $number;
    }

    /**
     * Check if appointment is upcoming
     */
    public function isUpcoming(): bool
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        return $appointmentDateTime->isFuture() && in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if appointment is past
     */
    public function isPast(): bool
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        return $appointmentDateTime->isPast();
    }

    /**
     * Get formatted appointment date and time
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->appointment_date->format('M d, Y') . ' at ' . date('h:i A', strtotime($this->appointment_time));
    }

    /**
     * Attributes to include in audit
     */
    protected $auditInclude = [
        'status',
        'appointment_date',
        'appointment_time',
        'reason',
        'consultation_fee',
        'payment_status',
    ];

    /**
     * Attributes to exclude from audit
     */
    protected $auditExclude = ['created_at', 'updated_at', 'appointment_number'];
}
