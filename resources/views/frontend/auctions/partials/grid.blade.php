{{-- Demo data notice --}}
@if($usingMock && count($auctions) > 0)
    <div class="demo-banner">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        {{ __('Showing demo data for design preview. Real auctions will appear once available.') }}
    </div>
@endif

@if($auctions->count() > 0)
    <div class="auctions-grid">
        @foreach($auctions as $i => $auction)
        @php
            $gradients = [
                'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)',
                'linear-gradient(135deg, #0f0c29 0%, #302b63 60%, #24243e 100%)',
                'linear-gradient(135deg, #1c1c2e 0%, #2d132c 100%)',
                'linear-gradient(135deg, #0a0a1a 0%, #1a2a3a 100%)',
                'linear-gradient(135deg, #141e30 0%, #243b55 100%)',
            ];
            $gradient = $gradients[$i % count($gradients)];

            $isMock    = is_array($auction);
            $id        = $isMock ? $auction['id'] : $auction->id;
            $img       = $isMock ? $auction['image'] : $auction->primary_image_url;
            $status    = $isMock ? $auction['status'] : $auction->status;
            $title     = $isMock ? (app()->getLocale() == 'ar' ? $auction['title_ar'] : $auction['title_en']) : $auction->title;
            $year      = $isMock ? $auction['year'] : ($auction->vehicle->year ?? '-');
            $make      = $isMock ? ($auction['make'] ?? '') : ($auction->vehicle->make_en ?? '');
            $bidsCount = $isMock ? $auction['bids_count'] : $auction->bids_count;
            $price     = $isMock ? $auction['current_price'] : $auction->current_price;
            $endTime   = $isMock ? $auction['end_time'] : $auction->end_time;
            $location  = $isMock ? ($auction['location'] ?? '') : ($auction->location_en ?? '');
        @endphp

        <a href="{{ route('frontend.auctions.show', $id) }}" class="auction-card" style="text-decoration:none;color:inherit;">
            {{-- Image --}}
            <div class="auction-img" style="background: {{ $gradient }}; {{ $img ? 'background-image: url('.$img.'); background-size: cover; background-position: center;' : '' }}">
                @if(!$img)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="width:64px;height:64px;color:rgba(255,255,255,0.15)"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg>
                @endif

                @if($status === 'live')
                    <div class="auction-live"><span class="pulse"></span> {{ __('Live') }}</div>
                    <div class="auction-timer countdown-timer" data-end-time="{{ is_string($endTime) ? $endTime : $endTime->format('Y-m-d\TH:i:s') }}">--:--:--</div>
                @elseif($status === 'upcoming')
                    <div class="auction-featured" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ __('Upcoming') }}
                    </div>
                    <div class="auction-timer countdown-timer" data-end-time="{{ is_string($endTime) ? $endTime : $endTime->format('Y-m-d\TH:i:s') }}">--:--:--</div>
                @else
                    <div class="auction-featured" style="background: rgba(100,116,139,0.85); box-shadow: none;">{{ __('Ended') }}</div>
                @endif
            </div>

            {{-- Body --}}
            <div class="auction-body">
                <h3>{{ $title }}</h3>
                <div class="auction-meta">
                    @if($year && $year !== '-')
                    <span>
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $year }}
                    </span>
                    @endif
                    @if($location)
                    <span>
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $location }}
                    </span>
                    @endif
                    <span class="auction-bids">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        {{ $bidsCount }} {{ __('bids') }}
                    </span>
                </div>
                <div class="auction-price">
                    <div>
                        <div class="label">{{ $status === 'live' ? __('Highest Bid') : ($status === 'upcoming' ? __('Starting Price') : __('Winning Bid')) }}</div>
                        <div class="price">{{ number_format($price) }} {{ __('SAR') }}</div>
                    </div>
                    <span class="btn btn-primary btn-sm" style="pointer-events:none;">
                        {{ $status === 'live' ? __('Bid Now') : __('Details') }}
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-inline-start:0.25rem"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if(!$usingMock && $auctions->hasPages())
        <div style="margin-top: 2rem;">
            {{ $auctions->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <h3>{{ __('No auctions found') }}</h3>
        <p>{{ __('Try adjusting your filters or search terms to discover more vehicles.') }}</p>
    </div>
@endif
