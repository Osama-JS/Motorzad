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
    public function handle(Request $request, Closure $next, $level = 2): Response
    {
        $user = auth()->user();

        if (!$user || $user->kyc_level < $level) {
            if ($request->expectsJson()) {
                $errorMsg = $level == 3 ? 'يجب ترقية حسابك إلى بائع للوصول إلى هذه الميزة.' : 'يجب إكمال التحقق (KYC) للوصول إلى هذه الميزة.';
                return response()->json([
                    'error' => $errorMsg,
                    'required_level' => $level,
                    'current_level' => $user ? $user->kyc_level : 0
                ], 403);
            }

            $errorMsg = $level == 3 ? 'يجب ترقية حسابك إلى بائع للوصول إلى هذه الميزة.' : 'يجب إكمال التحقق من الهوية أولاً.';
            return redirect()->route('kyc.index')->with('error', $errorMsg);
        }

        return $next($request);
    }
}
