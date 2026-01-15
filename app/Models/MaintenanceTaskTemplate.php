<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTaskTemplate extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'report_type_id',
        'task_name',
        'task_description',
        'estimated_time_minutes',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_time_minutes' => 'integer',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the report type this template belongs to.
     */
    public function reportType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReportType::class, 'report_type_id');
    }

    /**
     * Scope active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered templates.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
