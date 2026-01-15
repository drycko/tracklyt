<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccessControlController extends Controller
{
    /**
     * Display access control page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only owners and admins can access
        if (!$user->isOwner() && !$user->isAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to access control.');
        }
        
        $section = $request->get('section', 'roles');
        
        // Get all roles (exclude super_admin for tenant users)
        $roles = Role::with('permissions')->withCount('users')
            ->when(!$user->isSuperAdmin(), function($query) {
                $query->where('name', '!=', 'super_admin');
            })
            ->get();
        
        // Get all permissions grouped by module
        $permissions = Permission::all()->groupBy(function($permission) {
            // Extract module from permission name (e.g., "clients.view" -> "clients")
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });
        
        // Get all users with their roles (exclude super admins for tenant users)
        $users = User::with(['roles', 'tenant'])
            ->when(!$user->isSuperAdmin(), function($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id)
                      ->where('is_super_admin', false);
            })
            ->orderBy('name')
            ->get();
        
        // Define system roles that cannot be edited/deleted
        $systemRoles = ['super_admin', 'owner', 'admin', 'staff', 'client'];
        
        return view('access-control.index', compact('user', 'section', 'roles', 'permissions', 'users', 'systemRoles'));
    }
    
    /**
     * Create a new role.
     */
    public function storeRole(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can create roles.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        
        // Store display name and description in role's metadata if needed
        // For now, Spatie only supports name and guard_name by default
        
        return back()->with('success', 'Role created successfully.');
    }
    
    /**
     * Update role permissions.
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $user = Auth::user();
        
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can update role permissions.');
        }
        
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role->syncPermissions($validated['permissions'] ?? []);
        
        return back()->with('success', 'Role permissions updated successfully.');
    }
    
    /**
     * Delete a role.
     */
    public function destroyRole(Role $role)
    {
        $user = Auth::user();
        
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can delete roles.');
        }
        
        // Protect system roles
        $systemRoles = ['super_admin', 'owner', 'admin', 'staff', 'client'];
        if (in_array($role->name, $systemRoles)) {
            return back()->with('error', 'Cannot delete system roles.');
        }
        
        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has assigned users.');
        }
        
        $role->delete();
        
        return back()->with('success', 'Role deleted successfully.');
    }
    
    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $assignUser)
    {
        $authUser = Auth::user();
        
        if (!$authUser->isOwner() && !$authUser->isAdmin() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Insufficient permissions.');
        }
        
        // Can't modify super admin users
        if ($assignUser->isSuperAdmin() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Cannot modify super admin users.');
        }
        
        // Tenant users cannot assign super_admin role
        if (!$authUser->isSuperAdmin() && $assignUser->tenant_id !== null) {
            $requestedRoles = $request->input('roles', []);
            if (in_array('super_admin', $requestedRoles)) {
                return back()->with('error', 'Cannot assign super_admin role to tenant users.');
            }
        }
        
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $assignUser->syncRoles($validated['roles'] ?? []);
        
        return back()->with('success', 'User roles updated successfully.');
    }
    
    /**
     * Create a new permission.
     */
    public function storePermission(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can create permissions.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        
        return back()->with('success', 'Permission created successfully.');
    }
    
    /**
     * Delete a permission.
     */
    public function destroyPermission(Permission $permission)
    {
        $user = Auth::user();
        
        if (!$user->isOwner() && !$user->isSuperAdmin()) {
            return back()->with('error', 'Only owners can delete permissions.');
        }
        
        // Protect system permissions (you can define these)
        $systemPermissions = [
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'time-entries.view', 'time-entries.create', 'time-entries.edit', 'time-entries.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'quotes.view', 'quotes.create', 'quotes.edit', 'quotes.delete',
            'reports.view', 'settings.view', 'settings.edit',
        ];
        
        if (in_array($permission->name, $systemPermissions)) {
            return back()->with('error', 'Cannot delete system permissions.');
        }
        
        $permission->delete();
        
        return back()->with('success', 'Permission deleted successfully.');
    }
    
    /**
     * Assign permissions directly to user.
     */
    public function assignPermission(Request $request, User $assignUser)
    {
        $authUser = Auth::user();
        
        if (!$authUser->isOwner() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Only owners can assign permissions.');
        }
        
        // Can't modify super admin users
        if ($assignUser->isSuperAdmin() && !$authUser->isSuperAdmin()) {
            return back()->with('error', 'Cannot modify super admin users.');
        }
        
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $assignUser->syncPermissions($validated['permissions'] ?? []);
        
        return back()->with('success', 'User permissions updated successfully.');
    }
}
