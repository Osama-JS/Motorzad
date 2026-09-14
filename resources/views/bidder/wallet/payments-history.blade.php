@extends('layouts.bidder')

@section('title', __('سجل المدفوعات وشحن الرصيد'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/wallet-profile.css') }}">
<style>
    .payments-hero {
        background: linear-gradient(135deg, rgba(229, 62, 62, 0.06), rgba(16, 185, 129, 0.06));
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.75rem;
        margin-bottom: 2rem;
    }
    .payments-tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--bg-hover);
        padding: 0.35rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .payments-tab-btn {
        flex: 1;
        min-width: 140px;
        padding: 0.65rem 1rem;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.85rem;
        text-align: center;
        transition: all 0.25s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .payments-tab-btn.active {
        background: var(--card-bg, #ffffff);
        color: var(--text-color, #1a202c);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .payments-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .payments-table th {
        background: var(--bg-hover);
        padding: 0.85rem 1rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
    }
    .payments-table td {
        padding: 1rem;
        font-size: 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-light);
    }
    .receipt-thumb {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 1px solid var(--border);
        transition: transform 0.2s;
    }
    .receipt-thumb:hover {
        transform: scale(1.08);
    }
    .badge-status {
        padding: 0.35rem 0.75rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .badge-approved { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .badge-pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .badge-rejected { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .brand-pill {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 800;
        background: var(--bg-hover);
        border: 1px solid var(--border);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Section --}}
    <div class="payments-hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                {{ __('سجل المدفوعات وعمليات الشحن') }}
            </h1>
            <p class="text-muted small mb-0">{{ __('استعراض كافة الحوالات البنكية المرفوعة والمدفوعات الإلكترونية عبر البوابة مع تفاصيل حالتها.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bidder.wallet.index') }}" class="btn btn-outline-secondary px-3 py-2 fw-bold d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ __('العودة للمحفظة') }}
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="premium-card p-3 h-100">
                <div class="text-muted small fw-bold mb-1">{{ __('إجمالي الشحن البنكي المعتمد') }}</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($stats['bank_transfers_approved_total'], 2) }} <small class="fs-6">SAR</small></div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ $stats['bank_transfers_count'] }} {{ __('طلب تحويل') }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="premium-card p-3 h-100">
                <div class="text-muted small fw-bold mb-1">{{ __('إجمالي الدفع الإلكتروني (البوابة)') }}</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($stats['online_payments_paid_total'], 2) }} <small class="fs-6">SAR</small></div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ $stats['online_payments_count'] }} {{ __('عملية دفع') }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="premium-card p-3 h-100">
                <div class="text-muted small fw-bold mb-1">{{ __('رصيد المحفظة المتاح') }}</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($user->wallet->available_balance ?? 0, 2) }} <small class="fs-6">SAR</small></div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ __('جاهز للمزايدة وسداد الالتزامات') }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="premium-card p-3 h-100">
                <div class="text-muted small fw-bold mb-1">{{ __('الرصيد المحجوز كضمانات') }}</div>
                <div class="fs-4 fw-bold text-warning">{{ number_format($user->wallet->held_balance ?? 0, 2) }} <small class="fs-6">SAR</small></div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ __('يُسترد تلقائياً فور انتهاء المزاد') }}</div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="payments-tabs">
        <a href="{{ route('bidder.wallet.payments-history', ['tab' => 'all']) }}" class="payments-tab-btn {{ $tab === 'all' ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            {{ __('جميع المدفوعات') }}
        </a>
        <a href="{{ route('bidder.wallet.payments-history', ['tab' => 'bank_transfers']) }}" class="payments-tab-btn {{ $tab === 'bank_transfers' ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 10h18"/><path d="M5 6l7-3 7 3"/><path d="M4 10v11"/><path d="M20 10v11"/><path d="M8 14v4"/><path d="M12 14v4"/><path d="M16 14v4"/></svg>
            {{ __('التحويلات البنكية') }} ({{ $bankTransfers->total() }})
        </a>
        <a href="{{ route('bidder.wallet.payments-history', ['tab' => 'electronic']) }}" class="payments-tab-btn {{ $tab === 'electronic' ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            {{ __('الدفع الإلكتروني (البوابة)') }} ({{ $onlinePayments->total() }})
        </a>
    </div>

    {{-- Tab Content 1: Bank Transfers --}}
    @if($tab === 'all' || $tab === 'bank_transfers')
    <div class="premium-card mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h3 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                {{ __('سجل التحويلات البنكية (إيداعات الضمان والشحن)') }}
            </h3>
        </div>
        <div class="table-responsive">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('البنك المحول إليه') }}</th>
                        <th>{{ __('المبلغ') }}</th>
                        <th>{{ __('إيصال التحويل') }}</th>
                        <th>{{ __('تاريخ الطلب') }}</th>
                        <th>{{ __('الحالة') }}</th>
                        <th>{{ __('ملاحظات الإدارة') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankTransfers as $index => $deposit)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $deposit->id }}</td>
                        <td>
                            <strong>{{ $deposit->bankAccount->bank_name ?? __('حساب منصة موتورزاد') }}</strong>
                            @if($deposit->bankAccount && $deposit->bankAccount->iban)
                                <br><small class="text-muted font-monospace" dir="ltr">{{ $deposit->bankAccount->iban }}</small>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success fs-6">+{{ number_format($deposit->amount, 2) }} SAR</strong>
                        </td>
                        <td>
                            @if($deposit->receipt_path)
                                <img src="{{ asset('storage/' . $deposit->receipt_path) }}" class="receipt-thumb" onclick="previewImage('{{ asset('storage/' . $deposit->receipt_path) }}')" title="{{ __('اضغط للتكبير') }}">
                            @else
                                <span class="text-muted small">{{ __('لا يوجد مرفق') }}</span>
                            @endif
                        </td>
                        <td>
                            <span dir="ltr">{{ $deposit->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td>
                            @if($deposit->status === 'approved')
                                <span class="badge-status badge-approved">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ __('تم الاعتماد وإيداع الرصيد') }}
                                </span>
                            @elseif($deposit->status === 'pending')
                                <span class="badge-status badge-pending">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ __('قيد المراجعة والتدقيق') }}
                                </span>
                            @elseif($deposit->status === 'rejected')
                                <span class="badge-status badge-rejected">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    {{ __('مرفوض') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($deposit->admin_note)
                                <span class="small text-danger fw-semibold">{{ $deposit->admin_note }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            {{ __('لم تقم برفع أي تحويلات بنكية حتى الآن.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bankTransfers->hasPages())
        <div class="p-3 border-top">
            {{ $bankTransfers->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Tab Content 2: Electronic Gateway Payments --}}
    @if($tab === 'all' || $tab === 'electronic')
    <div class="premium-card mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h3 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                {{ __('سجل المدفوعات الإلكترونية عبر البوابة (HyperPay)') }}
            </h3>
        </div>
        <div class="table-responsive">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('رقم العملية (Transaction ID)') }}</th>
                        <th>{{ __('طريقة الدفع') }}</th>
                        <th>{{ __('المبلغ المسدد') }}</th>
                        <th>{{ __('تاريخ العملية') }}</th>
                        <th>{{ __('حالة الدفع') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlinePayments as $tx)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $tx->id }}</td>
                        <td>
                            <span class="font-monospace small fw-bold" dir="ltr">{{ $tx->transaction_id ?? $tx->checkout_id }}</span>
                        </td>
                        <td>
                            <span class="brand-pill">{{ strtoupper($tx->brand ?? 'CARD') }}</span>
                        </td>
                        <td>
                            <strong class="text-primary fs-6">{{ number_format($tx->amount, 2) }} SAR</strong>
                        </td>
                        <td>
                            <span dir="ltr">{{ $tx->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td>
                            @if($tx->status === 'paid')
                                <span class="badge-status badge-approved">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ __('مدفوع بنجاح') }}
                                </span>
                            @elseif($tx->status === 'pending' || $tx->status === 'initiated')
                                <span class="badge-status badge-pending">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ __('قيد الانتظار') }}
                                </span>
                            @else
                                <span class="badge-status badge-rejected">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    {{ __($tx->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            {{ __('لا توجد أي مدفوعات إلكترونية سابقة.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($onlinePayments->hasPages())
        <div class="p-3 border-top">
            {{ $onlinePayments->links() }}
        </div>
        @endif
    </div>
    @endif

</div>

{{-- Receipt Preview Modal --}}
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background: var(--card-bg, #fff);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold fs-6">{{ __('إيصال التحويل البنكي') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <img id="receiptModalImg" src="" style="max-width: 100%; max-height: 500px; border-radius: 10px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function previewImage(src) {
    document.getElementById('receiptModalImg').src = src;
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
}
</script>
@endsection
