<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TimeEntry extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'project_id',
        'task_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_billable',
        'notes',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'locked_at' => 'datetime',
            'is_billable' => 'boolean',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate duration before saving
        static::saving(function ($entry) {
            if ($entry->start_time && $entry->end_time) {
                $start = Carbon::parse($entry->start_time);
                $end = Carbon::parse($entry->end_time);
                $entry->duration_minutes = $start->diffInMinutes($end);
            }
        });

        // Prevent updates to locked entries
        static::updating(function ($entry) {
            if ($entry->isLocked() && $entry->isDirty()) {
                throw new \Exception('Cannot update locked time entry.');
            }
        });

        // Prevent deletion of locked entries
        static::deleting(function ($entry) {
            if ($entry->isLocked()) {
                throw new \Exception('Cannot delete locked time entry.');
            }
        });
    }

    /**
     * Get the user that owns the time entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that owns the time entry.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the task that owns the time entry.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the invoice item that includes this time entry.
     */
    public function invoiceItem()
    {
        return $this->hasOne(InvoiceItem::class);
    }

    /**
     * Check if time entry is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    /**
     * Lock the time entry.
     */
    public function lock(): void
    {
        if (!$this->isLocked()) {
            $this->locked_at = now();
            $this->saveQuietly(); // Skip validation
        }
    }

    /**
     * Unlock the time entry (admin only).
     */
    public function unlock(): void
    {
        $this->locked_at = null;
        $this->saveQuietly();
    }

    /**
     * Get duration in hours.
     */
    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 2);
    }

    /**
     * Check if time entry is currently running.
     */
    public function isRunning(): bool
    {
        return $this->start_time && !$this->end_time;
    }

    /**
     * Stop a running time entry.
     */
    public function stop(): void
    {
        if ($this->isRunning()) {
            $this->end_time = now();
            $this->save();
        }
    }

    /**
     * Scope for locked entries.
     */
    public function scopeLocked($query)
    {
        return $query->whereNotNull('locked_at');
    }

    /**
     * Scope for unlocked entries.
     */
    public function scopeUnlocked($query)
    {
        return $query->whereNull('locked_at');
    }

    /**
     * Scope for billable entries.
     */
    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    /**
     * Scope for running entries.
     */
    public function scopeRunning($query)
    {
        return $query->whereNotNull('start_time')->whereNull('end_time');
    }
}
