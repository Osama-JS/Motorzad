@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions')

@section('css')
<style>
/* ===== PREMIUM WON AUCTIONS VIEW ===== */
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
    background: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15), transparent 50%), 
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
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
.stat-icon-wrap.won { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon-wrap.value { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.stat-icon-wrap.action { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

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
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
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
.price-tag .amount.won-amount {
    color: #10b981;
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
.bid-state-badge.won {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
}
.pay-state-badge.paid {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
}
.pay-state-badge.pending {
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

/* Tabs & Filters */
.filters-bar {
    display: flex;
    justify-content: flex-start;
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
    cursor: pointer;
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

.action-col {
    display: flex;
    justify-content: flex-end;
}
.btn-action-view {
    background: #10b981;
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
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    cursor: pointer;
}
.btn-action-view:hover {
    background: #059669;
    color: white;
    transform: translateY(-1px);
}
.btn-action-view.secondary-outline {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text);
    box-shadow: none;
}
.btn-action-view.secondary-outline:hover {
    background: var(--bg-hover);
    color: var(--text);
}

/* Modal styling overrides */
.premium-modal .modal-content {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    color: var(--text);
}
.premium-modal .modal-header {
    border-bottom: 1px solid var(--border);
    padding: 1.5rem;
}
.premium-modal .modal-footer {
    border-top: 1px solid var(--border);
    padding: 1.25rem 1.5rem;
}
.premium-modal .modal-title {
    font-weight: 800;
    color: var(--text);
}
.premium-modal .close {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.2s;
}
.premium-modal .close:hover {
    color: var(--text);
}

/* Bank details inside modal */
.bank-cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-top: 1rem;
}
.bank-card-item {
    background: var(--bg-body);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    position: relative;
    transition: border-color 0.2s;
}
.bank-card-item:hover {
    border-color: #10b981;
}
.bank-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.bank-name-lbl {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text);
}
.bank-meta-lbl {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.15rem;
}
.bank-meta-val {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text);
}
.iban-copy-btn {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: none;
    border-radius: 6px;
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.iban-copy-btn:hover {
    background: #10b981;
    color: white;
}

.step-item {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}
.step-number {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
    margin-top: 2px;
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
        <h1>{{ app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'تهانينا! هنا يمكنك متابعة وإتمام إجراءات الشراء لجميع المركبات والمزادات التي فزت بها.' : 'Congratulations! Here you can monitor and finalize the purchase steps for all vehicles you have won.' }}</p>
    </div>
</div>

{{-- Dynamic Stats Widgets Row --}}
@php
    $totalCount = $auctions->count();
    $totalValue = 0;
    foreach($auctions as $auc) {
        $totalValue += is_array($auc) ? $auc['current_price'] : ($auc->winning_bid_amount ?: $auc->current_price);
    }
@endphp
<div class="stats-row">
    <div class="stat-widget">
        <div class="stat-icon-wrap won">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $totalCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions' }}</span>
        </div>
    </div>
    
    <div class="stat-widget">
        <div class="stat-icon-wrap value">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ number_format($totalValue) }} SAR</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'إجمالي القيمة الفائزة' : 'Total Winning Value' }}</span>
        </div>
    </div>

    <div class="stat-widget">
        <div class="stat-icon-wrap action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val" style="color: #f59e0b;">{{ app()->getLocale() === 'ar' ? 'بانتظار السداد' : 'Pending Payment' }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'الخطوة التالية' : 'Next Step' }}</span>
        </div>
    </div>
</div>

{{-- ===== FILTER TABS ===== --}}
<div class="filters-bar" style="margin-top: 1rem;">
    <div class="auc-tabs">
        <a href="#" data-status="" class="auc-tab won-filter-tab {{ empty($paymentStatusFilter) ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
        </a>
        <a href="#" data-status="pending" class="auc-tab won-filter-tab {{ ($paymentStatusFilter ?? '') === 'pending' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'بانتظار الدفع' : 'Pending Payment' }}
        </a>
        <a href="#" data-status="paid" class="auc-tab won-filter-tab {{ ($paymentStatusFilter ?? '') === 'paid' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'تم السداد ومكتملة' : 'Paid / Completed' }}
        </a>
    </div>
</div>

{{-- Main List Container --}}
<div id="won-auctions-container">
    @include('bidder.auctions.partials.won-auctions-list')
</div>

{{-- COMPLETE PURCHASE MODAL --}}
<div class="modal fade premium-modal" id="completePurchaseModal" tabindex="-1" role="dialog" aria-labelledby="completePurchaseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completePurchaseModalTitle">
                    {{ app()->getLocale() === 'ar' ? 'إتمام شراء المركبة' : 'Complete Vehicle Purchase' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 text-center rounded-3 mb-3" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.15); color: #10b981;">
                    <span style="font-weight: 700;" id="modal-vehicle-title"></span>
                    <br>
                    <span style="font-size: 1.15rem; font-weight: 800;">{{ app()->getLocale() === 'ar' ? 'المبلغ المطلوب سداده:' : 'Amount to pay:' }} <span id="modal-total-amount"></span> SAR</span>
                </div>

                <div class="purchase-steps mb-3">
                    <h6 style="font-weight: 800; margin-bottom: 0.50rem;">{{ app()->getLocale() === 'ar' ? 'خطوات السداد والاستلام:' : 'Payment & Delivery Steps:' }}</h6>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>{{ app()->getLocale() === 'ar' ? 'قم بتحويل قيمة السيارة إلى أحد حساباتنا البنكية المدرجة أدناه.' : 'Transfer the vehicle value to one of our listed bank accounts.' }}</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>{{ app()->getLocale() === 'ar' ? 'توجه إلى محفظتك الإلكترونية وارفع طلب إيداع جديد بصورة التحويل.' : 'Go to your Wallet dashboard and submit a deposit request with the receipt.' }}</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>{{ app()->getLocale() === 'ar' ? 'سيتواصل معك فريق خدمة العملاء فور تأكيد الإيداع لتسليم السيارة.' : 'Customer care will contact you to arrange delivery once payment is verified.' }}</div>
                    </div>
                </div>

                <h6 style="font-weight: 800; margin-bottom: 0.50rem;">{{ app()->getLocale() === 'ar' ? 'حساباتنا البنكية المعتمدة:' : 'Approved Bank Accounts:' }}</h6>
                <div class="bank-cards-grid">
                    @foreach($bankAccounts as $acc)
                        <div class="bank-card-item">
                            <div class="bank-logo-wrap">
                                <span class="bank-name-lbl">{{ $acc->bank_name }}</span>
                                <button class="iban-copy-btn" data-iban="{{ $acc->iban }}">
                                    {{ app()->getLocale() === 'ar' ? 'نسخ الآيبان' : 'Copy IBAN' }}
                                </button>
                            </div>
                            <div class="mb-2">
                                <div class="bank-meta-lbl">{{ app()->getLocale() === 'ar' ? 'اسم المستفيد' : 'Beneficiary Name' }}</div>
                                <div class="bank-meta-val">{{ $acc->beneficiary_name }}</div>
                            </div>
                            <div>
                                <div class="bank-meta-lbl">{{ app()->getLocale() === 'ar' ? 'رقم الآيبان (IBAN)' : 'IBAN Number' }}</div>
                                <div class="bank-meta-val text-monospace" style="font-size: 0.85rem;">{{ $acc->iban }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action-view secondary-outline py-2" data-dismiss="modal">
                    {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
                </button>
                <a href="{{ route('bidder.wallet.index') }}" class="btn-action-view py-2">
                    {{ app()->getLocale() === 'ar' ? 'الذهاب إلى المحفظة لإرفاق الإيصال' : 'Go to Wallet to Attach Receipt' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initial active tab state sync from URL
    syncWonFormInputs(window.location.href);

    // Tab switching click handler via AJAX
    $(document).on('click', '.won-filter-tab', function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        
        // Visual feedback
        $('.won-filter-tab').removeClass('active');
        $(this).addClass('active');

        // Construct the AJAX URL
        const baseUrl = '{{ route("bidder.auctions.won") }}';
        const url = status ? (baseUrl + '?payment_status=' + status) : baseUrl;

        loadWonAuctions(url);
    });

    // Pagination links click handler via AJAX
    $(document).on('click', '#won-auctions-container .pagination-wrapper a, #won-auctions-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadWonAuctions(url);
        }
    });

    function loadWonAuctions(url) {
        $('#won-auctions-container').css('opacity', '0.5');

        BidderAjax.get(url, {}, {
            onSuccess: function(response) {
                $('#won-auctions-container').css('opacity', '1');
                if (response.success && response.html) {
                    $('#won-auctions-container').html(response.html);
                    window.history.pushState(null, null, url);
                    
                    // Sync active tab visually with current URL param
                    syncWonFormInputs(url);
                } else {
                    toastr.error('Failed to load won auctions.');
                }
            },
            onError: function() {
                $('#won-auctions-container').css('opacity', '1');
                toastr.error('Failed to load won auctions.');
            }
        });
    }

    function syncWonFormInputs(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            const statusParam = urlObj.searchParams.get('payment_status') || '';

            // Update tab selection highlight
            $('.won-filter-tab').removeClass('active');
            $(`.won-filter-tab[data-status="${statusParam}"]`).addClass('active');
        } catch(err) {
            console.error('syncWonFormInputs failed', err);
        }
    }

    // Handle back/forward navigation state restore
    window.addEventListener('popstate', function() {
        const currentUrl = window.location.href;
        loadWonAuctions(currentUrl);
    });

    // Complete Purchase Button Trigger
    $(document).on('click', '.complete-purchase-btn', function() {
        const title = $(this).data('title');
        const amount = $(this).data('amount');
        
        $('#modal-vehicle-title').text(title);
        $('#modal-total-amount').text(amount);
        
        $('#completePurchaseModal').modal('show');
    });

    // IBAN Copy Handler
    $(document).on('click', '.iban-copy-btn', function() {
        const iban = $(this).data('iban');
        const btn = $(this);
        
        navigator.clipboard.writeText(iban).then(function() {
            const originalText = btn.text();
            btn.text("{{ __('Copied!') }}").css('background', '#10b981').css('color', 'white');
            toastr.success("{{ __('IBAN copied to clipboard!') }}");
            setTimeout(function() {
                btn.text(originalText).css('background', '').css('color', '');
            }, 2000);
        }).catch(function() {
            toastr.error('Could not copy text.');
        });
    });
});
</script>
@endsection
