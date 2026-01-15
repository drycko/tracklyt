<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Only owners and admins can access tenant settings
        if (!$user->isOwner() && !$user->isAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to settings.');
        }
        
        $section = $request->get('section', 'company');
        
        // Get team members (owners and admins only)
        $teamMembers = null;
        if ($user->isOwner() || $user->isAdmin() || $user->isSuperAdmin()) {
            $teamMembers = User::with('tenant')
                ->when(!$user->isSuperAdmin(), function($query) use ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Get usage statistics
        $usageStats = [
            'users' => [
                'current' => $tenant->current_users_count ?? 0,
                'limit' => $tenant->max_users ?? 1,
                'percentage' => $tenant->max_users > 0 ? ($tenant->current_users_count / $tenant->max_users * 100) : 0,
            ],
            'projects' => [
                'current' => $tenant->current_projects_count ?? 0,
                'limit' => $tenant->max_projects ?? 5,
                'percentage' => $tenant->max_projects > 0 ? ($tenant->current_projects_count / $tenant->max_projects * 100) : 0,
            ],
            'clients' => [
                'current' => $tenant->current_clients_count ?? 0,
                'limit' => $tenant->max_clients ?? 10,
                'percentage' => $tenant->max_clients > 0 ? ($tenant->current_clients_count / $tenant->max_clients * 100) : 0,
            ],
            'storage' => [
                'current' => $tenant->current_storage_mb ?? 0,
                'limit' => $tenant->max_storage_mb ?? 1000,
                'percentage' => $tenant->max_storage_mb > 0 ? ($tenant->current_storage_mb / $tenant->max_storage_mb * 100) : 0,
            ],
        ];
        
        return view('settings.index', compact('user', 'tenant', 'section', 'teamMembers', 'usageStats'));
    }
    
    /**
     * Update company profile.
     */
    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Only owners can update company settings
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can update company settings.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'company_size' => 'nullable|in:1-5,6-10,11-25,26-50,51-100,100+',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($tenant->company_logo) {
                Storage::disk('public')->delete($tenant->company_logo);
            }
            
            $path = $request->file('company_logo')->store('logos', 'public');
            $validated['company_logo'] = $path;
        }
        
        $tenant->update($validated);
        
        return back()->with('success', 'Company profile updated successfully.');
    }
    
    /**
     * Update billing information.
     */
    public function updateBilling(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Only owners can update billing settings
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can update billing settings.');
        }
        
        $validated = $request->validate([
            'billing_email' => 'required|email|max:255',
            'billing_contact_name' => 'nullable|string|max:255',
            'billing_contact_phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'payment_method' => 'nullable|in:card,eft,manual',
        ]);
        
        $tenant->update($validated);
        
        return back()->with('success', 'Billing information updated successfully.');
    }
    
    /**
     * Update preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Owners and admins can update preferences
        if (!$user->isOwner() && !$user->isAdmin() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Insufficient permissions.');
        }
        
        $validated = $request->validate([
            'timezone' => 'required|string|max:100',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|in:12,24',
            'week_start' => 'nullable|in:sunday,monday',
            'allow_client_portal' => 'boolean',
            'allow_api_access' => 'boolean',
        ]);
        
        // Store additional settings in the settings JSON field
        $settings = $tenant->settings ?? [];
        $settings['date_format'] = $validated['date_format'] ?? 'Y-m-d';
        $settings['time_format'] = $validated['time_format'] ?? '24';
        $settings['week_start'] = $validated['week_start'] ?? 'monday';
        
        $tenant->update([
            'timezone' => $validated['timezone'],
            'allow_client_portal' => $validated['allow_client_portal'] ?? false,
            'allow_api_access' => $validated['allow_api_access'] ?? false,
            'settings' => $settings,
        ]);
        
        return back()->with('success', 'Preferences updated successfully.');
    }
    
    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'hourly_rate' => 'nullable|numeric|min:0',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (isset($validated['hourly_rate'])) {
            $user->hourly_rate = $validated['hourly_rate'];
        }
        
        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }
        
        $user->save();
        
        return back()->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Update team member.
     */
    public function updateTeamMember(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        // Only owners and admins can update team members
        if (!$authUser->isOwner() && !$authUser->isAdmin() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Insufficient permissions.');
        }
        
        // Can't edit super admins
        if ($user->isSuperAdmin() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Cannot edit super admin users.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:owner,admin,staff,client',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Team member updated successfully.');
    }
    
    /**
     * Delete team member.
     */
    public function deleteTeamMember(User $user)
    {
        $authUser = Auth::user();
        
        // Only owners can delete team members
        if (!$authUser->isOwner() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Only owners can delete team members.');
        }
        
        // Can't delete yourself
        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        // Can't delete super admins
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete super admin users.');
        }
        
        $user->delete();
        
        return back()->with('success', 'Team member deleted successfully.');
    }
}
