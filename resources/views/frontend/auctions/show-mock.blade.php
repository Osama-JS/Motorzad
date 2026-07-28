@extends('layouts.landing')

@section('title', (app()->getLocale() == 'ar' ? $auctionData['title_ar'] : $auctionData['title_en']) . ' - Motorzad')

@push('styles')
<style>
.auction-show-page { padding: 8rem 0 4rem; background: var(--bg); min-height: 80vh; }
.auc-detail-layout { display: grid; grid-template-columns: 1.6fr 1fr; gap: 2rem; }
.detail-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 1rem; overflow: hidden; padding: 1.75rem; }

/* Image Showcase */
.image-showcase { position: relative; border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem; background: #0b0f19; }
.main-img-wrap { position: relative; padding-top: 56.25%; }
.main-img-wrap img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.gallery-thumbs { display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
.thumb-item { width: 80px; height: 55px; flex-shrink: 0; border-radius: 8px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: all 0.3s; background: #0b0f19; }
.thumb-item:hover { opacity: 0.9; }
.thumb-item.active { border-color: var(--primary); }
.thumb-item img { width: 100%; height: 100%; object-fit: cover; }

/* Specs Grid */
.specs-detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
.spec-box { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; }
.spec-box-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(229, 62, 62, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; }
.spec-box-icon svg { width: 20px; height: 20px; }
.spec-box-info h4 { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; }
.spec-box-info p { font-size: 0.95rem; font-weight: 700; color: var(--text); }

/* Bidding Section */
.bidding-panel h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; line-height: 1.3; }
.auc-badges { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.badge { padding: 0.25rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem; }
.badge-live { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
.badge-ended { background: rgba(107, 114, 128, 0.1); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.2); }

.price-box { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; text-align: center; }
.price-box .label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
.price-box .amount { font-size: 2.5rem; font-weight: 800; color: var(--primary); font-family: monospace; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.price-box .amount span { font-size: 1rem; color: var(--text-muted); font-weight: 600; }

.auth-prompt { background: rgba(41, 128, 185, 0.1); border: 1px solid rgba(41, 128, 185, 0.3); border-radius: 12px; padding: 1.5rem; text-align: center; }
.auth-prompt h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; }
.auth-prompt p { color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem; }
.auth-prompt .actions { display: flex; gap: 1rem; justify-content: center; }

@media (max-width: 992px) {
    .auc-detail-layout { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<main class="auction-show-page">
    <div class="section-container">
        
        <div class="alert alert-info mb-4" style="background: rgba(41, 128, 185, 0.1); border: 1px solid rgba(41, 128, 185, 0.3); color: var(--text); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#2980b9" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            {{ __('This is a demo representation. Content is mocked.') }}
        </div>

        <div class="auc-detail-layout">
            
            <!-- Left: Gallery & Specs -->
            <div class="detail-left">
                <div class="detail-card">
                    <div class="image-showcase">
                        <div class="main-img-wrap">
                            <img id="mainImage" src="{{ $auctionData['image'] }}" alt="{{ app()->getLocale() == 'ar' ? $auctionData['title_ar'] : $auctionData['title_en'] }}">
                        </div>
                    </div>
                    
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">{{ __('Vehicle Specifications') }}</h3>
                    <div class="specs-detail-grid">
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><div class="spec-box-info"><h4>{{ __('Make') }}</h4><p>{{ $auctionData['make'] }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div class="spec-box-info"><h4>{{ __('Model') }}</h4><p>{{ $auctionData['model'] }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="spec-box-info"><h4>{{ __('Year') }}</h4><p>{{ $auctionData['year'] }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="spec-box-info"><h4>{{ __('Mileage') }}</h4><p>{{ number_format($auctionData['mileage'] ?? 0) }} {{ __('KM') }}</p></div></div>
                    </div>

                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; margin-top: 2rem;">{{ __('Description') }}</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">
                        {{ app()->getLocale() == 'ar' ? $auctionData['description_ar'] : $auctionData['description_en'] }}
                    </p>
                </div>
            </div>

            <!-- Right: Bidding -->
            <div class="detail-right">
                <div class="detail-card bidding-panel" style="position: sticky; top: 6rem;">
                    <div class="auc-badges">
                        @if($auctionData['status'] === 'live')
                            <span class="badge badge-live"><span class="pulse" style="width:6px;height:6px;background:currentColor;border-radius:50%;display:inline-block;"></span> {{ __('Live Auction') }}</span>
                        @else
                            <span class="badge badge-ended">{{ __($auctionData['status']) }}</span>
                        @endif
                        <span class="badge" style="background:var(--bg);border:1px solid var(--border);"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ $auctionData['location'] }}</span>
                    </div>

                    <h1>{{ app()->getLocale() == 'ar' ? $auctionData['title_ar'] : $auctionData['title_en'] }}</h1>
                    
                    <div style="display:flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        <span><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:text-bottom"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ __('Ends in') }}: <strong class="countdown-timer" data-end-time="{{ is_string($auctionData['end_time']) ? $auctionData['end_time'] : $auctionData['end_time']->format('Y-m-d\TH:i:s') }}" style="color:var(--text)">--:--:--</strong></span>
                        <span>{{ $auctionData['bids_count'] }} {{ __('bids') }}</span>
                    </div>

                    <div class="price-box">
                        <div class="label">{{ $auctionData['status'] === 'live' ? __('Current Bid') : __('Winning Bid') }}</div>
                        <div class="amount">{{ number_format($auctionData['current_price']) }} <span>SAR</span></div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.5rem;">{{ __('Min Increment') }}: {{ number_format($auctionData['min_bid_increment']) }} SAR</div>
                    </div>

                    <div class="auth-prompt">
                        <h3>{{ __('Want to place a bid?') }}</h3>
                        <p>{{ __('You must be logged in and have a verified account to participate in this auction.') }}</p>
                        <div class="actions">
                            @if(Route::has('login'))
                                @auth
                                    <a href="{{ route('bidder.auctions.show', $auctionData['id']) }}" class="btn btn-primary" style="width:100%">{{ __('Go to Bidder Panel') }}</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Log In') }}</a>
                                    <a href="{{ route('register') }}" class="btn btn-ghost">{{ __('Register') }}</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</main>
@endsection
