@extends('layouts.admin')

@section('title', __('إدارة طلبات الإيداع'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .table td { vertical-align: middle; }
    .status-badge {
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    .receipt-preview-img {
        max-width: 100%;
        max-height: 250px;
        border-radius: 8px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .receipt-preview-img:hover {
        transform: scale(1.02);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('طلبات إيداع الضمان المالي') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('طلبات الإيداع') }}</div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_pending'] }}</div>
                <div class="stat-label">{{ __('طلبات معلقة بالانتظار') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_approved'], 2) }}</div>
                <div class="stat-label">{{ __('إجمالي المبالغ المعتمدة') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">{{ __('إجمالي طلبات الإيداع') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by bidder, bank...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchDeposits(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_id" value="0" checked>
                    <label class="form-check-label" for="col_id">{{ __('ID') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_bidder" value="1" checked disabled>
                    <label class="form-check-label" for="col_bidder">{{ __('المزايد') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_bank" value="2" checked>
                    <label class="form-check-label" for="col_bank">{{ __('البنك المحوّل إليه') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_amount" value="3" checked>
                    <label class="form-check-label" for="col_amount">{{ __('المبلغ') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="4" checked>
                    <label class="form-check-label" for="col_status">{{ __('حالة الطلب') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_date" value="5" checked>
                    <label class="form-check-label" for="col_date">{{ __('تاريخ الطلب') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="6" checked disabled>
                    <label class="form-check-label" for="col_actions">{{ __('الإجراءات') }}</label>
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

<div id="table-view-container" class="card shadow-sm border-0 mb-4">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
        <h6 class="mb-0">{{ __('قائمة طلبات الإيداع') }}</h6>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-3">
            <table id="deposits-table" class="table align-items-center mb-0 w-100">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-0">{{ __('ID') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-1">{{ __('المزايد') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-2">{{ __('البنك المحوّل إليه') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-3">{{ __('المبلغ') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-4">{{ __('حالة الطلب') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-5">{{ __('تاريخ الطلب') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-6">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody id="custom-deposits-tbody">
                    <tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Grid View Container -->
<div id="grid-view-container" class="row g-3 d-none mb-4">
    <div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</div>
</div>

<!-- Pagination Container -->
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>

@endsection

@section('modals')
<!-- Modal for Viewing & Processing Deposit -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <form id="depositForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4 d-flex align-items-center gap-2" id="depositModalLabel">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                        <span>{{ __('معالجة طلب الإيداع وتأكيد الرصيد') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('المزايد') }}:</strong> <span id="view_user_name"></span><br>
                            <small class="text-muted" id="view_user_email"></small>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('المبلغ المحوّل') }}:</strong> <span id="view_requested_amount" class="text-success fw-bold fs-5"></span> <span class="text-success fw-bold">SAR</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('البنك المستهدف') }}:</strong> <span id="view_bank_name"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('تاريخ الطلب') }}:</strong> <span id="view_date"></span>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        {{-- Process Actions --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('تغيير حالة الطلب') }} <span class="text-danger">*</span></label>
                                <select name="status" id="form_status" class="form-select form-select-lg" required>
                                    <option value="pending">{{ __('معلق بالانتظار') }}</option>
                                    <option value="approved">{{ __('مقبول (اعتماد وشحن المحفظة)') }}</option>
                                    <option value="rejected">{{ __('مرفوض') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ملاحظة الإدارة (تظهر للمستخدم)') }}</label>
                                <textarea name="admin_note" id="form_admin_note" class="form-control" rows="4" placeholder="مثال: تم تأكيد استلام الحوالة البنكية وشحن الرصيد بنجاح."></textarea>
                            </div>
                        </div>

                        {{-- Receipt Visual Verification --}}
                        <div class="col-md-6 text-center">
                            <label class="form-label fw-bold d-block text-start">{{ __('إثبات التحويل (الوصل)') }}</label>
                            <div id="receipt_container" class="mt-2">
                                <a id="receipt_link" href="#" target="_blank" title="اضغط لفتح الصورة بحجمها الكامل">
                                    <img id="view_receipt_img" src="" class="receipt-preview-img" alt="وصل التحويل البنكي">
                                </a>
                                <p class="text-muted small mt-2">ⓘ اضغط على الوصل أعلاه لمعاينته بحجم كامل.</p>
                            </div>
                            <div id="no_receipt_msg" class="text-muted py-5" style="display:none;">
                                {{ __('لا يوجد إثبات مرفق') }}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="background:#10b981; border-color:#10b981;">{{ __('تحديث حالة الإيداع') }}</button>
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.DepositConfig = {
        csrf: '{{ csrf_token() }}',
        storageBaseUrl: "{{ asset('storage') }}",
        urls: {
            data: "{{ route('admin.deposits.index') }}",
            details: "{{ url('admin/deposits') }}/:id",
            process: "{{ url('admin/deposits') }}/:id/process"
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
            operationFailed: "{{ __('Operation failed') }}",
            detailsLoadFailed: "{{ __('Could not load deposit details.') }}",
            alreadyProcessed: "{{ __('تم معالجة الطلب مسبقاً') }}",
            updateStatusBtn: "{{ __('تحديث حالة الإيداع') }}"
        }
    };
</script>
<script src="{{ asset('js/admin/deposits.js') }}"></script>
@endsection
