@extends('layouts.auth')

@section('title', __('تأكيد البريد الإلكتروني عبر رمز OTP'))
@section('subtitle', __('أدخل رمز التحقق المكوّن من 6 أرقام لتفعيل حسابك'))

@section('content')
<style>
    .otp-container {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        direction: ltr;
        margin: 1.5rem 0;
    }
    .otp-digit {
        width: 48px;
        height: 56px;
        text-align: center;
        font-size: 1.6rem;
        font-weight: 700;
        border: 2px solid var(--border, rgba(255,255,255,0.12));
        background: var(--bg-input, rgba(255,255,255,0.04));
        color: var(--text, #fff);
        border-radius: var(--radius, 8px);
        transition: all 0.2s ease;
        outline: none;
    }
    .otp-digit:focus {
        border-color: var(--brand-red, #e53e3e);
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.25);
        background: rgba(229, 62, 62, 0.05);
    }
    .otp-digit.filled {
        border-color: rgba(229, 62, 62, 0.5);
    }
    .email-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(229, 62, 62, 0.08);
        border: 1px solid rgba(229, 62, 62, 0.25);
        color: var(--text, #fff);
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
        font-size: 0.85rem;
        direction: ltr;
        word-break: break-all;
    }
    .timer-badge {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: var(--brand-gold, #f59e0b);
    }
</style>

{{-- Instructions & Target Email --}}
<div style="text-align: center; margin-bottom: 1.25rem;">
    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border, rgba(255,255,255,0.08)); padding: 1.25rem; border-radius: var(--radius, 10px);">
        <p class="mb-2" style="font-size: 0.9rem; color: var(--text-muted, #9ca3af);">
            {{ __('تم إرسال رمز تحقق مكوّن من 6 أرقام إلى عنوان بريدك الإلكتروني:') }}
        </p>
        <div class="email-badge">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span>{{ auth()->user()->email }}</span>
        </div>
        <small class="d-block mt-2 text-muted" style="font-size: 0.78rem;">
            {{ __('صلاحية الرمز 15 دقيقة. يرجى مراجعة صندوق الوارد أو مجلد الرسائل غير المرغوب فيها (Spam).') }}
        </small>
    </div>
</div>

{{-- Success Alerts --}}
@if (session('status') == 'verification-otp-sent' || session('status') == 'verification-link-sent')
    <div style="margin-bottom: 1.25rem; background: rgba(16, 185, 129, 0.12); color: #34d399; padding: 0.75rem 1rem; border-radius: var(--radius, 8px); font-size: 0.85rem; font-weight: 600; text-align: center; border: 1px solid rgba(16, 185, 129, 0.3);">
        ✓ {{ __('تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني بنجاح.') }}
    </div>
@endif

{{-- Error Alerts --}}
@if (session('error'))
    <div style="margin-bottom: 1.25rem; background: rgba(239, 68, 68, 0.12); color: #f87171; padding: 0.75rem 1rem; border-radius: var(--radius, 8px); font-size: 0.85rem; font-weight: 600; text-align: center; border: 1px solid rgba(239, 68, 68, 0.3);">
        ✕ {{ session('error') }}
    </div>
@endif

@if ($errors->has('otp'))
    <div style="margin-bottom: 1.25rem; background: rgba(239, 68, 68, 0.12); color: #f87171; padding: 0.75rem 1rem; border-radius: var(--radius, 8px); font-size: 0.85rem; font-weight: 600; text-align: center; border: 1px solid rgba(239, 68, 68, 0.3);">
        ✕ {{ $errors->first('otp') }}
    </div>
@endif

{{-- OTP Verification Form --}}
<form method="POST" action="{{ route('verification.verify.otp') }}" id="otpForm">
    @csrf
    
    <div class="otp-container">
        <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" autocomplete="one-time-code" autofocus>
        <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric">
        <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric">
        <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric">
        <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric">
        <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric">
    </div>

    {{-- Hidden full OTP value --}}
    <input type="hidden" name="otp" id="fullOtpInput" value="">

    <button type="submit" class="btn btn-primary w-100 py-2 mb-3" id="btnVerifySubmit" style="font-size: 1rem; font-weight: 600;">
        <span id="btnText">🔒 {{ __('تأكيد الحساب والدخول') }}</span>
        <span id="btnLoading" class="d-none">
            <span class="spinner-border spinner-border-sm me-1"></span> {{ __('جارٍ التحقق...') }}
        </span>
    </button>
</form>

{{-- Resend Section with Countdown --}}
<div style="text-align: center; padding: 0.75rem 0; border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
    <div id="resendCountdownBox" style="font-size: 0.85rem; color: var(--text-muted, #9ca3af);">
        {{ __('لم يصلك الرمز؟ يمكنك إعادة الإرسال خلال') }} 
        <span class="timer-badge" id="countdownTimer">60</span> {{ __('ثانية') }}
    </div>

    <form method="POST" action="{{ route('verification.send') }}" id="resendForm" class="d-none">
        @csrf
        <span style="font-size: 0.85rem; color: var(--text-muted, #9ca3af);">{{ __('لم يصلك الرمز؟') }}</span>
        <button type="submit" class="btn btn-link p-0 text-danger" style="font-size: 0.88rem; font-weight: 700; text-decoration: underline; margin-right: 0.35rem;">
            {{ __('إعادة إرسال رمز التحقق الآن') }}
        </button>
    </form>
</div>

{{-- Logout Option --}}
<div style="text-align: center; margin-top: 1rem;">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-muted" style="font-size: 0.8rem; text-decoration: none; opacity: 0.7;">
            ← {{ __('تسجيل الخروج أو استخدام بريد آخر') }}
        </button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const digits = Array.from(document.querySelectorAll('.otp-digit'));
    const fullInput = document.getElementById('fullOtpInput');
    const form = document.getElementById('otpForm');
    const btnSubmit = document.getElementById('btnVerifySubmit');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    function updateFullOtp() {
        const otp = digits.map(d => d.value).join('');
        fullInput.value = otp;
        return otp;
    }

    digits.forEach((digit, idx) => {
        // Handle input event
        digit.addEventListener('input', function(e) {
            const val = e.target.value;
            // Clean non-digit characters
            const num = val.replace(/[^0-9]/g, '');
            digit.value = num.length > 0 ? num[num.length - 1] : '';

            if (digit.value) {
                digit.classList.add('filled');
                if (idx < digits.length - 1) {
                    digits[idx + 1].focus();
                }
            } else {
                digit.classList.remove('filled');
            }

            const currentOtp = updateFullOtp();
            if (currentOtp.length === 6) {
                // Auto submit when all 6 digits entered
                form.requestSubmit();
            }
        });

        // Handle backspace and navigation
        digit.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!digit.value && idx > 0) {
                    digits[idx - 1].focus();
                    digits[idx - 1].value = '';
                    digits[idx - 1].classList.remove('filled');
                    e.preventDefault();
                } else {
                    digit.value = '';
                    digit.classList.remove('filled');
                }
                updateFullOtp();
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                digits[idx - 1].focus();
            } else if (e.key === 'ArrowRight' && idx < digits.length - 1) {
                digits[idx + 1].focus();
            }
        });

        // Handle paste across the inputs
        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
            const numbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
            if (numbers.length > 0) {
                numbers.split('').forEach((num, i) => {
                    if (digits[i]) {
                        digits[i].value = num;
                        digits[i].classList.add('filled');
                    }
                });
                const focusIdx = Math.min(numbers.length, digits.length - 1);
                digits[focusIdx].focus();
                
                const currentOtp = updateFullOtp();
                if (currentOtp.length === 6) {
                    form.requestSubmit();
                }
            }
        });
    });

    // Form submit state
    form.addEventListener('submit', function() {
        const otp = updateFullOtp();
        if (otp.length !== 6) {
            alert('{{ __("يرجى إدخال كافة أرقام الرمز الستة.") }}');
            event.preventDefault();
            return false;
        }
        btnSubmit.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
    });

    // Live 60-second Countdown Timer for Resend
    let timeLeft = 60;
    const timerElem = document.getElementById('countdownTimer');
    const countdownBox = document.getElementById('resendCountdownBox');
    const resendForm = document.getElementById('resendForm');

    const countdown = setInterval(function() {
        timeLeft--;
        if (timerElem) {
            timerElem.textContent = timeLeft < 10 ? '0' + timeLeft : timeLeft;
        }
        if (timeLeft <= 0) {
            clearInterval(countdown);
            if (countdownBox) countdownBox.classList.add('d-none');
            if (resendForm) resendForm.classList.remove('d-none');
        }
    }, 1000);
});
</script>
@endpush
@endsection
