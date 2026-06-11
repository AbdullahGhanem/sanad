<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Adds a clinical audit trail to a model via spatie/laravel-activitylog.
 *
 * Models may set `$auditLogName` to group their entries and `$auditLogOnly` to
 * whitelist attributes — important for models holding secrets (API keys) or large
 * values (embedding vectors) that must never reach the audit log.
 */
trait Auditable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName(property_exists($this, 'auditLogName') ? $this->auditLogName : class_basename($this));

        return property_exists($this, 'auditLogOnly')
            ? $options->logOnly($this->auditLogOnly)
            : $options->logFillable();
    }
}
