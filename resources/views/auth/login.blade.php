<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorzad - تسجيل الدخول</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            background-color: var(--bg-body);
            font-family: 'Tajawal', sans-serif;
        }

        /* Banner Section (Image) */
        .auth-banner {
            flex: 1;
            display: none;
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1542362567-b07e54358753?q=80&w=2070&auto=format&fit=crop'); /* Interior / Dashboard Image */
            background-size: cover;
            background-position: center;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, var(--bg-body) 0%, rgba(6, 9, 15, 0.6) 100%);
        }
        
        [data-theme="light"] .auth-banner::before {
            background: linear-gradient(to right, var(--bg-body) 0%, rgba(255, 255, 255, 0.4) 100%);
        }

        .auth-banner-content {
            position: absolute;
            bottom: 15%;
            right: 10%;
            left: 10%;
            z-index: 10;
        }

        .auth-banner-content h1 {
            color: #fff;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        
        .auth-banner-content p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            font-weight: 500;
            max-width: 500px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        @media (min-width: 992px) {
            .auth-banner { display: block; }
        }

        /* Form Section */
        .auth-form-container {
            width: 100%;
            max-width: 550px;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: var(--bg-body);
            position: relative;
            z-index: 5;
            box-shadow: -20px 0 40px rgba(0,0,0,0.1);
        }

        .auth-header {
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        .brand-logo .text-motor { color: var(--text); }
        .brand-logo .text-zad { color: var(--brand-red); }

        .auth-header h2 {
            color: var(--text);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.9rem 1.2rem;
            color: var(--text);
            font-family: inherit;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            background: var(--bg-hover);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        /* Checkbox customization */
        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-left: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .checkbox-container:hover input ~ .checkmark {
            background-color: var(--bg-hover);
            border-color: var(--primary-light);
        }

        .checkbox-container input:checked ~ .checkmark {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .checkmark:after {
            content: "";
            display: none;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }

        /* Button */
        .btn-login {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 1.1rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px var(--primary-glow);
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-right: 0.5rem;
        }

        .auth-footer a:hover {
            color: var(--primary-light);
        }

        @media (max-width: 576px) {
            .auth-form-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Split Screen Design: Form Side -->
    <div class="auth-form-container">
        
        <div class="auth-header">
            <a href="/" style="text-decoration: none;">
                <div class="brand-logo">
                    <span class="text-motor">MOTOR</span><span class="text-zad">ZAD</span>
                </div>
            </a>
            <h2>مرحباً بك مجدداً</h2>
            <p>قم بتسجيل الدخول للمتابعة إلى حسابك في موتورزاد.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="margin-bottom: 0;">كلمة المرور</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600; transition: 0.2s;">نسيت كلمة المرور؟</a>
                    @endif
                </div>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                @error('password')
                    <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="checkmark"></span>
                    <span style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 500;">تذكرني على هذا الجهاز</span>
                </label>
            </div>

            <button type="submit" class="btn-login">
                تسجيل الدخول للمنصة
            </button>
        </form>

        <div class="auth-footer">
            ليس لديك حساب بعد؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
        </div>

    </div>

    <!-- Split Screen Design: Banner Side -->
    <div class="auth-banner">
        <div class="auth-banner-content">
            <h1>أداء يفوق التوقعات</h1>
            <p>منصتك الأولى للوصول إلى أندر وأفضل السيارات. ادخل إلى حسابك الآن لاستكمال مزايداتك ومتابعة سياراتك المفضلة.</p>
        </div>
    </div>

    <script>
        // Apply theme on load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', currentTheme);
        });
    </script>
</body>
</html>
