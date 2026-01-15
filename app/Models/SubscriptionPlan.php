<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'billing_cycle',
        'max_users',
        'max_projects',
        'max_clients',
        'max_monthly_hours',
        'max_invoices_per_month',
        'max_twilio_messages_per_month',
        'has_time_tracking',
        'has_invoicing',
        'has_client_portal',
        'has_maintenance_reports',
        'has_advanced_reporting',
        'has_api_access',
        'has_white_label',
        'has_priority_support',
        'display_order',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'max_users' => 'integer',
            'max_projects' => 'integer',
            'max_clients' => 'integer',
            'max_monthly_hours' => 'integer',
            'max_invoices_per_month' => 'integer',
            'max_twilio_messages_per_month' => 'integer',
            'has_time_tracking' => 'boolean',
            'has_invoicing' => 'boolean',
            'has_client_portal' => 'boolean',
            'has_maintenance_reports' => 'boolean',
            'has_advanced_reporting' => 'boolean',
            'has_api_access' => 'boolean',
            'has_white_label' => 'boolean',
            'has_priority_support' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the tenant subscriptions for this plan.
     */
    public function tenantSubscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    /**
     * Alias for tenant subscriptions (for backwards compatibility).
     */
    public function subscriptions(): HasMany
    {
        return $this->tenantSubscriptions();
    }

    /**
     * Check if this is an unlimited feature (-1 means unlimited).
     */
    public function isUnlimited(string $feature): bool
    {
        return $this->{$feature} === -1;
    }

    /**
     * Get the price based on billing cycle.
     */
    public function getPrice(string $billingCycle = 'monthly'): float
    {
        return $billingCycle === 'yearly' ? $this->price_yearly : $this->price_monthly;
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    /**
     * Scope to get featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
