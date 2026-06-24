$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.DepositConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('deposits_view_mode') || 'table';

    window.fetchDeposits = function(page = 1) {
        currentPage = page;
        $('#custom-deposits-tbody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.DepositConfig.trans.loading + '</td></tr>');
        
        let search = $('#filter_search').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.DepositConfig.urls.data,
            data: {
                page: page,
                search: search,
                status: status,
                per_page: perPage
            },
            success: function(response) {
                if (response.success || response.data) {
                    currentData = response.data;
                    renderCurrentView();
                    renderPagination(response.pagination);
                } else {
                    $('#custom-deposits-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.DepositConfig.trans.errorLoading + '</td></tr>');
                }
            },
            error: function() {
                $('#custom-deposits-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.DepositConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.DepositConfig.trans.errorLoading + '</div>');
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

    window.toggleView = function(mode) {
        currentView = mode;
        localStorage.setItem('deposits_view_mode', mode);
        
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

    function renderGrid(data) {
        let container = $('#grid-view-container');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.DepositConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(deposit => {
            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card border border-light bg-white shadow-sm rounded p-3 h-100 position-relative">
                        <div class="position-absolute top-0 end-0 p-3">
                            ${deposit.actions}
                        </div>
                        <div class="card-info mb-3 pe-5">
                            <strong class="d-block mb-1">#${deposit.id}</strong>
                            <div class="mb-2">${deposit.user_name}</div>
                        </div>
                        <div class="card-details border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">حالة الطلب:</span>
                                <span>${deposit.status}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">المبلغ:</span>
                                <span class="fw-medium small text-success">${deposit.amount}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">البنك المحوّل إليه:</span>
                                <span class="fw-medium small">${deposit.bank_name}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">تاريخ الطلب:</span>
                                <span class="fw-medium small">${deposit.created_at}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-deposits-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' + window.DepositConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(deposit => {
            let tr = `
                <tr>
                    <td>${deposit.id}</td>
                    <td>${deposit.user_name}</td>
                    <td>${deposit.bank_name}</td>
                    <td>${deposit.amount}</td>
                    <td>${deposit.status}</td>
                    <td>${deposit.created_at}</td>
                    <td>${deposit.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (!pagination || pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.DepositConfig.trans.showing} ${(pagination.current_page - 1) * parseInt($('#filter_per_page').val() || 10) + 1} ${window.DepositConfig.trans.to} ${Math.min(pagination.current_page * parseInt($('#filter_per_page').val() || 10), pagination.total)} ${window.DepositConfig.trans.of} ${pagination.total} ${window.DepositConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchDeposits(${pageNum})">${link.label}</button></li>`;
            }
        });
        
        ul += `</ul>`;

        container.html(info + ul);
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

    // Bind filters
    $('#filter_status').on('change', function() {
        fetchDeposits(1);
    });

    $('#btn-filter').on('click', function() {
        fetchDeposits(1);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchDeposits(1);
        }
    });

    // Initialize View Mode
    toggleView(currentView);
    fetchDeposits(1);

    // Modal view & process
    window.openDepositModal = function(id) {
        window.currentDepositId = id;
        const baseUrl = window.DepositConfig.urls.details.replace(':id', id);
        
        // Reset Form
        $('#depositForm')[0].reset();
        
        // Fetch Details
        $.get(baseUrl, function(response) {
            if(response.data) {
                const data = response.data;
                
                $('#view_user_name').text(data.user ? data.user.full_name : '---');
                $('#view_user_email').text(data.user ? data.user.email : '');
                $('#view_requested_amount').text(parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#view_bank_name').text(data.bank_account ? data.bank_account.bank_name : '---');
                $('#view_date').text(new Date(data.created_at).toLocaleString());
                
                $('#form_status').val(data.status);
                $('#form_admin_note').val(data.admin_note || '');
                
                // Receipt path handling
                if (data.receipt_path) {
                    const fullUrl = window.DepositConfig.storageBaseUrl + '/' + data.receipt_path;
                    $('#view_receipt_img').attr('src', fullUrl);
                    $('#receipt_link').attr('href', fullUrl);
                    $('#receipt_container').show();
                    $('#no_receipt_msg').hide();
                } else {
                    $('#receipt_container').hide();
                    $('#no_receipt_msg').show();
                }
                
                // Disable form if already processed
                if(data.status !== 'pending') {
                    $('#depositForm button[type="submit"]').prop('disabled', true).text(window.DepositConfig.trans.alreadyProcessed);
                    $('#form_status').prop('disabled', true);
                    $('#form_admin_note').prop('disabled', true);
                } else {
                    $('#depositForm button[type="submit"]').prop('disabled', false).text(window.DepositConfig.trans.updateStatusBtn);
                    $('#form_status').prop('disabled', false);
                    $('#form_admin_note').prop('disabled', false);
                }

                $('#depositModal').modal('show');
            }
        }).fail(function() {
            Swal.fire(window.DepositConfig.trans.error, window.DepositConfig.trans.detailsLoadFailed, 'error');
        });
    };

    // Handle Form Submit
    $('#depositForm').on('submit', function(e) {
        e.preventDefault();
        if(!window.currentDepositId) return;

        const url = window.DepositConfig.urls.process.replace(':id', window.currentDepositId);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#depositModal').modal('hide');
                    fetchDeposits(currentPage);
                    Swal.fire(window.DepositConfig.trans.success, response.message, 'success').then(() => {
                        window.location.reload(); // Refresh to update top stats
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = window.DepositConfig.trans.operationFailed;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire(window.DepositConfig.trans.error, errorMessage, 'error');
            }
        });
    });
});
