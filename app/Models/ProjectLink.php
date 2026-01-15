<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectLink extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'type',
        'label',
        'url',
    ];

    /**
     * Get the project that owns the link.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope for production links.
     */
    public function scopeProduction($query)
    {
        return $query->where('type', 'production');
    }

    /**
     * Scope for staging links.
     */
    public function scopeStaging($query)
    {
        return $query->where('type', 'staging');
    }

    /**
     * Scope for demo links.
     */
    public function scopeDemo($query)
    {
        return $query->where('type', 'demo');
    }
}
