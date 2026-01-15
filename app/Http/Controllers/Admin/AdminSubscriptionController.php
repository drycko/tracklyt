<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\TwilioUsage;
use App\Models\PlanUsage;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    protected PlanLimitService $planLimitService;

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    /**
     * Display a listing of all tenant subscriptions.
     */
    public function index(Request $request)
    {
        $query = TenantSubscription::with('tenant', 'plan');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        // Filter by billing cycle
        if ($request->filled('billing_cycle')) {
            $query->where('billing_cycle', $request->billing_cycle);
        }

        // Search by tenant name
        if ($request->filled('search')) {
            $query->whereHas('tenant', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->latest()->paginate(20);
        $plans = SubscriptionPlan::all();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
    }

    /**
     * Show a single subscription details.
     */
    public function show(TenantSubscription $subscription)
    {
        $subscription->load('tenant', 'plan');
        
        // Get usage stats for current month
        $usageStats = $this->planLimitService->getUsageStats($subscription->tenant_id);
        
        // Get Twilio usage
        $currentMonth = now()->format('Y-m');
        $twilioUsage = TwilioUsage::where('tenant_id', $subscription->tenant_id)
            ->where('month', $currentMonth)
            ->first();

        // Get historical usage (last 6 months)
        $historicalUsage = PlanUsage::where('tenant_id', $subscription->tenant_id)
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('admin.subscriptions.show', compact('subscription', 'usageStats', 'twilioUsage', 'historicalUsage'));
    }

    /**
     * Show the form for changing a tenant's subscription plan.
     */
    public function edit(TenantSubscription $subscription)
    {
        $subscription->load('tenant', 'plan');
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update the tenant's subscription plan.
     */
    public function update(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:active,trial,canceled,expired,suspended',
        ]);

        $subscription->update([
            'subscription_plan_id' => $request->subscription_plan_id,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Extend trial period for a tenant.
     */
    public function extendTrial(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90',
        ]);

        if ($subscription->status !== 'trial') {
            return back()->with('error', 'Can only extend trial subscriptions.');
        }

        $subscription->update([
            'trial_ends_at' => $subscription->trial_ends_at->addDays($request->days),
        ]);

        return back()->with('success', "Trial extended by {$request->days} days.");
    }

    /**
     * Suspend a tenant's subscription.
     */
    public function suspend(TenantSubscription $subscription)
    {
        $subscription->update(['status' => 'suspended']);
        $subscription->tenant->update(['status' => 'suspended']);

        return back()->with('success', 'Subscription suspended successfully.');
    }

    /**
     * Reactivate a suspended subscription.
     */
    public function activate(TenantSubscription $subscription)
    {
        $subscription->update(['status' => 'active']);
        $subscription->tenant->update(['status' => 'active']);

        return back()->with('success', 'Subscription activated successfully.');
    }

    /**
     * Cancel a tenant's subscription.
     */
    public function cancel(TenantSubscription $subscription)
    {
        $subscription->update([
            'status' => 'canceled',
            'cancels_at' => now(),
        ]);

        return back()->with('success', 'Subscription canceled successfully.');
    }

    /**
     * Resume a canceled subscription.
     */
    public function resume(TenantSubscription $subscription)
    {
        if ($subscription->status !== 'canceled') {
            return back()->with('error', 'Can only resume canceled subscriptions.');
        }

        $subscription->update([
            'status' => 'active',
            'cancels_at' => null,
        ]);

        return back()->with('success', 'Subscription resumed successfully.');
    }

    /**
     * Show usage details for a tenant.
     */
    public function usage(TenantSubscription $subscription)
    {
        $currentMonth = now()->format('Y-m');
        
        // Current month usage
        $planUsage = PlanUsage::where('tenant_id', $subscription->tenant_id)
            ->where('month', $currentMonth)
            ->first();

        $twilioUsage = TwilioUsage::where('tenant_id', $subscription->tenant_id)
            ->where('month', $currentMonth)
            ->first();

        // Historical data (last 12 months)
        $historicalData = PlanUsage::where('tenant_id', $subscription->tenant_id)
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $twilioHistory = TwilioUsage::where('tenant_id', $subscription->tenant_id)
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Usage limits from plan
        $plan = $subscription->plan;
        $usageStats = $this->planLimitService->getUsageStats($subscription->tenant_id);

        return view('admin.subscriptions.usage', compact(
            'subscription',
            'planUsage',
            'twilioUsage',
            'historicalData',
            'twilioHistory',
            'plan',
            'usageStats'
        ));
    }

    /**
     * Reset usage limits for a tenant (for current month).
     */
    public function resetUsage(TenantSubscription $subscription)
    {
        $currentMonth = now()->format('Y-m');

        // Reset plan usage
        PlanUsage::where('tenant_id', $subscription->tenant_id)
            ->where('month', $currentMonth)
            ->delete();

        // Reset Twilio usage
        TwilioUsage::where('tenant_id', $subscription->tenant_id)
            ->where('month', $currentMonth)
            ->delete();

        return back()->with('success', 'Usage limits reset successfully for current month.');
    }

    /**
     * Adjust usage limits for a tenant (one-time increase).
     */
    public function adjustLimits(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'additional_twilio_messages' => 'nullable|integer|min:0',
            'additional_hours' => 'nullable|integer|min:0',
            'additional_invoices' => 'nullable|integer|min:0',
        ]);

        // This would require adding a table for one-time limit adjustments
        // For now, just return a message
        return back()->with('info', 'Limit adjustments feature coming soon. Consider upgrading their plan instead.');
    }
}
