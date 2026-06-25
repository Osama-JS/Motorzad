$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.WithdrawalConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('withdrawals_view_mode') || 'table';

    window.fetchWithdrawals = function(page = 1) {
        currentPage = page;
        $('#custom-withdrawals-tbody').html('<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.WithdrawalConfig.trans.loading + '</td></tr>');
        
        let search = $('#filter_search').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.WithdrawalConfig.urls.data,
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
                    applyColumnVisibility();
                } else {
                    $('#custom-withdrawals-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.WithdrawalConfig.trans.errorLoading + '</td></tr>');
                }
            },
            error: function() {
                $('#custom-withdrawals-tbody').html('<tr><td colspan="7" class="text-center py-4 text-danger">' + window.WithdrawalConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.WithdrawalConfig.trans.errorLoading + '</div>');
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
        localStorage.setItem('withdrawals_view_mode', mode);
        
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
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.WithdrawalConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(withdrawal => {
            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card border border-light bg-white shadow-sm rounded p-3 h-100 position-relative">
                        <div class="position-absolute top-0 end-0 p-3 col-toggle-6">
                            ${withdrawal.actions}
                        </div>
                        <div class="card-info mb-3 pe-5">
                            <strong class="d-block mb-1 col-toggle-0">#${withdrawal.id}</strong>
                            <div class="mb-2 col-toggle-1">${withdrawal.user}</div>
                        </div>
                        <div class="card-details border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 col-toggle-4">
                                <span class="text-muted small">${window.WithdrawalConfig.trans.status || 'Status'}:</span>
                                <span>${withdrawal.status}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-2">
                                <span class="text-muted small">${window.WithdrawalConfig.trans.requestedAmount || 'Requested Amount'}:</span>
                                <span class="fw-medium small">${withdrawal.requested_amount} SAR</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-3">
                                <span class="text-muted small">${window.WithdrawalConfig.trans.approvedAmount || 'Approved Amount'}:</span>
                                <span class="fw-medium small">${withdrawal.approved_amount} ${withdrawal.approved_amount !== '---' ? 'SAR' : ''}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 col-toggle-5">
                                <span class="text-muted small">${window.WithdrawalConfig.trans.date || 'Date'}:</span>
                                <span class="fw-medium small">${withdrawal.created_at}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-withdrawals-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' + window.WithdrawalConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(withdrawal => {
            let tr = `
                <tr>
                    <td class="col-toggle-0">${withdrawal.id}</td>
                    <td class="col-toggle-1">${withdrawal.user}</td>
                    <td class="col-toggle-2">${withdrawal.requested_amount} SAR</td>
                    <td class="col-toggle-3">${withdrawal.approved_amount} ${withdrawal.approved_amount !== '---' ? 'SAR' : ''}</td>
                    <td class="col-toggle-4">${withdrawal.status}</td>
                    <td class="col-toggle-5">${withdrawal.created_at}</td>
                    <td class="col-toggle-6">${withdrawal.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (!pagination || pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.WithdrawalConfig.trans.showing} ${(pagination.current_page - 1) * parseInt($('#filter_per_page').val() || 10) + 1} ${window.WithdrawalConfig.trans.to} ${Math.min(pagination.current_page * parseInt($('#filter_per_page').val() || 10), pagination.total)} ${window.WithdrawalConfig.trans.of} ${pagination.total} ${window.WithdrawalConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchWithdrawals(${pageNum})">${link.label}</button></li>`;
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

    // Handle Column Toggle check/uncheck
    $('.col-toggle').on('change', function() {
        let visArray = [];
        $('.col-toggle:checked').each(function() {
            visArray.push($(this).val());
        });
        localStorage.setItem('withdrawals_col_visibility', JSON.stringify(visArray));
        applyColumnVisibility();
    });

    function applyColumnVisibility() {
        let savedVis = localStorage.getItem('withdrawals_col_visibility');
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

    // Bind filters
    $('#filter_status').on('change', function() {
        fetchWithdrawals(1);
    });

    $('#btn-filter').on('click', function() {
        fetchWithdrawals(1);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchWithdrawals(1);
        }
    });

    // Initialize View Mode
    toggleView(currentView);
    fetchWithdrawals(1);

    // Modal view & process
    window.openWithdrawalModal = function(id) {
        window.currentWithdrawalId = id;
        const baseUrl = window.WithdrawalConfig.urls.details.replace(':id', id);
        
        // Reset Form
        $('#withdrawalForm')[0].reset();
        
        // Fetch Details
        $.get(baseUrl, function(response) {
            if(response.data) {
                const data = response.data;
                
                $('#view_user_name').text(data.user ? data.user.name : '---');
                $('#view_requested_amount').text(parseFloat(data.requested_amount).toFixed(2));
                $('#view_status').text(data.status);
                $('#view_date').text(new Date(data.created_at).toLocaleString());
                
                $('#form_status').val(data.status);
                $('#form_approved_amount').val(data.approved_amount || data.requested_amount);
                $('#form_payment_method').val(data.payment_method || '');
                $('#form_admin_notes').val(data.admin_notes || '');
                
                $('#withdrawalModal').modal('show');
            }
        }).fail(function() {
            Swal.fire(window.WithdrawalConfig.trans.error, window.WithdrawalConfig.trans.detailsLoadFailed, 'error');
        });
    };

    // Handle Form Submit
    $('#withdrawalForm').on('submit', function(e) {
        e.preventDefault();
        if(!window.currentWithdrawalId) return;

        const url = window.WithdrawalConfig.urls.process.replace(':id', window.currentWithdrawalId);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#withdrawalModal').modal('hide');
                    fetchWithdrawals(currentPage);
                    Swal.fire(window.WithdrawalConfig.trans.success, response.message, 'success').then(() => {
                        window.location.reload(); // Refresh to update top stats
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = window.WithdrawalConfig.trans.operationFailed;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire(window.WithdrawalConfig.trans.error, errorMessage, 'error');
            }
        });
    });
});
