@extends('layouts.auth')

@section('title', 'إنشاء حساب')
@section('subtitle', 'انضم إلى أكبر منصة مزادات سيارات')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label class="form-label">الاسم الكامل</label>
        <input type="text" name="name" class="form-control" placeholder="أدخل اسمك بالكامل" value="{{ old('name') }}" required autofocus>
        @error('name')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
        @error('email')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        @error('password')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
        إنشاء حساب مجاني
    </button>

    <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        لديك حساب بالفعل؟ <a href="{{ route('login') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">تسجيل الدخول</a>
    </div>
</form>
@endsection
