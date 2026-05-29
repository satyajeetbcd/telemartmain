<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Raw Razorpay webhook delivery log. This IS an audit log itself, so it does not
 * extend BaseModel (no need to audit the audit) and only tracks created_at.
 */
class PaymentEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'payment_id',
        'razorpay_event_id',
        'event',
        'signature_valid',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'payload' => 'array',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
