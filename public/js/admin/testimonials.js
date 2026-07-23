$(document).ready(function () {
    // Basic setup from global TestimonialConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.TestimonialConfig.csrf
        }
    });

    // Initialize View Mode
    let savedView = localStorage.getItem('testimonials_view_mode') || 'table';
    toggleView(savedView);

    // Initial fetch
    fetchTestimonials(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchTestimonials(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchTestimonials(1);
        }
    });

    $('#filter_status').on('change', function() {
        fetchTestimonials(1);
    });

    // Add/Edit Form Submit
    $('#testimonialForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#testimonialId').val();
        let method = $('#testimonialMethod').val();
        let url = id ? window.TestimonialConfig.urls.update.replace(':id', id) : window.TestimonialConfig.urls.store;
        
        let btn = $('#saveBtn');
        WJHTAKAdmin.btnLoading(btn, true);

        // serialize handles everything, but checkboxes aren't sent if they are unchecked.
        // We will make sure is_active is sent as 0 if unchecked.
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
                    $('#testimonialModal').modal('hide');
                    $('#testimonialForm')[0].reset();
                    fetchTestimonials(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.TestimonialConfig.trans.unexpectedError);
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
        localStorage.setItem('testimonials_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });
});

function applyColumnVisibility() {
    let savedVis = localStorage.getItem('testimonials_col_visibility');
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

window.currentTestimonialsData = [];

window.toggleView = function(view) {
    localStorage.setItem('testimonials_view_mode', view);
    
    if (view === 'grid') {
        $('#table-view-container').addClass('d-none');
        $('#grid-view-container').removeClass('d-none');
        $('#btn-view-grid').addClass('active');
        $('#btn-view-table').removeClass('active');
        if (window.currentTestimonialsData.length > 0) {
            renderTestimonialsGrid(window.currentTestimonialsData);
        }
    } else {
        $('#grid-view-container').addClass('d-none');
        $('#table-view-container').removeClass('d-none');
        $('#btn-view-table').addClass('active');
        $('#btn-view-grid').removeClass('active');
        if (window.currentTestimonialsData.length > 0) {
            renderTestimonialsTable(window.currentTestimonialsData);
        }
    }
    applyColumnVisibility();
};

window.fetchTestimonials = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();
    let status = $('#filter_status').val();

    let loadingHtml = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.TestimonialConfig.trans.loading + '</div></div>';
    
    $('#custom-testimonials-tbody').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.TestimonialConfig.trans.loading + '</div></td></tr>');
    $('#grid-view-container').html(loadingHtml);

    $.ajax({
        url: window.TestimonialConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status
        },
        success: function(res) {
            if(res.success) {
                window.currentTestimonialsData = res.data;
                
                let currentView = localStorage.getItem('testimonials_view_mode') || 'table';
                if (currentView === 'grid') {
                    renderTestimonialsGrid(res.data);
                } else {
                    renderTestimonialsTable(res.data);
                }
                
                renderPagination(res.pagination);
                applyColumnVisibility();
            }
        },
        error: function() {
            let errorHtml = '<div class="col-12 text-center text-danger py-4">' + window.TestimonialConfig.trans.errorLoading + '</div>';
            $('#custom-testimonials-tbody').html('<tr><td colspan="4" class="text-center text-danger py-4">' + window.TestimonialConfig.trans.errorLoading + '</td></tr>');
            $('#grid-view-container').html(errorHtml);
        }
    });
};

function renderTestimonialsTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">' + window.TestimonialConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(item => {
            let actionsHtml = `
                <div class="dropdown action-dropdown text-center">
                    <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                        <li><a class="dropdown-item text-primary" href="javascript:void(0)" onclick="editTestimonial(${item.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>${__('Edit', 'تعديل')}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteTestimonial(${item.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>${__('Delete', 'حذف')}</a></li>
                    </ul>
                </div>
            `;

            let toggleSwitch = `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" onchange="toggleStatus(${item.id})" ${item.is_active ? 'checked' : ''}>
                </div>
            `;

            html += '<tr>';
            html += '<td class="align-middle text-muted small col-toggle-0">' + item.id + '</td>';
            html += '<td class="align-middle col-toggle-1">';
            html += '   <div class="fw-bold mb-1">' + item.name + ' <span class="text-muted fw-normal">(' + item.role + ')</span></div>';
            html += '   <div class="testimonial-text-collapse">' + item.text + '</div>';
            html += '</td>';
            html += '<td class="align-middle col-toggle-2">' + toggleSwitch + '</td>';
            html += '<td class="align-middle col-toggle-3">' + actionsHtml + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-testimonials-tbody').html(html);
}

function renderTestimonialsGrid(data) {
    let html = '';
    if (data.length === 0) {
        html = '<div class="col-12 text-center py-4 text-muted">' + window.TestimonialConfig.trans.noRecords + '</div>';
    } else {
        data.forEach(item => {
            html += `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden">
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <div class="d-flex justify-content-between align-items-start mb-3 col-toggle-2">
                            <span class="badge ${item.is_active ? 'bg-success' : 'bg-secondary'} bg-opacity-10 ${item.is_active ? 'text-success' : 'text-secondary'} px-3 py-1 border-0">
                                ${item.is_active ? __('Active', 'نشط') : __('Inactive', 'غير نشط')}
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="toggleStatus(${item.id})" ${item.is_active ? 'checked' : ''}>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark col-toggle-1">${item.name}</h6>
                        <div class="text-muted small mb-2 col-toggle-1">${item.role}</div>
                        <p class="text-muted small flex-grow-1 col-toggle-1" style="line-height: 1.5;">${item.text}</p>
                        
                        <div class="d-flex gap-2 justify-content-center mt-3 pt-3 border-top col-toggle-3">
                            <button class="btn btn-sm btn-outline-primary px-3 flex-grow-1" onclick="editTestimonial(${item.id})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                ${__('Edit', 'تعديل')}
                            </button>
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="deleteTestimonial(${item.id})">
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

// Helper translation function for JS side rendering
function __(en, ar) {
    let dir = $('html').attr('dir') || 'rtl';
    return dir === 'rtl' ? (ar || en) : en;
}

function renderPagination(pagination) {
    let container = $('#custom-pagination');
    container.empty();

    if (pagination.total === 0) return;

    let infoHtml = '<div class="text-muted small">' + window.TestimonialConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.TestimonialConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.TestimonialConfig.trans.of + ' ' + pagination.total + ' ' + window.TestimonialConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchTestimonials(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.openAddModal = function() {
    $('#testimonialForm')[0].reset();
    $('#testimonialMethod').val('POST');
    $('#testimonialId').val('');
    $('#modalTitleText').text(__('Add New Testimonial', 'إضافة رأي جديد'));
    $('#testimonialModal').modal('show');
};

window.editTestimonial = function(id) {
    let item = window.currentTestimonialsData.find(f => f.id == id);
    if (!item) return;

    $('#testimonialForm')[0].reset();
    $('#testimonialMethod').val('PUT');
    $('#testimonialId').val(id);
    
    $('#name_ar').val(item.name_ar);
    $('#name_en').val(item.name_en);
    $('#role_ar').val(item.role_ar);
    $('#role_en').val(item.role_en);
    $('#text_ar').val(item.text_ar);
    $('#text_en').val(item.text_en);
    $('#avatar_init').val(item.avatar_init);
    $('#avatar_init_en').val(item.avatar_init_en);
    $('#is_active').prop('checked', item.is_active);
    
    $('#modalTitleText').text(__('Edit Testimonial', 'تعديل الرأي'));
    $('#testimonialModal').modal('show');
};

window.deleteTestimonial = function(id) {
    Swal.fire({
        title: window.TestimonialConfig.trans.deleteTestimonial,
        text: window.TestimonialConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.TestimonialConfig.trans.yesDelete,
        cancelButtonText: window.TestimonialConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.TestimonialConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: window.TestimonialConfig.csrf },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchTestimonials(1);
                    } else {
                        toastr.error(res.message || window.TestimonialConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.TestimonialConfig.trans.unexpectedError);
                }
            });
        }
    });
};

window.toggleStatus = function(id) {
    let url = window.TestimonialConfig.urls.toggleActive.replace(':id', id);
    $.ajax({
        url: url,
        method: 'POST',
        data: { _token: window.TestimonialConfig.csrf },
        success: function(res) {
            if (res.success) {
                toastr.success(res.message);
                fetchTestimonials(1);
            } else {
                toastr.error(res.message || window.TestimonialConfig.trans.unexpectedError);
                fetchTestimonials(1); // revert checkbox state
            }
        },
        error: function() {
            toastr.error(window.TestimonialConfig.trans.unexpectedError);
            fetchTestimonials(1); // revert checkbox state
        }
    });
};
