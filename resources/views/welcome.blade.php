<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('Motorzad — Car Auctions') }}">
    <title>{{ __('Motorzad — Car Auctions') }}</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
        <a href="/" class="nav-logo">
            <div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg></div>
            <div class="logo-text"><span class="brand-motor">MOTOR</span><span class="brand-azad">AZAD</span></div>
        </a>
        <ul class="nav-links">
            <li><a href="#features">{{ __('Features') }}</a></li>
            <li><a href="#auctions">{{ __('auctions landing') }}</a></li>
            <li><a href="#how">{{ __('How it works?') }}</a></li>
            <li><a href="#faq">{{ __('Questions') }}</a></li>
        </ul>
        <div class="nav-actions">
            <!-- Language Switcher -->
            <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="theme-toggle" aria-label="Switch Language" title="{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}">
                <span style="font-weight:700;font-size:0.8rem">{{ app()->getLocale() == 'ar' ? 'EN' : 'ع' }}</span>
            </a>
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle Theme">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">{{ __('Control Panel') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Log In') }}</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Create Account') }}</a>
                    @endif
                @endauth
            @endif
        </div>
        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-container">
        <div>
            <div class="hero-badge"><span class="pulse"></span> {{ __('The #1 auction platform in the region') }}</div>
            <h1 class="hero-title">{{ __('Discover the world of') }} <span class="highlight">{{ __('car auctions') }}</span> {{ __('with an unmatched experience') }}</h1>
            <p class="hero-desc">{{ __('Join thousands of bidders and get your dream car at the best price. Motorzad provides you with a safe, transparent, and fast bidding experience.') }}</p>
            <div class="hero-actions">
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Start Bidding Now') }}</a>
                @endif
                <a href="#how" class="btn btn-ghost btn-lg">{{ __('How it works?') }}</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="stat-num">5<span>K+</span></div><div class="stat-label">{{ __('Active Bidder') }}</div></div>
                <div class="hero-stat"><div class="stat-num">12<span>K+</span></div><div class="stat-label">{{ __('Cars Sold') }}</div></div>
                <div class="hero-stat"><div class="stat-num">98<span>%</span></div><div class="stat-label">{{ __('Customer Satisfaction') }}</div></div>
            </div>
        </div>
        <div class="hero-image"><img src="{{ asset('images/hero-car.png') }}" alt="{{ __('car auctions') }}"></div>
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
            <div class="feature-card animate-on-scroll"><div class="feature-icon red"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>{{ __('Ultimate Security') }}</h3><p>{{ __('All transactions are protected and encrypted with the highest security standards') }}</p></div>
            <div class="feature-card animate-on-scroll"><div class="feature-icon gold"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><h3>{{ __('Instant Auctions') }}</h3><p>{{ __('Follow auctions moment by moment with live price and offer updates') }}</p></div>
            <div class="feature-card animate-on-scroll"><div class="feature-icon blue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><h3>{{ __('Smart Interface') }}</h3><p>{{ __('Modern and user-friendly design that works seamlessly on all devices') }}</p></div>
            <div class="feature-card animate-on-scroll"><div class="feature-icon green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><h3>{{ __('Full Verification') }}</h3><p>{{ __('Integrated KYC system to verify user identity and ensure transaction credibility') }}</p></div>
            <div class="feature-card animate-on-scroll"><div class="feature-icon red"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><h3>{{ __('Secure Payment') }}</h3><p>{{ __('Direct link with your bank accounts to facilitate payment and receiving') }}</p></div>
            <div class="feature-card animate-on-scroll"><div class="feature-icon gold"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div><h3>{{ __('24/7 Support') }}</h3><p>{{ __('Specialized support team ready to help you around the clock') }}</p></div>
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
        <div class="auctions-grid">
            @php $cars = [['name'=>__('Mercedes AMG GT'),'year'=>'2024','price'=>'185,000','bids'=>23,'color'=>'#c0392b'],['name'=>__('BMW M4'),'year'=>'2025','price'=>'142,500','bids'=>18,'color'=>'#2980b9'],['name'=>__('Porsche Cayenne'),'year'=>'2024','price'=>'225,000','bids'=>31,'color'=>'#f39c12']]; @endphp
            @foreach($cars as $i => $car)
            <div class="auction-card animate-on-scroll">
                <div class="auction-img" style="background:linear-gradient(135deg,{{ $car['color'] }}22,#0e1421)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg>
                    <div class="auction-live"><span class="pulse"></span> {{ __('Live') }}</div>
                    <div class="auction-timer">02:{{ 45-$i*12 }}:{{ 30+$i*7 }}</div>
                </div>
                <div class="auction-body">
                    <h3>{{ $car['name'] }}</h3>
                    <div class="auction-meta"><span>{{ $car['year'] }}</span><span class="auction-bids">{{ $car['bids'] }} {{ __('bids') }}</span></div>
                    <div class="auction-price"><div><div class="label">{{ __('Highest Bid') }}</div><div class="price">{{ $car['price'] }} {{ __('SAR Landing') }}</div></div><a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('Bid Now Landing') }}</a></div>
                </div>
            </div>
            @endforeach
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
            @php $reviews = [
                ['name'=> app()->getLocale()=='ar'?'أحمد الشمري':'Ahmed Al-Shamri','role'=>__('Distinguished Bidder'),'text'=>__('Amazing experience! I got my car at a great price and the process was very smooth'),'init'=> app()->getLocale()=='ar'?'أ':'A'],
                ['name'=> app()->getLocale()=='ar'?'سارة القحطاني':'Sara Al-Qahtani','role'=>__('New Customer'),'text'=>__('The platform is easy to use and the support team is very helpful. I highly recommend it'),'init'=> app()->getLocale()=='ar'?'س':'S'],
                ['name'=> app()->getLocale()=='ar'?'محمد العتيبي':'Mohammed Al-Otaibi','role'=>__('Car Dealer'),'text'=>__('Motorzad changed the way of selling cars. Excellent results and high transparency'),'init'=> app()->getLocale()=='ar'?'م':'M']
            ]; @endphp
            @foreach($reviews as $r)
            <div class="testimonial-card animate-on-scroll">
                <div class="testimonial-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>@endfor</div>
                <p class="testimonial-text">"{{ $r['text'] }}"</p>
                <div class="testimonial-author"><div class="testimonial-avatar">{{ $r['init'] }}</div><div><div class="testimonial-name">{{ $r['name'] }}</div><div class="testimonial-role">{{ $r['role'] }}</div></div></div>
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
            @php $faqs = [
                ['q'=>__('How do I register?'),'a'=>__('You can register for free through the create account page. You will need to provide basic info and complete identity verification.')],
                ['q'=>__('Is the platform secure?'),'a'=>__('Yes, we use the highest security and encryption standards to protect your data and financial transactions.')],
                ['q'=>__('How do I participate in an auction?'),'a'=>__('After registering and verifying your account, you can browse available auctions and submit your bids directly.')],
                ['q'=>__('What payment methods are available?'),'a'=>__('We support direct bank transfer through approved bank accounts to ensure transaction security.')]
            ]; @endphp
            @foreach($faqs as $faq)
            <div class="faq-item animate-on-scroll">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                    <span>{{ $faq['q'] }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-answer"><p>{{ $faq['a'] }}</p></div>
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
                    <a href="#" aria-label="Twitter"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                    <a href="#" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
                    <a href="#" aria-label="WhatsApp"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                </div>
            </div>
            <div class="footer-col"><h4>{{ __('Quick Links') }}</h4><ul><li><a href="#features">{{ __('Features') }}</a></li><li><a href="#auctions">{{ __('auctions landing') }}</a></li><li><a href="#how">{{ __('How it works?') }}</a></li><li><a href="#faq">{{ __('Questions') }}</a></li></ul></div>
            <div class="footer-col"><h4>{{ __('Account') }}</h4><ul><li><a href="{{ route('login') }}">{{ __('Log In') }}</a></li><li><a href="{{ route('register') }}">{{ __('Create Account') }}</a></li></ul></div>
            <div class="footer-col"><h4>{{ __('Contact Us') }}</h4><ul><li><a href="mailto:support@motorzad.com">support@motorzad.com</a></li><li><a href="tel:+966500000000">+966 500 000 000</a></li></ul></div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} موتورزاد. {{ __('All rights reserved.') }}</span>
            <span>{{ __('Made with ❤️ in Saudi Arabia') }}</span>
        </div>
    </div>
</footer>

<script>
// Navbar scroll
window.addEventListener('scroll',()=>{document.getElementById('navbar').classList.toggle('scrolled',window.scrollY>50)});
// Scroll animations
const obs=new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target)}})},{threshold:0.1});
document.querySelectorAll('.animate-on-scroll').forEach(el=>obs.observe(el));
// Theme toggle
document.getElementById('themeToggle')?.addEventListener('click',()=>{
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    if (newTheme === 'light') { document.documentElement.removeAttribute('data-theme'); }
    else { document.documentElement.setAttribute('data-theme', 'dark'); }
    localStorage.setItem('motorzad-landing-theme', newTheme);
});
// Mobile menu
document.getElementById('mobileToggle')?.addEventListener('click',()=>{const l=document.querySelector('.nav-links');l.style.display=l.style.display==='flex'?'none':'flex';l.style.flexDirection='column';l.style.position='absolute';l.style.top='100%';l.style.right='0';l.style.left='0';l.style.background='var(--bg-card-solid)';l.style.padding='1rem 2rem';l.style.borderBottom='1px solid var(--border)'});
</script>
</body>
</html>
