$(document).ready(function () {
    // Basic setup from global FeatureConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.FeatureConfig.csrf
        }
    });

    // Initialize View Mode
    let savedView = localStorage.getItem('features_view_mode') || 'table';
    toggleView(savedView);

    // Initial fetch
    fetchFeatures(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchFeatures(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchFeatures(1);
        }
    });

    $('#filter_status').on('change', function() {
        fetchFeatures(1);
    });

    // Add/Edit Form Submit
    $('#featureForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#featureId').val();
        let method = $('#featureMethod').val();
        let url = id ? window.FeatureConfig.urls.update.replace(':id', id) : window.FeatureConfig.urls.store;
        
        let btn = $('#saveBtn');
        WJHTAKAdmin.btnLoading(btn, true);

        let data = $(this).serialize();
        if (!$('#is_active').is(':checked')) {
            data += '&is_active=0';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#featureModal').modal('hide');
                    $('#featureForm')[0].reset();
                    fetchFeatures(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.FeatureConfig.trans.unexpectedError);
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
        localStorage.setItem('features_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });
});

function applyColumnVisibility() {
    let savedVis = localStorage.getItem('features_col_visibility');
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

window.currentFeaturesData = [];

window.toggleView = function(view) {
    localStorage.setItem('features_view_mode', view);
    
    if (view === 'grid') {
        $('#table-view-container').addClass('d-none');
        $('#grid-view-container').removeClass('d-none');
        $('#btn-view-grid').addClass('active');
        $('#btn-view-table').removeClass('active');
        if (window.currentFeaturesData.length > 0) {
            renderFeaturesGrid(window.currentFeaturesData);
        }
    } else {
        $('#grid-view-container').addClass('d-none');
        $('#table-view-container').removeClass('d-none');
        $('#btn-view-table').addClass('active');
        $('#btn-view-grid').removeClass('active');
        if (window.currentFeaturesData.length > 0) {
            renderFeaturesTable(window.currentFeaturesData);
        }
    }
    applyColumnVisibility();
};

window.fetchFeatures = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();
    let status = $('#filter_status').val();

    let loadingHtml = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.FeatureConfig.trans.loading + '</div></div>';
    
    $('#custom-features-tbody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.FeatureConfig.trans.loading + '</div></td></tr>');
    $('#grid-view-container').html(loadingHtml);

    $.ajax({
        url: window.FeatureConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status
        },
        success: function(res) {
            if(res.success) {
                window.currentFeaturesData = res.data;
                
                let currentView = localStorage.getItem('features_view_mode') || 'table';
                if (currentView === 'grid') {
                    renderFeaturesGrid(res.data);
                } else {
                    renderFeaturesTable(res.data);
                }
                
                renderPagination(res.pagination);
                applyColumnVisibility();
            }
        },
        error: function() {
            let errorHtml = '<div class="col-12 text-center text-danger py-4">' + window.FeatureConfig.trans.errorLoading + '</div>';
            $('#custom-features-tbody').html('<tr><td colspan="4" class="text-center text-danger py-4">' + window.FeatureConfig.trans.errorLoading + '</td></tr>');
            $('#grid-view-container').html(errorHtml);
        }
    });
};

function renderFeaturesTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.FeatureConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(feature => {
            let actionsHtml = `
                <div class="dropdown action-dropdown text-center">
                    <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                        <li><a class="dropdown-item text-primary" href="javascript:void(0)" onclick="editFeature(${feature.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>${__('Edit', 'تعديل')}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteFeature(${feature.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>${__('Delete', 'حذف')}</a></li>
                    </ul>
                </div>
            `;

            let toggleSwitch = `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" onchange="toggleStatus(${feature.id})" ${feature.is_active ? 'checked' : ''}>
                </div>
            `;

            let iconHtml = `<div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--bg-body); color: var(--${feature.color_class});">${feature.icon}</div>`;

            html += '<tr>';
            html += '<td class="align-middle text-muted small col-toggle-0">' + feature.id + '</td>';
            html += '<td class="align-middle col-toggle-1">';
            html += '   <div class="d-flex align-items-center gap-3">';
            html += '       ' + iconHtml;
            html += '       <div>';
            html += '           <div class="fw-bold mb-1">' + feature.title + '</div>';
            html += '           <div class="text-muted small">' + feature.description + '</div>';
            html += '       </div>';
            html += '   </div>';
            html += '</td>';
            html += '<td class="align-middle col-toggle-2">' + toggleSwitch + '</td>';
            html += '<td class="align-middle col-toggle-3">' + actionsHtml + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-features-tbody').html(html);
}

function renderFeaturesGrid(data) {
    let html = '';
    if (data.length === 0) {
        html = '<div class="col-12 text-center py-4 text-muted">' + window.FeatureConfig.trans.noRecords + '</div>';
    } else {
        data.forEach(feature => {
            html += `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden">
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <div class="d-flex justify-content-between align-items-start mb-3 col-toggle-2">
                            <span class="badge ${feature.is_active ? 'bg-success' : 'bg-secondary'} bg-opacity-10 ${feature.is_active ? 'text-success' : 'text-secondary'} px-3 py-1 border-0">
                                ${feature.is_active ? __('Active', 'نشط') : __('Inactive', 'غير نشط')}
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="toggleStatus(${feature.id})" ${feature.is_active ? 'checked' : ''}>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2 col-toggle-1">
                            <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--bg-body); color: var(--${feature.color_class});">${feature.icon}</div>
                            <h6 class="fw-bold mb-0 text-dark">${feature.title}</h6>
                        </div>
                        <p class="text-muted small flex-grow-1 col-toggle-1" style="line-height: 1.5;">${feature.description}</p>
                        
                        <div class="d-flex gap-2 justify-content-center mt-3 pt-3 border-top col-toggle-3">
                            <button class="btn btn-sm btn-outline-primary px-3 flex-grow-1" onclick="editFeature(${feature.id})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                ${__('Edit', 'تعديل')}
                            </button>
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="deleteFeature(${feature.id})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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

function __(en, ar) {
    let dir = $('html').attr('dir') || 'rtl';
    return dir === 'rtl' ? (ar || en) : en;
}

function renderPagination(pagination) {
    let container = $('#custom-pagination');
    container.empty();

    if (pagination.total === 0) return;

    let infoHtml = '<div class="text-muted small">' + window.FeatureConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.FeatureConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.FeatureConfig.trans.of + ' ' + pagination.total + ' ' + window.FeatureConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchFeatures(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.openAddModal = function() {
    $('#featureForm')[0].reset();
    $('#featureMethod').val('POST');
    $('#featureId').val('');
    $('#modalTitleText').text(__('Add New Feature', 'إضافة ميزة جديدة'));
    $('#featureModal').modal('show');
};

window.editFeature = function(id) {
    let feature = window.currentFeaturesData.find(f => f.id == id);
    if (!feature) return;

    $('#featureForm')[0].reset();
    $('#featureMethod').val('PUT');
    $('#featureId').val(id);
    
    $('#title_ar').val(feature.title_ar);
    $('#title_en').val(feature.title_en);
    $('#description_ar').val(feature.description_ar);
    $('#description_en').val(feature.description_en);
    $('#icon').val(feature.icon);
    $('#color_class').val(feature.color_class);
    $('#sort_order').val(feature.sort_order);
    $('#is_active').prop('checked', feature.is_active);
    
    $('#modalTitleText').text(__('Edit Feature', 'تعديل الميزة'));
    $('#featureModal').modal('show');
};

window.deleteFeature = function(id) {
    Swal.fire({
        title: window.FeatureConfig.trans.deleteFeature,
        text: window.FeatureConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.FeatureConfig.trans.yesDelete,
        cancelButtonText: window.FeatureConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.FeatureConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: window.FeatureConfig.csrf },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchFeatures(1);
                    } else {
                        toastr.error(res.message || window.FeatureConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.FeatureConfig.trans.unexpectedError);
                }
            });
        }
    });
};

window.toggleStatus = function(id) {
    let url = window.FeatureConfig.urls.toggleActive.replace(':id', id);
    $.ajax({
        url: url,
        method: 'POST',
        data: { _token: window.FeatureConfig.csrf },
        success: function(res) {
            if (res.success) {
                toastr.success(res.message);
                fetchFeatures(1);
            } else {
                toastr.error(res.message || window.FeatureConfig.trans.unexpectedError);
                fetchFeatures(1);
            }
        },
        error: function() {
            toastr.error(window.FeatureConfig.trans.unexpectedError);
            fetchFeatures(1);
        }
    });
};
