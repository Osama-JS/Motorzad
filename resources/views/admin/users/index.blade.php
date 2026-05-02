@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('css')

<style>
    .modal-backdrop {
        --bs-backdrop-zindex: 0 !important;
    }
    .modal {
        z-index: 1050 !important;
    }

    /* DataTables wrapper spacing */
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        background-color: var(--bg-input);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 12px;
    }
    
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { 
        color: var(--text-muted); 
        margin-top: 1rem;
    }

    .table td { vertical-align: middle; }
    
    .form-group label { margin-bottom: 0.5rem; display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); }

    /* Custom Checkbox Styling for Grid */
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .checkbox-item {
        background: var(--bg-input);
        border: 1px solid var(--border);
        padding: 0.75rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
    }
    
    .checkbox-item:hover {
        border-color: var(--brand-red);
    }

    /* Loading Overlay */
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(255,255,255,0.1);
        border-top: 5px solid var(--brand-red);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Adjustments for User Table */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        .page-header .btn {
            width: 100%;
        }
        
        /* Modal adjustments */
        .modal-body {
            padding: 1rem;
        }
        
        .row > [class^="col-"] {
            margin-bottom: 0.5rem;
        }
    }

    /* Fix for horizontal scroll in small tables */
    .table-responsive {
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted);">{{ __('Total:') }} <span>{{ $stats['total'] }}</span> {{ __('User') }}</div>
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<div class="page-header">
    <div>
        <h1>{{ __('User Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Users') }}</div>
    </div>
   <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    {{ __('Add New User') }}
</button>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card blue h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">{{ __('Total Users') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="stat-value">{{ $stats['active'] }}</div>
            <div class="stat-label">{{ __('Active Users') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
        <div class="stat-card red h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="stat-value">{{ $stats['inactive'] }}</div>
            <div class="stat-label">{{ __('Inactive Users') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="stat-value">{{ $stats['unverified'] }}</div>
            <div class="stat-label">{{ __('Unverified Users') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Users List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="users-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('Photo') }}</th>
                    <th>{{ __('Information') }}</th>
                    <th>{{ __('Phone Number') }}</th>
                    <th>{{ __('Roles') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Account Verification') }}</th>
                    <th>{{ __('Identity Verification') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will fill this -->
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Country Code') }}</label>
                            <input type="text" name="country_code" class="form-control" placeholder="+966" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('ID / Residence Number') }}</label>
                            <input type="text" name="id_number" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Country') }}</label>
                            <input type="text" name="country" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('City') }}</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Address') }}</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Date of Birth') }}</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Gender') }}</label>
                            <select name="gender" class="form-control">
                                <option value="">{{ __('Not Specified') }}</option>
                                <option value="male">{{ __('Male') }}</option>
                                <option value="female">{{ __('Female') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Roles (Permissions)') }}</label>
                        <div class="checkbox-grid">
                            @foreach($roles as $role)
                            <div class="checkbox-item">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_add_{{ $role->id }}">
                                <label for="role_add_{{ $role->id }}" class="mb-0">
                                    <span class="check-label">{{ $role->name }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                <input type="hidden" id="edit_user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" id="edit_first_name" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" id="edit_last_name" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Email Address') }}</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label>{{ __('Password') }} <small class="text-muted">({{ __('Leave empty if you do not want to change it') }})</small></label>
                            <input type="password" id="edit_password" name="password" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Country Code') }}</label>
                            <input type="text" id="edit_country_code" name="country_code" class="form-control" placeholder="+966" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('ID / Residence Number') }}</label>
                            <input type="text" id="edit_id_number" name="id_number" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('Country') }}</label>
                            <input type="text" id="edit_country" name="country" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">{{ __('City') }}</label>
                            <input type="text" id="edit_city" name="city" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Address') }}</label>
                        <input type="text" id="edit_address" name="address" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Date of Birth') }}</label>
                            <input type="date" id="edit_date_of_birth" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Gender') }}</label>
                            <select id="edit_gender" name="gender" class="form-control">
                                <option value="">{{ __('Not Specified') }}</option>
                                <option value="male">{{ __('Male') }}</option>
                                <option value="female">{{ __('Female') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select id="edit_status" name="status" class="form-control" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Roles (Permissions)') }}</label>
                        <div class="checkbox-grid">
                            @foreach($roles as $role)
                            <div class="checkbox-item">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="edit-role-checkbox" id="role_edit_{{ $role->id }}">
                                <label for="role_edit_{{ $role->id }}" class="mb-0">
                                    <span class="check-label">{{ $role->name }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('User Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewUserBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

<script>
    var usersDataUrl = "{{ route('admin.users.data') }}";
    let updateUserUrl = "{{ route('admin.users.update', ':id') }}";
    let toggleStatusUrlTemplate = "{{ route('admin.users.toggle-status', ':id') }}";
</script>

<script>
    let usersTable;
    
    const WJHTAKAdmin = {
        btnLoading: function(btn, isLoading) {
            if(isLoading) {
                btn.data('original-text', btn.html());
                btn.html('جاري التحميل...').prop('disabled', true);
            } else {
                btn.html(btn.data('original-text')).prop('disabled', false);
            }
        }
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Reset form when opening add modal
        $('#addUserModal').on('show.bs.modal', function() {
            $('#addUserForm')[0].reset();
        });

        // Initialize DataTables
        // إعدادات الـ DataTable مع ترجمة محلية لتجنب خطأ الـ CORS
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: usersDataUrl,
            columns: [
                { data: 'photo' },
                { data: 'info' },
                { data: 'phone' },
                { data: 'roles' },
                { data: 'status' },
                { data: 'verified' },
                { data: 'identity' },
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
            }
        });

        // Handle Add Form Submit
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            $.ajax({
                url: "{{ route('admin.users.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#addUserModal').modal('hide');
                        $('#addUserForm')[0].reset();
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error('حدث خطأ غير متوقع');
                    }
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });

        // Handle Edit Form Submit
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            const id = $('#edit_user_id').val();
            const url = updateUserUrl.replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('حدث خطأ غير متوقع');
                    }
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });
    });

    function viewUser(id) {
        const btn = event.currentTarget ? $(event.currentTarget) : null;
        if(btn) WJHTAKAdmin.btnLoading(btn, true);
        
        let url = "{{ route('admin.users.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                const html = `
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <img src="${response.photo_url}" class="img-fluid rounded shadow mb-3" style="max-width: 150px; width: 100%; border-radius:12px; object-fit: cover;">
                            <div class="mt-2">
                                <span class="badge ${user.status === 'active' ? 'badge-success' : 'badge-danger'}">${user.status === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}'}</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" style="text-align:right;">
                                    <tr><th style="width: 40%;">{{ __('First Name') }}</th><td>${user.first_name || '---'}</td></tr>
                                    <tr><th>{{ __('Last Name') }}</th><td>${user.last_name || '---'}</td></tr>
                                    <tr><th>{{ __('Email Address') }}</th><td>${user.email}</td></tr>
                                    <tr><th>{{ __('Phone Number') }}</th><td dir="ltr" style="text-align:right;">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                    <tr><th>{{ __('ID / Residence Number') }}</th><td>${user.id_number || '---'}</td></tr>
                                    <tr><th>{{ __('City') }}</th><td>${user.city || '---'}</td></tr>
                                    <tr><th>{{ __('Country') }}</th><td>${user.country || '---'}</td></tr>
                                    <tr><th>{{ __('Address') }}</th><td>${user.address || '---'}</td></tr>
                                    <tr><th>{{ __('Gender') }}</th><td>${user.gender === 'male' ? '{{ __("Male") }}' : (user.gender === 'female' ? '{{ __("Female") }}' : '---')}</td></tr>
                                    <tr><th>{{ __('Date of Birth') }}</th><td>${user.date_of_birth || '---'}</td></tr>
                                    <tr><th>{{ __('Date Joined') }}</th><td>${response.created_at}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                $('#viewUserBody').html(html);
                $('#viewUserModal').modal('show');
            }
        }).always(function() {
            if(btn) WJHTAKAdmin.btnLoading(btn, false);
        });
    }

    function editUser(id) {
        const btn = event.currentTarget ? $(event.currentTarget) : null;
        if(btn) WJHTAKAdmin.btnLoading(btn, true);
        
        let url = "{{ route('admin.users.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_email').val(user.email);
                $('#edit_country_code').val(user.country_code);
                $('#edit_phone').val(user.phone);
                $('#edit_country').val(user.country);
                $('#edit_city').val(user.city);
                $('#edit_address').val(user.address);
                $('#edit_date_of_birth').val(user.date_of_birth);
                $('#edit_id_number').val(user.id_number);
                $('#edit_status').val(user.status);
                $('#edit_gender').val(user.gender);
                $('#edit_password').val('');
                
                $('.edit-role-checkbox').each(function() {
                    $(this).prop('checked', response.roles.includes($(this).val()));
                });
                
                $('#editUserModal').modal('show');
            }
        }).always(function() {
            if(btn) WJHTAKAdmin.btnLoading(btn, false);
        });
    }

    function toggleUserStatus(id) {
        const url = toggleStatusUrlTemplate.replace(':id', id);
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'هل تريد تغيير حالة المستخدم؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، قم بالتغيير!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            usersTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function verifyUser(id) {
        Swal.fire({
            title: 'توثيق الحساب؟',
            text: 'هل أنت متأكد من رغبتك بتوثيق هذا الحساب (البريد/الهاتف)؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، وثّق الحساب!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                $.post("{{ route('admin.users.verify', ':id') }}".replace(':id', id), function(response) {
                    if (response.success) {
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                });
            }
        });
    }

    function verifyIdentity(id) {
        Swal.fire({
            title: 'توثيق الهوية؟',
            text: 'هل قمت بالتحقق من الهوية الشخصية لهذا المستخدم؟',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'نعم، وثّق الهوية!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                let url = "{{ route('admin.users.verify-identity', ':id') }}".replace(':id', id);
                $.post(url, function(response) {
                    if (response.success) {
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                });
            }
        });
    }

    function deleteUser(id) {
        let url = "{{ route('admin.users.destroy', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: 'حذف الحساب؟',
            text: 'لا يمكن التراجع عن هذا الإجراء!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            usersTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
