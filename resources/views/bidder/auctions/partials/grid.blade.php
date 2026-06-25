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
        <div style="margin-top: 2rem; display: flex; justify-content: center;" class="pagination-wrapper">
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
        <a href="{{ route('bidder.auctions.index') }}" class="btn btn-primary btn-reset-filters">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset Filters' }}</a>
    </div>
@endif
