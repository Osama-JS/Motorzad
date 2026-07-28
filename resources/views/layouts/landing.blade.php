<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ __('Motorzad — Car Auctions') }}">
    <title>@yield('title', __('Motorzad — Car Auctions'))</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    @stack('styles')
    <script>
        (function() {
            var saved = localStorage.getItem('motorzad-landing-theme') || 'light';
            if (saved === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        })();
    </script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="/" class="nav-logo">
            <div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg></div>
            <div class="logo-text"><span class="brand-motor">MOTOR</span><span class="brand-azad">AZAD</span></div>
        </a>

        <!-- Nav Drawer -->
        <div class="nav-drawer" id="navDrawer">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}" class="nav-link-item">{{ __('Home') }}</a></li>
                <li><a href="{{ route('frontend.auctions.index') }}" class="nav-link-item">{{ __('Auctions') }}</a></li>
                <li><a href="{{ route('frontend.contact') }}" class="nav-link-item">{{ __('Contact Us') }}</a></li>
            </ul>
            <div class="nav-auth">
                @if(Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Control Panel') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Log In') }}</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Create Account') }}</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <!-- Theme & Language -->
        <div class="nav-actions">
            <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="theme-toggle" aria-label="Switch Language" title="{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}">
                <span style="font-weight:700;font-size:0.8rem">{{ app()->getLocale() == 'ar' ? 'EN' : 'ع' }}</span>
            </a>
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle Theme">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        </div>

        <!-- Mobile Hamburger -->
        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>
    </div>
</nav>

@yield('content')

<!-- FOOTER -->
<footer class="footer">
    <div class="section-container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg></div><div class="logo-text"><span class="brand-motor" style="color:var(--text)">MOTOR</span><span class="brand-azad" style="color:var(--red)">AZAD</span></div></div>
                <p>{{ __('Motorzad — The #1 destination for car auctions in the region. We provide you with a safe and transparent bidding experience.') }}</p>
                <div class="footer-social">
                    @if(\App\Models\Setting::get('show_twitter', '1') == '1' && \App\Models\Setting::get('twitter_url'))
                    <a href="{{ \App\Models\Setting::get('twitter_url') }}" aria-label="Twitter" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_instagram', '1') == '1' && \App\Models\Setting::get('instagram_url'))
                    <a href="{{ \App\Models\Setting::get('instagram_url') }}" aria-label="Instagram" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
                    @endif
                </div>
            </div>
            <div class="footer-col"><h4>{{ __('Quick Links') }}</h4><ul><li><a href="/#features">{{ __('Features') }}</a></li><li><a href="{{ route('frontend.auctions.index') }}">{{ __('Auctions') }}</a></li><li><a href="/#how">{{ __('How it works?') }}</a></li><li><a href="/#faq">{{ __('Questions') }}</a></li></ul></div>
            <div class="footer-col"><h4>{{ __('Account') }}</h4><ul><li><a href="{{ route('login') }}">{{ __('Log In') }}</a></li><li><a href="{{ route('register') }}">{{ __('Create Account') }}</a></li></ul></div>
            <div class="footer-col"><h4>{{ __('Contact Us') }}</h4><ul><li><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@motorzad.com') }}">{{ \App\Models\Setting::get('contact_email', 'support@motorzad.com') }}</a></li><li><a href="tel:{{ preg_replace('/[^0-9+]/', '', \App\Models\Setting::get('contact_phone', '+966500000000')) }}" dir="ltr">{{ \App\Models\Setting::get('contact_phone', '+966 500 000 000') }}</a></li></ul></div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} موتورزاد. {{ __('All rights reserved.') }}</span>
            <span>{{ __('Made with ❤️ in Saudi Arabia') }}</span>
        </div>
    </div>
</footer>

<script>
(function() {
    'use strict';

    // === Navbar scroll effect ===
    const navbar = document.getElementById('navbar');
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        navbar.classList.toggle('scrolled', scrollY > 50);
        lastScroll = scrollY;
    }, { passive: true });

    // === Theme toggle ===
    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';
        if (newTheme === 'light') document.documentElement.removeAttribute('data-theme');
        else document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('motorzad-landing-theme', newTheme);
    });

    // === Mobile Menu ===
    const mobileToggle = document.getElementById('mobileToggle');
    const navDrawer = document.getElementById('navDrawer');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const body = document.body;

    function openMenu() {
        mobileToggle.classList.add('active');
        navDrawer.classList.add('open');
        mobileOverlay.classList.add('open');
        body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileToggle.classList.remove('active');
        navDrawer.classList.remove('open');
        mobileOverlay.classList.remove('open');
        body.style.overflow = '';
    }

    mobileToggle?.addEventListener('click', () => {
        if (navDrawer.classList.contains('open')) closeMenu();
        else openMenu();
    });

    mobileOverlay?.addEventListener('click', closeMenu);

    document.querySelectorAll('.nav-link-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) closeMenu();
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) closeMenu();
    });

    // === Countdown timers ===
    function initCountdowns() {
        document.querySelectorAll('.countdown-timer').forEach(el => {
            if(el.dataset.initialized) return;
            el.dataset.initialized = true;
            const end = new Date(el.getAttribute('data-end-time')).getTime();
            function updateTimer() {
                const dist = end - Date.now();
                if (dist < 0) { el.innerText = '00:00:00'; return; }
                const h = Math.floor((dist % 86400000) / 3600000) + Math.floor(dist / 86400000) * 24;
                const m = Math.floor((dist % 3600000) / 60000);
                const s = Math.floor((dist % 60000) / 1000);
                el.innerText = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
            }
            updateTimer();
            setInterval(updateTimer, 1000);
        });
    }
    initCountdowns();
    window.initCountdowns = initCountdowns;
})();
</script>
@stack('scripts')
</body>
</html>
