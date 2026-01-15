<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant resolution for guest routes
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // Super admins can access everything - no tenant restriction
        if ($user->is_super_admin) {
            // Set null so global scopes know to skip filtering
            $request->merge(['current_tenant_id' => null]);
            return $next($request);
        }

        // For regular users, ensure they have a tenant
        if (!$user->tenant_id) {
            abort(403, 'User is not associated with a tenant.');
        }

        // Verify tenant exists and is active
        $tenant = Tenant::withoutGlobalScope('tenant')->find($user->tenant_id);
        
        if (!$tenant || !$tenant->isActive()) {
            auth()->logout();
            abort(403, 'Tenant is not active.');
        }

        // Store tenant in request for easy access AND for global scopes
        $request->merge([
            'current_tenant' => $tenant,
            'current_tenant_id' => $tenant->id
        ]);

        return $next($request);
    }
}
