<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    use HasFactory;

    protected $fillable = [
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
     * Get all cities for this state
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get active cities for this state
     */
    public function activeCities(): HasMany
    {
        return $this->hasMany(City::class)->where('is_active', true);
    }

    protected $auditInclude = ['name', 'code', 'is_active'];
    protected $auditExclude = ['created_at', 'updated_at'];
}
