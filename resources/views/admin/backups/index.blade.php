@extends('layouts.admin')

@section('title', 'إدارة النسخ الاحتياطي')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    /* Premium UI & Theme Styling (aligned with Auctions page) */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.85);
        --glass-border: rgba(51, 65, 85, 0.8);
    }



    .data-view-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .data-view-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }

    .table-view th {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom-width: 1px;
    }
    .table-view td {
        vertical-align: middle;
        color: var(--text);
        border-bottom-color: var(--border);
    }

    .view-toolbar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .grid-view-btn, .table-view-btn {
        background: var(--bg-body);
        border: 1px solid var(--border);
        color: var(--text-muted);
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .grid-view-btn.active, .table-view-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ── Backup specific ── */
    .backup-file-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    #backups-custom-table tr:hover .backup-file-icon {
        background: var(--brand-blue-glow);
        color: var(--brand-blue-light);
        border-color: rgba(59,130,246,0.2);
    }
    .backup-filename {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text);
        display: block;
        direction: ltr;
        word-break: break-all;
    }
    .backup-filepath {
        font-size: 0.71rem;
        color: var(--text-muted);
        display: block;
        direction: ltr;
    }

    .backup-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.7rem;
        border-radius: 100px;
        font-size: 0.71rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .backup-badge.db    { background: var(--brand-blue-glow); color: var(--brand-blue-light); }
    .backup-badge.files { background: var(--brand-gold-glow); color: var(--brand-gold-light); }
    .backup-badge.all   { background: var(--success-glow);    color: var(--success); }

    .backup-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        background: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .backup-action.dl  { background: var(--brand-blue-glow); color: var(--brand-blue-light); border-color: rgba(59,130,246,0.2); }
    .backup-action.del { background: var(--danger-glow);     color: var(--danger);           border-color: rgba(239,68,68,0.2); }
    .backup-action:hover { transform: translateY(-1px); filter: brightness(1.1); }

    /* Grid card */
    .backup-grid-head {
        height: 110px;
        display: flex; align-items: center; justify-content: center;
        border-bottom: 1px solid var(--border);
        position: relative;
    }
    .backup-grid-head.db    { background: radial-gradient(circle at 30% 30%, rgba(59,130,246,0.14), transparent 70%); color: var(--brand-blue-light); }
    .backup-grid-head.files { background: radial-gradient(circle at 30% 30%, rgba(245,158,11,0.14), transparent 70%); color: var(--brand-gold-light); }
    .backup-grid-head.all   { background: radial-gradient(circle at 30% 30%, rgba(16,185,129,0.14), transparent 70%); color: var(--success); }





    @keyframes btn-spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">
    الإجمالي: <span class="text-primary fs-5 font-weight-bold ml-1">{{ $stats['total_count'] }}</span> نسخة
</div>
@endsection

@section('content')
<x-admin-header title="إدارة النسخ الاحتياطي" breadcrumb="النسخ الاحتياطي">
    <button type="button" class="btn-ultra" data-bs-toggle="modal" data-bs-target="#createBackupModal">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>إنشاء نسخة احتياطية</span>
    </button>
</x-admin-header>

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_count'] }}</div>
                <div class="stat-label">إجمالي النسخ</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_size'] }}</div>
                <div class="stat-label">حجم النسخ</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['free_disk_space'] }}</div>
                <div class="stat-label">مساحة متاحة</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card purple h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['last_backup'] }}</div>
                <div class="stat-label">آخر نسخة</div>
            </div>
        </div>
    </div>
</div>

<!-- Automation Settings Accordion -->
<div class="accordion mb-4 shadow-sm border-0" id="automationAccordion" style="border-radius: 12px; overflow: hidden;">
    <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingAutomation">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAutomation" aria-expanded="false" aria-controls="collapseAutomation" style="background: var(--bg-card); color: var(--text); font-weight: 600;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-primary"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                إعدادات الجدولة والأتمتة (النسخ التلقائي)
            </button>
        </h2>
        <div id="collapseAutomation" class="accordion-collapse collapse" aria-labelledby="headingAutomation" data-bs-parent="#automationAccordion">
            <div class="accordion-body" style="background: var(--bg-body);">
                <form id="automationSettingsForm" action="{{ route('admin.backups.settings') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <!-- Daily DB Backup -->
                        <div class="col-12 col-lg-4">
                            <div class="data-view-card p-3 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-database me-2"></i>نسخة يومية (بيانات)</h6>
                                    <div class="form-check form-switch" style="transform: scale(1.1); margin-right: 0;">
                                        <input class="form-check-input" type="checkbox" name="backup_auto_db_enabled" value="1" {{ $settings['db_enabled'] == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">أخذ نسخة احتياطية ذكية لقاعدة البيانات بشكل يومي.</p>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">وقت التنفيذ</label>
                                    <input type="time" class="form-control form-control-sm bg-body" name="backup_auto_db_time" value="{{ $settings['db_time'] }}">
                                </div>
                                <hr class="my-3 text-muted" style="opacity: 0.15">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label small text-muted mb-0">الاحتفاظ بآخر (نسخ):</label>
                                    <input type="number" class="form-control form-control-sm bg-body" style="width: 70px;" name="backup_retention_db" value="{{ $settings['retention_db'] }}" min="1" max="365">
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Files Backup -->
                        <div class="col-12 col-lg-4">
                            <div class="data-view-card p-3 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6 class="mb-0 text-warning fw-bold"><i class="fas fa-folder-open me-2"></i>نسخة أسبوعية (ملفات)</h6>
                                    <div class="form-check form-switch" style="transform: scale(1.1); margin-right: 0;">
                                        <input class="form-check-input" type="checkbox" name="backup_auto_files_enabled" value="1" {{ $settings['files_enabled'] == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">أخذ نسخة احتياطية لجميع الملفات المرفوعة أسبوعياً.</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-muted">يوم التنفيذ</label>
                                        <select class="form-select form-select-sm bg-body" name="backup_auto_files_day">
                                            <option value="0" {{ $settings['files_day'] == '0' ? 'selected' : '' }}>الأحد</option>
                                            <option value="1" {{ $settings['files_day'] == '1' ? 'selected' : '' }}>الإثنين</option>
                                            <option value="2" {{ $settings['files_day'] == '2' ? 'selected' : '' }}>الثلاثاء</option>
                                            <option value="3" {{ $settings['files_day'] == '3' ? 'selected' : '' }}>الأربعاء</option>
                                            <option value="4" {{ $settings['files_day'] == '4' ? 'selected' : '' }}>الخميس</option>
                                            <option value="5" {{ $settings['files_day'] == '5' ? 'selected' : '' }}>الجمعة</option>
                                            <option value="6" {{ $settings['files_day'] == '6' ? 'selected' : '' }}>السبت</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted">الوقت</label>
                                        <input type="time" class="form-control form-control-sm bg-body" name="backup_auto_files_time" value="{{ $settings['files_time'] }}">
                                    </div>
                                </div>
                                <hr class="my-3 text-muted" style="opacity: 0.15">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label small text-muted mb-0">الاحتفاظ بآخر (نسخ):</label>
                                    <input type="number" class="form-control form-control-sm bg-body" style="width: 70px;" name="backup_retention_files" value="{{ $settings['retention_files'] }}" min="1" max="365">
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Full Backup -->
                        <div class="col-12 col-lg-4">
                            <div class="data-view-card p-3 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6 class="mb-0 text-success fw-bold"><i class="fas fa-archive me-2"></i>نسخة شهرية (شاملة)</h6>
                                    <div class="form-check form-switch" style="transform: scale(1.1); margin-right: 0;">
                                        <input class="form-check-input" type="checkbox" name="backup_auto_all_enabled" value="1" {{ $settings['all_enabled'] == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">أخذ نسخة شاملة لبيانات وملفات الموقع مرة كل شهر.</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-muted">يوم التنفيذ</label>
                                        <input type="number" class="form-control form-control-sm bg-body" name="backup_auto_all_day" value="{{ $settings['all_day'] }}" min="1" max="28" placeholder="1-28">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted">الوقت</label>
                                        <input type="time" class="form-control form-control-sm bg-body" name="backup_auto_all_time" value="{{ $settings['all_time'] }}">
                                    </div>
                                </div>
                                <hr class="my-3 text-muted" style="opacity: 0.15">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label small text-muted mb-0">الاحتفاظ بآخر (نسخ):</label>
                                    <input type="number" class="form-control form-control-sm bg-body" style="width: 70px;" name="backup_retention_all" value="{{ $settings['retention_all'] }}" min="1" max="365">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" id="saveSettingsBtn" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i> حفظ الإعدادات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filters Panel -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="ابحث باسم الملف...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                    <input type="text" id="filter_date" class="form-control border-start-0 ps-0" placeholder="نطاق زمني...">
                </div>
            </div>
            <div class="col-md-2">
                <select id="filter_type" class="form-select select2-init" data-dropdown-parent="body">
                    <option value="">كل الأنواع</option>
                    <option value="db">قاعدة بيانات</option>
                    <option value="files">ملفات فقط</option>
                    <option value="all">نسخة كاملة</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter_size" class="form-select select2-init" data-dropdown-parent="body">
                    <option value="">كل الأحجام</option>
                    <option value="small">أقل من 50 MB</option>
                    <option value="medium">50 MB - 500 MB</option>
                    <option value="large">أكبر من 500 MB</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="sort_by" class="form-select select2-init" data-dropdown-parent="body">
                    <option value="newest">الأحدث أولاً</option>
                    <option value="oldest">الأقدم أولاً</option>
                    <option value="largest">الأكبر حجماً</option>
                    <option value="smallest">الأصغر حجماً</option>
                    <option value="az">أبجدياً (A-Z)</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filters -->
<div class="mb-4 d-flex gap-2 flex-wrap align-items-center">
    <span class="text-muted small me-1"><i class="fas fa-bolt text-warning me-1"></i> فلاتر سريعة:</span>
    <button type="button" class="btn btn-sm rounded-pill btn-outline-primary quick-filter-btn border" style="background: var(--bg-card);" data-filter="this_week">
        نسخ هذا الأسبوع
    </button>
    <button type="button" class="btn btn-sm rounded-pill btn-outline-success quick-filter-btn border" style="background: var(--bg-card);" data-filter="full_only">
        النسخ الشاملة فقط
    </button>
    <button type="button" class="btn btn-sm rounded-pill btn-outline-danger quick-filter-btn border" style="background: var(--bg-card);" data-filter="older_3_months">
        تجاوزت 3 أشهر
    </button>
    <button type="button" class="btn btn-sm rounded-pill btn-outline-secondary quick-filter-btn border" style="background: var(--bg-card);" data-filter="clear">
        إلغاء الفلاتر
    </button>
</div>

<div class="view-toolbar">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="text-muted small me-2">عرض:</span>
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M12 3v18"/><path d="M3 12h18"/></svg>
                الأعمدة
            </button>
            <div class="dropdown-menu shadow-sm p-3" style="min-width: 200px;">
                <h6 class="dropdown-header px-0 text-primary">إظهار / إخفاء الأعمدة</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_name" value="0" checked disabled>
                    <label class="form-check-label" for="col_name">اسم الملف</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_type" value="1" checked>
                    <label class="form-check-label" for="col_type">النوع</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_size" value="2" checked>
                    <label class="form-check-label" for="col_size">الحجم</label>
                </div>
                <div class="form-check mb-0">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_date" value="3" checked>
                    <label class="form-check-label" for="col_date">تاريخ الإنشاء</label>
                </div>
            </div>
        </div>
        
        <a href="{{ route('admin.backups.index') }}" class="btn btn-sm btn-outline-secondary" title="تحديث البيانات">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
        </a>
    </div>

    <div class="btn-group">
        <button type="button" class="btn btn-sm table-view-btn active" onclick="BackupsView.toggleView('table')" title="عرض جدول">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <button type="button" class="btn btn-sm grid-view-btn" onclick="BackupsView.toggleView('grid')" title="عرض شبكي">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </button>
    </div>
</div>

<div id="bulkActionsContainer" class="card mb-3 shadow-sm border-0 d-none fade-in">
    <div class="card-body py-2 d-flex align-items-center justify-content-between" style="background: rgba(59,130,246,0.05);">
        <div>
            <span class="fw-bold me-2">تم تحديد <span id="selectedCount" class="text-primary mx-1 fs-5">0</span> نسخ احتياطية</span>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-danger px-3" onclick="confirmBulkDelete()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                حذف المحدد
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="BackupsView.clearSelection()">إلغاء</button>
        </div>
    </div>
</div>

<!-- Table View -->
<div id="table-view-container" class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-view align-middle mb-0 w-100" id="backups-custom-table">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;"><input class="form-check-input" type="checkbox" id="selectAllCheckbox" onclick="BackupsView.toggleAll(this.checked)"></th>
                    <th class="border-bottom-0 col-toggle-0">اسم الملف</th>
                    <th class="border-bottom-0 col-toggle-1">النوع</th>
                    <th class="border-bottom-0 col-toggle-2">الحجم</th>
                    <th class="border-bottom-0 col-toggle-3">تاريخ الإنشاء</th>
                    <th class="border-bottom-0 text-center" style="width: 120px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="custom-backups-tbody">
                <!-- Rendered via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View -->
<div id="grid-view-container" class="d-none">
    <div class="row g-4" id="custom-backups-grid">
        <!-- Rendered via JS -->
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-4" id="custom-pagination"></div>

{{-- Hidden delete form (reused) --}}
<form id="deleteBackupForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('modals')
    @include('admin.backups.partials.create-modal')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/ar.js"></script>
<script>
window.BackupConfig = {
    data: @json(collect($backups)->values()),
    urls: {
        download: "{{ route('admin.backups.download', '__NAME__') }}",
        destroy:  "{{ route('admin.backups.destroy', '__NAME__') }}",
        bulkDestroy: "{{ route('admin.backups.bulk') }}"
    },
    userId: {{ auth()->id() ?? 0 }}
};

const ICONS = {
    file: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
    db: '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
    files: '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
    all: '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>',
    download: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
    trash: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>'
};
const TYPE_LABELS = { db: 'قاعدة البيانات', files: 'الملفات فقط', all: 'نسخة كاملة' };

function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

const BackupsView = {
    currentView: localStorage.getItem('backups_view_preference') || 'table',
    page: 1,
    selectedFiles: new Set(),

    toggleSelection(el) {
        if (el.checked) this.selectedFiles.add(el.value);
        else this.selectedFiles.delete(el.value);
        if (this.currentView === 'grid') {
            const card = el.closest('.data-view-card');
            if (card) {
                if (el.checked) card.style.cssText = 'border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary);';
                else card.style.cssText = '';
            }
        }
        this.updateBulkUI();
    },

    toggleAll(checked) {
        const checkboxes = document.querySelectorAll('.backup-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checked;
            if (checked) this.selectedFiles.add(cb.value);
            else this.selectedFiles.delete(cb.value);
            if (this.currentView === 'grid') {
                const card = cb.closest('.data-view-card');
                if (card) {
                    if (checked) card.style.cssText = 'border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary);';
                    else card.style.cssText = '';
                }
            }
        });
        this.updateBulkUI();
    },

    clearSelection() {
        this.selectedFiles.clear();
        document.querySelectorAll('.backup-checkbox, #selectAllCheckbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.data-view-card').forEach(card => card.style.cssText = '');
        this.updateBulkUI();
    },

    updateBulkUI() {
        const count = this.selectedFiles.size;
        const container = document.getElementById('bulkActionsContainer');
        const countEl = document.getElementById('selectedCount');
        const selectAll = document.getElementById('selectAllCheckbox');
        
        if (count > 0) {
            container.classList.remove('d-none');
            countEl.innerText = count;
        } else {
            container.classList.add('d-none');
        }

        if (selectAll) {
            const visibleCheckboxes = document.querySelectorAll('.backup-checkbox').length;
            const checkedCheckboxes = document.querySelectorAll('.backup-checkbox:checked').length;
            selectAll.checked = (visibleCheckboxes > 0 && visibleCheckboxes === checkedCheckboxes);
        }
    },

    init() {
        let t;
        document.getElementById('filter_search').addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => this.render(1), 300);
        });
        document.querySelectorAll('.col-toggle').forEach(cb => {
            cb.addEventListener('click', e => e.stopPropagation());
            cb.addEventListener('change', e => this.toggleColumn(e.target.value, e.target.checked));
        });

        window.backupDatePicker = flatpickr("#filter_date", {
            mode: "range",
            locale: "ar",
            dateFormat: "Y-m-d",
            onChange: (selectedDates) => {
                if (selectedDates.length === 2 || selectedDates.length === 0) {
                    this.render(1);
                }
            }
        });

        // Quick Filters Logic
        document.querySelectorAll('.quick-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                const setSelect = (id, val) => {
                    const el = document.getElementById(id);
                    el.value = val;
                    if (typeof jQuery !== 'undefined' && jQuery(el).hasClass('select2-hidden-accessible')) {
                        jQuery(el).trigger('change');
                    }
                };

                // Reset all
                document.getElementById('filter_search').value = '';
                setSelect('filter_type', '');
                setSelect('filter_size', '');
                setSelect('sort_by', 'newest');
                if (window.backupDatePicker) window.backupDatePicker.clear();

                const today = new Date();

                if (filter === 'this_week') {
                    const lastWeek = new Date();
                    lastWeek.setDate(today.getDate() - 7);
                    if (window.backupDatePicker) window.backupDatePicker.setDate([lastWeek, today]);
                } else if (filter === 'full_only') {
                    setSelect('filter_type', 'all');
                } else if (filter === 'older_3_months') {
                    const threeMonthsAgo = new Date();
                    threeMonthsAgo.setMonth(today.getMonth() - 3);
                    const longAgo = new Date();
                    longAgo.setFullYear(today.getFullYear() - 10);
                    if (window.backupDatePicker) window.backupDatePicker.setDate([longAgo, threeMonthsAgo]);
                    setSelect('sort_by', 'oldest');
                }

                BackupsView.render(1);
            });
        });

        this.applyViewPreference();
        this.render(1);
    },

    toggleView(mode) {
        this.currentView = mode;
        localStorage.setItem('backups_view_preference', mode);
        this.applyViewPreference();
    },

    applyViewPreference() {
        const isGrid = this.currentView === 'grid';
        document.querySelector('.table-view-btn').classList.toggle('active', !isGrid);
        document.querySelector('.grid-view-btn').classList.toggle('active', isGrid);
        document.getElementById('table-view-container').classList.toggle('d-none', isGrid);
        document.getElementById('grid-view-container').classList.toggle('d-none', !isGrid);
    },

    initColumnVisibility() {
        const prefs = JSON.parse(localStorage.getItem('backups_cols_preference') || '{}');
        document.querySelectorAll('.col-toggle').forEach(cb => {
            if (prefs[cb.value] !== undefined && !cb.disabled) {
                cb.checked = prefs[cb.value];
                this.toggleColumn(cb.value, prefs[cb.value], false);
            }
        });
    },

    toggleColumn(idx, visible, save = true) {
        document.querySelectorAll('.col-toggle-' + idx).forEach(el => el.classList.toggle('d-none', !visible));
        if (save) {
            const prefs = JSON.parse(localStorage.getItem('backups_cols_preference') || '{}');
            prefs[idx] = visible;
            localStorage.setItem('backups_cols_preference', JSON.stringify(prefs));
        }
    },

    filtered() {
        const q = document.getElementById('filter_search').value.trim().toLowerCase();
        const type = document.getElementById('filter_type').value;
        const size = document.getElementById('filter_size').value;
        const sortBy = document.getElementById('sort_by').value;
        let startDate = null;
        let endDate = null;

        if (window.backupDatePicker && window.backupDatePicker.selectedDates.length === 2) {
            startDate = new Date(window.backupDatePicker.selectedDates[0]);
            startDate.setHours(0, 0, 0, 0);
            endDate = new Date(window.backupDatePicker.selectedDates[1]);
            endDate.setHours(23, 59, 59, 999);
        }

        let results = window.BackupConfig.data.filter(b => {
            const matchSearch = !q || String(b.name).toLowerCase().includes(q);
            const matchType = !type || b.type === type;
            let matchDate = true;
            if (startDate && endDate) {
                const bDate = new Date(b.created_at_exact.replace(/-/g, '/'));
                matchDate = (bDate >= startDate && bDate <= endDate);
            }
            let matchSize = true;
            if (size) {
                const mb = (b.size_bytes || 0) / (1024 * 1024);
                if (size === 'small') matchSize = mb < 50;
                else if (size === 'medium') matchSize = mb >= 50 && mb <= 500;
                else if (size === 'large') matchSize = mb > 500;
            }
            return matchSearch && matchType && matchDate && matchSize;
        });

        results.sort((a, b) => {
            if (sortBy === 'newest') return new Date(b.created_at_exact.replace(/-/g, '/')) - new Date(a.created_at_exact.replace(/-/g, '/'));
            if (sortBy === 'oldest') return new Date(a.created_at_exact.replace(/-/g, '/')) - new Date(b.created_at_exact.replace(/-/g, '/'));
            if (sortBy === 'largest') return (b.size_bytes || 0) - (a.size_bytes || 0);
            if (sortBy === 'smallest') return (a.size_bytes || 0) - (b.size_bytes || 0);
            if (sortBy === 'az') return String(a.name).localeCompare(String(b.name));
            return 0;
        });

        return results;
    },

    render(page = 1) {
        const items = this.filtered();
        const perPage = parseInt(document.getElementById('filter_per_page').value || 10);
        const totalPages = Math.max(1, Math.ceil(items.length / perPage));
        this.page = Math.min(Math.max(1, page), totalPages);
        const slice = items.slice((this.page - 1) * perPage, this.page * perPage);

        this.renderTable(slice);
        this.renderGrid(slice);
        this.renderPagination(items.length, perPage, totalPages);
        this.initColumnVisibility();
        this.updateBulkUI();
    },

    badge(type) {
        const t = ['db', 'files'].includes(type) ? type : 'all';
        return `<span class="backup-badge ${t}">${ICONS[t]} ${TYPE_LABELS[t]}</span>`;
    },

    actions(name) {
        const enc = encodeURIComponent(name);
        const dlUrl = window.BackupConfig.urls.download.replace('__NAME__', enc);
        return `
            <div class="d-flex align-items-center justify-content-center gap-2">
                <button type="button" class="backup-action dl" title="تنزيل" onclick="downloadBackupAjax(this, '${dlUrl}', '${esc(name)}')">${ICONS.download}</button>
                <button type="button" class="backup-action del" title="حذف" data-name="${esc(name)}" onclick="confirmDeleteBackup(this.dataset.name)">${ICONS.trash}</button>
            </div>`;
    },

    renderTable(data) {
        const tbody = document.getElementById('custom-backups-tbody');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">
                <div class="mb-2" style="opacity:.6">${ICONS.file}</div>لا توجد نسخ احتياطية مطابقة</td></tr>`;
            return;
        }
        tbody.innerHTML = data.map(b => `
            <tr>
                <td><input class="form-check-input backup-checkbox" style="transform: scale(1.1);" type="checkbox" value="${esc(b.name)}" ${this.selectedFiles.has(b.name) ? 'checked' : ''} onchange="BackupsView.toggleSelection(this)"></td>
                <td class="col-toggle-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="backup-file-icon">${ICONS.file}</div>
                        <div>
                            <span class="backup-filename">${esc(b.name)}</span>
                            <span class="backup-filepath">storage/app/backups/</span>
                        </div>
                    </div>
                </td>
                <td class="col-toggle-1">${this.badge(b.type)}</td>
                <td class="col-toggle-2"><span class="fw-bold">${esc(b.size)}</span></td>
                <td class="col-toggle-3">
                    <span class="d-block fw-semibold" style="font-size:.85rem">${esc(b.created_at)}</span>
                    <span class="d-block text-muted" style="font-size:.72rem;direction:ltr;text-align:start">${esc(b.created_at_exact)}</span>
                </td>
                <td class="text-center">${this.actions(b.name)}</td>
            </tr>`).join('');
    },

    renderGrid(data) {
        const grid = document.getElementById('custom-backups-grid');
        if (!data.length) {
            grid.innerHTML = `<div class="col-12 text-center py-4 text-muted">لا توجد نسخ احتياطية مطابقة</div>`;
            return;
        }
        grid.innerHTML = data.map(b => {
            const t = ['db', 'files'].includes(b.type) ? b.type : 'all';
            return `
            <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                <div class="data-view-card h-100 d-flex flex-column" style="${this.selectedFiles.has(b.name) ? 'border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary);' : ''}">
                    <div class="backup-grid-head ${t}">
                        <div class="position-absolute top-0 start-0 p-2 z-index-1">
                            <input class="form-check-input backup-checkbox" style="transform: scale(1.2);" type="checkbox" value="${esc(b.name)}" ${this.selectedFiles.has(b.name) ? 'checked' : ''} onchange="BackupsView.toggleSelection(this)">
                        </div>
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">${ICONS[t].replace(/<\/?svg[^>]*>/g, '')}</svg>
                        <div class="position-absolute top-0 end-0 p-2 col-toggle-1">${this.badge(b.type)}</div>
                    </div>
                    <div class="card-body flex-grow-1">
                        <span class="backup-filename mb-3 col-toggle-0" title="${esc(b.name)}">${esc(b.name)}</span>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom small col-toggle-2">
                            <span class="text-muted">الحجم:</span><span class="fw-bold">${esc(b.size)}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small col-toggle-3">
                            <span class="text-muted">التاريخ:</span><span>${esc(b.created_at)}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 p-3 pt-0 text-center">${this.actions(b.name)}</div>
                </div>
            </div>`;
        }).join('');
    },

    renderPagination(total, perPage, totalPages) {
        const c = document.getElementById('custom-pagination');
        if (!total) { c.innerHTML = ''; return; }
        const start = (this.page - 1) * perPage + 1;
        const end = Math.min(this.page * perPage, total);
        let links = '<ul class="pagination custom-pagination mb-0">';
        links += `<li class="page-item ${this.page === 1 ? 'disabled' : ''}"><button class="page-link" onclick="BackupsView.render(${this.page - 1})">&laquo;</button></li>`;
        for (let i = 1; i <= totalPages; i++) {
            links += `<li class="page-item ${i === this.page ? 'active' : ''}"><button class="page-link" onclick="BackupsView.render(${i})">${i}</button></li>`;
        }
        links += `<li class="page-item ${this.page === totalPages ? 'disabled' : ''}"><button class="page-link" onclick="BackupsView.render(${this.page + 1})">&raquo;</button></li></ul>`;
        c.innerHTML = `<div class="text-muted small">عرض ${start} إلى ${end} من ${total} سجل</div>` + links;
    }
};

// ── Download ──
function downloadBackupAjax(btn, url, filename) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:btn-spin .9s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`;
    btn.style.pointerEvents = 'none';

    fetch(url, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.message || 'حدث خطأ أثناء التنزيل'); });
        }
        return response.blob();
    })
    .then(blob => {
        const urlObj = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = urlObj;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(urlObj);
        document.body.removeChild(a);
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'تم تنزيل النسخة بنجاح 📥', showConfirmButton: false, timer: 3000 });
    })
    .catch(error => {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: error.message, showConfirmButton: false, timer: 4000 });
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.style.pointerEvents = 'auto';
    });
}

// ── Delete ──
function confirmDeleteBackup(filename) {
    Swal.fire({
        title: 'حذف النسخة الاحتياطية؟',
        html: `<div class="text-muted mb-2" style="font-size:.88rem">هذا الإجراء لا يمكن التراجع عنه.</div><code style="font-size:.75rem;color:#ef4444;word-break:break-all">${esc(filename)}</code>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const url = window.BackupConfig.urls.destroy.replace('__NAME__', encodeURIComponent(filename));
            return fetch(url, {
                method: 'DELETE',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`فشل الحذف: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Remove from JS data
            window.BackupConfig.data = window.BackupConfig.data.filter(b => b.name !== filename);
            BackupsView.selectedFiles.delete(filename);
            // Re-render views
            BackupsView.render(BackupsView.page);
            // Show success toast
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.value.message, showConfirmButton: false, timer: 3000 });
        }
    });
}

function confirmBulkDelete() {
    const count = BackupsView.selectedFiles.size;
    if (count === 0) return;

    Swal.fire({
        title: 'تأكيد الحذف الجماعي',
        html: `<div class="text-muted mb-2">هل أنت متأكد من حذف <b>${count}</b> نسخ احتياطية؟<br>لا يمكن التراجع عن هذا الإجراء.</div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف المحدد',
        cancelButtonText: 'إلغاء',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(window.BackupConfig.urls.bulkDestroy, {
                method: 'DELETE',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ files: Array.from(BackupsView.selectedFiles) })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`فشل الحذف: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const deletedNames = Array.from(BackupsView.selectedFiles);
            window.BackupConfig.data = window.BackupConfig.data.filter(b => !deletedNames.includes(b.name));
            BackupsView.clearSelection();
            BackupsView.render(BackupsView.page);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.value.message, showConfirmButton: false, timer: 3000 });
        }
    });
}

// ── Option cards in modal ──
document.querySelectorAll('.backup-option-card').forEach(card => {
    const select = () => {
        document.querySelectorAll('.backup-option-card').forEach(el => el.classList.remove('selected'));
        card.classList.add('selected');
        document.getElementById('opt_' + card.dataset.value).checked = true;
    };
    card.addEventListener('click', select);
    card.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(); } });
});

// ── Progress helpers ──
const START_BTN_HTML = document.getElementById('startBackupBtn').innerHTML;
function setProgress(pct, text, state = 'running') {
    const bar = document.getElementById('backupProgressBar');
    const per = document.getElementById('backupProgressPercent');
    const colors = { running: 'var(--brand-blue)', completed: 'var(--success)', failed: 'var(--danger)' };
    if (pct !== null) { bar.style.width = pct + '%'; per.innerText = pct + '%'; }
    if (text) document.getElementById('backupProgressText').innerText = text;
    bar.style.background = state === 'running' ? 'linear-gradient(90deg, var(--brand-blue), #60a5fa)' : colors[state];
    per.style.color = colors[state];
}
function setBusy(busy) {
    const btn = document.getElementById('startBackupBtn');
    btn.disabled = busy;
    document.getElementById('cancelBackupBtn').disabled = busy;
    document.getElementById('closeBackupModalBtn').disabled = busy;
    btn.innerHTML = busy
        ? `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:btn-spin .9s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg><span>جاري التنفيذ...</span>`
        : START_BTN_HTML;
}
function failBackup(message) {
    setProgress(null, message, 'failed');
    setBusy(false);
    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: message, showConfirmButton: false, timer: 4000 });
}

// ── Automation Settings Submit (AJAX) ──
document.getElementById('automationSettingsForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('saveSettingsBtn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-2" style="animation:btn-spin .9s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> جاري الحفظ...`;

    fetch(this.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'حدث خطأ غير متوقع');
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
    })
    .catch(err => { 
        console.error('Settings Save Error:', err); 
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message, showConfirmButton: false, timer: 4000 });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
});

// ── Backup Form Submit (AJAX) ──
document.getElementById('backupForm').addEventListener('submit', function (e) {
    e.preventDefault();
    setBusy(true);
    document.getElementById('backupNotice').style.setProperty('display', 'none', 'important');
    document.getElementById('backupProgressContainer').style.display = 'block';
    setProgress(0, 'جاري الاتصال...');

    fetch(this.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'حدث خطأ غير متوقع');
        if (typeof window.Echo === 'undefined') {
            setProgress(100, data.message, 'completed');
            setTimeout(() => window.location.reload(), 1500);
        }
    })
    .catch(err => { console.error('Backup Error:', err); failBackup(err.message); });
});

// ── Init + WebSocket Progress ──
document.addEventListener('DOMContentLoaded', function () {
    BackupsView.init();

    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        const dir = $('html').attr('dir') || 'rtl';
        $('.select2-init').each(function () {
            $(this).select2({ dir, dropdownParent: $(document.body), minimumResultsForSearch: 10 });
        });
        $('#filter_type, #filter_size, #sort_by, #filter_per_page').on('change', () => BackupsView.render(1));
    } else {
        document.getElementById('filter_type').addEventListener('change', () => BackupsView.render(1));
        document.getElementById('filter_size').addEventListener('change', () => BackupsView.render(1));
        document.getElementById('sort_by').addEventListener('change', () => BackupsView.render(1));
        document.getElementById('filter_per_page').addEventListener('change', () => BackupsView.render(1));
    }

    if (typeof window.Echo !== 'undefined' && window.BackupConfig.userId) {
        window.Echo.private('App.Models.User.' + window.BackupConfig.userId)
            .listen('.BackupProgressUpdate', e => {
                if (e.progress === undefined) return;
                if (e.status === 'completed') {
                    setProgress(e.progress, e.message, 'completed');
                    setTimeout(() => window.location.reload(), 2000);
                } else if (e.status === 'failed') {
                    setProgress(e.progress, e.message, 'failed');
                    setBusy(false);
                } else {
                    setProgress(e.progress, e.message);
                }
            });
    }
});
</script>
@endsection
