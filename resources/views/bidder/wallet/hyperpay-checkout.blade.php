@extends('layouts.app')

@section('title', __('إتمام الدفع الإلكتروني — موتورزاد'))

@section('content')
<div class="container py-5" style="max-width: 680px; margin: 0 auto;">
    {{-- Card Container --}}
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: var(--bg-card, #161c27); color: var(--text, #f0f4ff); border: 1px solid var(--border, rgba(255,255,255,0.08));">
        {{-- Header --}}
        <div class="p-4 border-bottom text-center position-relative" style="background: linear-gradient(135deg, rgba(229,62,62,0.12), rgba(30,41,59,0.5)); border-color: rgba(255,255,255,0.08) !important;">
            <a href="{{ route('bidder.wallet.index') }}" class="btn btn-sm btn-outline-secondary position-absolute start-0 ms-3 top-50 translate-middle-y" style="border-radius: 50px;">
                ← {{ __('إلغاء وعودة للمحفظة') }}
            </a>
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-2" style="background: rgba(229,62,62,0.15); width: 56px; height: 56px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <h3 class="fw-bold mb-1" style="font-size: 1.35rem;">{{ __('بوابة الدفع الآمن — هايبر باي') }}</h3>
            <p class="text-muted small mb-0">{{ __('شحن المحفظة برصيد فوري عبر البطاقة المصرفية') }}</p>
        </div>

        {{-- Order Summary Box --}}
        <div class="p-4 border-bottom" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.06) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">{{ __('رقم العملية المرجعي:') }}</span>
                <span class="font-monospace fw-semibold" style="color: #93c5fd;">{{ $transaction->merchant_transaction_id }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">{{ __('وسيلة الدفع المختارة:') }}</span>
                <span class="badge px-3 py-2" style="background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); font-size: 0.85rem;">
                    {{ $transaction->brand_name }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.06) !important;">
                <span class="fw-bold fs-5">{{ __('إجمالي المبلغ المستحق:') }}</span>
                <span class="fw-bold fs-4 text-success" style="font-family: inherit;">
                    {{ number_format($transaction->amount, 2) }} <small class="fs-6 text-muted">ر.س</small>
                </span>
            </div>
        </div>

        {{-- HyperPay Official Widget Area --}}
        <div class="p-4 p-md-5">
            <div id="hyperpay-widget-wrapper" class="py-2">
                <form action="{{ $returnUrl }}" class="paymentWidgets" data-brands="{{ $widgetBrands }}"></form>
            </div>
        </div>

        {{-- Footer Trust Badges --}}
        <div class="p-3 text-center border-top small" style="background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.06) !important; color: #8892a4;">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>{{ __('معاملة مشفرة وآمنة وفق معايير الحماية المصرفية العالمية (PCI-DSS 256-bit)') }}</span>
            </div>
            <span>{{ __('جميع المعاملات تخضع لبروتوكول التحقق الثنائي (3D Secure)') }}</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- HyperPay Payment Widgets Script --}}
<script src="{{ $scriptUrl }}"></script>
<style>
/* Style adjustments for HyperPay Widget embedded form */
.wpwl-container {
    max-width: 100% !important;
    margin: 0 auto !important;
    direction: ltr !important;
}
.wpwl-form {
    border-radius: 12px !important;
    background: transparent !important;
}
.wpwl-label {
    font-size: 0.88rem !important;
    font-weight: 600 !important;
}
.wpwl-control {
    border-radius: 8px !important;
    height: 44px !important;
    font-size: 1rem !important;
}
.wpwl-button-pay {
    border-radius: 8px !important;
    background: #e53e3e !important;
    border: none !important;
    height: 48px !important;
    font-size: 1.05rem !important;
    font-weight: 700 !important;
    transition: all 0.3s ease !important;
}
.wpwl-button-pay:hover {
    background: #c53030 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.4) !important;
}
</style>
@endsection
