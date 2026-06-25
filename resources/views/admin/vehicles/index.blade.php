@extends('layouts.admin')

@section('title', __('Vehicles'))

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

    .modal-backdrop { --bs-backdrop-zindex: 0 !important; }
    .modal { z-index: 1050 !important; }
    
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
    .scg-amber { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .scg-rose { background: linear-gradient(135deg, #e11d48, #f43f5e); }

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

    /* Premium Table Card Panel */
    .premium-panel {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: var(--shadow-premium);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }
    .panel-header-premium {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 28px;
        border-bottom: 1px solid var(--glass-border);
        background: rgba(248, 250, 252, 0.4);
    }
    [data-theme="dark"] .panel-header-premium {
        background: rgba(15, 23, 42, 0.3);
    }
    .panel-header-premium h2 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-color);
        margin: 0;
    }

    .table-responsive {
        padding: 1rem;
    }

    /* Card Overrides */
    .vehicle-grid-card {
        transition: all 0.2s ease;
    }
    .vehicle-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .vehicle-grid-card .quick-actions-overlay {
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(2px);
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .vehicle-grid-card:hover .quick-actions-overlay {
        opacity: 1;
    }
    .vehicle-card-img-top {
        height: 200px;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
    }
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">
    {{ __('Total:') }} <span style="color:var(--primary); font-weight:700;">{{ $stats['total'] }}</span> {{ __('Vehicle') }}
</div>
@endsection

@section('content')
<div class="page-header mb-4">
    <div>
        <h1 style="font-weight: 800; letter-spacing: -0.5px;">{{ __('Vehicles Management') }}</h1>
        <div class="breadcrumb" style="font-size: 0.85rem;"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Vehicles') }}</div>
    </div>
    <a href="{{ route('admin.vehicles.create') }}" class="btn d-inline-flex align-items-center gap-2 px-4 py-2 text-white font-weight-bold rounded-pill" style="background: linear-gradient(135deg, var(--primary), #4f46e5); border: none; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); transition: all 0.2s;">
        <i class="fa-solid fa-plus"></i>
        {{ __('Add New Vehicle') }}
    </a>
</div>

<div class="row mb-4 g-3">
    <!-- Total Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('Total Vehicles') }}</div>
            </div>
        </div>
    </div>
    <!-- Approved Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">{{ __('Approved Vehicles') }}</div>
            </div>
        </div>
    </div>
    <!-- Pending Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">{{ __('Pending Vehicles') }}</div>
            </div>
        </div>
    </div>
    <!-- Rejected Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">{{ __('Rejected Vehicles') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by title, VIN...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
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

<div class="view-toolbar mb-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="text-muted small me-2">{{ __('Show:') }}</span>
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchVehicles(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_vin" value="2" checked>
                    <label class="form-check-label" for="col_vin">{{ __('VIN Number') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="3" checked>
                    <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="4" checked disabled>
                    <label class="form-check-label" for="col_actions">{{ __('Actions') }}</label>
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

<div id="table-view-container" class="premium-panel mt-2 mb-4">
    <div class="panel-header-premium">
        <h2><i class="fa-solid fa-list-check me-2"></i> {{ __('Vehicles List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="vehicles-table" class="table w-100">
            <thead>
                <tr>
                    <th class="col-toggle-0" style="width: 80px;">{{ __('Image') }}</th>
                    <th class="col-toggle-1">{{ __('Title') }}</th>
                    <th class="col-toggle-2">{{ __('VIN Number') }}</th>
                    <th class="col-toggle-3">{{ __('Status') }}</th>
                    <th class="col-toggle-4" style="text-align: center; width: 250px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-vehicles-tbody">
                <tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View Container -->
<div id="grid-view-container" class="row g-4 d-none mb-4">
    <div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</div>
</div>

<!-- Pagination Container -->
<div class="card shadow-sm border-0 mt-3 mb-5">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.VehicleConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.vehicles.data') }}",
            destroy: "{{ url('admin/vehicles') }}/:id",
            approve: "{{ url('admin/vehicles') }}/:id/approve",
            reject: "{{ url('admin/vehicles') }}/:id/reject"
        },
        trans: {
            loading: "{{ __('Loading...') }}",
            errorLoading: "{{ __('Error loading data.') }}",
            noRecords: "{{ __('No matching records found') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            success: "{{ __('Success') }}",
            error: "{{ __('Error') }}",
            deleteVehicleTitle: "{{ __('Delete Vehicle?') }}",
            deleteVehicleText: "{{ __('This action cannot be undone! Make sure this vehicle is not linked to any auction.') }}",
            deleteVehicleError: "{{ __('Could not delete vehicle, it might be linked to an auction.') }}",
            approveVehicleTitle: "{{ __('Approve Vehicle?') }}",
            approveVehicleText: "{{ __('Are you sure you want to approve this vehicle?') }}",
            rejectVehicleTitle: "{{ __('Reject Vehicle') }}",
            reasonForRejection: "{{ __('Reason for Rejection') }}",
            rejectPlaceholder: "{{ __('Please write the reason for rejecting the vehicle...') }}",
            mustWriteReason: "{{ __('You must write a reason for rejection!') }}",
            yesDelete: "{{ __('Yes, delete!') }}",
            yesApprove: "{{ __('Yes, approve!') }}",
            reject: "{{ __('Reject') }}",
            cancel: "{{ __('Cancel') }}",
            view: "{{ __('View') }}",
            edit: "{{ __('Edit') }}",
            delete: "{{ __('Delete') }}",
            make: "{{ __('Make') }}",
            model: "{{ __('Model') }}",
            year: "{{ __('Year') }}",
            vin: "{{ __('VIN Number') }}"
        }
    };
</script>
<script src="{{ asset('js/admin/vehicles.js') }}"></script>
@endsection
