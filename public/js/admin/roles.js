$(document).ready(function () {
    // Basic setup from global RoleConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.RoleConfig.csrf
        }
    });
    // Initial fetch
    fetchRoles(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchRoles(1), 500);
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

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchRoles(1);
        }
    });

});

window.fetchRoles = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();

    $('#custom-roles-tbody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.RoleConfig.trans.loading + '</div></td></tr>');

    $.ajax({
        url: window.RoleConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(res) {
            if(res.success) {
                renderRolesTable(res.data);
                renderPagination(res.pagination);
            }
        },
        error: function() {
            $('#custom-roles-tbody').html('<tr><td colspan="4" class="text-center text-danger py-4">' + window.RoleConfig.trans.errorLoading + '</td></tr>');
        }
    });
};

function renderRolesTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.RoleConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(role => {
            html += '<tr>';
            html += '<td class="align-middle">' + role.name + '</td>';
            html += '<td class="align-middle">' + role.permissions + '</td>';
            html += '<td class="align-middle">' + role.users_count + '</td>';
            html += '<td class="align-middle text-center">' + role.actions + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-roles-tbody').html(html);
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
