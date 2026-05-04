<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $redirect = route('dashboard');
            if ($request->user()->hasRole('admin')) {
                $redirect = route('admin.dashboard');
            } elseif ($request->user()->hasRole('bidder')) {
                $redirect = route('bidder.dashboard');
            }
            return redirect()->intended($redirect . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $redirect = route('dashboard');
        if ($request->user()->hasRole('admin')) {
            $redirect = route('admin.dashboard');
        } elseif ($request->user()->hasRole('bidder')) {
            $redirect = route('bidder.dashboard');
        }
        return redirect()->intended($redirect . '?verified=1');
    }
}
