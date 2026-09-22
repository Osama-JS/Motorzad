@extends('layouts.bidder')

@section('title', __('الاشتراك كبائع'))

@section('css')
<style>
    /* ═══════════════════════════════════════════════════════════════
       Ultra Premium Seller Subscription UI — SaaS Grade
       ═══════════════════════════════════════════════════════════════ */

    .seller-page-wrapper {
        max-width: 960px;
        margin: 0 auto;
        padding: 1.5rem 0 5rem;
        position: relative;
    }

    /* ─── Ambient Background ─── */
    .seller-page-wrapper::before {
        content: '';
        position: fixed;
        top: 0; left: 0; right: 0;
        height: 100vh;
        background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(245,158,11,0.07) 0%, transparent 60%),
                    radial-gradient(ellipse 60% 40% at 80% 30%, rgba(239,68,68,0.04) 0%, transparent 50%),
                    radial-gradient(ellipse 60% 40% at 20% 60%, rgba(59,130,246,0.03) 0%, transparent 50%);
        z-index: -1;
        pointer-events: none;
    }

    /* ─── Hero Card ─── */
    .hero-card {
        background: var(--bg-card);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.03);
        position: relative;
    }
    html[data-bs-theme="dark"] .hero-card {
        border-color: rgba(255,255,255,0.06);
        background: linear-gradient(160deg, var(--bg-card) 0%, rgba(15,15,25,0.7) 100%);
    }

    /* Animated top bar */
    .hero-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f59e0b, #ef4444, #8b5cf6, #3b82f6, #f59e0b);
        background-size: 300% auto;
        animation: shimmer 4s linear infinite;
    }
    @keyframes shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 300% 50%; }
    }

    /* ─── Hero Inner ─── */
    .hero-inner {
        padding: 3.5rem 3rem 3rem;
        text-align: center;
    }
    @media(max-width: 768px) {
        .hero-inner { padding: 2.5rem 1.5rem 2rem; }
    }

    /* Floating icon */
    .hero-float-icon {
        width: 88px; height: 88px;
        border-radius: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        position: relative;
        background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(239,68,68,0.06) 100%);
        color: #f59e0b;
        box-shadow: 0 8px 30px rgba(245,158,11,0.12);
        transform: rotate(-6deg);
        transition: all 0.5s cubic-bezier(.175,.885,.32,1.275);
    }
    .hero-float-icon:hover {
        transform: rotate(0deg) scale(1.08);
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        box-shadow: 0 16px 40px rgba(245,158,11,0.28);
    }
    .hero-float-icon svg { width: 42px; height: 42px; }

    /* Titles */
    .hero-title {
        font-size: 2.25rem;
        font-weight: 900;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
        background: linear-gradient(135deg, var(--text) 30%, var(--text-muted) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.3;
    }
    .hero-subtitle {
        font-size: 1.05rem;
        color: var(--text-muted);
        max-width: 620px;
        margin: 0 auto;
        line-height: 1.75;
    }
    @media(max-width: 768px) {
        .hero-title { font-size: 1.75rem; }
    }

    /* ─── Status Chips ─── */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 24px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 1.25rem;
    }
    .chip-active {
        background: rgba(16,185,129,0.08);
        color: #059669;
        border: 1px solid rgba(16,185,129,0.2);
    }
    .chip-pending {
        background: rgba(245,158,11,0.08);
        color: #d97706;
        border: 1px solid rgba(245,158,11,0.2);
    }

    /* ─── Alert Banners ─── */
    .alert-banner {
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        text-align: right;
        margin: 0 2.5rem 0;
    }
    html[dir="ltr"] .alert-banner { text-align: left; }
    @media(max-width: 768px) { .alert-banner { margin: 0 1rem; } }

    .alert-banner-rejected {
        background: rgba(239,68,68,0.04);
        border: 1px solid rgba(239,68,68,0.15);
    }
    .alert-banner-action {
        background: rgba(249,115,22,0.04);
        border: 1px solid rgba(249,115,22,0.15);
    }
    .alert-banner .alert-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .alert-banner-rejected .alert-icon { background: rgba(239,68,68,0.08); color: #ef4444; }
    .alert-banner-action .alert-icon  { background: rgba(249,115,22,0.08); color: #ea580c; }
    .alert-banner h5 { font-weight: 800; font-size: 0.95rem; margin-bottom: 4px; }
    .alert-banner-rejected h5 { color: #dc2626; }
    .alert-banner-action h5  { color: #ea580c; }
    .alert-banner p { margin: 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.65; }

    .admin-note-box {
        background: rgba(0,0,0,0.03);
        border-radius: 10px;
        padding: 10px 14px;
        margin-top: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 0.9rem;
        border-inline-start: 3px solid #ea580c;
    }
    html[data-bs-theme="dark"] .admin-note-box { background: rgba(255,255,255,0.04); }

    /* ─── Features Section ─── */
    .features-section {
        padding: 2.5rem 2.5rem 0;
    }
    @media(max-width: 768px) { .features-section { padding: 2rem 1.25rem 0; } }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    @media(max-width: 640px) {
        .features-grid { grid-template-columns: 1fr; }
    }

    .feat-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 1.25rem;
        border-radius: 18px;
        border: 1px solid rgba(0,0,0,0.04);
        background: rgba(255,255,255,0.02);
        transition: all 0.35s ease;
        text-align: right;
    }
    html[dir="ltr"] .feat-card { text-align: left; }
    html[data-bs-theme="dark"] .feat-card { border-color: rgba(255,255,255,0.04); background: rgba(0,0,0,0.1); }
    .feat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.05);
        border-color: rgba(245,158,11,0.25);
        background: var(--bg-card);
    }
    .feat-icon {
        width: 44px; height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    .feat-card:hover .feat-icon {
        transform: scale(1.08) rotate(5deg);
        color: #fff !important;
    }
    .feat-card:nth-child(1) .feat-icon { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .feat-card:nth-child(1):hover .feat-icon { background: #f59e0b; }
    .feat-card:nth-child(2) .feat-icon { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .feat-card:nth-child(2):hover .feat-icon { background: #3b82f6; }
    .feat-card:nth-child(3) .feat-icon { background: rgba(16,185,129,0.1); color: #10b981; }
    .feat-card:nth-child(3):hover .feat-icon { background: #10b981; }
    .feat-card:nth-child(4) .feat-icon { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .feat-card:nth-child(4):hover .feat-icon { background: #8b5cf6; }
    .feat-card h4 { font-size: 0.95rem; font-weight: 800; margin: 0 0 4px; color: var(--text); }
    .feat-card p { font-size: 0.85rem; margin: 0; color: var(--text-muted); line-height: 1.55; }

    /* ─── Form Section ─── */
    .form-section {
        padding: 2rem 2.5rem 2.5rem;
    }
    @media(max-width: 768px) { .form-section { padding: 1.5rem 1.25rem 2rem; } }

    .form-section-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 2rem;
    }
    .form-section-divider::before,
    .form-section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0,0,0,0.08), transparent);
    }
    html[data-bs-theme="dark"] .form-section-divider::before,
    html[data-bs-theme="dark"] .form-section-divider::after {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
    }
    .form-section-divider span {
        font-weight: 800;
        font-size: 0.85rem;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* Step Progress */
    .step-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 2.5rem;
    }
    .step-dot {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        transition: all 0.35s ease;
        position: relative;
        z-index: 1;
    }
    .step-dot.active {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        box-shadow: 0 4px 14px rgba(245,158,11,0.25);
        transform: scale(1.1);
    }
    .step-dot.completed {
        background: #10b981;
        color: #fff;
        box-shadow: 0 4px 12px rgba(16,185,129,0.2);
    }
    .step-dot.inactive {
        background: rgba(0,0,0,0.06);
        color: var(--text-muted);
    }
    html[data-bs-theme="dark"] .step-dot.inactive { background: rgba(255,255,255,0.08); }

    .step-line {
        width: 48px; height: 3px;
        border-radius: 3px;
        transition: all 0.35s ease;
    }
    .step-line.completed { background: #10b981; }
    .step-line.inactive  { background: rgba(0,0,0,0.06); }
    html[data-bs-theme="dark"] .step-line.inactive { background: rgba(255,255,255,0.06); }

    /* Form Input Fields */
    .form-field-group {
        margin-bottom: 1.5rem;
    }
    .form-field-group label.field-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text);
        margin-bottom: 8px;
    }
    .form-field-group .field-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .form-field-group .form-control,
    .form-field-group .form-select {
        border-radius: 14px;
        padding: 12px 16px;
        border: 1.5px solid rgba(0,0,0,0.08);
        background: rgba(0,0,0,0.01);
        font-size: 0.95rem;
        color: var(--text);
        transition: all 0.25s ease;
    }
    html[data-bs-theme="dark"] .form-field-group .form-control,
    html[data-bs-theme="dark"] .form-field-group .form-select {
        border-color: rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.03);
    }
    .form-field-group .form-control:focus,
    .form-field-group .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
    }
    .form-field-group .form-control.is-invalid,
    .form-field-group .form-select.is-invalid {
        border-color: #ef4444;
    }

    /* File Upload */
    .file-upload-zone {
        border: 2px dashed rgba(0,0,0,0.1);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    html[data-bs-theme="dark"] .file-upload-zone { border-color: rgba(255,255,255,0.1); }
    .file-upload-zone:hover {
        border-color: #f59e0b;
        background: rgba(245,158,11,0.02);
    }
    .file-upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Primary CTA */
    .btn-primary-cta {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        border: none;
        padding: 14px 36px;
        font-size: 1.05rem;
        font-weight: 800;
        border-radius: 100px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(.175,.885,.32,1.275);
        box-shadow: 0 8px 24px rgba(245,158,11,0.25);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-primary-cta:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 14px 32px rgba(245,158,11,0.35);
        color: #fff;
    }
    .btn-primary-cta:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary-cta {
        background: transparent;
        border: 2px solid rgba(0,0,0,0.1);
        color: var(--text);
        padding: 14px 28px;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 100px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    html[data-bs-theme="dark"] .btn-secondary-cta { border-color: rgba(255,255,255,0.1); }
    .btn-secondary-cta:hover {
        background: rgba(0,0,0,0.03);
        border-color: rgba(0,0,0,0.2);
        color: var(--text);
    }

    .btn-draft-cta {
        background: transparent;
        border: 2px solid rgba(245,158,11,0.3);
        color: #d97706;
        padding: 14px 28px;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 100px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-draft-cta:hover {
        background: rgba(245,158,11,0.05);
        border-color: #f59e0b;
        color: #b45309;
    }

    .btn-green-cta {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 8px 24px rgba(16,185,129,0.25);
    }
    .btn-green-cta:hover {
        box-shadow: 0 14px 32px rgba(16,185,129,0.35);
        color: #fff;
    }

    .btn-next-cta {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 8px 24px rgba(59,130,246,0.25);
    }
    .btn-next-cta:hover {
        box-shadow: 0 14px 32px rgba(59,130,246,0.35);
        color: #fff;
    }

    /* Action Footer */
    .action-footer {
        display: flex;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 2rem;
        border-top: 1px solid rgba(0,0,0,0.06);
        margin-top: 1rem;
    }
    html[data-bs-theme="dark"] .action-footer { border-color: rgba(255,255,255,0.06); }

    /* Decorative Ring */
    .hero-float-icon::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 30px;
        border: 1.5px solid rgba(245,158,11,0.2);
        animation: ringPulse 2.5s ease-in-out infinite;
    }
    @keyframes ringPulse {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.1; transform: scale(1.08); }
    }

    /* No template alert */
    .no-template-alert {
        background: rgba(245,158,11,0.04);
        border: 1px dashed rgba(245,158,11,0.3);
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        margin: 0 2.5rem 2rem;
    }
</style>
@endsection

@section('content')
<div class="seller-page-wrapper">
    <div class="hero-card">

        {{-- ═══════ HERO SECTION ═══════ --}}
        <div class="hero-inner">
            <div class="hero-float-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>

            <h1 class="hero-title">{{ app()->getLocale() === 'ar' ? 'انطلق كبائع محترف' : 'Start as a Professional Seller' }}</h1>

            {{-- ══════════════════════════════════════════════
                 STATE 1 — Already a Seller
                 ══════════════════════════════════════════════ --}}
            @if($isSeller)
                <div class="status-chip chip-active">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    {{ app()->getLocale() === 'ar' ? 'أنت مشترك كبائع بالفعل' : 'You are already a seller' }}
                </div>
                <p class="hero-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'حسابك يمتلك صلاحيات البائع بالكامل. يمكنك الآن الوصول إلى لوحة تحكم البائعين لإضافة مركباتك وإدارة مزاداتك ومتابعة الأرباح.' : 'Your account has full seller privileges. You can now access the seller dashboard to add your vehicles, manage auctions, and track earnings.' }}
                </p>
                <a href="{{ route('bidder.garage.index') }}" class="btn-primary-cta btn-green-cta mt-2">
                    {{ app()->getLocale() === 'ar' ? 'الذهاب إلى كراج البائع' : 'Go to Seller Garage' }}
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                </a>

            {{-- ══════════════════════════════════════════════
                 STATE 2 — Pending / Under Review
                 ══════════════════════════════════════════════ --}}
            @elseif(isset($pendingRequest) && $pendingRequest)
                <div class="status-chip chip-pending">
                    @if($pendingRequest->status === 'under_review')
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 1.1rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'طلبك قيد المراجعة حالياً' : 'Your request is currently under review' }}
                    @else
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        {{ app()->getLocale() === 'ar' ? 'طلبك قيد الانتظار' : 'Your request is pending' }}
                    @endif
                </div>
                <p class="hero-subtitle">
                    @if($pendingRequest->status === 'under_review')
                        {{ app()->getLocale() === 'ar' ? 'طلبك قيد المراجعة الان من قبل الإدارة. سيتم إشعارك قريباً.' : 'Your request is currently under review by the administration. You will be notified soon.' }}
                    @else
                        {{ app()->getLocale() === 'ar' ? 'لقد استلمنا طلب الترقية الخاص بك وبانتظار المراجعة. سيتم إشعارك فور الموافقة.' : 'We have received your upgrade request and it is awaiting review. You will be notified upon approval.' }}
                    @endif
                </p>

            {{-- ══════════════════════════════════════════════
                 STATE 3 — Can Submit (New / Rejected / Action Required)
                 ══════════════════════════════════════════════ --}}
            @else
                <p class="hero-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'قم بترقية حسابك لتتمكن من إضافة سياراتك وعرضها في منصة موترزاد والوصول لآلاف المشترين المحتملين.' : 'Upgrade your account to add and display your cars on the Motorzad platform and reach thousands of potential buyers.' }}
                </p>
            @endif
        </div>

        {{-- ═══════ ALERT BANNERS (Action Required / Rejected) ═══════ --}}
        @if(!$isSeller && !(isset($pendingRequest) && $pendingRequest))
            @if(isset($actionRequiredRequest) && $actionRequiredRequest)
                <div class="alert-banner alert-banner-action mb-3">
                    <div class="alert-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div style="flex: 1;">
                        <h5>{{ app()->getLocale() === 'ar' ? 'مطلوب إجراء أو تعديل' : 'Action Required' }}</h5>
                        <p>
                            {{ app()->getLocale() === 'ar' ? 'لقد طلبت الإدارة تعديلاً على طلبك. يرجى تعديل الحقول أدناه وإعادة الإرسال.' : 'The administration requested a modification. Please edit the fields below and resubmit.' }}
                        </p>
                        @if($actionRequiredRequest->admin_notes)
                            <div class="admin-note-box">{{ $actionRequiredRequest->admin_notes }}</div>
                        @endif
                    </div>
                </div>
            @endif

            @if(isset($rejectedRequest) && $rejectedRequest && !isset($actionRequiredRequest))
                <div class="alert-banner alert-banner-rejected mb-3">
                    <div class="alert-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div style="flex: 1;">
                        <h5>{{ app()->getLocale() === 'ar' ? 'تم رفض طلبك السابق' : 'Your previous request was rejected' }}</h5>
                        <p>
                            <strong>{{ app()->getLocale() === 'ar' ? 'السبب:' : 'Reason:' }}</strong>
                            {{ $rejectedRequest->admin_notes ?? (app()->getLocale() === 'ar' ? 'لم يتم توضيح سبب محدد' : 'No specific reason provided') }}
                        </p>
                        <p class="mt-1" style="font-size: 0.85rem; opacity: 0.8;">
                            {{ app()->getLocale() === 'ar' ? 'يمكنك تقديم طلب جديد الآن وتأكد من استيفاء جميع الشروط.' : 'You can submit a new request now, ensuring all conditions are met.' }}
                        </p>
                    </div>
                </div>
            @endif
        @endif

        {{-- ═══════ FEATURES GRID ═══════ --}}
        @if(!$isSeller && !(isset($pendingRequest) && $pendingRequest))
            <div class="features-section">
                <div class="features-grid">
                    <div class="feat-card">
                        <div class="feat-icon">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <h4>{{ app()->getLocale() === 'ar' ? 'عرض متميز للسيارات' : 'Premium Car Display' }}</h4>
                            <p>{{ app()->getLocale() === 'ar' ? 'احصل على مساحة لعرض سياراتك بصور عالية الدقة وتفاصيل شاملة تجذب المشترين.' : 'Display your cars with high-res photos and comprehensive details that attract buyers.' }}</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h4>{{ app()->getLocale() === 'ar' ? 'وصول واسع للمشترين' : 'Broad Buyer Reach' }}</h4>
                            <p>{{ app()->getLocale() === 'ar' ? 'تصل مزاداتك لآلاف المزايدين الموثقين الجاهزين للشراء يومياً.' : 'Your auctions reach thousands of verified bidders ready to buy daily.' }}</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <h4>{{ app()->getLocale() === 'ar' ? 'عوائد أعلى لسيارتك' : 'Higher Returns' }}</h4>
                            <p>{{ app()->getLocale() === 'ar' ? 'نظام المزايدة التنافسي يضمن لك أفضل قيمة سوقية لسيارتك.' : 'Our competitive bidding system ensures the best market value for your car.' }}</p>
                        </div>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h4>{{ app()->getLocale() === 'ar' ? 'معاملات آمنة وموثوقة' : 'Secure Transactions' }}</h4>
                            <p>{{ app()->getLocale() === 'ar' ? 'نضمن حقوقك المالية عبر نظام محفظة إلكتروني آمن وعمليات دفع موثقة.' : 'We guarantee your financial rights through a secure electronic wallet system.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════ FORM SECTION ═══════ --}}
        @if(!$isSeller && !(isset($pendingRequest) && $pendingRequest))
            <div class="form-section">

                <div class="form-section-divider">
                    <span>{{ app()->getLocale() === 'ar' ? 'نموذج التسجيل' : 'Registration Form' }}</span>
                </div>

                @if($template)
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4" style="font-size: 0.9rem;">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $totalSteps = 1;
                        if($template) {
                            $totalSteps = 1 + $template->fields->where('type', 'step-divider')->count();
                        }
                    @endphp

                    <form action="{{ route('bidder.become-seller.store') }}" method="POST" enctype="multipart/form-data"
                        @submit="submitForm($event)"
                        x-data="{
                            formData: {{ Js::from(old('data', isset($actionRequiredRequest) && $actionRequiredRequest ? $actionRequiredRequest->data : ($draftRequest ? $draftRequest->data : ($rejectedRequest ? $rejectedRequest->data : [])))) }},
                            currentStep: 1,
                            totalSteps: {{ $totalSteps }},
                            isSubmitting: false,
                            nextStep() {
                                let isValid = true;
                                const elements = this.$el.querySelectorAll('input, select, textarea');
                                for (let el of elements) {
                                    if (el.offsetParent !== null && el.required) {
                                        if (!el.checkValidity()) {
                                            el.reportValidity();
                                            isValid = false;
                                            break;
                                        }
                                    }
                                }
                                if (isValid) {
                                    if (this.currentStep < this.totalSteps) this.currentStep++;
                                    window.scrollTo({ top: document.querySelector('.hero-card').offsetTop - 50, behavior: 'smooth' });
                                }
                            },
                            prevStep() {
                                if (this.currentStep > 1) this.currentStep--;
                                window.scrollTo({ top: document.querySelector('.hero-card').offsetTop - 50, behavior: 'smooth' });
                            },
                            submitForm(e) {
                                if (this.isSubmitting) {
                                    e.preventDefault();
                                    return;
                                }
                                this.isSubmitting = true;
                                const elements = this.$el.querySelectorAll('input, select, textarea');
                                for (let el of elements) {
                                    if (el.offsetParent === null) {
                                        el.disabled = true;
                                    }
                                }
                            }
                        }">
                        @csrf
                        <input type="hidden" name="template_id" value="{{ $template->id }}">

                        {{-- Step Progress Indicator --}}
                        @if($totalSteps > 1)
                            <div class="step-progress mb-4">
                                @for($s = 1; $s <= $totalSteps; $s++)
                                    <div class="step-dot"
                                         :class="{
                                            'active': currentStep === {{ $s }},
                                            'completed': currentStep > {{ $s }},
                                            'inactive': currentStep < {{ $s }}
                                         }">
                                        <template x-if="currentStep > {{ $s }}">
                                            <i class="fa-solid fa-check" style="font-size: 0.7rem;"></i>
                                        </template>
                                        <template x-if="currentStep <= {{ $s }}">
                                            <span>{{ $s }}</span>
                                        </template>
                                    </div>
                                    @if($s < $totalSteps)
                                        <div class="step-line"
                                             :class="currentStep > {{ $s }} ? 'completed' : 'inactive'"></div>
                                    @endif
                                @endfor
                            </div>
                        @endif

                        {{-- Dynamic Fields --}}
                        @php $currentStepNumber = 1; @endphp
                        @foreach($template->fields as $field)
                            @if($field->type === 'step-divider')
                                @php $currentStepNumber++; @endphp
                            @else
                                <div class="form-field-group" x-show="currentStep === {{ $currentStepNumber }} {{ $field->depends_on_field_name ? '&& formData[\''. $field->depends_on_field_name .'\'] == \''. addslashes($field->depends_on_value) .'\'' : '' }}" x-transition>

                                    @if($field->type !== 'html')
                                        <label class="field-label">
                                            {{ $field->label }}
                                            @if($field->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        @if($field->description && !in_array($field->type, ['html']))
                                            <div class="field-desc">{{ $field->description }}</div>
                                        @endif
                                    @endif

                                    @if($field->type === 'text')
                                        <input type="text" name="data[{{ $field->name }}]" x-model="formData['{{ $field->name }}']" class="form-control @error('data.'.$field->name) is-invalid @enderror" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'textarea')
                                        <textarea name="data[{{ $field->name }}]" x-model="formData['{{ $field->name }}']" class="form-control @error('data.'.$field->name) is-invalid @enderror" rows="3" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}"></textarea>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'file')
                                        <div class="file-upload-zone" x-data="{ fileName: '' }">
                                            <input type="file" name="data[{{ $field->name }}]" class="@error('data.'.$field->name) is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}" @change="fileName = $event.target.files[0]?.name || ''">
                                            <div>
                                                <i class="fa-solid fa-cloud-arrow-up text-muted mb-2" style="font-size: 2rem;"></i>
                                                <p class="mb-1 fw-bold text-muted" style="font-size: 0.9rem;" x-text="fileName || '{{ app()->getLocale() === 'ar' ? 'اسحب الملف هنا أو اضغط للرفع' : 'Drag file here or click to upload' }}'"></p>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ app()->getLocale() === 'ar' ? 'يسمح بصور أو PDF (بحد أقصى 10 ميجابايت)' : 'Images or PDF allowed (max 10MB)' }}</small>
                                            </div>
                                        </div>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                                        @if(old() && $errors->any()) <div class="text-warning small mt-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> لقد قمت برفع الملف سابقاً، يرجى إعادة رفعه مرة أخرى بسبب خطأ في الإرسال.</div> @endif

                                    @elseif($field->type === 'date')
                                        <input type="date" name="data[{{ $field->name }}]" x-model="formData['{{ $field->name }}']" class="form-control @error('data.'.$field->name) is-invalid @enderror" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'select')
                                        <select name="data[{{ $field->name }}]" x-model="formData['{{ $field->name }}']" class="form-select @error('data.'.$field->name) is-invalid @enderror" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                            <option value="" disabled>-- {{ app()->getLocale() === 'ar' ? 'يرجى الاختيار' : 'Please select' }} --</option>
                                            @if(is_array($field->options))
                                                @foreach($field->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'multi-select')
                                        <select name="data[{{ $field->name }}][]" x-model="formData['{{ $field->name }}']" class="form-select @error('data.'.$field->name) is-invalid @enderror" multiple style="height: 110px;" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                            @if(is_array($field->options))
                                                @foreach($field->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">{{ app()->getLocale() === 'ar' ? 'يمكنك اختيار أكثر من عنصر عبر الضغط على (Ctrl) أو (Cmd)' : 'You can select multiple items by holding Ctrl or Cmd' }}</small>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'radio')
                                        <div class="d-flex flex-wrap gap-3 mt-1">
                                            @if(is_array($field->options))
                                                @foreach($field->options as $idx => $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('data.'.$field->name) is-invalid @enderror" type="radio" name="data[{{ $field->name }}]" value="{{ $option }}" x-model="formData['{{ $field->name }}']" id="rad_{{ $field->name }}_{{ $idx }}" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                                        <label class="form-check-label" for="rad_{{ $field->name }}_{{ $idx }}" style="color: var(--text);">{{ $option }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'checkbox')
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input @error('data.'.$field->name) is-invalid @enderror" type="checkbox" name="data[{{ $field->name }}]" value="1" x-model="formData['{{ $field->name }}']" id="chk_{{ $field->name }}" :required="{{ $field->is_required ? 'currentStep === '.$currentStepNumber : 'false' }}">
                                            <label class="form-check-label fw-bold" for="chk_{{ $field->name }}" style="color: var(--text);">{{ __('نعم، أوافق') }}</label>
                                        </div>
                                        @error('data.'.$field->name) <span class="text-danger small mt-1 d-block fw-bold">{{ $message }}</span> @enderror

                                    @elseif($field->type === 'html')
                                        @if($field->label)
                                            <h5 class="fw-bold mb-2" style="color: var(--text);">{{ $field->label }}</h5>
                                        @endif
                                        <div class="p-3 rounded-4 mb-1" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05); color: var(--text-muted); line-height: 1.8; font-size: 0.9rem;">
                                            {!! $field->description !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        {{-- Action Footer --}}
                        <div class="action-footer">
                            <button type="button" x-show="currentStep > 1" @click="prevStep()" class="btn-secondary-cta">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"></path></svg>
                                {{ __('السابق') }}
                            </button>

                            <div x-show="currentStep === 1 && totalSteps > 1" class="d-none d-md-block"></div>

                            <button type="button" x-show="currentStep < totalSteps" @click="nextStep()" class="btn-primary-cta btn-next-cta flex-grow-1 justify-content-center mx-md-4">
                                {{ __('التالي') }}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"></path></svg>
                            </button>

                            <div x-show="currentStep === totalSteps" class="d-flex flex-grow-1 gap-3 flex-column flex-md-row">
                                <button type="submit" name="is_draft" value="1" formnovalidate class="btn-draft-cta flex-grow-1 justify-content-center" :disabled="isSubmitting">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                    {{ app()->getLocale() === 'ar' ? 'حفظ كمسودة' : 'Save as Draft' }}
                                </button>

                                <button type="submit" class="btn-primary-cta flex-grow-1 justify-content-center m-0" :disabled="isSubmitting">
                                    <span x-show="!isSubmitting">{{ app()->getLocale() === 'ar' ? 'إرسال طلب التسجيل' : 'Submit Registration' }}</span>
                                    <span x-show="isSubmitting">{{ app()->getLocale() === 'ar' ? 'جاري الإرسال...' : 'Submitting...' }}</span>
                                    <svg x-show="!isSubmitting" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                    <div x-show="isSubmitting" class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="no-template-alert">
                        <i class="fa-solid fa-circle-info text-warning mb-2" style="font-size: 1.5rem;"></i>
                        <p class="fw-bold mb-0" style="color: var(--text);">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد نموذج تسجيل متاح حالياً. يرجى التواصل مع الإدارة.' : 'No registration form is currently available. Please contact administration.' }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
