/**
 * Auctions Management - DataViews Architecture
 */

const WJHTAKAdmin = {
    currentView: localStorage.getItem('auctions_view_preference') || 'table',
    
    init: function() {
        this.setupEventListeners();
        this.applyViewPreference();
        this.initColumnVisibility();
    },

    setupEventListeners: function() {
        // Search & Filter (Debounced)
        let debounceTimer;
        document.getElementById('filter_search')?.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchAuctions(1), 500);
        });

        // Dropdowns don't close when clicking checkboxes
        document.querySelectorAll('.dropdown-menu .form-check-input').forEach(checkbox => {
            checkbox.addEventListener('click', e => e.stopPropagation());
            checkbox.addEventListener('change', e => {
                this.toggleColumn(e.target.value, e.target.checked);
            });
        });
    },

    toggleView: function(viewMode) {
        this.currentView = viewMode;
        localStorage.setItem('auctions_view_preference', viewMode);
        this.applyViewPreference();
    },

    applyViewPreference: function() {
        const tableBtn = document.querySelector('.table-view-btn');
        const gridBtn = document.querySelector('.grid-view-btn');
        const tableContainer = document.getElementById('table-view-container');
        const gridContainer = document.getElementById('grid-view-container');

        if (!tableBtn || !gridBtn) return;

        if (this.currentView === 'grid') {
            tableBtn.classList.remove('active');
            gridBtn.classList.add('active');
            tableContainer.classList.add('d-none');
            gridContainer.classList.remove('d-none');
        } else {
            gridBtn.classList.remove('active');
            tableBtn.classList.add('active');
            gridContainer.classList.add('d-none');
            tableContainer.classList.remove('d-none');
        }
    },

    initColumnVisibility: function() {
        const savedPrefs = localStorage.getItem('auctions_cols_preference');
        if (savedPrefs) {
            const prefs = JSON.parse(savedPrefs);
            document.querySelectorAll('.col-toggle').forEach(checkbox => {
                const colIndex = checkbox.value;
                if (prefs[colIndex] !== undefined && !checkbox.disabled) {
                    checkbox.checked = prefs[colIndex];
                    this.toggleColumn(colIndex, prefs[colIndex], false);
                }
            });
        }
    },

    toggleColumn: function(colIndex, isVisible, save = true) {
        const table = document.getElementById('auctions-custom-table');
        if (!table) return;

        // Toggle Th
        const th = table.querySelector(`th[data-col="${colIndex}"]`);
        if (th) th.style.display = isVisible ? '' : 'none';

        // Toggle Td
        const tbody = document.getElementById('custom-auctions-tbody');
        const trs = tbody.querySelectorAll('tr');
        trs.forEach(tr => {
            const td = tr.children[colIndex];
            if (td) td.style.display = isVisible ? '' : 'none';
        });

        if (save) {
            let prefs = JSON.parse(localStorage.getItem('auctions_cols_preference') || '{}');
            prefs[colIndex] = isVisible;
            localStorage.setItem('auctions_cols_preference', JSON.stringify(prefs));
        }
    }
};

let currentAuctionsData = [];

function fetchAuctions(page = 1) {
    const tbody = document.getElementById('custom-auctions-tbody');
    const grid = document.getElementById('custom-auctions-grid');
    
    if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">${window.AuctionConfig.trans.loading}</div></td></tr>`;
    if (grid) grid.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">${window.AuctionConfig.trans.loading}</div></div>`;

    const searchQuery = document.getElementById('filter_search')?.value || '';
    const statusQuery = document.getElementById('filter_status')?.value || '';
    const perPage = document.getElementById('filter_per_page')?.value || 10;

    const url = new URL(window.AuctionConfig.urls.data);
    url.searchParams.append('page', page);
    url.searchParams.append('search', searchQuery);
    url.searchParams.append('status', statusQuery);
    url.searchParams.append('per_page', perPage);

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            currentAuctionsData = response.data;
            renderTable(response.data);
            renderGrid(response.data);
            renderPagination(response.pagination);
            WJHTAKAdmin.initColumnVisibility(); // Re-apply column visibility to new rows
        } else {
            toastr.error(window.AuctionConfig.trans.errorLoading);
        }
    })
    .catch(err => {
        console.error(err);
        toastr.error(window.AuctionConfig.trans.unexpectedError);
    });
}

function renderTable(data) {
    const tbody = document.getElementById('custom-auctions-tbody');
    if (!tbody) return;

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">${window.AuctionConfig.trans.noRecords}</td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        html += `
            <tr id="auction-row-${item.id}">
                <td>${item.image}</td>
                <td>${item.title}</td>
                <td><span class="text-muted fw-semibold">${item.vehicle}</span></td>
                <td>${item.start_price}</td>
                <td>${item.status}</td>
                <td>${item.start_time}</td>
                <td>${item.end_time}</td>
                <td class="text-center">${item.actions}</td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

function renderGrid(data) {
    const grid = document.getElementById('custom-auctions-grid');
    if (!grid) return;

    if (data.length === 0) {
        grid.innerHTML = `<div class="col-12 text-center py-4 text-muted">${window.AuctionConfig.trans.noRecords}</div>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        html += `
            <div class="col-12 col-md-6 col-xl-4 col-xxl-3" id="auction-card-${item.id}">
                <div class="data-view-card h-100 d-flex flex-column">
                    <div class="position-relative">
                        ${item.image_url ? `<img src="${item.image_url}" class="card-img-top" alt="">` : `<div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-car fa-3x"></i></div>`}
                        <div class="position-absolute top-0 end-0 p-2">
                            ${item.status}
                        </div>
                    </div>
                    <div class="card-body flex-grow-1">
                        <h5 class="card-title text-truncate mb-1" title="${item.raw_title}">${item.raw_title}</h5>
                        <p class="text-muted small mb-3"><i class="fa-solid fa-car me-1"></i> ${item.vehicle}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted small">Start Price:</span>
                            <span>${item.start_price}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span class="text-muted"><i class="fa-regular fa-clock me-1"></i> Start:</span>
                            <span>${item.start_time}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted"><i class="fa-solid fa-hourglass-end me-1"></i> End:</span>
                            <span>${item.end_time}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 p-3 pt-0 text-center">
                        ${item.actions}
                    </div>
                </div>
            </div>
        `;
    });
    grid.innerHTML = html;
}

function renderPagination(pagination) {
    const container = document.getElementById('custom-pagination');
    if (!container) return;

    if (pagination.total === 0) {
        container.innerHTML = '';
        return;
    }

    const perPage = parseInt(document.getElementById('filter_per_page')?.value || 10);
    const start = (pagination.current_page - 1) * perPage + 1;
    const end = Math.min(pagination.current_page * perPage, pagination.total);

    let infoHtml = `<div class="text-muted small">${window.AuctionConfig.trans.showing} ${start} ${window.AuctionConfig.trans.to} ${end} ${window.AuctionConfig.trans.of} ${pagination.total} ${window.AuctionConfig.trans.entries}</div>`;
    
    let linksHtml = '<ul class="pagination custom-pagination mb-0">';
    pagination.links.forEach(link => {
        if (link.url === null) {
            linksHtml += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
        } else {
            const activeClass = link.active ? 'active' : '';
            // Extract page number from URL safely
            let pageNum = 1;
            try {
                const urlObj = new URL(link.url);
                pageNum = urlObj.searchParams.get('page') || 1;
            } catch(e) {
                const match = link.url.match(/page=(\d+)/);
                if(match) pageNum = match[1];
            }
            
            linksHtml += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchAuctions(${pageNum})">${link.label}</button></li>`;
        }
    });
    linksHtml += '</ul>';

    container.innerHTML = infoHtml + linksHtml;
}

function deleteAuction(id) {
    let url = window.AuctionConfig.urls.destroy.replace(':id', id);
    
    Swal.fire({
        title: window.AuctionConfig.trans.deleteTitle,
        text: window.AuctionConfig.trans.deleteDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: window.AuctionConfig.trans.yesDelete,
        cancelButtonText: window.AuctionConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.AuctionConfig.csrf,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    toastr.success(response.message);
                    fetchAuctions(1); // Reload data
                } else {
                    toastr.error(response.message || 'Error deleting auction.');
                }
            })
            .catch(err => {
                console.error(err);
                toastr.error(window.AuctionConfig.trans.unexpectedError);
            });
        }
    });
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    WJHTAKAdmin.init();
    fetchAuctions(1);
    
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
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        initSelect2();
    }
        
        // Listen to select2 changes for the filters
        $('#filter_status, #filter_per_page').on('change', function() {
            fetchAuctions(1);
        });
});
