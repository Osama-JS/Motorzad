@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'تصفح المزادات' : 'Browse Auctions')

@section('css')
<style>
/* ===== PREMIUM AUCTIONS VIEW ===== */
.auc-header {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(22, 33, 62, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.auc-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(229, 62, 62, 0.15), transparent 50%), 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1), transparent 50%);
    pointer-events: none;
}
.auc-header-inner {
    position: relative;
    z-index: 2;
}
.auc-header h1 {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}
.auc-header p {
    opacity: 0.8;
    font-size: 1.05rem;
    max-width: 600px;
}

/* Tabs & Filters */
.filters-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.auc-tabs {
    display: flex;
    gap: 0.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    padding: 0.35rem;
    border-radius: 14px;
}
.auc-tab {
    padding: 0.65rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-muted);
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    cursor: pointer;
}
.auc-tab:hover {
    color: var(--text);
    background: var(--bg-hover);
}
.auc-tab.active {
    background: var(--brand-red);
    color: white !important;
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.25);
}

.search-filter-box {
    display: flex;
    gap: 0.75rem;
    flex: 1;
    max-width: 580px;
}
.search-input-wrapper {
    position: relative;
    flex: 1;
}
.search-input-wrapper svg {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    width: 18px;
    height: 18px;
}
html[dir="rtl"] .search-input-wrapper svg {
    left: auto;
    right: 1rem;
}
.search-input-wrapper input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text);
    font-size: 0.9rem;
    transition: all 0.3s ease;
}
html[dir="rtl"] .search-input-wrapper input {
    padding: 0.75rem 2.5rem 0.75rem 1rem;
}
.search-input-wrapper input:focus {
    border-color: var(--brand-red);
    outline: none;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}
.btn-toggle-filters {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0 1.25rem;
    color: var(--text);
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-toggle-filters:hover, .btn-toggle-filters.active {
    background: var(--bg-hover);
    border-color: var(--brand-red);
}
.btn-toggle-filters svg {
    width: 16px;
    height: 16px;
    transition: transform 0.3s ease;
}
.btn-toggle-filters.active svg {
    color: var(--brand-red);
}
.active-filter-badge {
    background: var(--brand-red);
    color: white;
    font-size: 0.75rem;
    padding: 0.15rem 0.45rem;
    border-radius: 20px;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(229, 62, 62, 0.3);
    line-height: 1;
}
.btn-search {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0 1.5rem;
    color: var(--text);
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-search:hover {
    background: var(--bg-hover);
    border-color: var(--text-muted);
}

/* Advanced Filters Panel */
.advanced-filters-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    display: none; /* Controlled by jQuery */
}
.adv-filters-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.25rem;
}
@media (max-width: 992px) {
    .adv-filters-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .adv-filters-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 480px) {
    .adv-filters-grid {
        grid-template-columns: 1fr;
    }
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.filter-group label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
html[dir="rtl"] .filter-group label {
    letter-spacing: 0;
}
.filter-group select {
    background: var(--bg-body);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0.65rem 1rem;
    color: var(--text);
    font-size: 0.875rem;
    outline: none;
    transition: all 0.3s ease;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23888888' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.2rem;
    padding-right: 2.25rem;
}
html[dir="rtl"] .filter-group select {
    background-position: left 0.75rem center;
    padding-right: 1rem;
    padding-left: 2.25rem;
}
.filter-group select:focus {
    border-color: var(--brand-red);
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}
.adv-filters-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
}
.btn-clear-adv-filters {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-weight: 700;
    padding: 0.65rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-clear-adv-filters:hover {
    background: var(--bg-hover);
    color: var(--text);
    border-color: var(--text-muted);
}
.btn-apply-filters {
    background: var(--brand-red);
    border: none;
    color: white;
    font-weight: 700;
    padding: 0.65rem 1.75rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
}
.btn-apply-filters:hover {
    background: #dd3b3b;
    box-shadow: 0 6px 16px rgba(229, 62, 62, 0.3);
    transform: translateY(-1px);
}
.btn-apply-filters:active {
    transform: translateY(0);
}

/* Grid & Cards */
.auctions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}
.auc-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}
.auc-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    border-color: rgba(229, 62, 62, 0.3);
}

.auc-image-area {
    position: relative;
    padding-top: 56.25%; /* 16:9 ratio */
    overflow: hidden;
    background: #0f172a;
}
.auc-image-area img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.auc-card:hover .auc-image-area img {
    transform: scale(1.08);
}

.badge-floating {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 10;
    padding: 0.4rem 0.85rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
html[dir="rtl"] .badge-floating {
    left: auto;
    right: 1rem;
}
.badge-floating.live {
    background: rgba(229, 62, 62, 0.9);
    color: white;
}
.badge-floating.upcoming {
    background: rgba(245, 158, 11, 0.9);
    color: white;
}
.badge-floating.ended {
    background: rgba(100, 116, 139, 0.9);
    color: white;
}

.pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 0 8px white;
    animation: dot-pulse 1.5s infinite;
}
@keyframes dot-pulse {
    0% { transform: scale(0.9); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.5; }
    100% { transform: scale(0.9); opacity: 1; }
}

.watchlist-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
}
html[dir="rtl"] .watchlist-btn {
    right: auto;
    left: 1rem;
}
.watchlist-btn:hover {
    background: rgba(229, 62, 62, 0.9);
    border-color: transparent;
    transform: scale(1.1);
}
.watchlist-btn.active {
    background: var(--brand-red);
    color: white;
    border-color: transparent;
}
.watchlist-btn.active svg {
    fill: currentColor;
}

.timer-floating {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    z-index: 10;
    padding: 0.4rem 0.75rem;
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 8px;
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
html[dir="rtl"] .timer-floating {
    right: auto;
    left: 1rem;
}

/* Card Content */
.auc-info {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.auc-title-row {
    margin-bottom: 0.75rem;
}
.auc-title-row h3 {
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1.4;
    color: var(--text);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.auc-specs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.65rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--border-light);
    margin-bottom: 1.25rem;
}
.spec-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
}
.spec-item svg {
    width: 14px;
    height: 14px;
    color: var(--text-secondary);
}

/* Bids Info and CTA */
.auc-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}
.price-block {
    display: flex;
    flex-direction: column;
}
.price-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    font-weight: 700;
}
.price-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--brand-red-light);
}
.btn-auc-action {
    background: linear-gradient(135deg, var(--brand-red), #991b1b);
    color: white;
    border: none;
    padding: 0.7rem 1.25rem;
    border-radius: 10px;
    font-weight: 800;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.15);
}
.btn-auc-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(229, 62, 62, 0.35);
    color: white;
}
.btn-auc-action.secondary {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    color: var(--text);
    box-shadow: none;
}
.btn-auc-action.secondary:hover {
    background: var(--border);
}

/* Empty State */
.empty-auctions {
    text-align: center;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 5rem 2rem;
    max-width: 600px;
    margin: 3rem auto;
}
.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(229, 62, 62, 0.08);
    color: var(--brand-red);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.empty-icon svg {
    width: 38px;
    height: 38px;
}
.empty-auctions h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}
.empty-auctions p {
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

/* ===== AUCTION BROWSE RESPONSIVE ===== */
@media (max-width: 768px) {
    .auc-header {
        padding: 1.75rem 1.25rem;
        margin-bottom: 1.25rem;
        border-radius: var(--radius-lg);
    }
    .auc-header h1 {
        font-size: 1.5rem;
    }
    .auc-header p {
        font-size: 0.85rem;
    }
    .filters-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .auc-tabs {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        flex-wrap: nowrap;
    }
    .auc-tabs::-webkit-scrollbar {
        display: none;
    }
    .auc-tab {
        padding: 0.55rem 1rem;
        font-size: 0.78rem;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .search-filter-box {
        max-width: 100%;
        flex-direction: column;
    }
    .search-input-wrapper input {
        font-size: 0.85rem;
    }
    .btn-toggle-filters,
    .btn-search {
        width: 100%;
        justify-content: center;
        padding: 0.65rem 1rem;
    }
    .advanced-filters-panel {
        padding: 1rem;
    }
    .adv-filters-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
    .btn-clear-adv-filters,
    .btn-apply-filters {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    .auctions-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .auc-info {
        padding: 1rem;
    }
    .auc-title-row h3 {
        font-size: 1rem;
    }
    .auc-specs {
        gap: 0.5rem;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .spec-item {
        font-size: 0.72rem;
    }
    .price-value {
        font-size: 1.05rem;
    }
    .btn-auc-action {
        padding: 0.6rem 1rem;
        font-size: 0.78rem;
    }
    .empty-auctions {
        padding: 3rem 1.5rem;
    }
    .empty-auctions h2 {
        font-size: 1.2rem;
    }
    .empty-icon {
        width: 64px;
        height: 64px;
    }
    .empty-icon svg {
        width: 30px;
        height: 30px;
    }
}

@media (max-width: 480px) {
    .auc-header {
        padding: 1.25rem 1rem;
    }
    .auc-header h1 {
        font-size: 1.2rem;
    }
    .auc-header p {
        font-size: 0.78rem;
    }
    .auc-tab {
        padding: 0.45rem 0.85rem;
        font-size: 0.72rem;
    }
    .auctions-grid {
        grid-template-columns: 1fr;
        gap: 0.85rem;
    }
    .auc-image-area {
        padding-top: 50%;
    }
    .auc-info {
        padding: 0.85rem;
    }
    .auc-title-row h3 {
        font-size: 0.9rem;
    }
    .auc-specs {
        grid-template-columns: 1fr 1fr;
        gap: 0.4rem;
    }
    .spec-item {
        font-size: 0.68rem;
    }
    .price-value {
        font-size: 0.95rem;
    }
    .btn-auc-action {
        padding: 0.5rem 0.85rem;
        font-size: 0.75rem;
    }
    .badge-floating {
        font-size: 0.65rem;
        padding: 0.3rem 0.65rem;
    }
    .watchlist-btn {
        width: 32px;
        height: 32px;
    }
    .timer-floating {
        font-size: 0.68rem;
        padding: 0.3rem 0.6rem;
    }
    .empty-auctions {
        padding: 2rem 1rem;
        margin: 1.5rem auto;
    }
    .empty-auctions h2 {
        font-size: 1rem;
    }
}
</style>
@endsection

@section('content')

{{-- ===== HERO BANNER ===== --}}
<div class="auc-header">
    <div class="auc-header-inner">
        <div class="hero-badge" style="margin-bottom: 1rem; border-color: rgba(255,255,255,0.2);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ app()->getLocale() === 'ar' ? 'منصة المزايدات الحية' : 'Live Auctions Portal' }}
        </div>
        <h1>{{ app()->getLocale() === 'ar' ? 'ابدأ المزايدة الآن' : 'Start Bidding Now' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'حسابك موثق بالكامل وجاهز للمشاركة في المزادات الحية والمميزة. تصفح السيارات الفارهة وقدم عروضك بأمان وسهولة.' : 'Your account is fully verified and ready to participate in live and premium auctions. Browse luxury vehicles and place your bids securely.' }}</p>
    </div>
</div>

{{-- ===== FILTER & SEARCH BAR ===== --}}
<form id="auctions-filter-form" action="{{ route('bidder.auctions.index') }}" method="GET" style="width: 100%;">
    <input type="hidden" id="filter-tab" name="tab" value="{{ $tab }}">
    
    <div class="filters-bar">
        <div class="auc-tabs">
            <a href="#" data-tab="live" class="auc-tab {{ $tab === 'live' ? 'active' : '' }}">
                {{ app()->getLocale() === 'ar' ? 'المزادات الحية' : 'Live Auctions' }}
            </a>
            <a href="#" data-tab="upcoming" class="auc-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
                {{ app()->getLocale() === 'ar' ? 'القادمة قريباً' : 'Upcoming' }}
            </a>
            <a href="#" data-tab="ended" class="auc-tab {{ $tab === 'ended' ? 'active' : '' }}">
                {{ app()->getLocale() === 'ar' ? 'المنتهية' : 'Ended' }}
            </a>
            <a href="#" data-tab="watchlist" class="auc-tab {{ $tab === 'watchlist' ? 'active' : '' }}">
                {{ app()->getLocale() === 'ar' ? 'المتابعة' : 'Watchlist' }}
            </a>
        </div>

        <div class="search-filter-box">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="filter-search" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالشركة، الموديل...' : 'Search by make, model...' }}">
            </div>
            
            <button type="button" class="btn-toggle-filters" title="{{ app()->getLocale() === 'ar' ? 'فلاتر متقدمة' : 'Advanced Filters' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <span class="btn-text">{{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}</span>
                <span class="active-filter-badge" style="display: none;">0</span>
            </button>

            <button type="submit" class="btn-search">{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}</button>
        </div>
    </div>

    <!-- Advanced Filters Panel (initially hidden, expands/slides down) -->
    <div class="advanced-filters-panel">
        <div class="adv-filters-grid">
            <!-- Make -->
            <div class="filter-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الماركة' : 'Make' }}</label>
                <select name="make" id="filter-make">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الماركات' : 'All Makes' }}</option>
                    @foreach($makes as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Location -->
            <div class="filter-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</label>
                <select name="location" id="filter-location">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل المواقع' : 'All Locations' }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year From -->
            <div class="filter-group">
                <label>{{ app()->getLocale() === 'ar' ? 'سنة الصنع (من)' : 'Year From' }}</label>
                <select name="year_from" id="filter-year-from">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'من' : 'From' }}</option>
                    @for($y = date('Y') + 1; $y >= 1990; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Year To -->
            <div class="filter-group">
                <label>{{ app()->getLocale() === 'ar' ? 'سنة الصنع (إلى)' : 'Year To' }}</label>
                <select name="year_to" id="filter-year-to">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'إلى' : 'To' }}</option>
                    @for($y = date('Y') + 1; $y >= 1990; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Condition -->
            <div class="filter-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Condition' }}</label>
                <select name="condition" id="filter-condition">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Conditions' }}</option>
                    <option value="new">{{ app()->getLocale() === 'ar' ? 'جديدة' : 'New' }}</option>
                    <option value="excellent">{{ app()->getLocale() === 'ar' ? 'ممتازة' : 'Excellent' }}</option>
                    <option value="good">{{ app()->getLocale() === 'ar' ? 'جيدة' : 'Good' }}</option>
                </select>
            </div>
        </div>

        <div class="adv-filters-footer">
            <button type="button" class="btn-clear-adv-filters">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset Filters' }}</button>
            <button type="submit" class="btn-apply-filters">{{ app()->getLocale() === 'ar' ? 'تطبيق الفلاتر' : 'Apply Filters' }}</button>
        </div>
    </div>
</form>

{{-- ===== AUCTIONS GRID ===== --}}
<div id="auctions-container">
    @include('bidder.auctions.partials.grid')
</div>

@endsection

@section('js')
<script>
function toggleWatch(btn, id) {
    btn.disabled = true;
    const currentlyWatched = btn.classList.contains('active');
    
    BidderAjax.post(`{{ url('/bidder/auctions') }}/${id}/watch`, { 
        currently_watched: currentlyWatched 
    }, {
        onSuccess: function(data) {
            btn.disabled = false;
            if (data.success) {
                btn.classList.toggle('active');
                toastr.success(currentlyWatched ? 'Removed from watchlist' : 'Added to watchlist');
            }
        },
        onError: function() {
            btn.disabled = false;
            toastr.error('Failed to update watchlist');
        }
    });
}

$(document).ready(function() {
    // Sync inputs initially on page load matching current URL query params
    syncFormInputs(window.location.href);

    // Advanced Filters Panel Toggle
    $(document).on('click', '.btn-toggle-filters', function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.advanced-filters-panel').slideToggle(300);
    });

    // Tab switching via unified form
    $(document).on('click', '.auc-tab', function(e) {
        e.preventDefault();
        const tab = $(this).data('tab');
        if (!tab) return;

        // Visual feedback
        $('.auc-tab').removeClass('active');
        $(this).addClass('active');

        // Update hidden input and submit the form
        $('#filter-tab').val(tab);
        $('#auctions-filter-form').submit();
    });

    // Pagination links click handler via AJAX
    $(document).on('click', '#auctions-container .pagination-wrapper a, #auctions-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadAuctions(url);
        }
    });

    // Unified form submission handler via AJAX
    $('#auctions-filter-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const action = form.attr('action') || window.location.pathname;
        const serialized = form.serialize();
        const url = action + (action.includes('?') ? '&' : '?') + serialized;
        loadAuctions(url);
    });

    // Reset advanced filters inside the panel
    $(document).on('click', '.btn-clear-adv-filters', function(e) {
        e.preventDefault();
        
        // Reset all select dropdowns and search inputs
        $('#filter-search').val('');
        $('#filter-make').val('');
        $('#filter-location').val('');
        $('#filter-year-from').val('');
        $('#filter-year-to').val('');
        $('#filter-condition').val('');
        
        // Submit form to fetch default state
        $('#auctions-filter-form').submit();
    });

    // Reset filters click handler from empty-state view
    $(document).on('click', '.btn-reset-filters', function(e) {
        e.preventDefault();
        
        // Reset all inputs
        $('#filter-search').val('');
        $('#filter-make').val('');
        $('#filter-location').val('');
        $('#filter-year-from').val('');
        $('#filter-year-to').val('');
        $('#filter-condition').val('');
        
        // Submit form to fetch default state
        $('#auctions-filter-form').submit();
    });

    function loadAuctions(url) {
        // Show loading state
        $('#auctions-container').css('opacity', '0.5');

        BidderAjax.get(url, {}, {
            onSuccess: function(response) {
                $('#auctions-container').css('opacity', '1');
                if (response.success && response.html) {
                    clearCountdowns();
                    $('#auctions-container').html(response.html);
                    initCountdowns();
                    
                    // Update URL browser history
                    window.history.pushState(null, null, url);

                    // Sync inputs with the new URL state
                    syncFormInputs(url);
                } else {
                    toastr.error('Failed to load auctions.');
                }
            },
            onError: function() {
                $('#auctions-container').css('opacity', '1');
                toastr.error('Failed to load auctions.');
            }
        });
    }

    function syncFormInputs(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            const tabParam = urlObj.searchParams.get('tab') || 'live';
            const searchParam = urlObj.searchParams.get('search') || '';
            const makeParam = urlObj.searchParams.get('make') || '';
            const locationParam = urlObj.searchParams.get('location') || '';
            const yearFromParam = urlObj.searchParams.get('year_from') || '';
            const yearToParam = urlObj.searchParams.get('year_to') || '';
            const conditionParam = urlObj.searchParams.get('condition') || '';

            // Update DOM fields
            $('#filter-tab').val(tabParam);
            $('#filter-search').val(searchParam);
            $('#filter-make').val(makeParam);
            $('#filter-location').val(locationParam);
            $('#filter-year-from').val(yearFromParam);
            $('#filter-year-to').val(yearToParam);
            $('#filter-condition').val(conditionParam);

            // Sync tab active class
            $('.auc-tab').removeClass('active');
            $(`.auc-tab[data-tab="${tabParam}"]`).addClass('active');

            // Count active advanced filters
            let activeFilters = 0;
            if (makeParam) activeFilters++;
            if (locationParam) activeFilters++;
            if (yearFromParam) activeFilters++;
            if (yearToParam) activeFilters++;
            if (conditionParam) activeFilters++;

            // Update filter button badge
            if (activeFilters > 0) {
                $('.active-filter-badge').text(activeFilters).show();
                $('.btn-toggle-filters').addClass('active');
            } else {
                $('.active-filter-badge').hide();
                $('.btn-toggle-filters').removeClass('active');
            }
        } catch(err) {
            console.error('syncFormInputs failed', err);
        }
    }

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function() {
        const currentUrl = window.location.href;
        loadAuctions(currentUrl);
    });

    // Countdown logic
    function clearCountdowns() {
        $('.auction-countdown').each(function() {
            const timerId = $(this).data('timer-id');
            if (timerId) clearInterval(timerId);
        });
    }

    function initCountdowns() {
        $('.auction-countdown').each(function() {
            const el = $(this);
            const targetStr = el.data('target');
            if (!targetStr) return;
            
            const targetTime = new Date(targetStr).getTime();
            if (isNaN(targetTime)) return;
            
            const isAr = '{{ app()->getLocale() }}' === 'ar';
            const status = el.data('status'); // 'live' or 'upcoming'
            
            const timerId = setInterval(function() {
                const now = new Date().getTime();
                const distance = targetTime - now;
                
                if (distance < 0) {
                    clearInterval(timerId);
                    el.text(isAr ? (status === 'live' ? 'انتهى' : 'بدأ المزاد') : (status === 'live' ? 'Ended' : 'Started'));
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let output = '';
                if (days > 0) {
                    output += days + (isAr ? ' ي ' : ' d ');
                }
                output += (hours < 10 ? '0' : '') + hours + ':';
                output += (minutes < 10 ? '0' : '') + minutes + ':';
                output += (seconds < 10 ? '0' : '') + seconds;
                
                el.text(output);
            }, 1000);
            
            el.data('timer-id', timerId);
        });
    }

    // Initial countdowns start
    initCountdowns();
});
</script>
@endsection
