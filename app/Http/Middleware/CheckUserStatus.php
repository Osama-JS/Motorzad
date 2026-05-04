<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->hasRole('admin') || $request->routeIs('kyc.*') || $request->routeIs('logout') || $request->routeIs('verification.*')) {
                return $next($request);
            }

            if ($user->status !== 'approved') {
                // If the user is pending or rejected, redirect to KYC page instead of logging out
                return redirect()->route('kyc.index');
            }
        }

        return $next($request);
    }
}
