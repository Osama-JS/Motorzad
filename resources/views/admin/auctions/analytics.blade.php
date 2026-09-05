@extends('layouts.admin')

@section('title', __('تحليلات وإحصائيات المزادات'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.9);
        --glass-border: rgba(51, 65, 85, 0.8);
    }

    .analytics-panel {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: var(--shadow-premium);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .analytics-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--glass-border);
        background: rgba(248, 250, 252, 0.5);
    }

    [data-theme="dark"] .analytics-header {
        background: rgba(15, 23, 42, 0.4);
    }

    .analytics-header h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .analytics-body {
        padding: 22px;
    }

    /* KPI Cards */
    .kpi-card {
        border-radius: 18px;
        padding: 20px 22px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 24px;
        border: none;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -5px rgba(0,0,0,0.18);
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        top: -40px;
        inset-inline-end: -40px;
    }

    .kpi-purple { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    .kpi-emerald { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
    .kpi-blue { background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); }
    .kpi-amber { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
    .kpi-rose { background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); }
    .kpi-dark { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 2px;
    }
    .kpi-label {
        font-size: 0.82rem;
        font-weight: 700;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .kpi-icon {
        position: absolute;
        bottom: 18px;
        inset-inline-end: 20px;
        font-size: 2.2rem;
        opacity: 0.22;
    }

    .chart-container-custom {
        position: relative;
        height: 310px;
        width: 100%;
    }

    .metric-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ __('تحليلات وإحصائيات المزادات الشاملة') }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.auctions.index') }}">{{ __('Auctions') }}</a> / 
            <span>{{ __('Analytics') }}</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.auctions.export-report') }}" class="btn btn-success d-flex align-items-center gap-2 px-4 rounded-pill shadow-sm fw-bold">
            <i class="fa-solid fa-file-excel"></i>
            <span>{{ __('تصدير التقرير المالي CSV') }}</span>
        </a>
    </div>
</div>

{{-- Interactive Filter Bar with Select2 and Custom Datepicker --}}
<div class="card mb-4 shadow-sm border-0 rounded-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.auctions.analytics') }}" id="analyticsFilterForm">
            <div class="row g-3 align-items-center">
                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('السنة المالية المحددة:') }}</label>
                    <select name="year" class="form-select select2-analytics">
                        @for($y = now()->year; $y >= now()->year - 4; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('من تاريخ:') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                        <input type="text" name="date_from" class="form-control custom-datepicker border-start-0 ps-0" placeholder="{{ __('من تاريخ...') }}" value="{{ request('date_from') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('إلى تاريخ:') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                        <input type="text" name="date_to" class="form-control custom-datepicker border-start-0 ps-0" placeholder="{{ __('إلى تاريخ...') }}" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-12 d-flex align-items-end gap-2" style="padding-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary flex-fill fw-bold rounded-pill">
                        <i class="fa-solid fa-filter me-1"></i> {{ __('تحديث الإحصائيات') }}
                    </button>
                    @if(request()->hasAny(['year', 'date_from', 'date_to']))
                        <a href="{{ route('admin.auctions.analytics') }}" class="btn btn-light rounded-pill px-3" title="{{ __('إعادة ضبط') }}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 1. Main Key Performance Indicators (KPIs) --}}
<div class="row">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card kpi-purple">
            <div class="kpi-value">{{ number_format($totalCommissions, 2) }} <small class="fs-6 font-monospace">SAR</small></div>
            <div class="kpi-label">{{ __('إجمالي عمولات المنصة المحصلة') }}</div>
            <i class="fa-solid fa-sack-dollar kpi-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card kpi-emerald">
            <div class="kpi-value">{{ number_format($totalSalesVolume, 2) }} <small class="fs-6 font-monospace">SAR</small></div>
            <div class="kpi-label">{{ __('حجم التداولات ومبيعات المزادات') }}</div>
            <i class="fa-solid fa-chart-simple kpi-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-value">{{ $soldCount }} <small class="fs-6">من أصل {{ $totalAuctions }}</small></div>
            <div class="kpi-label">{{ __('المزادات المكتملة بنجاح (مبيعة)') }}</div>
            <i class="fa-solid fa-circle-check kpi-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card kpi-amber">
            <div class="kpi-value">{{ $successRate }}%</div>
            <div class="kpi-label">{{ __('نسبة نجاح البيع (Sell-through)') }}</div>
            <i class="fa-solid fa-bullseye kpi-icon"></i>
        </div>
    </div>
</div>

{{-- Secondary KPI Mini Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger fs-4">
                <i class="fa-solid fa-tower-broadcast"></i>
            </div>
            <div>
                <div class="fw-bold fs-5 text-dark">{{ $liveCount }}</div>
                <small class="text-muted fw-semibold">{{ __('مزادات مباشرة الآن') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary fs-4">
                <i class="fa-solid fa-gavel"></i>
            </div>
            <div>
                <div class="fw-bold fs-5 text-dark">{{ number_format($totalBids) }}</div>
                <small class="text-muted fw-semibold">{{ __('إجمالي المزايدات المسجلة') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success fs-4">
                <i class="fa-solid fa-tag"></i>
            </div>
            <div>
                <div class="fw-bold fs-5 text-dark">{{ number_format($avgSellingPrice, 0) }} SAR</div>
                <small class="text-muted fw-semibold">{{ __('متوسط سعر بيع المركبة') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
            <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info fs-4">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div class="fw-bold fs-5 text-dark">{{ $avgBidsPerAuction }} مزايدة</div>
                <small class="text-muted fw-semibold">{{ __('معدل التنافس لكل مزاد') }}</small>
            </div>
        </div>
    </div>
</div>

{{-- 2. Interactive Charts Row --}}
<div class="row g-4">
    {{-- Volume & Commissions Trend Chart --}}
    <div class="col-xl-8">
        <div class="analytics-panel">
            <div class="analytics-header">
                <h3>
                    <i class="fa-solid fa-chart-line text-primary"></i>
                    <span>{{ __('نمو المبيعات وعمولات المنصة الشهرية (' . now()->year . ')') }}</span>
                </h3>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary metric-badge">العمولات المحصلة</span>
                    <span class="badge bg-success bg-opacity-10 text-success metric-badge">قيمة المبيعات</span>
                </div>
            </div>
            <div class="analytics-body">
                <div class="chart-container-custom">
                    <canvas id="monthlyGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Breakdown Donut Chart --}}
    <div class="col-xl-4">
        <div class="analytics-panel">
            <div class="analytics-header">
                <h3>
                    <i class="fa-solid fa-chart-pie text-warning"></i>
                    <span>{{ __('توزيع حالات المزادات') }}</span>
                </h3>
            </div>
            <div class="analytics-body">
                <div class="chart-container-custom">
                    <canvas id="statusDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Top Brands & Highest Value Sales --}}
<div class="row g-4">
    {{-- Top Brands Bar Chart --}}
    <div class="col-xl-5">
        <div class="analytics-panel h-100 mb-0">
            <div class="analytics-header">
                <h3>
                    <i class="fa-solid fa-car text-danger"></i>
                    <span>{{ __('أكثر ماركات السيارات طلباً بالمزادات') }}</span>
                </h3>
            </div>
            <div class="analytics-body">
                <div class="chart-container-custom">
                    <canvas id="topBrandsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Highest Value Sales Table --}}
    <div class="col-xl-7">
        <div class="analytics-panel h-100 mb-0">
            <div class="analytics-header">
                <h3>
                    <i class="fa-solid fa-trophy text-warning"></i>
                    <span>{{ __('أعلى الصفقات المكتملة قيمة (Top Auctions)') }}</span>
                </h3>
            </div>
            <div class="analytics-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-2 text-muted">{{ __('المزاد') }}</th>
                                <th class="py-2 text-muted">{{ __('الفائز') }}</th>
                                <th class="py-2 text-end text-muted">{{ __('سعر الترسية') }}</th>
                                <th class="py-2 text-end text-muted">{{ __('عمولة المنصة') }}</th>
                                <th class="py-2 px-3 text-center text-muted">{{ __('التاريخ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAuctions as $sale)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 170px;">
                                            <a href="{{ route('admin.auctions.show', $sale->id) }}" class="text-decoration-none text-dark">
                                                #{{ $sale->id }} - {{ $sale->title }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $sale->vehicle?->make_ar ?? 'سيارة' }} {{ $sale->vehicle?->year }}</small>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark">{{ $sale->winner?->name ?? 'مستخدم' }}</div>
                                        <small class="text-muted font-monospace">{{ $sale->winner?->phone ?? $sale->winner?->email }}</small>
                                    </td>
                                    <td class="py-3 text-end font-weight-bold text-primary">
                                        {{ number_format($sale->winning_bid_amount, 2) }} SAR
                                    </td>
                                    <td class="py-3 text-end font-weight-bold text-success">
                                        {{ number_format($sale->commission_amount, 2) }} SAR
                                    </td>
                                    <td class="py-3 px-3 text-center text-muted small">
                                        {{ $sale->sold_at ? $sale->sold_at->format('Y-m-d') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                        {{ __('لا توجد صفقات مكتملة حتى الآن.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthsLabels = [
            "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
            "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
        ];

        // ── 1. Monthly Commissions & Sales Growth Multi-Chart ──────────────
        const ctxGrowth = document.getElementById('monthlyGrowthChart').getContext('2d');
        const commData = @json(array_values($monthsCommissions));
        const volData = @json(array_values($monthsVolume));

        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: monthsLabels,
                datasets: [
                    {
                        label: 'عمولات المنصة (SAR)',
                        data: commData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'حجم المبيعات الكلي (SAR)',
                        data: volData,
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Tajawal, sans-serif', weight: 'bold' }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(val) { return val.toLocaleString() + ' SAR'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            callback: function(val) { return val.toLocaleString() + ' SAR'; }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // ── 2. Auctions Status Donut Chart ────────────────────────────────
        const ctxStatus = document.getElementById('statusDonutChart').getContext('2d');
        const liveCount = {{ $liveCount }};
        const soldCount = {{ $soldCount }};
        const endedCount = {{ $endedCount }};
        const cancelledCount = {{ $cancelledCount }};
        const draftCount = {{ $draftCount }};

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: [
                    'مبيعة بنجاح', 
                    'مباشرة الآن', 
                    'انتهت دون بيع', 
                    'ملغاة',
                    'مسودة'
                ],
                datasets: [{
                    data: [soldCount, liveCount, endedCount, cancelledCount, draftCount],
                    backgroundColor: [
                        '#10b981', // Emerald - Sold
                        '#ef4444', // Red - Live
                        '#f59e0b', // Amber - Ended
                        '#64748b', // Slate - Cancelled
                        '#cbd5e1'  // Light - Draft
                    ],
                    borderWidth: 2,
                    borderColor: 'var(--glass-bg)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Tajawal, sans-serif', size: 11, weight: 'bold' }
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // ── 3. Top Vehicle Brands Horizontal Bar Chart ────────────────────
        const ctxBrands = document.getElementById('topBrandsChart').getContext('2d');
        const brandLabels = @json($brandLabels);
        const brandCounts = @json($brandCounts);

        new Chart(ctxBrands, {
            type: 'bar',
            data: {
                labels: brandLabels.length ? brandLabels : ['تويوتا', 'نيسان', 'مرسيدس', 'فورد', 'هيونداي'],
                datasets: [{
                    label: 'عدد المزادات',
                    data: brandCounts.length ? brandCounts : [0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderRadius: 8,
                    maxBarThickness: 32
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Initialize Select2 & Custom Datepicker
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-analytics').select2({
            dir: dir,
            width: '100%'
        });

        $('.custom-datepicker').flatpickr({
            locale: "ar",
            dateFormat: "Y-m-d",
            disableMobile: "true"
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endsection
