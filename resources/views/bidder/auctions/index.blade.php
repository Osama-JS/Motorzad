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
    margin-bottom: 2rem;
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
    max-width: 450px;
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
.btn-search {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0 1.25rem;
    color: var(--text);
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-search:hover {
    background: var(--bg-hover);
    border-color: var(--text-muted);
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
<div class="filters-bar">
    <div class="auc-tabs">
        <a href="{{ route('bidder.auctions.index', ['tab' => 'live', 'search' => $search]) }}" class="auc-tab {{ $tab === 'live' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'المزادات الحية' : 'Live Auctions' }}
        </a>
        <a href="{{ route('bidder.auctions.index', ['tab' => 'upcoming', 'search' => $search]) }}" class="auc-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'القادمة قريباً' : 'Upcoming' }}
        </a>
        <a href="{{ route('bidder.auctions.index', ['tab' => 'ended', 'search' => $search]) }}" class="auc-tab {{ $tab === 'ended' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'المنتهية' : 'Ended' }}
        </a>
        <a href="{{ route('bidder.auctions.index', ['tab' => 'watchlist', 'search' => $search]) }}" class="auc-tab {{ $tab === 'watchlist' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'المتابعة' : 'Watchlist' }}
        </a>
    </div>

    <form action="{{ route('bidder.auctions.index') }}" method="GET" class="search-filter-box">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="search-input-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالشركة، الموديل...' : 'Search by make, model...' }}">
        </div>
        <button type="submit" class="btn-search">{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}</button>
    </form>
</div>

{{-- ===== AUCTIONS GRID ===== --}}
@if($auctions && $auctions->count() > 0)
    <div class="auctions-grid">
        @foreach($auctions as $auc)
            @php
                // Standardizing properties between DB models and Mock array
                $aucId = $usingMock ? $auc['id'] : $auc->id;
                $aucTitle = $usingMock ? (app()->getLocale() === 'ar' ? $auc['title_ar'] : $auc['title_en']) : $auc->title;
                $aucMake = $usingMock ? $auc['make'] : $auc->vehicle->make;
                $aucModel = $usingMock ? $auc['model'] : $auc->vehicle->model;
                $aucYear = $usingMock ? $auc['year'] : $auc->vehicle->year;
                $aucTransmission = $usingMock ? $auc['transmission'] : $auc->vehicle->transmission;
                $aucFuel = $usingMock ? $auc['fuel_type'] : $auc->vehicle->fuel_type;
                $aucEngine = $usingMock ? $auc['engine_capacity'] : $auc->vehicle->engine_capacity;
                $aucLocation = $usingMock ? $auc['location'] : $auc->location;
                $aucPrice = $usingMock ? $auc['current_price'] : $auc->current_price;
                $aucImage = $usingMock ? $auc['image'] : ($auc->vehicle->primary_image_url ?? 'https://images.unsplash.com/photo-1625231334401-6162a5e0a0d9?w=600&h=400&fit=crop');
                $aucStatus = $usingMock ? $auc['status'] : $auc->status;
                $aucBidsCount = $usingMock ? $auc['bids_count'] : $auc->bids_count;
                
                // Format Status
                $statusClass = 'ended';
                $statusLabel = app()->getLocale() === 'ar' ? 'منتهي' : 'Ended';
                if ($aucStatus === 'live' || $aucStatus === 'sold') {
                    $statusClass = $aucStatus === 'live' ? 'live' : 'ended';
                    $statusLabel = $aucStatus === 'live' ? (app()->getLocale() === 'ar' ? 'مباشر' : 'Live') : (app()->getLocale() === 'ar' ? 'منتهي' : 'Ended');
                } elseif ($aucStatus === 'scheduled' || $aucStatus === 'upcoming') {
                    $statusClass = 'upcoming';
                    $statusLabel = app()->getLocale() === 'ar' ? 'قادم' : 'Upcoming';
                }

                // Time remaining label
                $timeLeft = '';
                if ($usingMock) {
                    $timeLeft = $aucStatus === 'live' ? '03:14:02' : ($aucStatus === 'upcoming' ? (app()->getLocale() === 'ar' ? 'خلال 24 ساعة' : 'In 24 hours') : (app()->getLocale() === 'ar' ? 'مغلق' : 'Closed'));
                } else {
                    $timeLeft = $auc->is_live ? gmdate("H:i:s", $auc->time_remaining) : (app()->getLocale() === 'ar' ? 'مغلق' : 'Closed');
                }
            @endphp
            
            <div class="auc-card">
                <div class="auc-image-area">
                    <img src="{{ $aucImage }}" alt="{{ $aucTitle }}">
                    
                    <span class="badge-floating {{ $statusClass }}">
                        @if($statusClass === 'live')
                            <span class="pulse-dot"></span>
                        @endif
                        {{ $statusLabel }}
                    </span>

                    <button class="watchlist-btn {{ (isset($isWatched) && $isWatched) ? 'active' : '' }}" onclick="toggleWatch(this, {{ $aucId }})" aria-label="Add to watchlist">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    </button>

                    @if($statusClass === 'live' || $statusClass === 'upcoming')
                        <div class="timer-floating">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>{{ $timeLeft }}</span>
                        </div>
                    @endif
                </div>

                <div class="auc-info">
                    <div class="auc-title-row">
                        <h3>{{ $aucTitle }}</h3>
                    </div>

                    <div class="auc-specs">
                        <div class="spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>{{ $aucYear }}</span>
                        </div>
                        <div class="spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <span>{{ ucfirst($aucTransmission) }}</span>
                        </div>
                        <div class="spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <span>{{ $aucEngine }}</span>
                        </div>
                        <div class="spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $aucLocation }}</span>
                        </div>
                    </div>

                    <div class="auc-footer">
                        <div class="price-block">
                            <span class="price-label">
                                @if($statusClass === 'live')
                                    {{ app()->getLocale() === 'ar' ? 'المزايدة الحالية' : 'Current Price' }}
                                @else
                                    {{ app()->getLocale() === 'ar' ? 'سعر البدء' : 'Starting Price' }}
                                @endif
                            </span>
                            <span class="price-value">{{ number_format($aucPrice) }} {{ app()->getLocale() === 'ar' ? 'ر.س' : 'SAR' }}</span>
                        </div>

                        <a href="{{ route('bidder.auctions.show', $aucId) }}" class="btn-auc-action {{ $statusClass === 'live' ? '' : 'secondary' }}">
                            @if($statusClass === 'live')
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'زايد الآن' : 'Bid Now' }}
                            @else
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(!$usingMock)
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $auctions->links() }}
        </div>
    @endif

@else
    <div class="empty-auctions">
        <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h2>{{ app()->getLocale() === 'ar' ? 'لا توجد مزادات حالياً' : 'No Auctions Found' }}</h2>
        <p>{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على مزادات في هذا القسم، يرجى التحقق لاحقاً أو تغيير الفلتر.' : 'No auctions found matching this category. Please check again later or try a different filter.' }}</p>
        <a href="{{ route('bidder.auctions.index') }}" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset Filters' }}</a>
    </div>
@endif

@endsection

@section('js')
<script>
function toggleWatch(btn, id) {
    btn.disabled = true;
    const currentlyWatched = btn.classList.contains('active');
    
    fetch(`{{ url('/bidder/auctions') }}/${id}/watch`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ currently_watched: currentlyWatched })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            btn.classList.toggle('active');
            toastr.success(currentlyWatched ? 'Removed from watchlist' : 'Added to watchlist');
        }
    })
    .catch(err => {
        btn.disabled = false;
        toastr.error('Failed to update watchlist');
    });
}
</script>
@endsection
