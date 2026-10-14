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
                WJHTAdmin_btnLoading_helper(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#addRoleModal').modal('hide');
                    $('#addRoleForm')[0].reset();
                    if (typeof syncPermissionSelectorState === 'function') {
                        syncPermissionSelectorState('add');
                    }
                    fetchRoles(1);
                }
            },
            error: function(err) {
                WJHTAdmin_btnLoading_helper(btn, false);
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
        WJHTAdmin_btnLoading_helper(btn, true);

        let id = $('#edit_role_id').val();
        let url = window.RoleConfig.urls.update.replace(':id', id);

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(res) {
                WJHTAdmin_btnLoading_helper(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#editRoleModal').modal('hide');
                    fetchRoles(1);
                }
            },
            error: function(err) {
                WJHTAdmin_btnLoading_helper(btn, false);
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

    function WJHTAdmin_btnLoading_helper(btn, isLoading) {
        if (typeof WJHTAKAdmin !== 'undefined' && WJHTAKAdmin.btnLoading) {
            WJHTAKAdmin.btnLoading(btn, isLoading);
        } else {
            btn.prop('disabled', isLoading);
        }
    }

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

    // Initialize Permission Selectors
    $('.perm-selector-wrapper').each(function() {
        initPermissionSelector(this);
    });

    $('#addRoleModal').on('show.bs.modal', function () {
        if (typeof syncPermissionSelectorState === 'function') {
            syncPermissionSelectorState('add');
        }
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

function renderRolesTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.RoleConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(role => {
            let badgeHtml = role.is_system 
                ? '<span class="badge bg-warning text-dark ms-2"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' + __('System Role', 'دور نظام أساسي') + '</span>' 
                : '';

            let permsBadges = '';
            if (role.permissions && role.permissions.length > 0) {
                let limit = 4;
                let shown = role.permissions.slice(0, limit);
                shown.forEach(p => {
                    let label = (typeof p === 'object' && p !== null) ? (p.label || p.name) : p;
                    let code = (typeof p === 'object' && p !== null) ? (p.name || '') : p;
                    permsBadges += '<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:0.75rem;" title="' + code + '">' + label + '</span>';
                });
                if (role.permissions.length > limit) {
                    permsBadges += '<span class="badge bg-secondary-subtle text-secondary me-1 mb-1" style="font-size:0.75rem;">+' + (role.permissions.length - limit) + '</span>';
                }
            } else {
                permsBadges = '<span class="text-muted small">' + __('No permissions', 'لا توجد صلاحيات') + '</span>';
            }

            let canManageRole = (typeof role.can_manage !== 'undefined') ? role.can_manage : (window.RoleConfig && window.RoleConfig.canManage);

            let actionsHtml = '';
            if (canManageRole) {
                let deleteBtn = role.is_system 
                    ? '' 
                    : '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteRole(' + role.id + ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>' + __('Delete', 'حذف') + '</a></li>';

                actionsHtml = `
                    <div class="dropdown action-dropdown text-center">
                        <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="editRole(${role.id})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-primary"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>${__('Edit Role & Permissions', 'تعديل الدور والصلاحيات')}</a></li>
                            ${deleteBtn}
                        </ul>
                    </div>
                `;
            } else {
                actionsHtml = '<div class="text-center text-muted small">-</div>';
            }

            html += '<tr>';
            html += '<td class="align-middle fw-bold col-toggle-0"><div class="d-flex align-items-center">' + role.name + badgeHtml + '</div></td>';
            html += '<td class="align-middle col-toggle-1">' + permsBadges + '</td>';
            html += '<td class="align-middle col-toggle-2"><span class="badge bg-light text-dark px-3 py-2 border">' + role.users_count + ' ' + __('User', 'مستخدم') + '</span></td>';
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
            let badgeHtml = role.is_system 
                ? '<span class="badge bg-warning text-dark"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' + __('System Role', 'دور أساسي') + '</span>' 
                : '<span class="badge bg-light text-dark border">' + __('Custom Role', 'دور مخصص') + '</span>';

            let permsBadges = '';
            if (role.permissions && role.permissions.length > 0) {
                let limit = 4;
                let shown = role.permissions.slice(0, limit);
                shown.forEach(p => {
                    let label = (typeof p === 'object' && p !== null) ? (p.label || p.name) : p;
                    let code = (typeof p === 'object' && p !== null) ? (p.name || '') : p;
                    permsBadges += '<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:0.75rem;" title="' + code + '">' + label + '</span>';
                });
                if (role.permissions.length > limit) {
                    permsBadges += '<span class="badge bg-secondary-subtle text-secondary me-1 mb-1" style="font-size:0.75rem;">+' + (role.permissions.length - limit) + '</span>';
                }
            } else {
                permsBadges = '<span class="text-muted small">' + __('No permissions', 'لا توجد صلاحيات') + '</span>';
            }

            let canManageRole = (typeof role.can_manage !== 'undefined') ? role.can_manage : (window.RoleConfig && window.RoleConfig.canManage);

            let gridActions = '';
            if (canManageRole) {
                let deleteBtn = role.is_system 
                    ? '' 
                    : `<button class="btn btn-sm btn-outline-danger px-2" onclick="deleteRole(${role.id})" title="${__('Delete', 'حذف')}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                       </button>`;

                gridActions = `
                    <div class="d-flex gap-2 mt-auto pt-3 border-top">
                        <button class="btn btn-sm btn-primary flex-grow-1" onclick="editRole(${role.id})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            ${__('Edit Permissions', 'تعديل الصلاحيات')}
                        </button>
                        ${deleteBtn}
                    </div>
                `;
            }

            html += `
            <div class="col-12 col-sm-6 col-md-4 col-xl-4">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden" style="border-radius: var(--radius-lg); background: var(--bg-card);">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            ${badgeHtml}
                        </div>

                        <h5 class="fw-bold mb-1">${role.name}</h5>
                        <p class="text-muted small mb-3">${role.users_count} ${__('Users assigned', 'مستخدم مرتبط')}</p>

                        <div class="mb-4 flex-grow-1">
                            <div class="small fw-semibold text-muted mb-2">${__('Permissions:', 'الصلاحيات:')}</div>
                            <div class="d-flex flex-wrap">${permsBadges}</div>
                        </div>
                        
                        ${gridActions}
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
                
                let isSystem = res.is_system || ['admin', 'seller', 'bidder'].includes(res.role.name);
                if (isSystem) {
                    $('#edit_name').prop('readonly', true).addClass('bg-light').css('cursor', 'not-allowed');
                    $('#edit_system_role_badge').removeClass('d-none');
                    $('#edit_system_role_notice').removeClass('d-none');
                } else {
                    $('#edit_name').prop('readonly', false).removeClass('bg-light').css('cursor', 'text');
                    $('#edit_system_role_badge').addClass('d-none');
                    $('#edit_system_role_notice').addClass('d-none');
                }

                // Clear all checkboxes
                $('.edit-perm-checkbox').prop('checked', false);
                
                // Check the ones role has
                if (res.permissions && res.permissions.length > 0) {
                    res.permissions.forEach(permId => {
                        $('#edit_perm_' + permId).prop('checked', true);
                    });
                }
                
                if (typeof syncPermissionSelectorState === 'function') {
                    syncPermissionSelectorState('edit');
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
    let targetRole = (window.currentRolesData || []).find(r => r.id === id);
    if (targetRole && (targetRole.is_system || ['admin', 'seller', 'bidder'].includes(targetRole.name))) {
        toastr.warning(__('Cannot delete a core system role.', 'لا يمكن حذف دور أساسي للنظام.'));
        return;
    }

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

// ========================================================
// ===== PERMISSION SELECTOR COMPONENT LOGIC =============
// ========================================================
function initPermissionSelector(wrapper) {
    let $wrapper = $(wrapper);

    // 1. Checkbox click / change
    $wrapper.on('change', '.perm-checkbox', function () {
        let isChecked = $(this).is(':checked');
        let $card = $(this).closest('.perm-card');
        $card.toggleClass('is-checked', isChecked);

        let $catSection = $(this).closest('.perm-category-section');
        updateCategoryState($catSection);
        updateGlobalCount($wrapper);
    });

    // 2. Category header toggle all
    $wrapper.on('change', '.perm-cat-toggle-all', function () {
        let isChecked = $(this).is(':checked');
        let $catSection = $(this).closest('.perm-category-section');

        $catSection.find('.perm-checkbox').each(function () {
            $(this).prop('checked', isChecked);
            $(this).closest('.perm-card').toggleClass('is-checked', isChecked);
        });

        updateCategoryState($catSection);
        updateGlobalCount($wrapper);
    });

    // 3. Global Select All button
    $wrapper.on('click', '.perm-btn-select-all', function () {
        $wrapper.find('.perm-checkbox').each(function () {
            $(this).prop('checked', true);
            $(this).closest('.perm-card').addClass('is-checked');
        });
        $wrapper.find('.perm-category-section').each(function () {
            updateCategoryState($(this));
        });
        updateGlobalCount($wrapper);
    });

    // 4. Global Deselect All button
    $wrapper.on('click', '.perm-btn-deselect-all', function () {
        $wrapper.find('.perm-checkbox').each(function () {
            $(this).prop('checked', false);
            $(this).closest('.perm-card').removeClass('is-checked');
        });
        $wrapper.find('.perm-category-section').each(function () {
            updateCategoryState($(this));
        });
        updateGlobalCount($wrapper);
    });

    // 5. Category Pills filtering
    $wrapper.on('click', '.perm-filter-pill', function () {
        $wrapper.find('.perm-filter-pill').removeClass('active');
        $(this).addClass('active');

        let target = $(this).data('target');
        if (target === 'all') {
            $wrapper.find('.perm-category-section').removeClass('d-none');
        } else {
            $wrapper.find('.perm-category-section').each(function () {
                let cat = $(this).data('category');
                $(this).toggleClass('d-none', cat !== target);
            });
        }
        applyPermSearch($wrapper);
    });

    // 6. Live Search filtering
    let searchTimeout;
    $wrapper.on('keyup input', '.perm-search-box', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyPermSearch($wrapper);
        }, 150);
    });
}

function applyPermSearch($wrapper) {
    let query = $.trim($wrapper.find('.perm-search-box').val()).toLowerCase();
    let activePill = $wrapper.find('.perm-filter-pill.active').data('target') || 'all';
    let totalVisibleCards = 0;

    $wrapper.find('.perm-category-section').each(function () {
        let $section = $(this);
        let cat = $section.data('category');
        let allowedByPill = (activePill === 'all' || activePill === cat);

        if (!allowedByPill) {
            $section.addClass('d-none');
            return;
        }

        let visibleCardsInCat = 0;
        $section.find('.perm-card').each(function () {
            let keywords = $(this).data('keywords') || '';
            let matches = !query || keywords.indexOf(query) !== -1;
            $(this).toggleClass('d-none', !matches);
            if (matches) {
                visibleCardsInCat++;
                totalVisibleCards++;
            }
        });

        // Hide section if no cards match search query
        $section.toggleClass('d-none', visibleCardsInCat === 0);
    });

    // Show empty notice if no cards match anywhere
    $wrapper.find('.perm-empty-search').toggleClass('d-none', totalVisibleCards > 0);
}

function updateCategoryState($catSection) {
    let total = $catSection.find('.perm-checkbox').length;
    let checked = $catSection.find('.perm-checkbox:checked').length;

    $catSection.find('.cat-selected-count').text(checked);
    let $toggle = $catSection.find('.perm-cat-toggle-all');

    if (checked === 0) {
        $toggle.prop('checked', false).prop('indeterminate', false);
    } else if (checked === total) {
        $toggle.prop('checked', true).prop('indeterminate', false);
    } else {
        $toggle.prop('checked', false).prop('indeterminate', true);
    }
}

function updateGlobalCount($wrapper) {
    let checked = $wrapper.find('.perm-checkbox:checked').length;
    $wrapper.find('.perm-selected-count').text(checked);
}

window.syncPermissionSelectorState = function (prefix) {
    let $wrapper = $('#' + prefix + '_perm_selector_container');
    if (!$wrapper.length) return;

    $wrapper.find('.perm-checkbox').each(function () {
        let isChecked = $(this).is(':checked');
        $(this).closest('.perm-card').toggleClass('is-checked', isChecked);
    });

    $wrapper.find('.perm-category-section').each(function () {
        updateCategoryState($(this));
    });

    updateGlobalCount($wrapper);
};
