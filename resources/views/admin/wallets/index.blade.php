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

<!-- Modal for adjusting balance -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Financial Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transactionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Operation Type') }}</label>
                        <select name="type" class="form-control" required>
                            <option value="credit">{{ __('Deposit') }} (Credit)</option>
                            <option value="debit">{{ __('Withdraw') }} (Debit)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Description / Notes') }}</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Attachment (Optional)') }}</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Transaction') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for updating debt ceiling -->
<div class="modal fade" id="debtModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Update Debt Ceiling') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="debtForm">
                @csrf
                <input type="hidden" id="debt_wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Allowed Debt Ceiling') }}</label>
                        <input type="number" name="debt_ceiling" id="debt_ceiling_input" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
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
                        return `
                            <div class="btn-group">
                                <a href="/admin/wallets/${data}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <button type="button" class="btn btn-sm btn-success" onclick="openTransactionModal(${data})" title="{{ __('Add Transaction') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="openDebtModal(${data}, ${row.debt_ceiling})" title="{{ __('Debt Ceiling') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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

        // Handle Transaction Form Submit
        $('#transactionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#wallet_id').val();
            const formData = new FormData(this);
            
            $.ajax({
                url: `/admin/wallets/${id}/transaction`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#transactionModal').modal('hide');
                        walletsTable.ajax.reload(null, false);
                        Swal.fire("{{ __('Success') }}", response.message, 'success');
                    }
                },
                error: function(xhr) {
                    Swal.fire("{{ __('Error') }}", "{{ __('Operation failed, please check the data') }}", 'error');
                }
            });
        });

        // Handle Debt Form Submit
        $('#debtForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#debt_wallet_id').val();
            
            $.ajax({
                url: `/admin/wallets/${id}/debt-ceiling`,
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

    function openTransactionModal(id) {
        $('#wallet_id').val(id);
        $('#transactionForm')[0].reset();
        $('#transactionModal').modal('show');
    }

    function openDebtModal(id, currentCeiling) {
        $('#debt_wallet_id').val(id);
        $('#debt_ceiling_input').val(currentCeiling);
        $('#debtModal').modal('show');
    }
</script>
@endsection
