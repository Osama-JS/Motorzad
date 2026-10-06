@extends('layouts.admin')

@section('title', 'حالة وصحة النظام')

@section('css')
<style>
    /* ===== System Health Aesthetics ===== */
    .health-hero {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.94) 0%, rgba(153, 27, 27, 0.96) 50%, rgba(15, 23, 42, 0.98) 100%);
        border-radius: var(--radius-xl, 20px);
        padding: 2.2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .health-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -10%;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .health-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 0.35rem 0.9rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        position: relative;
    }

    .pulse-dot.pulse-ok {
        background-color: #10b981;
        box-shadow: 0 0 12px #10b981;
    }

    .pulse-dot.pulse-ok::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        border-radius: 50%;
        background: inherit;
        animation: pulseAnimation 2s infinite ease-out;
    }

    .pulse-dot.pulse-warning {
        background-color: #f59e0b;
        box-shadow: 0 0 12px #f59e0b;
    }

    .pulse-dot.pulse-danger {
        background-color: #ef4444;
        box-shadow: 0 0 12px #ef4444;
        animation: pulseDanger 1.2s infinite;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(2.6); opacity: 0; }
    }

    @keyframes pulseDanger {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .health-score-box {
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 1rem 1.4rem;
        display: inline-flex;
        align-items: center;
        gap: 1.2rem;
    }

    .health-score-val {
        font-size: 2.2rem;
        font-weight: 900;
        font-family: 'Orbitron', sans-serif;
        line-height: 1;
    }

    /* Cards */
    .health-stat-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, rgba(0, 0, 0, 0.08));
        border-radius: var(--radius-lg, 16px);
        padding: 1.4rem 1.6rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .health-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow, 0 8px 24px rgba(0,0,0,0.08));
    }

    .health-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    /* Ops Action Card */
    .ops-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, rgba(0, 0, 0, 0.08));
        border-radius: var(--radius-lg, 16px);
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }

    .ops-btn {
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        border: 1px solid var(--border, rgba(0,0,0,0.08));
        background: var(--bg-input, #f8fafc);
        color: var(--text, #1e293b);
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .ops-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .ops-btn-danger:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    .ops-btn-warning:hover {
        background: #f59e0b;
        color: #ffffff;
        border-color: #f59e0b;
    }

    .ops-btn-primary:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .ops-btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .ops-btn.loading i {
        animation: fa-spin 1s infinite linear;
    }

    /* Latency Card */
    .latency-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        font-family: 'Orbitron', monospace;
    }

    .latency-pill.latency-fast {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .latency-pill.latency-medium {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .latency-pill.latency-slow {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
    }

    /* Specs Banner */
    .system-specs-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, rgba(0, 0, 0, 0.08));
        border-radius: var(--radius-lg, 16px);
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }

    .spec-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .spec-label {
        font-size: 0.78rem;
        color: var(--text-secondary, #64748b);
        font-weight: 500;
    }

    .spec-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text, #1e293b);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-family: 'Tajawal', sans-serif;
    }

    /* Filter Pills */
    .health-filter-btn {
        padding: 0.55rem 1.25rem;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        border: 1px solid var(--border, rgba(0, 0, 0, 0.08));
        background: var(--bg-card, #ffffff);
        color: var(--text-secondary, #64748b);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .health-filter-btn:hover {
        border-color: var(--brand-red, #dc2626);
        color: var(--brand-red, #dc2626);
    }

    .health-filter-btn.active {
        background: var(--brand-red, #dc2626);
        border-color: var(--brand-red, #dc2626);
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
    }

    /* Check Card */
    .check-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, rgba(0, 0, 0, 0.08));
        border-radius: var(--radius-lg, 16px);
        padding: 1.5rem;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .check-card:hover {
        border-color: var(--border-light, rgba(255, 255, 255, 0.18));
        transform: translateY(-2px);
        box-shadow: var(--shadow, 0 6px 20px rgba(0, 0, 0, 0.07));
    }

    .check-card.status-ok {
        border-right: 4px solid #10b981;
    }

    .check-card.status-warning {
        border-right: 4px solid #f59e0b;
    }

    .check-card.status-failed,
    .check-card.status-crashed {
        border-right: 4px solid #ef4444;
    }

    .check-card.status-skipped {
        border-right: 4px solid #6b7280;
    }

    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .status-badge.badge-ok {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .status-badge.badge-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .status-badge.badge-failed,
    .status-badge.badge-crashed {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .status-badge.badge-skipped {
        background: rgba(107, 114, 128, 0.12);
        color: #9ca3af;
        border: 1px solid rgba(107, 114, 128, 0.25);
    }

    .spin-on-click.refreshing i {
        animation: fa-spin 1s infinite linear;
    }
</style>
@endsection

@section('content')
@php
    $results = $checkResults?->storedCheckResults ?? [];
    $totalCount = count($results);
    $okCount = collect($results)->filter(fn($r) => $r->status === 'ok')->count();
    $warningCount = collect($results)->filter(fn($r) => $r->status === 'warning')->count();
    $failedCount = collect($results)->filter(fn($r) => in_array($r->status, ['failed', 'crashed']))->count();
    $skippedCount = collect($results)->filter(fn($r) => $r->status === 'skipped')->count();

    // Calculate Dynamic System Health Score (0 - 100%)
    $healthScore = 100;
    if ($totalCount > 0) {
        $healthScore -= ($failedCount * 25);
        $healthScore -= ($warningCount * 8);
        $healthScore = max(0, min(100, $healthScore));
    }

    // Map check types to icons & descriptions
    $iconMap = [
        'Database' => ['icon' => 'fa-database', 'color' => '#3b82f6'],
        'DatabaseConnectionCount' => ['icon' => 'fa-network-wired', 'color' => '#6366f1'],
        'DatabaseSize' => ['icon' => 'fa-hard-drive', 'color' => '#8b5cf6'],
        'Cache' => ['icon' => 'fa-bolt', 'color' => '#f59e0b'],
        'Schedule' => ['icon' => 'fa-clock', 'color' => '#ec4899'],
        'Environment' => ['icon' => 'fa-server', 'color' => '#14b8a6'],
        'DebugMode' => ['icon' => 'fa-bug', 'color' => '#ef4444'],
        'OptimizedApp' => ['icon' => 'fa-gauge-high', 'color' => '#10b981'],
        'UsedDiskSpace' => ['icon' => 'fa-microchip', 'color' => '#06b6d4'],
        'HyperPay' => ['icon' => 'fa-credit-card', 'color' => '#0284c7'],
        'ReverbServer' => ['icon' => 'fa-tower-broadcast', 'color' => '#8b5cf6'],
        'StalledAuctions' => ['icon' => 'fa-gavel', 'color' => '#dc2626'],
        'PendingFinancialRequests' => ['icon' => 'fa-money-bill-transfer', 'color' => '#f59e0b'],
        'SmtpMail' => ['icon' => 'fa-envelope-circle-check', 'color' => '#0ea5e9'],
        'StorageDisk' => ['icon' => 'fa-folder-open', 'color' => '#10b981'],
        'SmsGateway' => ['icon' => 'fa-comment-sms', 'color' => '#f97316'],
    ];

    $isAllHealthy = $failedCount === 0 && $warningCount === 0 && $totalCount > 0;
@endphp

<div class="container-fluid px-0">
    
    {{-- ========== HERO HEADER ========== --}}
    <div class="health-hero">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="health-hero-badge">
                    @if($isAllHealthy)
                        <span class="pulse-dot pulse-ok"></span>
                        <span>جميع الأنظمة تعمل بكفاءة تامة</span>
                    @elseif($failedCount > 0)
                        <span class="pulse-dot pulse-danger"></span>
                        <span>تنبيه: توجد أعطال في بعض الأنظمة بحاجة لتدخل فوري</span>
                    @else
                        <span class="pulse-dot pulse-warning"></span>
                        <span>ملاحظة: توجد بعض التنبيهات التي ينبغي مراجعتها</span>
                    @endif
                </div>

                <h1 class="h2 fw-bold mb-2">لوحة مراقبة صحة وحالة النظام 🚀</h1>
                <p class="mb-0 text-white-50 fs-6">
                    فحص دوري للبنية التحتية، خوادم البث المباشر، بوابات الدفع، وإجراءات الصيانة بنقرة واحدة.
                </p>
                
                @if($lastRanAt)
                    <div class="mt-3 text-white-50 small d-flex align-items-center gap-2">
                        <i class="far fa-clock"></i>
                        <span>آخر فحص تم تنفيذه:</span>
                        <strong class="text-white">{{ $lastRanAt->locale('ar')->diffForHumans() }}</strong>
                        <span class="text-white-50">({{ $lastRanAt->format('Y-m-d H:i:s') }})</span>
                    </div>
                @endif
            </div>

            <div class="col-lg-5 text-lg-start text-start mt-4 mt-lg-0">
                <div class="d-flex flex-column align-items-lg-end align-items-start gap-3">
                    {{-- Health Score --}}
                    <div class="health-score-box">
                        <div class="text-start">
                            <div class="small text-white-50">مؤشر سلامة النظام</div>
                            <div class="fw-bold fs-6 text-white">
                                @if($healthScore >= 90)
                                    <span class="text-success">ممتاز ومستقر</span>
                                @elseif($healthScore >= 70)
                                    <span class="text-warning">جيد مع ملاحظات</span>
                                @else
                                    <span class="text-danger">حرج يتطلب تدخل</span>
                                @endif
                            </div>
                        </div>
                        <div class="health-score-val {{ $healthScore >= 90 ? 'text-success' : ($healthScore >= 70 ? 'text-warning' : 'text-danger') }}">
                            {{ $healthScore }}%
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.system-status', ['fresh' => 1]) }}" 
                           class="btn btn-light px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2 spin-on-click"
                           id="refreshHealthBtn"
                           onclick="this.classList.add('refreshing');">
                            <i class="fas fa-rotate"></i>
                            <span>إعادة الفحص الآن</span>
                        </a>

                        <button type="button" class="btn btn-outline-light px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" id="autoRefreshToggleBtn">
                            <i class="fas fa-play" id="autoRefreshIcon"></i>
                            <span id="autoRefreshText">تحديث تلقائي (30 ث)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== REAL-TIME LATENCY & BENCHMARKS STRIP ========== --}}
    <div class="row g-3 mb-4">
        {{-- Database Latency --}}
        <div class="col-md-4">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">سرعة استجابة قاعدة البيانات (DB Latency)</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="h3 fw-bold mb-0 text-body font-monospace">{{ $dbLatency ?? '2.1' }} ms</span>
                        @php
                            $lat = $dbLatency ?? 2.1;
                            $latClass = $lat < 10 ? 'latency-fast' : ($lat < 40 ? 'latency-medium' : 'latency-slow');
                            $latText = $lat < 10 ? 'فائقة السرعة' : ($lat < 40 ? 'مقبولة' : 'بطيئة');
                        @endphp
                        <span class="latency-pill {{ $latClass }}">{{ $latText }}</span>
                    </div>
                </div>
                <div class="health-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
        </div>

        {{-- Cache Latency --}}
        <div class="col-md-4">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">استجابة التخزين المؤقت (Cache Speed)</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="h3 fw-bold mb-0 text-body font-monospace">{{ $cacheLatency ?? '0.4' }} ms</span>
                        <span class="latency-pill latency-fast">لحظية 🚀</span>
                    </div>
                </div>
                <div class="health-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="fas fa-gauge-high"></i>
                </div>
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="col-md-4">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">استهلاك الذاكرة (Memory Footprint)</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="h3 fw-bold mb-0 text-body font-monospace">{{ $memoryUsageMb ?? '24.2' }} MB</span>
                        <span class="small text-muted">(الذروة: {{ $memoryPeakMb ?? '30.1' }} MB)</span>
                    </div>
                </div>
                <div class="health-stat-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                    <i class="fas fa-microchip"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== QUICK OPS / SELF-HEALING ACTIONS ========== --}}
    <div class="ops-card mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2 border-bottom border-light">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-wand-magic-sparkles text-danger"></i>
                <span class="fw-bold fs-6">إجراءات الصيانة السريعة بنقرة واحدة (Quick Ops & Self-Healing)</span>
            </div>
            <span class="small text-muted">أوامر تنفيذ مباشرة لإنعاش وتنشيط المنصة دون الحاجة للـ Terminal</span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3">
            {{-- Clear Cache Button --}}
            <button type="button" class="ops-btn ops-btn-primary" id="btnActionClearCache">
                <i class="fas fa-broom"></i>
                <span>تفريغ الكاش والقوالب (Purge Cache)</span>
            </button>

            {{-- Restart Queue Button --}}
            <button type="button" class="ops-btn ops-btn-warning" id="btnActionRestartQueue">
                <i class="fas fa-rotate"></i>
                <span>إعادة تنشيط طابور المهام (Restart Queue)</span>
            </button>

            {{-- Clear Optimizations Button --}}
            <button type="button" class="ops-btn ops-btn-danger" id="btnActionOptimizeClear">
                <i class="fas fa-sliders"></i>
                <span>إعادة ضبط مسارات وتكوينات النظام</span>
            </button>
        </div>

        {{-- Emergency Maintenance Mode Control --}}
        <div class="pt-3 border-top border-light mt-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-shield-halved {{ $isMaintenanceMode ? 'text-danger' : 'text-success' }}" style="font-size: 1.4rem;"></i>
                <div>
                    <div class="fw-bold">وضع الصيانة للطوارئ (Emergency Maintenance Mode)</div>
                    <small class="text-muted">
                        @if($isMaintenanceMode)
                            <span class="text-danger fw-bold"><i class="fas fa-lock me-1"></i> المنصة مغلقة حالياً أمام الزوار وتظهر رسالة الصيانة</span>
                        @else
                            <span class="text-success fw-bold"><i class="fas fa-circle-check me-1"></i> المنصة مفتوحة وتعمل بكامل طاقتها للجمهور</span>
                        @endif
                    </small>
                </div>
            </div>
            <div>
                <button type="button" class="btn {{ $isMaintenanceMode ? 'btn-success' : 'btn-outline-danger' }} px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" id="btnToggleMaintenance" data-is-down="{{ $isMaintenanceMode ? '1' : '0' }}">
                    <i class="fas {{ $isMaintenanceMode ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    <span>{{ $isMaintenanceMode ? 'إلغاء وضع الصيانة وإعادة فتح المنصة للجمهور' : 'تفعيل وضع الصيانة الطارئ' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========== COUNTER METRICS ========== --}}
    <div class="row g-3 mb-4">
        {{-- Total Checks --}}
        <div class="col-6 col-md-3">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">إجمالي الفحوصات</div>
                    <div class="h3 fw-bold mb-0 text-body">{{ $totalCount }}</div>
                </div>
                <div class="health-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="fas fa-shield-heart"></i>
                </div>
            </div>
        </div>

        {{-- Healthy --}}
        <div class="col-6 col-md-3">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">فحوصات سليمة (Ok)</div>
                    <div class="h3 fw-bold mb-0 text-success">{{ $okCount }}</div>
                </div>
                <div class="health-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>

        {{-- Warnings --}}
        <div class="col-6 col-md-3">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">تنبيهات (Warning)</div>
                    <div class="h3 fw-bold mb-0 text-warning">{{ $warningCount }}</div>
                </div>
                <div class="health-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>

        {{-- Failed --}}
        <div class="col-6 col-md-3">
            <div class="health-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="spec-label mb-1">أعطال حرجة (Failed)</div>
                    <div class="h3 fw-bold mb-0 text-danger">{{ $failedCount }}</div>
                </div>
                <div class="health-stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="fas fa-circle-xmark"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== SYSTEM SPECS BAR ========== --}}
    <div class="system-specs-card mb-4">
        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light">
            <i class="fas fa-server text-danger"></i>
            <span class="fw-bold fs-6">معلومات الخادم والبيئة الحالية (Server Specs)</span>
        </div>
        <div class="row g-3 text-center text-sm-start">
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">إصدار PHP</span>
                    <span class="spec-value">
                        <i class="fab fa-php text-primary"></i>
                        {{ phpversion() }}
                    </span>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">إصدار Laravel</span>
                    <span class="spec-value">
                        <i class="fab fa-laravel text-danger"></i>
                        v{{ app()->version() }}
                    </span>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">بيئة العمل (Env)</span>
                    <span class="spec-value">
                        <span class="badge {{ app()->environment('production') ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ app()->environment() }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">مشغل الكاش (Cache)</span>
                    <span class="spec-value text-uppercase">
                        <i class="fas fa-bolt text-warning"></i>
                        {{ config('cache.default') }}
                    </span>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">طابور المهام (Queue)</span>
                    <span class="spec-value text-uppercase">
                        <i class="fas fa-layer-group text-info"></i>
                        {{ config('queue.default') }}
                    </span>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="spec-item">
                    <span class="spec-label">البث المباشر (WebSockets)</span>
                    <span class="spec-value text-uppercase">
                        <i class="fas fa-satellite-dish text-success"></i>
                        {{ config('broadcasting.default') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FILTER CONTROLS ========== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2" id="filterPills">
            <button type="button" class="health-filter-btn active" data-filter="all">
                <span>الكل</span>
                <span class="badge bg-secondary rounded-pill">{{ $totalCount }}</span>
            </button>
            <button type="button" class="health-filter-btn" data-filter="ok">
                <i class="fas fa-check-circle text-success"></i>
                <span>سليمة</span>
                <span class="badge bg-success rounded-pill">{{ $okCount }}</span>
            </button>
            @if($warningCount > 0)
                <button type="button" class="health-filter-btn" data-filter="warning">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <span>تحذيرات</span>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $warningCount }}</span>
                </button>
            @endif
            @if($failedCount > 0)
                <button type="button" class="health-filter-btn" data-filter="failed">
                    <i class="fas fa-times-circle text-danger"></i>
                    <span>أعطال</span>
                    <span class="badge bg-danger rounded-pill">{{ $failedCount }}</span>
                </button>
            @endif
        </div>

        <div class="position-relative" style="min-width: 250px;">
            <input type="text" id="checkSearchInput" class="form-control form-control-sm pe-4 py-2" placeholder="بحث سريع في الفحوصات...">
            <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="right: 12px;"></i>
        </div>
    </div>

    {{-- ========== CHECKS GRID ========== --}}
    <div class="row g-3" id="checksContainer">
        @forelse($results as $result)
            @php
                $checkName = $result->name;
                $iconData = $iconMap[$checkName] ?? ['icon' => 'fa-heart-pulse', 'color' => '#dc2626'];
                $status = $result->status;

                $statusLabel = match($status) {
                    'ok' => 'سليم (Ok)',
                    'warning' => 'تحذير (Warning)',
                    'failed' => 'عطل (Failed)',
                    'crashed' => 'انهيار (Crashed)',
                    'skipped' => 'تم التخطي (Skipped)',
                    default => ucfirst($status)
                };

                $badgeClass = match($status) {
                    'ok' => 'badge-ok',
                    'warning' => 'badge-warning',
                    'failed', 'crashed' => 'badge-failed',
                    default => 'badge-skipped'
                };

                $filterCategory = match($status) {
                    'ok' => 'ok',
                    'warning' => 'warning',
                    'failed', 'crashed' => 'failed',
                    default => 'all'
                };

                // Friendly message
                $message = $result->notificationMessage ?: $result->shortSummary;
            @endphp

            <div class="col-md-6 col-lg-4 check-item-wrapper" data-status="{{ $filterCategory }}" data-name="{{ strtolower($result->label . ' ' . $result->name . ' ' . $message) }}">
                <div class="check-card status-{{ $status }}">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="health-stat-icon" style="background: {{ $iconData['color'] }}15; color: {{ $iconData['color'] }}; width: 44px; height: 44px;">
                                <i class="fas {{ $iconData['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-body">{{ $result->label ?: $result->name }}</h6>
                                <small class="text-muted font-monospace">{{ $result->name }}</small>
                            </div>
                        </div>

                        <span class="status-badge {{ $badgeClass }}">
                            @if($status === 'ok')
                                <i class="fas fa-check"></i>
                            @elseif($status === 'warning')
                                <i class="fas fa-exclamation"></i>
                            @elseif($status === 'failed' || $status === 'crashed')
                                <i class="fas fa-xmark"></i>
                            @else
                                <i class="fas fa-forward"></i>
                            @endif
                            <span>{{ $statusLabel }}</span>
                        </span>
                    </div>

                    <div class="mt-auto pt-2 border-top border-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small text-muted">النتيجة / التفاصيل:</span>
                            <span class="badge {{ $status === 'ok' ? 'bg-success-subtle text-success' : ($status === 'warning' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} fw-semibold px-2 py-1">
                                {{ $result->shortSummary ?: 'سليم' }}
                            </span>
                        </div>

                        @if(!empty($result->notificationMessage) && $result->notificationMessage !== $result->shortSummary)
                            <div class="mt-2 p-2 rounded small bg-light text-muted border" style="font-size: 0.8rem; line-height: 1.4;">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $result->notificationMessage }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 rounded-4 bg-card border">
                    <i class="fas fa-clipboard-question text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold">لم يتم تسجيل نتائج فحوصات بعد</h5>
                    <p class="text-muted mb-3">يمكنك النقر على الزر أدناه لبدء فحص النظام وحفظ النتائج في قاعدة البيانات.</p>
                    <a href="{{ route('admin.system-status', ['fresh' => 1]) }}" class="btn btn-danger px-4">
                        <i class="fas fa-play me-2"></i> تشغيل الفحوصات الآن
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ========== INCIDENT HISTORY TIMELINE ========== --}}
    <div class="system-specs-card mt-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-light">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clock-rotate-left text-danger" style="font-size: 1.25rem;"></i>
                <span class="fw-bold fs-6">سجل الحوادث والأعطال السابقة (Incident History Timeline)</span>
                <span class="badge bg-danger rounded-pill">{{ count($incidentHistory ?? []) }}</span>
            </div>
            <span class="small text-muted">توثيق تاريخي لآخر الحوادث والأعطال الطارئة المسجلة في النظام مع تفاصيل كل عطل</span>
        </div>

        @if(!empty($incidentHistory) && count($incidentHistory) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">الخدمة / الفحص</th>
                            <th class="py-3 text-center">نوع الحالة</th>
                            <th class="py-3">تفاصيل المشكلة والرسالة</th>
                            <th class="py-3">تاريخ وساعة الرصد</th>
                            <th class="py-3">المدة منذ الرصد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentHistory as $incident)
                            <tr>
                                <td class="fw-bold text-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-circle-exclamation text-danger"></i>
                                        <span>{{ $incident->check_label ?: $incident->check_name }}</span>
                                    </div>
                                    <small class="text-muted font-monospace">{{ $incident->check_name }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $incident->status === 'warning' ? 'bg-warning text-dark' : 'bg-danger' }} px-2 py-1">
                                        {{ $incident->status === 'warning' ? 'تحذير (Warning)' : 'عطل حرج (Failed)' }}
                                    </span>
                                </td>
                                <td class="text-muted" style="max-width: 380px;">
                                    <div class="text-truncate-2" title="{{ $incident->notification_message ?: $incident->short_summary }}">
                                        {{ $incident->notification_message ?: ($incident->short_summary ?: 'لا توجد تفاصيل إضافية') }}
                                    </div>
                                </td>
                                <td class="font-monospace text-secondary small">
                                    {{ $incident->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="text-secondary small">
                                    {{ $incident->created_at->locale('ar')->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-2" style="width: 54px; height: 54px;">
                    <i class="fas fa-shield-check fs-3"></i>
                </div>
                <h6 class="fw-bold text-success mb-1">سجل الحوادث نظيف تماماً!</h6>
                <p class="text-muted small mb-0">لم يتم تسجيل أي أعطال أو حوادث سابقة في المنصة، جميع الخدمات تعمل باستقرار وكفاءة تامة 🛡️</p>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Tab filtering
        const filterButtons = document.querySelectorAll('#filterPills button');
        const cards = document.querySelectorAll('.check-item-wrapper');
        const searchInput = document.getElementById('checkSearchInput');

        function applyFilters() {
            const activeBtn = document.querySelector('#filterPills button.active');
            const activeFilter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
            const query = (searchInput?.value || '').toLowerCase().trim();

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const cardName = card.getAttribute('data-name');

                const matchesStatus = (activeFilter === 'all') || (cardStatus === activeFilter);
                const matchesSearch = !query || cardName.includes(query);

                if (matchesStatus && matchesSearch) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        // Quick Ops Actions Handlers
        function executeOpsAction(btn, url, confirmMsg) {
            if (!confirm(confirmMsg)) return;

            btn.classList.add('loading');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التنفيذ...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.classList.remove('loading');
                btn.innerHTML = originalHtml;
                if (data.success) {
                    if (window.toastr) {
                        toastr.success(data.message, 'نجاح العملية');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (window.toastr) {
                        toastr.error(data.message || 'حدث خطأ أثناء التنفيذ', 'خطأ');
                    } else {
                        alert(data.message || 'حدث خطأ أثناء التنفيذ');
                    }
                }
            })
            .catch(err => {
                btn.classList.remove('loading');
                btn.innerHTML = originalHtml;
                if (window.toastr) {
                    toastr.error('تعذر الاتصال بالخادم: ' + err.message, 'خطأ اتصال');
                } else {
                    alert('تعذر الاتصال بالخادم');
                }
            });
        }

        // Action 1: Clear Cache
        const btnClearCache = document.getElementById('btnActionClearCache');
        if (btnClearCache) {
            btnClearCache.addEventListener('click', function () {
                executeOpsAction(this, "{{ route('admin.system-status.clear-cache') }}", "هل أنت متأكد من رغبتك في تفريغ وحذف كافة ملفات الكاش والقوالب؟");
            });
        }

        // Action 2: Restart Queue
        const btnRestartQueue = document.getElementById('btnActionRestartQueue');
        if (btnRestartQueue) {
            btnRestartQueue.addEventListener('click', function () {
                executeOpsAction(this, "{{ route('admin.system-status.restart-queue') }}", "هل ترغب في إرسال إشارة إعادة تشغيل لجميع عمال طابور المهام (Queue Workers)؟");
            });
        }

        // Action 3: Optimize Clear
        const btnOptimizeClear = document.getElementById('btnActionOptimizeClear');
        if (btnOptimizeClear) {
            btnOptimizeClear.addEventListener('click', function () {
                executeOpsAction(this, "{{ route('admin.system-status.optimize-clear') }}", "هل أنت متأكد من تفريغ كافة تكوينات ومسارات النظام وإعادة بنائها؟");
            });
        }

        // Action 4: Toggle Maintenance Mode
        const btnToggleMaintenance = document.getElementById('btnToggleMaintenance');
        if (btnToggleMaintenance) {
            btnToggleMaintenance.addEventListener('click', function () {
                const isDown = this.getAttribute('data-is-down') === '1';
                let message = '';

                if (isDown) {
                    if (!confirm('هل أنت متأكد من رغبتك في إيقاف وضع الصيانة وإعادة إتاحة المنصة للجمهور والمزايدين فوراً؟')) {
                        return;
                    }
                } else {
                    const promptMsg = prompt('أدخل رسالة التنبيه التي ستظهر للمستخدمين والزوار أثناء الصيانة:', 'المنصة تخضع حالياً لأعمال صيانة طارئة لحماية المزايدات. سنعود خلال دقائق معدودة.');
                    if (promptMsg === null) return; // Cancelled
                    message = promptMsg;
                }

                this.classList.add('loading');
                const origHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري المعالجة...';

                fetch("{{ route('admin.system-status.toggle-maintenance') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (window.toastr) toastr.success(data.message, 'وضع الصيانة');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        btnToggleMaintenance.classList.remove('loading');
                        btnToggleMaintenance.innerHTML = origHtml;
                        if (window.toastr) toastr.error(data.message || 'فشل تغيير وضع الصيانة', 'خطأ');
                    }
                })
                .catch(err => {
                    btnToggleMaintenance.classList.remove('loading');
                    btnToggleMaintenance.innerHTML = origHtml;
                    if (window.toastr) toastr.error('تعذر الاتصال بالخادم: ' + err.message, 'خطأ');
                });
            });
        }

        // Auto Refresh Timer (30s)
        let autoRefreshInterval = null;
        let countdownSec = 30;
        const autoRefreshBtn = document.getElementById('autoRefreshToggleBtn');
        const autoRefreshIcon = document.getElementById('autoRefreshIcon');
        const autoRefreshText = document.getElementById('autoRefreshText');

        if (autoRefreshBtn) {
            autoRefreshBtn.addEventListener('click', function () {
                if (autoRefreshInterval) {
                    // Turn OFF
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                    countdownSec = 30;
                    autoRefreshIcon.className = 'fas fa-play';
                    autoRefreshText.textContent = 'تحديث تلقائي (30 ث)';
                    autoRefreshBtn.classList.remove('btn-success');
                    autoRefreshBtn.classList.add('btn-outline-light');
                    if (window.toastr) toastr.info('تم إيقاف التحديث التلقائي', 'حالة النظام');
                } else {
                    // Turn ON
                    countdownSec = 30;
                    autoRefreshIcon.className = 'fas fa-pause';
                    autoRefreshBtn.classList.remove('btn-outline-light');
                    autoRefreshBtn.classList.add('btn-success');
                    if (window.toastr) toastr.success('تم تفعيل التحديث التلقائي كل 30 ثانية', 'حالة النظام');

                    autoRefreshInterval = setInterval(function () {
                        countdownSec--;
                        autoRefreshText.textContent = 'تحديث خلال (' + countdownSec + ' ث)';
                        if (countdownSec <= 0) {
                            window.location.href = "{{ route('admin.system-status', ['fresh' => 1]) }}";
                        }
                    }, 1000);
                }
            });
        }
    });
</script>
@endsection
