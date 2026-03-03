<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends BaseModel
{
    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'role_id',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Attributes to include in audit
     */
    protected $auditInclude = ['email', 'role_id', 'expires_at', 'accepted_at'];

    /**
     * Attributes to exclude from audit (in addition to BaseModel defaults)
     */
    protected $auditExclude = ['created_at', 'updated_at', 'token'];
}
