@extends('layouts.bidder')

@section('title', __('Bidder Dashboard'))

@section('content')
{{-- ===== WELCOME HERO ===== --}}
<div class="bidder-hero">
    <div class="hero-content">
        <div class="hero-greeting">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            {{ __('Bidder Dashboard') }}
        </div>
        <h2>{{ __('Welcome back') }}، <span class="user-name">{{ auth()->user()->full_name }}</span> 👋</h2>
        <p>{{ __('Track your auctions, manage your bids, and discover new opportunities. Your auction journey starts here.') }}</p>
        <div class="hero-actions">
            <a href="#" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                {{ __('Browse Auctions') }}
            </a>
            <a href="#" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ __('Live Auctions') }}
            </a>
        </div>
    </div>
    <div class="hero-stats">
        <div class="hero-stat-item red">
            <div class="stat-num">{{ $stats['active_bids'] }}</div>
            <div class="stat-txt">{{ __('Active Bids') }}</div>
        </div>
        <div class="hero-stat-item green">
            <div class="stat-num">{{ $stats['won_auctions'] }}</div>
            <div class="stat-txt">{{ __('Won') }}</div>
        </div>
        <div class="hero-stat-item gold">
            <div class="stat-num">{{ $stats['watchlist'] }}</div>
            <div class="stat-txt">{{ __('Watchlist') }}</div>
        </div>
    </div>
</div>

{{-- ===== STATS GRID ===== --}}
<div class="stats-grid" style="margin-top:1.5rem;">
    <div class="stat-card red">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_bids'] }}</div>
        <div class="stat-label">{{ __('Total Bids') }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 9 7 12 7s5-3 7.5-3a2.5 2.5 0 0 1 0 5H18"/><path d="M18 9v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9"/><path d="M12 7v14"/></svg>
        </div>
        <div class="stat-value">{{ $stats['won_auctions'] }}</div>
        <div class="stat-label">{{ __('Won Auctions') }}</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="stat-value">{{ number_format($stats['total_spent']) }}</div>
        <div class="stat-label">{{ __('Total Spent') }} ({{ __('SAR') }})</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div class="stat-value">%{{ $stats['win_rate'] }}</div>
        <div class="stat-label">{{ __('Win Rate') }}</div>
    </div>
</div>

{{-- ===== QUICK ACTIONS ===== --}}
<div class="quick-actions">
    <a href="#" class="quick-action-card red">
        <div class="qa-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="qa-text">
            <div class="qa-title">{{ __('Live Auctions') }}</div>
            <div class="qa-sub">{{ $stats['live_count'] }} {{ __('auction running') }}</div>
        </div>
    </a>
    <a href="#" class="quick-action-card blue">
        <div class="qa-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div class="qa-text">
            <div class="qa-title">{{ __('My Bids') }}</div>
            <div class="qa-sub">{{ $stats['active_bids'] }} {{ __('active bids') }}</div>
        </div>
    </a>
    <a href="#" class="quick-action-card gold">
        <div class="qa-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="qa-text">
            <div class="qa-title">{{ __('Watchlist') }}</div>
            <div class="qa-sub">{{ $stats['watchlist'] }} {{ __('items saved') }}</div>
        </div>
    </a>
    <a href="#" class="quick-action-card green">
        <div class="qa-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="qa-text">
            <div class="qa-title">{{ __('My Wallet') }}</div>
            <div class="qa-sub">{{ number_format($stats['wallet_balance']) }} {{ __('SAR') }}</div>
        </div>
    </a>
</div>

{{-- ===== TWO COLUMN LAYOUT ===== --}}
<div class="two-col">
    {{-- === LEFT: FEATURED AUCTIONS === --}}
    <div class="col-wide">
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-inline-end:0.5rem;vertical-align:-3px;color:var(--brand-red-light)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ __('Featured Auctions') }}
                </h2>
                <a href="#" class="btn btn-ghost btn-sm">{{ __('View All') }}</a>
            </div>
            <div class="card-body">
                <div class="auctions-grid">
                    @forelse($featuredAuctions as $auction)
                    <div class="auction-card">
                        <div class="auction-img">
                            <img src="{{ $auction['image'] }}" alt="{{ $auction['title'] }}" loading="lazy">
                            <span class="auction-badge {{ $auction['status'] }}">
                                @if($auction['status'] === 'live') {{ __('Live') }}
                                @elseif($auction['status'] === 'upcoming') {{ __('Upcoming') }}
                                @else {{ __('Ended') }}
                                @endif
                            </span>
                            <div class="auction-timer">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $auction['time_left'] }}
                            </div>
                        </div>
                        <div class="auction-body">
                            <div class="auction-title">{{ $auction['title'] }}</div>
                            <div class="auction-meta">
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $auction['location'] }}
                                </span>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    {{ $auction['bidders'] }} {{ __('bidders') }}
                                </span>
                            </div>
                            <div class="auction-price-row">
                                <div class="current-price">
                                    <span class="price-label">{{ __('Current Price') }}</span>
                                    {{ number_format($auction['current_price']) }} {{ __('SAR') }}
                                </div>
                                <button class="bid-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                                    {{ __('Bid Now') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="grid-column:1/-1;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <p>{{ __('No featured auctions at this time.') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- === RIGHT SIDEBAR === --}}
    <div class="col-aside">
        {{-- Wallet Card --}}
        <div class="wallet-card">
            <div class="wallet-label">{{ __('Available Balance') }}</div>
            <div class="wallet-amount">{{ number_format($stats['wallet_balance'], 2) }}</div>
            <div class="wallet-currency">{{ __('Saudi Riyal') }} (SAR)</div>
            <div class="wallet-actions">
                <a href="#" class="btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    {{ __('Deposit') }}
                </a>
                <a href="#" class="btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 11 12 6 17 11"/><line x1="12" y1="6" x2="12" y2="18"/></svg>
                    {{ __('Withdraw') }}
                </a>
            </div>
        </div>

        {{-- Mini Stats --}}
        <div class="bidder-mini-stats">
            <div class="mini-stat red">
                <div class="ms-val">{{ $stats['active_bids'] }}</div>
                <div class="ms-lbl">{{ __('Active Bids') }}</div>
            </div>
            <div class="mini-stat green">
                <div class="ms-val">{{ $stats['won_auctions'] }}</div>
                <div class="ms-lbl">{{ __('Won') }}</div>
            </div>
            <div class="mini-stat blue">
                <div class="ms-val">{{ $stats['total_bids'] }}</div>
                <div class="ms-lbl">{{ __('Total Bids') }}</div>
            </div>
            <div class="mini-stat gold">
                <div class="ms-val">{{ $stats['watchlist'] }}</div>
                <div class="ms-lbl">{{ __('Watchlist') }}</div>
            </div>
        </div>

        {{-- Bid Win Rate --}}
        <div class="card" style="margin-top:1.25rem;">
            <div class="card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
                    <span style="font-size:0.85rem;font-weight:700;">{{ __('Win Rate') }}</span>
                    <span style="font-family:'Orbitron',sans-serif;font-size:0.9rem;font-weight:800;color:var(--success);">{{ $stats['win_rate'] }}%</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill green" style="width:{{ $stats['win_rate'] }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card" style="margin-top:1.25rem;">
            <div class="card-header">
                <h2>{{ __('Recent Activity') }}</h2>
            </div>
            <div class="card-body" style="padding:0.5rem 1.5rem;">
                <ul class="activity-timeline">
                    @forelse($recentActivity as $activity)
                    <li class="activity-item">
                        <div class="act-icon {{ $activity['type'] }}">
                            @if($activity['type'] === 'bid')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                            @elseif($activity['type'] === 'won')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            @elseif($activity['type'] === 'outbid')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            @endif
                        </div>
                        <div class="act-content">
                            <div class="act-title">{{ $activity['title'] }}</div>
                            <div class="act-desc">{{ $activity['desc'] }}</div>
                        </div>
                        <span class="act-time">{{ $activity['time'] }}</span>
                    </li>
                    @empty
                    <li class="activity-item">
                        <div class="act-content" style="text-align:center;color:var(--text-muted);padding:1rem 0;">
                            {{ __('No recent activity.') }}
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
