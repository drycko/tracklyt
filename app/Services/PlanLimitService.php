<?php

namespace App\Services;

use App\Models\PlanUsage;
use App\Models\Tenant;
use App\Models\TwilioUsage;

class PlanLimitService
{
    /**
     * Check if tenant can add more users.
     */
    public function canAddUser(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_users')) {
            return true;
        }

        $currentUsers = $tenant->users()->count();
        return $currentUsers < $plan->max_users;
    }

    /**
     * Check if tenant can add more projects.
     */
    public function canAddProject(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_projects')) {
            return true;
        }

        $currentProjects = $tenant->projects()->count();
        return $currentProjects < $plan->max_projects;
    }

    /**
     * Check if tenant can add more clients.
     */
    public function canAddClient(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_clients')) {
            return true;
        }

        $currentClients = $tenant->clients()->count();
        return $currentClients < $plan->max_clients;
    }

    /**
     * Check if tenant can create more invoices this month.
     */
    public function canCreateInvoice(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_invoices_per_month')) {
            return true;
        }

        $usage = PlanUsage::getOrCreateForCurrentMonth($tenant->id);
        return !$usage->hasReachedLimit('invoices', $plan->max_invoices_per_month);
    }

    /**
     * Check if tenant can track more hours this month.
     */
    public function canTrackHours(Tenant $tenant, int $minutesToAdd = 0): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_monthly_hours')) {
            return true;
        }

        $usage = PlanUsage::getOrCreateForCurrentMonth($tenant->id);
        $currentHours = $usage->tracked_hours / 60; // Convert minutes to hours
        $hoursToAdd = $minutesToAdd / 60;
        
        return ($currentHours + $hoursToAdd) <= $plan->max_monthly_hours;
    }

    /**
     * Check if tenant can send Twilio messages.
     */
    public function canSendTwilioMessage(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        if ($plan->isUnlimited('max_twilio_messages_per_month')) {
            return true;
        }

        $usage = TwilioUsage::getOrCreateForCurrentMonth($tenant->id);
        return !$usage->hasReachedLimit($plan->max_twilio_messages_per_month);
    }

    /**
     * Get all usage statistics for a tenant.
     */
    public function getUsageStats(Tenant $tenant): array
    {
        $subscription = $tenant->subscription;
        if (!$subscription) {
            return [];
        }

        $plan = $subscription->plan;
        $planUsage = PlanUsage::getOrCreateForCurrentMonth($tenant->id);
        $twilioUsage = TwilioUsage::getOrCreateForCurrentMonth($tenant->id);

        return [
            'users' => [
                'current' => $tenant->users()->count(),
                'limit' => $plan->max_users,
                'unlimited' => $plan->isUnlimited('max_users'),
                'percentage' => $this->calculatePercentage($tenant->users()->count(), $plan->max_users),
            ],
            'projects' => [
                'current' => $tenant->projects()->count(),
                'limit' => $plan->max_projects,
                'unlimited' => $plan->isUnlimited('max_projects'),
                'percentage' => $this->calculatePercentage($tenant->projects()->count(), $plan->max_projects),
            ],
            'clients' => [
                'current' => $tenant->clients()->count(),
                'limit' => $plan->max_clients,
                'unlimited' => $plan->isUnlimited('max_clients'),
                'percentage' => $this->calculatePercentage($tenant->clients()->count(), $plan->max_clients),
            ],
            'monthly_hours' => [
                'current' => round($planUsage->tracked_hours / 60, 2),
                'limit' => $plan->max_monthly_hours,
                'unlimited' => $plan->isUnlimited('max_monthly_hours'),
                'percentage' => $this->calculatePercentage($planUsage->tracked_hours / 60, $plan->max_monthly_hours),
            ],
            'invoices' => [
                'current' => $planUsage->invoices_count,
                'limit' => $plan->max_invoices_per_month,
                'unlimited' => $plan->isUnlimited('max_invoices_per_month'),
                'percentage' => $this->calculatePercentage($planUsage->invoices_count, $plan->max_invoices_per_month),
            ],
            'twilio_messages' => [
                'current' => $twilioUsage->total_messages,
                'limit' => $plan->max_twilio_messages_per_month,
                'unlimited' => $plan->isUnlimited('max_twilio_messages_per_month'),
                'percentage' => $this->calculatePercentage($twilioUsage->total_messages, $plan->max_twilio_messages_per_month),
            ],
        ];
    }

    /**
     * Calculate usage percentage.
     */
    protected function calculatePercentage($current, $limit): int
    {
        if ($limit === -1 || $limit === 0) {
            return 0;
        }

        return min(100, round(($current / $limit) * 100));
    }

    /**
     * Get feature status for a tenant.
     */
    public function getFeatureStatus(Tenant $tenant): array
    {
        $subscription = $tenant->subscription;
        if (!$subscription) {
            return [];
        }

        $plan = $subscription->plan;

        return [
            'has_time_tracking' => $plan->has_time_tracking,
            'has_invoicing' => $plan->has_invoicing,
            'has_client_portal' => $plan->has_client_portal,
            'has_maintenance_reports' => $plan->has_maintenance_reports,
            'has_advanced_reporting' => $plan->has_advanced_reporting,
            'has_api_access' => $plan->has_api_access,
            'has_white_label' => $plan->has_white_label,
            'has_priority_support' => $plan->has_priority_support,
        ];
    }

    /**
     * Check if tenant has access to a specific feature.
     */
    public function hasFeature(Tenant $tenant, string $feature): bool
    {
        $subscription = $tenant->subscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        return $plan->{"has_$feature"} ?? false;
    }
}
