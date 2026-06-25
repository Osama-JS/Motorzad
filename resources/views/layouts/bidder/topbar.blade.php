        <div class="topbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Toggle Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="search-box" style="position: relative;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="bidder_quick_search" placeholder="{{ __('Search auctions...') }}" class="form-control" autocomplete="off">
                    <div class="global-search-dropdown" id="bidderSearchDropdown"></div>
                </div>
                <style>
                    .global-search-dropdown {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        right: 0;
                        background: var(--bg-card);
                        border: 1px solid var(--border);
                        border-radius: 12px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                        z-index: 1000;
                        max-height: 400px;
                        overflow-y: auto;
                        display: none;
                        margin-top: 5px;
                        width: 320px;
                    }
                    html[dir="rtl"] .global-search-dropdown {
                        left: auto;
                        right: 0;
                        text-align: right;
                    }
                    .global-search-category {
                        font-size: 0.75rem;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        color: var(--brand-red-light);
                        padding: 0.75rem 1rem 0.35rem;
                        background: var(--bg-hover);
                        border-bottom: 1px solid var(--border-light);
                    }
                    .global-search-item {
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.75rem 1rem;
                        color: var(--text);
                        text-decoration: none;
                        transition: background 0.2s;
                        border-bottom: 1px solid var(--border-light);
                    }
                    .global-search-item:last-child {
                        border-bottom: none;
                    }
                    .global-search-item:hover {
                        background: var(--bg-hover);
                    }
                    .global-search-item-img {
                        width: 32px;
                        height: 32px;
                        border-radius: 6px;
                        object-fit: cover;
                        background: #0f172a;
                    }
                    .global-search-item-info {
                        display: flex;
                        flex-direction: column;
                        flex: 1;
                    }
                    .global-search-item-title {
                        font-weight: 700;
                        font-size: 0.85rem;
                        display: -webkit-box;
                        -webkit-line-clamp: 1;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                    }
                    .global-search-item-subtitle {
                        font-size: 0.75rem;
                        color: var(--text-muted);
                    }
                    /* Notification Badge */
                    .notif-dot {
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: linear-gradient(135deg, var(--brand-red, #ef4444), #ff0055);
                        color: white !important;
                        font-size: 0.68rem;
                        font-weight: 800;
                        min-width: 18px;
                        height: 18px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 2px 6px rgba(229, 62, 62, 0.4);
                        border: 2px solid var(--bg-card, #ffffff);
                        padding: 1px;
                        line-height: 1;
                        font-family: system-ui, -apple-system, sans-serif;
                    }
                    html[dir="rtl"] .notif-dot {
                        right: auto;
                        left: -5px;
                    }
                </style>
            </div>
            <div class="topbar-right" style="display:flex; align-items:center; gap:1rem;">
                <div style="position:relative;">
                    <a href="{{ route('bidder.notifications') }}" class="btn-icon-only" aria-label="Notifications" style="text-decoration:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        @php
                            $realUnreadCount = auth()->user()->unreadNotifications->count();
                            $hasNotifications = auth()->user()->notifications()->exists();
                            $unreadCount = $hasNotifications ? $realUnreadCount : 2;
                        @endphp
                        @if($unreadCount > 0)
                            <span class="notif-dot" id="headerNotifDot">{{ $unreadCount }}</span>
                        @endif
                    </a>
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
