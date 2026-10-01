<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $email = $request->session()->get('pending_verification_email');
        if (!$email) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login');
        }

        return view('auth.verify-email', ['email' => $email]);
    }
}
