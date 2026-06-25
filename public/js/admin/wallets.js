$(document).ready(function () {


    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('wallets_view_mode') || 'table';

    window.fetchWallets = function (page = 1) {
        currentPage = page;
        $('#custom-wallets-tbody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.WalletConfig.trans.loading + '</td></tr>');

        let search = $('#filter_search').val();
        let perPage = $('#filter_per_page').val();

        BidderAjax.get(window.WalletConfig.urls.data, {
            page: page,
            search: search,
            per_page: perPage
        }, {
            onSuccess: function (response) {
                if (response.success || response.data) {
                    currentData = response.data;
                    renderCurrentView();
                    renderPagination(response.pagination);
                    applyColumnVisibility();
                } else {
                    $('#custom-wallets-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.WalletConfig.trans.errorLoading + '</td></tr>');
                }
            },
            onError: function () {
                $('#custom-wallets-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.WalletConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.WalletConfig.trans.errorLoading + '</div>');
            }
        });
    };

    function renderCurrentView() {
        if (currentView === 'table') {
            renderTable(currentData);
        } else {
            renderGrid(currentData);
        }
    }

    window.toggleView = function (mode) {
        currentView = mode;
        localStorage.setItem('wallets_view_mode', mode);

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
        applyColumnVisibility();
    };

    function renderGrid(data) {
        let container = $('#grid-view-container');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.WalletConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(wallet => {
            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card border border-light bg-white shadow-sm rounded p-3 h-100 position-relative">
                        <div class="position-absolute top-0 end-0 p-3 col-toggle-6">
                            ${wallet.actions}
                        </div>
                        <div class="card-info mb-3 pe-5 col-toggle-0">
                            <div class="mb-2">${wallet.user}</div>
                        </div>
                        <div class="card-details border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 col-toggle-1">
                                <span class="text-muted small">${window.WalletConfig.trans.balance || 'Balance'}:</span>
                                <span>${wallet.balance}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-2">
                                <span class="text-muted small">${window.WalletConfig.trans.debtCeiling || 'Debt Ceiling'}:</span>
                                <span class="fw-medium small">${wallet.debt_ceiling}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-3">
                                <span class="text-muted small">${window.WalletConfig.trans.debtUsage || 'Debt Usage'}:</span>
                                <span class="fw-medium small">${wallet.debt_usage}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-4">
                                <span class="text-muted small">${window.WalletConfig.trans.totalDeposits || 'Total Deposits'}:</span>
                                <span class="fw-medium small">${wallet.total_deposits}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-5">
                                <span class="text-muted small">${window.WalletConfig.trans.totalWithdrawals || 'Total Withdrawals'}:</span>
                                <span class="fw-medium small">${wallet.total_withdrawals}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-wallets-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' + window.WalletConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(wallet => {
            let tr = `
                <tr>
                    <td class="col-toggle-0">${wallet.user}</td>
                    <td class="col-toggle-1">${wallet.balance}</td>
                    <td class="col-toggle-2">${wallet.debt_ceiling}</td>
                    <td class="col-toggle-3">${wallet.debt_usage}</td>
                    <td class="col-toggle-4">${wallet.total_deposits}</td>
                    <td class="col-toggle-5">${wallet.total_withdrawals}</td>
                    <td class="col-toggle-6">${wallet.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (!pagination || pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.WalletConfig.trans.showing} ${(pagination.current_page - 1) * parseInt($('#filter_per_page').val() || 10) + 1} ${window.WalletConfig.trans.to} ${Math.min(pagination.current_page * parseInt($('#filter_per_page').val() || 10), pagination.total)} ${window.WalletConfig.trans.of} ${pagination.total} ${window.WalletConfig.trans.entries}</div>`;

        let ul = `<ul class="pagination custom-pagination mb-0">`;

        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchWallets(${pageNum})">${link.label}</button></li>`;
            }
        });

        ul += `</ul>`;

        container.html(info + ul);
    }

    // Initialize Select2 globally
    function initSelect2() {
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-init').each(function () {
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
    $('.col-toggle').on('change', function () {
        let visArray = [];
        $('.col-toggle:checked').each(function () {
            visArray.push($(this).val());
        });
        localStorage.setItem('wallets_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });

    function applyColumnVisibility() {
        let savedVis = localStorage.getItem('wallets_col_visibility');
        if (savedVis) {
            let visArray = JSON.parse(savedVis);
            $('.col-toggle').each(function () {
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

    // Bind filters
    $('#btn-filter').on('click', function () {
        fetchWallets(1);
    });

    $('#filter_search').on('keypress', function (e) {
        if (e.which == 13) {
            fetchWallets(1);
        }
    });

    // Initialize View Mode
    toggleView(currentView);
    fetchWallets(1);

    // Modal Debt Ceiling
    window.openDebtModal = function (id, currentCeiling) {
        $('#debt_wallet_id').val(id);
        $('#debt_ceiling_input').val(currentCeiling);
        $('#debtModal').modal('show');
    };

    // Handle Debt Form Submit
    $('#debtForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#debt_wallet_id').val();
        const url = window.WalletConfig.urls.debtCeiling.replace(':id', id);

        BidderAjax.post(url, $(this).serialize(), {
            onSuccess: function (response) {
                if (response.success) {
                    $('#debtModal').modal('hide');
                    fetchWallets(currentPage);
                    Swal.fire(window.WalletConfig.trans.success, response.message, 'success');
                }
            }
        });
    });
});
