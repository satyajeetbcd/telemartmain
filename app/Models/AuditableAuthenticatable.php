<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Base Authenticatable class with Auditing enabled
 * 
 * Use this class for User models that need authentication and auditing.
 * Override $auditInclude and $auditExclude properties in child models to customize.
 */
abstract class AuditableAuthenticatable extends Authenticatable implements Auditable
{
    use AuditableTrait;

    /**
     * Attributes to include in audit.
     * If empty, all attributes except those in $auditExclude will be audited.
     * 
     * @var array
     */
    protected $auditInclude = [];

    /**
     * Attributes to exclude from audit.
     * Common exclusions: passwords, tokens, timestamps (unless needed)
     * 
     * @var array
     */
    protected $auditExclude = ['password', 'remember_token', 'created_at', 'updated_at'];

    /**
     * Get the events that trigger an audit.
     * Override this method in child models to customize.
     * 
     * @return array
     */
    public function getAuditEvents(): array
    {
        return ['created', 'updated', 'deleted'];
    }
}

