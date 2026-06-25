@extends('layouts.admin')

@section('title', __('Auctions'))

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
    .data-view-card .card-img-top {
        height: 180px;
        object-fit: cover;
        width: 100%;
        border-bottom: 1px solid var(--border);
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
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">
    {{ __('Total:') }} <span class="text-primary fs-5 font-weight-bold ml-1">{{ $stats['total'] }}</span> {{ __('Auction') }}
</div>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ __('Auctions Management') }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Auctions') }}
        </div>
    </div>
    <a href="{{ route('admin.auctions.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-4 rounded-pill">
        <i class="fa-solid fa-plus"></i>
        <span>{{ __('Add New Auction') }}</span>
    </a>
</div>

{{-- Upgraded Stats Row --}}
<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('Total Auctions') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['live'] }}</div>
                <div class="stat-label">{{ __('Live Auctions') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['scheduled'] }}</div>
                <div class="stat-label">{{ __('Scheduled Auctions') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card purple h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['completed'] }}</div>
                <div class="stat-label">{{ __('Completed Auctions') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Panel -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by title or vehicle...') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter_status" class="form-select select2-init" data-dropdown-parent="body">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="scheduled">{{ __('Scheduled') }}</option>
                    <option value="live">{{ __('Live') }}</option>
                    <option value="ended">{{ __('Ended') }}</option>
                    <option value="sold">{{ __('Sold') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-secondary w-100" onclick="fetchAuctions(1)">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchAuctions(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_image" value="0" checked>
                    <label class="form-check-label" for="col_image">{{ __('Image') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_title" value="1" checked disabled>
                    <label class="form-check-label" for="col_title">{{ __('Title') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_vehicle" value="2" checked>
                    <label class="form-check-label" for="col_vehicle">{{ __('Vehicle') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_price" value="3" checked>
                    <label class="form-check-label" for="col_price">{{ __('Start Price') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="4" checked>
                    <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_start" value="5" checked>
                    <label class="form-check-label" for="col_start">{{ __('Start Time') }}</label>
                </div>
                <div class="form-check mb-0">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_end" value="6" checked>
                    <label class="form-check-label" for="col_end">{{ __('End Time') }}</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="btn-group">
        <button type="button" class="btn btn-sm table-view-btn active" onclick="WJHTAKAdmin.toggleView('table')" title="{{ __('Table View') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <button type="button" class="btn btn-sm grid-view-btn" onclick="WJHTAKAdmin.toggleView('grid')" title="{{ __('Grid View') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </button>
    </div>
</div>

<!-- Table View -->
<div id="table-view-container" class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0 w-100" id="auctions-custom-table">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0 col-toggle-0" data-col="0">{{ __('Image') }}</th>
                    <th class="border-bottom-0 col-toggle-1" data-col="1">{{ __('Title') }}</th>
                    <th class="border-bottom-0 col-toggle-2" data-col="2">{{ __('Vehicle') }}</th>
                    <th class="border-bottom-0 col-toggle-3" data-col="3">{{ __('Start Price') }}</th>
                    <th class="border-bottom-0 col-toggle-4" data-col="4">{{ __('Status') }}</th>
                    <th class="border-bottom-0 col-toggle-5" data-col="5">{{ __('Start Time') }}</th>
                    <th class="border-bottom-0 col-toggle-6" data-col="6">{{ __('End Time') }}</th>
                    <th class="border-bottom-0 text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-auctions-tbody">
                <!-- Data injected via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View -->
<div id="grid-view-container" class="d-none">
    <div class="row g-4" id="custom-auctions-grid">
        <!-- Grid cards injected via AJAX -->
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-4" id="custom-pagination">
    <!-- Pagination injected via AJAX -->
</div>

@endsection

@section('js')
<script>
    window.AuctionConfig = {
        urls: {
            data: "{{ route('admin.auctions.data') }}",
            destroy: "{{ route('admin.auctions.destroy', ':id') }}"
        },
        trans: {
            errorLoading: "{{ __('Error loading data.') }}",
            noRecords: "{{ __('No matching records found') }}",
            loading: "{{ __('Loading...') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            deleteTitle: "{{ __('Delete Auction?') }}",
            deleteDesc: "{{ __('This action cannot be undone!') }}",
            yesDelete: "{{ __('Yes, delete!') }}",
            cancel: "{{ __('Cancel') }}",
            unexpectedError: "{{ __('An unexpected error occurred.') }}",
            noImage: "{{ __('No Image') }}"
        },
        csrf: "{{ csrf_token() }}"
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/admin/auctions.js') }}"></script>
@endsection
