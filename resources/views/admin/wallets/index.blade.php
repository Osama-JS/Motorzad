@extends('layouts.admin')

@section('title', __('Wallet Management'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .table td { vertical-align: middle; }
    .balance-badge {
        font-weight: 700;
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }
    .balance-positive { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .balance-negative { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Wallet Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Wallets') }}</div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_balance'], 2) }}</div>
                <div class="stat-label">{{ __('Total Liquidity (Balances)') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_deposits'], 2) }}</div>
                <div class="stat-label">{{ __('Total Deposits') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_withdrawals'], 2) }}</div>
                <div class="stat-label">{{ __('Total Withdrawals') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['count'] }}</div>
                <div class="stat-label">{{ __('Active Wallets Count') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by user name, email...') }}">
                </div>
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchWallets(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_user" value="0" checked disabled>
                    <label class="form-check-label" for="col_user">{{ __('User') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_balance" value="1" checked>
                    <label class="form-check-label" for="col_balance">{{ __('Balance') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_debt" value="2" checked>
                    <label class="form-check-label" for="col_debt">{{ __('Debt Ceiling') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_usage" value="3" checked>
                    <label class="form-check-label" for="col_usage">{{ __('Debt Usage') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_deposits" value="4" checked>
                    <label class="form-check-label" for="col_deposits">{{ __('Total Deposits') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_withdrawals" value="5" checked>
                    <label class="form-check-label" for="col_withdrawals">{{ __('Total Withdrawals') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="6" checked disabled>
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
        <h6 class="mb-0">{{ __('Wallets List') }}</h6>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-3">
            <table id="wallets-table" class="table align-items-center mb-0 w-100">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-0">{{ __('User') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-1">{{ __('Balance') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-2">{{ __('Debt Ceiling') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-3">{{ __('Debt Usage') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-4">{{ __('Total Deposits') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-5">{{ __('Total Withdrawals') }}</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 col-toggle-6">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="custom-wallets-tbody">
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
<!-- Modal for updating debt ceiling -->
<div class="modal fade" id="debtModal" tabindex="-1" aria-labelledby="debtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="debtForm">
                @csrf
                <input type="hidden" id="debt_wallet_id" name="wallet_id">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4 d-flex align-items-center gap-2" id="debtModalLabel">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-gold, #f59e0b)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>{{ __('Update Debt Ceiling') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="card border-0 shadow-none mb-0" style="background: var(--bg-body); border-radius: var(--radius-lg);">
                        <div class="card-body p-4">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold">{{ __('Allowed Debt Ceiling') }} <span class="text-danger">*</span></label>
                                <input type="number" name="debt_ceiling" id="debt_ceiling_input" class="form-control form-control-lg px-3 py-2" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-ghost px-4 py-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    window.WalletConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.wallets.data') }}",
            debtCeiling: "{{ url('admin/wallets') }}/:id/debt-ceiling"
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
            operationFailed: "{{ __('Operation failed') }}"
        }
    };
</script>
<script src="{{ asset('js/admin/wallets.js') }}"></script>
@endsection
