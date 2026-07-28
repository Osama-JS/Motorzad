@extends('layouts.landing')

@section('title', __('Public Auctions') . ' - Motorzad')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<style>
/* ══════════════════════════════════════════════════════
   WORLD-CLASS AUCTIONS PAGE
   ══════════════════════════════════════════════════════ */

/* ─── Page Shell ─── */
.auctions-page {
    padding-top: 5rem;
    min-height: 100vh;
    background: var(--bg);
    overflow-x: hidden;
}

/* ─── Hero Banner ─── */
.auctions-hero {
    position: relative;
    padding: 2.5rem 1rem 2rem;
    overflow: hidden;
    margin-bottom: 2rem;
    border-radius: 0;
    border-left: none;
    border-right: none;
    background:
        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(229,62,62,0.08) 0%, transparent 70%),
        radial-gradient(ellipse 60% 50% at 80% 100%, rgba(245,158,11,0.05) 0%, transparent 70%),
        var(--bg-card);
    border: 1px solid var(--border);
}
.auctions-hero::before {
    content: '';
    position: absolute;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: var(--red);
    opacity: 0.04;
    top: -200px; left: 50%;
    transform: translateX(-50%);
    filter: blur(100px);
    pointer-events: none;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(229,62,62,0.08);
    border: 1px solid rgba(229,62,62,0.15);
    color: var(--red-light);
    padding: 0.4rem 1.25rem;
    border-radius: 100px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-bottom: 1.25rem;
}
.hero-badge .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--red);
    animation: heroPulse 2s ease-in-out infinite;
}
@keyframes heroPulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.4; transform:scale(0.6); } }
.hero-title {
    font-size: clamp(2rem, 4.5vw, 3.2rem);
    font-weight: 900;
    letter-spacing: -0.03em;
    line-height: 1.15;
    margin-bottom: 1rem;
    color: var(--text);
}
.hero-title .accent {
    background: linear-gradient(135deg, var(--red), var(--gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-content-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
    text-align: start;
}
.hero-text-side {
    flex: 1;
    max-width: 600px;
}
.hero-stats-side {
    flex-shrink: 0;
}
.hero-sub {
    max-width: 560px;
    font-size: 1.05rem;
    color: var(--text-sec);
    line-height: 1.7;
    margin: 0;
}
/* Floating stats bar inside hero */
.hero-stats {
    display: flex;
    justify-content: flex-end;
    gap: 2.5rem;
    flex-wrap: wrap;
}
.hero-stat {
    text-align: center;
}
.hero-stat .num {
    font-family: 'Orbitron', sans-serif;
    font-weight: 800;
    font-size: 1.6rem;
    color: var(--red-light);
}
.hero-stat:nth-child(2) .num { color: var(--gold-light); }
.hero-stat:nth-child(3) .num { color: var(--blue-light); }
.hero-stat .lbl {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.15rem;
}

/* ─── Main Layout ─── */
.auctions-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    align-items: start;
}

/* ─── Sidebar ─── */
.auctions-sidebar {
    position: sticky;
    top: 6rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
}
.sidebar-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}
.sidebar-head h3 {
    font-size: 1.1rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: -0.01em;
}
.sidebar-head h3 svg { color: var(--red-light); }
.filter-count {
    font-size: 0.72rem;
    font-weight: 700;
    background: var(--red);
    color: #fff;
    width: 22px; height: 22px;
    border-radius: 50%;
    display: none; /* shown via JS when filters active */
    align-items: center;
    justify-content: center;
}
.filter-count.visible { display: flex; }

.filter-group {
    margin-bottom: 1.25rem;
}
.filter-group label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-sec);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.filter-group label svg {
    width: 14px; height: 14px;
    color: var(--text-muted);
}
.filter-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    background: var(--bg);
    color: var(--text);
    font-family: inherit;
    font-size: 0.92rem;
    outline: none;
    transition: var(--transition);
    -webkit-appearance: none;
    appearance: none;
}
select.filter-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}
html[dir="rtl"] select.filter-control {
    background-position: left 1rem center;
    padding-right: 1rem;
    padding-left: 2.5rem;
}
.filter-control:focus {
    border-color: var(--red);
    box-shadow: 0 0 0 3px var(--red-glow);
}
.filter-range {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}
.filter-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
}
.btn-filter-apply {
    width: 100%;
    padding: 0.8rem;
    border: none;
    border-radius: var(--radius);
    background: linear-gradient(135deg, var(--red), #b91c1c);
    color: #fff;
    font-weight: 700;
    font-size: 0.92rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.btn-filter-apply:hover {
    box-shadow: 0 6px 20px rgba(229,62,62,0.3);
    transform: translateY(-1px);
}
.btn-filter-reset {
    width: 100%;
    padding: 0.7rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: transparent;
    color: var(--text-sec);
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    transition: var(--transition);
}
.btn-filter-reset:hover {
    border-color: var(--red);
    color: var(--red-light);
    background: var(--red-glow);
}

/* ─── Content Area ─── */
.auctions-content {
    min-width: 0;
}

/* Toolbar bar */
.auctions-toolbar {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
}

/* Search */
.search-wrap {
    position: relative;
    flex: 1;
    min-width: 220px;
}
.search-wrap .search-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 18px; height: 18px;
    color: var(--text-muted);
    left: 1rem;
    pointer-events: none;
    transition: color 0.3s;
}
html[dir="rtl"] .search-wrap .search-icon { left: auto; right: 1rem; }
.search-wrap input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.75rem;
    border-radius: 100px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text);
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: var(--transition);
}
html[dir="rtl"] .search-wrap input { padding: 0.8rem 2.75rem 0.8rem 1rem; }
.search-wrap input:focus {
    border-color: var(--red);
    box-shadow: 0 0 0 3px var(--red-glow);
}
.search-wrap input:focus ~ .search-icon { color: var(--red-light); }

/* Tab Pills */
.tab-pills {
    display: flex;
    gap: 0.25rem;
    background: var(--bg-card);
    padding: 0.3rem;
    border-radius: 100px;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.tab-pill {
    padding: 0.55rem 1.15rem;
    border: none;
    background: transparent;
    color: var(--text-muted);
    border-radius: 100px;
    cursor: pointer;
    font-weight: 700;
    font-size: 0.82rem;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    position: relative;
    font-family: inherit;
}
.tab-pill:hover { color: var(--text); }
.tab-pill.active {
    background: var(--red);
    color: #fff;
    box-shadow: 0 4px 14px rgba(229,62,62,0.35);
}
.tab-pill .pill-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 100px;
    background: rgba(255,255,255,0.15);
    font-size: 0.7rem;
    margin-inline-start: 0.35rem;
}
.tab-pill:not(.active) .pill-count {
    background: var(--border);
    color: var(--text-muted);
}

/* Results meta */
.results-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    font-size: 0.85rem;
    color: var(--text-muted);
}
.results-meta strong { color: var(--text); }

/* ─── Grid ─── */
.auctions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* ─── Premium Card Overrides ─── */
.auctions-grid .auction-card {
    border-radius: var(--radius-xl);
    overflow: hidden;
    border: 1px solid var(--border);
    background: var(--bg-card);
    transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
    display: flex;
    flex-direction: column;
    position: relative;
}
.auctions-grid .auction-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1px;
    background: linear-gradient(135deg, rgba(229,62,62,0) 0%, rgba(229,62,62,0) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
    transition: background 0.5s;
    z-index: 1;
}
.auctions-grid .auction-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    border-color: var(--border-l);
}
.auctions-grid .auction-card:hover::before {
    background: linear-gradient(135deg, rgba(229,62,62,0.4) 0%, rgba(245,158,11,0.2) 100%);
}

.auctions-grid .auction-img {
    aspect-ratio: 16/10;
    position: relative;
    overflow: hidden;
}
.auctions-grid .auction-img img,
.auctions-grid .auction-img > div:first-child {
    transition: transform 0.6s cubic-bezier(0.4,0,0.2,1);
}
.auctions-grid .auction-card:hover .auction-img img {
    transform: scale(1.06);
}

.auctions-grid .auction-body {
    padding: 1.25rem 1.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.auctions-grid .auction-body h3 {
    font-size: 1.02rem;
    font-weight: 700;
    margin-bottom: 0.65rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.auctions-grid .auction-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}
.auctions-grid .auction-meta span {
    font-size: 0.78rem;
    color: var(--text-sec);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.auctions-grid .auction-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
    gap: 0.75rem;
    margin-top: auto;
}
.auctions-grid .auction-price .label {
    font-size: 0.72rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
}
.auctions-grid .auction-price .price {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--gold-light);
}

/* ─── Loading Skeleton ─── */
.grid-loading {
    position: absolute;
    inset: 0;
    z-index: 10;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
}
.grid-loading.visible {
    opacity: 1;
    pointer-events: auto;
}
.skeleton-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
}
.skeleton-img {
    aspect-ratio: 16/10;
    background: linear-gradient(90deg, var(--border) 0%, rgba(255,255,255,0.05) 50%, var(--border) 100%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
}
.skeleton-body { padding: 1.25rem 1.5rem 1.5rem; }
.skeleton-line {
    height: 14px;
    border-radius: 8px;
    background: linear-gradient(90deg, var(--border) 0%, rgba(255,255,255,0.05) 50%, var(--border) 100%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    margin-bottom: 0.75rem;
}
.skeleton-line.w60 { width: 60%; }
.skeleton-line.w80 { width: 80%; }
.skeleton-line.w40 { width: 40%; margin-top: 1rem; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* ─── Empty State ─── */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px dashed var(--border-l);
}
.empty-state .empty-icon {
    width: 80px; height: 80px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    background: var(--red-glow);
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-state .empty-icon svg {
    width: 36px; height: 36px;
    color: var(--red-light);
}
.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
.empty-state p {
    color: var(--text-muted);
    max-width: 380px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ─── Mobile Responsive ─── */
@media (max-width: 1024px) {
    .auctions-layout {
        grid-template-columns: 1fr;
    }
    .auctions-sidebar {
        position: static;
        order: -1;
    }
    /* Collapsible sidebar on tablet */
    .sidebar-collapse-btn { display: flex; }
    .sidebar-body.collapsed { display: none; }
    
    .hero-content-wrapper {
        flex-direction: column;
        text-align: center;
    }
    .hero-text-side {
        max-width: 100%;
    }
    .hero-stats {
        justify-content: center;
        margin-top: 2rem !important;
    }
    .hero-sub {
        margin: 0 auto;
    }
}
@media (min-width: 1025px) {
    .sidebar-collapse-btn { display: none; }
}
@media (max-width: 640px) {
    .auctions-hero { padding: 3rem 1.25rem 2.5rem; }
    .hero-stats { gap: 1.5rem; }
    .auctions-toolbar { flex-direction: column; align-items: stretch; }
    .tab-pills { overflow-x: auto; scrollbar-width: none; }
    .tab-pills::-webkit-scrollbar { display: none; }
    .auctions-grid { grid-template-columns: 1fr; }
}

/* ─── Mobile Filter Toggle ─── */
.sidebar-collapse-btn {
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.85rem 0;
    background: none;
    border: none;
    color: var(--text);
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    font-family: inherit;
    display: none;
}
.sidebar-collapse-btn svg {
    width: 20px; height: 20px;
    transition: transform 0.3s;
}
.sidebar-collapse-btn.open svg { transform: rotate(180deg); }

/* Demo banner */
.demo-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(245,158,11,0.06);
    border: 1px solid rgba(245,158,11,0.15);
    color: var(--text-sec);
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
}
.demo-banner svg { color: var(--gold); flex-shrink: 0; }
/* ─── Join Us CTA ─── */
.join-cta-section {
    padding: 2rem 1rem 4rem;
    margin-top: 2rem;
}
.join-cta-card {
    background: linear-gradient(135deg, var(--bg-card) 0%, rgba(229,62,62,0.05) 100%);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 2.5rem 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.join-cta-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?q=80&w=2070&auto=format&fit=crop') center/cover;
    opacity: 0.05;
    z-index: 0;
    pointer-events: none;
}
[data-theme="light"] .join-cta-card::before {
    opacity: 0.15;
}
.cta-content {
    position: relative;
    z-index: 1;
    max-width: 700px;
}
.cta-content h2 {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 1rem;
}
.cta-content p {
    font-size: 1.1rem;
    color: var(--text-sec);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}
.cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-cta-primary, .btn-cta-secondary {
    padding: 0.9rem 2.5rem;
    border-radius: 100px;
    font-weight: 700;
    font-size: 1.05rem;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-cta-primary {
    background: var(--red);
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(229,62,62,0.3);
    border: 1px solid var(--red);
}
.btn-cta-primary:hover {
    background: var(--red-light);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(229,62,62,0.4);
}
.btn-cta-secondary {
    background: transparent;
    color: var(--text) !important;
    border: 1px solid var(--border-hover);
}
.btn-cta-secondary:hover {
    border-color: var(--text);
    background: var(--bg-hover);
}
@media (max-width: 768px) {
    .join-cta-card {
        padding: 2rem 1.5rem;
    }
    .cta-content h2 {
        font-size: 1.8rem;
    }
    .btn-cta-primary, .btn-cta-secondary {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<main class="auctions-page">
    <!-- ═══ Hero (Full Width) ═══ -->
    <div class="auctions-hero">
        <div class="section-container">
            <div class="hero-content-wrapper">
                <div class="hero-text-side">
                    <div class="hero-badge"><span class="dot"></span> {{ __('Explore Auctions') }}</div>
                    <h1 class="hero-title">{{ __('Find Your') }} <span class="accent">{{ __('Dream Car') }}</span></h1>
                    <p class="hero-sub">{{ __('Browse live, upcoming, and ended auctions — all in one place. Transparent bidding. Premium vehicles. Real-time updates.') }}</p>
                </div>
                <div class="hero-stats-side">
                    <div class="hero-stats">
                        <div class="hero-stat"><div class="num" data-count="{{ $totalLive ?? 12 }}">0</div><div class="lbl">{{ __('Live Now') }}</div></div>
                        <div class="hero-stat"><div class="num" data-count="{{ $totalUpcoming ?? 24 }}">0</div><div class="lbl">{{ __('Upcoming') }}</div></div>
                        <div class="hero-stat"><div class="num" data-count="{{ $totalBids ?? 1580 }}">0</div><div class="lbl">{{ __('Total Bids') }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-container">
        <!-- ═══ Main Layout ═══ -->
        <div class="auctions-layout">

            <!-- ── Sidebar Filters ── -->
            <aside class="auctions-sidebar">
                <button type="button" class="sidebar-collapse-btn open" id="sidebarToggle">
                    <span>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -3px; margin-inline-end: 0.4rem;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        {{ __('Filters') }}
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <div class="sidebar-head">
                    <h3>
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        {{ __('Filters') }}
                    </h3>
                    <span class="filter-count" id="filterCount">0</span>
                </div>

                <div class="sidebar-body" id="sidebarBody">
                    <form id="filterForm">
                        <div class="filter-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                {{ __('Make') }}
                            </label>
                            <select name="make" class="filter-control">
                                <option value="">{{ __('All Makes') }}</option>
                                @foreach($makes as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ __('Location') }}
                            </label>
                            <select name="location" class="filter-control">
                                <option value="">{{ __('All Locations') }}</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}">{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ __('Year Range') }}
                            </label>
                            <div class="filter-range">
                                <input type="number" name="year_from" class="filter-control" placeholder="{{ __('From') }}" min="1990" max="{{ date('Y')+1 }}">
                                <input type="number" name="year_to" class="filter-control" placeholder="{{ __('To') }}" min="1990" max="{{ date('Y')+1 }}">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                {{ __('Condition') }}
                            </label>
                            <select name="condition" class="filter-control">
                                <option value="">{{ __('Any Condition') }}</option>
                                <option value="new">{{ __('New') }}</option>
                                <option value="excellent">{{ __('Excellent') }}</option>
                                <option value="good">{{ __('Good') }}</option>
                                <option value="fair">{{ __('Fair') }}</option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn-filter-apply">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                {{ __('Apply Filters') }}
                            </button>
                            <button type="button" id="resetFilters" class="btn-filter-reset">{{ __('Reset All') }}</button>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- ── Content ── -->
            <div class="auctions-content">
                <!-- Toolbar -->
                <div class="auctions-toolbar">
                    <div class="search-wrap">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="searchInput" placeholder="{{ __('Search by car name, make, model...') }}">
                    </div>

                    <div class="tab-pills" id="statusTabs">
                        <button type="button" class="tab-pill active" data-tab="live">
                            {{ __('Live') }}
                            <span class="pill-count">{{ $totalLive ?? '—' }}</span>
                        </button>
                        <button type="button" class="tab-pill" data-tab="upcoming">
                            {{ __('Upcoming') }}
                            <span class="pill-count">{{ $totalUpcoming ?? '—' }}</span>
                        </button>
                        <button type="button" class="tab-pill" data-tab="ended">
                            {{ __('Ended') }}
                            <span class="pill-count">{{ $totalEnded ?? '—' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Grid Container -->
                <div id="auctionsGridContainer" style="position: relative; min-height: 400px;">
                    @include('frontend.auctions.partials.grid')

                    <!-- Skeleton Loading Overlay -->
                    <div class="grid-loading" id="gridLoading">
                        @for($s = 0; $s < 6; $s++)
                        <div class="skeleton-card">
                            <div class="skeleton-img"></div>
                            <div class="skeleton-body">
                                <div class="skeleton-line w80"></div>
                                <div class="skeleton-line w60"></div>
                                <div class="skeleton-line w40"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- ═══ Join Us CTA ═══ -->
    <div class="join-cta-section">
        <div class="section-container">
            <div class="join-cta-card">
                <div class="cta-content">
                    <h2>{{ __('Join Motorzad Today') }}</h2>
                    <p>{{ __('Whether you want to bid on premium vehicles or sell your own car to the highest bidder, Motorzad is your trusted platform.') }}</p>
                    <div class="cta-actions">
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-cta-primary">{{ __('Join as Bidder') }}</a>
                            <a href="{{ route('register') }}" class="btn-cta-secondary">{{ __('Join as Seller') }}</a>
                        @else
                            <a href="{{ url('/') }}" class="btn-cta-primary">{{ __('Join as Bidder') }}</a>
                            <a href="{{ url('/') }}" class="btn-cta-secondary">{{ __('Join as Seller') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm   = document.getElementById('filterForm');
    const searchInput  = document.getElementById('searchInput');
    const tabBtns      = document.querySelectorAll('.tab-pill');
    const gridContainer= document.getElementById('auctionsGridContainer');
    const gridLoading  = document.getElementById('gridLoading');
    const resetBtn     = document.getElementById('resetFilters');
    const filterCount  = document.getElementById('filterCount');
    const sidebarToggle= document.getElementById('sidebarToggle');
    const sidebarBody  = document.getElementById('sidebarBody');

    let currentTab = 'live';
    let searchTimeout = null;

    // ─── Counter Animation ───
    document.querySelectorAll('.hero-stat .num').forEach(el => {
        const target = parseInt(el.getAttribute('data-count')) || 0;
        const duration = 1500;
        const start = performance.now();
        function animate(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (progress < 1) requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    });

    // ─── Sidebar Toggle (mobile) ───
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebarToggle.classList.toggle('open');
            sidebarBody.classList.toggle('collapsed');
        });
    }

    // ─── Update filter count badge ───
    function updateFilterCount() {
        let count = 0;
        filterForm.querySelectorAll('select').forEach(s => { if (s.value) count++; });
        filterForm.querySelectorAll('input').forEach(i => { if (i.value) count++; });
        if (searchInput.value) count++;
        filterCount.textContent = count;
        filterCount.classList.toggle('visible', count > 0);
    }

    // ─── Fetch with skeleton loading ───
    function fetchAuctions(url = null) {
        gridLoading.classList.add('visible');
        gridContainer.querySelector('.auctions-grid, .empty-state, .demo-banner')?.style && 
            (gridContainer.querySelectorAll('.auctions-grid, .empty-state, .demo-banner').forEach(el => el.style.opacity = '0.3'));

        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.append('tab', currentTab);
        if (searchInput.value) params.append('search', searchInput.value);

        const fetchUrl = url || `{{ route('frontend.auctions.index') }}?${params.toString()}`;

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                gridContainer.querySelector('.auctions-grid')?.remove();
                gridContainer.querySelector('.empty-state')?.remove();
                gridContainer.querySelectorAll('.demo-banner').forEach(el => el.remove());
                gridLoading.insertAdjacentHTML('beforebegin', data.html);
                if(window.initCountdowns) window.initCountdowns();
            }
        })
        .catch(err => console.error('Fetch error:', err))
        .finally(() => {
            gridLoading.classList.remove('visible');
            gridContainer.querySelectorAll('.auctions-grid, .empty-state, .demo-banner').forEach(el => el.style.opacity = '1');
            updateFilterCount();
        });
    }

    // ─── Tab clicks ───
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.dataset.tab;
            fetchAuctions();
        });
    });

    // ─── Form submit ───
    filterForm.addEventListener('submit', e => { e.preventDefault(); fetchAuctions(); });

    // ─── Reset ───
    resetBtn.addEventListener('click', () => { filterForm.reset(); searchInput.value = ''; fetchAuctions(); });

    // ─── Debounced search ───
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchAuctions(), 400);
    });

    // ─── Pagination delegation ───
    gridContainer.addEventListener('click', e => {
        const link = e.target.closest('.pagination a');
        if (link) { e.preventDefault(); fetchAuctions(link.href); }
    });

    updateFilterCount();
});
</script>
@endpush
