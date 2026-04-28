<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorazad - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        body::before {
            top: -40%;
            right: -25%;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.6s ease;
        }

        .auth-card {
            padding: 2.5rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo .logo-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--brand-red), #991b1b);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 25px rgba(229,62,62,0.35);
        }

        .auth-logo .logo-icon svg {
            width: 30px;
            height: 30px;
            color: white;
        }

        .auth-logo .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .auth-logo .logo-text .brand-motor { color: var(--text); }
        .auth-logo .logo-text .brand-azad { color: var(--brand-red); }

        .auth-logo .subtitle {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        /* Red racing stripe at top of card */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-red), var(--brand-gold), var(--brand-red));
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card" style="position: relative;">
            <div class="auth-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 17h2l2-4h6l2 4h2"/>
                        <circle cx="7.5" cy="17.5" r="2.5"/>
                        <circle cx="16.5" cy="17.5" r="2.5"/>
                        <path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <span class="brand-motor">MOTOR</span><span class="brand-azad">AZAD</span>
                </div>
                <p class="subtitle">@yield('subtitle')</p>
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>
