@extends('layouts.admin')

@section('title', __('Contact Messages'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
@endsection

@section('content')
<div id="page-loader"><div class="spinner"></div></div>
<div class="page-header">
    <div>
        <h1>{{ __('Contact Messages') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Contact Messages') }}</div>
    </div>
</div>

<div class="row mb-4 g-3">
    <!-- Total -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card blue h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['total'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Total Messages') }}</div>
            </div>
        </div>
    </div>
    <!-- New -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card red h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['new'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('New Messages') }}</div>
            </div>
        </div>
    </div>
    <!-- Read -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card green h-100 stat-card-compact shadow-sm border-0">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 1.25rem !important;">{{ $stats['read'] }}</div>
                <div class="stat-label" style="font-size: 0.75rem !important;">{{ __('Read Messages') }}</div>
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
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search Messages...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="new">{{ __('New') }}</option>
                    <option value="read">{{ __('Read') }}</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-secondary w-100" onclick="fetchContacts(1)">
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
            <select id="filter_per_page" class="form-select form-select-sm select2-init" style="width: 80px;" onchange="fetchContacts(1)">
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
                    <input class="form-check-input col-toggle" type="checkbox" id="col_sender" value="1" checked disabled>
                    <label class="form-check-label" for="col_sender">{{ __('Sender Name') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_subject" value="2" checked>
                    <label class="form-check-label" for="col_subject">{{ __('Subject') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_status" value="3" checked>
                    <label class="form-check-label" for="col_status">{{ __('Status') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" id="col_actions" value="4" checked disabled>
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
        <table class="table table-hover table-striped align-middle mb-0 w-100" id="contacts-custom-table">
            <thead class="table-light">
                <tr>
                    <th class="border-bottom-0 col-toggle-0">#</th>
                    <th class="border-bottom-0 col-toggle-1">{{ __('Sender Name') }}</th>
                    <th class="border-bottom-0 col-toggle-2">{{ __('Subject') }}</th>
                    <th class="border-bottom-0 col-toggle-3" style="width: 150px;">{{ __('Status') }}</th>
                    <th class="border-bottom-0 text-center col-toggle-4" style="width: 150px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="custom-contacts-tbody">
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let currentView = localStorage.getItem('contacts_view_preference') || 'table';

    $(document).ready(function() {
        $('.select2-init').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        fetchContacts(1);

        $('#filter_search').on('keypress', function(e) {
            if(e.which == 13) fetchContacts(1);
        });
        $('#filter_status').on('change', function() { fetchContacts(1); });

        // Column toggling
        $('.col-toggle').on('change', function() {
            let colIdx = $(this).val();
            let isChecked = $(this).is(':checked');
            if (isChecked) {
                $('.col-toggle-' + colIdx).removeClass('d-none');
            } else {
                $('.col-toggle-' + colIdx).addClass('d-none');
            }
        });
    });

    function toggleView(view) {
        currentView = view;
        localStorage.setItem('contacts_view_preference', view);
        
        if (view === 'grid') {
            $('#table-view-container').addClass('d-none');
            $('#grid-view-container').removeClass('d-none');
            $('#btn-view-grid').addClass('active');
            $('#btn-view-table').removeClass('active');
        } else {
            $('#grid-view-container').addClass('d-none');
            $('#table-view-container').removeClass('d-none');
            $('#btn-view-table').addClass('active');
            $('#btn-view-grid').removeClass('active');
        }
    }

    function fetchContacts(page) {
        $('#page-loader').addClass('show');
        let search = $('#filter_search').val();
        let status = $('#filter_status').val();
        let per_page = $('#filter_per_page').val();

        $.ajax({
            url: "{{ route('admin.contacts.data') }}",
            type: "GET",
            data: {
                page: page,
                search: search,
                status: status,
                per_page: per_page
            },
            success: function(response) {
                if (response.success) {
                    renderTable(response.data);
                    renderGrid(response.data);
                    renderPagination(response.pagination);
                    // Apply column toggles
                    $('.col-toggle').each(function() {
                        let colIdx = $(this).val();
                        let isChecked = $(this).is(':checked');
                        if (!isChecked) $('.col-toggle-' + colIdx).addClass('d-none');
                    });
                    
                    // Apply saved view
                    toggleView(currentView);
                }
            },
            error: function() {
                alert('{{ __("Failed to fetch data") }}');
            },
            complete: function() {
                $('#page-loader').removeClass('show');
            }
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-contacts-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center py-5 text-muted">{{ __("No messages found.") }}</td></tr>');
            return;
        }

        $.each(data, function(index, item) {
            let statusBadge = item.is_read 
                ? '<span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fas fa-check-double me-1"></i> {{ __("Read") }}</span>'
                : '<span class="badge bg-danger-subtle text-danger px-2 py-1 fw-bold"><i class="fas fa-envelope me-1"></i> {{ __("New") }}</span>';
            
            let rowClass = !item.is_read ? 'table-active fw-bold' : '';

            let tr = `
                <tr class="${rowClass}">
                    <td class="col-toggle-0 fw-medium">#${item.id}</td>
                    <td class="col-toggle-1">
                        <div class="d-flex flex-column">
                            <span class="text-dark">${item.name}</span>
                            <span class="text-muted small">${item.email}</span>
                        </div>
                    </td>
                    <td class="col-toggle-2">${item.subject}</td>
                    <td class="col-toggle-3">${statusBadge}</td>
                    <td class="text-center col-toggle-4">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="${item.view_url}" class="btn btn-sm btn-light btn-icon" title="{{ __('View') }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>
                            <button type="button" class="btn btn-sm btn-light btn-icon text-danger" title="{{ __('Delete') }}" onclick="deleteContact(${item.id})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderGrid(data) {
        let gridContainer = $('#grid-view-container');
        gridContainer.empty();

        if (data.length === 0) {
            gridContainer.append('<div class="col-12 text-center py-5 text-muted">{{ __("No messages found.") }}</div>');
            return;
        }

        $.each(data, function(index, item) {
            let statusBadge = item.is_read 
                ? '<span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fas fa-check-double me-1"></i> {{ __("Read") }}</span>'
                : '<span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fas fa-envelope me-1"></i> {{ __("New") }}</span>';
            
            let cardClass = !item.is_read ? 'border-primary shadow-sm' : 'border-light shadow-sm';

            let card = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 ${cardClass} data-grid-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">${item.name}</h6>
                                    <div class="text-muted small">${item.email}</div>
                                </div>
                                ${statusBadge}
                            </div>
                            <div class="mb-3">
                                <p class="mb-0 text-dark fw-medium">${item.subject}</p>
                                <small class="text-muted" dir="ltr">${item.date}</small>
                            </div>
                            
                            <div class="d-flex gap-2 mt-auto pt-3 border-top">
                                <a href="${item.view_url}" class="btn btn-sm btn-light flex-grow-1 text-primary">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    {{ __('View') }}
                                </a>
                                <button type="button" class="btn btn-sm btn-light text-danger" onclick="deleteContact(${item.id})" title="{{ __('Delete') }}">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            gridContainer.append(card);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();
        
        let start = (pagination.current_page - 1) * $('#filter_per_page').val() + 1;
        let end = Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total);
        if (pagination.total === 0) { start = 0; end = 0; }
        
        let infoHtml = `<div class="text-muted small">{{ __('Showing') }} <span class="fw-bold">${start}</span> {{ __('to') }} <span class="fw-bold">${end}</span> {{ __('of') }} <span class="fw-bold">${pagination.total}</span> {{ __('results') }}</div>`;
        
        let linksHtml = '<ul class="pagination pagination-sm mb-0">';
        $.each(pagination.links, function(index, link) {
            let active = link.active ? 'active' : '';
            let disabled = link.url === null ? 'disabled' : '';
            let label = link.label;
            
            if(label.includes('Previous')) label = '&laquo;';
            if(label.includes('Next')) label = '&raquo;';
            
            if (link.url !== null) {
                let urlObj = new URL(link.url);
                let pageNum = urlObj.searchParams.get('page');
                linksHtml += `<li class="page-item ${active} ${disabled}"><a class="page-link cursor-pointer" onclick="fetchContacts(${pageNum})">${label}</a></li>`;
            } else {
                linksHtml += `<li class="page-item ${active} ${disabled}"><span class="page-link">${label}</span></li>`;
            }
        });
        linksHtml += '</ul>';
        
        container.append(infoHtml);
        container.append(linksHtml);
    }

    function deleteContact(id) {
        if(confirm('{{ __("Are you sure you want to delete this message?") }}')) {
            $.ajax({
                url: `/admin/contacts/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    fetchContacts(1);
                },
                error: function() {
                    alert('{{ __("Error deleting message.") }}');
                }
            });
        }
    }
</script>
@endsection
