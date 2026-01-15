<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReportTask extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'maintenance_report_id',
        'task_name',
        'task_description',
        'comments',
        'screenshots',
        'time_spent_minutes',
        'estimated_time_minutes',
        'display_order',
        'is_completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'screenshots' => 'array',
            'time_spent_minutes' => 'integer',
            'estimated_time_minutes' => 'integer',
            'display_order' => 'integer',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the report this task belongs to.
     */
    public function maintenanceReport(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'maintenance_report_id');
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark task as incomplete.
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Add a screenshot.
     */
    public function addScreenshot(string $path): void
    {
        $screenshots = $this->screenshots ?? [];
        $screenshots[] = $path;
        $this->update(['screenshots' => $screenshots]);
    }

    /**
     * Remove a screenshot.
     */
    public function removeScreenshot(string $path): void
    {
        $screenshots = $this->screenshots ?? [];
        $screenshots = array_filter($screenshots, fn($s) => $s !== $path);
        $this->update(['screenshots' => array_values($screenshots)]);
    }

    /**
     * Scope completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope incomplete tasks.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope ordered tasks.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
