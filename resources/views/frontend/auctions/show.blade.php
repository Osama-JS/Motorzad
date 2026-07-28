@extends('layouts.landing')

@section('title', $auction->title . ' - Motorzad')

@push('styles')
<style>
.auction-show-page { padding: 8rem 0 4rem; background: var(--bg); min-height: 80vh; position: relative; }
/* Decorative background glow */
.auction-show-page::before { content: ''; position: absolute; top: -20%; left: -10%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(229,62,62,0.05) 0%, transparent 60%); pointer-events: none; }

.auc-detail-layout { display: grid; grid-template-columns: 1.6fr 1fr; gap: 2.5rem; position: relative; z-index: 1; }
.detail-card { background: rgba(var(--card-bg-rgb, 14, 20, 33), 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.05); border-radius: 1.5rem; overflow: hidden; padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }

/* Image Showcase */
.image-showcase { position: relative; border-radius: 1.25rem; overflow: hidden; margin-bottom: 1.5rem; background: #0b0f19; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
.main-img-wrap { position: relative; padding-top: 60%; }
.main-img-wrap img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.image-showcase:hover .main-img-wrap img { transform: scale(1.03); }
.gallery-thumbs { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.75rem; margin-bottom: 2rem; scrollbar-width: thin; }
.thumb-item { width: 90px; height: 60px; flex-shrink: 0; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: all 0.3s; background: #0b0f19; opacity: 0.6; }
.thumb-item:hover { opacity: 1; transform: translateY(-2px); }
.thumb-item.active { border-color: var(--primary); opacity: 1; box-shadow: 0 4px 12px rgba(229,62,62,0.3); }
.thumb-item img { width: 100%; height: 100%; object-fit: cover; }

/* Specs Grid */
.specs-detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; }
.spec-box { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.3s; }
.spec-box:hover { background: rgba(255,255,255,0.04); transform: translateY(-3px); border-color: rgba(229,62,62,0.2); }
.spec-box-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, rgba(229, 62, 62, 0.1) 0%, rgba(229, 62, 62, 0.05) 100%); color: var(--primary); display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(255,255,255,0.05); }
.spec-box-icon svg { width: 24px; height: 24px; }
.spec-box-info h4 { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.05em; }
.spec-box-info p { font-size: 1.05rem; font-weight: 800; color: var(--text); }

/* Bidding Section */
.bidding-panel h1 { font-size: 1.75rem; font-weight: 900; margin-bottom: 1rem; line-height: 1.3; letter-spacing: -0.02em; }
.auc-badges { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.badge { padding: 0.35rem 1rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.35rem; letter-spacing: 0.05em; text-transform: uppercase; }
.badge-live { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: 0 0 15px rgba(239, 68, 68, 0.1); }
.badge-ended { background: rgba(107, 114, 128, 0.1); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.2); }

.price-box { background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0.2) 100%); border: 1px solid rgba(255,255,255,0.05); border-radius: 1.25rem; padding: 2rem; margin-bottom: 2rem; text-align: center; position: relative; overflow: hidden; }
.price-box::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--primary), #ff6b6b); }
.price-box .label { font-size: 0.85rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; }
.price-box .amount { font-size: 3rem; font-weight: 900; color: var(--primary); display: flex; align-items: center; justify-content: center; gap: 0.5rem; letter-spacing: -0.03em; }
.price-box .amount span { font-size: 1.25rem; color: var(--text-muted); font-weight: 700; margin-top: 0.5rem; }

.auth-prompt { background: rgba(229, 62, 62, 0.05); border: 1px dashed rgba(229, 62, 62, 0.3); border-radius: 1.25rem; padding: 2rem; text-align: center; transition: all 0.3s; }
.auth-prompt:hover { background: rgba(229, 62, 62, 0.08); border-style: solid; border-color: rgba(229, 62, 62, 0.5); }
.auth-prompt h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text); }
.auth-prompt p { color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem; line-height: 1.5; }
.auth-prompt .actions { display: flex; gap: 1rem; justify-content: center; }
.auth-prompt .btn { padding: 0.875rem 2rem; font-weight: 800; border-radius: 0.75rem; letter-spacing: 0.02em; }

@media (max-width: 992px) {
    .auc-detail-layout { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<main class="auction-show-page">
    <div class="section-container">
        
        <div class="auc-detail-layout">
            
            <!-- Left: Gallery & Specs -->
            <div class="detail-left">
                <div class="detail-card">
                    <div class="image-showcase">
                        <div class="main-img-wrap">
                            <img id="mainImage" src="{{ $auction->primary_image_url ?: 'https://via.placeholder.com/800x450?text=No+Image' }}" alt="{{ $auction->title }}">
                        </div>
                    </div>
                    
                    @if($auction->vehicle && $auction->vehicle->images->count() > 0)
                    <div class="gallery-thumbs">
                        @foreach($auction->vehicle->images as $img)
                        <div class="thumb-item {{ $loop->first ? 'active' : '' }}" onclick="changeMainImage(this, '{{ Storage::url($img->image_path) }}')">
                            <img src="{{ Storage::url($img->image_path) }}" alt="Thumbnail">
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">{{ __('Vehicle Specifications') }}</h3>
                    <div class="specs-detail-grid">
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><div class="spec-box-info"><h4>{{ __('Make') }}</h4><p>{{ app()->getLocale() == 'ar' ? ($auction->vehicle->make_ar ?? $auction->vehicle->make_en) : ($auction->vehicle->make_en ?? $auction->vehicle->make_ar) }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div class="spec-box-info"><h4>{{ __('Model') }}</h4><p>{{ app()->getLocale() == 'ar' ? ($auction->vehicle->model_ar ?? $auction->vehicle->model_en) : ($auction->vehicle->model_en ?? $auction->vehicle->model_ar) }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="spec-box-info"><h4>{{ __('Year') }}</h4><p>{{ $auction->vehicle->year ?? '-' }}</p></div></div>
                        <div class="spec-box"><div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="spec-box-info"><h4>{{ __('Mileage') }}</h4><p>{{ number_format($auction->vehicle->mileage ?? 0) }} {{ __('KM') }}</p></div></div>
                    </div>

                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; margin-top: 2rem;">{{ __('Description') }}</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">
                        {{ app()->getLocale() == 'ar' ? ($auction->description_ar ?: $auction->description_en) : ($auction->description_en ?: $auction->description_ar) }}
                    </p>
                </div>
            </div>

            <!-- Right: Bidding -->
            <div class="detail-right">
                <div class="detail-card bidding-panel" style="position: sticky; top: 6rem;">
                    <div class="auc-badges">
                        @if($auction->status === 'live')
                            <span class="badge badge-live"><span class="pulse" style="width:6px;height:6px;background:currentColor;border-radius:50%;display:inline-block;"></span> {{ __('Live Auction') }}</span>
                        @else
                            <span class="badge badge-ended">{{ __($auction->status) }}</span>
                        @endif
                        <span class="badge" style="background:var(--bg);border:1px solid var(--border);"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ app()->getLocale() == 'ar' ? ($auction->location_ar ?: $auction->location_en) : ($auction->location_en ?: $auction->location_ar) }}</span>
                    </div>

                    <h1>{{ $auction->title }}</h1>
                    
                    <div style="display:flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        <span><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:text-bottom"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ __('Ends in') }}: <strong class="countdown-timer" data-end-time="{{ $auction->end_time->format('Y-m-d\TH:i:s') }}" style="color:var(--text)">--:--:--</strong></span>
                        <span>{{ $auction->bids_count }} {{ __('bids') }}</span>
                    </div>

                    <div class="price-box">
                        <div class="label">{{ $auction->status === 'live' ? __('Current Bid') : __('Winning Bid') }}</div>
                        <div class="amount">{{ number_format($auction->current_price) }} <span>SAR</span></div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.5rem;">{{ __('Min Increment') }}: {{ number_format($auction->min_bid_increment) }} SAR</div>
                    </div>

                    <div class="auth-prompt">
                        <h3>{{ __('Want to place a bid?') }}</h3>
                        <p>{{ __('You must be logged in and have a verified account to participate in this auction.') }}</p>
                        <div class="actions">
                            @if(Route::has('login'))
                                @auth
                                    <a href="{{ route('bidder.auctions.show', $auction->id) }}" class="btn btn-primary" style="width:100%">{{ __('Go to Bidder Panel') }}</a>
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

@push('scripts')
<script>
function changeMainImage(element, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}
</script>
@endpush
