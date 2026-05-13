@extends('layouts.admin')

@section('title', __('Wallet Management'))

@section('css')
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
        <div class="stat-card blue h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_balance'], 2) }}</div>
            <div class="stat-label">{{ __('Total Liquidity (Balances)') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_deposits'], 2) }}</div>
            <div class="stat-label">{{ __('Total Deposits') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
        <div class="stat-card red h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_withdrawals'], 2) }}</div>
            <div class="stat-label">{{ __('Total Withdrawals') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-value">{{ $stats['count'] }}</div>
            <div class="stat-label">{{ __('Active Wallets Count') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Wallets List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="wallets-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Balance') }}</th>
                    <th>{{ __('Debt Ceiling') }}</th>
                    <th>{{ __('Debt Usage') }}</th>
                    <th>{{ __('Total Deposits') }}</th>
                    <th>{{ __('Total Withdrawals') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will fill this -->
            </tbody>
        </table>
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
    let walletsTable;

    $(document).ready(function() {
        walletsTable = $('#wallets-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.wallets.data') }}",
            columns: [
                { 
                    data: 'user',
                    render: function(data) {
                        return data ? `<strong>${data.name}</strong><br><small class="text-muted">${data.email}</small>` : '---';
                    }
                },
                { 
                    data: 'balance',
                    render: function(data) {
                        const cls = parseFloat(data) >= 0 ? 'balance-positive' : 'balance-negative';
                        return `<span class="balance-badge ${cls}">${parseFloat(data).toFixed(2)}</span>`;
                    }
                },
                { data: 'debt_ceiling' },
                { 
                    data: 'debt_usage',
                    render: function(data) {
                        return `<div class="progress" style="height: 10px;">
                                    <div class="progress-bar ${data > 80 ? 'bg-danger' : 'bg-primary'}" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small>${data}%</small>`;
                    }
                },
                { data: 'total_deposits' },
                { data: 'total_withdrawals' },
                { 
                    data: 'id',
                    render: function(data, type, row) {
                        const baseUrl = "{{ url('admin/wallets') }}";
                        return `
                            <div class="btn-group shadow-sm" style="border-radius: 8px;">
                                <a href="${baseUrl}/${data}/transactions" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold" title="{{ __('Advanced Transactions History') }}">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4H10a2 2 0 0 1-2-2v-4"/><circle cx="18" cy="12" r="1.5"/></svg>
                                    <span>{{ __('Transactions') }}</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-dark" onclick="openDebtModal(${data}, ${row.debt_ceiling})" title="{{ __('Debt Ceiling') }}">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                                    <span>{{ __('Ceiling') }}</span>
                                </button>
                            </div>
                        `;
                    }
                }
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
            }
        });

        // Handle Debt Form Submit
        $('#debtForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#debt_wallet_id').val();
            const baseUrl = "{{ url('admin/wallets') }}";
            
            $.ajax({
                url: `${baseUrl}/${id}/debt-ceiling`,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#debtModal').modal('hide');
                        walletsTable.ajax.reload(null, false);
                        Swal.fire("{{ __('Success') }}", response.message, 'success');
                    }
                },
                error: function(xhr) {
                    Swal.fire("{{ __('Error') }}", "{{ __('Operation failed') }}", 'error');
                }
            });
        });
    });

    function openDebtModal(id, currentCeiling) {
        $('#debt_wallet_id').val(id);
        $('#debt_ceiling_input').val(currentCeiling);
        $('#debtModal').modal('show');
    }
</script>
@endsection
