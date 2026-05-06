@extends('layouts.admin')

@section('title', __('Bank Accounts Management'))

@section('css')

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
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">{{ __('Total Accounts') }}</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-university text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">{{ __('Active Accounts') }}</p>
                                <h5 class="font-weight-bolder mb-0 text-success">{{ $stats['active'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">{{ __('Inactive Accounts') }}</p>
                                <h5 class="font-weight-bolder mb-0 text-danger">{{ $stats['inactive'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fas fa-times-circle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Logo') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Bank Name') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('IBAN') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('Beneficiary') }}</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
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
<script>
    // Explicitly define functions on the window object to ensure they are globally accessible
    window.openAddModal = function() {
        $('#bankAccountForm')[0].reset();
        $('#account_id').val('');
        $('#modal-title-text').text("{{ __('Add Bank Account') }}");
        $('#logo-preview').hide();
        $('#bankAccountModal').modal('show');
    };

    window.editAccount = function(id) {
        $.get("{{ url('admin/bank-accounts') }}/" + id + "/edit", function(response) {
            if (response.success) {
                let account = response.account;
                $('#account_id').val(account.id);
                $('#bank_name').val(account.bank_name);
                $('#iban').val(account.iban);
                $('#beneficiary_name').val(account.beneficiary_name);
                $('#is_active').prop('checked', account.is_active);
                
                if (response.logo_url) {
                    $('#current-logo').attr('src', response.logo_url);
                    $('#logo-preview').show();
                } else {
                    $('#logo-preview').hide();
                }
                
                $('#modal-title-text').text("{{ __('Edit Bank Account') }}");
                $('#bankAccountModal').modal('show');
            }
        });
    };

    window.toggleAccountStatus = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("You want to change the account status.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, toggle it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ url('admin/bank-accounts') }}/" + id + "/toggle-active", {
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    if (response.success) {
                        if (typeof table !== 'undefined' && table !== null) {
                            table.ajax.reload(null, false);
                        }
                        Swal.fire('Updated!', response.message, 'success');
                    }
                });
            }
        });
    };

    window.deleteAccount = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/bank-accounts') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof table !== 'undefined' && table !== null) {
                                table.ajax.reload(null, false);
                            }
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    }
                });
            }
        });
    };

    let table;
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#bankAccountsTable')) {
            $('#bankAccountsTable').DataTable().destroy();
        }

        table = $('#bankAccountsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.bank-accounts.data') }}",
                error: function(xhr, error, thrown) {
                    console.error('DataTables Error:', error, thrown);
                }
            },
            columns: [
                { data: 'logo', className: 'text-center' },
                { data: 'bank_name' },
                { data: 'iban' },
                { data: 'beneficiary_name' },
                { data: 'status', className: 'text-center' },
                { data: 'actions', className: 'text-center' }
            ],
            language: {
                "sProcessing": "{{ __('Processing...') }}",
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

        $('#bankAccountForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let id = $('#account_id').val();
            let url = id ? "{{ url('admin/bank-accounts') }}/" + id : "{{ route('admin.bank-accounts.store') }}";
            
            if (id) {
                formData.append('_method', 'PUT');
            }

            let $btn = $('#saveBtn');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Processing...") }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#bankAccountModal').modal('hide');
                        if (table) table.ajax.reload(null, false);
                        Swal.fire('Success', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        Object.keys(errors).forEach(key => {
                            errorMsg += errors[key][0] + '<br>';
                        });
                        Swal.fire('Validation Error', errorMsg, 'error');
                    } else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> {{ __("Save Changes") }}');
                }
            });
        });
    });
</script>
@endsection
