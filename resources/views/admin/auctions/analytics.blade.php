@extends('layouts.admin')

@section('title', __('Auctions Analytics'))

@section('css')
<style>
    /* Premium UI Styling */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.85);
        --glass-border: rgba(51, 65, 85, 0.8);
    }

    .premium-panel {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: var(--shadow-premium);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .panel-header-premium {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--glass-border);
        background: rgba(248, 250, 252, 0.4);
    }

    [data-theme="dark"] .panel-header-premium {
        background: rgba(15, 23, 42, 0.3);
    }

    .panel-header-premium h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-color);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .panel-body-premium {
        padding: 24px;
    }

    /* Stats Grid */
    .stat-card-gradient {
        position: relative;
        border-radius: 20px;
        padding: 24px;
        color: #ffffff;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
        margin-bottom: 24px;
    }
    .stat-card-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15);
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50px;
        right: -50px;
    }
    html[dir="rtl"] .stat-card-gradient::after {
        right: auto;
        left: -50px;
    }
    .scg-purple { background: linear-gradient(135deg, #6366f1, #a855f7); }
    .scg-emerald { background: linear-gradient(135deg, #059669, #10b981); }
    .scg-amber { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .scg-blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }

    .scg-value {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .scg-label {
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    .scg-icon {
        position: absolute;
        bottom: 20px;
        right: 20px;
        font-size: 2.2rem;
        opacity: 0.25;
    }
    html[dir="rtl"] .scg-icon {
        right: auto;
        left: 20px;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ __('Auctions Analytics') }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.auctions.index') }}">{{ __('Auctions') }}</a> / 
            {{ __('Analytics') }}
        </div>
    </div>
    <a href="{{ route('admin.auctions.export-report') }}" class="btn btn-success d-flex align-items-center gap-2 px-4 rounded-pill">
        <i class="fa-solid fa-file-csv"></i>
        <span>{{ __('Export Financial Report') }}</span>
    </a>
</div>

{{-- Top Row Stats --}}
<div class="row">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-purple">
            <div class="scg-value">{{ number_format($totalCommissions, 2) }} SAR</div>
            <div class="scg-label">{{ __('Total Commissions') }}</div>
            <i class="fa-solid fa-wallet scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-emerald">
            <div class="scg-value">{{ $soldCount }}</div>
            <div class="scg-label">{{ __('Successful Sales') }}</div>
            <i class="fa-solid fa-circle-check scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-amber">
            <div class="scg-value">{{ $totalEnded }}</div>
            <div class="scg-label">{{ __('Unsold / Cancelled') }}</div>
            <i class="fa-solid fa-circle-xmark scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-blue">
            <div class="scg-value">{{ number_format($avgCommission, 2) }} SAR</div>
            <div class="scg-label">{{ __('Avg Commission') }}</div>
            <i class="fa-solid fa-calculator scg-icon"></i>
        </div>
    </div>
</div>

<div class="row">
    {{-- Monthly Commissions Chart --}}
    <div class="col-lg-8">
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3>
                    <i class="fa-solid fa-chart-line text-primary"></i>
                    <span>{{ __('Monthly Commissions Growth') }}</span>
                </h3>
            </div>
            <div class="panel-body-premium">
                <div class="chart-container">
                    <canvas id="monthlyCommissionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Ratio Chart --}}
    <div class="col-lg-4">
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3>
                    <i class="fa-solid fa-chart-pie text-warning"></i>
                    <span>{{ __('Auctions Success Ratio') }}</span>
                </h3>
            </div>
            <div class="panel-body-premium">
                <div class="chart-container">
                    <canvas id="successRatioChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Financial Sales Table --}}
<div class="premium-panel">
    <div class="panel-header-premium">
        <h3>
            <i class="fa-solid fa-list text-success"></i>
            <span>{{ __('Recent Sales & Commissions') }}</span>
        </h3>
    </div>
    <div class="panel-body-premium p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 text-sm">
                <thead>
                    <tr class="bg-light">
                        <th class="px-4 py-3">{{ __('Auction ID') }}</th>
                        <th class="py-3">{{ __('Title') }}</th>
                        <th class="py-3">{{ __('Winner') }}</th>
                        <th class="py-3 text-end">{{ __('Winning Bid') }}</th>
                        <th class="py-3 text-end">{{ __('Commission') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td class="px-4 py-3 font-weight-bold">#{{ $sale->id }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.auctions.show', $sale->id) }}" class="text-decoration-none font-weight-bold text-dark">
                                    {{ $sale->title }}
                                </a>
                            </td>
                            <td class="py-3">
                                <strong>{{ $sale->winner?->name ?? 'N/A' }}</strong>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $sale->winner?->email }}</div>
                            </td>
                            <td class="py-3 text-end font-weight-bold">
                                {{ number_format($sale->winning_bid_amount, 2) }} SAR
                            </td>
                            <td class="py-3 text-end font-weight-bold text-success">
                                {{ number_format($sale->commission_amount, 2) }} SAR
                                <span class="text-muted" style="font-size:0.75rem; font-weight:normal;">({{ $sale->commission_rate }}%)</span>
                            </td>
                            <td class="py-3 px-4 text-center text-muted">
                                {{ $sale->sold_at ? $sale->sold_at->format('Y-m-d H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                {{ __('No completed sales yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── Monthly Commissions Chart (Bar / Line) ──────────────────────────
        const ctxMonthly = document.getElementById('monthlyCommissionsChart').getContext('2d');
        const monthsLabels = [
            "{{ __('Jan') }}", "{{ __('Feb') }}", "{{ __('Mar') }}", 
            "{{ __('Apr') }}", "{{ __('May') }}", "{{ __('Jun') }}", 
            "{{ __('Jul') }}", "{{ __('Aug') }}", "{{ __('Sep') }}", 
            "{{ __('Oct') }}", "{{ __('Nov') }}", "{{ __('Dec') }}"
        ];
        const monthlyData = @json(array_values($monthsData));

        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: monthsLabels,
                datasets: [{
                    label: "{{ __('Commission (SAR)') }}",
                    data: monthlyData,
                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1,
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // ── Success Ratio Chart (Doughnut) ──────────────────────────────────
        const ctxRatio = document.getElementById('successRatioChart').getContext('2d');
        const soldCount = {{ $soldCount }};
        const endedCount = {{ $endedCount }};
        const cancelledCount = {{ $cancelledCount }};

        new Chart(ctxRatio, {
            type: 'doughnut',
            data: {
                labels: [
                    "{{ __('Sold Successfully') }}", 
                    "{{ __('Unsold (Reserve not met)') }}", 
                    "{{ __('Cancelled') }}"
                ],
                datasets: [{
                    data: [soldCount, endedCount, cancelledCount],
                    backgroundColor: [
                        '#10b981', // Emerald
                        '#f59e0b', // Amber
                        '#ef4444'  // Red
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
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection
