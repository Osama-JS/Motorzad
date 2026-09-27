<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorzad - حذف الحساب</title>
    <meta name="description" content="صفحة حذف حساب المستخدم في منصة موتورزاد. يمكنك من خلال هذه الصفحة طلب حذف حسابك وبياناتك الشخصية بشكل نهائي.">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-body);
            font-family: 'Tajawal', sans-serif;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glow effects */
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(229, 62, 62, 0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.04) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .deletion-wrapper {
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 1;
            animation: fadeSlideUp 0.6s ease;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Main card */
        .deletion-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
        }

        /* Racing stripe at top */
        .deletion-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--danger), var(--brand-gold), var(--danger));
            z-index: 2;
        }

        /* Header section */
        .deletion-header {
            padding: 2.5rem 2.5rem 0;
            text-align: center;
        }

        .brand-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            display: inline-block;
            text-decoration: none;
        }
        .brand-logo .text-motor { color: var(--text); }
        .brand-logo .text-zad { color: var(--brand-red); }

        /* Danger icon */
        .danger-icon-wrapper {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.15); }
            50% { box-shadow: 0 0 20px 8px rgba(239, 68, 68, 0.08); }
        }

        .danger-icon-wrapper svg {
            width: 32px;
            height: 32px;
            color: var(--danger);
        }

        .deletion-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .deletion-header .subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Body section */
        .deletion-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* Info box */
        .info-box {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-box-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box-title svg {
            width: 16px;
            height: 16px;
            color: var(--brand-gold);
            flex-shrink: 0;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-box li {
            color: var(--text-secondary);
            font-size: 0.85rem;
            padding: 0.4rem 0;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            line-height: 1.6;
        }

        .info-box li::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--danger);
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 0.5rem;
        }

        /* Consequences section */
        .consequences-box {
            background: rgba(239, 68, 68, 0.04);
            border: 1px solid rgba(239, 68, 68, 0.12);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }

        .consequences-box .info-box-title svg {
            color: var(--danger);
        }

        .consequences-box li::before {
            background: var(--text-muted);
        }

        /* Divider */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 1.5rem 0;
        }

        /* Error alert */
        .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            animation: shakeAlert 0.5s ease;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-danger li {
            color: var(--danger);
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.2rem 0;
        }

        .alert-danger li svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* Form */
        .form-group { margin-bottom: 1.5rem; }

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
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            background: var(--bg-hover);
            border-color: var(--danger);
            box-shadow: 0 0 0 4px var(--danger-glow);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            display: block;
        }

        /* Buttons */
        .btn-delete {
            width: 100%;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            position: relative;
            overflow: hidden;
        }

        .btn-delete::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
        }

        .btn-delete:hover::before {
            transform: translateX(100%);
        }

        .btn-delete:active {
            transform: translateY(0);
        }

        .btn-delete svg {
            width: 20px;
            height: 20px;
        }

        .btn-cancel {
            width: 100%;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 0.9rem;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.75rem;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-cancel:hover {
            background: var(--bg-hover);
            border-color: var(--border-light);
            color: var(--text);
        }

        /* Confirmation modal overlay */
        .confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            animation: fadeIn 0.3s ease;
        }

        .confirm-overlay.active { display: flex; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .confirm-dialog {
            background: var(--bg-card-solid);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: scaleIn 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .confirm-dialog h3 {
            color: var(--text);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .confirm-dialog p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .confirm-actions {
            display: flex;
            gap: 0.75rem;
        }

        .confirm-actions .btn-confirm-yes {
            flex: 1;
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 700;
            font-family: inherit;
            font-size: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .confirm-actions .btn-confirm-yes:hover {
            background: #b91c1c;
        }

        .confirm-actions .btn-confirm-no {
            flex: 1;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 0.75rem;
            font-weight: 600;
            font-family: inherit;
            font-size: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .confirm-actions .btn-confirm-no:hover {
            background: var(--bg-hover);
        }

        /* Footer */
        .deletion-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .deletion-footer p {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .deletion-footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .deletion-footer a:hover { color: var(--brand-red); }

        /* Responsive */
        @media (max-width: 576px) {
            body { padding: 1rem; }
            .deletion-header { padding: 2rem 1.5rem 0; }
            .deletion-body { padding: 1.5rem; }
            .deletion-header h1 { font-size: 1.25rem; }
            .danger-icon-wrapper { width: 60px; height: 60px; }
            .danger-icon-wrapper svg { width: 26px; height: 26px; }
            .info-box, .consequences-box { padding: 1rem 1.25rem; }
        }
    </style>
</head>
<body>

    <div class="deletion-wrapper">

        <!-- Main Card -->
        <div class="deletion-card">

            <!-- Header -->
            <div class="deletion-header">
                <a href="/" class="brand-logo">
                    <span class="text-motor">MOTOR</span><span class="text-zad">ZAD</span>
                </a>

                <div class="danger-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>

                <h1>حذف الحساب نهائياً</h1>
                <p class="subtitle">بمجرد تأكيد الحذف، سيتم تعطيل حسابك وإخفاء بياناتك الشخصية من المنصة بشكل لا يمكن التراجع عنه.</p>
            </div>

            <!-- Body -->
            <div class="deletion-body">

                <!-- Error Messages -->
                @if (session('error') || $errors->any())
                    <div class="alert-danger">
                        <ul>
                            @if (session('error'))
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    {{ session('error') }}
                                </li>
                            @endif
                            @foreach ($errors->all() as $error)
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Restrictions info box -->
                <div class="info-box">
                    <div class="info-box-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        لا يمكن حذف الحساب إذا كان لديك:
                    </div>
                    <ul>
                        <li>رصيد متاح في المحفظة — يجب سحبه أولاً.</li>
                        <li>مزادات نشطة أو مجدولة تحت حسابك.</li>
                        <li>مزايدات نشطة على مزادات جارية حالياً.</li>
                    </ul>
                </div>

                <!-- Consequences info box -->
                <div class="consequences-box">
                    <div class="info-box-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        ماذا سيحدث عند الحذف:
                    </div>
                    <ul>
                        <li>سيتم تعطيل حسابك وإخفاء بياناتك الشخصية.</li>
                        <li>لن تتمكن من تسجيل الدخول بعد الحذف.</li>
                        <li>نحتفظ بالسجلات المالية لأغراض التدقيق القانوني فقط.</li>
                    </ul>
                </div>

                <div class="divider"></div>

                <!-- Password Confirmation Form -->
                <form method="POST" action="{{ route('account.delete.confirm') }}" id="deleteForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="password">أدخل كلمة المرور لتأكيد هويتك:</label>
                        <input
                            type="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        @error('password', 'userDeletion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="button" class="btn-delete" id="deleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                        تأكيد حذف الحساب نهائياً
                    </button>

                    <a href="{{ url('/') }}" class="btn-cancel">تراجع والعودة للرئيسية</a>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="deletion-footer">
            <p>تحتاج مساعدة؟ <a href="{{ url('/contact') }}">تواصل مع الدعم الفني</a></p>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-dialog">
            <div class="danger-icon-wrapper" style="margin-bottom: 1rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:28px;height:28px;color:var(--danger);">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <h3>هل أنت متأكد تماماً؟</h3>
            <p>هذا الإجراء لا يمكن التراجع عنه. سيتم تعطيل حسابك وإخفاء جميع بياناتك الشخصية نهائياً.</p>
            <div class="confirm-actions">
                <button type="button" class="btn-confirm-yes" id="confirmYes">نعم، احذف حسابي</button>
                <button type="button" class="btn-confirm-no" id="confirmNo">لا، تراجع</button>
            </div>
        </div>
    </div>

    <script>
        // Apply saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', currentTheme);
        });

        // Confirmation dialog flow
        const deleteBtn = document.getElementById('deleteBtn');
        const confirmOverlay = document.getElementById('confirmOverlay');
        const confirmYes = document.getElementById('confirmYes');
        const confirmNo = document.getElementById('confirmNo');
        const deleteForm = document.getElementById('deleteForm');
        const passwordField = document.getElementById('password');

        deleteBtn.addEventListener('click', function() {
            if (!passwordField.value.trim()) {
                passwordField.focus();
                passwordField.style.borderColor = 'var(--danger)';
                passwordField.style.boxShadow = '0 0 0 4px var(--danger-glow)';
                return;
            }
            confirmOverlay.classList.add('active');
        });

        confirmYes.addEventListener('click', function() {
            deleteForm.submit();
        });

        confirmNo.addEventListener('click', function() {
            confirmOverlay.classList.remove('active');
        });

        confirmOverlay.addEventListener('click', function(e) {
            if (e.target === confirmOverlay) {
                confirmOverlay.classList.remove('active');
            }
        });

        // Reset input style on focus
        passwordField.addEventListener('focus', function() {
            this.style.borderColor = '';
            this.style.boxShadow = '';
        });
    </script>
</body>
</html>
