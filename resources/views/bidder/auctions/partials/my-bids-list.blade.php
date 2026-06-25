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
    <div style="margin-top: 2rem;" class="pagination-wrapper">
        {{ $auctions->links() }}
    </div>
@endif
