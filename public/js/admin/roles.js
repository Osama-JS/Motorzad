$(document).ready(function () {
    // Basic setup from global RoleConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.RoleConfig.csrf
        }
    });

    // Initialize View Mode
    let savedView = localStorage.getItem('roles_view_mode') || 'table';
    toggleView(savedView);

    // Initial fetch
    fetchRoles(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchRoles(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchRoles(1);
        }
    });

    // Add Role Form Submit
    $('#addRoleForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        WJHTAKAdmin.btnLoading(btn, true);

        $.ajax({
            url: window.RoleConfig.urls.store,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#addRoleModal').modal('hide');
                    $('#addRoleForm')[0].reset();
                    fetchRoles(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.RoleConfig.trans.unexpectedError);
                }
            }
        });
    });

    // Edit Role Form Submit
    $('#editRoleForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        WJHTAKAdmin.btnLoading(btn, true);

        let id = $('#edit_role_id').val();
        let url = window.RoleConfig.urls.update.replace(':id', id);

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#editRoleModal').modal('hide');
                    fetchRoles(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.RoleConfig.trans.unexpectedError);
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
        localStorage.setItem('roles_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });
});

function applyColumnVisibility() {
    let savedVis = localStorage.getItem('roles_col_visibility');
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

window.currentRolesData = [];

window.toggleView = function(view) {
    localStorage.setItem('roles_view_mode', view);
    
    if (view === 'grid') {
        $('#table-view-container').addClass('d-none');
        $('#grid-view-container').removeClass('d-none');
        $('#btn-view-grid').addClass('active');
        $('#btn-view-table').removeClass('active');
        if (window.currentRolesData.length > 0) {
            renderRolesGrid(window.currentRolesData);
        }
    } else {
        $('#grid-view-container').addClass('d-none');
        $('#table-view-container').removeClass('d-none');
        $('#btn-view-table').addClass('active');
        $('#btn-view-grid').removeClass('active');
        if (window.currentRolesData.length > 0) {
            renderRolesTable(window.currentRolesData);
        }
    }
    applyColumnVisibility();
};

window.fetchRoles = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();

    let loadingHtml = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.RoleConfig.trans.loading + '</div></div>';
    
    $('#custom-roles-tbody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.RoleConfig.trans.loading + '</div></td></tr>');
    $('#grid-view-container').html(loadingHtml);

    $.ajax({
        url: window.RoleConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(res) {
            if(res.success) {
                window.currentRolesData = res.data;
                
                let currentView = localStorage.getItem('roles_view_mode') || 'table';
                if (currentView === 'grid') {
                    renderRolesGrid(res.data);
                } else {
                    renderRolesTable(res.data);
                }
                
                renderPagination(res.pagination);
                applyColumnVisibility();
            }
        },
        error: function() {
            let errorHtml = '<div class="col-12 text-center text-danger py-4">' + window.RoleConfig.trans.errorLoading + '</div>';
            $('#custom-roles-tbody').html('<tr><td colspan="4" class="text-center text-danger py-4">' + window.RoleConfig.trans.errorLoading + '</td></tr>');
            $('#grid-view-container').html(errorHtml);
        }
    });
};

function generatePermissionsHtml(permissionsArray) {
    let html = '<div style="display:flex; flex-wrap:wrap; gap:0.3rem; justify-content:center;">';
    let displayPerms = permissionsArray.slice(0, 3);
    displayPerms.forEach(perm => {
        html += '<span class="badge bg-primary text-white">' + perm + '</span>';
    });
    if (permissionsArray.length > 3) {
        html += '<span class="badge" style="background:rgba(100,116,139,0.1); color:var(--text-muted);">+' + (permissionsArray.length - 3) + '</span>';
    }
    html += '</div>';
    return html;
}

function renderRolesTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.RoleConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(role => {
            let permissionsHtml = generatePermissionsHtml(role.permissions);
            
            let actionsHtml = `
                <div class="dropdown action-dropdown text-center">
                    <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                        <li><a class="dropdown-item text-primary" href="javascript:void(0)" onclick="editRole(${role.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>${__('Edit', 'تعديل')}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteRole(${role.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>${__('Delete', 'حذف')}</a></li>
                    </ul>
                </div>
            `;

            html += '<tr>';
            html += '<td class="align-middle fw-bold col-toggle-0">' + role.name + '</td>';
            html += '<td class="align-middle col-toggle-1">' + permissionsHtml + '</td>';
            html += '<td class="align-middle text-center col-toggle-2"><span class="badge bg-light text-dark px-3 py-2 border">' + role.users_count + '</span></td>';
            html += '<td class="align-middle col-toggle-3">' + actionsHtml + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-roles-tbody').html(html);
}

function renderRolesGrid(data) {
    let html = '';
    if (data.length === 0) {
        html = '<div class="col-12 text-center py-4 text-muted">' + window.RoleConfig.trans.noRecords + '</div>';
    } else {
        data.forEach(role => {
            let permissionsHtml = generatePermissionsHtml(role.permissions);
            
            html += `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle col-toggle-0" style="width: 64px; height: 64px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h5 class="fw-bold mb-1 col-toggle-0">${role.name}</h5>
                        <p class="text-muted small mb-3 col-toggle-2">${role.users_count} ${__('Users', 'مستخدمين')}</p>
                        
                        <div class="p-3 bg-light rounded-3 mb-3 col-toggle-1">
                            <p class="text-muted small fw-bold mb-2">${__('Permissions', 'الصلاحيات')}</p>
                            ${permissionsHtml}
                        </div>

                        <div class="d-flex gap-2 justify-content-center mt-auto col-toggle-3">
                            <button class="btn btn-sm btn-outline-primary px-3 flex-grow-1" onclick="editRole(${role.id})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                ${__('Edit', 'تعديل')}
                            </button>
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="deleteRole(${role.id})">
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

    let infoHtml = '<div class="text-muted small">' + window.RoleConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.RoleConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.RoleConfig.trans.of + ' ' + pagination.total + ' ' + window.RoleConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchRoles(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.editRole = function(id) {
    let url = window.RoleConfig.urls.show.replace(':id', id);
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                $('#edit_role_id').val(res.role.id);
                $('#edit_name').val(res.role.name);
                
                // Clear all checkboxes
                $('.edit-perm-checkbox').prop('checked', false);
                
                // Check the ones role has
                if (res.permissions && res.permissions.length > 0) {
                    res.permissions.forEach(permId => {
                        $('#edit_perm_' + permId).prop('checked', true);
                    });
                }
                
                $('#editRoleModal').modal('show');
            }
        },
        error: function() {
            toastr.error(window.RoleConfig.trans.errorLoading);
        }
    });
};

window.deleteRole = function(id) {
    Swal.fire({
        title: window.RoleConfig.trans.deleteRole,
        text: window.RoleConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.RoleConfig.trans.yesDelete,
        cancelButtonText: window.RoleConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.RoleConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: window.RoleConfig.csrf },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchRoles(1);
                    } else {
                        toastr.error(res.message || window.RoleConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.RoleConfig.trans.unexpectedError);
                }
            });
        }
    });
};
