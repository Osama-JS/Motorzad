<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $level = 3): Response
    {
        $user = auth()->user();

        if (!$user || $user->kyc_level < $level) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'يجب إكمال التحقق (KYC) للوصول إلى هذه الميزة.',
                    'required_level' => $level,
                    'current_level' => $user ? $user->kyc_level : 0
                ], 403);
            }

            return redirect()->route('kyc.index')->with('error', 'يجب إكمال التحقق من الهوية أولاً.');
        }

        return $next($request);
    }
}
