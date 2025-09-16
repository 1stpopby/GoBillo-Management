<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Allow superadmin to access everything
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Allow access to logout route
        if ($request->routeIs('logout')) {
            return $next($request);
        }
        
        // Allow access to blocked page
        if ($request->routeIs('blocked')) {
            return $next($request);
        }
        
        // Allow access to company assignment page
        if ($request->routeIs('company.assignment') || $request->routeIs('company.assign')) {
            return $next($request);
        }
        
        // Check if user has no company assigned
        if ($user && !$user->company_id) {
            return redirect()->route('company.assignment');
        }
        
        // Ensure user belongs to a company
        if (!$user || !$user->company_id) {
            abort(403, 'Access denied: No company assigned.');
        }
        
        // Check if user's company is active
        if (!$user->company || !$user->company->isActive()) {
            abort(403, 'Access denied: Company is inactive.');
        }
        
        // Check subscription status (optional - can be disabled for development)
        if ($user->company->isSubscriptionExpired() && !$user->company->isOnTrial()) {
            abort(403, 'Access denied: Subscription expired.');
        }
        
        return $next($request);
    }
}
