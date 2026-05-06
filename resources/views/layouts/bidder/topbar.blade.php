        <div class="topbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Toggle Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" placeholder="{{ __('Search auctions...') }}" class="form-control">
                </div>
            </div>
            <div class="topbar-right" style="display:flex; align-items:center; gap:1rem;">
                <div style="position:relative;">
                    <button class="btn-icon-only" type="button" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span class="notif-dot"></span>
                    </button>
                </div>
                <div style="display:flex; align-items:center;">
                    @if (app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-ghost btn-sm" style="font-weight:700; border:1px solid var(--border-light); display:flex; align-items:center; gap:0.5rem;">EN</a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-ghost btn-sm" style="font-weight:700; border:1px solid var(--border-light); display:flex; align-items:center; gap:0.5rem;">عربي</a>
                    @endif
                </div>
                <div style="display:flex; align-items:center; gap:0.6rem;">
                    <span class="theme-label" id="themeLabel">الوضع</span>
                    <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
                        <div class="theme-toggle-track">
                            <div class="theme-toggle-stars"><span class="theme-toggle-star"></span><span class="theme-toggle-star"></span><span class="theme-toggle-star"></span></div>
                        </div>
                        <div class="theme-toggle-thumb">
                            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                        </div>
                    </button>
                </div>
                @yield('actions')
            </div>
        </div>
