<?php

namespace App\Models\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically set tenant_id on creation
        static::creating(function (Model $model) {
            $tenantId = request()->get('current_tenant_id');
            if (!$model->tenant_id && $tenantId) {
                $model->tenant_id = $tenantId;
            }
        });

        // Apply global scope to filter by tenant_id (skip for super admins)
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = request()->get('current_tenant_id');
            if ($tenantId) {
                $builder->where($builder->getQuery()->from . '.tenant_id', $tenantId);
            }
        });
    }

    /**
     * Get the tenant that owns the model.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
