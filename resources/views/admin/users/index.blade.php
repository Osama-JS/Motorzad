@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
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

<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('Total Users') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">{{ __('Approved Users') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card gold h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">{{ __('Pending Verification') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">{{ __('Rejected') }}</div>
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
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by name, email, or phone...') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter_role" class="form-select select2-init">
                    <option value="all">{{ __('All Roles') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter_status" class="form-select select2-init">
                    <option value="all">{{ __('All Statuses') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-secondary w-100" onclick="fetchUsers(1)">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="view-toolbar">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="text-muted small me-2">{{ __('Show:') }}</span>
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchUsers(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_photo" value="0" checked>
                    <label class="form-check-label" for="col_photo">{{ __('Photo') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_info" value="1" checked disabled>
                    <label class="form-check-label" for="col_info">{{ __('Information') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_phone" value="2" checked>
                    <label class="form-check-label" for="col_phone">{{ __('Phone Number') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_roles" value="3" checked>
                    <label class="form-check-label" for="col_roles">{{ __('Roles') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_kyc" value="4" checked>
                    <label class="form-check-label" for="col_kyc">KYC Level</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="5" checked>
                    <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_verified" value="6" checked>
                    <label class="form-check-label" for="col_verified">{{ __('Verification') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="7" checked disabled>
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

<div id="table-view-container" class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0 w-100" id="users-custom-table">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0 col-toggle-0">{{ __('Photo') }}</th>
                    <th class="border-bottom-0 col-toggle-1">{{ __('Information') }}</th>
                    <th class="border-bottom-0 col-toggle-2">{{ __('Phone Number') }}</th>
                    <th class="border-bottom-0 col-toggle-3">{{ __('Roles') }}</th>
                    <th class="border-bottom-0 col-toggle-4">KYC Level</th>
                    <th class="border-bottom-0 col-toggle-5">{{ __('Status') }}</th>
                    <th class="border-bottom-0 col-toggle-6">{{ __('Verification') }}</th>
                    <th class="border-bottom-0 text-center col-toggle-7">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-users-tbody">
                <tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View Container -->
<div id="grid-view-container" class="row g-3 d-none">
    <div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</div>
</div>

<!-- Pagination Container (Shared) -->
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>
@endsection

@section('modals')
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom pb-3" style="background: var(--bg-card);">
                <div>
                    <h5 class="modal-title fw-bold mb-1"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="color: var(--primary);"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>{{ __('Add New User') }}</h5>
                    <p class="mb-0 text-muted small">{{ __('Please fill out the following steps to complete the user registration process.') }}</p>
                </div>
                <button type="button" class="bg-transparent border-0 p-0 text-muted" style="outline: none; box-shadow: none;" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body p-4" style="background: var(--bg-input);">
                    <!-- Wizard Header -->
                    <div class="wizard-steps" id="wizardSteps">
                        <div class="wizard-step-container active" data-step="1" onclick="jumpToWizardStep(1)">
                            <div class="wizard-step-btn mx-auto">1</div>
                            <span class="wizard-step-label">{{ __('Personal Info') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="2" onclick="jumpToWizardStep(2)">
                            <div class="wizard-step-btn mx-auto">2</div>
                            <span class="wizard-step-label">{{ __('Contact') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="3" onclick="jumpToWizardStep(3)">
                            <div class="wizard-step-btn mx-auto">3</div>
                            <span class="wizard-step-label">{{ __('Address') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="4" onclick="jumpToWizardStep(4)">
                            <div class="wizard-step-btn mx-auto">4</div>
                            <span class="wizard-step-label">{{ __('Permissions') }}</span>
                        </div>
                    </div>

                    <!-- Step 1: Personal Info -->
                    <div class="wizard-step-content active" id="step-1">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header border-bottom-0 pt-3 pb-0" style="background: var(--bg-card);">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>{{ __('Personal Information') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name" class="form-control form-control-lg bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name" class="form-control form-control-lg bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Date of Birth') }}</label>
                                    <input type="text" name="date_of_birth" class="form-control form-control-lg bg-light custom-datepicker-dob" placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Gender') }}</label>
                                    <select name="gender" class="form-select form-control-lg bg-light select2-init" data-dropdown-parent="#addUserModal">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">{{ __('ID / Residence Number') }}</label>
                                    <input type="text" name="id_number" class="form-control form-control-lg bg-light text-start" dir="ltr">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Contact & Account -->
                    <div class="wizard-step-content" id="step-2">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>{{ __('Contact & Account') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Email Address') }}</label>
                                    <input type="email" name="email" class="form-control form-control-lg bg-light text-start" required dir="ltr">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Password') }}</label>
                                    <input type="password" name="password" class="form-control form-control-lg bg-light text-start" required dir="ltr">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Country Code') }}</label>
                                    <select name="country_code" class="form-select form-control-lg bg-light text-start select2-init" dir="ltr" data-dropdown-parent="#addUserModal">
                                        @include('partials.country-codes', ['selected' => '+966'])
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Phone Number') }}</label>
                                    <input type="text" name="phone" class="form-control form-control-lg bg-light text-start" dir="ltr">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Address Info -->
                    <div class="wizard-step-content" id="step-3">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>{{ __('Address Details') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Country') }}</label>
                                    <input type="text" name="country" class="form-control form-control-lg bg-light">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('City') }}</label>
                                    <input type="text" name="city" class="form-control form-control-lg bg-light">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Address') }}</label>
                                    <input type="text" name="address" class="form-control form-control-lg bg-light">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: System Roles & Status -->
                    <div class="wizard-step-content" id="step-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ __('System Status & Permissions') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">KYC Level</label>
                                    <select name="kyc_level" class="form-select form-control-lg bg-light select2-init" required data-dropdown-parent="#addUserModal">
                                        <option value="0">Level 0</option>
                                        <option value="1">Level 1</option>
                                        <option value="2">Level 2</option>
                                        <option value="3">Level 3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Account Status') }}</label>
                                    <select name="status" class="form-select form-control-lg bg-light select2-init" required data-dropdown-parent="#addUserModal">
                                        <option value="pending">{{ __('Pending Verification') }}</option>
                                        <option value="approved">{{ __('Approved') }}</option>
                                        <option value="rejected">{{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <label class="form-label fw-semibold text-muted small">{{ __('Roles (Permissions)') }}</label>
                            <div class="checkbox-grid">
                                @foreach($roles as $role)
                                <div class="checkbox-item bg-light border-0">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_add_{{ $role->id }}" class="form-check-input mt-0 me-2">
                                    <label for="role_add_{{ $role->id }}" class="mb-0 fw-medium text-dark">
                                        <span class="check-label">{{ $role->name }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    </div>
                
                    <!-- Wizard Navigation Buttons (Inside Body to avoid theme footer hiding) -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top" id="wizardButtonsContainer">
                        <button type="button" class="btn btn-secondary px-4" id="wizardPrevBtn" onclick="nextPrev(-1)" disabled style="display: inline-block;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            {{ __('Previous') }}
                        </button>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary px-4" id="wizardNextBtn" onclick="nextPrev(1)" style="display: inline-block;">
                                {{ __('Next') }}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button type="submit" class="btn btn-success px-4" id="wizardSubmitBtn" style="display: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                {{ __('Save User') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom pb-3" style="background: var(--bg-card);">
                <div>
                    <h5 class="modal-title fw-bold mb-1"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="color: var(--primary);"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>{{ __('Edit User') }}</h5>
                    <p class="mb-0 text-muted small">{{ __('Please fill out the following steps to update the user data.') }}</p>
                </div>
                <button type="button" class="bg-transparent border-0 p-0 text-muted" style="outline: none; box-shadow: none;" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form id="editUserForm">
                @csrf
                <input type="hidden" id="edit_user_id">
                <div class="modal-body p-4" style="background: var(--bg-input);">
                    <!-- Wizard Header -->
                    <div class="wizard-steps" id="editWizardSteps">
                        <div class="wizard-step-container active" data-step="1" onclick="jumpToEditWizardStep(1)">
                            <div class="wizard-step-btn mx-auto">1</div>
                            <span class="wizard-step-label">{{ __('Personal Info') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="2" onclick="jumpToEditWizardStep(2)">
                            <div class="wizard-step-btn mx-auto">2</div>
                            <span class="wizard-step-label">{{ __('Contact') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="3" onclick="jumpToEditWizardStep(3)">
                            <div class="wizard-step-btn mx-auto">3</div>
                            <span class="wizard-step-label">{{ __('Address') }}</span>
                        </div>
                        <div class="wizard-step-container" data-step="4" onclick="jumpToEditWizardStep(4)">
                            <div class="wizard-step-btn mx-auto">4</div>
                            <span class="wizard-step-label">{{ __('Permissions') }}</span>
                        </div>
                    </div>

                    <!-- Step 1: Personal Info -->
                    <div class="wizard-step-content active" id="edit-step-1">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header border-bottom-0 pt-3 pb-0" style="background: var(--bg-card);">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>{{ __('Personal Information') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('First Name') }}</label>
                                    <input type="text" id="edit_first_name" name="first_name" class="form-control form-control-lg bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Last Name') }}</label>
                                    <input type="text" id="edit_last_name" name="last_name" class="form-control form-control-lg bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Date of Birth') }}</label>
                                    <input type="text" id="edit_date_of_birth" name="date_of_birth" class="form-control form-control-lg bg-light custom-datepicker-dob" placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Gender') }}</label>
                                    <select id="edit_gender" name="gender" class="form-select form-control-lg bg-light select2-init" data-dropdown-parent="#editUserModal">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">{{ __('ID / Residence Number') }}</label>
                                    <input type="text" id="edit_id_number" name="id_number" class="form-control form-control-lg bg-light text-start" dir="ltr">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Contact & Account -->
                    <div class="wizard-step-content" id="edit-step-2">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>{{ __('Contact & Account') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Email Address') }}</label>
                                    <input type="email" id="edit_email" name="email" class="form-control form-control-lg bg-light text-start" required dir="ltr">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Password') }} <span class="text-muted" style="font-size: 11px;">(اتركه فارغاً إن لم ترغب بتغييره)</span></label>
                                    <input type="password" id="edit_password" name="password" class="form-control form-control-lg bg-light text-start" dir="ltr">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Country Code') }}</label>
                                    <select id="edit_country_code" name="country_code" class="form-select form-control-lg bg-light text-start select2-init" dir="ltr" data-dropdown-parent="#editUserModal">
                                        @include('partials.country-codes', ['selected' => ''])
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Phone Number') }}</label>
                                    <input type="text" id="edit_phone" name="phone" class="form-control form-control-lg bg-light text-start" dir="ltr">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Address Info -->
                    <div class="wizard-step-content" id="edit-step-3">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>{{ __('Address Details') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Country') }}</label>
                                    <input type="text" id="edit_country" name="country" class="form-control form-control-lg bg-light">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('City') }}</label>
                                    <input type="text" id="edit_city" name="city" class="form-control form-control-lg bg-light">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Address') }}</label>
                                    <input type="text" id="edit_address" name="address" class="form-control form-control-lg bg-light">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: System Roles & Status -->
                    <div class="wizard-step-content" id="edit-step-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ __('System Status & Permissions') }}</h6>
                            </div>
                            <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">KYC Level</label>
                                    <select id="edit_kyc_level" name="kyc_level" class="form-select form-control-lg bg-light select2-init" required data-dropdown-parent="#editUserModal">
                                        <option value="0">Level 0</option>
                                        <option value="1">Level 1</option>
                                        <option value="2">Level 2</option>
                                        <option value="3">Level 3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">{{ __('Account Status') }}</label>
                                    <select id="edit_status" name="status" class="form-select form-control-lg bg-light select2-init" required data-dropdown-parent="#editUserModal">
                                        <option value="pending">{{ __('Pending Verification') }}</option>
                                        <option value="approved">{{ __('Approved') }}</option>
                                        <option value="rejected">{{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <label class="form-label fw-semibold text-muted small">{{ __('Roles (Permissions)') }}</label>
                            <div class="checkbox-grid">
                                @foreach($roles as $role)
                                <div class="checkbox-item bg-light border-0">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_edit_{{ $role->id }}" class="form-check-input mt-0 me-2 edit-role-checkbox">
                                    <label for="role_edit_{{ $role->id }}" class="mb-0 fw-medium text-dark">
                                        {{ $role->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Wizard Navigation Buttons -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top px-4 pb-4" id="editWizardButtonsContainer">
                    <button type="button" class="btn btn-secondary px-4" id="editWizardPrevBtn" onclick="nextEditPrev(-1)" disabled style="display: inline-block;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        {{ __('Previous') }}
                    </button>
                    <div>
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-primary px-4" id="editWizardNextBtn" onclick="nextEditPrev(1)" style="display: inline-block;">
                            {{ __('Next') }}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-1"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="editWizardSubmitBtn" style="display: none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            {{ __('Update User') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background: var(--bg-card, #f8f9fa);">
            <div class="modal-header border-0 pb-0" style="background: transparent;">
                <h5 class="modal-title fw-bold text-dark fs-4 d-flex align-items-center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-primary"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    {{ __('User Profile') }}
                </h5>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-4" id="viewUserBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
<script>
    window.UserConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.users.data') }}",
            update: "{{ route('admin.users.update', ':id') }}",
            updateStatus: "{{ route('admin.users.update-status', ':id') }}",
            show: "{{ route('admin.users.show', ':id') }}",
            store: "{{ route('admin.users.store') }}",
            destroy: "{{ route('admin.users.destroy', ':id') }}",
            verify: "{{ route('admin.users.verify', ':id') }}",
            verifyIdentity: "{{ route('admin.users.verify-identity', ':id') }}"
        },
        trans: {
            loading: "{{ __('Loading...') }}",
            errorLoading: "{{ __('Error loading data.') }}",
            noRecords: "{{ __('No matching records found') }}",
            phone: "{{ __('Phone') }}",
            roles: "{{ __('Roles') }}",
            status: "{{ __('Status') }}",
            verification: "{{ __('Verification') }}",
            showing: "{{ __('Showing') }}",
            to: "{{ __('to') }}",
            of: "{{ __('of') }}",
            entries: "{{ __('entries') }}",
            fillRequired: "{{ __('Please fill all required fields correctly.') }}",
            unexpectedError: "{{ __('Unexpected error occurred.') }}",
            walletBalance: "{{ __('Wallet Balance') }}",
            createdAuctions: "{{ __('Created Auctions') }}",
            wonAuctions: "{{ __('Won Auctions') }}",
            totalBids: "{{ __('Total Bids') }}",
            approved: "{{ __('Approved') }}",
            rejected: "{{ __('Rejected') }}",
            pendingVerify: "{{ __('Pending Verification') }}",
            noRoles: "{{ __('No Roles') }}",
            personalInfo: "{{ __('Personal Information') }}",
            phoneNumber: "{{ __('Phone Number') }}",
            idNumber: "{{ __('ID / Residence Number') }}",
            male: "{{ __('Male') }}",
            female: "{{ __('Female') }}",
            dob: "{{ __('Date of Birth') }}",
            country: "{{ __('Country') }}",
            city: "{{ __('City') }}",
            address: "{{ __('Address') }}",
            dateJoined: "{{ __('Date Joined') }}",
            kycDocs: "{{ __('KYC Verification Documents') }}",
            idImage: "{{ __('ID Image') }}",
            selfieImage: "{{ __('Selfie Image') }}",
            nameInReq: "{{ __('Name in Request') }}",
            adminNote: "{{ __('Admin Note') }}",
            noKyc: "{{ __('No KYC verification request attached to this user.') }}",
            approveVerify: "{{ __('Approve Verification?') }}",
            rejectVerify: "{{ __('Reject Verification?') }}",
            approveDesc: "{{ __('The user documents will be approved and their level will be upgraded to Level 3.') }}",
            rejectDesc: "{{ __('The request will be rejected. Please enter the reason for rejection below:') }}",
            writeReject: "{{ __('Write rejection reason here...') }}",
            yesConfirm: "{{ __('Yes, Confirm!') }}",
            cancel: "{{ __('Cancel') }}",
            verifyAccount: "{{ __('Verify Account?') }}",
            verifyAccountDesc: "{{ __('Are you sure you want to verify this account (Email/Phone)?') }}",
            yesVerify: "{{ __('Yes, verify it!') }}",
            verifyIdentityTitle: "{{ __('Verify Identity?') }}",
            verifyIdentityDesc: "{{ __('Have you manually verified the identity documents for this user?') }}",
            yesVerifyIdentity: "{{ __('Yes, verify identity!') }}",
            deleteAccount: "{{ __('Delete Account?') }}",
            deleteDesc: "{{ __('This action cannot be undone!') }}",
            yesDelete: "{{ __('Yes, delete it!') }}"
        }
    };
    
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

    $(document).ready(function() {
        $(".custom-datepicker-dob").flatpickr({
            maxDate: "2005-12-31",
            dateFormat: "Y-m-d"
        });
        $('.custom-datepicker').flatpickr({
            locale: "ar",
            dateFormat: "Y-m-d",
            disableMobile: "true"
        });
    });
</script>

<script src="{{ asset('js/admin/users.js') }}"></script>
@endsection
