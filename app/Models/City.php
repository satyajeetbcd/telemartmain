<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the state that owns this city
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    protected $auditInclude = ['state_id', 'name', 'code', 'is_active'];
    protected $auditExclude = ['created_at', 'updated_at'];
}
