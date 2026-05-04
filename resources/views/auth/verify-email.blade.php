@extends('layouts.auth')

@section('title', __('Verify Email'))
@section('subtitle', __('Please verify your email to access all features'))

@section('content')
<div style="text-align: center; margin-bottom: 1.5rem;">
    <div style="background: var(--info-glow); color: var(--info); padding: 1rem; border-radius: var(--radius); font-size: 0.9rem; line-height: 1.6; border: 1px solid rgba(6, 182, 212, 0.2);">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>
</div>

@if (session('status') == 'verification-link-sent')
    <div style="margin-bottom: 1.5rem; background: var(--success-glow); color: var(--success); padding: 0.75rem 1rem; border-radius: var(--radius); font-size: 0.85rem; font-weight: 600; text-align: center; border: 1px solid rgba(16, 185, 129, 0.2);">
        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
    </div>
@endif

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            {{ __('Resend Verification Email') }}
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="text-align: center;">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--text-muted); border: none;">
            {{ __('Log Out') }}
        </button>
    </form>
</div>
@endsection
