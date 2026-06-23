<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? $page->title_ar : $page->title_en }} - Motorzad</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .page-content-wrapper { max-width: 900px; margin: 120px auto 60px auto; padding: 40px; background: var(--bg-card); border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .page-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem; color: var(--text); border-bottom: 1px solid var(--border); padding-bottom: 1rem; }
        .page-body { line-height: 1.8; font-size: 1.1rem; color: var(--text); }
        .page-body img { max-width: 100%; border-radius: 8px; margin: 1rem 0; }
        .page-body h1, .page-body h2, .page-body h3 { margin-top: 1.5rem; margin-bottom: 1rem; color: var(--text); }
        .page-body p { margin-bottom: 1rem; }
        .page-body ul, .page-body ol { margin-bottom: 1rem; padding-inline-start: 2rem; }
    </style>
    <script>
        (function() {
            var saved = localStorage.getItem('motorzad-landing-theme') || 'light';
            if (saved === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        })();
    </script>
</head>
<body>
    <nav class="navbar" style="background: var(--bg-card-solid); border-bottom: 1px solid var(--border);" id="navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg></div>
                <div class="logo-text"><span class="brand-motor">MOTOR</span><span class="brand-azad">AZAD</span></div>
            </a>
            <div class="nav-actions">
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="theme-toggle" aria-label="Switch Language" title="{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}">
                    <span style="font-weight:700;font-size:0.8rem">{{ app()->getLocale() == 'ar' ? 'EN' : 'ع' }}</span>
                </a>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle Theme">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ url('/') }}" class="btn btn-ghost">{{ __('Home') }}</a>
            </div>
        </div>
    </nav>

    <div class="page-content-wrapper">
        <h1 class="page-title">{{ app()->getLocale() == 'ar' ? $page->title_ar : $page->title_en }}</h1>
        <div class="page-body">
            {!! app()->getLocale() == 'ar' ? $page->content_ar : $page->content_en !!}
        </div>
    </div>

    <script>
        document.getElementById('themeToggle')?.addEventListener('click',()=>{
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const newTheme = isDark ? 'light' : 'dark';
            if (newTheme === 'light') { document.documentElement.removeAttribute('data-theme'); }
            else { document.documentElement.setAttribute('data-theme', 'dark'); }
            localStorage.setItem('motorzad-landing-theme', newTheme);
        });
    </script>
</body>
</html>
