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

            $winningAmount = $isMock ? $auc['current_price'] : ($auc->winning_bid_amount ?: $auc->current_price);
            
            $order = $isMock ? null : ($auc->order ?? null);
            $paymentStatus = $isMock ? ($auc['payment_status'] ?? 'pending') : ($order?->payment_status ?? 'pending');
            
            if ($paymentStatus === 'paid') {
                $payBadgeClass = 'paid';
                $payBadgeLabel = app()->getLocale() === 'ar' ? 'تم السداد ومكتملة' : 'Paid / Completed';
            } else {
                $payBadgeClass = 'pending';
                $payBadgeLabel = app()->getLocale() === 'ar' ? 'بانتظار الدفع' : 'Pending Payment';
            }
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

                {{-- Winning Amount --}}
                <div class="price-tag">
                    <span class="label">{{ app()->getLocale() === 'ar' ? 'سعر الفوز' : 'Winning Amount' }}</span>
                    <span class="amount won-amount">{{ number_format($winningAmount) }} SAR</span>
                </div>

                {{-- Status Badges --}}
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <span class="bid-state-badge won">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        {{ app()->getLocale() === 'ar' ? 'فزت بالمزاد' : 'Won Auction' }}
                    </span>
                    <span class="bid-state-badge pay-state-badge {{ $payBadgeClass }}">
                        @if($payBadgeClass === 'paid')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        @else
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @endif
                        {{ $payBadgeLabel }}
                    </span>
                </div>

                {{-- Action CTA --}}
                <div class="action-col" style="gap: 0.75rem; display: flex; align-items: center;">
                    <a href="{{ route('bidder.auctions.show', $id) }}" class="btn-action-view secondary-outline">
                        {{ app()->getLocale() === 'ar' ? 'تفاصيل المزاد' : 'View Details' }}
                    </a>
                    
                    @if($payBadgeClass !== 'paid')
                        <button class="btn-action-view complete-purchase-btn" 
                                data-id="{{ $id }}"
                                data-title="{{ $title }}"
                                data-amount="{{ number_format($winningAmount) }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-inline-end: 0.2rem;"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'إتمام الشراء' : 'Complete Purchase' }}
                        </button>
                    @else
                        <button class="btn-action-view" disabled style="background: rgba(16, 185, 129, 0.15); color: #10b981; box-shadow: none; cursor: not-allowed; border: none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-inline-end: 0.2rem;"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'تم الدفع بنجاح' : 'Paid Successfully' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 4rem 2rem; border-radius: var(--radius-xl); text-align: center; color: var(--text-muted);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:1rem; opacity:0.5;"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
            <h4 style="font-weight:800; color:var(--text); margin-bottom:0.5rem;">{{ app()->getLocale() === 'ar' ? 'لا توجد مزادات فائزة' : 'No won auctions found' }}</h4>
            <p style="font-size:0.95rem; max-width:400px; margin:0 auto 1.5rem;">{{ app()->getLocale() === 'ar' ? 'لم تفز بأي مزاد بعد. ابدأ بالمزايدة والمشاركة في المزادات الحية للفوز بها!' : 'You have not won any auction yet. Start bidding in live auctions to win!' }}</p>
            <a href="{{ route('bidder.auctions.index', ['tab' => 'live']) }}" class="btn-action-view" style="margin: 0 auto; display: inline-flex;">
                {{ app()->getLocale() === 'ar' ? 'تصفح المزادات الحية' : 'Browse Live Auctions' }}
            </a>
        </div>
    @endforelse
</div>

{{-- Pagination Links --}}
@if(!$usingMock && method_exists($auctions, 'links'))
    <div style="margin-top: 2rem;" class="pagination-wrapper">
        {{ $auctions->links() }}
    </div>
@endif
