        <div class="topbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="بحث سريع..." class="form-control">
            </div>
            <div class="topbar-right" style="display:flex; align-items:center; gap:1rem;">
                {{-- Theme Toggle --}}
                <div style="display:flex; align-items:center; gap:0.6rem;">
                    <span class="theme-label" id="themeLabel">الوضع الليلي</span>
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
                @yield('actions')
            </div>
        </div>
