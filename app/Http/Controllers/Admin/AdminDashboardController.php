<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\TwilioUsage;
use App\Models\PlanUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Tenant Statistics
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = TenantSubscription::where('status', 'trial')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();

        // Subscription Statistics
        $activeSubscriptions = TenantSubscription::where('status', 'active')->count();
        $canceledSubscriptions = TenantSubscription::where('status', 'canceled')->count();
        $expiredTrials = TenantSubscription::where('status', 'trial')
            ->where('trial_ends_at', '<=', now())
            ->count();

        // Revenue Statistics (Monthly Recurring Revenue)
        $mrr = $this->calculateMRR();
        $arr = $mrr * 12; // Annual Recurring Revenue

        // Plan Distribution
        $planDistribution = TenantSubscription::select('subscription_plan_id', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('subscription_plan_id')
            ->with('plan')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->plan->name => $item->count];
            });

        // Recent Tenants
        $recentTenants = Tenant::with('subscription.plan')
            ->latest()
            ->limit(10)
            ->get();

        // Trial Expiring Soon (next 7 days)
        $expiringTrials = TenantSubscription::where('status', 'trial')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->with('tenant', 'plan')
            ->get();

        // Monthly Growth
        $monthlyGrowth = $this->calculateMonthlyGrowth();

        // Twilio Usage Stats
        $twilioUsageStats = $this->getTwilioUsageStats();

        return view('admin.dashboard.index', compact(
            'totalTenants',
            'activeTenants',
            'trialTenants',
            'suspendedTenants',
            'activeSubscriptions',
            'canceledSubscriptions',
            'expiredTrials',
            'mrr',
            'arr',
            'planDistribution',
            'recentTenants',
            'expiringTrials',
            'monthlyGrowth',
            'twilioUsageStats'
        ));
    }

    /**
     * Calculate Monthly Recurring Revenue.
     */
    protected function calculateMRR(): float
    {
        $monthlyMRR = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'monthly')
            ->join('subscription_plans', 'tenant_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price_monthly');

        $yearlyMRR = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'yearly')
            ->join('subscription_plans', 'tenant_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price_yearly');

        // Convert yearly to monthly equivalent
        $yearlyMRRMonthly = $yearlyMRR / 12;

        return $monthlyMRR + $yearlyMRRMonthly;
    }

    /**
     * Calculate monthly growth rate.
     */
    protected function calculateMonthlyGrowth(): array
    {
        $currentMonth = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = Tenant::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growthRate = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return [
            'current_month' => $currentMonth,
            'last_month' => $lastMonth,
            'growth_rate' => round($growthRate, 2),
        ];
    }

    /**
     * Get Twilio usage statistics across all tenants.
     */
    protected function getTwilioUsageStats(): array
    {
        $currentMonth = now()->format('Y-m');
        
        $usage = TwilioUsage::where('month', $currentMonth)
            ->select(
                DB::raw('SUM(whatsapp_count) as total_whatsapp'),
                DB::raw('SUM(sms_count) as total_sms'),
                DB::raw('SUM(total_messages) as total_messages'),
                DB::raw('SUM(total_cost) as total_cost')
            )
            ->first();

        return [
            'total_whatsapp' => $usage->total_whatsapp ?? 0,
            'total_sms' => $usage->total_sms ?? 0,
            'total_messages' => $usage->total_messages ?? 0,
            'total_cost' => $usage->total_cost ?? 0,
        ];
    }

    /**
     * Show revenue analytics.
     */
    public function revenue()
    {
        // Monthly revenue breakdown
        $monthlyRevenue = TenantSubscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->groupBy(function ($subscription) {
                return $subscription->current_period_start->format('Y-m');
            })
            ->map(function ($subscriptions) {
                return $subscriptions->sum(function ($subscription) {
                    return $subscription->billing_cycle === 'yearly' 
                        ? $subscription->plan->price_yearly / 12 
                        : $subscription->plan->price_monthly;
                });
            });

        // Plan revenue breakdown
        $planRevenue = TenantSubscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->groupBy('subscription_plan_id')
            ->map(function ($subscriptions) {
                $plan = $subscriptions->first()->plan;
                $revenue = $subscriptions->sum(function ($subscription) use ($plan) {
                    return $subscription->billing_cycle === 'yearly' 
                        ? $plan->price_yearly / 12 
                        : $plan->price_monthly;
                });
                return [
                    'plan_name' => $plan->name,
                    'count' => $subscriptions->count(),
                    'mrr' => $revenue,
                ];
            });

        return view('admin.dashboard.revenue', compact('monthlyRevenue', 'planRevenue'));
    }

    /**
     * Show usage analytics.
     */
    public function usage()
    {
        $currentMonth = now()->format('Y-m');

        // Get top tenants by tracked hours
        $topByHours = PlanUsage::where('month', $currentMonth)
            ->with('tenant')
            ->orderBy('tracked_hours', 'desc')
            ->limit(10)
            ->get();

        // Get top tenants by Twilio usage
        $topByTwilio = TwilioUsage::where('month', $currentMonth)
            ->with('tenant')
            ->orderBy('total_messages', 'desc')
            ->limit(10)
            ->get();

        // Overall usage stats
        $usageStats = PlanUsage::where('month', $currentMonth)
            ->select(
                DB::raw('SUM(users_count) as total_users'),
                DB::raw('SUM(projects_count) as total_projects'),
                DB::raw('SUM(clients_count) as total_clients'),
                DB::raw('SUM(tracked_hours) as total_hours'),
                DB::raw('SUM(invoices_count) as total_invoices')
            )
            ->first();

        return view('admin.dashboard.usage', compact('topByHours', 'topByTwilio', 'usageStats'));
    }
}
