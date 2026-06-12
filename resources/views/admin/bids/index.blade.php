@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'سجل المزايدات العامة' : 'Global Bids Log')

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

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ app()->getLocale() === 'ar' ? 'سجل المزايدات العامة' : 'Global Bids Log' }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ app()->getLocale() === 'ar' ? 'المزايدات' : 'Bids' }}
        </div>
    </div>
</div>

{{-- Upgraded Stats Row --}}
<div class="row mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card-gradient scg-blue">
            <div class="scg-value">{{ number_format($stats['total']) }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'إجمالي المزايدات' : 'Total Bids' }}</div>
            <i class="fa-solid fa-gavel scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card-gradient scg-purple">
            <div class="scg-value">{{ number_format($stats['auto_bids']) }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'مزايدات تلقائية (Auto Bids)' : 'Auto Bids Count' }}</div>
            <i class="fa-solid fa-robot scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card-gradient scg-emerald">
            <div class="scg-value">{{ number_format($stats['active_bids']) }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'مزايدات نشطة حالياً' : 'Active Bids Count' }}</div>
            <i class="fa-solid fa-circle-check scg-icon"></i>
        </div>
    </div>
</div>

{{-- Table Panel Container --}}
<div class="premium-panel">
    <div class="panel-header-premium">
        <h2>{{ app()->getLocale() === 'ar' ? 'قائمة المزايدات الحية على المنصة' : 'Platform Live Bids List' }}</h2>
    </div>
    <div class="table-responsive">
        <table id="bids-table" class="table w-100">
            <thead>
                <tr>
                    <th>{{ app()->getLocale() === 'ar' ? 'المزايد' : 'Bidder' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'المزاد / المركبة' : 'Auction / Vehicle' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'مبلغ المزايدة' : 'Bid Amount' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'طريقة المزايدة' : 'Type' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'التوقيت' : 'Time' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'عنوان IP' : 'IP Address' }}</th>
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
    var bidsDataUrl = "{{ route('admin.bids.data') }}";
</script>

<script>
    let bidsTable;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        bidsTable = $('#bids-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: bidsDataUrl,
            columns: [
                { data: 'user' },
                { data: 'auction' },
                { data: 'amount' },
                { data: 'type' },
                { data: 'status' },
                { data: 'time' },
                { data: 'ip' }
            ],
            order: [[5, 'desc']], // Sort by time column descending
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
            }
        });
    });
</script>
@endsection
