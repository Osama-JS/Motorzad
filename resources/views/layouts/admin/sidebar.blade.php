    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
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
            <div class="nav-section-title">القيادة</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>لوحة التحكم</span>
            </a>

            <div class="nav-section-title">الإدارة</div>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>المستخدمين</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>الأدوار</span>
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>الصلاحيات</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ Auth::check() ? mb_substr(Auth::user()->name, 0, 1) : 'م' }}</div>
                <div class="user-info">
                    <div class="name">{{ Auth::check() ? Auth::user()->name : 'مدير النظام' }}</div>
                    <div class="role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 0.75rem;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>
