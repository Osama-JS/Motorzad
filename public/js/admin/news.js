$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.NewsConfig.csrf
        }
    });

    let savedView = localStorage.getItem('news_view_mode') || 'table';
    toggleView(savedView);

    fetchNews(1);

    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchNews(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchNews(1);
        }
    });

    $('#filter_status').on('change', function() {
        fetchNews(1);
    });

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


    $('.col-toggle').on('change', function() {
        let visArray = [];
        $('.col-toggle:checked').each(function() {
            visArray.push($(this).val());
        });
        localStorage.setItem('news_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });
});

function applyColumnVisibility() {
    let savedVis = localStorage.getItem('news_col_visibility');
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

window.currentNewsData = [];

window.toggleView = function(view) {
    localStorage.setItem('news_view_mode', view);
    
    if (view === 'grid') {
        $('#table-view-container').addClass('d-none');
        $('#grid-view-container').removeClass('d-none');
        $('#btn-view-grid').addClass('active');
        $('#btn-view-table').removeClass('active');
        if (window.currentNewsData.length > 0) {
            renderNewsGrid(window.currentNewsData);
        }
    } else {
        $('#grid-view-container').addClass('d-none');
        $('#table-view-container').removeClass('d-none');
        $('#btn-view-table').addClass('active');
        $('#btn-view-grid').removeClass('active');
        if (window.currentNewsData.length > 0) {
            renderNewsTable(window.currentNewsData);
        }
    }
    applyColumnVisibility();
};

window.fetchNews = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();
    let status = $('#filter_status').val();

    let loadingHtml = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.NewsConfig.trans.loading + '</div></div>';
    
    $('#custom-news-tbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.NewsConfig.trans.loading + '</div></td></tr>');
    $('#grid-view-container').html(loadingHtml);

    $.ajax({
        url: window.NewsConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status
        },
        success: function(res) {
            if(res.success) {
                window.currentNewsData = res.data;
                
                let currentView = localStorage.getItem('news_view_mode') || 'table';
                if (currentView === 'grid') {
                    renderNewsGrid(res.data);
                } else {
                    renderNewsTable(res.data);
                }
                
                renderPagination(res.pagination);
                applyColumnVisibility();
            }
        },
        error: function() {
            let errorHtml = '<div class="col-12 text-center text-danger py-4">' + window.NewsConfig.trans.errorLoading + '</div>';
            $('#custom-news-tbody').html('<tr><td colspan="5" class="text-center text-danger py-4">' + window.NewsConfig.trans.errorLoading + '</td></tr>');
            $('#grid-view-container').html(errorHtml);
        }
    });
};

function renderNewsTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="5" class="text-center py-4 text-muted">' + window.NewsConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(news => {
            let actionsHtml = `
                <div class="dropdown action-dropdown text-center">
                    <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                        <li><a class="dropdown-item text-primary" href="${window.NewsConfig.urls.edit.replace(':id', news.id)}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>${__('Edit', 'تعديل')}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteNews(${news.id})">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>${__('Delete', 'حذف')}</a></li>
                    </ul>
                </div>
            `;

            let toggleSwitch = `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" onchange="toggleStatus(${news.id})" ${news.is_active ? 'checked' : ''}>
                </div>
            `;
            
            let imageHtml = news.image_url ? `<img src="${news.image_url}" class="news-image-preview">` : `<div class="bg-light text-muted d-flex align-items-center justify-content-center news-image-preview" style="height: 60px;">No Image</div>`;

            html += '<tr>';
            html += '<td class="align-middle text-muted small col-toggle-0">' + news.id + '</td>';
            html += '<td class="align-middle col-toggle-1">' + imageHtml + '</td>';
            html += '<td class="align-middle col-toggle-2">';
            html += '   <div class="fw-bold mb-1">' + (news.is_featured ? '<i class="fas fa-star text-warning me-1" title="' + __('Featured', 'مميز') + '"></i> ' : '') + news.title + '</div>';
            html += '   <div class="mb-2"><span class="badge bg-primary bg-opacity-10 text-primary border-0 me-2"><i class="fas fa-tag me-1"></i>' + news.category_name + '</span><span class="badge bg-info bg-opacity-10 text-info border-0"><i class="fas fa-eye me-1"></i>' + news.views_count + '</span></div>';
            html += '   <div class="news-content-collapse text-truncate" style="max-width: 300px;">' + news.content + '</div>';
            html += '</td>';
            html += '<td class="align-middle col-toggle-3">' + toggleSwitch + '</td>';
            html += '<td class="align-middle col-toggle-4">' + actionsHtml + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-news-tbody').html(html);
}

function renderNewsGrid(data) {
    let html = '';
    if (data.length === 0) {
        html = '<div class="col-12 text-center py-4 text-muted">' + window.NewsConfig.trans.noRecords + '</div>';
    } else {
        data.forEach(news => {
            let imageHtml = news.image_url ? `<img src="${news.image_url}" class="news-grid-img">` : `<div class="bg-light text-muted d-flex align-items-center justify-content-center news-grid-img">No Image</div>`;

            html += `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm user-grid-card position-relative overflow-hidden">
                    <div class="col-toggle-1">
                        ${imageHtml}
                    </div>
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <div class="d-flex justify-content-between align-items-start mb-3 col-toggle-3">
                            <span class="badge ${news.is_active ? 'bg-success' : 'bg-secondary'} bg-opacity-10 ${news.is_active ? 'text-success' : 'text-secondary'} px-3 py-1 border-0">
                                ${news.is_active ? __('Active', 'نشط') : __('Inactive', 'غير نشط')}
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="toggleStatus(${news.id})" ${news.is_active ? 'checked' : ''}>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-2 text-dark col-toggle-2">${news.is_featured ? '<i class="fas fa-star text-warning me-1" title="' + __('Featured', 'مميز') + '"></i> ' : ''}${news.title}</h6>
                        <div class="mb-2 d-flex flex-wrap gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border-0"><i class="fas fa-tag me-1"></i>${news.category_name}</span>
                            <span class="badge bg-info bg-opacity-10 text-info border-0" title="${__('Views', 'المشاهدات')}"><i class="fas fa-eye me-1"></i>${news.views_count}</span>
                        </div>
                        <p class="text-muted small flex-grow-1 col-toggle-2" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${news.content}</p>
                        
                        <div class="d-flex gap-2 justify-content-center mt-3 pt-3 border-top col-toggle-4">
                            <a href="${window.NewsConfig.urls.edit.replace(':id', news.id)}" class="btn btn-sm btn-outline-primary px-3 flex-grow-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                ${__('Edit', 'تعديل')}
                            </a>
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="deleteNews(${news.id})">
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

    let infoHtml = '<div class="text-muted small">' + window.NewsConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.NewsConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.NewsConfig.trans.of + ' ' + pagination.total + ' ' + window.NewsConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchNews(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.deleteNews = function(id) {
    Swal.fire({
        title: window.NewsConfig.trans.deleteNews,
        text: window.NewsConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.NewsConfig.trans.yesDelete,
        cancelButtonText: window.NewsConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.NewsConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: window.NewsConfig.csrf },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchNews(1);
                    } else {
                        toastr.error(res.message || window.NewsConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.NewsConfig.trans.unexpectedError);
                }
            });
        }
    });
};

window.toggleStatus = function(id) {
    let url = window.NewsConfig.urls.toggleActive.replace(':id', id);
    $.ajax({
        url: url,
        method: 'POST',
        data: { _token: window.NewsConfig.csrf },
        success: function(res) {
            if (res.success) {
                toastr.success(res.message);
                fetchNews(1);
            } else {
                toastr.error(res.message || window.NewsConfig.trans.unexpectedError);
                fetchNews(1); 
            }
        },
        error: function() {
            toastr.error(window.NewsConfig.trans.unexpectedError);
            fetchNews(1); 
        }
    });
};
