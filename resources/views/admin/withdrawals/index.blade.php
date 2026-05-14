@extends('layouts.admin')

@section('title', __('Withdrawal Requests'))

@section('css')
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
    .status-processing { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .status-approved, .status-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Withdrawal Requests') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Withdrawals') }}</div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card gold h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total_pending'] }}</div>
            <div class="stat-label">{{ __('Pending Requests') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_approved'], 2) }}</div>
            <div class="stat-label">{{ __('Total Approved Amount') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card blue h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total_requests'] }}</div>
            <div class="stat-label">{{ __('Total Requests') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Withdrawals List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="withdrawals-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('Request ID') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Requested Amount') }}</th>
                    <th>{{ __('Approved Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
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
<!-- Modal for Viewing & Processing Withdrawal -->
<div class="modal fade" id="withdrawalModal" tabindex="-1" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form id="withdrawalForm">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4 d-flex align-items-center gap-2" id="withdrawalModalLabel">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-gold, #f59e0b)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>{{ __('Process Withdrawal Request') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('User') }}:</strong> <span id="view_user_name"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Requested Amount') }}:</strong> <span id="view_requested_amount" class="text-primary fw-bold"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Current Status') }}:</strong> <span id="view_status"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Request Date') }}:</strong> <span id="view_date"></span>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Update Status') }} <span class="text-danger">*</span></label>
                            <select name="status" id="form_status" class="form-select form-select-lg" required>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="processing">{{ __('Processing') }}</option>
                                <option value="approved">{{ __('Approved') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="rejected">{{ __('Rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Approved Amount') }}</label>
                            <input type="number" name="approved_amount" id="form_approved_amount" class="form-control form-control-lg" step="0.01" min="0">
                            <small class="text-muted">{{ __('Leave empty to approve full amount') }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Payment Method / Reference') }}</label>
                        <input type="text" name="payment_method" id="form_payment_method" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Admin Notes') }}</label>
                        <textarea name="admin_notes" id="form_admin_notes" class="form-control" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-ghost px-4 py-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let withdrawalsTable;
    let currentWithdrawalId = null;

    $(document).ready(function() {
        withdrawalsTable = $('#withdrawals-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.withdrawals.index') }}",
            columns: [
                { data: 'id' },
                { 
                    data: 'user',
                    render: function(data) {
                        return data ? `<strong>${data.name}</strong><br><small class="text-muted">${data.email}</small>` : '---';
                    }
                },
                { 
                    data: 'requested_amount',
                    render: function(data) {
                        return `<strong>${parseFloat(data).toFixed(2)}</strong>`;
                    }
                },
                { 
                    data: 'approved_amount',
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) : '---';
                    }
                },
                { 
                    data: 'status',
                    render: function(data) {
                        const statusClasses = {
                            'pending': 'status-pending',
                            'processing': 'status-processing',
                            'approved': 'status-approved',
                            'completed': 'status-completed',
                            'rejected': 'status-rejected'
                        };
                        const statusLabels = {
                            'pending': '{{ __("Pending") }}',
                            'processing': '{{ __("Processing") }}',
                            'approved': '{{ __("Approved") }}',
                            'completed': '{{ __("Completed") }}',
                            'rejected': '{{ __("Rejected") }}'
                        };
                        return `<span class="status-badge ${statusClasses[data] || 'status-pending'}">${statusLabels[data] || data}</span>`;
                    }
                },
                { 
                    data: 'created_at',
                    render: function(data) {
                        return new Date(data).toLocaleDateString();
                    }
                },
                { 
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn btn-sm btn-info d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-white shadow-sm" onclick="openWithdrawalModal(${data})" title="{{ __('View Details') }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>{{ __('View') }}</span>
                            </button>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
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

        // Handle Form Submit
        $('#withdrawalForm').on('submit', function(e) {
            e.preventDefault();
            if(!currentWithdrawalId) return;

            const baseUrl = "{{ url('admin/wallets/withdrawals') }}";
            
            $.ajax({
                url: `${baseUrl}/${currentWithdrawalId}/process`,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#withdrawalModal').modal('hide');
                        withdrawalsTable.ajax.reload(null, false);
                        Swal.fire("{{ __('Success') }}", response.message, 'success').then(() => {
                            window.location.reload(); // Refresh to update top stats
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "{{ __('Operation failed') }}";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire("{{ __('Error') }}", errorMessage, 'error');
                }
            });
        });
    });

    function openWithdrawalModal(id) {
        currentWithdrawalId = id;
        const baseUrl = "{{ url('admin/wallets/withdrawals') }}";
        
        // Reset Form
        $('#withdrawalForm')[0].reset();
        
        // Fetch Details
        $.get(`${baseUrl}/${id}/details`, function(response) {
            if(response.data) {
                const data = response.data;
                
                $('#view_user_name').text(data.user ? data.user.name : '---');
                $('#view_requested_amount').text(parseFloat(data.requested_amount).toFixed(2));
                $('#view_status').text(data.status);
                $('#view_date').text(new Date(data.created_at).toLocaleString());
                
                $('#form_status').val(data.status);
                $('#form_approved_amount').val(data.approved_amount || data.requested_amount);
                $('#form_payment_method').val(data.payment_method || '');
                $('#form_admin_notes').val(data.admin_notes || '');
                
                $('#withdrawalModal').modal('show');
            }
        }).fail(function() {
            Swal.fire("{{ __('Error') }}", "{{ __('Could not load withdrawal details.') }}", 'error');
        });
    }
</script>
@endsection
