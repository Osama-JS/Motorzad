@extends('layouts.auth')

@section('title', 'إنشاء حساب جديد')
@section('subtitle', 'انضم إلى منصة موتورزاد وابدأ المزايدة الآن')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">الاسم الأول</label>
                <input type="text" name="first_name" class="form-control" placeholder="مثال: أحمد" value="{{ old('first_name') }}" required autofocus>
                @error('first_name')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">الاسم الأخير</label>
                <input type="text" name="last_name" class="form-control" placeholder="مثال: محمد" value="{{ old('last_name') }}" required>
                @error('last_name')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
        @error('email')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">رقم الهاتف</label>
        <div style="display: flex; gap: 0.5rem;">
            <select name="country_code" class="form-control" style="max-width: 120px;" required>
                @include('partials.country-codes', ['selected' => old('country_code', '+966')])
            </select>
            <input type="text" name="phone" class="form-control" placeholder="50xxxxxxx" value="{{ old('phone') }}" required>
        </div>
        @error('phone')
            <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
        @enderror
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                @error('password')
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
        إنشاء حساب مجاني
    </button>

    <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        لديك حساب بالفعل؟ <a href="{{ route('login') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">تسجيل الدخول</a>
    </div>
</form>
@endsection
