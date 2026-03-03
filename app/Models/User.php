<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends AuditableAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'invitation_token',
        'invited_by',
        'invited_at',
        'phone',
        'date_of_birth',
        'aadhar_card_number',
        'address',
        'state_id',
        'city_id',
        'city', // Keep for backward compatibility
        'state', // Keep for backward compatibility
        'postal_code',
        'country',
        'specialization',
        'qualifications',
        'bio',
        'experience_years',
        'consultation_fee',
        'license_number',
        'profile_image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'invited_at' => 'datetime',
            'date_of_birth' => 'date',
            'experience_years' => 'integer',
            'consultation_fee' => 'decimal:2',
        ];
    }

    /**
     * Attributes to include in audit
     */
    protected $auditInclude = ['name', 'email', 'phone', 'date_of_birth', 'aadhar_card_number', 'address', 'city', 'state', 'postal_code', 'country'];

    /**
     * Attributes to exclude from audit (in addition to base class defaults)
     */
    protected $auditExclude = ['password', 'remember_token', 'created_at', 'updated_at'];

    /**
     * Get the KYC documents for this doctor
     */
    public function kycDocuments()
    {
        return $this->hasMany(\App\Models\DoctorKyc::class, 'doctor_id');
    }

    /**
     * Get the profile changes for this doctor
     */
    public function profileChanges()
    {
        return $this->hasMany(\App\Models\DoctorProfileChange::class, 'doctor_id');
    }

    /**
     * Get pending profile changes
     */
    public function pendingProfileChanges()
    {
        return $this->hasMany(\App\Models\DoctorProfileChange::class, 'doctor_id')
            ->where('status', 'pending');
    }

    /**
     * Get the reviews for this doctor.
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\DoctorReview::class, 'doctor_id');
    }

    /**
     * Get the state that the user belongs to
     */
    public function stateRelation()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id');
    }

    /**
     * Get the city that the user belongs to
     */
    public function cityRelation()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id');
    }
}
