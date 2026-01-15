<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected PlanLimitService $planLimitService;

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tenant::with('subscription.plan');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('billing_email', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by subscription status
        if ($request->filled('subscription_status')) {
            $query->whereHas('subscription', function ($q) use ($request) {
                $q->where('status', $request->subscription_status);
            });
        }

        $tenants = $query->latest()->paginate(15);
        
        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('tenants.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants',
            'billing_email' => 'required|email',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'trial_days' => 'nullable|integer|min:0|max:90',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'billing_email' => $validated['billing_email'],
            'status' => 'active',
        ]);

        // Create subscription
        $trialDays = $validated['trial_days'] ?? 0;
        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $validated['subscription_plan_id'],
            'billing_cycle' => $validated['billing_cycle'],
            'status' => $trialDays > 0 ? 'trial' : 'active',
            'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
            'current_period_start' => now(),
            'current_period_end' => $validated['billing_cycle'] === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('subscription.plan', 'projects', 'clients');
        
        // Get usage statistics
        $usageStats = $this->planLimitService->getUsageStats($tenant->id);
        
        // Get feature status
        $features = $this->planLimitService->getFeatureStatus($tenant->id);
        
        // Get recent activity
        $recentProjects = $tenant->projects()->latest()->limit(5)->get();
        $recentClients = $tenant->clients()->latest()->limit(5)->get();
        
        return view('tenants.show', compact('tenant', 'usageStats', 'features', 'recentProjects', 'recentClients'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        $tenant->load('subscription');
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'billing_email' => 'required|email',
            'status' => 'required|in:active,suspended,cancelled',
        ]);

        $tenant->update($validated);

        // Sync subscription status if tenant is suspended
        if ($validated['status'] === 'suspended') {
            $tenant->subscription?->update(['status' => 'suspended']);
        }

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        // Check if tenant has data
        $hasProjects = $tenant->projects()->exists();
        $hasClients = $tenant->clients()->exists();

        if ($hasProjects || $hasClients) {
            return back()->with('error', 'Cannot delete tenant with existing projects or clients. Suspend instead.');
        }

        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Suspend a tenant.
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);
        $tenant->subscription?->update(['status' => 'suspended']);

        return back()->with('success', 'Tenant suspended successfully.');
    }

    /**
     * Reactivate a suspended tenant.
     */
    public function reactivate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        $tenant->subscription?->update(['status' => 'active']);

        return back()->with('success', 'Tenant reactivated successfully.');
    }
}
