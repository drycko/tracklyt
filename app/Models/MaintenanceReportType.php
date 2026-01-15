<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceReportType extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'report_image',
        'footer_text',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the task templates for this report type.
     */
    public function taskTemplates(): HasMany
    {
        return $this->hasMany(MaintenanceTaskTemplate::class, 'report_type_id')
            ->orderBy('display_order');
    }

    /**
     * Get the maintenance reports using this type.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class, 'report_type_id');
    }

    /**
     * Scope active report types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
