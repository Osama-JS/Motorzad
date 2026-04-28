@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('css')

<style>

    .modal-backdrop{
        --bs-backdrop-zindex: 0 !important;
    }

    /* DataTables wrapper spacing */
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }


    
    .mb-3 { margin-bottom: 1rem !important; }

    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        background-color: var(--surface-color);
        color: var(--text-color);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 4px;
    }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: var(--text-muted); }
    table.dataTable.table-striped>tbody>tr.odd>* { box-shadow: inset 0 0 0 9999px rgba(0,0,0,0.02); }
    .table td { vertical-align: middle; }
    
    .form-group label { margin-bottom: 0.5rem; display: block; font-weight: 500; }

    /* Loading Overlay */
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--brand-red);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted);">إجمالي: <span>{{ $stats['total'] }}</span> مستخدم</div>
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<div class="page-header">
    <div>
        <h1>إدارة المستخدمين</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a> / المستخدمين</div>
    </div>
   <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    إضافة مستخدم جديد
</button>
</div>

<div class="card">
    <div class="card-header">
        <h2>قائمة المستخدمين</h2>
    </div>
    <div class="table-responsive">
        <table id="users-table" class="table table-striped w-100" style="text-align: right;">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>المعلومات</th>
                    <th>رقم الهاتف</th>
                    <th>الأدوار</th>
                    <th>الحالة</th>
                    <th>توثيق الحساب</th>
                    <th>توثيق الهوية</th>
                    <th>الإجراءات</th>
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
                <h5 class="modal-title">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">اسم العائلة</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رمز الدولة</label>
                            <input type="text" name="country_code" class="form-control" placeholder="+966" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رقم الهوية / الإقامة</label>
                            <input type="text" name="id_number" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">الدولة</label>
                            <input type="text" name="country" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">المدينة</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">الجنس</label>
                            <select name="gender" class="form-control">
                                <option value="">غير محدد</option>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-control" required>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">الأدوار (الصلاحيات)</label>
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
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ المستخدم</button>
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
                <h5 class="modal-title">تعديل المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                <input type="hidden" id="edit_user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" id="edit_first_name" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">اسم العائلة</label>
                            <input type="text" id="edit_last_name" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label>كلمة المرور <small class="text-muted">(اتركها فارغة إذا لم ترد التغيير)</small></label>
                            <input type="password" id="edit_password" name="password" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رمز الدولة</label>
                            <input type="text" id="edit_country_code" name="country_code" class="form-control" placeholder="+966" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">رقم الهوية / الإقامة</label>
                            <input type="text" id="edit_id_number" name="id_number" class="form-control" dir="ltr" style="text-align:left;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">الدولة</label>
                            <input type="text" id="edit_country" name="country" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                            <label class="form-label">المدينة</label>
                            <input type="text" id="edit_city" name="city" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" id="edit_address" name="address" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" id="edit_date_of_birth" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">الجنس</label>
                            <select id="edit_gender" name="gender" class="form-control">
                                <option value="">غير محدد</option>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 form-group">
                            <label class="form-label">الحالة</label>
                            <select id="edit_status" name="status" class="form-control" required>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">الأدوار (الصلاحيات)</label>
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
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث المستخدم</button>
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
                <h5 class="modal-title">الملف الشخصي للمستخدم</h5>
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
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sSearch": "بحث:",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
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
                        <div class="col-md-4 text-center">
                            <img src="${response.photo_url}" class="img-fluid rounded shadow mb-3" style="max-width: 150px; border-radius:12px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped" style="text-align:right;">
                                <tr><th>الاسم الأول</th><td>${user.first_name || '---'}</td></tr>
                                <tr><th>اسم العائلة</th><td>${user.last_name || '---'}</td></tr>
                                <tr><th>البريد الإلكتروني</th><td>${user.email}</td></tr>
                                <tr><th>رقم الهاتف</th><td dir="ltr" style="text-align:right;">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                <tr><th>رقم الهوية</th><td>${user.id_number || '---'}</td></tr>
                                <tr><th>المدينة</th><td>${user.city || '---'}</td></tr>
                                <tr><th>الدولة</th><td>${user.country || '---'}</td></tr>
                                <tr><th>العنوان</th><td>${user.address || '---'}</td></tr>
                                <tr><th>الجنس</th><td>${user.gender === 'male' ? 'ذكر' : (user.gender === 'female' ? 'أنثى' : '---')}</td></tr>
                                <tr><th>تاريخ الميلاد</th><td>${user.date_of_birth || '---'}</td></tr>
                                <tr><th>تاريخ الانضمام</th><td>${response.created_at}</td></tr>
                            </table>
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
