@extends('layouts.admin')

@section('title', 'إدارة النسخ الاحتياطي')

@section('css')
<style>
/* ======================================================
   BACKUP PAGE — DESIGN SYSTEM ALIGNED STYLES
   Uses CSS custom properties from admin.css
====================================================== */

/* ── Compact Stat Cards ── */
.stat-card-compact {
    padding: 0.75rem 1rem !important;
    min-height: auto !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}
.stat-card-compact .stat-icon {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: var(--radius-sm, 8px) !important;
    margin-bottom: 0 !important;
}
.stat-card-compact .stat-icon svg {
    width: 18px !important;
    height: 18px !important;
}
.stat-card-compact .stat-value {
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
}
.stat-card-compact .stat-label {
    font-size: 0.72rem !important;
    margin-top: 0.1rem !important;
}

/* ── Option Selection Grid ── */
.backup-type-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.85rem;
    margin-bottom: 1rem;
}
@media (max-width: 767px) {
    .backup-type-grid { grid-template-columns: 1fr; }
}

/* ── Individual Option Card (Compact) ── */
.backup-option-card {
    position: relative;
    border: 2px solid var(--border);
    border-radius: var(--radius);
    padding: 0.85rem 1rem;
    cursor: pointer;
    transition: var(--transition);
    background: var(--bg-card);
    overflow: hidden;
    user-select: none;
    outline: none;
}
.backup-option-card input[type="radio"] { display: none; }

/* Sheen glow background layer */
.backup-option-card::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: var(--transition);
    pointer-events: none;
    border-radius: inherit;
}

/* Accent top bar */
.backup-option-card::after {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    inset-inline-end: 0;
    height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
    opacity: 0;
    transition: var(--transition);
}

.backup-option-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm, 0 4px 12px rgba(0,0,0,0.05));
}
.backup-option-card:hover::before { opacity: 1; }

/* Type: Database */
.backup-option-card.type-db:hover  { border-color: var(--brand-blue); box-shadow: 0 6px 20px rgba(59,130,246,0.12); }
.backup-option-card.type-db::before { background: radial-gradient(circle at 20% 20%, rgba(59,130,246,0.06) 0%, transparent 65%); }
.backup-option-card.type-db::after  { background: var(--brand-blue); }

/* Type: Files */
.backup-option-card.type-files:hover  { border-color: var(--brand-gold); box-shadow: 0 6px 20px rgba(245,158,11,0.12); }
.backup-option-card.type-files::before { background: radial-gradient(circle at 20% 20%, rgba(245,158,11,0.06) 0%, transparent 65%); }
.backup-option-card.type-files::after  { background: var(--brand-gold); }

/* Type: Full */
.backup-option-card.type-all:hover  { border-color: var(--success); box-shadow: 0 6px 20px rgba(16,185,129,0.12); }
.backup-option-card.type-all::before { background: radial-gradient(circle at 20% 20%, rgba(16,185,129,0.06) 0%, transparent 65%); }
.backup-option-card.type-all::after  { background: var(--success); }

/* Selected states */
.backup-option-card.selected { transform: translateY(-2px); }
.backup-option-card.selected::after { opacity: 1; }
.backup-option-card.selected.type-db    { border-color: var(--brand-blue); background: rgba(59,130,246,0.04);   box-shadow: 0 0 0 2px rgba(59,130,246,0.18), 0 6px 20px rgba(59,130,246,0.08); }
.backup-option-card.selected.type-files { border-color: var(--brand-gold); background: rgba(245,158,11,0.04);  box-shadow: 0 0 0 2px rgba(245,158,11,0.18), 0 6px 20px rgba(245,158,11,0.08); }
.backup-option-card.selected.type-all   { border-color: var(--success);    background: rgba(16,185,129,0.04);  box-shadow: 0 0 0 2px rgba(16,185,129,0.18), 0 6px 20px rgba(16,185,129,0.08); }

/* Check indicator (top-right) */
.backup-option-card .check-circle {
    position: absolute;
    top: 0.65rem;
    inset-inline-end: 0.65rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid var(--border-light);
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}
.backup-option-card .check-circle svg {
    width: 9px; height: 9px;
    color: #fff;
    opacity: 0;
    transform: scale(0);
    transition: var(--transition);
}
.backup-option-card.selected.type-db    .check-circle { background: var(--brand-blue); border-color: var(--brand-blue); }
.backup-option-card.selected.type-files .check-circle { background: var(--brand-gold); border-color: var(--brand-gold); }
.backup-option-card.selected.type-all   .check-circle { background: var(--success);    border-color: var(--success); }
.backup-option-card.selected .check-circle svg { opacity: 1; transform: scale(1); }

/* Icon container */
.backup-type-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}
.backup-option-card:hover .backup-type-icon,
.backup-option-card.selected .backup-type-icon { transform: scale(1.06); }
.backup-type-icon.db    { background: var(--brand-blue-glow); color: var(--brand-blue-light); }
.backup-type-icon.files { background: var(--brand-gold-glow); color: var(--brand-gold-light); }
.backup-type-icon.all   { background: var(--success-glow);    color: var(--success); }

.backup-option-card h6 {
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.25rem;
}
.backup-option-card p {
    font-size: 0.74rem;
    color: var(--text-secondary);
    line-height: 1.45;
    margin: 0;
}

/* Type pill label */
.backup-type-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.5rem;
    border-radius: 100px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    margin-top: 0.55rem;
}
.backup-type-pill.db    { background: var(--brand-blue-glow); color: var(--brand-blue-light); }
.backup-type-pill.files { background: var(--brand-gold-glow); color: var(--brand-gold-light); }
.backup-type-pill.all   { background: var(--success-glow);    color: var(--success); }

/* ── Start Button ── */
.btn-backup-start {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.7rem 1.75rem;
    background: linear-gradient(135deg, var(--brand-red), #991b1b);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 16px rgba(229,62,62,0.3);
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}
.btn-backup-start::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.13), transparent);
    transition: left 0.5s ease;
}
.btn-backup-start:hover { box-shadow: 0 8px 28px rgba(229,62,62,0.45); transform: translateY(-2px); }
.btn-backup-start:hover::before { left: 100%; }
.btn-backup-start:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

/* ── Backup Log Table ── */
.backup-log-table { width: 100%; }
.backup-log-table th {
    background: var(--th-bg);
    color: var(--text-muted);
    font-size: 0.67rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    padding: 0.8rem 1.25rem;
    border-bottom: 1px solid var(--border);
    text-align: start;
}
.backup-log-table td {
    padding: 0.95rem 1.25rem;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    font-size: 0.85rem;
}
.backup-log-table tr:last-child td { border-bottom: none; }
.backup-log-table tr:hover td { background: var(--bg-hover); }

.backup-file-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: var(--bg-hover);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted);
    flex-shrink: 0;
    transition: var(--transition);
}
.backup-log-table tr:hover .backup-file-icon {
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
    margin-top: 0.15rem;
    display: block;
    direction: ltr;
}

/* Type badge pills */
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

/* Action buttons */
.backup-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.38rem 0.8rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: var(--transition);
    white-space: nowrap;
    background: none;
}
.backup-action.dl {
    background: var(--brand-blue-glow);
    color: var(--brand-blue-light);
    border-color: rgba(59,130,246,0.2);
}
.backup-action.dl:hover { background: rgba(59,130,246,0.18); transform: translateY(-1px); }
.backup-action.del {
    background: var(--danger-glow);
    color: var(--danger);
    border-color: rgba(239,68,68,0.2);
}
.backup-action.del:hover { background: var(--btn-danger-hover); transform: translateY(-1px); }

/* Empty state */
.backup-empty {
    padding: 4rem 2rem;
    text-align: center;
}
.backup-empty-icon {
    width: 68px; height: 68px;
    border-radius: var(--radius-xl);
    background: var(--bg-hover);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    color: var(--text-muted);
}

@keyframes btn-spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<x-admin-header title="إدارة النسخ الاحتياطي" breadcrumb="النسخ الاحتياطي">
    <a href="{{ route('admin.backups.index') }}" class="btn btn-ghost">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
        تحديث
    </a>
</x-admin-header>

{{-- ===== STAT CARDS ===== --}}
<div class="row mb-3 g-2.5">
    {{-- DB Stat --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_count'] }}</div>
                <div class="stat-label">إجمالي النسخ</div>
            </div>
        </div>
    </div>
    {{-- Size Stat --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_size'] }}</div>
                <div class="stat-label">حجم النسخ</div>
            </div>
        </div>
    </div>
    {{-- Disk Stat --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['free_disk_space'] }}</div>
                <div class="stat-label">مساحة متاحة</div>
            </div>
        </div>
    </div>
    {{-- Last Backup Stat --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size:0.95rem !important;">{{ $stats['last_backup'] }}</div>
                <div class="stat-label">آخر نسخة</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== CREATE BACKUP CARD ===== --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center gap-3">
            <div style="width:34px;height:34px;border-radius:8px;background:var(--brand-red-glow);color:var(--brand-red-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v10m0 0l-3-3m3 3l3-3M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
            </div>
            <div>
                <h2 style="font-size:0.95rem;margin:0;">إنشاء نسخة احتياطية جديدة</h2>
                <p style="font-size:0.75rem;color:var(--text-secondary);margin:0;">اختر نوع البيانات التي تريد أرشفتها وحفظها على السيرفر</p>
            </div>
        </div>
        <span class="badge-info badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            مشفّر وآمن
        </span>
    </div>
    <div class="card-body">
        <form id="backupForm" action="{{ route('admin.backups.store') }}" method="POST">
            @csrf

            {{-- Backup Type Selection --}}
            <div class="backup-type-grid">

                {{-- Database Only --}}
                <div class="backup-option-card type-db selected" onclick="selectBackupOption('db', this)" role="button" tabindex="0" aria-pressed="true">
                    <input type="radio" name="option" value="db" id="opt_db" checked>
                    <div class="check-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="backup-type-icon db">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    </div>
                    <h6>قاعدة البيانات فقط</h6>
                    <p>تصدير جميع الجداول وتفريغ بيانات MySQL بصيغة SQL مضغوطة في حزمة ZIP.</p>
                    <div class="backup-type-pill db">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><ellipse cx="12" cy="5" rx="9" ry="3"/></svg>
                        MySQL · SQL Dump
                    </div>
                </div>

                {{-- Files Only --}}
                <div class="backup-option-card type-files" onclick="selectBackupOption('files', this)" role="button" tabindex="0" aria-pressed="false">
                    <input type="radio" name="option" value="files" id="opt_files">
                    <div class="check-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="backup-type-icon files">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                    </div>
                    <h6>الملفات المرفوعة فقط</h6>
                    <p>أرشفة الوسائط والصور والمستندات المحفوظة في مجلدات storage و uploads.</p>
                    <div class="backup-type-pill files">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        Storage · Uploads
                    </div>
                </div>

                {{-- Full Backup --}}
                <div class="backup-option-card type-all" onclick="selectBackupOption('all', this)" role="button" tabindex="0" aria-pressed="false">
                    <input type="radio" name="option" value="all" id="opt_all">
                    <div class="check-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="backup-type-icon all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <h6>النسخة الكاملة (الكل)</h6>
                    <p>دمج شامل يجمع قاعدة البيانات والملفات المرفوعة في حزمة واحدة متكاملة.</p>
                    <div class="backup-type-pill all">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        DB + Files · Full
                    </div>
                </div>

            </div>{{-- /backup-type-grid --}}

            {{-- Notice + Submit Row --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;padding:1rem 1.2rem;background:var(--bg-hover);border-radius:var(--radius);border:1px solid var(--border);">
                <div style="display:flex;align-items:flex-start;gap:0.6rem;color:var(--text-secondary);font-size:0.8rem;flex:1;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;color:var(--brand-gold-light);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>قد تستغرق عملية الأرشفة من <strong style="color:var(--text);">ثوانٍ إلى عدة دقائق</strong> حسب حجم البيانات. لا تغلق الصفحة أثناء التشغيل.</span>
                </div>
                <button type="submit" id="startBackupBtn" class="btn-backup-start">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                    <span>بدء النسخ الاحتياطي</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== BACKUPS LOG TABLE ===== --}}
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center gap-3">
            <div style="width:36px;height:36px;border-radius:10px;background:var(--success-glow);color:var(--success);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </div>
            <div>
                <h2 style="font-size:1rem;margin:0;">سجل النسخ الاحتياطية</h2>
                <p style="font-size:0.78rem;color:var(--text-secondary);margin:0;">جميع الملفات المحفوظة جاهزة للتنزيل أو الحذف</p>
            </div>
        </div>
        <span class="badge" style="background:var(--bg-hover);color:var(--text-secondary);border:1px solid var(--border-light);font-size:0.7rem;padding:0.3rem 0.7rem;">
            {{ count($backups) }} {{ count($backups) === 1 ? 'ملف' : 'ملفات' }}
        </span>
    </div>

    <div class="table-responsive">
        <table class="backup-log-table">
            <thead>
                <tr>
                    <th style="padding-inline-start:1.5rem;">اسم الملف</th>
                    <th>النوع</th>
                    <th>الحجم</th>
                    <th>تاريخ الإنشاء</th>
                    <th style="text-align:center;padding-inline-end:1.5rem;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr>
                    <td style="padding-inline-start:1.5rem;">
                        <div style="display:flex;align-items:center;gap:0.85rem;">
                            <div class="backup-file-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </div>
                            <div>
                                <span class="backup-filename">{{ $backup['name'] }}</span>
                                <span class="backup-filepath">storage/app/backups/</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($backup['type'] === 'db')
                            <span class="backup-badge db">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                قاعدة البيانات
                            </span>
                        @elseif($backup['type'] === 'files')
                            <span class="backup-badge files">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                الملفات فقط
                            </span>
                        @else
                            <span class="backup-badge all">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                نسخة كاملة
                            </span>
                        @endif
                    </td>
                    <td><span style="font-weight:700;color:var(--text);">{{ $backup['size'] }}</span></td>
                    <td>
                        <span style="display:block;font-weight:600;color:var(--text);font-size:0.85rem;">{{ $backup['created_at'] }}</span>
                        <span style="font-size:0.72rem;color:var(--text-muted);direction:ltr;display:block;">{{ $backup['created_at_exact'] }}</span>
                    </td>
                    <td style="text-align:center;padding-inline-end:1.5rem;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                            <a href="{{ route('admin.backups.download', $backup['name']) }}" class="backup-action dl" title="تنزيل الملف">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                تنزيل
                            </a>
                            <form action="{{ route('admin.backups.destroy', $backup['name']) }}" method="POST"
                                  onsubmit="return confirmDeleteBackup(event, '{{ $backup['name'] }}');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="backup-action del" title="حذف الملف">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="border:none;padding:0;">
                        <div class="backup-empty">
                            <div class="backup-empty-icon">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h6 style="font-weight:700;color:var(--text);margin-bottom:0.4rem;">لا توجد نسخ احتياطية بعد</h6>
                            <p style="font-size:0.82rem;color:var(--text-secondary);margin:0 auto;max-width:320px;">اختر نوع النسخ الاحتياطي من القسم أعلاه وانقر على "بدء النسخ الاحتياطي" لإنشاء أول نسخة.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
// ── Option Selection ──
function selectBackupOption(value, cardEl) {
    document.querySelectorAll('.backup-option-card').forEach(el => {
        el.classList.remove('selected');
        el.setAttribute('aria-pressed', 'false');
    });
    cardEl.classList.add('selected');
    cardEl.setAttribute('aria-pressed', 'true');
    document.getElementById('opt_' + value).checked = true;
}

// Keyboard support for cards
document.querySelectorAll('.backup-option-card').forEach(card => {
    card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
    });
});

// ── Form Submit ──
document.getElementById('backupForm').addEventListener('submit', function() {
    const btn = document.getElementById('startBackupBtn');
    const type = document.querySelector('input[name="option"]:checked')?.value;
    const msgs = { db: 'جاري تصدير قاعدة البيانات...', files: 'جاري أرشفة الملفات...', all: 'جاري إنشاء النسخة الكاملة...' };
    btn.disabled = true;
    btn.innerHTML = `
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             style="animation:btn-spin 0.9s linear infinite;flex-shrink:0;">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
        </svg>
        <span>${msgs[type] || 'جاري الأرشفة...'}</span>
    `;
});

// ── Delete Confirmation ──
function confirmDeleteBackup(e, filename) {
    e.preventDefault();
    const form = e.target;
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'حذف النسخة الاحتياطية',
            html: `<div style="color:var(--text-secondary,#64748b);font-size:0.88rem;margin-bottom:0.5rem;">هذا الإجراء لا يمكن التراجع عنه.</div><code style="font-size:0.75rem;color:#ef4444;display:block;word-break:break-all;">${filename}</code>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذف الملف',
            cancelButtonText: 'إلغاء'
        }).then(result => { if (result.isConfirmed) form.submit(); });
    } else {
        if (confirm('هل أنت متأكد من حذف: ' + filename + '؟')) form.submit();
    }
    return false;
}
</script>
@endsection
