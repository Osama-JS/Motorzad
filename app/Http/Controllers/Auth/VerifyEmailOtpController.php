<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerifyEmailOtpController extends Controller
{
    /**
     * Verify the user's email using the submitted 6-digit OTP code.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => __('يرجى إدخال رمز التحقق المكوّن من 6 أرقام.'),
            'otp.size' => __('يجب أن يتكون رمز التحقق من 6 أرقام بالضبط.'),
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->getRedirectUrl($user));
        }

        $cachedCode = Cache::get('email_verify_' . $user->email);
        $attemptsKey = 'otp_attempts_email_' . $user->email;
        $attempts = (int) Cache::get($attemptsKey, 0);

        if (!$cachedCode) {
            return back()->withErrors(['otp' => __('انتهت صلاحية رمز التحقق أو أنه غير صالح. يرجى طلب رمز جديد.')]);
        }

        if ($cachedCode !== $request->otp) {
            $attempts++;
            if ($attempts >= 5) {
                Cache::forget('email_verify_' . $user->email);
                Cache::forget($attemptsKey);
                return back()->withErrors(['otp' => __('تم تجاوز الحد الأقصى للمحاولات الخاطئة (5 محاولات). تم إبطال الرمز، يرجى طلب رمز جديد.')]);
            }
            Cache::put($attemptsKey, $attempts, now()->addMinutes(15));
            return back()->withErrors(['otp' => __('رمز التحقق غير صحيح. المحاولات المتبقية: :remaining', ['remaining' => 5 - $attempts])]);
        }

        // Successfully verified
        Cache::forget('email_verify_' . $user->email);
        Cache::forget($attemptsKey);
        Cache::forget('otp_throttle_' . $user->email);

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($user->status === 'pending') {
            $user->status = 'active';
            $user->save();
        }

        return redirect()->intended($this->getRedirectUrl($user))->with('success', __('تم تأكيد بريدك الإلكتروني بنجاح! أهلاً بك في منصة موتورزاد.'));
    }

    protected function getRedirectUrl($user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('bidder')) {
            return route('bidder.dashboard');
        }
        return route('dashboard');
    }
}
