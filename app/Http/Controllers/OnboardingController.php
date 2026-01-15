<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding wizard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Only owners can complete onboarding
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return redirect()->route('home')->with('error', 'Only owners can complete onboarding.');
        }
        
        // If already onboarded, redirect to dashboard
        if ($tenant->is_onboarded) {
            return redirect()->route('home');
        }
        
        $step = $request->get('step', 1);
        
        return view('onboarding.index', compact('user', 'tenant', 'step'));
    }
    
    /**
     * Save step 1: Company Information
     */
    public function saveCompanyInfo(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'industry' => 'nullable|string|max:100',
            'company_size' => 'nullable|in:1-5,6-10,11-25,26-50,51-100,100+',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            if ($tenant->company_logo) {
                Storage::disk('public')->delete($tenant->company_logo);
            }
            $path = $request->file('company_logo')->store('logos', 'public');
            $validated['company_logo'] = $path;
        }
        
        $tenant->update($validated);
        $tenant->completeOnboardingStep(1);
        
        return redirect()->route('onboarding.index', ['step' => 2]);
    }
    
    /**
     * Save step 2: Team Setup
     */
    public function saveTeamSetup(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        $validated = $request->validate([
            'team_members' => 'nullable|array',
            'team_members.*.name' => 'required|string|max:255',
            'team_members.*.email' => 'required|email|max:255|unique:users,email',
            'team_members.*.role' => 'required|in:admin,staff',
        ]);
        
        // Create team members
        if (isset($validated['team_members'])) {
            foreach ($validated['team_members'] as $member) {
                User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $member['name'],
                    'email' => $member['email'],
                    'role' => $member['role'],
                    'password' => Hash::make('password'), // Temporary password, should send invitation email
                    'is_active' => true,
                ]);
            }
        }
        
        $tenant->completeOnboardingStep(2);
        
        return redirect()->route('onboarding.index', ['step' => 3]);
    }
    
    /**
     * Save step 3: Quick Start Data
     */
    public function saveQuickStart(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        $validated = $request->validate([
            'skip_quick_start' => 'nullable|boolean',
            'client_name' => 'nullable|required_without:skip_quick_start|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'project_name' => 'nullable|required_without:skip_quick_start|string|max:255',
            'project_description' => 'nullable|string',
            'project_budget' => 'nullable|numeric|min:0',
        ]);
        
        // Create sample client and project if not skipped
        if (!$request->input('skip_quick_start')) {
            $client = Client::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['client_name'],
                'email' => $validated['client_email'] ?? null,
                'phone' => $validated['client_phone'] ?? null,
                'status' => 'active',
            ]);
            
            if ($validated['project_name']) {
                Project::create([
                    'tenant_id' => $tenant->id,
                    'client_id' => $client->id,
                    'name' => $validated['project_name'],
                    'description' => $validated['project_description'] ?? null,
                    'budget' => $validated['project_budget'] ?? null,
                    'status' => 'active',
                ]);
            }
        }
        
        $tenant->completeOnboardingStep(3);
        
        return redirect()->route('onboarding.index', ['step' => 4]);
    }
    
    /**
     * Save step 4: Preferences & Complete
     */
    public function savePreferences(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string|max:100',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|in:H:i,h:i A',
            'week_start' => 'nullable|in:monday,sunday',
            'language' => 'nullable|string|max:10',
            'allow_client_portal' => 'boolean',
        ]);
        
        // Store settings
        $settings = $tenant->settings ?? [];
        $settings['date_format'] = $validated['date_format'] ?? 'Y-m-d';
        $settings['time_format'] = $validated['time_format'] ?? 'H:i';
        $settings['week_start'] = $validated['week_start'] ?? 'monday';
        $settings['language'] = $validated['language'] ?? 'en';
        
        $tenant->update([
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'allow_client_portal' => $validated['allow_client_portal'] ?? false,
            'settings' => $settings,
        ]);
        
        // Mark onboarding as complete
        $tenant->markAsOnboarded();
        
        return redirect()->route('onboarding.complete');
    }
    
    /**
     * Show completion page
     */
    public function complete()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant->is_onboarded) {
            return redirect()->route('onboarding.index');
        }
        
        return view('onboarding.complete', compact('user', 'tenant'));
    }
    
    /**
     * Skip onboarding (for testing or later completion)
     */
    public function skip()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Only owners can skip
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return redirect()->route('onboarding.index')->with('error', 'Only owners can skip onboarding.');
        }
        
        $tenant->markAsOnboarded();
        
        return redirect()->route('home')->with('info', 'Onboarding skipped. You can update settings anytime.');
    }
}
