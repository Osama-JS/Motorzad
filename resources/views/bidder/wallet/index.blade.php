@extends('layouts.bidder')

@section('title', __('My Wallet'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/wallet-profile.css') }}">
<style>
    .modal-backdrop {
        --bs-backdrop-zindex: 0 !important;
         background: var(--bg-body) !important;
    }
    #depositModal .modal-content,#withdrawalModal .modal-content{
        background: var(--bg-body) !important;
    }
</style>
@endsection

@section('content')

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
            <span class="balance-label">{{ __('Total Balance') }}</span>
            <span class="balance-amount">{{ number_format($wallet->balance, 2) }}</span>
            <span class="balance-currency">{{ __('SAR') }}</span>
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
                    <select id="txTypeFilter" class="tx-filter-select" onchange="filterTransactions()">
                        <option value="all">{{ __('All') }}</option>
                        <option value="credit">{{ __('Deposits') }}</option>
                        <option value="debit">{{ __('Withdrawals') }}</option>
                    </select>
                </div>
            </div>
            <div class="transactions-list" id="transactionsList">
                @forelse($transactions as $tx)
                <div class="tx-item {{ $tx->type }}">
                    <div class="tx-icon {{ $tx->type }}">
                        @if($tx->type === 'credit')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                        @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                        @endif
                    </div>
                    <div class="tx-details">
                        <div class="tx-title">{{ $tx->type === 'credit' ? __('Deposit') : __('Withdrawal') }}</div>
                        <div class="tx-desc">{{ $tx->description ?: '---' }}</div>
                        <div class="tx-date">{{ $tx->created_at->translatedFormat('d M Y - h:i A') }}</div>
                    </div>
                    <div class="tx-amount {{ $tx->type }}">
                        <span>{{ $tx->type === 'credit' ? '+' : '-' }} {{ number_format($tx->amount, 2) }}</span>
                        <small>{{ __('SAR') }}</small>
                    </div>
                </div>
                @empty
                <div class="empty-transactions">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <p>{{ __('No transactions yet.') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="wallet-content-side">

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
                        <span class="fw-bold small text-muted">{{ __('الرصيد المتاح') }}</span>
                        <span style="font-family:'Orbitron',monospace;font-size:1.2rem;font-weight:800;color:#10b981;">{{ number_format($wallet->balance, 2) }} {{ __('SAR') }}</span>
                    </div>

                    {{-- Amount --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('المبلغ المراد سحبه') }}</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control form-control-lg px-3" step="0.01" min="1" max="{{ $wallet->balance }}" required placeholder="0.00" style="border-radius:8px 0 0 8px;">
                            <span class="input-group-text fw-bold" style="border-radius:0 8px 8px 0;background:var(--bg-input);color:var(--text-muted);border-color:var(--border);">{{ __('ر.س') }}</span>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('طريقة الاستلام') }}</label>
                        <select name="payment_method" class="form-select px-3 py-2" required style="border-radius:8px;">
                            <option value="bank_transfer">{{ __('تحويل بنكي (Bank Transfer)') }}</option>
                            <option value="wallet">{{ __('محفظة إلكترونية (E-Wallet)') }}</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-1">
                        <label class="form-label fw-bold small text-muted mb-2">{{ __('ملاحظات (اختياري)') }}</label>
                        <textarea name="notes" class="form-control px-3 py-2" rows="2" placeholder="{{ __('أي ملاحظات إضافية...') }}" style="border-radius:8px;"></textarea>
                    </div>
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
                                        <div class="flex-1">
                                            <div class="fw-bold" style="font-size:.9rem;">{{ $bank->bank_name }}</div>
                                            <div style="font-size:.75rem;color:var(--text-muted);">{{ __('Beneficiary') }}: {{ $bank->beneficiary_name }}</div>
                                            <div class="mt-1"><code style="font-size:.7rem;background:var(--bg-hover);padding:2px 6px;border-radius:4px;">{{ $bank->iban }}</code></div>
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

@endsection

@section('js')
<script>
function openWithdrawalModal() {
    new bootstrap.Modal(document.getElementById('withdrawalModal')).show();
}
function openDepositModal() {
    new bootstrap.Modal(document.getElementById('depositModal')).show();
}
function filterTransactions() {
    const type = document.getElementById('txTypeFilter').value;
    document.querySelectorAll('.tx-item').forEach(item => {
        item.style.display = (type === 'all' || item.classList.contains(type)) ? '' : 'none';
    });
}

// ===== Bank Card Selection Highlight =====
document.querySelectorAll('.bank-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.bank-card').forEach(card => {
            card.style.borderColor = 'var(--border)';
            card.style.background = '';
            card.querySelector('.bank-check-icon').style.background = '';
            card.querySelector('.bank-check-icon').style.borderColor = 'var(--border)';
        });
        const card = radio.nextElementSibling;
        card.style.borderColor = '#10b981';
        card.style.background = 'rgba(16,185,129,.04)';
        card.querySelector('.bank-check-icon').style.background = '#10b981';
        card.querySelector('.bank-check-icon').style.borderColor = '#10b981';
    });
    // Init first selected
    if (radio.checked) radio.dispatchEvent(new Event('change'));
});

// ===== Receipt File Preview =====
const receiptInput = document.getElementById('receiptInput');
if (receiptInput) {
    receiptInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        document.getElementById('receiptDropzone').style.borderColor = '#10b981';
        document.getElementById('receiptDropzone').style.background = 'rgba(16,185,129,.04)';
        if (file.type.startsWith('image/')) {
            document.getElementById('receiptImg').src = URL.createObjectURL(file);
            document.getElementById('receiptImg').style.display = 'block';
        } else {
            document.getElementById('receiptImg').style.display = 'none';
        }
        document.getElementById('receiptName').textContent = '✓ ' + file.name;
        document.getElementById('receiptPlaceholder').style.display = 'none';
        document.getElementById('receiptPreview').style.display = 'block';
    });
}

// ===== Drag & Drop on Dropzone =====
const dropzone = document.getElementById('receiptDropzone');
if (dropzone) {
    dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.style.borderColor = '#10b981'; });
    dropzone.addEventListener('dragleave', () => { dropzone.style.borderColor = 'var(--border)'; });
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        receiptInput.files = dt.files;
        receiptInput.dispatchEvent(new Event('change'));
    });
}

// ===== Withdrawal Form Submit =====
document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('withdrawSubmitBtn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83"/></svg> {{ __("جاري المعالجة...") }}';
    btn.disabled = true;

    fetch("{{ route('bidder.wallet.withdraw') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(this)))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            bootstrap.Modal.getInstance(document.getElementById('withdrawalModal')).hide();
            this.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            toastr.error(data.message || '{{ __("حدث خطأ") }}');
        }
    })
    .catch(() => toastr.error('{{ __("حدث خطأ غير متوقع.") }}'))
    .finally(() => { btn.innerHTML = originalHTML; btn.disabled = false; });
});

// ===== Deposit Form Submit =====
document.getElementById('depositForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('depositSubmitBtn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4"/></svg> {{ __("جاري الرفع...") }}';
    btn.disabled = true;

    fetch("{{ route('bidder.wallet.deposit') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            bootstrap.Modal.getInstance(document.getElementById('depositModal')).hide();
            this.reset();
            document.getElementById('receiptPreview').style.display = 'none';
            document.getElementById('receiptPlaceholder').style.display = 'block';
            setTimeout(() => location.reload(), 1500);
        } else {
            if (data.errors) Object.values(data.errors).forEach(err => toastr.error(err[0]));
            else toastr.error(data.message || '{{ __("حدث خطأ") }}');
        }
    })
    .catch(() => toastr.error('{{ __("حدث خطأ غير متوقع.") }}'))
    .finally(() => { btn.innerHTML = originalHTML; btn.disabled = false; });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

@endsection
