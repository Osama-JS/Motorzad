$(document).ready(function () {
    // Basic setup from global PermissionConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.PermissionConfig.csrf
        }
    });

    // Initialize View Mode
    let savedView = localStorage.getItem('permissions_view_mode') || 'table';
    toggleView(savedView);

    // Initial fetch
    fetchPermissions(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchPermissions(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchPermissions(1);
        }
    });

    // Add Permission Form Submit
    $('#addPermissionForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        WJHTAKAdmin.btnLoading(btn, true);

        $.ajax({
            url: window.PermissionConfig.urls.store,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#addPermissionModal').modal('hide');
                    $('#addPermissionForm')[0].reset();
                    fetchPermissions(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.PermissionConfig.trans.unexpectedError);
                }
            }
        });
    });

    // Initialize Select2 globally
    function initSelect2() {
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-init').each(function() {
            let dropdownParent = $(this).data('dropdown-parent');
            $(this).select2({
                dir: dir,
                dropdownParent: dropdownParent ? $(dropdownParent) : $(document.body),
                minimumResultsForSearch: 10
            });
        });
    }
    initSelect2();

    // Handle Column Toggle check/uncheck
    $('.col-toggle').on('change', function() {
        let visArray = [];
        $('.col-toggle:checked').each(function() {
            visArray.push($(this).val());
        });
        localStorage.setItem('permissions_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });
});

function applyColumnVisibility() {
    let savedVis = localStorage.getItem('permissions_col_visibility');
    if (savedVis) {
        let visArray = JSON.parse(savedVis);
        $('.col-toggle').each(function() {
            let colIdx = $(this).val();
            let isVisible = visArray.includes(colIdx);
            $(this).prop('checked', isVisible);
            
            if (isVisible) {
                $('.col-toggle-' + colIdx).removeClass('d-none');
            } else {
                $('.col-toggle-' + colIdx).addClass('d-none');
            }
        });
    }
}

window.currentPermissionsData = [];

window.toggleView = function(view) {
    localStorage.setItem('permissions_view_mode', view);
    
    if (view === 'grid') {
        $('#table-view-container').addClass('d-none');
        $('#grid-view-container').removeClass('d-none');
        $('#btn-view-grid').addClass('active');
        $('#btn-view-table').removeClass('active');
        if (window.currentPermissionsData.length > 0) {
            renderPermissionsGrid(window.currentPermissionsData);
        }
    } else {
        $('#grid-view-container').addClass('d-none');
        $('#table-view-container').removeClass('d-none');
        $('#btn-view-table').addClass('active');
        $('#btn-view-grid').removeClass('active');
        if (window.currentPermissionsData.length > 0) {
            renderPermissionsTable(window.currentPermissionsData);
        }
    }
    applyColumnVisibility();
};

window.fetchPermissions = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();

    let loadingHtml = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.PermissionConfig.trans.loading + '</div></div>';
    
    $('#custom-permissions-tbody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.PermissionConfig.trans.loading + '</div></td></tr>');
    $('#grid-view-container').html(loadingHtml);

    $.ajax({
        url: window.PermissionConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(res) {
            if(res.success) {
                window.currentPermissionsData = res.data;
                
                let currentView = localStorage.getItem('permissions_view_mode') || 'table';
                if (currentView === 'grid') {
                    renderPermissionsGrid(res.data);
                } else {
                    renderPermissionsTable(res.data);
                }
                
                renderPagination(res.pagination);
                applyColumnVisibility();
            }
        },
        error: function() {
            let errorHtml = '<div class="col-12 text-center text-danger py-4">' + window.PermissionConfig.trans.errorLoading + '</div>';
            $('#custom-permissions-tbody').html('<tr><td colspan="4" class="text-center text-danger py-4">' + window.PermissionConfig.trans.errorLoading + '</td></tr>');
            $('#grid-view-container').html(errorHtml);
        }
    });
};

function renderPermissionsTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.PermissionConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(permission => {
            let actionsHtml = `
                <div class="dropdown action-dropdown text-center">
                    <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deletePermission(${permission.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>${__('Delete', 'حذف')}</a></li>
                    </ul>
                </div>
            `;

            html += '<tr>';
            html += '<td class="align-middle text-muted small col-toggle-0">' + permission.id + '</td>';
            html += '<td class="align-middle fw-bold col-toggle-1">' + permission.name + '</td>';
            html += '<td class="align-middle col-toggle-2"><span class="badge bg-light text-dark px-3 py-2 border">' + permission.roles_count + ' ' + __('Role', 'أدوار') + '</span></td>';
            html += '<td class="align-middle col-toggle-3">' + actionsHtml + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-permissions-tbody').html(html);
}

function renderPermissionsGrid(data) {
    let html = '';
    if (data.length === 0) {
        html = '<div class="col-12 text-center py-4 text-muted">' + window.PermissionConfig.trans.noRecords + '</div>';
    } else {
        data.forEach(permission => {
            html += `
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle col-toggle-0" style="width: 56px; height: 56px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h6 class="fw-bold mb-2 col-toggle-1">${permission.name}</h6>
                        <span class="badge bg-light text-dark px-3 py-1 border mb-3 col-toggle-2">${permission.roles_count} ${__('Role', 'أدوار')}</span>
                        
                        <div class="d-flex justify-content-center mt-auto col-toggle-3">
                            <button class="btn btn-sm btn-outline-danger px-3 w-100" onclick="deletePermission(${permission.id})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                ${__('Delete', 'حذف')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
    }
    $('#grid-view-container').html(html);
}

// Helper translation function for JS side rendering
function __(en, ar) {
    let dir = $('html').attr('dir') || 'rtl';
    return dir === 'rtl' ? (ar || en) : en;
}

function renderPagination(pagination) {
    let container = $('#custom-pagination');
    container.empty();

    if (pagination.total === 0) return;

    let infoHtml = '<div class="text-muted small">' + window.PermissionConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.PermissionConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.PermissionConfig.trans.of + ' ' + pagination.total + ' ' + window.PermissionConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchPermissions(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.deletePermission = function(id) {
    Swal.fire({
        title: window.PermissionConfig.trans.deletePermission,
        text: window.PermissionConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.PermissionConfig.trans.yesDelete,
        cancelButtonText: window.PermissionConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.PermissionConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: window.PermissionConfig.csrf },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchPermissions(1);
                    } else {
                        toastr.error(res.message || window.PermissionConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.PermissionConfig.trans.unexpectedError);
                }
            });
        }
    });
};
