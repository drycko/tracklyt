<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminTenantController extends Controller
{
    /**
     * Display a listing of tenants
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['subscription.plan']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_email', 'LIKE', "%{$search}%")
                  ->orWhere('domain', 'LIKE', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Plan filter
        if ($request->filled('plan')) {
            $query->whereHas('subscription', function($q) use ($request) {
                $q->where('subscription_plan_id', $request->plan);
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $tenants = $query->paginate(15);
        $plans = SubscriptionPlan::all();
        
        return view('admin.tenants.index', compact('tenants', 'plans'));
    }
    
    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('admin.tenants.create', compact('plans'));
    }
    
    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:tenants,contact_email',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'status' => 'required|in:active,trial,suspended,canceled',
            'plan_id' => 'nullable|exists:subscription_plans,id'
        ]);
        
        $tenant = Tenant::create($validated);
        
        // Create subscription if plan is selected
        if ($validated['plan_id']) {
            $plan = SubscriptionPlan::find($validated['plan_id']);
            $tenant->subscription()->create([
                'subscription_plan_id' => $plan->id,
                'status' => $tenant->status === 'trial' ? 'trial' : 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'trial_ends_at' => $tenant->status === 'trial' ? now()->addDays(14) : null,
            ]);
        }
        
        return redirect()->route('admin.tenants.index')
                         ->with('success', 'Tenant created successfully.');
    }
    
    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['subscription.plan']);
        return view('admin.tenants.show', compact('tenant'));
    }
    
    /**
     * Show the form for editing the tenant
     */
    public function edit(Tenant $tenant)
    {
        $plans = SubscriptionPlan::all();
        return view('admin.tenants.edit', compact('tenant', 'plans'));
    }
    
    /**
     * Update the specified tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:tenants,contact_email,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'status' => 'required|in:active,trial,suspended,canceled',
        ]);
        
        $tenant->update($validated);
        
        return redirect()->route('admin.tenants.show', $tenant)
                         ->with('success', 'Tenant updated successfully.');
    }
    
    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        
        return redirect()->route('admin.tenants.index')
                         ->with('success', 'Tenant deleted successfully.');
    }
    
    /**
     * Suspend a tenant
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);
        
        if ($tenant->subscription) {
            $tenant->subscription->update(['status' => 'suspended']);
        }
        
        return redirect()->back()
                         ->with('success', 'Tenant suspended successfully.');
    }
    
    /**
     * Activate a tenant
     */
    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        
        if ($tenant->subscription) {
            $tenant->subscription->update(['status' => 'active']);
        }
        
        return redirect()->back()
                         ->with('success', 'Tenant activated successfully.');
    }
}