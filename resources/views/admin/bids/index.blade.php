@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'سجل المزايدات العامة' : 'Global Bids Log')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    /* Premium UI & Theme Styling */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.85);
        --glass-border: rgba(51, 65, 85, 0.8);
    }

    /* Stats Row upgrade */
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
        transform: translateY(-3px) scale(1.01);
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
    .scg-blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }

    .scg-value {
        font-size: 1.9rem;
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

    .table {
        border-collapse: separate !important;
        border-spacing: 0 8px !important;
    }
    .table thead th {
        border: none !important;
        background: transparent !important;
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 12px 16px !important;
    }
    .table tbody tr {
        background: rgba(255,255,255,0.05) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
        border-radius: 12px !important;
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        transform: scale(1.005);
        box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;
        background: rgba(255,255,255,0.1) !important;
    }
    .table tbody td {
        border-top: 1px solid var(--border) !important;
        border-bottom: 1px solid var(--border) !important;
        padding: 16px !important;
        vertical-align: middle !important;
    }
    .table tbody td:first-child {
        border-left: 1px solid var(--border) !important;
        border-radius: 12px 0 0 12px !important;
    }
    .table tbody td:last-child {
        border-right: 1px solid var(--border) !important;
        border-radius: 0 12px 12px 0 !important;
    }
    html[dir="rtl"] .table tbody td:first-child {
        border-left: none !important;
        border-right: 1px solid var(--border) !important;
        border-radius: 0 12px 12px 0 !important;
    }
    html[dir="rtl"] .table tbody td:last-child {
        border-right: none !important;
        border-left: 1px solid var(--border) !important;
        border-radius: 12px 0 0 12px !important;
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ app()->getLocale() === 'ar' ? 'سجل المزايدات العامة' : 'Global Bids Log' }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ app()->getLocale() === 'ar' ? 'المزايدات' : 'Bids' }}
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <!-- Total Bids -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22v-6M17 22v-9M7 22v-3M2 22h20M22 2H2v4h20V2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي المزايدات' : 'Total Bids' }}</div>
            </div>
        </div>
    </div>
    <!-- Auto Bids -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card purple h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['auto_bids']) }}</div>
                <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'مزايدات تلقائية (Auto Bids)' : 'Auto Bids Count' }}</div>
            </div>
        </div>
    </div>
    <!-- Active Bids -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['active_bids']) }}</div>
                <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'مزايدات نشطة حالياً' : 'Active Bids Count' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by bidder, vehicle...') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter_type" class="form-select select2-init">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'طريقة المزايدة (الكل)' : 'All Types' }}</option>
                    <option value="auto">{{ app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto' }}</option>
                    <option value="manual">{{ app()->getLocale() === 'ar' ? 'يدوي' : 'Manual' }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'الحالة (الكل)' : 'All Status' }}</option>
                    <option value="active">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</option>
                    <option value="outbid">{{ app()->getLocale() === 'ar' ? 'تخطي' : 'Outbid' }}</option>
                    <option value="winner">{{ app()->getLocale() === 'ar' ? 'فائز' : 'Winner' }}</option>
                    <option value="cancelled">{{ app()->getLocale() === 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-secondary w-100" id="btn-filter">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="view-toolbar">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="text-muted small me-2">{{ __('Show:') }}</span>
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchBids(1)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M12 3v18"/><path d="M3 12h18"/></svg>
                {{ __('Columns') }}
            </button>
            <div class="dropdown-menu shadow-sm p-3" style="min-width: 200px;">
                <h6 class="dropdown-header px-0 text-primary">{{ __('Toggle Columns') }}</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_bidder" value="0" checked disabled>
                    <label class="form-check-label" for="col_bidder">{{ app()->getLocale() === 'ar' ? 'المزايد' : 'Bidder' }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_auction" value="1" checked>
                    <label class="form-check-label" for="col_auction">{{ app()->getLocale() === 'ar' ? 'المزاد / المركبة' : 'Auction / Vehicle' }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_amount" value="2" checked>
                    <label class="form-check-label" for="col_amount">{{ app()->getLocale() === 'ar' ? 'مبلغ المزايدة' : 'Bid Amount' }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_type" value="3" checked>
                    <label class="form-check-label" for="col_type">{{ app()->getLocale() === 'ar' ? 'طريقة المزايدة' : 'Type' }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="4" checked>
                    <label class="form-check-label" for="col_status">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_time" value="5" checked>
                    <label class="form-check-label" for="col_time">{{ app()->getLocale() === 'ar' ? 'التوقيت' : 'Time' }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_ip" value="6" checked>
                    <label class="form-check-label" for="col_ip">{{ app()->getLocale() === 'ar' ? 'عنوان IP' : 'IP Address' }}</label>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-outline-primary active" id="btn-view-table" onclick="toggleView('table')" title="{{ __('Table View') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-view-grid" onclick="toggleView('grid')" title="{{ __('Grid View') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        </button>
    </div>
</div>

{{-- Table Panel Container --}}
<div id="table-view-container" class="card shadow-sm border-0">
    <div class="table-responsive">
        <table id="bids-custom-table" class="table table-hover table-striped align-middle mb-0 w-100">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0 col-toggle-0">{{ app()->getLocale() === 'ar' ? 'المزايد' : 'Bidder' }}</th>
                    <th class="border-bottom-0 col-toggle-1">{{ app()->getLocale() === 'ar' ? 'المزاد / المركبة' : 'Auction / Vehicle' }}</th>
                    <th class="border-bottom-0 col-toggle-2">{{ app()->getLocale() === 'ar' ? 'مبلغ المزايدة' : 'Bid Amount' }}</th>
                    <th class="border-bottom-0 col-toggle-3">{{ app()->getLocale() === 'ar' ? 'طريقة المزايدة' : 'Type' }}</th>
                    <th class="border-bottom-0 col-toggle-4">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th class="border-bottom-0 col-toggle-5">{{ app()->getLocale() === 'ar' ? 'التوقيت' : 'Time' }}</th>
                    <th class="border-bottom-0 col-toggle-6">{{ app()->getLocale() === 'ar' ? 'عنوان IP' : 'IP Address' }}</th>
                </tr>
            </thead>
            <tbody id="custom-bids-tbody">
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View Container -->
<div id="grid-view-container" class="row g-3 d-none">
    <div class="col-12 text-center py-4 text-muted">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}
    </div>
</div>

<!-- Pagination Container (Shared) -->
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>
@endsection

@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.BidConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.bids.data') }}"
        },
        trans: {
            loading: "{{ __('Loading...') }}",
            errorLoading: "{{ __('Error loading bids data.') }}",
            noRecords: "{{ __('No bids found.') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            bidder: "{{ __('Bidder') }}",
            auctionVehicle: "{{ __('Auction / Vehicle') }}",
            amount: "{{ __('Bid Amount') }}",
            type: "{{ __('Type') }}",
            status: "{{ __('Status') }}",
            time: "{{ __('Time') }}",
            ipAddress: "{{ __('IP Address') }}"
        }
    };
</script>
<script src="{{ asset('js/admin/bids.js') }}"></script>
@endsection
