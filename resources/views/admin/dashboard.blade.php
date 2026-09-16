@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('css')
<style>
    /* ===== Dashboard Design System (#dc2626) ===== */
    :root {
        --dash-accent: #dc2626;
        --dash-accent-light: #ef4444;
        --dash-accent-glow: rgba(220, 38, 38, 0.12);
        --dash-accent-soft: rgba(220, 38, 38, 0.06);
        --dash-emerald: #059669;
        --dash-amber: #d97706;
        --dash-blue: #2563eb;
        --dash-purple: #7c3aed;
    }

    /* ===== Welcome Banner ===== */
    .dash-welcome {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #1e1b4b 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        color: #fff;
    }
    .dash-welcome::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
    }
    .dash-welcome::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 5%;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.04);
    }
    .dash-welcome-content { position: relative; z-index: 1; }
    .dash-welcome h2 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    .dash-welcome h2 span {
        font-family: 'Orbitron', sans-serif;
        letter-spacing: 2px;
        color: #fca5a5;
    }
    .dash-welcome p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.95rem;
        margin: 0;
        max-width: 550px;
    }
    .dash-welcome-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.25rem;
        flex-wrap: wrap;
    }
    .dash-welcome-actions .dash-btn {
        padding: 0.6rem 1.25rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s ease;
    }
    .dash-btn-white {
        background: #fff;
        color: #dc2626;
    }
    .dash-btn-white:hover {
        background: #fef2f2;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .dash-btn-outline {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .dash-btn-outline:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: translateY(-2px);
    }

    /* ===== KPI Cards ===== */
    .dash-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1200px) {
        .dash-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .dash-kpi-grid { grid-template-columns: 1fr; }
    }
    .dash-kpi {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .dash-kpi:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }
    .dash-kpi-stripe {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }
    .dash-kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .dash-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dash-kpi-icon svg { width: 22px; height: 22px; }
    .dash-kpi-value {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.35rem;
        color: var(--text);
    }
    .dash-kpi-label {
        font-size: 0.82rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .dash-kpi-sub {
        font-size: 0.78rem;
        margin-top: 0.5rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dash-kpi-sub a {
        color: var(--dash-accent);
        text-decoration: none;
        font-weight: 600;
    }
    .dash-kpi-sub a:hover { text-decoration: underline; }

    /* ===== Charts Section ===== */
    .dash-charts-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1024px) {
        .dash-charts-grid { grid-template-columns: 1fr; }
    }
    .dash-chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }
    .dash-chart-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border);
    }
    .dash-chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dash-chart-title .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .dash-chart-body {
        padding: 1.25rem 1.5rem;
    }
    .dash-chart-canvas {
        position: relative;
        width: 100%;
    }

    /* ===== Two Column Chart Row ===== */
    .dash-charts-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 900px) {
        .dash-charts-row-2 { grid-template-columns: 1fr; }
    }

    /* ===== Tables Section ===== */
    .dash-tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .dash-table-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }
    .dash-table-header {
        padding: 1.1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border);
    }
    .dash-table-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dash-table-link {
        font-size: 0.8rem;
        color: var(--dash-accent);
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .dash-table-link:hover { opacity: 0.7; }
    .dash-table-body {
        max-height: 340px;
        overflow-y: auto;
    }
    .dash-table-body::-webkit-scrollbar { width: 4px; }
    .dash-table-body::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 4px; }
    .dash-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.87rem;
    }
    .dash-table th {
        padding: 0.7rem 1.25rem;
        text-align: start;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        background: var(--th-bg);
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .dash-table td {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--border);
        vertical-align: middle;
    }
    .dash-table tr:hover td {
        background: var(--bg-hover);
    }
    .dash-table .user-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .dash-table .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .dash-table .user-name {
        font-weight: 600;
        color: var(--text);
        line-height: 1.3;
    }
    .dash-table .user-sub {
        font-size: 0.78rem;
        color: var(--text-secondary);
        max-width: 130px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dash-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .dash-badge-pending {
        background: rgba(217, 119, 6, 0.12);
        color: #d97706;
    }
    .dash-badge-live {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        animation: pulse-badge 2s infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .dash-review-btn {
        padding: 0.35rem 0.85rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        background: var(--dash-accent);
        color: #fff;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .dash-review-btn:hover {
        background: #b91c1c;
        color: #fff;
        transform: translateY(-1px);
    }
    .dash-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--text-secondary);
    }
    .dash-empty svg {
        width: 40px;
        height: 40px;
        opacity: 0.3;
        margin-bottom: 0.75rem;
    }
    .dash-amount {
        font-family: 'Orbitron', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .dash-amount-green { color: var(--dash-emerald); }
    .dash-amount-red { color: var(--dash-accent); }
    .dash-time {
        font-size: 0.78rem;
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<x-admin-header :title="__('Dashboard')" :breadcrumb="__('Welcome to Motorazad Management System')" />

{{-- ===== Welcome Banner ===== --}}
<div class="dash-welcome">
    <div class="dash-welcome-content">
        <h2>{{ __('Welcome to') }} <span>MOTORAZAD</span></h2>
        <p>{{ __('From here you can control everything — manage users, define roles and permissions, and monitor auction activity with ease and flexibility. Start your journey now.') }}</p>
        <div class="dash-welcome-actions">
            <a href="{{ route('admin.auctions.index') }}" class="dash-btn dash-btn-white">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ __('إدارة المزادات') }}
            </a>
            <a href="{{ route('admin.users.index') }}" class="dash-btn dash-btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                {{ __('Manage Users') }}
            </a>
        </div>
    </div>
</div>

{{-- ===== KPI Cards ===== --}}
<div class="dash-kpi-grid">
    {{-- Live Auctions --}}
    <div class="dash-kpi">
        <div class="dash-kpi-stripe" style="background: linear-gradient(90deg, #dc2626, #ef4444);"></div>
        <div class="dash-kpi-header">
            <div>
                <div class="dash-kpi-value">{{ number_format($stats['live_auctions']) }}</div>
                <div class="dash-kpi-label">{{ __('المزادات النشطة') }}</div>
            </div>
            <div class="dash-kpi-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
        <div class="dash-kpi-sub">
            <span class="dash-badge dash-badge-live">● LIVE</span>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="dash-kpi">
        <div class="dash-kpi-stripe" style="background: linear-gradient(90deg, #059669, #34d399);"></div>
        <div class="dash-kpi-header">
            <div>
                <div class="dash-kpi-value" style="font-size: 1.4rem;">{{ number_format($stats['total_revenue']) }} <span style="font-size: 0.8rem; font-family: 'Tajawal', sans-serif; font-weight: 500; color: var(--text-secondary);">SAR</span></div>
                <div class="dash-kpi-label">{{ __('إجمالي الإيداعات') }}</div>
            </div>
            <div class="dash-kpi-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
        </div>
    </div>

    {{-- Active Users --}}
    <div class="dash-kpi">
        <div class="dash-kpi-stripe" style="background: linear-gradient(90deg, #2563eb, #60a5fa);"></div>
        <div class="dash-kpi-header">
            <div>
                <div class="dash-kpi-value" style="font-size: 1.4rem;">
                    {{ number_format($stats['active_bidders'] + $stats['active_sellers']) }}
                </div>
                <div class="dash-kpi-label">{{ __('الأعضاء النشطين') }}</div>
            </div>
            <div class="dash-kpi-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="dash-kpi-sub">
            <span>{{ number_format($stats['active_bidders']) }} {{ __('مزايد') }}</span>
            <span style="opacity: 0.3;">|</span>
            <span>{{ number_format($stats['active_sellers']) }} {{ __('بائع') }}</span>
        </div>
    </div>

    {{-- Action Required --}}
    <div class="dash-kpi" style="{{ $stats['action_required'] > 0 ? 'border-color: rgba(220, 38, 38, 0.3);' : '' }}">
        <div class="dash-kpi-stripe" style="background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
        <div class="dash-kpi-header">
            <div>
                <div class="dash-kpi-value" style="font-size: 1.8rem; {{ $stats['action_required'] > 0 ? 'color: #dc2626;' : '' }}">{{ $stats['action_required'] }}</div>
                <div class="dash-kpi-label">{{ __('إجراءات مطلوبة') }}</div>
            </div>
            <div class="dash-kpi-icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
        </div>
        @if($stats['action_required'] > 0)
        <div class="dash-kpi-sub" style="flex-direction: column; align-items: flex-start; gap: 3px;">
            @if($stats['pending_withdrawals'] > 0)<a href="{{ route('admin.wallets.index') }}">● {{ $stats['pending_withdrawals'] }} {{ __('طلب سحب') }}</a>@endif
            @if($stats['pending_kyc'] > 0)<a href="{{ route('admin.users.index') }}">● {{ $stats['pending_kyc'] }} {{ __('هوية للتوثيق') }}</a>@endif
            @if($stats['pending_vehicles'] > 0)<a href="{{ route('admin.vehicles.index') }}">● {{ $stats['pending_vehicles'] }} {{ __('سيارة للمراجعة') }}</a>@endif
        </div>
        @else
        <div class="dash-kpi-sub" style="color: var(--dash-emerald);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ __('كل شيء على ما يرام') }}
        </div>
        @endif
    </div>
</div>

{{-- ===== Charts Row 1: Bids (Wide) + Vehicles (Donut) ===== --}}
<div class="dash-charts-grid">
    <div class="dash-chart-card">
        <div class="dash-chart-header">
            <div class="dash-chart-title">
                <span class="dot" style="background: #dc2626;"></span>
                {{ __('معدل المزايدات') }}
            </div>
            <span style="font-size: 0.78rem; color: var(--text-muted);">{{ __('آخر 7 أيام') }}</span>
        </div>
        <div class="dash-chart-body">
            <div class="dash-chart-canvas" style="height: 280px;"><canvas id="bidsChart"></canvas></div>
        </div>
    </div>

    <div class="dash-chart-card">
        <div class="dash-chart-header">
            <div class="dash-chart-title">
                <span class="dot" style="background: #d97706;"></span>
                {{ __('حالة المركبات') }}
            </div>
        </div>
        <div class="dash-chart-body">
            <div class="dash-chart-canvas" style="height: 280px;"><canvas id="vehiclesChart"></canvas></div>
        </div>
    </div>
</div>

{{-- ===== Charts Row 2: Users ===== --}}
<div style="margin-bottom: 2rem;">
    <div class="dash-chart-card">
        <div class="dash-chart-header">
            <div class="dash-chart-title">
                <span class="dot" style="background: #7c3aed;"></span>
                {{ __('نمو المستخدمين') }}
            </div>
            <span style="font-size: 0.78rem; color: var(--text-muted);">{{ __('آخر 6 أشهر') }}</span>
        </div>
        <div class="dash-chart-body">
            <div class="dash-chart-canvas" style="height: 220px;"><canvas id="usersChart"></canvas></div>
        </div>
    </div>
</div>

{{-- ===== Quick Action Tables ===== --}}
<div class="dash-tables-grid">
    {{-- Latest Bids --}}
    <div class="dash-table-card">
        <div class="dash-table-header">
            <div class="dash-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                {{ __('أحدث المزايدات') }}
            </div>
            <a href="{{ route('admin.bids.index') }}" class="dash-table-link">{{ __('عرض الكل') }} →</a>
        </div>
        <div class="dash-table-body">
            <table class="dash-table">
                <thead><tr><th>{{ __('المزايد') }}</th><th>{{ __('المبلغ') }}</th><th>{{ __('الوقت') }}</th></tr></thead>
                <tbody>
                    @forelse($latestBids as $bid)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #dc2626, #991b1b);">{{ mb_substr($bid->user->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <div class="user-name">{{ $bid->user->name ?? __('Unknown') }}</div>
                                    <div class="user-sub" title="{{ $bid->auction->title ?? '' }}">{{ $bid->auction->title ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="dash-amount dash-amount-green">{{ number_format($bid->amount) }} SAR</span></td>
                        <td><span class="dash-time">{{ $bid->created_at->diffForHumans() }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3"><div class="dash-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg><div>{{ __('لا توجد مزايدات حديثة') }}</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Vehicles --}}
    <div class="dash-table-card">
        <div class="dash-table-header">
            <div class="dash-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h2l2-4h6l2 4h2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/><path d="M3 17V9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"/></svg>
                {{ __('سيارات للمراجعة') }}
            </div>
            <a href="{{ route('admin.vehicles.index') }}" class="dash-table-link">{{ __('عرض الكل') }} →</a>
        </div>
        <div class="dash-table-body">
            <table class="dash-table">
                <thead><tr><th>{{ __('السيارة') }}</th><th>{{ __('البائع') }}</th><th>{{ __('إجراء') }}</th></tr></thead>
                <tbody>
                    @forelse($pendingVehiclesList as $vehicle)
                    <tr>
                        <td>
                            <div class="user-name" style="max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $vehicle->title }}">{{ $vehicle->title }}</div>
                            <div class="user-sub">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                        </td>
                        <td><span style="color: var(--text-secondary);">{{ $vehicle->seller->name ?? __('Unknown') }}</span></td>
                        <td><a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="dash-review-btn">{{ __('مراجعة') }}</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="3"><div class="dash-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><div>{{ __('لا توجد سيارات بانتظار المراجعة') }}</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Withdrawals --}}
    <div class="dash-table-card">
        <div class="dash-table-header">
            <div class="dash-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                {{ __('طلبات سحب معلقة') }}
            </div>
            <a href="{{ route('admin.wallets.index') }}" class="dash-table-link">{{ __('مراجعة المحافظ') }} →</a>
        </div>
        <div class="dash-table-body">
            <table class="dash-table">
                <thead><tr><th>{{ __('المستخدم') }}</th><th>{{ __('المبلغ') }}</th><th>{{ __('الحالة') }}</th></tr></thead>
                <tbody>
                    @forelse($recentWithdrawals as $withdrawal)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #d97706, #92400e);">{{ mb_substr($withdrawal->user->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <div class="user-name">{{ $withdrawal->user->name ?? __('Unknown') }}</div>
                                    <div class="user-sub">{{ $withdrawal->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="dash-amount dash-amount-red">{{ number_format($withdrawal->requested_amount) }} SAR</span></td>
                        <td><span class="dash-badge dash-badge-pending">{{ __('معلق') }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3"><div class="dash-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><div>{{ __('لا توجد طلبات سحب معلقة') }}</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.04)';
    const cardBg = isDark ? '#0e1421' : '#ffffff';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Tajawal', 'Inter', sans-serif";
    Chart.defaults.font.size = 12;

    // Tooltip styling
    Chart.defaults.plugins.tooltip.backgroundColor = isDark ? 'rgba(10, 14, 24, 0.95)' : 'rgba(255, 255, 255, 0.95)';
    Chart.defaults.plugins.tooltip.titleColor = isDark ? '#f1f5f9' : '#0f172a';
    Chart.defaults.plugins.tooltip.bodyColor = isDark ? '#94a3b8' : '#475569';
    Chart.defaults.plugins.tooltip.borderColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.padding = 14;
    Chart.defaults.plugins.tooltip.cornerRadius = 10;
    Chart.defaults.plugins.tooltip.boxPadding = 6;

    // 1. Bids Line Chart
    const bidsCtx = document.getElementById('bidsChart').getContext('2d');
    const bidsGradient = bidsCtx.createLinearGradient(0, 0, 0, 280);
    bidsGradient.addColorStop(0, 'rgba(220, 38, 38, 0.25)');
    bidsGradient.addColorStop(0.7, 'rgba(220, 38, 38, 0.03)');
    bidsGradient.addColorStop(1, 'rgba(220, 38, 38, 0.0)');

    new Chart(bidsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($bidsChart['labels']) !!},
            datasets: [{
                label: '{{ __("عدد المزايدات") }}',
                data: {!! json_encode($bidsChart['data']) !!},
                borderColor: '#dc2626',
                backgroundColor: bidsGradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: cardBg,
                pointBorderColor: '#dc2626',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#dc2626',
                pointHoverBorderColor: cardBg,
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, border: { display: false }, ticks: { padding: 10 } },
                x: { grid: { display: false }, border: { display: false }, ticks: { padding: 8 } }
            },
            plugins: { legend: { display: false } },
            interaction: { intersect: false, mode: 'index' },
        }
    });

    // 2. Vehicles Donut Chart
    const vehiclesCtx = document.getElementById('vehiclesChart').getContext('2d');
    new Chart(vehiclesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($vehiclesChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($vehiclesChart['data']) !!},
                backgroundColor: ['#059669', '#d97706', '#2563eb', '#dc2626'],
                borderWidth: 3,
                borderColor: cardBg,
                hoverOffset: 10,
                hoverBorderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            layout: { padding: 8 },
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } }
            }
        }
    });

    // 3. Users Bar Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const usersGradient = usersCtx.createLinearGradient(0, 0, 0, 220);
    usersGradient.addColorStop(0, '#7c3aed');
    usersGradient.addColorStop(1, '#a78bfa');

    new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($usersChart['labels']) !!},
            datasets: [{
                label: '{{ __("مستخدم جديد") }}',
                data: {!! json_encode($usersChart['data']) !!},
                backgroundColor: usersGradient,
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 36,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor, borderDash: [4, 4], drawBorder: false }, border: { display: false }, ticks: { padding: 10 } },
                x: { grid: { display: false }, border: { display: false }, ticks: { padding: 8 } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection
