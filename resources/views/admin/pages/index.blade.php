@extends('layouts.admin')

@section('title', __('Pages Management'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    /* Summernote Dark Mode Fix */
    [data-theme="dark"] .note-editor {
        background-color: var(--bg-card);
        color: var(--text-color);
        border-color: var(--border);
    }
    [data-theme="dark"] .note-toolbar {
        background-color: var(--bg-card);
        border-bottom-color: var(--border);
    }
    [data-theme="dark"] .note-editable {
        background-color: var(--bg-input);
        color: var(--text-color);
    }
    [data-theme="dark"] .note-btn {
        background-color: var(--bg-card);
        color: var(--text-color);
        border-color: var(--border);
    }
    [data-theme="dark"] .note-btn:hover, [data-theme="dark"] .note-btn.active {
        background-color: var(--primary-color);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<div class="page-header">
    <div>
        <h1>{{ __('Pages Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Pages') }}</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPageModal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Page') }}
    </button>
</div>

<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card blue h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['total'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Total Pages') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card green h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['active'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Active Pages') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card red h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['inactive'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Inactive Pages') }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="stat-card gold h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3zM3 9h18M9 21V9"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['footer'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Footer Pages') }}</div>
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
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search Pages...') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter_status" class="form-select select2-init" data-dropdown-parent="body">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-5 text-end">
                <button type="button" class="btn btn-secondary" onclick="fetchPages(1)">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchPages(1)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>

<div id="table-view-container" class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0 w-100" id="pages-custom-table">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0">{{ __('Title (Arabic)') }}</th>
                    <th class="border-bottom-0">{{ __('Title (English)') }}</th>
                    <th class="border-bottom-0">{{ __('Slug') }}</th>
                    <th class="border-bottom-0">{{ __('Status') }}</th>
                    <th class="border-bottom-0">{{ __('Show in Footer') }}</th>
                    <th class="border-bottom-0 text-center" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-pages-tbody">
                <!-- Data injected via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body bg-white d-flex justify-content-between align-items-center py-3" id="custom-pagination">
        <!-- Pagination controls will be injected here -->
    </div>
</div>

@endsection

@section('modals')
<!-- Add Page Modal -->
<div class="modal fade" id="addPageModal" tabindex="-1" aria-labelledby="addPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); background: var(--bg-card);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="addPageModalLabel">{{ __('Add New Page') }}</h5>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-4 pt-2">
                <form id="addPageForm">
                    <div class="row g-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Title (Arabic)') }} <span class="text-danger">*</span></span>
                                <x-translate-button from="#title_ar" to="#title_en" />
                            </label>
                            <input type="text" id="title_ar" name="title_ar" class="form-control" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" id="title_en" name="title_en" class="form-control" required dir="ltr" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>
                        
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ __('Slug') }} <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" required dir="ltr" placeholder="{{ __('Example: about-us, privacy-policy') }}" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Content (Arabic)') }} <span class="text-danger">*</span></span>
                                <x-translate-button from="#content_ar" to="#content_en" />
                            </label>
                            <textarea id="content_ar" name="content_ar" class="form-control" rows="6" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ __('Content (English)') }} <span class="text-danger">*</span></label>
                            <textarea id="content_en" name="content_en" class="form-control" rows="6" required dir="ltr" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                        </div>

                        <div class="col-md-6 form-group mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label ms-2" for="is_active">{{ __('Active Page') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_in_footer" id="show_in_footer" value="1">
                                <label class="form-check-label ms-2" for="show_in_footer">{{ __('Show in Footer') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Page Modal -->
<div class="modal fade" id="editPageModal" tabindex="-1" aria-labelledby="editPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); background: var(--bg-card);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="editPageModalLabel">{{ __('Edit Page') }}</h5>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width:32px; height:32px; border-radius:50%; padding:0; background:transparent;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-4 pt-2">
                <form id="editPageForm">
                    <input type="hidden" id="edit_page_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Title (Arabic)') }} <span class="text-danger">*</span></span>
                                <x-translate-button from="#edit_title_ar" to="#edit_title_en" />
                            </label>
                            <input type="text" id="edit_title_ar" name="title_ar" class="form-control" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_title_en" name="title_en" class="form-control" required dir="ltr" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>
                        
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ __('Slug') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_slug" name="slug" class="form-control" required dir="ltr" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Content (Arabic)') }} <span class="text-danger">*</span></span>
                                <x-translate-button from="#edit_content_ar" to="#edit_content_en" />
                            </label>
                            <textarea id="edit_content_ar" name="content_ar" class="form-control" rows="6" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ __('Content (English)') }} <span class="text-danger">*</span></label>
                            <textarea id="edit_content_en" name="content_en" class="form-control" rows="6" required dir="ltr" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);"></textarea>
                        </div>

                        <div class="col-md-6 form-group mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label ms-2" for="edit_is_active">{{ __('Active Page') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_in_footer" id="edit_show_in_footer" value="1">
                                <label class="form-check-label ms-2" for="edit_show_in_footer">{{ __('Show in Footer') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    window.PageConfig = {
        csrf: '{{ csrf_token() }}',
        urls: {
            data: "{{ route('admin.pages.data') }}",
            update: "{{ route('admin.pages.update', ':id') }}",
            show: "{{ route('admin.pages.show', ':id') }}",
            store: "{{ route('admin.pages.store') }}",
            destroy: "{{ route('admin.pages.destroy', ':id') }}"
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
            deletePage: "{{ __('Delete Page?') }}",
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

<script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
