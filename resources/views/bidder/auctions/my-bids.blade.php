@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'سجل مزايداتي' : 'My Bids History')

@section('css')
<style>
/* ===== PREMIUM MY BIDS VIEW ===== */
.bids-header {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(22, 33, 62, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.bids-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(229, 62, 62, 0.15), transparent 50%), 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1), transparent 50%);
    pointer-events: none;
}
.bids-header-inner {
    position: relative;
    z-index: 2;
}
.bids-header h1 {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}
.bids-header p {
    opacity: 0.8;
    font-size: 1.05rem;
    max-width: 600px;
}

/* Stats Widgets Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-widget {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s;
}
.stat-widget:hover {
    transform: translateY(-3px);
}
.stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon-wrap.total { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-icon-wrap.winning { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon-wrap.outbid { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-icon-wrap.won { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.stat-info {
    display: flex;
    flex-direction: column;
}
.stat-val {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--text);
}
.stat-lbl {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
}

/* Wide List Row design */
.bids-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.bid-row-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.25rem;
    display: flex;
    gap: 1.5rem;
    align-items: center;
    transition: all 0.3s;
}
.bid-row-card:hover {
    border-color: var(--text-muted);
}
.bid-img-wrap {
    width: 140px;
    height: 95px;
    border-radius: 12px;
    overflow: hidden;
    background: #0b0f19;
    flex-shrink: 0;
    position: relative;
}
.bid-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.bid-details {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.2fr;
    gap: 1.5rem;
    align-items: center;
}
.veh-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.veh-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text);
    text-decoration: none;
    transition: color 0.3s;
}
.veh-title:hover {
    color: var(--brand-red);
}
.veh-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.price-tag {
    display: flex;
    flex-direction: column;
}
.price-tag .label {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 0.25rem;
}
.price-tag .amount {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text);
}
.price-tag .amount.user-bid {
    color: var(--brand-red-light);
}

/* Bid State Badges styling */
.bid-state-badge {
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    width: fit-content;
}
.bid-state-badge.winning {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
}
.bid-state-badge.outbid {
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}
.bid-state-badge.won {
    background: rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.2);
    color: #8b5cf6;
}
.bid-state-badge.lost {
    background: rgba(100, 116, 139, 0.08);
    border: 1px solid rgba(100, 116, 139, 0.2);
    color: #64748b;
}

.pulse-dot-green {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 8px #10b981;
    animation: blink 1.5s infinite;
}

.action-col {
    display: flex;
    justify-content: flex-end;
}
.btn-action-view {
    background: var(--brand-red);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1.2rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
}
.btn-action-view:hover {
    background: var(--brand-red-light);
    color: white;
    transform: translateY(-1px);
}

@keyframes blink {
    0% { opacity: 0.4; }
    50% { opacity: 1; }
    100% { opacity: 0.4; }
}

@media(max-width: 991px) {
    .bid-row-card {
        flex-direction: column;
        align-items: stretch;
    }
    .bid-img-wrap {
        width: 100%;
        height: 180px;
    }
    .bid-details {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .action-col {
        justify-content: flex-start;
        margin-top: 0.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="bids-header">
    <div class="bids-header-inner">
        <h1>{{ app()->getLocale() === 'ar' ? 'سجل مزايداتي' : 'My Bids History' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'تابع حالة جميع مزايداتك، وتعرف على ما إذا كنت الفائز أو تم تجاوز عرضك من المزايدين الآخرين.' : 'Track the status of all your bids, find out if you are winning or if your bid has been exceeded.' }}</p>
    </div>
</div>

{{-- Dynamic Stats Widgets Row --}}
@php
    $totalCount = $auctions->count();
    $winningCount = $auctions->where('bidder_status', 'winning')->count();
    $outbidCount = $auctions->where('bidder_status', 'outbid')->count();
    $wonCount = $auctions->where('bidder_status', 'won')->count();
@endphp
<div class="stats-row">
    <div class="stat-widget">
        <div class="stat-icon-wrap total">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $totalCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'إجمالي المزايدات' : 'Total Bids' }}</span>
        </div>
    </div>
    
    <div class="stat-widget">
        <div class="stat-icon-wrap winning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $winningCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'في الصدارة' : 'Winning' }}</span>
        </div>
    </div>

    <div class="stat-widget">
        <div class="stat-icon-wrap outbid">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $outbidCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'تم تخطيك' : 'Outbid' }}</span>
        </div>
    </div>

    <div class="stat-widget">
        <div class="stat-icon-wrap won">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $wonCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions' }}</span>
        </div>
    </div>
</div>

{{-- Main List Container --}}
<div class="bids-list">
    @forelse($auctions as $auc)
        @php
            $isMock = is_array($auc);
            $id = $isMock ? $auc['id'] : $auc->id;
            $title = $isMock ? (app()->getLocale() === 'ar' ? $auc['title_ar'] : $auc['title_en']) : (app()->getLocale() === 'ar' ? $auc->title_ar : $auc->title_en);
            $make = $isMock ? $auc['make'] : ($auc->vehicle->make ?? '');
            $model = $isMock ? $auc['model'] : ($auc->vehicle->model ?? '');
            $year = $isMock ? $auc['year'] : ($auc->vehicle->year ?? '');
            
            // Image resolving
            $imageUrl = 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=200&fit=crop';
            if ($isMock) {
                $imageUrl = $auc['image'];
            } else {
                if ($auc->vehicle && $auc->vehicle->images->isNotEmpty()) {
                    $primaryImg = $auc->vehicle->images->where('is_primary', true)->first();
                    $targetImg = $primaryImg ?: $auc->vehicle->images->first();
                    $imageUrl = asset('storage/' . $targetImg->image_path);
                }
            }

            $currentPrice = $isMock ? $auc['current_price'] : $auc->current_price;
            $userMaxBid = $isMock ? $auc['user_max_bid'] : $auc->user_max_bid;
            $bidderStatus = $isMock ? $auc['bidder_status'] : $auc->bidder_status;
        @endphp
        
        <div class="bid-row-card">
            {{-- Vehicle Image --}}
            <div class="bid-img-wrap">
                <img src="{{ $imageUrl }}" alt="{{ $title }}">
            </div>

            {{-- Row Details Grid --}}
            <div class="bid-details">
                {{-- Vehicle Info --}}
                <div class="veh-info">
                    <a href="{{ route('bidder.auctions.show', $id) }}" class="veh-title">{{ $title }}</a>
                    <div class="veh-meta">
                        <span>{{ $make }} {{ $model }}</span>
                        <span>•</span>
                        <span>{{ $year }}</span>
                    </div>
                </div>

                {{-- User's Max Bid --}}
                <div class="price-tag">
                    <span class="label">{{ app()->getLocale() === 'ar' ? 'عرضك الأعلى' : 'Your Max Bid' }}</span>
                    <span class="amount user-bid">{{ number_format($userMaxBid) }} SAR</span>
                </div>

                {{-- Current Price --}}
                <div class="price-tag">
                    <span class="label">{{ app()->getLocale() === 'ar' ? 'السعر الحالي' : 'Current Price' }}</span>
                    <span class="amount">{{ number_format($currentPrice) }} SAR</span>
                </div>

                {{-- Bid Status Badge --}}
                <div>
                    @if($bidderStatus === 'winning')
                        <span class="bid-state-badge winning">
                            <span class="pulse-dot-green"></span>
                            {{ app()->getLocale() === 'ar' ? 'في الصدارة' : 'Winning' }}
                        </span>
                    @elseif($bidderStatus === 'outbid')
                        <span class="bid-state-badge outbid">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'تم تخطيك' : 'Outbid' }}
                        </span>
                    @elseif($bidderStatus === 'won')
                        <span class="bid-state-badge won">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'فزت بالمزاد' : 'Won' }}
                        </span>
                    @else
                        <span class="bid-state-badge lost">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'خسرت المزاد' : 'Lost' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action CTA --}}
            <div class="action-col">
                <a href="{{ route('bidder.auctions.show', $id) }}" class="btn-action-view">
                    {{ app()->getLocale() === 'ar' ? 'تفاصيل المزاد' : 'View Details' }}
                    @if(app()->getLocale() === 'ar')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    @endif
                </a>
            </div>
        </div>
    @empty
        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 4rem 2rem; border-radius: var(--radius-xl); text-align: center; color: var(--text-muted);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:1rem; opacity:0.5;"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <h4 style="font-weight:800; color:var(--text); margin-bottom:0.5rem;">{{ app()->getLocale() === 'ar' ? 'لا توجد مزايدات' : 'No bids found' }}</h4>
            <p style="font-size:0.95rem; max-width:400px; margin:0 auto 1.5rem;">{{ app()->getLocale() === 'ar' ? 'لم تقم بالمزايدة على أي مركبة حتى الآن. ابدأ بتصفح المزادات الحية للمشاركة.' : 'You have not bid on any vehicle yet. Start browsing live auctions to participate.' }}</p>
            <a href="{{ route('bidder.auctions.index', ['tab' => 'live']) }}" class="btn-action-view" style="margin: 0 auto; display: inline-flex;">
                {{ app()->getLocale() === 'ar' ? 'تصفح المزادات الحية' : 'Browse Live Auctions' }}
            </a>
        </div>
    @endforelse
</div>

{{-- Pagination Links (only if database pagination used) --}}
@if(!$usingMock && method_exists($auctions, 'links'))
    <div style="margin-top: 2rem;">
        {{ $auctions->links() }}
    </div>
@endif
@endsection
