$(document).ready(function() {
    // Basic setup from global BidConfig
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.BidConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('bids_view_mode') || 'table';

    window.fetchBids = function(page = 1) {
        currentPage = page;
        
        let loadingTableHtml = '<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.BidConfig.trans.loading + '</td></tr>';
        let loadingGridHtml = '<div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.BidConfig.trans.loading + '</div>';
        
        $('#custom-bids-tbody').html(loadingTableHtml);
        $('#grid-view-container').html(loadingGridHtml);
        
        let search = $('#filter_search').val();
        let type = $('#filter_type').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.BidConfig.urls.data,
            data: {
                page: page,
                search: search,
                type: type,
                status: status,
                per_page: perPage
            },
            success: function(response) {
                if (response.success) {
                    currentData = response.data;
                    renderCurrentView();
                    renderPagination(response.pagination);
                }
            },
            error: function() {
                let errorTableHtml = '<tr><td colspan="7" class="text-center py-4 text-danger">' + window.BidConfig.trans.errorLoading + '</td></tr>';
                let errorGridHtml = '<div class="col-12 text-center py-4 text-danger">' + window.BidConfig.trans.errorLoading + '</div>';
                $('#custom-bids-tbody').html(errorTableHtml);
                $('#grid-view-container').html(errorGridHtml);
            }
        });
    };

    function renderCurrentView() {
        if (currentView === 'table') {
            renderTable(currentData);
        } else {
            renderGrid(currentData);
        }
        applyColumnVisibility();
    }

    window.toggleView = function(mode) {
        currentView = mode;
        localStorage.setItem('bids_view_mode', mode);
        
        if (mode === 'table') {
            $('#table-view-container').removeClass('d-none');
            $('#grid-view-container').addClass('d-none');
            $('#btn-view-table').addClass('active');
            $('#btn-view-grid').removeClass('active');
        } else {
            $('#table-view-container').addClass('d-none');
            $('#grid-view-container').removeClass('d-none');
            $('#btn-view-table').removeClass('active');
            $('#btn-view-grid').addClass('active');
        }
        
        renderCurrentView();
    };

    function renderTable(data) {
        let tbody = $('#custom-bids-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' + window.BidConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(bid => {
            let tr = `
                <tr>
                    <td class="col-toggle-0">${bid.user}</td>
                    <td class="col-toggle-1">${bid.auction}</td>
                    <td class="col-toggle-2">${bid.amount}</td>
                    <td class="col-toggle-3">${bid.type}</td>
                    <td class="col-toggle-4">${bid.status_badge}</td>
                    <td class="col-toggle-5">${bid.time}</td>
                    <td class="col-toggle-6">${bid.ip}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderGrid(data) {
        let container = $('#grid-view-container');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.BidConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(bid => {
            // Badges & styling for Grid cards
            let typeBadgeHtml = bid.is_auto_bid 
                ? `<span class="badge" style="background:#faf5ff; color:#a855f7; border:1px solid #f3e8ff; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-robot"></i> ${__('Auto', 'تلقائي')}</span>`
                : `<span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #dcfce7; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-user"></i> ${__('Manual', 'يدوي')}</span>`;

            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card shadow-sm h-100 p-3 border-0 d-flex flex-column justify-content-between" style="background: var(--glass-bg); border-radius:15px; position:relative;">
                        <div>
                            <!-- User header details -->
                            <div class="d-flex align-items-center gap-2 mb-3 col-toggle-0">
                                <div class="d-inline-flex align-items-center justify-content-center bg-light text-dark rounded-circle font-weight-bold" style="width:40px; height:40px; font-size:1rem;">
                                    ${bid.user_avatar_initial}
                                </div>
                                <div style="line-height: 1.2;">
                                    <strong class="text-dark d-block" style="font-size:0.9rem;">${bid.user_name}</strong>
                                    <small class="text-muted" style="font-size:0.75rem;">${bid.user_email}</small>
                                </div>
                            </div>

                            <!-- Auction details -->
                            <div class="mb-3 col-toggle-1 pt-2 border-top">
                                <span class="text-muted d-block small" style="font-size:0.75rem;">${window.BidConfig.trans.auctionVehicle}:</span>
                                <a href="${bid.auction_url}" class="text-decoration-none font-weight-bold text-dark d-block" style="font-size:0.85rem;">
                                    ${bid.auction_title}
                                </a>
                                <small class="text-muted d-block">${bid.vehicle_title}</small>
                            </div>

                            <!-- Bid Amount details -->
                            <div class="d-flex justify-content-between align-items-center mb-2 col-toggle-2">
                                <span class="text-muted small">${window.BidConfig.trans.amount}:</span>
                                <strong class="text-primary" style="font-size:1.1rem;">${bid.amount_formatted}</strong>
                            </div>

                            <!-- Type details -->
                            <div class="d-flex justify-content-between align-items-center mb-2 col-toggle-3">
                                <span class="text-muted small">${window.BidConfig.trans.type}:</span>
                                <div>${typeBadgeHtml}</div>
                            </div>

                            <!-- Status details -->
                            <div class="d-flex justify-content-between align-items-center mb-2 col-toggle-4">
                                <span class="text-muted small">${window.BidConfig.trans.status}:</span>
                                <div>${bid.status_badge}</div>
                            </div>
                        </div>

                        <!-- Date/Time & IP footer -->
                        <div class="pt-3 border-top mt-3" style="font-size: 0.75rem; line-height: 1.3;">
                            <div class="d-flex justify-content-between col-toggle-5 mb-1">
                                <span class="text-muted">${__('Time', 'التوقيت')}:</span>
                                <span class="text-dark font-weight-bold" title="${bid.time_formatted}">${bid.time_diff}</span>
                            </div>
                            <div class="d-flex justify-content-between col-toggle-6">
                                <span class="text-muted">${__('IP Address', 'عنوان IP')}:</span>
                                <code class="text-dark">${bid.ip_address}</code>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (pagination.total === 0) return;

        let perPage = parseInt($('#filter_per_page').val());
        let fromEntry = (pagination.current_page - 1) * perPage + 1;
        let toEntry = Math.min(pagination.current_page * perPage, pagination.total);

        let info = `<div class="text-muted small">${window.BidConfig.trans.showing} ${fromEntry} ${window.BidConfig.trans.to} ${toEntry} ${window.BidConfig.trans.of} ${pagination.total} ${window.BidConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchBids(${pageNum})">${link.label}</button></li>`;
            }
        });
        
        ul += `</ul>`;

        container.html(info + ul);
    }

    function applyColumnVisibility() {
        let savedVis = localStorage.getItem('bids_col_visibility');
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

    // Helper translation function for JS side rendering
    function __(en, ar) {
        let dir = $('html').attr('dir') || 'rtl';
        return dir === 'rtl' ? (ar || en) : en;
    }

    // Initialize View Mode
    toggleView(currentView);

    // Handle Column Toggle check/uncheck
    $('.col-toggle').on('change', function() {
        let visArray = [];
        $('.col-toggle:checked').each(function() {
            visArray.push($(this).val());
        });
        localStorage.setItem('bids_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
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

    // Filters event listeners
    let searchTimeout;
    $('#filter_search').on('keyup input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchBids(1), 500);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchBids(1);
        }
    });

    $('#filter_type, #filter_status').on('change', function() {
        fetchBids(1);
    });

    $('#btn-filter').on('click', function() {
        fetchBids(1);
    });
    
    // Initial fetch
    fetchBids(1);
});
