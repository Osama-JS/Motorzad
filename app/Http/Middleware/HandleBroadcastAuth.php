<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleBroadcastAuth
{
    /**
     * Handle an incoming request for broadcasting authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('broadcasting/auth') || $request->is('api/broadcasting/auth')) {
            // 1. If a Bearer token is passed from mobile app, authenticate with Sanctum
            if ($request->bearerToken()) {
                $user = Auth::guard('sanctum')->user();
                if ($user) {
                    Auth::setUser($user);
                    $request->setUserResolver(fn($guard = null) => $user);
                }
            }

            // 2. If channel_name is empty but body has raw text/form data, parse it
            if (empty($request->channel_name) && !empty($request->getContent())) {
                parse_str($request->getContent(), $parsed);
                if (!empty($parsed)) {
                    $request->merge($parsed);
                }
            }
        }

        return $next($request);
    }
}
