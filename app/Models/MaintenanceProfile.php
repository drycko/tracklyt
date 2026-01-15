<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceProfile extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'maintenance_type',
        'monthly_hours',
        'rate',
        'sla_notes',
        'rollover_hours',
        'start_date',
        'last_reset_date',
    ];

    protected function casts(): array
    {
        return [
            'monthly_hours' => 'decimal:2',
            'rate' => 'decimal:2',
            'rollover_hours' => 'decimal:2',
            'start_date' => 'date',
            'last_reset_date' => 'date',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set initial reset date on creation
        static::creating(function ($profile) {
            if (!$profile->last_reset_date) {
                $profile->last_reset_date = $profile->start_date ?? now();
            }
        });
    }

    /**
     * Get the project that owns the maintenance profile.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get time entries for current billing period.
     */
    public function currentPeriodTimeEntries()
    {
        $startOfPeriod = $this->getCurrentPeriodStart();
        
        return $this->project->timeEntries()
            ->where('start_time', '>=', $startOfPeriod)
            ->where('is_billable', true);
    }

    /**
     * Get the start date of current billing period.
     */
    public function getCurrentPeriodStart(): Carbon
    {
        if (!$this->last_reset_date) {
            return Carbon::parse($this->start_date);
        }

        $lastReset = Carbon::parse($this->last_reset_date);
        $now = now();

        // Calculate how many months have passed
        $monthsPassed = $lastReset->diffInMonths($now);
        
        return $lastReset->copy()->addMonths($monthsPassed);
    }

    /**
     * Get hours used in current period.
     */
    public function getUsedHoursAttribute(): float
    {
        $minutes = $this->currentPeriodTimeEntries()->sum('duration_minutes');
        return round($minutes / 60, 2);
    }

    /**
     * Get total available hours (monthly + rollover).
     */
    public function getTotalAvailableHoursAttribute(): float
    {
        if ($this->maintenance_type === 'hourly') {
            return 0; // No limit for hourly
        }

        return round($this->monthly_hours + $this->rollover_hours, 2);
    }

    /**
     * Get remaining hours in current period.
     */
    public function getRemainingHoursAttribute(): float
    {
        if ($this->maintenance_type === 'hourly') {
            return 0; // No limit for hourly
        }

        $remaining = $this->total_available_hours - $this->used_hours;
        return round(max(0, $remaining), 2);
    }

    /**
     * Get percentage of hours used.
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->maintenance_type === 'hourly' || $this->total_available_hours == 0) {
            return 0;
        }

        return round(($this->used_hours / $this->total_available_hours) * 100, 1);
    }

    /**
     * Check if retainer needs to be reset (new month).
     */
    public function needsReset(): bool
    {
        if ($this->maintenance_type === 'hourly') {
            return false;
        }

        $currentPeriodStart = $this->getCurrentPeriodStart();
        $lastReset = Carbon::parse($this->last_reset_date);

        return $currentPeriodStart->greaterThan($lastReset);
    }

    /**
     * Reset monthly retainer (handle rollover).
     */
    public function resetMonthly(): void
    {
        if ($this->maintenance_type === 'hourly') {
            return;
        }

        // Calculate unused hours for rollover
        $unusedHours = $this->remaining_hours;
        
        // Update rollover (you can add a max rollover limit if needed)
        $this->rollover_hours = $unusedHours;
        $this->last_reset_date = now();
        $this->save();
    }

    /**
     * Check if retainer is over limit.
     */
    public function isOverLimit(): bool
    {
        if ($this->maintenance_type === 'hourly') {
            return false;
        }

        return $this->used_hours > $this->total_available_hours;
    }

    /**
     * Get overage hours.
     */
    public function getOverageHoursAttribute(): float
    {
        if ($this->maintenance_type === 'hourly') {
            return 0;
        }

        $overage = $this->used_hours - $this->total_available_hours;
        return round(max(0, $overage), 2);
    }

    /**
     * Scope for retainer type.
     */
    public function scopeRetainer($query)
    {
        return $query->where('maintenance_type', 'retainer');
    }

    /**
     * Scope for hourly type.
     */
    public function scopeHourly($query)
    {
        return $query->where('maintenance_type', 'hourly');
    }
}
