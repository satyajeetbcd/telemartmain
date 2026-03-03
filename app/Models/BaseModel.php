<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Base Model class with Auditing enabled
 * 
 * All models should extend this class to automatically have audit logging enabled.
 * Override $auditInclude and $auditExclude properties in child models to customize.
 */
abstract class BaseModel extends Model implements Auditable
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
    protected $auditExclude = ['created_at', 'updated_at'];

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

