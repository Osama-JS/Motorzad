@extends('layouts.auth')

@section('title', 'تسجيل الدخول')
@section('subtitle', 'مرحباً بك مجدداً في موتورزاد')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        @error('email')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <label class="form-label">كلمة المرور</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: var(--primary-light); text-decoration: none;">نسيت كلمة المرور؟</a>
            @endif
        </div>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        @error('password')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-item" style="border: none; background: none; padding: 0;">
            <input type="checkbox" name="remember">
            <span class="check-label" style="font-size: 0.85rem; color: var(--text-secondary);">تذكرني على هذا الجهاز</span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
        دخول للمنصة
    </button>

    <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        ليس لديك حساب؟ <a href="{{ route('register') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">إنشاء حساب جديد</a>
    </div>
</form>
@endsection
