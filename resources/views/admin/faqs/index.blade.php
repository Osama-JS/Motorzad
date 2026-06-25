@extends('layouts.admin')

@section('title', __('FAQs Management'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .faq-answer-collapse {
        font-size: 0.875rem;
        color: var(--text-muted);
        background: var(--bg-body);
        padding: 0.75rem;
        border-radius: var(--radius-sm);
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<div class="page-header">
    <div>
        <h1>{{ __('FAQs Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('FAQs') }}</div>
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New FAQ') }}
    </button>
</div>

<div class="row mb-4 g-3">
    <!-- Total FAQs -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card blue h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['total'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Total FAQs') }}</div>
            </div>
        </div>
    </div>
    <!-- Active FAQs -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card green h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['active'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Active FAQs') }}</div>
            </div>
        </div>
    </div>
    <!-- Inactive FAQs -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card red h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['inactive'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Inactive FAQs') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search FAQs...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-secondary w-100" onclick="fetchFaqs(1)">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchFaqs(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_id" value="0" checked>
                    <label class="form-check-label" for="col_id">#</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_question" value="1" checked disabled>
                    <label class="form-check-label" for="col_question">{{ __('Question') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="2" checked>
                    <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="3" checked disabled>
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

<!-- Table View Container -->
<div id="table-view-container" class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0 w-100" id="faqs-custom-table">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0 col-toggle-0">#</th>
                    <th class="border-bottom-0 col-toggle-1">{{ __('Question') }}</th>
                    <th class="border-bottom-0 col-toggle-2" style="width: 150px;">{{ __('Status') }}</th>
                    <th class="border-bottom-0 text-center col-toggle-3" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-faqs-tbody">
                <!-- Data injected via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Grid View Container -->
<div id="grid-view-container" class="row g-3 d-none mb-4">
    <div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> {{ __('Loading...') }}</div>
</div>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>

@endsection

@section('modals')
<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); background: var(--bg-card); color: var(--text-color);">
            <form id="faqForm">
                @csrf
                <input type="hidden" name="_method" id="faqMethod" value="POST">
                <input type="hidden" id="faqId">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4 d-flex align-items-center gap-2" id="faqModalLabel">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-red)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span id="modalTitleText">{{ __('Add New FAQ') }}</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body pt-4">
                    
                    <div class="row g-4">
                        <!-- Arabic Section -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-none" style="background: var(--bg-body); border-radius: var(--radius-lg);">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color: var(--brand-blue-light);">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">AR</span>
                                        {{ __('Arabic Content') }}
                                    </h6>
                                    
                                    <div class="form-group mb-4">
                                        <label for="question_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                            <span>{{ __('Question') }} <span class="text-danger">*</span></span>
                                            <x-translate-button from="#question_ar" to="#question_en" />
                                        </label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg px-3 py-2" id="question_ar" name="question_ar" placeholder="أدخل السؤال بالعربية..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="answer_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                            <span>{{ __('Answer') }} <span class="text-danger">*</span></span>
                                            <x-translate-button from="#answer_ar" to="#answer_en" />
                                        </label>
                                        <textarea class="form-control px-3 py-2" id="answer_ar" name="answer_ar" rows="6" placeholder="أدخل الإجابة بالعربية..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- English Section -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-none" style="background: var(--bg-body); border-radius: var(--radius-lg);">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2 text-primary">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">EN</span>
                                        {{ __('English Content') }}
                                    </h6>
                                    
                                    <div class="form-group mb-4">
                                        <label for="question_en" class="form-label fw-bold">{{ __('Question') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg px-3 py-2" id="question_en" name="question_en" placeholder="Enter question in English..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="answer_en" class="form-label fw-bold">{{ __('Answer') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control px-3 py-2" id="answer_en" name="answer_en" rows="6" placeholder="Enter answer in English..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top" style="border-color: var(--border-light) !important;">
                        <label class="checkbox-item w-100 mb-0 d-flex align-items-center p-3 rounded" style="background: var(--bg-body);">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <div class="ms-3">
                                <div class="check-label fw-bold fs-6">{{ __('Active Status') }}</div>
                                <div class="check-sub text-muted mt-1">{{ __('Make this FAQ visible to users immediately.') }}</div>
                            </div>
                        </label>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-ghost px-4 py-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" id="saveBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Save Data') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.FaqConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.faqs.data') }}",
            store: "{{ route('admin.faqs.store') }}",
            update: "{{ route('admin.faqs.update', ':id') }}",
            destroy: "{{ route('admin.faqs.destroy', ':id') }}",
            toggleActive: "{{ route('admin.faqs.toggle-active', ':id') }}"
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
            deleteFaq: "{{ __('Delete FAQ?') }}",
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

<script src="{{ asset('js/admin/faqs.js') }}"></script>
@endsection
