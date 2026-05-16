    <aside class="sidebar bidder-sidebar" id="sidebar">
        <button class="sidebar-close-btn" id="sidebarCloseBtn" type="button" aria-label="Close Menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="sidebar-header">
            <a href="{{ route('bidder.dashboard') }}" class="sidebar-logo">
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
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">{{ __('Main') }}</div>
            <a href="{{ route('bidder.dashboard') }}" class="nav-item {{ request()->routeIs('bidder.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <div class="nav-section-title">{{ __('Auctions') }}</div>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.auctions.active') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ __('Live Auctions') }}</span>
                <span class="nav-badge pulse">{{ __('Live') }}</span>
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.auctions.browse') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>{{ __('Browse Auctions') }}</span>
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.bids.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                <span>{{ __('My Bids') }}</span>
            </a>

            <div class="nav-section-title">{{ __('Activity') }}</div>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.won') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 9 7 12 7s5-3 7.5-3a2.5 2.5 0 0 1 0 5H18"/><path d="M18 9v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9"/><path d="M12 7v14"/></svg>
                <span>{{ __('Won Auctions') }}</span>
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.watchlist') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                <span>{{ __('Watchlist') }}</span>
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('bidder.notifications') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span>{{ __('Notifications') }}</span>
                <span class="nav-count">3</span>
            </a>

            <div class="nav-section-title">{{ __('Account') }}</div>
            <a href="{{ route('bidder.wallet.index') }}" class="nav-item {{ request()->routeIs('bidder.wallet.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span>{{ __('My Wallet') }}</span>
            </a>
            <a href="{{ route('kyc.index') }}" class="nav-item {{ request()->routeIs('kyc.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>{{ __('Identity Verification') }}</span>
                @if(auth()->user()->kyc_level == 0)
                    <span class="nav-badge" style="background:rgba(245,158,11,.15); color:#f59e0b;">{{ __('Required') }}</span>
                @endif
            </a>
            <a href="{{ route('bidder.bank-details.index') }}" class="nav-item {{ request()->routeIs('bidder.bank-details.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/><line x1="5" y1="21" x2="5" y2="10"/><line x1="19" y1="21" x2="19" y2="10"/><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/></svg>
                <span>{{ __('البيانات البنكية') }}</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>{{ __('My Profile') }}</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ Auth::check() ? mb_substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1) : 'U' }}</div>
                <div class="user-info">
                    <div class="name">{{ Auth::check() ? Auth::user()->full_name : 'Bidder' }}</div>
                    <div class="role">{{ __('Bidder') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 0.75rem;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </aside>
