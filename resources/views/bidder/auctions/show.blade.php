@extends('layouts.bidder')

@section('title', $auction->title)

@section('css')
<style>
/* ===== PREMIUM SINGLE AUCTION VIEW ===== */
.auc-detail-layout {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.detail-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    padding: 1.75rem;
}

/* Image Showcase */
.image-showcase {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    background: #0b0f19;
}
.main-img-wrap {
    position: relative;
    padding-top: 56.25%; /* 16:9 */
}
.main-img-wrap img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-thumbs {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}
.thumb-item {
    width: 80px;
    height: 55px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
    background: #0b0f19;
}
.thumb-item:hover {
    opacity: 0.9;
}
.thumb-item.active {
    border-color: var(--brand-red);
}
.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Tabs */
.detail-tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
    gap: 1.5rem;
}
.detail-tab-btn {
    padding: 0.75rem 0.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-muted);
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
}
.detail-tab-btn:hover {
    color: var(--text);
}
.detail-tab-btn.active {
    color: var(--brand-red-light);
    border-bottom-color: var(--brand-red);
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

/* Specs Grid */
.specs-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}
.spec-box {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.spec-box-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(229, 62, 62, 0.08);
    color: var(--brand-red);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.spec-box-icon svg {
    width: 20px;
    height: 20px;
}
.spec-box-info {
    display: flex;
    flex-direction: column;
}
.spec-box-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
}
.spec-box-value {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text);
}

/* Bidding Sidebar Card */
.bid-panel {
    background: linear-gradient(180deg, var(--bg-card) 0%, rgba(229, 62, 62, 0.02) 100%);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.status-badge-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.timer-panel {
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price-summary {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.price-summary .value {
    font-size: 1.75rem;
    font-weight: 900;
    color: var(--brand-red-light);
}

/* Bidding Form */
.bid-form-group {
    margin-bottom: 1.25rem;
}
.bid-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.bid-input-wrap input {
    width: 100%;
    background: var(--bg-input);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1rem 1rem 4.5rem;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text);
    text-align: center;
    transition: all 0.3s;
}
html[dir="rtl"] .bid-input-wrap input {
    padding: 1rem 4.5rem 1rem 1rem;
}
.bid-input-wrap input:focus {
    outline: none;
    border-color: var(--brand-red);
    box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.15);
}
.currency-suffix {
    position: absolute;
    left: 1.25rem;
    font-weight: 800;
    font-size: 1rem;
    color: var(--text-secondary);
}
html[dir="rtl"] .currency-suffix {
    left: auto;
    right: 1.25rem;
}

.quick-bids {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
.quick-bid-btn {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--text);
    cursor: pointer;
    transition: all 0.3s;
}
.quick-bid-btn:hover {
    background: rgba(229, 62, 62, 0.08);
    border-color: var(--brand-red);
    color: var(--brand-red-light);
}

.btn-submit-bid {
    width: 100%;
    background: linear-gradient(135deg, var(--brand-red), #991b1b);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 5px 20px rgba(229, 62, 62, 0.25);
    transition: all 0.3s;
}
.btn-submit-bid:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
}

/* Bids feed */
.bids-feed-box {
    margin-top: 2rem;
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
}
.feed-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.feed-header h4 {
    font-weight: 800;
    margin: 0;
}
.feed-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 250px;
    overflow-y: auto;
}
.feed-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0.85rem;
    background: var(--bg-hover);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    font-size: 0.8rem;
    transition: all 0.3s;
}

@media(max-width: 992px) {
    .auc-detail-layout {
        grid-template-columns: 1fr;
    }
    .bid-panel {
        position: static;
    }
}
</style>
@endsection

@section('content')

@php
    $vehicle = $auction->vehicle;
    $primaryImg = $vehicle->primary_image_url ?? 'https://images.unsplash.com/photo-1625231334401-6162a5e0a0d9?w=600&h=400&fit=crop';
    $timeLeftSeconds = $auction->time_remaining;
@endphp

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('bidder.auctions.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        {{ app()->getLocale() === 'ar' ? 'العودة للمزادات' : 'Back to Auctions' }}
    </a>
</div>

<div class="auc-detail-layout">
    {{-- Left Side: Images & Info --}}
    <div>
        <div class="detail-card">
            {{-- Image slider --}}
            <div class="image-showcase">
                <div class="main-img-wrap">
                    <img id="mainShowcaseImg" src="{{ $primaryImg }}" alt="{{ $auction->title }}">
                </div>
            </div>

            @if($vehicle->images->count() > 0)
                <div class="gallery-thumbs">
                    @foreach($vehicle->images as $index => $img)
                        <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="switchImage(this, '{{ asset('storage/' . $img->image_path) }}')">
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Gallery view">
                        </div>
                    @endforeach
                </div>
            @endif

            <h1 style="font-size: 1.8rem; font-weight: 900; color: var(--text); margin-bottom: 1.5rem;">
                {{ $auction->title }}
            </h1>

            {{-- Tabs --}}
            <div class="detail-tabs">
                <button class="detail-tab-btn active" onclick="switchTab(this, 'specsTab')">
                    {{ app()->getLocale() === 'ar' ? 'المواصفات التقنية' : 'Specifications' }}
                </button>
                <button class="detail-tab-btn" onclick="switchTab(this, 'descTab')">
                    {{ app()->getLocale() === 'ar' ? 'تقرير الفحص والوصف' : 'Description & Issues' }}
                </button>
            </div>

            {{-- Specs Tab Content --}}
            <div id="specsTab" class="tab-content active">
                <div class="specs-detail-grid">
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'سنة الصنع' : 'Year' }}</span>
                            <span class="spec-box-value">{{ $vehicle->year }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Condition' }}</span>
                            <span class="spec-box-value">{{ ucfirst($vehicle->condition) }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'ناقل الحركة' : 'Transmission' }}</span>
                            <span class="spec-box-value">{{ ucfirst($vehicle->transmission) }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'عداد المسافة' : 'Mileage' }}</span>
                            <span class="spec-box-value">{{ number_format($vehicle->mileage) }} KM</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'سعة المحرك' : 'Engine' }}</span>
                            <span class="spec-box-value">{{ $vehicle->engine_capacity }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</span>
                            <span class="spec-box-value">{{ $auction->location }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Tab Content --}}
            <div id="descTab" class="tab-content">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.75rem;">{{ app()->getLocale() === 'ar' ? 'وصف المركبة' : 'Vehicle Description' }}</h3>
                <div style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem; margin-bottom: 1.5rem;">
                    {!! app()->getLocale() === 'ar' ? $auction->description_ar : $auction->description_en !!}
                </div>
                @if($vehicle->issues)
                    <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.15); padding: 1rem; border-radius: 10px;">
                        <strong style="color: #f59e0b; display: block; margin-bottom: 0.25rem;">⚠️ {{ app()->getLocale() === 'ar' ? 'العيوب أو الملاحظات' : 'Issues & Wear' }}</strong>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $vehicle->issues }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Side: Bidding Module --}}
    <div>
        <div class="bid-panel">
            <div class="status-badge-panel">
                @if($auction->is_live)
                    <span class="w-badge status approved" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span class="pulse-dot" style="background:#10b981; box-shadow:0 0 8px #10b981;"></span>
                        {{ app()->getLocale() === 'ar' ? 'مزايدة حية' : 'Live Auction' }}
                    </span>
                    
                    <div class="timer-panel">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span id="countdownTimer">00:00:00</span>
                    </div>
                @else
                    <span class="w-badge status rejected" style="padding: 0.5rem 1rem;">
                        {{ ucfirst($auction->status) }}
                    </span>
                @endif
            </div>

            <div class="price-summary">
                <div class="price-block">
                    <span class="price-label">{{ app()->getLocale() === 'ar' ? 'المزايدة الحالية' : 'Current Price' }}</span>
                    <span class="value" id="currentPriceVal">{{ number_format($auction->current_price) }}</span>
                </div>
                <span style="font-weight: 800; color: var(--text-secondary);">SAR</span>
            </div>

            @if($auction->is_live)
                <div class="bid-form-group">
                    <div class="bid-input-wrap">
                        <input type="number" id="bidAmountInput" value="{{ $auction->current_price + $auction->min_bid_increment }}" min="{{ $auction->current_price + $auction->min_bid_increment }}" step="{{ $auction->min_bid_increment }}">
                        <span class="currency-suffix">SAR</span>
                    </div>
                    <small style="display: block; margin-top: 0.5rem; font-weight: 700; color: var(--text-muted); text-align: center;">
                        {{ app()->getLocale() === 'ar' ? 'الزيادة الدنيا' : 'Minimum increment' }}: +{{ number_format($auction->min_bid_increment) }} SAR
                    </small>
                </div>

                <div class="quick-bids">
                    <button class="quick-bid-btn" onclick="applyQuickBid(1000)">+1,000</button>
                    <button class="quick-bid-btn" onclick="applyQuickBid(5000)">+5,000</button>
                    <button class="quick-bid-btn" onclick="applyQuickBid(10000)">+10,000</button>
                </div>

                <button class="btn-submit-bid" onclick="placeBidNow()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                    {{ app()->getLocale() === 'ar' ? 'تقديم عرض المزايدة' : 'Place Your Bid' }}
                </button>
            @endif

            @if($auction->deposit_amount > 0)
                <div style="margin-top: 1.5rem; background: var(--bg-hover); border: 1px solid var(--border); padding: 1rem; border-radius: 12px; display: flex; gap: 0.75rem; align-items: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #10b981; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;">
                        <strong>{{ app()->getLocale() === 'ar' ? 'ضمان المزاد مطلوب' : 'Auction Deposit Required' }}</strong><br>
                        {{ app()->getLocale() === 'ar' ? 'مبلغ التأمين:' : 'Required Deposit:' }} {{ number_format($auction->deposit_amount) }} SAR
                    </div>
                </div>
            @endif

            {{-- Real Bid Feed --}}
            <div class="bids-feed-box">
                <div class="feed-header">
                    <h4>{{ app()->getLocale() === 'ar' ? 'سجل المزايدات' : 'Bids Log' }}</h4>
                    <span class="w-badge" style="background: rgba(229,62,62,0.1); color: var(--brand-red-light); font-weight: 800;" id="bidsCountBadge">
                        {{ $auction->bids_count }} {{ app()->getLocale() === 'ar' ? 'مزايدة' : 'Bids' }}
                    </span>
                </div>
                
                <div class="feed-list" id="bidsFeedList">
                    @forelse($auction->bids as $bid)
                        <div class="feed-item">
                            <span style="font-weight: 800; color: {{ $bid->user_id === $user->id ? 'var(--brand-red-light)' : 'inherit' }}">{{ number_format($bid->amount) }} SAR</span>
                            <span style="opacity: 0.75;">{{ $bid->user_id === $user->id ? (app()->getLocale() === 'ar' ? 'أنت (مزايد)' : 'You (Bidder)') : (app()->getLocale() === 'ar' ? 'مزايد #' : 'Bidder #') . $bid->user_id }}</span>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 1.5rem 0;" id="noBidsMsg">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد عروض بعد.' : 'No bids yet.' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
// Image gallery switcher
function switchImage(thumb, src) {
    document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
    thumb.classList.add('active');
    document.getElementById('mainShowcaseImg').src = src;
}

// Tabs switcher
function switchTab(btn, tabId) {
    document.querySelectorAll('.detail-tab-btn').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// Apply quick bid button addition
function applyQuickBid(addAmt) {
    const currentPrice = parseInt(document.getElementById('currentPriceVal').textContent.replace(/,/g, ''));
    const input = document.getElementById('bidAmountInput');
    input.value = currentPrice + addAmt;
}

@if($auction->is_live && $timeLeftSeconds > 0)
// Countdown timer script
let timeInSec = {{ $timeLeftSeconds }};
const timerEl = document.getElementById('countdownTimer');
const timerInterval = setInterval(() => {
    if (timeInSec <= 0) {
        clearInterval(timerInterval);
        timerEl.textContent = "Closed";
        location.reload();
        return;
    }
    timeInSec--;
    const h = Math.floor(timeInSec / 3600);
    const m = Math.floor((timeInSec % 3600) / 60);
    const s = timeInSec % 60;
    
    const formatted = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    timerEl.textContent = formatted;
}, 1000);
@endif

// Place bid handler via AJAX
function placeBidNow() {
    const btn = document.querySelector('.btn-submit-bid');
    const input = document.getElementById('bidAmountInput');
    const bidAmount = parseInt(input.value);

    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg>';

    fetch(`{{ route('bidder.auctions.bid', $auction->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ amount: bidAmount })
    })
    .then(async res => {
        const data = await res.json();
        btn.disabled = false;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg> {{ app()->getLocale() === "ar" ? "تقديم عرض المزايدة" : "Place Your Bid" }}';

        if (res.ok && data.success) {
            // Update current price
            document.getElementById('currentPriceVal').textContent = data.new_price.toLocaleString();
            
            // Add to feed list
            const feedList = document.getElementById('bidsFeedList');
            const noBidsMsg = document.getElementById('noBidsMsg');
            if (noBidsMsg) noBidsMsg.remove();
            
            const newItem = document.createElement('div');
            newItem.className = 'feed-item';
            newItem.style.animation = 'highlight-green 2s ease-out';
            newItem.innerHTML = `<span style="font-weight: 800; color: var(--brand-red-light);">${data.new_price.toLocaleString()} SAR</span><span>{{ app()->getLocale() === 'ar' ? 'أنت (مزايد)' : 'You (Bidder)' }}</span>`;
            feedList.insertBefore(newItem, feedList.firstChild);

            // Update counters
            document.getElementById('bidsCountBadge').textContent = `${data.bids_count} {{ app()->getLocale() === 'ar' ? 'مزايدات' : 'Bids' }}`;

            // Set next bid recommendation
            const minIncrement = {{ $auction->min_bid_increment }};
            input.value = data.new_price + minIncrement;
            input.min = data.new_price + minIncrement;

            toastr.success(data.message);
        } else {
            toastr.error(data.message || 'Failed to submit bid');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg> {{ app()->getLocale() === "ar" ? "تقديم عرض المزايدة" : "Place Your Bid" }}';
        toastr.error('Connection error.');
    });
}
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
