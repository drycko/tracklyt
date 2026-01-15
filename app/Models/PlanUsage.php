<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PlanUsage extends Model
{
    use BelongsToTenant;

    protected $table = 'plan_usage';

    protected $fillable = [
        'tenant_id',
        'month',
        'users_count',
        'projects_count',
        'clients_count',
        'tracked_hours',
        'invoices_count',
        'snapshot_at',
    ];

    protected function casts(): array
    {
        return [
            'users_count' => 'integer',
            'projects_count' => 'integer',
            'clients_count' => 'integer',
            'tracked_hours' => 'integer',
            'invoices_count' => 'integer',
            'snapshot_at' => 'datetime',
        ];
    }

    /**
     * Get or create usage record for current month.
     */
    public static function getOrCreateForCurrentMonth(int $tenantId): self
    {
        $month = now()->format('Y-m');

        return static::firstOrCreate(
            ['tenant_id' => $tenantId, 'month' => $month],
            [
                'users_count' => 0,
                'projects_count' => 0,
                'clients_count' => 0,
                'tracked_hours' => 0,
                'invoices_count' => 0,
            ]
        );
    }

    /**
     * Take a snapshot of current usage.
     */
    public function takeSnapshot(): void
    {
        $tenant = Tenant::find($this->tenant_id);

        $this->update([
            'users_count' => User::where('tenant_id', $this->tenant_id)->count(),
            'projects_count' => Project::where('tenant_id', $this->tenant_id)->count(),
            'clients_count' => Client::where('tenant_id', $this->tenant_id)->count(),
            'tracked_hours' => $this->getMonthlyTrackedMinutes(),
            'invoices_count' => $this->getMonthlyInvoicesCount(),
            'snapshot_at' => now(),
        ]);
    }

    /**
     * Get tracked minutes for current month.
     */
    protected function getMonthlyTrackedMinutes(): int
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return TimeEntry::where('tenant_id', $this->tenant_id)
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->sum('duration_minutes') ?? 0;
    }

    /**
     * Get invoices created this month.
     */
    protected function getMonthlyInvoicesCount(): int
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return Invoice::where('tenant_id', $this->tenant_id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
    }

    /**
     * Check if limit is reached for a specific resource.
     */
    public function hasReachedLimit(string $resource, int $limit): bool
    {
        if ($limit === -1) {
            return false; // Unlimited
        }

        $resourceKey = $resource . '_count';
        if ($resource === 'monthly_hours') {
            $resourceKey = 'tracked_hours';
            $currentValue = $this->tracked_hours / 60; // Convert minutes to hours
        } else {
            $currentValue = $this->{$resourceKey} ?? 0;
        }

        return $currentValue >= $limit;
    }

    /**
     * Get remaining quota for a resource.
     */
    public function getRemainingQuota(string $resource, int $limit): int
    {
        if ($limit === -1) {
            return PHP_INT_MAX; // Unlimited
        }

        $resourceKey = $resource . '_count';
        if ($resource === 'monthly_hours') {
            $resourceKey = 'tracked_hours';
            $currentValue = $this->tracked_hours / 60; // Convert minutes to hours
        } else {
            $currentValue = $this->{$resourceKey} ?? 0;
        }

        return max(0, $limit - $currentValue);
    }

    /**
     * Scope to get current month's usage.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where('month', now()->format('Y-m'));
    }

    /**
     * Scope to get specific month's usage.
     */
    public function scopeForMonth($query, string $month)
    {
        return $query->where('month', $month);
    }
}
