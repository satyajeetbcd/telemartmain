# Audit System Setup Guide

This application uses **Laravel Auditing** (`owen-it/laravel-auditing`) to automatically track all changes to models.

## Base Classes

Two base classes have been created to ensure all models automatically have auditing enabled:

### 1. `BaseModel` - For Regular Models
Use this for standard Eloquent models that don't need authentication.

```php
<?php

namespace App\Models;

class YourModel extends BaseModel
{
    protected $fillable = ['name', 'description'];
    
    // Optional: Override audit configuration
    protected $auditInclude = ['name', 'description'];
    protected $auditExclude = ['created_at', 'updated_at', 'internal_id'];
}
```

### 2. `AuditableAuthenticatable` - For User Models
Use this for models that extend `Authenticatable` (like User models).

```php
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends AuditableAuthenticatable
{
    use Notifiable, HasRoles;
    
    // Optional: Override audit configuration
    protected $auditInclude = ['name', 'email'];
    protected $auditExclude = ['password', 'remember_token', 'created_at', 'updated_at'];
}
```

## Default Behavior

By default, all models extending these base classes will:
- ✅ Automatically audit `created`, `updated`, and `deleted` events
- ✅ Exclude `created_at` and `updated_at` from audits (unless overridden)
- ✅ Track who made the change (user_id)
- ✅ Store IP address and user agent
- ✅ Store old and new values

## Customization

### Include Specific Attributes Only
```php
protected $auditInclude = ['name', 'email', 'status'];
```

### Exclude Specific Attributes
```php
protected $auditExclude = ['password', 'token', 'secret_key'];
```

### Custom Audit Events
Override the `getAuditEvents()` method:
```php
public function getAuditEvents(): array
{
    return ['created', 'updated']; // Exclude 'deleted'
}
```

## Viewing Audit Logs

All audit logs are accessible via:
- **Route**: `/activity-logs`
- **Controller**: `ActivityLogController`
- **Model**: `OwenIt\Auditing\Models\Audit`

## Current Models with Auditing

- ✅ `User` - Extends `AuditableAuthenticatable`
- ✅ `Invitation` - Extends `BaseModel`

## Important Notes

1. **Always extend the base classes** - Don't extend `Model` or `Authenticatable` directly
2. **Sensitive data** - Always exclude passwords, tokens, and other sensitive information
3. **Performance** - Auditing adds a small overhead. For high-traffic models, consider excluding non-critical attributes
4. **Database** - All audits are stored in the `audits` table

## Example: Creating a New Model

```php
<?php

namespace App\Models;

class Patient extends BaseModel
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'medical_record_number',
    ];

    // Only audit these specific fields
    protected $auditInclude = [
        'name',
        'email',
        'phone',
        'date_of_birth',
    ];

    // Exclude sensitive/internal fields
    protected $auditExclude = [
        'created_at',
        'updated_at',
        'medical_record_number', // Internal ID, no need to audit
    ];
}
```

That's it! The model will automatically track all changes.

