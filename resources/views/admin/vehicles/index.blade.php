@extends('layouts.admin')

@section('title', __('Vehicles'))

@section('css')
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

    /* DataTable Overrides */
    .dataTables_wrapper { color: var(--text-color); }
    .dataTables_wrapper .dataTables_length select, 
    .dataTables_wrapper .dataTables_filter input {
        background-color: var(--bg-input) !important;
        color: var(--text-color) !important;
        border: 1px solid var(--border) !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease;
    }
    .dataTables_wrapper .dataTables_length select:focus, 
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #6366f1 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
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

    .dataTables_wrapper .dataTables_info {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-top: 16px;
    }
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 16px;
    }
    .paginate_button {
        border-radius: 8px !important;
        padding: 6px 12px !important;
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

<div class="row">
    <!-- Total Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-purple">
            <div class="scg-value">{{ $stats['total'] }}</div>
            <div class="scg-label">{{ __('Total Vehicles') }}</div>
            <i class="fa-solid fa-car scg-icon"></i>
        </div>
    </div>
    <!-- Approved Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-emerald">
            <div class="scg-value">{{ $stats['approved'] }}</div>
            <div class="scg-label">{{ __('Approved Vehicles') }}</div>
            <i class="fa-solid fa-circle-check scg-icon"></i>
        </div>
    </div>
    <!-- Pending Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-amber">
            <div class="scg-value">{{ $stats['pending'] }}</div>
            <div class="scg-label">{{ __('Pending Vehicles') }}</div>
            <i class="fa-solid fa-clock scg-icon"></i>
        </div>
    </div>
    <!-- Rejected Vehicles -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card-gradient scg-rose">
            <div class="scg-value">{{ $stats['rejected'] }}</div>
            <div class="scg-label">{{ __('Rejected Vehicles') }}</div>
            <i class="fa-solid fa-circle-xmark scg-icon"></i>
        </div>
    </div>
</div>

<div class="premium-panel mt-2">
    <div class="panel-header-premium">
        <h2><i class="fa-solid fa-list-check me-2"></i> {{ __('Vehicles List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="vehicles-table" class="table w-100">
            <thead>
                <tr>
                    <th style="width: 80px;">{{ __('Image') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('VIN Number') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th style="text-align: center; width: 250px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('js')
<script>
    var vehiclesDataUrl = "{{ route('admin.vehicles.data') }}";
</script>

<script>
    let vehiclesTable;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        vehiclesTable = $('#vehicles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: vehiclesDataUrl,
            columns: [
                { data: 'image', orderable: false, searchable: false },
                { data: 'title' },
                { data: 'vin_number' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "sProcessing": "{{ __('Loading...') }}",
                "sLengthMenu": "{{ __('Show _MENU_ entries') }}",
                "sZeroRecords": "{{ __('No matching records found') }}",
                "sInfo": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                "sSearch": "{{ __('Search:') }}",
                "oPaginate": {
                    "sFirst": "{{ __('First') }}",
                    "sPrevious": "{{ __('Previous') }}",
                    "sNext": "{{ __('Next') }}",
                    "sLast": "{{ __('Last') }}"
                }
            },
            drawCallback: function() {
                // Style pagination buttons in premium theme
                $('.paginate_button').addClass('btn btn-sm mx-1');
                $('.paginate_button.current').addClass('btn-primary').css('color', 'white');
            }
        });
    });

    function deleteVehicle(id) {
        let url = "{{ route('admin.vehicles.destroy', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Delete Vehicle?') }}",
            text: "{{ __('This action cannot be undone! Make sure this vehicle is not linked to any auction.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, delete!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            vehiclesTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error("{{ __('Could not delete vehicle, it might be linked to an auction.') }}");
                    }
                });
            }
        });
    }

    function approveVehicle(id) {
        let url = "{{ route('admin.vehicles.approve', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Approve Vehicle?') }}",
            text: "{{ __('Are you sure you want to approve this vehicle?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, approve!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            vehiclesTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }

    function rejectVehicle(id) {
        let url = "{{ route('admin.vehicles.reject', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Reject Vehicle') }}",
            input: 'textarea',
            inputLabel: "{{ __('Reason for Rejection') }}",
            inputPlaceholder: "{{ __('Please write the reason for rejecting the vehicle...') }}",
            inputAttributes: {
                'aria-label': "{{ __('Reason for Rejection') }}"
            },
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Reject') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            inputValidator: (value) => {
                if (!value) {
                    return "{{ __('You must write a reason for rejection!') }}";
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        rejection_reason: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            vehiclesTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
