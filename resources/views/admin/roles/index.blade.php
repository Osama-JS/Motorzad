@extends('layouts.admin')

@section('title', __('Roles Management'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<x-admin-header :title="__('Roles Management')" :breadcrumb="__('Roles')">
    @can('manage-roles')
    <button class="btn-ultra" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Role') }}
    </button>
    @endcan
</x-admin-header>

<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card gold h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $rolesCount }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('All Roles') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card blue h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $totalUsers }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Total Users') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search Roles...') }}">
                </div>
            </div>
            <div class="col-md-8 text-end">
                <button type="button" class="btn btn-secondary" onclick="fetchRoles(1)">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchRoles(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_name" value="0" checked disabled>
                    <label class="form-check-label" for="col_name">{{ __('Role Name') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_perms" value="1" checked>
                    <label class="form-check-label" for="col_perms">{{ __('Permissions') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_users" value="2" checked>
                    <label class="form-check-label" for="col_users">{{ __('Number of Users') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="3" checked disabled>
                    <label class="form-check-label" for="col_actions">{{ __('Actions') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-secondary btn-sm active" id="btn-view-table" onclick="toggleView('table')" title="{{ __('Table View') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-view-grid" onclick="toggleView('grid')" title="{{ __('Grid View') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        </button>
    </div>
</div>

<div id="table-view-container">
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="roles-table">
                <thead class="table-light">
                    <tr>
                        <th class="col-toggle-0">{{ __('Role Name') }}</th>
                        <th class="col-toggle-1">{{ __('Permissions') }}</th>
                        <th class="col-toggle-2">{{ __('Number of Users') }}</th>
                        <th class="text-center col-toggle-3" style="width: 80px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="custom-roles-tbody">
                    <!-- Populated via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="grid-view-container" class="row g-3 d-none">
    <!-- Populated via AJAX -->
</div>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center py-3 gap-3" id="custom-pagination">
        <!-- Pagination controls injected via AJAX -->
    </div>
</div>
@endsection

@section('modals')
@php
    $isEn = app()->getLocale() === 'en';
@endphp
<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); background: var(--bg-card);">
            <div class="modal-header border-bottom pb-3">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="addRoleModalLabel">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ __('Add New Role') }}
                    </h5>
                    <p class="text-muted small mb-0">{{ $isEn ? 'Create a new administrative role and define its assigned permissions based on job responsibilities' : 'أنشئ دوراً إدارياً جديداً وحدد باقة الصلاحيات بدقة حسب المهام الوظيفية' }}</p>
                </div>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="addRoleForm">
                    <div class="row g-3 mb-4">
                        <div class="col-md-12 form-group">
                            <label class="form-label fw-bold" style="color: var(--text-color);">
                                {{ __('Role Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="{{ $isEn ? 'e.g., Field Operations Manager, Finance Officer, Support Agent...' : 'مثال: مدير المزادات الميداني، مسؤول المالية، دعم العملاء...' }}" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>
                    </div>
                    
                    <div class="form-group mb-2">
                        <label class="form-label fw-bold mb-2" style="color: var(--text-color);">
                            {{ $isEn ? 'Role Permissions (Categorized & Detailed)' : 'صلاحيات الدور (مبوبة ومترجمة بالتفصيل)' }} <span class="text-danger">*</span>
                        </label>
                        @include('admin.roles.partials.permissions-selector', ['prefix' => 'add', 'isEdit' => false])
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="button" class="btn btn-light me-2 px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); background: var(--bg-card);">
            <div class="modal-header border-bottom pb-3">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="editRoleModalLabel">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        {{ __('Edit Role') }}
                    </h5>
                    <p class="text-muted small mb-0">{{ $isEn ? 'Update role title or reconfigure its assigned permissions package' : 'تعديل مسمى الدور أو إعادة تشكيل باقة الصلاحيات المرتبطة به' }}</p>
                </div>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="editRoleForm">
                    <input type="hidden" id="edit_role_id" name="id">
                    <div class="row g-3 mb-4">
                        <div class="col-md-12 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0 fw-bold" style="color: var(--text-color);">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                                <span class="badge bg-warning text-dark d-none" id="edit_system_role_badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    {{ $isEn ? 'System Core Role (Protected)' : 'دور نظام أساسي (محمي)' }}
                                </span>
                            </div>
                            <input type="text" id="edit_name" name="name" class="form-control form-control-lg" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                            <small class="text-muted d-none mt-1" id="edit_system_role_notice">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning me-1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $isEn ? 'This role name cannot be modified as it is central to platform core logic, but its permissions can be customized.' : 'لا يمكن تغيير اسم هذا الدور لأنه محوري في منطق عمل المنصة، ولكن يمكنك تخصيص صلاحياته بحرية.' }}
                            </small>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label fw-bold mb-2" style="color: var(--text-color);">
                            {{ $isEn ? 'Role Permissions (Categorized & Detailed)' : 'صلاحيات الدور (مبوبة ومترجمة بالتفصيل)' }} <span class="text-danger">*</span>
                        </label>
                        @include('admin.roles.partials.permissions-selector', ['prefix' => 'edit', 'isEdit' => true])
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="button" class="btn btn-light me-2 px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.RoleConfig = {
        csrf: '{{ csrf_token() }}',
        canManage: {{ (auth()->user()->hasRole('admin') || auth()->user()->can('manage-roles')) ? 'true' : 'false' }},
        urls: {
            data: "{{ route('admin.roles.data') }}",
            update: "{{ route('admin.roles.update', ':id') }}",
            show: "{{ route('admin.roles.show', ':id') }}",
            store: "{{ route('admin.roles.store') }}",
            destroy: "{{ route('admin.roles.destroy', ':id') }}"
        },
        trans: {
            loading: "{{ __('Loading...') }}",
            errorLoading: "{{ __('Error loading data.') }}",
            noRecords: "{{ __('No matching records found') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            unexpectedError: "{{ __('Unexpected error occurred.') }}",
            deleteRole: "{{ __('Delete Role?') }}",
            deleteDesc: "{{ __('This action cannot be undone!') }}",
            yesDelete: "{{ __('Yes, delete it!') }}",
            cancel: "{{ __('Cancel') }}"
        }
    };
    
    if (typeof window.WJHTAKAdmin === 'undefined') {
        window.WJHTAKAdmin = {
            btnLoading: function(btn, isLoading) {
                if (!btn || !btn.length) return;
                if (isLoading) {
                    if(!btn.data('original-text')) btn.data('original-text', btn.html());
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("Wait...") }}');
                } else {
                    btn.prop('disabled', false).html(btn.data('original-text'));
                }
            }
        };
    }
</script>

<script src="{{ asset('js/admin/roles.js') }}"></script>
@endsection
