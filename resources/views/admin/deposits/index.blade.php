@extends('layouts.admin')

@section('title', __('إدارة طلبات الإيداع'))

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
        <div class="stat-card gold h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total_pending'] }}</div>
            <div class="stat-label">{{ __('طلبات معلقة بالانتظار') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_approved'], 2) }}</div>
            <div class="stat-label">{{ __('إجمالي المبالغ المعتمدة') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card blue h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total_requests'] }}</div>
            <div class="stat-label">{{ __('إجمالي طلبات الإيداع') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('قائمة طلبات الإيداع') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="deposits-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('المزايد') }}</th>
                    <th>{{ __('البنك المحوّل إليه') }}</th>
                    <th>{{ __('المبلغ') }}</th>
                    <th>{{ __('حالة الطلب') }}</th>
                    <th>{{ __('تاريخ الطلب') }}</th>
                    <th>{{ __('الإجراءات') }}</th>
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
<script>
    let depositsTable;
    let currentDepositId = null;

    $(document).ready(function() {
        depositsTable = $('#deposits-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.deposits.index') }}",
            columns: [
                { data: 'id' },
                { 
                    data: 'user_name',
                    render: function(data, type, row) {
                        return `<strong>${data}</strong><br><small class="text-muted">${row.user_email}</small>`;
                    }
                },
                { data: 'bank_name' },
                { 
                    data: 'amount',
                    render: function(data) {
                        return `<strong class="text-success">${data} SAR</strong>`;
                    }
                },
                { 
                    data: 'status',
                    render: function(data) {
                        const statusClasses = {
                            'pending': 'status-pending',
                            'approved': 'status-approved',
                            'rejected': 'status-rejected'
                        };
                        const statusLabels = {
                            'pending': '{{ __("Pending") }}',
                            'approved': '{{ __("Approved") }}',
                            'rejected': '{{ __("Rejected") }}'
                        };
                        return `<span class="status-badge ${statusClasses[data] || 'status-pending'}">${statusLabels[data] || data}</span>`;
                    }
                },
                { data: 'created_at' },
                { 
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn btn-sm btn-info d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-white shadow-sm" onclick="openDepositModal(${data})" title="{{ __('معالجة الطلب') }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>{{ __('عرض ومعالجة') }}</span>
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
        $('#depositForm').on('submit', function(e) {
            e.preventDefault();
            if(!currentDepositId) return;

            const baseUrl = "{{ url('admin/deposits') }}";
            
            $.ajax({
                url: `${baseUrl}/${currentDepositId}/process`,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#depositModal').modal('hide');
                        depositsTable.ajax.reload(null, false);
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

    function openDepositModal(id) {
        currentDepositId = id;
        const baseUrl = "{{ url('admin/deposits') }}";
        
        // Reset Form
        $('#depositForm')[0].reset();
        
        // Fetch Details
        $.get(`${baseUrl}/${id}`, function(response) {
            if(response.data) {
                const data = response.data;
                
                $('#view_user_name').text(data.user ? data.user.full_name : '---');
                $('#view_user_email').text(data.user ? data.user.email : '');
                $('#view_requested_amount').text(parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#view_bank_name').text(data.bank_account ? data.bank_account.bank_name : '---');
                $('#view_date').text(new Date(data.created_at).toLocaleString());
                
                $('#form_status').val(data.status);
                $('#form_admin_note').val(data.admin_note || '');
                
                // Receipt path handling
                if (data.receipt_path) {
                    const fullUrl = `{{ asset('storage') }}/${data.receipt_path}`;
                    $('#view_receipt_img').attr('src', fullUrl);
                    $('#receipt_link').attr('href', fullUrl);
                    $('#receipt_container').show();
                    $('#no_receipt_msg').hide();
                } else {
                    $('#receipt_container').hide();
                    $('#no_receipt_msg').show();
                }
                
                // Disable form if already processed
                if(data.status !== 'pending') {
                    $('#depositForm button[type="submit"]').prop('disabled', true).text('{{ __("تم معالجة الطلب مسبقاً") }}');
                    $('#form_status').prop('disabled', true);
                    $('#form_admin_note').prop('disabled', true);
                } else {
                    $('#depositForm button[type="submit"]').prop('disabled', false).text('{{ __("تحديث حالة الإيداع") }}');
                    $('#form_status').prop('disabled', false);
                    $('#form_admin_note').prop('disabled', false);
                }

                $('#depositModal').modal('show');
            }
        }).fail(function() {
            Swal.fire("{{ __('Error') }}", "{{ __('Could not load deposit details.') }}", 'error');
        });
    }
</script>
@endsection
