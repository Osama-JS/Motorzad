@extends('layouts.admin')

@section('title', __('إدارة طلبات البائعين'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .seller-req-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .badge-req-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .badge-req-approved {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .badge-req-rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .user-avatar-req {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--brand-red, #e53e3e), #b91c1c);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }

    .quick-status-tab {
        padding: 8px 18px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        color: var(--text-muted);
        border: 1px solid transparent;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .quick-status-tab:hover {
        background: rgba(0, 0, 0, 0.03);
        color: var(--text);
    }
    .quick-status-tab.active {
        background: var(--brand-red, #e53e3e);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.25);
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ __('طلبات ترقية البائعين') }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <span>{{ __('طلبات البائعين') }}</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-weight-bold shadow-sm">
            <i class="fa-solid fa-hourglass-half me-1"></i> بانتظار المراجعة: {{ $stats['pending'] }}
        </span>
    </div>
</div>

{{-- 1. Statistics Cards --}}
<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('إجمالي الطلبات') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">{{ __('طلبات قيد المراجعة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">{{ __('طلبات مقبولة ومفعلة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">{{ __('طلبات مرفوضة') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Search & Filters Card --}}
<div class="card mb-4 shadow-sm border-0 rounded-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.seller-requests.index') }}" id="filterForm">
            <div class="row g-3 align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('بحث بالاسم، البريد أو رقم الجوال...') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <select name="status" class="form-select select2-init">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('جميع الحالات') }}</option>
                        <option value="pending" {{ request('status', 'all') == 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار (معلقة)') }}</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('مقبولة') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('مرفوضة') }}</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fa-regular fa-calendar"></i>
                        </span>
                        <input type="text" name="date_from" class="form-control custom-datepicker border-start-0 ps-0" placeholder="{{ __('من تاريخ') }}" value="{{ request('date_from') }}">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fa-regular fa-calendar"></i>
                        </span>
                        <input type="text" name="date_to" class="form-control custom-datepicker border-start-0 ps-0" placeholder="{{ __('إلى تاريخ') }}" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-bold rounded-pill">
                        <i class="fa-solid fa-filter me-1"></i> {{ __('فلترة') }}
                    </button>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.seller-requests.index') }}" class="btn btn-light rounded-pill px-3" title="{{ __('إعادة ضبط') }}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Quick Status Filter Pills --}}
<div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
    <span class="text-muted small fw-bold me-1">{{ __('عرض سريع:') }}</span>
    <a href="{{ route('admin.seller-requests.index') }}" class="quick-status-tab {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
        <span>الكل</span>
        <span class="badge bg-white bg-opacity-25 rounded-pill">{{ $stats['total'] }}</span>
    </a>
    <a href="{{ route('admin.seller-requests.index', ['status' => 'pending']) }}" class="quick-status-tab {{ request('status') == 'pending' ? 'active' : '' }}">
        <i class="fa-solid fa-clock"></i>
        <span>قيد المراجعة</span>
        <span class="badge bg-white bg-opacity-25 rounded-pill">{{ $stats['pending'] }}</span>
    </a>
    <a href="{{ route('admin.seller-requests.index', ['status' => 'approved']) }}" class="quick-status-tab {{ request('status') == 'approved' ? 'active' : '' }}">
        <i class="fa-solid fa-check"></i>
        <span>مقبول</span>
        <span class="badge bg-white bg-opacity-25 rounded-pill">{{ $stats['approved'] }}</span>
    </a>
    <a href="{{ route('admin.seller-requests.index', ['status' => 'rejected']) }}" class="quick-status-tab {{ request('status') == 'rejected' ? 'active' : '' }}">
        <i class="fa-solid fa-xmark"></i>
        <span>مرفوض</span>
        <span class="badge bg-white bg-opacity-25 rounded-pill">{{ $stats['rejected'] }}</span>
    </a>
</div>

{{-- 3. Requests Table Card --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold fs-6 text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-list-check text-primary"></i>
            <span>{{ __('قائمة طلبات الترقية') }}</span>
        </h6>
        <span class="text-muted small">{{ __('العدد الحالي:') }} <strong>{{ $requests->total() }}</strong></span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted">
                <tr>
                    <th class="px-4 py-3">{{ __('مقدم الطلب (المستخدم)') }}</th>
                    <th class="py-3">{{ __('تاريخ التقديم') }}</th>
                    <th class="py-3">{{ __('الحالة') }}</th>
                    <th class="py-3">{{ __('توثيق الهوية (KYC)') }}</th>
                    <th class="py-3">{{ __('ملاحظات الإدارة') }}</th>
                    <th class="px-4 py-3 text-end">{{ __('الإجراءات والعمليات') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                @if($req->user->profile_photo)
                                    <img src="{{ asset('storage/' . $req->user->profile_photo) }}" class="user-avatar-req" alt="{{ $req->user->full_name }}">
                                @else
                                    <div class="user-avatar-req">
                                        {{ mb_substr($req->user->first_name ?: $req->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $req->user->full_name }}</div>
                                    <div class="text-muted small">
                                        <i class="fa-solid fa-envelope me-1 text-secondary"></i>{{ $req->user->email }}
                                    </div>
                                    @if($req->user->phone)
                                        <div class="text-muted small font-monospace">
                                            <i class="fa-solid fa-phone me-1 text-secondary"></i>{{ $req->user->phone }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $req->created_at->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                        </td>

                        <td class="py-3">
                            @if($req->status === 'pending')
                                <span class="badge badge-req-pending px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-clock me-1"></i> {{ __('قيد المراجعة') }}
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="badge badge-req-approved px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('مقبول (بائع معتمد)') }}
                                </span>
                            @else
                                <span class="badge badge-req-rejected px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> {{ __('مرفوض') }}
                                </span>
                            @endif
                        </td>

                        <td class="py-3">
                            @if($req->user->identity_verified_at)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">
                                    <i class="fa-solid fa-shield-halved me-1"></i> موثق
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-2 py-1 small">
                                    <i class="fa-solid fa-shield-blank me-1"></i> غير موثق
                                </span>
                            @endif
                        </td>

                        <td class="py-3">
                            @if($req->admin_notes)
                                <span class="text-dark small d-inline-block text-truncate" style="max-width: 180px;" title="{{ $req->admin_notes }}">
                                    <i class="fa-regular fa-comment-dots text-secondary me-1"></i>{{ $req->admin_notes }}
                                </span>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-end">
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                {{-- View Details Button --}}
                                <button type="button" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 px-3" onclick="openDetailsModal({{ $req->id }})" title="{{ __('عرض التفاصيل') }}">
                                    <i class="fa-solid fa-eye text-primary"></i>
                                    <span>{{ __('تفاصيل') }}</span>
                                </button>

                                @if($req->status === 'pending')
                                    {{-- Approve Button --}}
                                    <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-3" onclick="openApproveModal({{ $req->id }}, '{{ addslashes($req->user->full_name) }}')">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('قبول') }}</span>
                                    </button>

                                    {{-- Reject Button --}}
                                    <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1 px-3" onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->user->full_name) }}')">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>{{ __('رفض') }}</span>
                                    </button>
                                @else
                                    <span class="btn btn-sm btn-light disabled text-muted px-3">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted mb-3"><i class="fa-solid fa-inbox fa-3x opacity-50"></i></div>
                            <h5 class="fw-bold">{{ __('لا توجد طلبات بائعين مطابقة') }}</h5>
                            <p class="text-muted mb-0">{{ __('لم يتم العثور على طلبات ترقية بائعين وفق الفلتر المحدد.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="px-4 py-3 border-top bg-white d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                {{ __('عرض') }} {{ $requests->firstItem() }} {{ __('إلى') }} {{ $requests->lastItem() }} {{ __('من أصل') }} {{ $requests->total() }} {{ __('طلب') }}
            </div>
            <div>
                {{ $requests->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('modals')
{{-- ── Modals ───────────────────────────────────────────────────────────── --}}

{{-- 1. Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2" id="detailsModalLabel">
                    <i class="fa-solid fa-id-card text-primary"></i>
                    <span>{{ __('تفاصيل طلب الترقية للمستخدم') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailsModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('إغلاق') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="approveForm" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2" id="approveModalLabel">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ __('الموافقة على طلب البائع وترقية الحساب') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 rounded-3 mb-3">
                    <p class="mb-0 small text-success fw-semibold">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        {{ __('سيتم منح المستخدم صلاحيات البائع (Seller) تلقائياً، وإرسال إشعار فوري له عبر المنصة.') }}
                    </p>
                </div>
                <p class="mb-3">{{ __('هل تود تأكيد الموافقة على ترقية المستخدم:') }} <strong id="approve_user_name" class="text-dark"></strong>؟</p>
                <div class="mb-0">
                    <label class="form-label fw-bold small text-muted">{{ __('ملاحظات إضافية للمستخدم (اختياري)') }}</label>
                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="{{ __('ملاحظات أو تعليمات خاصة تظهر للمستخدم...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-ghost px-4" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-check me-1"></i> {{ __('تأكيد الموافقة') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="rejectForm" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2" id="rejectModalLabel">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>{{ __('رفض طلب ترقية البائع') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">{{ __('أنت على وشك رفض طلب الترقية للمستخدم:') }} <strong id="reject_user_name" class="text-dark"></strong></p>
                <div class="mb-0">
                    <label class="form-label fw-bold">{{ __('سبب الرفض (مطلوب)') }} <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" class="form-control" rows="3" required placeholder="{{ __('اكتب سبب الرفض هنا بوضوح ليتم إرساله للمستخدم...') }}"></textarea>
                    <small class="text-muted mt-1 d-block">{{ __('سيصل هذا السبب إلى المستخدم عبر إشعار رسمي.') }}</small>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-ghost px-4" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-xmark me-1"></i> {{ __('تأكيد الرفض') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    // Open Approve Modal
    function openApproveModal(id, name) {
        document.getElementById('approve_user_name').innerText = name;
        document.getElementById('approveForm').action = "{{ url('admin/seller-requests') }}/" + id + "/approve";
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('approveModal'));
        modal.show();
    }

    // Open Reject Modal
    function openRejectModal(id, name) {
        document.getElementById('reject_user_name').innerText = name;
        document.getElementById('rejectForm').action = "{{ url('admin/seller-requests') }}/" + id + "/reject";
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectModal'));
        modal.show();
    }

    // Open Details Modal with AJAX
    function openDetailsModal(id) {
        const bodyEl = document.getElementById('detailsModalBody');
        bodyEl.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailsModal'));
        modal.show();

        fetch("{{ url('admin/seller-requests') }}/" + id)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const req = data.request;
                    const u = req.user;
                    let statusBadge = '';
                    if (req.status === 'pending') {
                        statusBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">قيد المراجعة</span>';
                    } else if (req.status === 'approved') {
                        statusBadge = '<span class="badge bg-success px-3 py-1 rounded-pill">مقبول</span>';
                    } else {
                        statusBadge = '<span class="badge bg-danger px-3 py-1 rounded-pill">مرفوض</span>';
                    }

                    bodyEl.innerHTML = `
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                            <img src="${u.profile_photo_url}" class="user-avatar-req" style="width: 54px; height: 54px;" alt="${u.name}">
                            <div>
                                <h5 class="fw-bold mb-1">${u.name}</h5>
                                <div class="text-muted small">${u.email}</div>
                                <div class="text-muted small font-monospace">${u.phone || 'لا يوجد هاتف'}</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-muted d-block mb-1">حالة الطلب</small>
                                    <div>${statusBadge}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-muted d-block mb-1">رصيد المحفظة</small>
                                    <div class="fw-bold text-primary font-monospace">${u.balance} SAR</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-muted d-block mb-1">توثيق الهوية</small>
                                    <div class="fw-semibold">${u.identity_verified ? '<span class="text-success"><i class="fa-solid fa-check-circle"></i> موثق</span>' : '<span class="text-muted">غير موثق</span>'}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-muted d-block mb-1">المدينة</small>
                                    <div class="fw-semibold">${u.city}</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border rounded-3 bg-white mb-2">
                            <small class="text-muted d-block mb-1">ملاحظات الإدارة المسجلة:</small>
                            <div class="text-dark small">${req.admin_notes || 'لا توجد ملاحظات مسجلة.'}</div>
                        </div>
                        
                        <div class="text-muted small text-end mt-3">
                            تاريخ تقديم الطلب: ${req.created_at}
                        </div>
                    `;
                } else {
                    bodyEl.innerHTML = '<div class="alert alert-danger mb-0">تعذر جلب تفاصيل الطلب.</div>';
                }
            })
            .catch(err => {
                bodyEl.innerHTML = '<div class="alert alert-danger mb-0">حدث خطأ أثناء الاتصال بالخادم.</div>';
            });
    }

    $(document).ready(function() {
        // Initialize Select2
        let dir = $('html').attr('dir') || 'rtl';
        if ($.fn.select2) {
            $('.select2-init').select2({
                dir: dir,
                width: '100%'
            });
        }

        // Initialize Custom Datepickers
        if (typeof flatpickr !== 'undefined') {
            $('.custom-datepicker').flatpickr({
                locale: "ar",
                dateFormat: "Y-m-d",
                disableMobile: "true"
            });
        }
    });
</script>
@endsection
