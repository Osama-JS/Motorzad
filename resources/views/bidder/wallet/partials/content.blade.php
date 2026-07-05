{{-- ===== WALLET HERO CARD ===== --}}
<div class="wallet-hero-card">
    <div class="wallet-hero-bg"></div>
    <div class="wallet-hero-content">
        <div class="wallet-hero-left">
            <div class="wallet-hero-avatar">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->full_name }}">
                <div class="wallet-verified-badge {{ $user->status === 'approved' ? 'verified' : '' }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            <div class="wallet-hero-info">
                <h1>{{ $user->full_name }}</h1>
                <p class="wallet-hero-email">{{ $user->email }}</p>
                <div class="wallet-hero-badges">
                    <span class="w-badge kyc">{{ __('KYC Level') }} {{ $user->kyc_level }}</span>
                    <span class="w-badge status {{ $user->status }}">
                        @if($user->status === 'approved') {{ __('Verified') }} ✅
                        @elseif($user->status === 'pending') {{ __('Pending Review') }} ⏳
                        @else {{ __('Rejected') }} ❌
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="wallet-hero-balance">
            <span class="balance-label">{{ __('Available Balance') }}</span>
            <span class="balance-amount">{{ number_format($wallet->available_balance, 2) }}</span>
            <span class="balance-currency">{{ __('SAR') }}</span>
            <div style="font-size: 0.85rem; opacity: 0.85; margin-top: 8px; display: flex; gap: 10px; justify-content: flex-end;">
                <span>{{ __('Total:') }} {{ number_format($wallet->balance, 2) }}</span>
                @if($wallet->frozen_balance > 0)
                <div class="dropdown d-inline-block">
                    <span style="color: #ffd700; cursor: pointer; text-decoration: underline; text-underline-offset: 4px; text-decoration-style: dotted;" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('انقر لعرض التفاصيل') }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-top:-2px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> {{ __('Frozen:') }} {{ number_format($wallet->frozen_balance, 2) }}
                    </span>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="width: 320px; border-radius: 12px; border: 1px solid var(--border, #333); background: var(--bg-card, #1e1e1e); z-index: 1050;">
                        <h6 class="dropdown-header fw-bold px-0 mb-3" style="color: var(--text-muted, #9ca3af); font-size: 0.85rem; border-bottom: 1px solid var(--border, #333); padding-bottom: 8px;">{{ __('تفاصيل المبالغ المحجوزة') }}</h6>
                        <div class="d-flex flex-column gap-2">
                            @forelse($wallet->frozen_bids as $bid)
                                @php
                                    $frozenAmount = $bid->is_auto_bid ? max($bid->amount, $bid->max_auto_bid) : $bid->amount;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                    <span class="text-truncate" style="max-width: 190px;" title="{{ $bid->auction->title ?? __('مزاد') }}">
                                        {{ $bid->auction->title ?? __('مزاد') }}
                                    </span>
                                    <span class="fw-bold" style="color: #ef4444;">
                                        - {{ number_format($frozenAmount, 2) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-muted small">{{ __('لا توجد مبالغ محجوزة حالياً.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== WALLET STATS GRID ===== --}}
<div class="wallet-stats-grid">
    <div class="w-stat-card deposits">
        <div class="w-stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="w-stat-info">
            <span class="w-stat-label">{{ __('Total Deposits') }}</span>
            <span class="w-stat-value">{{ number_format($wallet->total_deposits, 2) }}</span>
        </div>
    </div>
    <div class="w-stat-card withdrawals">
        <div class="w-stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
        </div>
        <div class="w-stat-info">
            <span class="w-stat-label">{{ __('Total Withdrawals') }}</span>
            <span class="w-stat-value">{{ number_format($wallet->total_withdrawals, 2) }}</span>
        </div>
    </div>
    <div class="w-stat-card debt">
        <div class="w-stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div class="w-stat-info">
            <span class="w-stat-label">{{ __('Debt Ceiling') }}</span>
            <span class="w-stat-value">{{ number_format($wallet->debt_ceiling, 2) }}</span>
        </div>
        @if($wallet->debt_ceiling > 0)
        <div class="debt-usage-bar">
            <div class="debt-usage-fill" style="width: {{ $wallet->debt_usage }}%"></div>
        </div>
        <span class="debt-usage-label">{{ $wallet->debt_usage }}% {{ __('used') }}</span>
        @endif
    </div>
    <div class="w-stat-card actions-card">
        <div class="action-buttons-group">
            <button class="wallet-action-btn deposit-btn" onclick="openDepositModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                {{ __('Deposit') }}
            </button>
            <button class="wallet-action-btn withdraw-btn" onclick="openWithdrawalModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                {{ __('Withdraw') }}
            </button>
        </div>
        <a href="{{ route('bidder.bank-details.index') }}" class="wallet-action-btn bank-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/></svg>
            {{ __('Bank Details') }}
        </a>
    </div>
</div>

{{-- ===== MAIN CONTENT GRID ===== --}}
<div class="wallet-content-grid">

    {{-- LEFT: TRANSACTIONS --}}
    <div class="wallet-content-main">
        <div class="premium-card">
            <div class="premium-card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    {{ __('Transaction History') }}
                </h2>
                <div class="transaction-filters">
                    <select id="txTypeFilter" class="tx-filter-select" name="type">
                        <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="credit" {{ ($type ?? '') === 'credit' ? 'selected' : '' }}>{{ __('Deposits') }}</option>
                        <option value="debit" {{ ($type ?? '') === 'debit' ? 'selected' : '' }}>{{ __('Withdrawals') }}</option>
                    </select>
                </div>
            </div>
            <div id="transactions-container">
                @include('bidder.wallet.partials.transactions-list')
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="wallet-content-side">

        {{-- Spending Analytics (Donut Chart) --}}
        <div class="premium-card chart-card">
            <div class="premium-card-header" style="border: none; padding-bottom: 0.5rem;">
                <h2 style="font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                    {{ __('تحليل المحفظة') }}
                </h2>
            </div>
            
            @php
                $avBal = $wallet->available_balance;
                $frBal = $wallet->frozen_balance;
                $dbUsed = ($wallet->debt_ceiling * $wallet->debt_usage) / 100;
                
                $totalFunds = $avBal + $frBal + $dbUsed;
                
                if ($totalFunds > 0) {
                    $avPct = round(($avBal / $totalFunds) * 100);
                    $frPct = round(($frBal / $totalFunds) * 100);
                    $dbPct = round(($dbUsed / $totalFunds) * 100);
                    
                    $degAv = ($avPct / 100) * 360;
                    $degFr = ($frPct / 100) * 360;
                    $degDb = ($dbPct / 100) * 360;
                    
                    $grad = "#10b981 0deg {$degAv}deg, #eab308 {$degAv}deg " . ($degAv + $degFr) . "deg, #ef4444 " . ($degAv + $degFr) . "deg 360deg";
                } else {
                    $avPct = $frPct = $dbPct = 0;
                    $grad = "#e5e7eb 0deg 360deg";
                }
            @endphp
            
            <div style="display: flex; flex-direction: column; align-items: center; padding: 10px 0;">
                <div style="
                    width: 150px; 
                    height: 150px; 
                    border-radius: 50%; 
                    background: conic-gradient({!! $grad !!});
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                ">
                    <div style="
                        width: 110px; 
                        height: 110px; 
                        background: var(--bg-card, #fff); 
                        border-radius: 50%;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
                    ">
                        <span style="font-size: 0.7rem; color: var(--text-muted, #888);">{{ __('إجمالي الأصول') }}</span>
                        <span style="font-weight: 800; font-size: 1.1rem; color: var(--text-main, #333);">{{ number_format($totalFunds, 0) }}</span>
                    </div>
                </div>
                
                <div style="width: 100%; margin-top: 25px; display: flex; flex-direction: column; gap: 12px; padding: 0 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <span style="display: flex; align-items: center; gap: 8px; color: var(--text-muted, #555); font-weight: 500;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,0.4);"></span>
                            {{ __('متاح للمزايدة') }}
                        </span>
                        <span style="font-weight: 700; color: var(--text-main, #333);">{{ $avPct }}%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <span style="display: flex; align-items: center; gap: 8px; color: var(--text-muted, #555); font-weight: 500;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #eab308; box-shadow: 0 0 5px rgba(234,179,8,0.4);"></span>
                            {{ __('مبالغ محجوزة') }}
                        </span>
                        <span style="font-weight: 700; color: var(--text-main, #333);">{{ $frPct }}%</span>
                    </div>
                    @if($wallet->debt_ceiling > 0)
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <span style="display: flex; align-items: center; gap: 8px; color: var(--text-muted, #555); font-weight: 500;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 5px rgba(239,68,68,0.4);"></span>
                            {{ __('ديون مستحقة') }}
                        </span>
                        <span style="font-weight: 700; color: var(--text-main, #333);">{{ $dbPct }}%</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Monthly Chart --}}
        <div class="premium-card chart-card">
            <div class="premium-card-header" style="border: none; padding-bottom: 0.5rem;">
                <h2 style="font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    {{ __('Monthly Overview') }}
                </h2>
            </div>
            <div class="mini-chart" id="miniChart">
                @foreach($monthlyStats as $stat)
                <div class="chart-bar-group">
                    <div class="chart-bars">
                        @php
                            $maxVal = max(collect($monthlyStats)->max('deposits'), collect($monthlyStats)->max('withdrawals'), 1);
                            $depHeight = ($stat['deposits'] / $maxVal) * 100;
                            $witHeight = ($stat['withdrawals'] / $maxVal) * 100;
                        @endphp
                        <div class="chart-bar deposit" style="height: {{ max($depHeight, 4) }}%" title="{{ __('Deposits') }}: {{ number_format($stat['deposits'], 2) }}"></div>
                        <div class="chart-bar withdrawal" style="height: {{ max($witHeight, 4) }}%" title="{{ __('Withdrawals') }}: {{ number_format($stat['withdrawals'], 2) }}"></div>
                    </div>
                    <span class="chart-label">{{ $stat['month'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot deposit"></span> {{ __('Deposits') }}</span>
                <span class="legend-item"><span class="legend-dot withdrawal"></span> {{ __('Withdrawals') }}</span>
            </div>
        </div>

        {{-- Withdrawal Requests --}}
        <div class="premium-card withdrawals-card">
            <div class="premium-card-header" style="border: none; padding-bottom: 0.5rem;">
                <h2 style="font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    {{ __('Withdrawal Requests') }}
                </h2>
            </div>
            <div class="withdrawal-list">
                @forelse($withdrawals as $wd)
                <div class="wd-item">
                    <div class="wd-info">
                        <span class="wd-amount text-danger">- {{ number_format($wd->requested_amount, 2) }}</span>
                        <span class="wd-date">{{ $wd->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <span class="wd-status {{ $wd->status }}">
                        @if($wd->status === 'approved') {{ __('Approved') }}
                        @elseif($wd->status === 'pending') {{ __('Pending') }}
                        @elseif($wd->status === 'rejected') {{ __('Rejected') }}
                        @else {{ $wd->status }}
                        @endif
                    </span>
                </div>
                @empty
                <div class="empty-wd">
                    <p>{{ __('No withdrawals.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Deposit Requests --}}
        <div class="premium-card withdrawals-card">
            <div class="premium-card-header" style="border: none; padding-bottom: 0.5rem;">
                <h2 style="font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    {{ __('Deposit Proofs') }}
                </h2>
            </div>
            <div class="withdrawal-list">
                @forelse($deposits as $dp)
                <div class="wd-item">
                    <div class="wd-info">
                        <span class="wd-amount text-success">+ {{ number_format($dp->amount, 2) }}</span>
                        <span class="wd-date">{{ $dp->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <span class="wd-status {{ $dp->status }}">
                        @if($dp->status === 'approved') {{ __('Approved') }}
                        @elseif($dp->status === 'pending') {{ __('Pending') }}
                        @elseif($dp->status === 'rejected') {{ __('Rejected') }}
                        @else {{ $dp->status }}
                        @endif
                    </span>
                </div>
                @empty
                <div class="empty-wd">
                    <p>{{ __('No deposit proofs.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Info Card --}}
        <div class="premium-card info-card">
            <div class="info-card-content">
                <div class="info-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </div>
                <h3>{{ __('Need Help?') }}</h3>
                <p>{{ __('Contact support for any wallet-related inquiries or issues.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ===== WITHDRAWAL MODAL (Admin Style) ===== --}}
<div class="modal fade" id="withdrawalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:8px;background:rgba(239,68,68,.12);display:inline-flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    </span>
                    {{ __('طلب سحب') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="withdrawalForm">
                @csrf
                <div class="modal-body p-4">
                    {{-- Balance Info --}}
                    <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-3" style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);">
                        <span class="fw-bold small text-muted">{{ __('الرصيد المتاح للسحب') }}</span>
                        <span style="font-family:'Orbitron',monospace;font-size:1.2rem;font-weight:800;color:#10b981;" id="availableBalanceText" data-amount="{{ $wallet->available_balance }}">{{ number_format($wallet->available_balance, 2) }} {{ __('SAR') }}</span>
                    </div>

                    {{-- Amount --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('المبلغ المراد سحبه') }}</label>
                        <div class="input-group mb-2">
                            <input type="number" name="amount" id="withdrawalAmountInput" class="form-control form-control-lg px-3" step="0.01" min="1" max="{{ $wallet->available_balance }}" required placeholder="0.00" style="border-radius:8px 0 0 8px;">
                            <span class="input-group-text fw-bold" style="border-radius:0 8px 8px 0;background:var(--bg-input);color:var(--text-muted);border-color:var(--border);">{{ __('ر.س') }}</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setWithdrawalAmount({{ $wallet->available_balance * 0.25 }})">25%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setWithdrawalAmount({{ $wallet->available_balance * 0.50 }})">50%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setWithdrawalAmount({{ $wallet->available_balance * 0.75 }})">75%</button>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold" onclick="setWithdrawalAmount({{ $wallet->available_balance }})">{{ __('الكل 100%') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setWithdrawalAmount(1000)">1,000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="setWithdrawalAmount(5000)">5,000</button>
                        </div>
                    </div>

                    {{-- Saved Bank Account --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('حساب الاستلام المعتمد') }}</label>
                        @if($user->iban && $user->bank_name)
                            <div class="p-3 rounded-3 d-flex align-items-center gap-3 mb-2" style="border:1px solid #10b981; background: rgba(16,185,129,0.03);">
                                <div style="width:40px;height:40px;border-radius:8px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:#10b981;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                </div>
                                <div class="flex-1" style="flex:1;">
                                    <div class="fw-bold" style="font-size:0.9rem;">{{ $user->bank_name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $user->beneficiary_name }}</div>
                                    <div style="font-family:monospace; font-size:0.8rem; letter-spacing:0.5px; margin-top:2px; direction: ltr; text-align: left;">{{ $user->iban }}</div>
                                </div>
                                <div>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" value="bank_transfer">
                        @else
                            <div class="p-3 rounded-3 text-center" style="border:1px dashed #ef4444; background: rgba(239,68,68,0.03);">
                                <p class="text-danger small fw-bold mb-2">{{ __('لا يوجد حساب بنكي محفوظ!') }}</p>
                                <a href="{{ route('bidder.bank-details.index') }}" class="btn btn-sm btn-danger">{{ __('إضافة حساب بنكي الآن') }}</a>
                            </div>
                            <input type="hidden" name="payment_method" value="">
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">{{ __('ملاحظات (اختياري)') }}</label>
                        <textarea name="notes" class="form-control px-3 py-2" rows="1" placeholder="{{ __('أي ملاحظات إضافية...') }}" style="border-radius:8px;"></textarea>
                    </div>

                    {{-- Summary & ETA --}}
                    <div class="p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e9ecef; font-size:0.85rem;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('رسوم التحويل:') }}</span>
                            <span class="fw-bold text-success">{{ __('مجاناً') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom:1px dashed #dee2e6;">
                            <span class="text-muted">{{ __('المبلغ الذي سيصلك:') }}</span>
                            <span class="fw-bold text-dark" id="netWithdrawalAmount">0.00 {{ __('SAR') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted mt-2" style="font-size:0.8rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ __('الوقت المتوقع لوصول الحوالة: 1 إلى 3 أيام عمل') }}
                        </div>
                    </div>
                    
                    <script>
                        function setWithdrawalAmount(amount) {
                            const input = document.getElementById('withdrawalAmountInput');
                            if(input) {
                                input.value = parseFloat(amount).toFixed(2);
                                // trigger input event to update summary
                                input.dispatchEvent(new Event('input'));
                            }
                        }
                        
                        document.addEventListener('DOMContentLoaded', function() {
                            const input = document.getElementById('withdrawalAmountInput');
                            const netDisplay = document.getElementById('netWithdrawalAmount');
                            if(input && netDisplay) {
                                input.addEventListener('input', function() {
                                    const val = parseFloat(this.value) || 0;
                                    netDisplay.textContent = val.toFixed(2) + ' SAR';
                                });
                            }
                        });
                    </script>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-start gap-2">
                    <button type="submit" id="withdrawSubmitBtn" class="btn px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border-radius:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                        {{ __('إرسال الطلب') }}
                    </button>
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius:8px;">{{ __('إلغاء') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== DEPOSIT MODAL (Admin Style) ===== --}}
<div class="modal fade" id="depositModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:8px;background:rgba(16,185,129,.12);display:inline-flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                    </span>
                    {{ __('إيداع رصيد') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="depositForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Left: Bank Accounts --}}
                        <div class="col-md-6">
                            <p class="fw-bold small text-muted mb-3">
                                <span class="badge rounded-pill me-1" style="background:rgba(59,130,246,.12);color:#3b82f6;">1</span>
                                {{ __('اختر الحساب البنكي وقم بالتحويل') }}
                            </p>
                            <div class="d-flex flex-column gap-2">
                                @forelse($platformBanks as $bank)
                                <label class="deposit-bank-option" style="cursor:pointer;">
                                    <input type="radio" name="bank_account_id" value="{{ $bank->id }}" class="d-none bank-radio" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="bank-card p-3 rounded-3 d-flex align-items-center gap-3" style="border:2px solid var(--border);transition:all .2s;">
                                        @if($bank->logo_path)
                                            <img src="{{ asset('storage/'.$bank->logo_path) }}" alt="" width="36" height="36" style="object-fit:contain;border-radius:6px;">
                                        @else
                                            <div style="width:36px;height:36px;border-radius:6px;background:var(--bg-hover);display:flex;align-items:center;justify-content:center;">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/></svg>
                                            </div>
                                        @endif
                                        <div class="flex-1" style="flex:1; max-width: calc(100% - 90px);">
                                            <div class="fw-bold mb-2" style="font-size:.9rem;">{{ $bank->bank_name }}</div>
                                            
                                            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:.75rem; color:var(--text-muted); background: rgba(0,0,0,0.02); padding: 4px 8px; border-radius: 4px;">
                                                <span class="text-truncate" style="max-width: 140px;" title="{{ $bank->beneficiary_name }}">{{ __('Beneficiary') }}: {{ $bank->beneficiary_name }}</span>
                                                <button type="button" class="btn btn-sm p-0 m-0 text-secondary copy-btn" onclick="copyToClipboard('{{ $bank->beneficiary_name }}', this, event)" title="{{ __('نسخ') }}" style="line-height:1;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                </button>
                                            </div>

                                            @if($bank->account_number)
                                            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:.75rem; color:var(--text-muted); background: rgba(0,0,0,0.02); padding: 4px 8px; border-radius: 4px;">
                                                <span class="text-truncate" style="font-family:monospace; font-size:0.8rem; letter-spacing:0.5px;" dir="ltr">{{ $bank->account_number }}</span>
                                                <button type="button" class="btn btn-sm p-0 m-0 text-secondary copy-btn" onclick="copyToClipboard('{{ $bank->account_number }}', this, event)" title="{{ __('نسخ') }}" style="line-height:1;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                </button>
                                            </div>
                                            @endif

                                            <div class="d-flex align-items-center justify-content-between" style="font-size:.75rem; color:var(--text-muted); background: rgba(16,185,129,0.05); padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(16,185,129,0.1);">
                                                <span class="text-truncate fw-bold" style="font-family:monospace; font-size:0.75rem; letter-spacing:0.5px; color:#10b981;" dir="ltr">{{ $bank->iban }}</span>
                                                <button type="button" class="btn btn-sm p-0 m-0 copy-btn" style="color:#10b981; line-height:1;" onclick="copyToClipboard('{{ $bank->iban }}', this, event)" title="{{ __('نسخ') }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bank-check-icon" style="width:20px;height:20px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    </div>
                                </label>
                                @empty
                                <div class="text-center py-3 text-muted"><small>{{ __('لا توجد حسابات بنكية متاحة.') }}</small></div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right: Proof Form --}}
                        <div class="col-md-6">
                            <p class="fw-bold small text-muted mb-3">
                                <span class="badge rounded-pill me-1" style="background:rgba(59,130,246,.12);color:#3b82f6;">2</span>
                                {{ __('أرسل إثبات التحويل') }}
                            </p>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted mb-2">* {{ __('المبلغ المحوّل') }}</label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control form-control-lg px-3" step="0.01" min="1" required placeholder="0.00" style="border-radius:8px 0 0 8px;">
                                    <span class="input-group-text fw-bold" style="border-radius:0 8px 8px 0;background:var(--bg-input);color:var(--text-muted);border-color:var(--border);">{{ __('ر.س') }}</span>
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label fw-bold small text-muted mb-2">* {{ __('رفع وصل التحويل') }}</label>
                                <div id="receiptDropzone" class="receipt-dropzone" style="border:2px dashed var(--border);border-radius:12px;padding:1.5rem;text-align:center;cursor:pointer;transition:all .3s;position:relative;">
                                    <input type="file" name="receipt" id="receiptInput" accept="image/*,.pdf" required style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                                    <div id="receiptPlaceholder">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--text-muted);margin-bottom:.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <p style="font-size:.8rem;color:var(--text-muted);margin:0;">{{ __('اسحب الملف هنا أو انقر للاختيار') }}</p>
                                        <p style="font-size:.7rem;color:var(--text-secondary);margin:.25rem 0 0;">JPEG, PNG, WebP, PDF — Max 5MB</p>
                                    </div>
                                    <div id="receiptPreview" style="display:none;">
                                        <img id="receiptImg" src="" style="max-height:80px;border-radius:6px;margin-bottom:.5rem;">
                                        <p id="receiptName" style="font-size:.75rem;color:#10b981;font-weight:600;margin:0;"></p>
                                    </div>
                                </div>
                                <div class="form-text small mt-1">ⓘ {{ __('يجب أن يكون الوصل واضحاً ويحتوي على المبلغ وبيانات التحويل') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-start gap-2">
                    <button type="submit" id="depositSubmitBtn" class="btn px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#10b981,#047857);border-radius:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                        {{ __('إرسال إثبات الإيداع') }}
                    </button>
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius:8px;">{{ __('إلغاء') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // One-Click Copy Function
    function copyToClipboard(text, btn, event) {
        if (event) event.preventDefault();
        
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = btn.innerHTML;
            const originalColor = btn.style.color;
            // Change to green checkmark
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            btn.style.color = '#10b981';
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.color = originalColor;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>
