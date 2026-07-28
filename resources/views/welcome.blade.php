<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ __('Motorzad — Car Auctions') }}">
    <title>{{ __('Motorzad — Car Auctions') }}</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script>
        (function() {
            var saved = localStorage.getItem('motorzad-landing-theme') || 'light';
            if (saved === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        })();
    </script>
    <style>
    /* Premium Hero Featured Auction Card */
    .hero-featured-auction {
        display: flex;
        justify-content: center;
        align-items: center;
        perspective: 1200px;
        position: relative;
        z-index: 2;
        width: 100%;
    }

    .premium-featured-card {
        background: rgba(var(--bg-card-rgb, 20, 25, 35), 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        width: 100%;
        max-width: 440px;
        position: relative;
        transform: rotateY(-8deg) rotateX(4deg) translateY(-10px);
        transition: all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        overflow: hidden;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.5), 
            0 0 0 1px rgba(229, 62, 62, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    /* Fallback for rgb vars if not defined */
    :root {
        --bg-card-rgb: 255, 255, 255;
    }
    html[data-theme="dark"] {
        --bg-card-rgb: 20, 25, 35;
    }

    .premium-featured-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
        opacity: 0;
        transition: opacity 0.6s;
        z-index: 10;
        pointer-events: none;
    }

    .premium-featured-card:hover {
        transform: rotateY(0) rotateX(0) translateY(-15px) scale(1.02);
        box-shadow: 
            0 35px 65px -15px rgba(229, 62, 62, 0.25), 
            0 0 0 1px rgba(229, 62, 62, 0.4),
            0 0 40px rgba(229, 62, 62, 0.15);
    }

    .premium-featured-card:hover::before {
        opacity: 1;
    }

    .pfc-glow {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 50% 50%, rgba(229, 62, 62, 0.15), transparent 60%);
        opacity: 0;
        transition: opacity 0.6s;
        pointer-events: none;
        z-index: 1;
    }

    .premium-featured-card:hover .pfc-glow {
        opacity: 1;
        animation: rotate-glow 10s linear infinite;
    }

    @keyframes rotate-glow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .pfc-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(229, 62, 62, 0.95);
        color: white;
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        z-index: 20;
        display: flex;
        align-items: center;
        gap: 8px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px rgba(229, 62, 62, 0.4);
    }

    .pfc-pulse {
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(255,255,255,0.7);
        animation: pulse-white 1.5s infinite;
    }

    .pfc-image-wrapper {
        width: 100%;
        height: 260px;
        position: relative;
        overflow: hidden;
        border-radius: 24px 24px 0 0;
        z-index: 5;
    }

    .pfc-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    .premium-featured-card:hover .pfc-image-wrapper img {
        transform: scale(1.08) translateY(-5px);
    }

    .pfc-overlay {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 60%;
        background: linear-gradient(to top, var(--bg-card) 0%, transparent 100%);
    }
    html[data-theme="dark"] .pfc-overlay {
        background: linear-gradient(to top, rgba(20, 25, 35, 1) 0%, transparent 100%);
    }

    .pfc-content {
        padding: 0 24px 24px;
        position: relative;
        z-index: 10;
        margin-top: -30px;
    }

    .pfc-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 20px;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .pfc-title span {
        font-weight: 400;
        color: var(--text-sec);
        font-size: 1.2rem;
    }

    .pfc-stats-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0, 0, 0, 0.04);
        padding: 16px;
        border-radius: 16px;
        margin-bottom: 24px;
        border: 1px solid rgba(128, 128, 128, 0.1);
    }
    
    html[data-theme="dark"] .pfc-stats-row {
        background: rgba(255, 255, 255, 0.03);
    }

    .pfc-divider {
        width: 1px;
        height: 40px;
        background: rgba(128, 128, 128, 0.2);
    }

    .pfc-stat-box {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .pfc-time-box {
        text-align: right;
    }
    
    [dir="rtl"] .pfc-time-box {
        text-align: left;
    }

    .pfc-label {
        font-size: 0.8rem;
        color: var(--text-sec);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .pfc-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text);
    }
    
    .pfc-price-box .pfc-value {
        color: var(--red);
    }
    
    .pfc-price-box small {
        font-size: 0.9rem;
        font-weight: 600;
        opacity: 0.8;
    }

    .pfc-time-box .pfc-value {
        font-variant-numeric: tabular-nums;
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: -1px;
    }

    .pfc-action-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        background: var(--red);
        color: white !important;
        padding: 16px 24px;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        position: relative;
    }

    .pfc-action-btn::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }

    .pfc-action-btn:hover {
        background: #c53030;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(229, 62, 62, 0.3);
    }

    .pfc-action-btn:hover::before {
        left: 100%;
    }
    
    .pfc-action-btn svg {
        transition: transform 0.3s ease;
    }
    
    .pfc-action-btn:hover svg {
        transform: translateX(4px);
    }
    
    [dir="rtl"] .pfc-action-btn:hover svg {
        transform: translateX(-4px);
    }
    
    [dir="rtl"] .pfc-action-btn svg {
        transform: scaleX(-1);
    }
    [dir="rtl"] .pfc-action-btn:hover svg {
        transform: scaleX(-1) translateX(4px);
    }

    [dir="rtl"] .pfc-badge {
        left: auto;
        right: 20px;
    }
    
    [dir="rtl"] .premium-featured-card {
        transform: rotateY(8deg) rotateX(4deg) translateY(-10px);
    }
    [dir="rtl"] .premium-featured-card:hover {
        transform: rotateY(0) rotateX(0) translateY(-15px) scale(1.02);
    }

    @media (max-width: 992px) {
        .hero-featured-auction {
            margin-top: 3rem;
            perspective: none;
        }
        .premium-featured-card {
            transform: none !important;
            max-width: 100%;
        }
    }
    </style>
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

        <!-- Nav Drawer: on desktop = inline links + auth, on mobile = slide-in panel -->
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

        <!-- Theme & Language (always visible) -->
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

<!-- HERO -->
<section class="hero">
    <div class="hero-container">
        <div>
            <div class="hero-badge"><span class="pulse"></span> {{ __('The #1 auction platform in the region') }}</div>
            <h1 class="hero-title">{{ \App\Models\Setting::get('hero_title_' . app()->getLocale(), __('Discover the world of car auctions with an unmatched experience')) }}</h1>
            <p class="hero-desc">{{ \App\Models\Setting::get('hero_desc_' . app()->getLocale(), __('Join thousands of bidders and get your dream car at the best price. Motorzad provides you with a safe, transparent, and fast bidding experience.')) }}</p>
            <div class="hero-actions">
                @auth
                    @if(auth()->user()->status === 'approved')
                        <a href="{{ route('bidder.auctions.index') }}" class="btn btn-primary btn-lg">{{ app()->getLocale() === 'ar' ? 'ابدأ المزايدة الآن' : 'Start Bidding Now' }}</a>
                    @else
                        <a href="{{ route('kyc.index') }}" class="btn btn-primary btn-lg">{{ app()->getLocale() === 'ar' ? 'وثق حسابك لبدء المزايدة' : 'Verify Account to Start Bidding' }}</a>
                    @endif
                @else
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ app()->getLocale() === 'ar' ? 'ابدأ المزايدة الآن' : 'Start Bidding Now' }}</a>
                    @endif
                @endauth
                <a href="#how" class="btn btn-ghost btn-lg">{{ __('How it works?') }}</a>
            </div>
            @if(\App\Models\Setting::get('show_homepage_stats', '1') == '1')
            <div class="hero-stats">
                @if(\App\Models\Setting::get('show_stat_bidders', '1') == '1')
                <div class="hero-stat"><div class="stat-num">{{ \App\Models\Setting::get('stats_active_bidders', '5') }}<span>{{ \App\Models\Setting::get('stats_active_bidders_unit', 'K+') }}</span></div><div class="stat-label">{{ __('Active Bidder') }}</div></div>
                @endif
                @if(\App\Models\Setting::get('show_stat_cars', '1') == '1')
                <div class="hero-stat"><div class="stat-num">{{ \App\Models\Setting::get('stats_cars_sold', '12') }}<span>{{ \App\Models\Setting::get('stats_cars_sold_unit', 'K+') }}</span></div><div class="stat-label">{{ __('Cars Sold') }}</div></div>
                @endif
                @if(\App\Models\Setting::get('show_stat_satisfaction', '1') == '1')
                <div class="hero-stat"><div class="stat-num">{{ \App\Models\Setting::get('stats_satisfaction', '98') }}<span>{{ \App\Models\Setting::get('stats_satisfaction_unit', '%') }}</span></div><div class="stat-label">{{ __('Customer Satisfaction') }}</div></div>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Hero Featured Auction Card -->
        <div class="hero-featured-auction">
            @if(isset($heroAuction) && $heroAuction)
                <div class="premium-featured-card">
                    <div class="pfc-glow"></div>
                    <div class="pfc-badge">
                        <span class="pfc-pulse"></span> {{ __('Featured') }}
                    </div>
                    <div class="pfc-image-wrapper">
                        <img src="{{ $heroAuction->vehicle->primary_image_url ?? asset('images/placeholder-car.jpg') }}" alt="{{ $heroAuction->vehicle->make ?? '' }} {{ $heroAuction->vehicle->model ?? '' }}">
                        <div class="pfc-overlay"></div>
                    </div>
                    <div class="pfc-content">
                        <h3 class="pfc-title">{{ $heroAuction->vehicle->make ?? '' }} {{ $heroAuction->vehicle->model ?? '' }} <span>({{ $heroAuction->vehicle->year ?? '' }})</span></h3>
                        
                        <div class="pfc-stats-row">
                            <div class="pfc-stat-box pfc-price-box">
                                <span class="pfc-label">{{ __('Current Bid') }}</span>
                                <span class="pfc-value">{{ number_format($heroAuction->highest_bid ?? $heroAuction->starting_price) }} <small>{{ __('SAR') }}</small></span>
                            </div>
                            <div class="pfc-divider"></div>
                            <div class="pfc-stat-box pfc-time-box">
                                <span class="pfc-label">{{ __('Ends In') }}</span>
                                <span class="pfc-value countdown" data-end="{{ $heroAuction->end_time }}">--:--:--</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('frontend.auctions.show', $heroAuction->id) }}" class="pfc-action-btn">
                            <span>{{ __('Bid Now') }}</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="hero-image"><img src="{{ asset('images/hero-car.png') }}" alt="{{ __('car auctions') }}"></div>
            @endif
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="section-container">
        <div class="section-header animate-on-scroll">
            <div class="section-badge">⚡ {{ __('Why Motorzad') }}</div>
            <h2 class="section-title">{{ __('Integrated platform') }} <span class="highlight">{{ __('for auctions') }}</span></h2>
            <p class="section-desc">{{ __('We provide everything you need to participate in car auctions easily and securely') }}</p>
        </div>
        <div class="features-grid">
            @foreach($features as $feature)
            <div class="feature-card animate-on-scroll">
                <div class="feature-icon {{ $feature->color_class }}">
                    {!! $feature->icon !!}
                </div>
                <h3>{{ app()->getLocale() == 'ar' ? $feature->title_ar : $feature->title_en }}</h3>
                <p>{{ app()->getLocale() == 'ar' ? $feature->description_ar : $feature->description_en }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" id="how">
    <div class="section-container">
        <div class="section-header animate-on-scroll">
            <div class="section-badge">🚀 {{ __('Simple Steps') }}</div>
            <h2 class="section-title">{{ __('How the') }} <span class="highlight">{{ __('site works') }}</span></h2>
            <p class="section-desc">{{ __('Four simple steps separate you from getting your dream car') }}</p>
        </div>
        <div class="steps-grid">
            <div class="step-card animate-on-scroll"><div class="step-num">1</div><h3>{{ __('Create Account') }}</h3><p>{{ __('Register for free and complete the verification process easily') }}</p></div>
            <div class="step-card animate-on-scroll"><div class="step-num">2</div><h3>{{ __('Browse Auctions Landing') }}</h3><p>{{ __('Discover available cars and their full details') }}</p></div>
            <div class="step-card animate-on-scroll"><div class="step-num">3</div><h3>{{ __('Place Your Bid') }}</h3><p>{{ __('Participate in the auction and submit your best price offer') }}</p></div>
            <div class="step-card animate-on-scroll"><div class="step-num">4</div><h3>{{ __('Win & Receive') }}</h3><p>{{ __('If you win the auction, complete payment and receive your car') }}</p></div>
        </div>
    </div>
</section>

<!-- LIVE AUCTIONS -->
<section class="section" id="auctions" style="background:linear-gradient(180deg,transparent,rgba(229,62,62,0.03),transparent)">
    <div class="section-container">
        <div class="section-header animate-on-scroll">
            <div class="section-badge">🔴 {{ __('Live Auctions Landing') }}</div>
            <h2 class="section-title">{{ __('Latest') }} <span class="highlight">{{ __('auctions landing') }}</span></h2>
            <p class="section-desc">{{ __('Browse the latest cars listed in our live auctions') }}</p>
        </div>
        <div class="auctions-grid" id="auctionsGrid">
            @forelse($featuredAuctions as $i => $auction)
            @php
                $colors = ['#c0392b', '#2980b9', '#f39c12', '#27ae60', '#8e44ad'];
                $color = $colors[$i % count($colors)];
            @endphp
            <div class="auction-card animate-on-scroll">
                <div class="auction-img" style="background: linear-gradient(135deg, {{ $color }}22, #0e1421); {{ $auction->primary_image_url ? 'background-image: url('.$auction->primary_image_url.'); background-size: cover; background-position: center;' : '' }}">
                    @if(!$auction->primary_image_url)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg>
                    @endif
                    <div class="auction-live"><span class="pulse"></span> {{ __('Live') }}</div>
                    @if($auction->is_featured)
                    <div class="auction-featured" title="{{ app()->getLocale() == 'ar' ? 'مزاد مميز' : 'Featured Auction' }}">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        {{ app()->getLocale() == 'ar' ? 'مميز' : 'Featured' }}
                    </div>
                    @endif
                    <div class="auction-timer countdown-timer" data-end-time="{{ $auction->end_time->format('Y-m-d\TH:i:s') }}">--:--:--</div>
                </div>
                <div class="auction-body">
                    <h3>{{ $auction->title }}</h3>
                    <div class="auction-meta">
                        <span>{{ $auction->vehicle->year ?? '-' }}</span>
                        <span class="auction-bids">{{ $auction->bids_count }} {{ __('bids') }}</span>
                    </div>
                    <div class="auction-price">
                        <div>
                            <div class="label">{{ __('Highest Bid') }}</div>
                            <div class="price">{{ number_format($auction->current_price) }} {{ __('SAR Landing') }}</div>
                        </div>
                        <a href="{{ route('bidder.auctions.show', $auction->id) }}" class="btn btn-primary btn-sm">{{ __('Bid Now Landing') }}</a>
                    </div>
                </div>
            </div>
            @empty
                <div style="text-align: center; grid-column: 1 / -1; padding: 2rem;">
                    <p style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'لا توجد مزادات مميزة حالياً' : 'No featured auctions available right now' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- STATS -->
<section class="section stats-section">
    <div class="section-container">
        <div class="stats-row">
            <div class="stat-item animate-on-scroll"><div class="num">5,000+</div><div class="label">{{ __('Registered Bidders') }}</div></div>
            <div class="stat-item animate-on-scroll"><div class="num">12,400+</div><div class="label">{{ __('Cars Sold') }}</div></div>
            <div class="stat-item animate-on-scroll"><div class="num">850+</div><div class="label">{{ __('Monthly Auctions') }}</div></div>
            <div class="stat-item animate-on-scroll"><div class="num">98%</div><div class="label">{{ __('Customer Satisfaction') }}</div></div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section">
    <div class="section-container">
        <div class="section-header animate-on-scroll">
            <div class="section-badge">💬 {{ __('Customer Reviews') }}</div>
            <h2 class="section-title">{{ __('What our') }} <span class="highlight">{{ __('customers say') }}</span></h2>
        </div>
        <div class="testimonials-grid">
            @php 
                $reviews = \App\Models\Testimonial::where('is_active', true)->latest()->take(6)->get();
            @endphp
            @foreach($reviews as $r)
            <div class="testimonial-card animate-on-scroll">
                <div class="testimonial-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>@endfor</div>
                <p class="testimonial-text">"{{ app()->getLocale() == 'ar' ? $r->text_ar : $r->text_en }}"</p>
                <div class="testimonial-author"><div class="testimonial-avatar">{{ app()->getLocale() == 'ar' ? ($r->avatar_init ?: mb_substr($r->name_ar, 0, 1)) : ($r->avatar_init_en ?: mb_substr($r->name_en, 0, 1)) }}</div><div><div class="testimonial-name">{{ app()->getLocale() == 'ar' ? $r->name_ar : $r->name_en }}</div><div class="testimonial-role">{{ app()->getLocale() == 'ar' ? $r->role_ar : $r->role_en }}</div></div></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section" id="faq">
    <div class="section-container">
        <div class="section-header animate-on-scroll">
            <div class="section-badge">❓ {{ __('FAQ Landing') }}</div>
            <h2 class="section-title">{{ __('Frequently Asked Questions') }}</h2>
        </div>
        <div class="faq-list">
            @foreach($faqs as $faq)
            <div class="faq-item animate-on-scroll">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                    <span>{{ app()->getLocale() == 'ar' ? ($faq->question_ar ?? $faq->question_en) : ($faq->question_en ?? $faq->question_ar) }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-answer"><p>{!! nl2br(e(app()->getLocale() == 'ar' ? ($faq->answer_ar ?? $faq->answer_en) : ($faq->answer_en ?? $faq->answer_ar))) !!}</p></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section">
    <div class="section-container">
        <div class="cta-content animate-on-scroll">
            <h2>{{ __('Ready to start') }} <span style="color:var(--red-light)">{{ __('bidding') }}</span>?</h2>
            <p>{{ __('Join now and start your journey in the world of car auctions with Motorzad') }}</p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Create Free Account') }}</a>
                <a href="{{ route('login') }}" class="btn btn-gold btn-lg">{{ __('Log In') }}</a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="section-container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg></div><div class="logo-text"><span class="brand-motor" style="color:var(--text)">MOTOR</span><span class="brand-azad" style="color:var(--red)">AZAD</span></div></div>
                <p>{{ __('Motorzad — The #1 destination for car auctions in the region. We provide you with a safe and transparent bidding experience.') }}</p>
                <div class="footer-social">
                    @if(\App\Models\Setting::get('show_facebook', '1') == '1' && \App\Models\Setting::get('facebook_url'))
                    <a href="{{ \App\Models\Setting::get('facebook_url') }}" aria-label="Facebook" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_twitter', '1') == '1' && \App\Models\Setting::get('twitter_url'))
                    <a href="{{ \App\Models\Setting::get('twitter_url') }}" aria-label="Twitter" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_instagram', '1') == '1' && \App\Models\Setting::get('instagram_url'))
                    <a href="{{ \App\Models\Setting::get('instagram_url') }}" aria-label="Instagram" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_linkedin', '1') == '1' && \App\Models\Setting::get('linkedin_url'))
                    <a href="{{ \App\Models\Setting::get('linkedin_url') }}" aria-label="LinkedIn" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_tiktok', '1') == '1' && \App\Models\Setting::get('tiktok_url'))
                    <a href="{{ \App\Models\Setting::get('tiktok_url') }}" aria-label="TikTok" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.12-3.44-3.17-3.65-5.46-.02-.14-.02-.29-.02-.44.02-2.31 1.25-4.48 3.23-5.65 1.17-.67 2.53-1.04 3.88-1.03v4.06c-1.3.06-2.52.79-3.2 1.86-.4.65-.63 1.4-.64 2.17-.03 1.17.65 2.27 1.67 2.75 1.05.47 2.34.42 3.32-.23.77-.52 1.25-1.39 1.25-2.32.02-5.46.01-10.92.02-16.38h4.08z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_snapchat', '1') == '1' && \App\Models\Setting::get('snapchat_url'))
                    <a href="{{ \App\Models\Setting::get('snapchat_url') }}" aria-label="Snapchat" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.65 0 4.9 1.83 5.4 4.41.22 1.16.22 2.36 0 3.52-.35 1.85-1.48 3.4-3.13 4.31a6.11 6.11 0 0 1-4.54 0c-1.65-.91-2.78-2.46-3.13-4.31a6.3 6.3 0 0 1 0-3.52C7.1 3.83 9.35 2 12 2m0-2C8.69 0 5.85 2.38 5.17 5.64a8.3 8.3 0 0 0 0 4.63c.47 2.45 2 4.49 4.14 5.68-1.74 1.16-2.9 2.92-3.1 4.97A3.33 3.33 0 0 1 5 19.8c-.85.35-1.84.44-2.75.24-.9-.2-1.7-.68-2.25-1.34V18c0-.79.52-1.46 1.25-1.71a4.34 4.34 0 0 0 2.46-1.75C3.33 13.9.72 12.01.27 9.87.1 9-.04 8.1 0 7.21c.14-3.3 1.94-6.3 4.79-7.96C6.73-.24 9.32-.4 12 0c2.68.4 5.27.56 7.21.75 2.85 1.66 4.65 4.66 4.79 7.96.04.89-.1 1.79-.27 2.66-.45 2.14-3.06 4.03-3.44 4.67a4.34 4.34 0 0 0 2.46 1.75c.73.25 1.25.92 1.25 1.71v.68c-.55.66-1.35 1.14-2.25 1.34-.91.2-1.9.11-2.75-.24a3.33 3.33 0 0 1-1.21 1.12c-.2.13-.42.22-.65.26a1.36 1.36 0 0 1-1.07-.36c-.47-.46-.66-1.12-.51-1.76a4.83 4.83 0 0 0-1.87-4.14c2.14-1.19 3.67-3.23 4.14-5.68a8.3 8.3 0 0 0 0-4.63C18.15 2.38 15.31 0 12 0z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_youtube', '1') == '1' && \App\Models\Setting::get('youtube_url'))
                    <a href="{{ \App\Models\Setting::get('youtube_url') }}" aria-label="YouTube" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 7.1c.3-1.6 1.4-2.8 2.9-3.2 2.6-.5 5.5-.6 6.6-.6 1.1 0 4 .1 6.6.6 1.5.4 2.6 1.6 2.9 3.2.4 2 .4 4.3.4 4.9s0 2.9-.4 4.9c-.3 1.6-1.4 2.8-2.9 3.2-2.6.5-5.5.6-6.6.6-1.1 0-4-.1-6.6-.6-1.5-.4-2.6-1.6-2.9-3.2-.4-2-.4-4.3-.4-4.9s0-2.9.4-4.9z"/><path d="m10 15 5-3-5-3v6z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_telegram', '1') == '1' && \App\Models\Setting::get('telegram_url'))
                    <a href="{{ \App\Models\Setting::get('telegram_url') }}" aria-label="Telegram" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.18 3.51a1.5 1.5 0 0 0-1.77-.28l-16.5 7A1.5 1.5 0 0 0 2.8 12.92l3.43 1.1 1.7 5.16a1.5 1.5 0 0 0 2.76.24l2.84-3.4 4.92 3.65a1.5 1.5 0 0 0 2.39-1l1.5-13.5a1.5 1.5 0 0 0-1.16-1.66zm-12.7 8l8.9-5.66-7.38 6.65-1.52.95zm-1 2.32l-.93-2.83 9.46-5.88z"/></svg></a>
                    @endif
                    @if(\App\Models\Setting::get('show_whatsapp', '1') == '1' && \App\Models\Setting::get('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number')) }}" aria-label="WhatsApp" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                    @endif
                </div>
            </div>
            <div class="footer-col"><h4>{{ __('Quick Links') }}</h4><ul><li><a href="#features">{{ __('Features') }}</a></li><li><a href="{{ route('frontend.auctions.index') }}">{{ __('auctions landing') }}</a></li><li><a href="#how">{{ __('How it works?') }}</a></li><li><a href="#faq">{{ __('Questions') }}</a></li></ul></div>
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

    // === Scroll animations ===
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.animate-on-scroll').forEach(el => obs.observe(el));

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

    // Close menu when clicking a nav link
    document.querySelectorAll('.nav-link-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) closeMenu();
        });
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) closeMenu();
    });

    // === Countdown timers ===
    document.querySelectorAll('.countdown-timer').forEach(el => {
        const end = new Date(el.getAttribute('data-end-time')).getTime();
        function updateTimer() {
            const dist = end - Date.now();
            if (dist < 0) { el.innerText = '00:00:00'; return; }
            const h = Math.floor((dist % 86400000) / 3600000);
            const m = Math.floor((dist % 3600000) / 60000);
            const s = Math.floor((dist % 60000) / 1000);
            el.innerText = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    });
})();
</script>
</body>
</html>
