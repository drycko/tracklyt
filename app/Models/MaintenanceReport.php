<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceReport extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'report_type_id',
        'created_by',
        'assigned_to',
        'report_number',
        'scheduled_date',
        'status',
        'notes',
        'started_at',
        'completed_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate report number on creation
        static::creating(function ($report) {
            if (!$report->report_number) {
                $report->report_number = static::generateReportNumber();
            }
        });
    }

    /**
     * Generate a unique report number.
     */
    public static function generateReportNumber(): string
    {
        $year = date('Y');
        $lastReport = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastReport ? intval(substr($lastReport->report_number, -3)) + 1 : 1;

        return 'MAINT-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the project this report belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the report type.
     */
    public function reportType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReportType::class, 'report_type_id');
    }

    /**
     * Get the user who created this report.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this report.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the tasks for this report.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(MaintenanceReportTask::class)
            ->orderBy('display_order');
    }

    /**
     * Check if report is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if report is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if report is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if report has been sent.
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->tasks()->where('is_completed', true)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get total time spent.
     */
    public function getTotalTimeSpentAttribute(): int
    {
        return $this->tasks()->sum('time_spent');
    }

    /**
     * Mark report as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark report as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Update completion percentage (recalculates from tasks).
     */
    public function updateCompletionPercentage(): void
    {
        // This method exists to maintain compatibility with controller calls
        // The percentage is calculated dynamically via the accessor
        // Just refresh the model to ensure the accessor has latest data
        $this->refresh();
    }

    /**
     * Scope draft reports.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope in progress reports.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope completed reports.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope sent reports.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
