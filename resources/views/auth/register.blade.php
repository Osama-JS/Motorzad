<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorzad - إنشاء حساب جديد</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" />
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
            background-image: url('https://images.unsplash.com/photo-1583121274602-3e2820c69888?q=80&w=2070&auto=format&fit=crop'); /* Luxury/Sports Car Image */
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

        /* Select2 Theme Tweaks */
        .select2-container--default .select2-selection--single {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px;
            height: 52px;
            display: flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text);
            padding-right: 15px;
            padding-left: 20px;
            font-weight: 500;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            left: 10px;
            right: auto;
        }
        
        .select2-dropdown {
            background: var(--bg-card-solid);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow);
            color: var(--text);
        }
        
        .select2-search--dropdown .select2-search__field {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            padding: 0.5rem;
        }
        
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--primary);
            color: white;
        }
        
        .select2-container--default .select2-results__option--selected {
            background-color: var(--bg-hover);
        }

        /* Button */
        .btn-register {
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

        .btn-register:hover {
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
        
        .phone-layout {
            display: flex;
            gap: 1rem;
        }
        .phone-layout .country-code-wrapper {
            flex: 0 0 120px;
        }
        .phone-layout .phone-wrapper {
            flex: 1;
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
            <h2>ابدأ رحلتك معنا</h2>
            <p>سجل حسابك الآن وشارك في أضخم مزادات السيارات الموثوقة.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row" style="display: flex; gap: 1rem; margin-bottom: 0;">
                <div style="flex: 1;">
                    <div class="form-group">
                        <label class="form-label">الاسم الأول</label>
                        <input type="text" name="first_name" class="form-control" placeholder="أحمد" value="{{ old('first_name') }}" required autofocus>
                        @error('first_name')
                            <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div style="flex: 1;">
                    <div class="form-group">
                        <label class="form-label">الاسم الأخير</label>
                        <input type="text" name="last_name" class="form-control" placeholder="محمد" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
                @error('email')
                    <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <div class="phone-layout">
                    <div class="country-code-wrapper">
                        <select name="country_code" class="form-control select2-country" required>
                            @include('partials.country-codes', ['selected' => old('country_code', '+966')])
                        </select>
                    </div>
                    <div class="phone-wrapper">
                        <input type="text" name="phone" class="form-control" placeholder="50xxxxxxx" value="{{ old('phone') }}" required>
                    </div>
                </div>
                @error('phone')
                    <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="row" style="display: flex; gap: 1rem; margin-bottom: 0;">
                <div style="flex: 1;">
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        @error('password')
                            <span style="color: var(--danger); font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div style="flex: 1;">
                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                تأكيد وإنشاء الحساب
            </button>
        </form>

        <div class="auth-footer">
            تمتلك حساباً بالفعل؟ <a href="{{ route('login') }}">قم بتسجيل الدخول</a>
        </div>

    </div>

    <!-- Split Screen Design: Banner Side -->
    <div class="auth-banner">
        <div class="auth-banner-content">
            <h1>عالم السيارات الفاخرة بين يديك</h1>
            <p>انضم إلى أكبر منصة مزادات تفاعلية في الشرق الأوسط. اكتشف، زايد، وتملك سيارة أحلامك بأعلى معايير الشفافية والموثوقية.</p>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Check current theme to apply appropriately
            const currentTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', currentTheme);

            $('.select2-country').select2({
                dir: "rtl",
                width: '100%',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 0,
                language: {
                    noResults: function() {
                        return "لم يتم العثور على نتائج";
                    }
                }
            });
        });
    </script>
</body>
</html>
