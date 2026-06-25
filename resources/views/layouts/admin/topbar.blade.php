        <div class="topbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Toggle Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="header_quick_search" placeholder="{{ __('Quick Search...') }}" class="form-control" autocomplete="off">
                    <div class="global-search-dropdown" id="globalSearchDropdown"></div>
                </div>
                
                <style>
                    .search-box {
                        position: relative;
                    }
                    .global-search-dropdown {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        right: 0;
                        background: var(--bg-card, #ffffff);
                        border: 1px solid var(--border, #e2e8f0);
                        border-radius: 12px;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                        z-index: 1050;
                        max-height: 400px;
                        overflow-y: auto;
                        margin-top: 5px;
                        display: none;
                        text-align: start;
                    }
                    .global-search-category {
                        font-size: 0.75rem;
                        font-weight: 700;
                        text-transform: uppercase;
                        color: var(--text-muted, #718096);
                        padding: 10px 15px 5px 15px;
                        border-bottom: 1px solid var(--border-light, #edf2f7);
                        background: rgba(0,0,0,0.02);
                    }
                    [data-theme="dark"] .global-search-category {
                        background: rgba(255,255,255,0.02);
                    }
                    .global-search-item {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        padding: 10px 15px;
                        color: var(--text-color, #2d3748);
                        text-decoration: none;
                        transition: background 0.15s ease;
                    }
                    .global-search-item:hover {
                        background: var(--bg-input, #f7fafc);
                        color: var(--primary, #6366f1);
                    }
                    [data-theme="dark"] .global-search-item:hover {
                        background: rgba(255,255,255,0.05);
                    }
                    .global-search-item i {
                        font-size: 1.1rem;
                        color: var(--primary, #6366f1);
                        width: 20px;
                        text-align: center;
                    }
                    .global-search-item-info {
                        display: flex;
                        flex-direction: column;
                    }
                    .global-search-item-title {
                        font-size: 0.85rem;
                        font-weight: 600;
                    }
                    .global-search-item-subtitle {
                        font-size: 0.75rem;
                        color: var(--text-muted, #718096);
                    }
                </style>
            </div>
            <div class="topbar-right" style="display:flex; align-items:center; gap:1rem;">
                {{-- Language Toggle --}}
                <div style="display:flex; align-items:center;">
                    @if (app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-ghost btn-sm" style="font-weight:700; border:1px solid var(--border-light); display:flex; align-items:center; gap:0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            English
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-ghost btn-sm" style="font-weight:700; border:1px solid var(--border-light); font-family:'Tajawal',sans-serif; display:flex; align-items:center; gap:0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            عربي
                        </a>
                    @endif
                </div>
                
                {{-- Theme Toggle --}}
                <div style="display:flex; align-items:center; gap:0.6rem;">
                    <span class="theme-label" id="themeLabel">{{ __('Dark Mode') }}</span>
                    <button class="theme-toggle" id="themeToggle" type="button" aria-label="تبديل الوضع الليلي/النهاري" title="تبديل المظهر">
                        <div class="theme-toggle-track">
                            {{-- Stars (visible in dark) --}}
                            <div class="theme-toggle-stars">
                                <span class="theme-toggle-star"></span>
                                <span class="theme-toggle-star"></span>
                                <span class="theme-toggle-star"></span>
                            </div>
                        </div>
                        {{-- Thumb with icon --}}
                        <div class="theme-toggle-thumb">
                            {{-- Moon icon (shown in dark) --}}
                            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                            {{-- Sun icon (shown in light) --}}
                            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"/>
                                <line x1="12" y1="1" x2="12" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="23"/>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                <line x1="1" y1="12" x2="3" y2="12"/>
                                <line x1="21" y1="12" x2="23" y2="12"/>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                            </svg>
                        </div>
                    </button>
                </div>
                
                {{-- User Dropdown --}}
                @auth
                <div class="dropdown ms-2">
                    <button class="btn btn-ghost dropdown-toggle d-flex align-items-center gap-2 border" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 20px; padding: 4px 12px 4px 4px; background: var(--bg-card); color: var(--text-color);">
                        <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="User" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                        <span class="fw-bold fs-6">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 200px; background: var(--bg-card); border-color: var(--border) !important;">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-decoration-none" href="#" style="color: var(--text-color);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ms-1">{{ __('View Profile') }}</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" style="border-color: var(--border);"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span class="ms-1">{{ __('Logout') }}</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
                
                @yield('actions')
            </div>
        </div>
