<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        // Skip if user doesn't have a tenant (platform admins)
        $user = auth()->user();
        if (!$user->tenant_id) {
            return $next($request);
        }

        $tenant = $user->tenant;

        // Skip if tenant is already onboarded
        if ($tenant && $tenant->is_onboarded) {
            return $next($request);
        }

        // Allow access to onboarding routes
        if ($request->routeIs('onboarding.*')) {
            return $next($request);
        }

        // Redirect to onboarding if tenant is not onboarded
        return redirect()->route('onboarding.index');
    }
}
