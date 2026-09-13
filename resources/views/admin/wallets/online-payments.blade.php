@extends('layouts.admin')

@section('title', __('حركات الدفع الإلكتروني (هايبر باي)'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .brand-mada { background: rgba(34, 197, 94, 0.12); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.25); }
    .brand-visa_master { background: rgba(59, 130, 246, 0.12); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.25); }
    .brand-apple_pay { background: rgba(255, 255, 255, 0.1); color: var(--text); border: 1px solid var(--border); }

    .status-badge-hp {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-hp-paid { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-hp-initiated { background: rgba(59, 130, 246, 0.12); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25); }
    .status-hp-failed { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
</style>
@endsection

@section('content')
<x-admin-header :title="__('حركات الدفع الإلكتروني (هايبر باي)')" :breadcrumb="__('عمليات هايبر باي')">
    <a href="{{ route('admin.settings.index') }}#panel-hyperpay" class="btn-ultra">
        ⚙️ {{ __('إعدادات البوابة') }}
    </a>
</x-admin-header>

{{-- Stats Grid --}}
<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
                <div class="stat-label">{{ __('إجمالي محاولات الدفع') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_paid_amount'], 2) }} <small style="font-size:0.8rem;">ر.س</small></div>
                <div class="stat-label">{{ __('إجمالي المبالغ المحصلة بنجاح') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_paid_count']) }}</div>
                <div class="stat-label">{{ __('العمليات الناجحة والمودعة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_failed_count']) }}</div>
                <div class="stat-label">{{ __('عمليات ملغاة أو غير مكتملة') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters Card --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.wallets.online-payments') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control" placeholder="{{ __('بحث برقم المعاملة، الاسم، البريد، أو معرف هايبر باي...') }}" value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-control">
                    <option value="all">{{ __('جميع الحالات') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>✅ {{ __('ناجحة / مدفوعة') }}</option>
                    <option value="initiated" {{ request('status') == 'initiated' ? 'selected' : '' }}>⏳ {{ __('قيد المعالجة (Initiated)') }}</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ {{ __('فاشلة (Failed)') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="brand" class="form-control">
                    <option value="all">{{ __('جميع وسائل الدفع') }}</option>
                    <option value="mada" {{ request('brand') == 'mada' ? 'selected' : '' }}>مدى (Mada)</option>
                    <option value="visa_master" {{ request('brand') == 'visa_master' ? 'selected' : '' }}>Visa / MasterCard</option>
                    <option value="apple_pay" {{ request('brand') == 'apple_pay' ? 'selected' : '' }}>Apple Pay</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('تصفية') }}</button>
                @if(request()->hasAny(['search', 'status', 'brand']))
                    <a href="{{ route('admin.wallets.online-payments') }}" class="btn btn-outline-secondary">{{ __('إلغاء') }}</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Transactions Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('رقم العملية المرجعي') }}</th>
                        <th>{{ __('المستخدم') }}</th>
                        <th>{{ __('المبلغ') }}</th>
                        <th>{{ __('طريقة الدفع') }}</th>
                        <th>{{ __('معلومات البطاقة') }}</th>
                        <th>{{ __('الحالة') }}</th>
                        <th>{{ __('تاريخ العملية') }}</th>
                        <th>{{ __('التفاصيل') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>
                                <strong class="font-monospace" style="font-size:0.85rem;">{{ $tx->merchant_transaction_id }}</strong>
                                @if($tx->hyperpay_payment_id)
                                    <br><small class="text-muted font-monospace">HP: {{ $tx->hyperpay_payment_id }}</small>
                                @endif
                            </td>
                            <td>
                                @if($tx->user)
                                    <a href="{{ route('admin.users.show', $tx->user->id) }}"><strong>{{ $tx->user->name }}</strong></a>
                                    <br><small class="text-muted">{{ $tx->user->email }}</small>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success" style="font-size:1.05rem;">
                                    {{ number_format($tx->amount, 2) }}
                                </strong>
                                <small class="text-muted">{{ $tx->currency }}</small>
                            </td>
                            <td>
                                <span class="brand-badge brand-{{ $tx->brand }}">
                                    {{ $tx->brand_name }}
                                </span>
                                <br><small class="text-muted">{{ strtoupper($tx->channel) }}</small>
                            </td>
                            <td>
                                @if($tx->card_last4)
                                    <span class="font-monospace" dir="ltr">{{ $tx->masked_card }}</span>
                                    @if($tx->card_holder)
                                        <br><small class="text-muted">{{ $tx->card_holder }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>
                                @if($tx->status === 'paid')
                                    <span class="status-badge-hp status-hp-paid">
                                        <span class="badge-dot" style="width:7px;height:7px;border-radius:50%;background:#10b981;"></span>
                                        {{ __('مدفوعة بنجاح') }}
                                    </span>
                                @elseif($tx->status === 'initiated')
                                    <span class="status-badge-hp status-hp-initiated">
                                        <span class="badge-dot" style="width:7px;height:7px;border-radius:50%;background:#3b82f6;"></span>
                                        {{ __('قيد التنفيذ') }}
                                    </span>
                                @else
                                    <span class="status-badge-hp status-hp-failed">
                                        <span class="badge-dot" style="width:7px;height:7px;border-radius:50%;background:#ef4444;"></span>
                                        {{ __('فشلت العملية') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span>{{ $tx->created_at->format('Y-m-d H:i') }}</span>
                                @if($tx->paid_at)
                                    <br><small class="text-success">{{ __('سُددت:') }} {{ $tx->paid_at->diffForHumans() }}</small>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="showPayloadModal('{{ $tx->id }}')">
                                    👁️ {{ __('الرد التقني') }}
                                </button>
                                <textarea id="payload-{{ $tx->id }}" class="d-none">{{ json_encode($tx->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ __('لا توجد حركات دفع إلكتروني مسجلة حتى الآن.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-transparent py-3">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection

@section('modals')
{{-- Payload Details Modal --}}
<div class="modal fade" id="payloadModal" tabindex="-1" aria-labelledby="payloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold fs-5" id="payloadModalLabel">📄 {{ __('تفاصيل رد بوابة هايبر باي (Raw JSON Response)') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <pre id="modalJsonContent" class="p-3 font-monospace text-light bg-dark rounded-3" style="max-height:400px; overflow-y:auto; font-size:0.85rem;" dir="ltr"></pre>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('إغلاق') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function showPayloadModal(txId) {
    var raw = $('#payload-' + txId).val();
    try {
        var formatted = JSON.stringify(JSON.parse(raw), null, 2);
        $('#modalJsonContent').text(formatted);
    } catch (e) {
        $('#modalJsonContent').text(raw || 'No raw payload available.');
    }
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('payloadModal'));
    modal.show();
}
</script>
@endsection
