@extends('layouts.admin')

@section('title', __('إدارة قوالب التسجيل'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    /* SaaS Delete Modal Styles */
    .modal-saas-delete .modal-content {
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .modal-saas-delete .icon-container {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        background: #fef2f2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.15), inset 0 0 0 1px rgba(239, 68, 68, 0.05);
        position: relative;
    }
    .modal-saas-delete .icon-container::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 28px;
        border: 1px solid rgba(239, 68, 68, 0.2);
        animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-red {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.3; transform: scale(1.05); }
    }
    .modal-saas-delete .btn-cancel {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        transition: all 0.2s;
    }
    .modal-saas-delete .btn-cancel:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .modal-saas-delete .btn-delete {
        background: linear-gradient(to bottom, #ef4444, #dc2626);
        border: 1px solid #b91c1c;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.1);
        transition: all 0.2s;
    }
    .modal-saas-delete .btn-delete:hover {
        background: linear-gradient(to bottom, #dc2626, #b91c1c);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2), inset 0 1px 0 rgba(255,255,255,0.1);
        transform: translateY(-1px);
    }


    /* Template Card Styles */
    .template-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }
    .template-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        border-color: rgba(59, 130, 246, 0.2);
    }

    /* Top accent gradient */
    .template-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
    .template-card:hover::before {
        opacity: 1;
    }
    .template-card.is-inactive::before {
        background: linear-gradient(90deg, #94a3b8, #cbd5e1);
    }

    .template-card-body {
        padding: 1.75rem;
    }

    .template-icon-box {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    .template-icon-box.active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .template-icon-box.inactive {
        background: rgba(100, 116, 139, 0.1);
        color: #94a3b8;
    }
    .template-card:hover .template-icon-box.active {
        background: rgba(16, 185, 129, 0.18);
        transform: rotate(-5deg) scale(1.08);
    }

    .template-title {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--text, #1e293b);
        margin: 0;
        line-height: 1.4;
    }
    .template-desc {
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
        margin: 0;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .template-meta-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border, rgba(0,0,0,0.06));
    }
    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .meta-chip:hover {
        transform: scale(1.05);
    }
    .meta-chip.fields {
        background: rgba(59, 130, 246, 0.08);
        color: #3b82f6;
    }
    .meta-chip.requests {
        background: rgba(245, 158, 11, 0.08);
        color: #f59e0b;
        text-decoration: none;
    }
    .meta-chip.requests:hover {
        background: rgba(245, 158, 11, 0.18);
        color: #d97706;
    }
    .meta-chip.date {
        background: rgba(100, 116, 139, 0.08);
        color: var(--text-muted, #64748b);
    }

    /* Status badge */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 2s infinite;
    }
    .status-dot.active { background: #10b981; }
    .status-dot.inactive { background: #94a3b8; animation: none; }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Actions row */
    .template-actions {
        display: flex;
        gap: 6px;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--border, rgba(0,0,0,0.06));
    }
    .template-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid var(--border, rgba(0,0,0,0.08));
        background: var(--bg-card, #fff);
        color: var(--text-muted, #64748b);
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .template-action-btn:hover {
        background: rgba(59, 130, 246, 0.06);
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.2);
    }
    .template-action-btn.edit:hover {
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.3);
    }
    .template-action-btn.clone:hover {
        color: #8b5cf6;
        border-color: rgba(139, 92, 246, 0.3);
        background: rgba(139, 92, 246, 0.06);
    }
    .template-action-btn.toggle:hover {
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.3);
        background: rgba(245, 158, 11, 0.06);
    }
    .template-action-btn.danger:hover {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    /* Professional Table Styles */
    .table-custom {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-custom thead th {
        background-color: var(--bg-body, #f8fafc);
        color: var(--text-muted, #64748b);
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid var(--border, #e2e8f0);
        white-space: nowrap;
    }
    .table-custom thead th:first-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .table-custom thead th:last-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .table-custom tbody tr {
        transition: all 0.2s ease;
    }
    .table-custom tbody tr:hover {
        background-color: var(--bg-body, #f8fafc);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05);
    }
    .table-custom tbody td {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border, #f1f5f9);
        vertical-align: middle;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }
    .table-action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid transparent;
        color: var(--text-muted, #64748b);
        background: transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .table-action-btn:hover {
        background: var(--bg-body, #f1f5f9);
        border-color: var(--border, #e2e8f0);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .table-action-btn.edit:hover { color: #3b82f6; }
    .table-action-btn.clone:hover { color: #8b5cf6; }
    .table-action-btn.toggle:hover { color: #10b981; }
    .table-action-btn.delete:hover { color: #ef4444; }

    /* Empty state */
    .empty-state-box {
        padding: 5rem 2rem;
        text-align: center;
    }
    .empty-state-icon {
        width: 100px;
        height: 100px;
        border-radius: 28px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 2.5rem;
    }
</style>
@endsection

@section('content')
<x-admin-header :title="__('إدارة قوالب التسجيل الديناميكية')" :breadcrumb="__('قوالب التسجيل')">
    <a href="{{ route('admin.form-templates.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4">
        <i class="fa-solid fa-plus me-1"></i> {{ __('إنشاء قالب جديد') }}
    </a>
</x-admin-header>

{{-- 1. Statistics Cards --}}
<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('إجمالي القوالب') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">{{ __('قوالب مفعلة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['inactive'] }}</div>
                <div class="stat-label">{{ __('قوالب معطلة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">{{ __('إجمالي الطلبات') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Filters & View Toolbar --}}
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.form-templates.index') }}" method="GET" class="row g-3 align-items-center">
            {{-- Search --}}
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-merge rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-body border-0 text-muted ps-3"><i class="fa-solid fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-body shadow-none" placeholder="{{ __('ابحث باسم القالب أو الوصف...') }}" value="{{ request('search') }}">
                </div>
            </div>
            
            {{-- Status Filter --}}
            <div class="col-12 col-md-4 col-lg-3">
                <div class="input-group input-group-merge rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-body border-0 text-muted ps-3"><i class="fa-solid fa-filter"></i></span>
                    <select name="status" class="form-select border-0 bg-body shadow-none" onchange="this.form.submit()">
                        <option value="">{{ __('جميع الحالات') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('مفعل') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('معطل') }}</option>
                    </select>
                </div>
            </div>

            {{-- Submit & Clear --}}
            <div class="col-12 col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-danger rounded-pill w-100 fw-bold shadow-sm">{{ __('بحث') }}</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.form-templates.index') }}" class="btn btn-light rounded-circle border shadow-sm text-muted d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink:0;" title="{{ __('مسح الفلاتر') }}">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>

            {{-- View Toggles (Pushed to the end) --}}
            <div class="col-12 col-lg-3 ms-auto d-flex justify-content-lg-end align-items-center gap-3">
                <span class="text-muted small fw-bold">{{ __('العدد:') }} <strong class="text-dark">{{ $templates->total() }}</strong></span>
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-danger active" id="btn-view-table" onclick="toggleTemplateView('table')" title="{{ __('عرض الجدول') }}">
                        <i class="fa-solid fa-list px-1"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btn-view-grid" onclick="toggleTemplateView('grid')" title="{{ __('عرض البطاقات') }}">
                        <i class="fa-solid fa-border-all px-1"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($templates->count() > 0)
{{-- 3. Table View (Default) --}}
<div id="table-view-container" class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>{{ __('رقم') }}</th>
                        <th>{{ __('اسم القالب') }}</th>
                        <th>{{ __('الوصف') }}</th>
                        <th>{{ __('عدد الحقول') }}</th>
                        <th class="text-center">{{ __('الطلبات') }}</th>
                        <th>{{ __('الحالة') }}</th>
                        <th>{{ __('تاريخ الإنشاء') }}</th>
                        <th class="text-end">{{ __('إجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $template->id }}</td>
                            <td class="fw-bold text-dark fs-6">{{ $template->name }}</td>
                            <td>
                                <span class="d-inline-block text-truncate text-muted small" style="max-width: 220px;" title="{{ $template->description }}">
                                    {{ $template->description ?: '---' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold">
                                    <i class="fa-solid fa-layer-group me-1"></i>{{ $template->fields_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($template->seller_requests_count > 0)
                                    <a href="{{ route('admin.seller-requests.index', ['template_id' => $template->id]) }}" class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-2 text-decoration-none fw-bold" title="{{ __('عرض الطلبات المرتبطة') }}">
                                        <i class="fa-solid fa-file-lines me-1"></i>{{ $template->seller_requests_count }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-3 py-2 fw-bold">0</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.form-templates.toggle-status', $template->id) }}" method="POST" class="d-inline ajax-toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm border-0 p-0 shadow-none bg-transparent" title="{{ $template->is_active ? __('انقر للتعطيل') : __('انقر للتفعيل') }}">
                                        @if($template->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold" style="cursor: pointer; transition: all 0.2s;">
                                                <i class="fa-solid fa-toggle-on fs-6 align-middle me-1"></i> {{ __('مفعل') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-3 py-2 fw-bold" style="cursor: pointer; transition: all 0.2s;">
                                                <i class="fa-solid fa-toggle-off fs-6 align-middle me-1"></i> {{ __('معطل') }}
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small fw-semibold">
                                <i class="fa-regular fa-calendar me-1"></i>{{ $template->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.form-templates.edit', $template->id) }}" class="table-action-btn edit" title="{{ __('تعديل') }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.form-templates.clone', $template->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="table-action-btn clone" title="{{ __('نسخ / تكرار') }}">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.form-templates.destroy', $template->id) }}" method="POST" class="d-inline ajax-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-btn delete" title="{{ __('حذف') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 4. Grid View (Hidden by default) --}}
<div id="grid-view-container" class="row g-4 mb-4 d-none">
    @foreach($templates as $template)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="template-card {{ !$template->is_active ? 'is-inactive' : '' }}">
                <div class="template-card-body">
                    {{-- Header --}}
                    <div class="d-flex gap-3 align-items-start mb-3">
                        <div class="template-icon-box {{ $template->is_active ? 'active' : 'inactive' }}">
                            <i class="fa-solid fa-cubes"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <form action="{{ route('admin.form-templates.toggle-status', $template->id) }}" method="POST" class="d-inline mb-1 ajax-toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm border-0 p-0 shadow-none bg-transparent" title="{{ $template->is_active ? __('انقر للتعطيل') : __('انقر للتفعيل') }}">
                                        @if($template->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                                                <i class="fa-solid fa-toggle-on fs-6 align-middle me-1"></i> {{ __('مفعل') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                                                <i class="fa-solid fa-toggle-off fs-6 align-middle me-1"></i> {{ __('معطل') }}
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                            <p class="template-desc">{{ $template->description ?: __('بدون وصف') }}</p>
                        </div>
                    </div>

                    {{-- Meta Chips --}}
                    <div class="template-meta-row">
                        <span class="meta-chip fields">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $template->fields_count }} {{ __('حقول') }}
                        </span>
                        @if($template->seller_requests_count > 0)
                            <a href="{{ route('admin.seller-requests.index', ['template_id' => $template->id]) }}" class="meta-chip requests">
                                <i class="fa-solid fa-file-lines"></i>
                                {{ $template->seller_requests_count }} {{ __('طلب') }}
                            </a>
                        @else
                            <span class="meta-chip date">
                                <i class="fa-solid fa-file-lines"></i>
                                0 {{ __('طلب') }}
                            </span>
                        @endif
                        <span class="meta-chip date">
                            <i class="fa-regular fa-calendar"></i>
                            {{ $template->created_at->format('Y-m-d') }}
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="template-actions">
                        <a href="{{ route('admin.form-templates.edit', $template->id) }}" class="template-action-btn edit flex-fill" title="{{ __('تعديل') }}">
                            <i class="fa-solid fa-pen-to-square"></i> {{ __('تعديل') }}
                        </a>
                        <form action="{{ route('admin.form-templates.clone', $template->id) }}" method="POST" style="display: contents;">
                            @csrf
                            <button type="submit" class="template-action-btn clone" title="{{ __('تكرار') }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </form>

                        <form action="{{ route('admin.form-templates.destroy', $template->id) }}" method="POST" style="display: contents;" class="ajax-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="template-action-btn danger" title="{{ __('حذف') }}">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

    {{-- Pagination --}}
    @if($templates->hasPages())
        <div class="d-flex justify-content-center mb-4">
            {{ $templates->links('pagination::bootstrap-5') }}
        </div>
    @endif
@else
    {{-- Empty State --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="empty-state-box">
            <div class="empty-state-icon">
                <i class="fa-solid {{ request()->hasAny(['search', 'status']) ? 'fa-magnifying-glass' : 'fa-cubes' }}"></i>
            </div>
            
            @if(request()->hasAny(['search', 'status']))
                <h4 class="fw-bold mb-2" style="color: var(--text);">{{ __('لا توجد نتائج مطابقة') }}</h4>
                <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">{{ __('لم نعثر على أي قوالب تطابق خيارات البحث الخاصة بك. جرب مسح الفلاتر أو تغيير الكلمات المفتاحية.') }}</p>
                <a href="{{ route('admin.form-templates.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                    <i class="fa-solid fa-rotate-right me-2"></i>{{ __('مسح الفلاتر') }}
                </a>
            @else
                <h4 class="fw-bold mb-2" style="color: var(--text);">{{ __('لا توجد قوالب حتى الآن') }}</h4>
                <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">{{ __('ابدأ بإنشاء أول قالب ديناميكي لتسجيل البائعين. يمكنك إضافة حقول متنوعة وتخصيص النموذج بالكامل.') }}</p>
                <a href="{{ route('admin.form-templates.create') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i>{{ __('إنشاء أول قالب') }}
                </a>
            @endif
        </div>
    </div>
@endif

{{-- Delete Confirmation Modal --}}
<div class="modal fade modal-saas-delete" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-body p-4 pt-5 text-center">
                <div class="icon-container">
                    <i class="fa-solid fa-trash-can fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('هل أنت متأكد من الحذف؟') }}</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.6;">{{ __('هذا الإجراء نهائي ولا يمكن التراجع عنه. سيتم حذف القالب بجميع حقوله وبياناته بشكل دائم من النظام.') }}</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-2 d-flex flex-nowrap gap-3">
                <button type="button" class="btn btn-cancel rounded-3 py-2 w-50 m-0" data-bs-dismiss="modal">{{ __('تراجع') }}</button>
                <button type="button" class="btn btn-delete rounded-3 py-2 w-50 m-0" id="confirmDeleteBtn">{{ __('تأكيد الحذف') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function toggleTemplateView(view) {
        const tableContainer = document.getElementById('table-view-container');
        const gridContainer = document.getElementById('grid-view-container');
        const btnTable = document.getElementById('btn-view-table');
        const btnGrid = document.getElementById('btn-view-grid');

        if (view === 'grid') {
            tableContainer.classList.add('d-none');
            gridContainer.classList.remove('d-none');
            btnTable.classList.remove('active');
            btnGrid.classList.add('active');
            localStorage.setItem('formTemplateView', 'grid');
        } else {
            gridContainer.classList.add('d-none');
            tableContainer.classList.remove('d-none');
            btnGrid.classList.remove('active');
            btnTable.classList.add('active');
            localStorage.setItem('formTemplateView', 'table');
        }
    }

    // Restore saved view preference
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('formTemplateView');
        if (savedView === 'grid') {
            toggleTemplateView('grid');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Helper to show generic toast
        function showToast(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: type,
                    title: message
                });
            } else {
                alert(message);
            }
        }

        // Handle Status Toggle via AJAX
        document.querySelectorAll('.ajax-toggle-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let btn = form.querySelector('button');
                let originalHtml = btn.innerHTML;
                
                // Show tiny spinner inside badge instead of replacing whole HTML
                let icon = btn.querySelector('i');
                if(icon) {
                    icon.className = 'spinner-border spinner-border-sm me-1';
                }
                btn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(new FormData(form)).toString()
                })
                .then(async res => {
                    const data = await res.json();
                    if(res.ok && data.success) {
                        showToast(data.message, 'success');
                        
                        // Update all identical forms (Table and Grid view)
                        document.querySelectorAll('form[action="'+form.action+'"]').forEach(f => {
                            let fBtn = f.querySelector('button');
                            if(data.is_active) {
                                fBtn.innerHTML = `
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold" style="cursor: pointer; transition: all 0.2s;">
                                        <i class="fa-solid fa-toggle-on fs-6 align-middle me-1"></i> {{ __('مفعل') }}
                                    </span>
                                `;
                                fBtn.title = "{{ __('انقر للتعطيل') }}";
                            } else {
                                fBtn.innerHTML = `
                                    <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-3 py-2 fw-bold" style="cursor: pointer; transition: all 0.2s;">
                                        <i class="fa-solid fa-toggle-off fs-6 align-middle me-1"></i> {{ __('معطل') }}
                                    </span>
                                `;
                                fBtn.title = "{{ __('انقر للتفعيل') }}";
                            }
                            fBtn.disabled = false;
                        });
                    } else {
                        showToast(data.message || 'حدث خطأ', 'error');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
            });
        });

        // Handle Delete via AJAX with Custom Modal
        let formToDelete = null;

        document.querySelectorAll('.ajax-delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                formToDelete = form;
                
                let deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
            if(!formToDelete) return;
            
            let form = formToDelete;
            let btn = form.querySelector('button');
            let originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm text-secondary"></span>';
            btn.disabled = true;

            // Hide the modal
            let modalInstance = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            if(modalInstance) modalInstance.hide();

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(new FormData(form)).toString()
            })
            .then(async res => {
                const data = await res.json();
                if(res.ok && data.success) {
                    showToast(data.message, 'success');
                    
                    // Fade out Table Row
                    let tr = form.closest('tr');
                    if(tr) {
                        tr.style.transition = 'all 0.4s ease';
                        tr.style.opacity = '0';
                        tr.style.transform = 'translateY(10px)';
                        setTimeout(() => tr.remove(), 400);
                    }
                    
                    // Fade out Grid Card
                    let cardCol = form.closest('.col-12.col-md-6.col-xl-4');
                    if(cardCol) {
                        cardCol.style.transition = 'all 0.4s ease';
                        cardCol.style.opacity = '0';
                        cardCol.style.transform = 'scale(0.95)';
                        setTimeout(() => cardCol.remove(), 400);
                    }
                } else {
                    showToast(data.message || 'حدث خطأ', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });
    });
</script>
@endsection
