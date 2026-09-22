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
    .badge-req-under-review {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    .badge-req-action-required {
        background: rgba(249, 115, 22, 0.12);
        color: #ea580c;
        border: 1px solid rgba(249, 115, 22, 0.25);
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
<x-admin-header :title="__('طلبات ترقية البائعين')" :breadcrumb="__('طلبات البائعين')">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-weight-bold shadow-sm">
        <i class="fa-solid fa-hourglass-half me-1"></i> بانتظار المراجعة: {{ $stats['pending'] }}
    </span>
</x-admin-header>

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
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>{{ __('جاري المراجعة') }}</option>
                        <option value="action_required" {{ request('status') == 'action_required' ? 'selected' : '' }}>{{ __('مطلوب إجراء') }}</option>
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
        <span class="badge bg-white bg-opacity-25 rounded-pill">{{ $stats['pending'] + $stats['under_review'] + $stats['action_required'] }}</span>
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
                                    <i class="fa-solid fa-clock me-1"></i> {{ __('قيد الانتظار') }}
                                </span>
                            @elseif($req->status === 'under_review')
                                <span class="badge badge-req-under-review px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i> {{ __('جاري المراجعة') }}
                                </span>
                            @elseif($req->status === 'action_required')
                                <span class="badge badge-req-action-required px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ __('مطلوب تعديل') }}
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
                            @if($req->user->identity_verified_at || $req->user->status === 'approved')
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
                                <button type="button" class="btn btn-sm btn-light text-primary border rounded-circle shadow-sm ms-1" 
                                    data-bs-toggle="offcanvas" data-bs-target="#detailsOffcanvas" 
                                    onclick="openDetailsOffcanvas('{{ route('admin.seller-requests.show', $req->id) }}')" 
                                    title="{{ __('عرض التفاصيل') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                @if(in_array($req->status, ['pending', 'under_review']))
                                    {{-- Approve Button --}}
                                    <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-3" onclick="openApproveModal({{ $req->id }}, '{{ addslashes($req->user->full_name) }}')">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('قبول') }}</span>
                                    </button>

                                    {{-- Modify Button --}}
                                    <button type="button" class="btn btn-sm btn-warning text-dark d-inline-flex align-items-center gap-1 px-3" onclick="openModifyModal({{ $req->id }}, '{{ addslashes($req->user->full_name) }}')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>{{ __('تعديل') }}</span>
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

{{-- 1. Details Offcanvas (Split-Screen) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="detailsOffcanvas" aria-labelledby="detailsOffcanvasLabel" style="width: 100vw; max-width: 100vw;">
    <div class="offcanvas-header border-bottom bg-light">
        <h5 class="offcanvas-title fw-bold fs-5 d-flex align-items-center gap-2" id="detailsOffcanvasLabel">
            <i class="fa-solid fa-id-card text-primary"></i>
            <span>{{ __('تفاصيل طلب الترقية للمستخدم') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0" style="overflow-x: hidden;">
        <div class="row h-100 m-0">
            <!-- Right Pane: User Data & Dynamic Answers -->
            <div class="col-lg-5 p-4 border-end bg-white" style="overflow-y: auto; height: 100%;" id="detailsOffcanvasBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            
            <!-- Left Pane: Document Viewer -->
            <div class="col-lg-7 p-0 bg-dark position-relative d-flex flex-column" style="height: 100%;">
                <div class="viewer-toolbar p-3 bg-secondary bg-opacity-25 d-flex justify-content-between align-items-center z-1 position-absolute w-100" style="backdrop-filter: blur(10px);">
                    <div class="text-white fw-bold" id="viewerTitle">{{ __('عارض الوثائق') }}</div>
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" class="btn btn-light" onclick="viewerZoomOut()" title="{{ __('تصغير') }}"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                        <button type="button" class="btn btn-light" onclick="viewerReset()" title="{{ __('إعادة تعيين') }}"><i class="fa-solid fa-compress"></i></button>
                        <button type="button" class="btn btn-light" onclick="viewerZoomIn()" title="{{ __('تكبير') }}"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                        <button type="button" class="btn btn-light border-start" onclick="viewerRotateLeft()" title="{{ __('تدوير لليسار') }}"><i class="fa-solid fa-rotate-left"></i></button>
                        <button type="button" class="btn btn-light" onclick="viewerRotateRight()" title="{{ __('تدوير لليمين') }}"><i class="fa-solid fa-rotate-right"></i></button>
                    </div>
                </div>
                
                <div id="documentViewerContainer" class="flex-grow-1 d-flex align-items-center justify-content-center overflow-hidden position-relative" style="background-color: #2b2b2b;">
                    <div class="text-muted text-center" id="viewerEmptyState">
                        <i class="fa-solid fa-file-invoice fa-4x mb-3 opacity-50"></i>
                        <h5>{{ __('اختر وثيقة لعرضها هنا') }}</h5>
                        <p class="small">{{ __('اضغط على أي ملف أو صورة في لوحة البيانات الجانبية') }}</p>
                    </div>
                    <div id="viewerContent" class="w-100 h-100 d-none align-items-center justify-content-center" style="transition: transform 0.3s ease;">
                        <!-- Content injected here (img or iframe) -->
                    </div>
                </div>
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

{{-- 4. Modify Modal --}}
<div class="modal fade" id="modifyModal" tabindex="-1" aria-labelledby="modifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="modifyForm" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2" id="modifyModalLabel">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>{{ __('طلب تعديل على بيانات البائع') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">{{ __('أنت تطلب تعديلاً على بيانات المستخدم:') }} <strong id="modify_user_name" class="text-dark"></strong></p>
                <div class="mb-0">
                    <label class="form-label fw-bold">{{ __('التعديل المطلوب (مطلوب)') }} <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" class="form-control" rows="3" required placeholder="{{ __('اكتب التعديل المطلوب بوضوح ليتمكن المستخدم من فهمه وتعديله...') }}"></textarea>
                    <small class="text-muted mt-1 d-block">{{ __('ستعود حالة الطلب إلى "مطلوب إجراء" للمستخدم لتعديل البيانات.') }}</small>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-ghost px-4" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm text-dark">
                    <i class="fa-solid fa-paper-plane me-1"></i> {{ __('إرسال الطلب') }}
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
        let approveUrl = "{{ route('admin.seller-requests.approve', ':id') }}";
        document.getElementById('approveForm').action = approveUrl.replace(':id', id);
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('approveModal'));
        modal.show();
    }

    // Open Reject Modal
    function openRejectModal(id, name) {
        document.getElementById('reject_user_name').innerText = name;
        let rejectUrl = "{{ route('admin.seller-requests.reject', ':id') }}";
        document.getElementById('rejectForm').action = rejectUrl.replace(':id', id);
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectModal'));
        modal.show();
    }

    // Open Modify Modal
    function openModifyModal(id, name) {
        document.getElementById('modify_user_name').innerText = name;
        let modifyUrl = "{{ route('admin.seller-requests.modification', ':id') }}";
        document.getElementById('modifyForm').action = modifyUrl.replace(':id', id);
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modifyModal'));
        modal.show();
    }

    // Open Details Offcanvas with AJAX
    function openDetailsOffcanvas(url) {
        const bodyEl = document.getElementById('detailsOffcanvasBody');
        bodyEl.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
        
        // Reset viewer
        viewerReset();
        document.getElementById('viewerEmptyState').classList.remove('d-none');
        document.getElementById('viewerContent').classList.add('d-none');
        document.getElementById('viewerContent').innerHTML = '';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const req = data.request;
                    const u = req.user;
                    let statusBadge = '';
                    if (req.status === 'pending') {
                        statusBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">قيد الانتظار</span>';
                    } else if (req.status === 'under_review') {
                        statusBadge = '<span class="badge bg-primary px-3 py-1 rounded-pill">جاري المراجعة</span>';
                    } else if (req.status === 'action_required') {
                        statusBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">مطلوب تعديل</span>';
                    } else if (req.status === 'approved') {
                        statusBadge = '<span class="badge bg-success px-3 py-1 rounded-pill">مقبول</span>';
                    } else {
                        statusBadge = '<span class="badge bg-danger px-3 py-1 rounded-pill">مرفوض</span>';
                    }

                    bodyEl.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="fw-bold m-0 text-primary"><i class="fa-solid fa-user-circle me-2"></i>بيانات المستخدم</h6>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4 border">
                            <img src="${u.profile_photo_url}" class="user-avatar-req rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="${u.name}">
                            <div>
                                <h5 class="fw-bold mb-1">${u.name}</h5>
                                <div class="text-muted small"><i class="fa-solid fa-envelope me-1"></i> ${u.email}</div>
                                <div class="text-muted small font-monospace"><i class="fa-solid fa-phone me-1"></i> ${u.phone || 'لا يوجد هاتف'}</div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light rounded-4 shadow-sm mb-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom py-2">
                                <h6 class="m-0 fw-bold text-dark fs-6"><i class="fa-solid fa-shield-halved text-primary me-2"></i>مؤشر أمان وموثوقية المستخدم</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-white p-2 rounded shadow-sm text-secondary"><i class="fa-solid fa-calendar-check fa-fw"></i></div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">تاريخ التسجيل</small>
                                                <span class="fw-bold fs-6 font-monospace" dir="ltr">${u.registration_date}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-white p-2 rounded shadow-sm text-secondary"><i class="fa-solid fa-gavel fa-fw"></i></div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">المزادات المشارك بها</small>
                                                <span class="fw-bold fs-6 font-monospace">${u.auctions_count}</span> <small class="text-muted">مزادات</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-white p-2 rounded shadow-sm ${u.identity_verified ? 'text-success' : 'text-danger'}"><i class="fa-solid fa-id-card-clip fa-fw"></i></div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">توثيق الهوية (KYC)</small>
                                                ${u.identity_verified ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill border border-success border-opacity-25"><i class="fa-solid fa-check-circle me-1"></i> موثق ومعتمد</span>' : '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill border border-danger border-opacity-25"><i class="fa-solid fa-xmark-circle me-1"></i> غير موثق</span>'}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-white p-2 rounded shadow-sm ${u.reports_count == 0 ? 'text-success' : 'text-danger'}"><i class="fa-solid fa-flag fa-fw"></i></div>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">البلاغات والتقارير</small>
                                                ${u.reports_count == 0 ? '<span class="fw-bold text-success"><i class="fa-solid fa-check me-1"></i> سجل نظيف</span>' : `<span class="fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> ${u.reports_count} بلاغات</span>`}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-light text-center h-100">
                                    <small class="text-muted d-block mb-1">حالة الطلب</small>
                                    <div>${statusBadge}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-light text-center h-100">
                                    <small class="text-muted d-block mb-1">الرصيد</small>
                                    <div class="fw-bold text-primary font-monospace">${u.balance} SAR</div>
                                </div>
                            </div>
                        </div>

                        ${req.admin_notes ? `
                        <div class="p-3 border-start border-4 border-warning rounded-end bg-warning bg-opacity-10 mb-4">
                            <small class="text-warning text-dark fw-bold d-block mb-1"><i class="fa-solid fa-note-sticky me-1"></i>ملاحظات الإدارة السابقة:</small>
                            <div class="text-dark small">${req.admin_notes}</div>
                        </div>
                        ` : ''}

                        ${renderDynamicAnswers(req.dynamic_answers)}
                        
                        ${renderHistoryTimeline(req.history)}

                        ${req.status === 'pending' || req.status === 'under_review' ? `
                            <div class="mt-4 pt-3 border-top text-center" id="globalActionsSection">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold flex-grow-1 shadow-sm" onclick="openApproveModal(${req.id}, '${req.user.name.replace(/'/g, "\\'")}')">
                                        <i class="fa-solid fa-check me-1"></i> قبول نهائي
                                    </button>
                                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-bold flex-grow-1 shadow-sm" onclick="openRejectModal(${req.id}, '${req.user.name.replace(/'/g, "\\'")}')">
                                        <i class="fa-solid fa-xmark me-1"></i> رفض قاطع
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top text-center" id="partialRejectSection" style="display: none;">
                                <button type="button" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold w-100 shadow-sm" onclick="submitPartialRejection(${req.id})">
                                    <i class="fa-solid fa-paper-plane me-2"></i> إرسال ملاحظات التعديل للمستخدم
                                </button>
                                <small class="text-muted d-block mt-2">سيتم تجميع ملاحظات الحقول المرفوضة وإرسالها للمستخدم.</small>
                            </div>
                        ` : ''}

                        <div class="text-muted small text-end mt-4 pt-3 border-top">
                            تاريخ تقديم الطلب الحالي: <span class="font-monospace">${req.created_at}</span>
                        </div>
                    `;
                } else {
                    bodyEl.innerHTML = '<div class="alert alert-danger mb-0">تعذر جلب تفاصيل الطلب.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                bodyEl.innerHTML = '<div class="alert alert-danger mb-0">حدث خطأ أثناء الاتصال بالخادم.</div>';
            });
    }

    // Helper to render user history timeline
    function renderHistoryTimeline(history) {
        if (!history || history.length === 0) {
            return `
            <div class="mt-4 border-top pt-3">
                <h6 class="fw-bold mb-3 pb-2 text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i>سجل طلبات المستخدم</h6>
                <div class="alert alert-light border small text-center text-muted">
                    <i class="fa-solid fa-star text-warning mb-2 fa-2x d-block"></i>
                    هذا هو أول طلب ترقية لهذا المستخدم
                </div>
            </div>`;
        }

        let html = `
        <div class="mt-4 border-top pt-3">
            <h6 class="fw-bold mb-3 pb-2 text-primary d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-clock-rotate-left me-2"></i>سجل طلبات المستخدم</span>
                <span class="badge bg-secondary rounded-pill">${history.length} طلبات سابقة</span>
            </h6>
            <div class="position-relative ms-3 border-start border-2 border-primary border-opacity-25 ps-3 pb-2">`;
        
        history.forEach(item => {
            let statusBadge = '';
            let iconClass = '';
            let borderClass = '';
            
            if (item.status === 'rejected') {
                statusBadge = '<span class="badge bg-danger">مرفوض</span>';
                iconClass = 'fa-xmark text-white bg-danger';
                borderClass = 'border-danger border-opacity-50';
            } else if (item.status === 'action_required') {
                statusBadge = '<span class="badge bg-warning text-dark">طلب تعديل</span>';
                iconClass = 'fa-triangle-exclamation text-dark bg-warning';
                borderClass = 'border-warning border-opacity-50';
            } else if (item.status === 'approved') {
                statusBadge = '<span class="badge bg-success">مقبول</span>';
                iconClass = 'fa-check text-white bg-success';
                borderClass = 'border-success border-opacity-50';
            } else {
                statusBadge = '<span class="badge bg-secondary">غير مكتمل/ملغي</span>';
                iconClass = 'fa-minus text-white bg-secondary';
                borderClass = 'border-secondary border-opacity-50';
            }

            html += `
                <div class="mb-3 position-relative">
                    <div class="position-absolute translate-middle rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                         style="width: 24px; height: 24px; left: -12px; top: 0px; font-size: 10px; z-index: 2;">
                         <i class="fa-solid ${iconClass} w-100 h-100 rounded-circle d-flex align-items-center justify-content-center"></i>
                    </div>
                    <div class="p-3 bg-light border ${borderClass} rounded-3 ms-2">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            ${statusBadge}
                            <small class="text-muted font-monospace" dir="ltr">${item.created_at}</small>
                        </div>
                        ${item.admin_notes ? `<div class="small mt-2 text-dark bg-white p-2 rounded border"><i class="fa-solid fa-quote-right text-muted me-1"></i> ${item.admin_notes}</div>` : '<div class="small mt-2 text-muted fst-italic">لا توجد ملاحظات مسجلة</div>'}
                    </div>
                </div>
            `;
        });

        html += `</div></div>`;
        return html;
    }

    // Helper to render dynamic answers elegantly
    let currentRejectedFields = {}; // { index: { label: '', reason: '' } }

    function renderDynamicAnswers(answers) {
        currentRejectedFields = {}; // reset
        if (!answers || answers.length === 0) return '';
        
        let html = '<div><h6 class="fw-bold mb-3 pb-2 border-bottom text-primary"><i class="fa-solid fa-clipboard-list me-2"></i>النماذج والمستندات المرفقة</h6><div class="row g-3">';
        
        answers.forEach((ans, index) => {
            let valueHtml = '';
            let val = ans.value;
            
            if (val === null || val === '') {
                valueHtml = '<span class="text-muted fst-italic">لم يتم الإرفاق</span>';
            } else if (typeof val === 'string' && val.startsWith('/storage/')) {
                // It's a file - use Document Viewer
                let type = 'file';
                let btnClass = 'btn-outline-primary';
                let icon = 'fa-file';
                
                if (val.endsWith('.pdf')) {
                    type = 'pdf';
                    btnClass = 'btn-outline-danger';
                    icon = 'fa-file-pdf';
                } else if (val.match(/\.(jpeg|jpg|png|gif|webp)$/i)) {
                    type = 'image';
                    btnClass = 'btn-outline-success';
                    icon = 'fa-image';
                }
                
                valueHtml = `<button type="button" onclick="viewDocument('${val}', '${type}', '${ans.label}')" class="btn btn-sm ${btnClass} px-3 rounded-pill w-100 text-start shadow-sm d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid ${icon} me-1"></i> استعراض</span>
                                <i class="fa-solid fa-chevron-left small"></i>
                            </button>`;
            } else if (ans.type === 'checkbox') {
                valueHtml = val == '1' ? '<span class="badge bg-success">نعم</span>' : '<span class="badge bg-secondary">لا</span>';
            } else {
                valueHtml = `<div class="text-dark bg-light p-2 rounded border small">${val}</div>`;
            }

            html += `
                <div class="col-12" id="field_container_${index}">
                    <div class="p-3 border rounded-3 bg-white position-relative shadow-sm transition-all">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-dark d-block fw-bold" style="font-size: 0.9rem;">${ans.label}</small>
                            <div class="btn-group shadow-sm rounded-pill" role="group">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-start-pill px-3" id="btn_accept_${index}" onclick="toggleFieldValidation(${index}, true, '${ans.label}')" title="بيانات صحيحة"><i class="fa-solid fa-check"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" id="btn_reject_${index}" onclick="toggleFieldValidation(${index}, false, '${ans.label}')" title="يوجد خطأ"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                        ${valueHtml}
                        
                        <div class="mt-3 d-none" id="reject_reason_container_${index}">
                            <textarea class="form-control form-control-sm border-danger" id="reject_reason_${index}" rows="2" placeholder="اكتب سبب الرفض لهذا الحقل (مثال: الصورة غير واضحة)..." onchange="updateRejectReason(${index}, this.value)"></textarea>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        return html;
    }

    window.toggleFieldValidation = function(index, isValid, label) {
        const acceptBtn = document.getElementById(`btn_accept_${index}`);
        const rejectBtn = document.getElementById(`btn_reject_${index}`);
        const reasonContainer = document.getElementById(`reject_reason_container_${index}`);
        const fieldContainer = document.getElementById(`field_container_${index}`).firstElementChild;
        const rejectInput = document.getElementById(`reject_reason_${index}`);

        if (isValid) {
            // Mark as valid
            acceptBtn.classList.replace('btn-outline-success', 'btn-success');
            rejectBtn.classList.replace('btn-danger', 'btn-outline-danger');
            reasonContainer.classList.add('d-none');
            fieldContainer.classList.remove('border-danger', 'border-2');
            fieldContainer.classList.add('border-success', 'border-2');
            
            delete currentRejectedFields[index];
        } else {
            // Mark as invalid
            rejectBtn.classList.replace('btn-outline-danger', 'btn-danger');
            acceptBtn.classList.replace('btn-success', 'btn-outline-success');
            reasonContainer.classList.remove('d-none');
            fieldContainer.classList.remove('border-success', 'border-2');
            fieldContainer.classList.add('border-danger', 'border-2');
            rejectInput.focus();
            
            currentRejectedFields[index] = { label: label, reason: rejectInput.value };
        }

        checkPartialRejections();
    };

    window.updateRejectReason = function(index, value) {
        if (currentRejectedFields[index]) {
            currentRejectedFields[index].reason = value;
        }
    };

    function checkPartialRejections() {
        const partialRejectSection = document.getElementById('partialRejectSection');
        if (!partialRejectSection) return;

        if (Object.keys(currentRejectedFields).length > 0) {
            partialRejectSection.style.display = 'block';
        } else {
            partialRejectSection.style.display = 'none';
        }
    }

    window.submitPartialRejection = function(reqId) {
        let notesArr = [];
        notesArr.push('الرجاء تعديل الملاحظات التالية المتعلقة بطلبك:');
        
        for (const [index, data] of Object.entries(currentRejectedFields)) {
            let reason = data.reason.trim();
            if (!reason) reason = 'البيانات غير صحيحة أو تحتاج إلى تحديث.';
            notesArr.push(`- ${data.label}: ${reason}`);
        }
        
        let finalNotes = notesArr.join('\\n');
        
        // Use the existing modification modal logic
        let modifyUrl = "{{ route('admin.seller-requests.modification', ':id', false) }}";
        document.getElementById('modifyForm').action = modifyUrl.replace(':id', reqId);
        document.getElementById('modifyForm').querySelector('textarea[name="admin_notes"]').value = finalNotes;
        
        // Hide offcanvas and show modal
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('detailsOffcanvas'));
        if(offcanvas) offcanvas.hide();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modifyModal'));
        modal.show();
    };

    // Document Viewer Logic
    let currentZoom = 1;
    let currentRotation = 0;
    const viewerContent = document.getElementById('viewerContent');
    const viewerEmptyState = document.getElementById('viewerEmptyState');
    const viewerTitle = document.getElementById('viewerTitle');

    window.viewDocument = function(url, type, title) {
        viewerTitle.innerText = title;
        viewerEmptyState.classList.add('d-none');
        viewerContent.classList.remove('d-none');
        
        // Reset transforms
        viewerReset();

        if (type === 'image') {
            viewerContent.innerHTML = `<img id="documentMedia" src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease;">`;
        } else if (type === 'pdf') {
            viewerContent.innerHTML = `<iframe src="${url}#toolbar=0&view=FitH" width="100%" height="100%" frameborder="0"></iframe>`;
        } else {
            viewerContent.innerHTML = `<div class="text-center text-white"><i class="fa-solid fa-file-arrow-down fa-3x mb-3"></i><h5>ملف غير مدعوم للعرض المباشر</h5><a href="${url}" target="_blank" class="btn btn-primary mt-2">تحميل الملف</a></div>`;
        }
    };

    window.viewerZoomIn = function() {
        currentZoom += 0.2;
        applyViewerTransform();
    };

    window.viewerZoomOut = function() {
        if (currentZoom > 0.4) {
            currentZoom -= 0.2;
            applyViewerTransform();
        }
    };

    window.viewerRotateLeft = function() {
        currentRotation -= 90;
        applyViewerTransform();
    };

    window.viewerRotateRight = function() {
        currentRotation += 90;
        applyViewerTransform();
    };

    window.viewerReset = function() {
        currentZoom = 1;
        currentRotation = 0;
        applyViewerTransform();
    };

    function applyViewerTransform() {
        const media = document.getElementById('documentMedia');
        if (media) {
            media.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
        }
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
