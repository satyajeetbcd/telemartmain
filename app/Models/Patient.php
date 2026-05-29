<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'blood_group',
        'medical_history',
        'allergies',
        'current_medications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'insurance_provider',
        'insurance_policy_number',
        'status',
        'notes',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the appointments for this patient
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(DoctorReview::class, 'patient_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the payment transaction log for this patient
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user account associated with this patient
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate unique patient ID
     */
    public static function generatePatientId(): string
    {
        do {
            $patientId = 'PAT-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('patient_id', $patientId)->exists());

        return $patientId;
    }

    /**
     * Attributes to include in audit
     */
    protected $auditInclude = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'blood_group',
        'status',
    ];

    /**
     * Attributes to exclude from audit
     */
    protected $auditExclude = ['created_at', 'updated_at', 'patient_id'];
}
