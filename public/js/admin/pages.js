$(document).ready(function () {
    // Basic setup from global PageConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.PageConfig.csrf
        }
    });

    // Initial fetch
    fetchPages(1);

    // Filter bindings
    let searchTimeout;
    $('#filter_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchPages(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchPages(1);
        }
    });

    $('#filter_status').on('change', function() {
        fetchPages(1);
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
        localStorage.setItem('pages_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });

    window.applyColumnVisibility = function() {
        let savedVis = localStorage.getItem('pages_col_visibility');
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
    };

    // Initialize Summernote
    function initSummernote() {
        let config = {
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        };
        $('#content_ar, #content_en, #edit_content_ar, #edit_content_en').summernote(config);
    }
    initSummernote();

    // Add Page Form Submit
    $('#addPageForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        WJHTAKAdmin.btnLoading(btn, true);

        $.ajax({
            url: window.PageConfig.urls.store,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#addPageModal').modal('hide');
                    $('#addPageForm')[0].reset();
                    $('#content_ar').summernote('code', '');
                    $('#content_en').summernote('code', '');
                    fetchPages(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.PageConfig.trans.unexpectedError);
                }
            }
        });
    });

    // Edit Page Form Submit
    $('#editPageForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        WJHTAKAdmin.btnLoading(btn, true);

        let id = $('#edit_page_id').val();
        let url = window.PageConfig.urls.update.replace(':id', id);

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(res) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#editPageModal').modal('hide');
                    fetchPages(1);
                }
            },
            error: function(err) {
                WJHTAKAdmin.btnLoading(btn, false);
                if (err.responseJSON && err.responseJSON.errors) {
                    let msg = '';
                    Object.values(err.responseJSON.errors).forEach(e => msg += e[0] + '<br>');
                    toastr.error(msg);
                } else {
                    toastr.error(window.PageConfig.trans.unexpectedError);
                }
            }
        });
    });
});

window.fetchPages = function(page) {
    let perPage = $('#filter_per_page').val();
    let search = $('#filter_search').val();
    let status = $('#filter_status').val();

    $('#custom-pages-tbody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">' + window.PageConfig.trans.loading + '</div></td></tr>');

    $.ajax({
        url: window.PageConfig.urls.data,
        data: {
            page: page,
            per_page: perPage,
            search: search,
            status: status
        },
        success: function(res) {
            if(res.success) {
                renderPagesTable(res.data);
                renderPagination(res.pagination);
                if (window.applyColumnVisibility) window.applyColumnVisibility();
            }
        },
        error: function() {
            $('#custom-pages-tbody').html('<tr><td colspan="6" class="text-center text-danger py-4">' + window.PageConfig.trans.errorLoading + '</td></tr>');
        }
    });
};

function renderPagesTable(data) {
    let html = '';
    if (data.length === 0) {
        html = '<tr><td colspan="6" class="text-center py-4 text-muted">' + window.PageConfig.trans.noRecords + '</td></tr>';
    } else {
        data.forEach(page => {
            html += '<tr>';
            html += '<td class="align-middle col-toggle-0">' + page.title_ar + '</td>';
            html += '<td class="align-middle col-toggle-1">' + page.title_en + '</td>';
            html += '<td class="align-middle col-toggle-2">' + page.slug + '</td>';
            html += '<td class="align-middle col-toggle-3">' + page.is_active + '</td>';
            html += '<td class="align-middle col-toggle-4">' + page.show_in_footer + '</td>';
            html += '<td class="align-middle text-center col-toggle-5">' + page.actions + '</td>';
            html += '</tr>';
        });
    }
    $('#custom-pages-tbody').html(html);
}

function renderPagination(pagination) {
    let container = $('#custom-pagination');
    container.empty();

    if (pagination.total === 0) return;

    let infoHtml = '<div class="text-muted small">' + window.PageConfig.trans.showing + ' ' + 
                   ((pagination.current_page - 1) * $('#filter_per_page').val() + 1) + ' ' + 
                   window.PageConfig.trans.to + ' ' + 
                   Math.min(pagination.current_page * $('#filter_per_page').val(), pagination.total) + ' ' + 
                   window.PageConfig.trans.of + ' ' + pagination.total + ' ' + window.PageConfig.trans.entries + '</div>';

    let ul = '<ul class="pagination custom-pagination mb-0">';
    
    pagination.links.forEach(link => {
        if (link.url === null) {
            ul += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
        } else {
            let activeClass = link.active ? 'active' : '';
            let pageNumMatch = link.url.match(/page=(\d+)/);
            let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
            ul += '<li class="page-item ' + activeClass + '"><button class="page-link" onclick="fetchPages(' + pageNum + ')">' + link.label + '</button></li>';
        }
    });
    
    ul += '</ul>';

    container.html(infoHtml + ul);
}

window.editPage = function(id) {
    let url = window.PageConfig.urls.show.replace(':id', id);
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                $('#edit_page_id').val(res.page.id);
                $('#edit_title_ar').val(res.page.title_ar);
                $('#edit_title_en').val(res.page.title_en);
                $('#edit_slug').val(res.page.slug);
                
                $('#edit_content_ar').summernote('code', res.page.content_ar);
                $('#edit_content_en').summernote('code', res.page.content_en);
                
                $('#edit_is_active').prop('checked', res.page.is_active == 1);
                $('#edit_show_in_footer').prop('checked', res.page.show_in_footer == 1);
                
                $('#editPageModal').modal('show');
            }
        },
        error: function() {
            toastr.error(window.PageConfig.trans.errorLoading);
        }
    });
};

window.deletePage = function(id) {
    Swal.fire({
        title: window.PageConfig.trans.deletePage,
        text: window.PageConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: window.PageConfig.trans.yesDelete,
        cancelButtonText: window.PageConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            let url = window.PageConfig.urls.destroy.replace(':id', id);
            $.ajax({
                url: url,
                method: 'DELETE',
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        fetchPages(1);
                    } else {
                        toastr.error(res.message || window.PageConfig.trans.unexpectedError);
                    }
                },
                error: function(err) {
                    toastr.error(err.responseJSON?.message || window.PageConfig.trans.unexpectedError);
                }
            });
        }
    });
};
