<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'company_name',
        'company_logo',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'timezone',
        'company_size',
        'industry',
        'tax_number',
        'plan',
        'status',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'monthly_amount',
        'payment_method',
        'stripe_customer_id',
        'stripe_subscription_id',
        'max_users',
        'max_projects',
        'max_clients',
        'max_storage_mb',
        'billing_email',
        'billing_contact_name',
        'billing_contact_phone',
        'is_onboarded',
        'onboarding_step',
        'onboarded_at',
        'settings',
        'metadata',
        'enabled_features',
        'allow_client_portal',
        'allow_api_access',
        'current_users_count',
        'current_projects_count',
        'current_clients_count',
        'current_storage_mb',
        'notes',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'onboarded_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'monthly_amount' => 'decimal:2',
            'settings' => 'array',
            'metadata' => 'array',
            'enabled_features' => 'array',
            'is_onboarded' => 'boolean',
            'allow_client_portal' => 'boolean',
            'allow_api_access' => 'boolean',
            'current_users_count' => 'integer',
            'current_projects_count' => 'integer',
            'current_clients_count' => 'integer',
            'current_storage_mb' => 'integer',
        ];
    }

    /**
     * Get the users for the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the tenant's subscription.
     */
    public function subscription()
    {
        return $this->hasOne(TenantSubscription::class)->latest();
    }

    /**
     * Get the projects for the tenant.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the clients for the tenant.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Scope a query to only include active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is on trial.
     */
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trialing' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if trial has expired.
     */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get days remaining in trial.
     */
    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }
        return now()->diffInDays($this->trial_ends_at, false);
    }

    /**
     * Check if subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing']);
    }

    /**
     * Check if tenant has reached user limit.
     */
    public function hasReachedUserLimit(): bool
    {
        return $this->current_users_count >= $this->max_users;
    }

    /**
     * Check if tenant has reached project limit.
     */
    public function hasReachedProjectLimit(): bool
    {
        return $this->current_projects_count >= $this->max_projects;
    }

    /**
     * Check if tenant has reached client limit.
     */
    public function hasReachedClientLimit(): bool
    {
        return $this->current_clients_count >= $this->max_clients;
    }

    /**
     * Check if tenant has reached storage limit.
     */
    public function hasReachedStorageLimit(): bool
    {
        return $this->current_storage_mb >= $this->max_storage_mb;
    }

    /**
     * Get storage usage percentage.
     */
    public function getStorageUsagePercentage(): float
    {
        if ($this->max_storage_mb == 0) {
            return 0;
        }
        return ($this->current_storage_mb / $this->max_storage_mb) * 100;
    }

    /**
     * Check if feature is enabled.
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->enabled_features ?? [];
        return in_array($feature, $features);
    }

    /**
     * Get setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Update usage counts.
     */
    public function updateUsageCounts(): void
    {
        $this->current_users_count = $this->users()->count();
        $this->current_projects_count = \App\Models\Project::count();
        $this->current_clients_count = \App\Models\Client::count();
        $this->save();
    }

    /**
     * Get plan display name.
     */
    public function getPlanNameAttribute(): string
    {
        return match($this->plan) {
            'trial' => 'Trial',
            'freelancer' => 'Freelancer',
            'agency' => 'Agency',
            'enterprise' => 'Enterprise',
            default => ucfirst($this->plan),
        };
    }

    /**
     * Complete onboarding step.
     */
    public function completeOnboardingStep(int $step): void
    {
        if ($step > $this->onboarding_step) {
            $this->onboarding_step = $step;
            if ($step >= 5) { // Total onboarding steps
                $this->is_onboarded = true;
                $this->onboarded_at = now();
            }
            $this->save();
        }
    }

    /**
     * Mark tenant as onboarded.
     */
    public function markAsOnboarded(): void
    {
        $this->is_onboarded = true;
        $this->onboarding_step = 5;
        $this->onboarded_at = now();
        $this->save();
    }
}
