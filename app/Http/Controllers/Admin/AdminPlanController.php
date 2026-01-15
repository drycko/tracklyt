<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_users' => 'required|integer|min:-1',
            'max_projects' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'max_monthly_hours' => 'required|integer|min:-1',
            'max_invoices_per_month' => 'required|integer|min:-1',
            'max_twilio_messages_per_month' => 'required|integer|min:-1',
            'time_tracking' => 'boolean',
            'invoicing' => 'boolean',
            'client_portal' => 'boolean',
            'maintenance_reports' => 'boolean',
            'advanced_reporting' => 'boolean',
            'api_access' => 'boolean',
            'white_label' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        SubscriptionPlan::create($validated);

        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified plan.
     */
    public function show(SubscriptionPlan $plan)
    {
        $plan->loadCount('subscriptions');
        
        // Get active subscriptions breakdown
        $activeSubscriptions = $plan->subscriptions()
            ->where('status', 'active')
            ->count();

        $trialSubscriptions = $plan->subscriptions()
            ->where('status', 'trial')
            ->count();

        // Calculate revenue from this plan
        $monthlyRevenue = $plan->subscriptions()
            ->where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->count() * $plan->price_monthly;

        $yearlyRevenue = $plan->subscriptions()
            ->where('status', 'active')
            ->where('billing_cycle', 'yearly')
            ->count() * ($plan->price_yearly / 12); // Convert to MRR

        $totalMRR = $monthlyRevenue + $yearlyRevenue;

        return view('admin.plans.show', compact(
            'plan',
            'activeSubscriptions',
            'trialSubscriptions',
            'totalMRR'
        ));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_users' => 'required|integer|min:-1',
            'max_projects' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'max_monthly_hours' => 'required|integer|min:-1',
            'max_invoices_per_month' => 'required|integer|min:-1',
            'max_twilio_messages_per_month' => 'required|integer|min:-1',
            'time_tracking' => 'boolean',
            'invoicing' => 'boolean',
            'client_portal' => 'boolean',
            'maintenance_reports' => 'boolean',
            'advanced_reporting' => 'boolean',
            'api_access' => 'boolean',
            'white_label' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $plan->update($validated);

        return redirect()
            ->route('admin.plans.show', $plan)
            ->with('success', 'Plan updated successfully.');
    }

    /**
     * Activate/Deactivate a plan.
     */
    public function toggleActive(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Plan {$status} successfully.");
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(SubscriptionPlan $plan)
    {
        $plan->update(['is_featured' => !$plan->is_featured]);

        $status = $plan->is_featured ? 'marked as featured' : 'unmarked as featured';
        
        return back()->with('success', "Plan {$status} successfully.");
    }

    /**
     * Remove the specified plan (soft delete or prevent if has active subscriptions).
     */
    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        $activeSubscriptions = $plan->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->count();

        if ($activeSubscriptions > 0) {
            return back()->with('error', 'Cannot delete plan with active subscriptions. Deactivate it instead.');
        }

        $plan->delete();

        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}
