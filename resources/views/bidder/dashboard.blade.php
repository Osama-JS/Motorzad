@extends('layouts.bidder')

@section('title', __('Bidder Dashboard'))

@section('content')

{{-- ===== PREMIUM WELCOME HERO ===== --}}
<div class="premium-hero">
    <div class="hero-content">
        <div class="hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            {{ __('Bidder Dashboard') }}
        </div>
        <h2>{{ __('Welcome back') }}، <span class="user-name">{{ auth()->user()->full_name }}</span> 👋</h2>
        <p>{{ __('Track your auctions, manage your bids, and discover new opportunities. Experience the next generation of premium bidding.') }}</p>
        <div class="hero-actions">
            <a href="#" class="btn-premium primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                {{ __('Browse Auctions') }}
            </a>
            <a href="#" class="btn-premium secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ __('Live Auctions') }}
            </a>
        </div>
    </div>
    
    <div class="hero-glass-stats">
        <div class="glass-stat-item red">
            <div class="stat-num">{{ $stats['active_bids'] }}</div>
            <div class="stat-label">{{ __('Active Bids') }}</div>
        </div>
        <div class="glass-stat-item green">
            <div class="stat-num">{{ $stats['won_auctions'] }}</div>
            <div class="stat-label">{{ __('Won') }}</div>
        </div>
        <div class="glass-stat-item gold">
            <div class="stat-num">{{ $stats['watchlist'] }}</div>
            <div class="stat-label">{{ __('Watchlist') }}</div>
        </div>
    </div>
</div>

{{-- ===== PREMIUM QUICK ACTIONS ===== --}}
<div class="premium-actions">
    <a href="#" class="p-action-card red">
        <div class="p-action-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="p-action-text">
            <div class="p-action-title">{{ __('Live Auctions') }}</div>
            <div class="p-action-sub">{{ $stats['live_count'] }} {{ __('running now') }}</div>
        </div>
    </a>
    
    <a href="#" class="p-action-card blue">
        <div class="p-action-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div class="p-action-text">
            <div class="p-action-title">{{ __('My Bids') }}</div>
            <div class="p-action-sub">{{ $stats['active_bids'] }} {{ __('active bids') }}</div>
        </div>
    </a>
    
    <a href="#" class="p-action-card gold">
        <div class="p-action-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="p-action-text">
            <div class="p-action-title">{{ __('Watchlist') }}</div>
            <div class="p-action-sub">{{ $stats['watchlist'] }} {{ __('items saved') }}</div>
        </div>
    </a>
    
    <a href="#" class="p-action-card green">
        <div class="p-action-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="p-action-text">
            <div class="p-action-title">{{ __('Wallet') }}</div>
            <div class="p-action-sub">{{ number_format($stats['wallet_balance']) }} {{ __('SAR') }}</div>
        </div>
    </a>
</div>

{{-- ===== TWO COLUMN LAYOUT ===== --}}
<div class="dashboard-grid">
    {{-- === LEFT: FEATURED AUCTIONS === --}}
    <div class="grid-col-left">
        <div class="premium-card">
            <div class="premium-card-header">
                <h2>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ __('Featured Auctions') }}
                </h2>
                <a href="#" class="btn btn-ghost btn-sm">{{ __('View All') }}</a>
            </div>
            
            <div class="premium-auctions-grid">
                @forelse($featuredAuctions as $auction)
                <div class="auc-card">
                    <div class="auc-img-wrap">
                        <img src="{{ $auction['image'] }}" alt="{{ $auction['title'] }}" loading="lazy">
                        <span class="auc-badge {{ $auction['status'] }}">
                            @if($auction['status'] === 'live') {{ __('Live') }}
                            @elseif($auction['status'] === 'upcoming') {{ __('Upcoming') }}
                            @else {{ __('Ended') }}
                            @endif
                        </span>
                        <div class="auc-timer">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $auction['time_left'] }}
                        </div>
                    </div>
                    
                    <div class="auc-body">
                        <div class="auc-title">{{ $auction['title'] }}</div>
                        <div class="auc-meta">
                            <span class="auc-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $auction['location'] }}
                            </span>
                            <span class="auc-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                {{ $auction['bidders'] }} {{ __('bidders') }}
                            </span>
                        </div>
                        <div class="auc-footer">
                            <div class="auc-price">
                                <span class="label">{{ __('Current Price') }}</span>
                                <span class="value">{{ number_format($auction['current_price']) }} {{ __('SAR') }}</span>
                            </div>
                            <button class="auc-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                                {{ __('Bid') }}
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="grid-column:1/-1;">
                    <p>{{ __('No featured auctions at this time.') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- === RIGHT SIDEBAR === --}}
    <div class="grid-col-right">
        
        {{-- Wallet Card --}}
        <div class="premium-wallet">
            <div class="wallet-header">
                <span class="wallet-title">{{ __('Available Balance') }}</span>
                <div class="wallet-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
            </div>
            
            <div class="wallet-balance">{{ number_format($stats['wallet_balance'], 2) }}</div>
            <div class="wallet-currency">{{ __('SAR') }} - {{ __('Saudi Riyal') }}</div>
            
            <div class="wallet-buttons">
                <button class="w-btn deposit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    {{ __('Deposit') }}
                </button>
                <button class="w-btn withdraw">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 11 12 6 17 11"/><line x1="12" y1="6" x2="12" y2="18"/></svg>
                    {{ __('Withdraw') }}
                </button>
            </div>
        </div>

        {{-- Mini Stats --}}
        <div class="side-stats">
            <div class="s-stat red">
                <div class="val">{{ $stats['total_bids'] }}</div>
                <div class="lbl">{{ __('Total Bids') }}</div>
            </div>
            <div class="s-stat green">
                <div class="val">{{ $stats['won_auctions'] }}</div>
                <div class="lbl">{{ __('Auctions Won') }}</div>
            </div>
        </div>

        {{-- Win Rate --}}
        <div class="premium-card win-rate-card">
            <div class="wr-header">
                <span class="wr-title">{{ __('Win Rate') }}</span>
                <span class="wr-percent">{{ $stats['win_rate'] }}%</span>
            </div>
            <div class="wr-progress-bg">
                <div class="wr-progress-fill" style="width:{{ $stats['win_rate'] }}%;"></div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="premium-card timeline-card">
            <div class="premium-card-header" style="padding-bottom: 1rem; border: none;">
                <h2 style="font-size: 1.1rem;">{{ __('Recent Activity') }}</h2>
            </div>
            
            <ul class="p-timeline">
                @forelse($recentActivity as $activity)
                <li class="p-timeline-item">
                    <div class="p-tl-icon {{ $activity['type'] }}">
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
                    
                    <div class="p-tl-content">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div class="p-tl-title">{{ $activity['title'] }}</div>
                            <div class="p-tl-time">{{ $activity['time'] }}</div>
                        </div>
                        <div class="p-tl-desc">{{ $activity['desc'] }}</div>
                    </div>
                </li>
                @empty
                <li class="p-timeline-item" style="border:none;">
                    <div style="text-align:center; width:100%; color:var(--text-muted); padding:1rem 0;">
                        {{ __('No recent activity.') }}
                    </div>
                </li>
                @endforelse
            </ul>
        </div>
        
    </div>
</div>

@endsection
