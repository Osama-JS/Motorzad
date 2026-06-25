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

/* Tabs & Filters */
.filters-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.auc-tabs {
    display: flex;
    gap: 0.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    padding: 0.35rem;
    border-radius: 14px;
}
.auc-tab {
    padding: 0.65rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-muted);
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
}
.auc-tab:hover {
    color: var(--text);
    background: var(--bg-hover);
}
.auc-tab.active {
    background: var(--brand-red);
    color: white !important;
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.25);
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

{{-- Bids Filter Tabs --}}
<div class="filters-bar">
    <div class="auc-tabs" id="my-bids-tabs">
        <a href="{{ route('bidder.auctions.my-bids', ['status' => 'all']) }}" class="auc-tab {{ $status === 'all' ? 'active' : '' }}" data-status="all">
            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
        </a>
        <a href="{{ route('bidder.auctions.my-bids', ['status' => 'winning']) }}" class="auc-tab {{ $status === 'winning' ? 'active' : '' }}" data-status="winning">
            {{ app()->getLocale() === 'ar' ? 'في الصدارة' : 'Winning' }}
        </a>
        <a href="{{ route('bidder.auctions.my-bids', ['status' => 'outbid']) }}" class="auc-tab {{ $status === 'outbid' ? 'active' : '' }}" data-status="outbid">
            {{ app()->getLocale() === 'ar' ? 'تم تخطيك' : 'Outbid' }}
        </a>
        <a href="{{ route('bidder.auctions.my-bids', ['status' => 'won']) }}" class="auc-tab {{ $status === 'won' ? 'active' : '' }}" data-status="won">
            {{ app()->getLocale() === 'ar' ? 'فزت بها' : 'Won' }}
        </a>
        <a href="{{ route('bidder.auctions.my-bids', ['status' => 'lost']) }}" class="auc-tab {{ $status === 'lost' ? 'active' : '' }}" data-status="lost">
            {{ app()->getLocale() === 'ar' ? 'خسرتها' : 'Lost' }}
        </a>
    </div>
</div>

{{-- Main List Container --}}
<div id="my-bids-container">
    @include('bidder.auctions.partials.my-bids-list')
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Tab switching via AJAX
    $(document).on('click', '#my-bids-tabs .auc-tab', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (!url) return;

        // Visual feedback
        $('#my-bids-tabs .auc-tab').removeClass('active');
        $(this).addClass('active');

        loadMyBids(url);
    });

    // Pagination links click handler via AJAX
    $(document).on('click', '#my-bids-container .pagination-wrapper a, #my-bids-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadMyBids(url);
        }
    });

    function loadMyBids(url) {
        $('#my-bids-container').css('opacity', '0.5');

        BidderAjax.get(url, {}, {
            onSuccess: function(response) {
                $('#my-bids-container').css('opacity', '1');
                if (response.success && response.html) {
                    $('#my-bids-container').html(response.html);
                    window.history.pushState(null, null, url);

                    // Sync tab active class if changed programmatically
                    try {
                        const urlObj = new URL(url, window.location.origin);
                        const statusParam = urlObj.searchParams.get('status') || 'all';
                        $('#my-bids-tabs .auc-tab').removeClass('active');
                        $(`#my-bids-tabs .auc-tab[data-status="${statusParam}"]`).addClass('active');
                    } catch(err) {
                        console.error('URL parsing failed', err);
                    }
                } else {
                    toastr.error('Failed to load bids.');
                }
            },
            onError: function() {
                $('#my-bids-container').css('opacity', '1');
                toastr.error('Failed to load bids.');
            }
        });
    }

    window.addEventListener('popstate', function() {
        loadMyBids(window.location.href);
    });
});
</script>
@endsection
