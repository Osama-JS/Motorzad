@extends('layouts.admin')

@section('title', __('Bank Accounts Management'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .modal-backdrop {
        --bs-backdrop-zindex: 0 !important;
    }
    .modal {
        z-index: 1050 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Stats Row -->
    <div class="row mb-4 g-3">
        <!-- Total Bank Accounts -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card blue h-100 stat-card-compact">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">{{ __('Total Accounts') }}</div>
                </div>
            </div>
        </div>
        <!-- Active Bank Accounts -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card green h-100 stat-card-compact">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['active'] }}</div>
                    <div class="stat-label">{{ __('Active Accounts') }}</div>
                </div>
            </div>
        </div>
        <!-- Inactive Bank Accounts -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card red h-100 stat-card-compact">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['inactive'] }}</div>
                    <div class="stat-label">{{ __('Inactive Accounts') }}</div>
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
                        <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by bank, IBAN, beneficiary...') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="filter_status" class="form-select select2-init">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
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

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <!-- View Toolbar -->
            <div class="view-toolbar mb-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-2">{{ __('Show:') }}</span>
                        <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchAccounts(1)">
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
                                <input class="form-check-input col-toggle" type="checkbox" id="col_logo" value="0" checked>
                                <label class="form-check-label" for="col_logo">{{ __('Logo') }}</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input col-toggle" type="checkbox" id="col_bank" value="1" checked disabled>
                                <label class="form-check-label" for="col_bank">{{ __('Bank Name') }}</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input col-toggle" type="checkbox" id="col_iban" value="2" checked>
                                <label class="form-check-label" for="col_iban">{{ __('IBAN') }}</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input col-toggle" type="checkbox" id="col_beneficiary" value="3" checked>
                                <label class="form-check-label" for="col_beneficiary">{{ __('Beneficiary') }}</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="4" checked>
                                <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="5" checked disabled>
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

            <div id="table-view-container" class="card shadow-sm border-0 mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="mb-0">{{ __('Bank Accounts List') }}</h6>
                    <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                        <i class="fas fa-plus me-2"></i> {{ __('Add New Bank Account') }}
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0" id="bankAccountsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-0">{{ __('Logo') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-1">{{ __('Bank Name') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 col-toggle-2">{{ __('IBAN') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 col-toggle-3">{{ __('Beneficiary') }}</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-4">{{ __('Status') }}</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-5">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="custom-bank-accounts-tbody">
                                <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</td></tr>
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
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="bankAccountModal" tabindex="-1" role="dialog" aria-labelledby="bankAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankAccountModalLabel">
                    <i class="fas fa-university me-2 text-primary"></i>
                    <span id="modal-title-text">{{ __('Add Bank Account') }}</span>
                </h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bankAccountForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="account_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="bank_name" class="form-control-label">{{ __('Bank Name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building text-xs"></i></span>
                            <input class="form-control" type="text" name="bank_name" id="bank_name" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="iban" class="form-control-label">{{ __('IBAN') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode text-xs"></i></span>
                            <input class="form-control" type="text" name="iban" id="iban" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="beneficiary_name" class="form-control-label">{{ __('Beneficiary Name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user text-xs"></i></span>
                            <input class="form-control" type="text" name="beneficiary_name" id="beneficiary_name" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="logo" class="form-control-label">{{ __('Bank Logo') }}</label>
                        <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
                        <div id="logo-preview" class="mt-2 text-center" style="display:none;">
                            <img src="" id="current-logo" class="img-thumbnail shadow-sm" width="100">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">{{ __('Active Status') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> {{ __('Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save me-1"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.BankAccountConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.bank-accounts.data') }}",
            store: "{{ route('admin.bank-accounts.store') }}",
            edit: "{{ url('admin/bank-accounts') }}/:id/edit",
            update: "{{ url('admin/bank-accounts') }}/:id",
            destroy: "{{ url('admin/bank-accounts') }}/:id",
            toggle: "{{ url('admin/bank-accounts') }}/:id/toggle-active"
        },
        trans: {
            loading: "{{ __('Loading...') }}",
            errorLoading: "{{ __('Error loading data.') }}",
            noRecords: "{{ __('No matching records found') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            areYouSure: "{{ __('Are you sure?') }}",
            confirmStatusChange: "{{ __('You want to change the account status.') }}",
            yesToggle: "{{ __('Yes, toggle it!') }}",
            cancel: "{{ __('Cancel') }}",
            yesDelete: "{{ __('Yes, delete it!') }}",
            deleteDesc: "{{ __('This action cannot be undone!') }}",
            unexpectedError: "{{ __('Unexpected error occurred.') }}",
            addTitle: "{{ __('Add Bank Account') }}",
            editTitle: "{{ __('Edit Bank Account') }}",
            processing: "{{ __('Processing...') }}",
            saveChanges: "{{ __('Save Changes') }}"
        }
    };
</script>
<script src="{{ asset('js/admin/bank_accounts.js') }}"></script>
@endsection
