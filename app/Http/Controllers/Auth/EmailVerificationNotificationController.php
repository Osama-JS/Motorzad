<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Rate limiting: 60 seconds between resend requests
        $throttleKey = 'otp_throttle_' . $user->email;
        if (\Illuminate\Support\Facades\Cache::has($throttleKey)) {
            $secondsLeft = \Illuminate\Support\Facades\Cache::get($throttleKey) - now()->timestamp;
            if ($secondsLeft > 0) {
                return back()->with('error', __('يرجى الانتظار :seconds ثانية قبل طلب رمز جديد.', ['seconds' => $secondsLeft]));
            }
        }

        \Illuminate\Support\Facades\Cache::put($throttleKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-otp-sent');
    }
}
