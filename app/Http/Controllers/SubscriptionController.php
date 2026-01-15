<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected PlanLimitService $planLimitService;

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    /**
     * Show the billing dashboard.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;
        $plans = SubscriptionPlan::active()->get();
        
        $usageStats = $subscription ? 
            $this->planLimitService->getUsageStats($tenant) : [];
        
        $featureStatus = $subscription ? 
            $this->planLimitService->getFeatureStatus($tenant) : [];

        return view('subscription.index', compact(
            'tenant',
            'subscription',
            'plans',
            'usageStats',
            'featureStatus'
        ));
    }

    /**
     * Show available plans for selection or upgrade.
     */
    public function plans()
    {
        $tenant = auth()->user()->tenant;
        $currentSubscription = $tenant->subscription;
        $plans = SubscriptionPlan::active()->get();

        return view('subscription.plans', compact('tenant', 'currentSubscription', 'plans'));
    }

    /**
     * Select a plan (initial subscription).
     */
    public function selectPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $tenant = auth()->user()->tenant;
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if tenant already has an active subscription
        if ($tenant->subscription && $tenant->subscription->isActive()) {
            return redirect()->route('subscription.upgrade', $plan->id)
                           ->with('info', 'You already have an active subscription. Use the upgrade option.');
        }

        // Create new subscription
        $subscription = TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => $plan->slug === 'trial' ? 'trial' : 'active',
            'billing_cycle' => $request->billing_cycle,
            'trial_ends_at' => $plan->slug === 'trial' ? now()->addDays(14) : null,
            'current_period_start' => now(),
            'current_period_end' => $request->billing_cycle === 'yearly' ? 
                now()->addYear() : now()->addMonth(),
        ]);

        return redirect()->route('subscription.index')
                       ->with('success', "Successfully subscribed to the {$plan->name} plan!");
    }

    /**
     * Show upgrade options.
     */
    public function showUpgrade(SubscriptionPlan $plan)
    {
        $tenant = auth()->user()->tenant;
        $currentSubscription = $tenant->subscription;

        if (!$currentSubscription) {
            return redirect()->route('subscription.plans')
                           ->with('error', 'Please select a plan first.');
        }

        return view('subscription.upgrade', compact('plan', 'currentSubscription', 'tenant'));
    }

    /**
     * Process upgrade to a new plan.
     */
    public function upgrade(Request $request, SubscriptionPlan $plan)
    {
        $tenant = auth()->user()->tenant;
        $currentSubscription = $tenant->subscription;

        if (!$currentSubscription) {
            return redirect()->route('subscription.plans')
                           ->with('error', 'No active subscription found.');
        }

        // Update subscription
        $currentSubscription->update([
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'trial_ends_at' => null, // End trial if upgrading
        ]);

        return redirect()->route('subscription.index')
                       ->with('success', "Successfully upgraded to the {$plan->name} plan!");
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;

        if (!$subscription || !$subscription->isActive()) {
            return redirect()->route('subscription.index')
                           ->with('error', 'No active subscription to cancel.');
        }

        $subscription->cancel();

        return redirect()->route('subscription.index')
                       ->with('success', 'Your subscription has been canceled. You can continue using the service until the end of your billing period.');
    }

    /**
     * Resume a canceled subscription.
     */
    public function resume(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;

        if (!$subscription || !$subscription->isCanceled()) {
            return redirect()->route('subscription.index')
                           ->with('error', 'No canceled subscription to resume.');
        }

        $subscription->resume();

        return redirect()->route('subscription.index')
                       ->with('success', 'Your subscription has been resumed successfully!');
    }

    /**
     * Show payment method update form.
     */
    public function paymentMethod()
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;

        return view('subscription.payment-method', compact('tenant', 'subscription'));
    }

    /**
     * Update payment method.
     */
    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;

        if (!$subscription) {
            return redirect()->route('subscription.index')
                           ->with('error', 'No subscription found.');
        }

        $subscription->update([
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('subscription.index')
                       ->with('success', 'Payment method updated successfully!');
    }

    /**
     * Show billing history.
     */
    public function billingHistory()
    {
        $tenant = auth()->user()->tenant;
        // In the future, this would fetch actual billing history from Stripe
        $billingHistory = [];

        return view('subscription.billing-history', compact('tenant', 'billingHistory'));
    }
}
