$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.BankAccountConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('bank_accounts_view_mode') || 'table';

    window.fetchAccounts = function(page = 1) {
        currentPage = page;
        $('#custom-bank-accounts-tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.BankAccountConfig.trans.loading + '</td></tr>');
        
        let search = $('#filter_search').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.BankAccountConfig.urls.data,
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
                    $('#custom-bank-accounts-tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">' + window.BankAccountConfig.trans.errorLoading + '</td></tr>');
                }
            },
            error: function() {
                $('#custom-bank-accounts-tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">' + window.BankAccountConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.BankAccountConfig.trans.errorLoading + '</div>');
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
        localStorage.setItem('bank_accounts_view_mode', mode);
        
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
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.BankAccountConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(account => {
            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card border border-light bg-white shadow-sm rounded p-3 h-100 position-relative">
                        <div class="position-absolute top-0 end-0 p-3">
                            ${account.actions}
                        </div>
                        <div class="text-center mb-3">
                            ${account.logo}
                        </div>
                        <div class="card-info text-center mb-3">
                            <strong class="d-block mb-1">${account.bank_name}</strong>
                            <small class="text-muted d-block" dir="ltr">${account.iban}</small>
                        </div>
                        <div class="card-details border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">${window.BankAccountConfig.trans.beneficiary || 'Beneficiary'}:</span>
                                <span class="fw-medium small">${account.beneficiary_name}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">${window.BankAccountConfig.trans.status || 'Status'}:</span>
                                <span>${account.status}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-bank-accounts-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">' + window.BankAccountConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(account => {
            let tr = `
                <tr>
                    <td class="text-center">${account.logo}</td>
                    <td>${account.bank_name}</td>
                    <td>${account.iban}</td>
                    <td>${account.beneficiary_name}</td>
                    <td class="text-center">${account.status}</td>
                    <td class="text-center">${account.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (!pagination || pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.BankAccountConfig.trans.showing} ${(pagination.current_page - 1) * parseInt($('#filter_per_page').val() || 10) + 1} ${window.BankAccountConfig.trans.to} ${Math.min(pagination.current_page * parseInt($('#filter_per_page').val() || 10), pagination.total)} ${window.BankAccountConfig.trans.of} ${pagination.total} ${window.BankAccountConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchAccounts(${pageNum})">${link.label}</button></li>`;
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
        fetchAccounts(1);
    });

    $('#btn-filter').on('click', function() {
        fetchAccounts(1);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchAccounts(1);
        }
    });

    // Initialize View Mode
    toggleView(currentView);
    fetchAccounts(1);

    // Modal Add / Edit
    window.openAddModal = function() {
        $('#bankAccountForm')[0].reset();
        $('#account_id').val('');
        $('#modal-title-text').text(window.BankAccountConfig.trans.addTitle);
        $('#logo-preview').hide();
        $('#bankAccountModal').modal('show');
    };

    window.editAccount = function(id) {
        let url = window.BankAccountConfig.urls.edit.replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                let account = response.account;
                $('#account_id').val(account.id);
                $('#bank_name').val(account.bank_name);
                $('#iban').val(account.iban);
                $('#beneficiary_name').val(account.beneficiary_name);
                $('#is_active').prop('checked', account.is_active);
                
                if (response.logo_url) {
                    $('#current-logo').attr('src', response.logo_url);
                    $('#logo-preview').show();
                } else {
                    $('#logo-preview').hide();
                }
                
                $('#modal-title-text').text(window.BankAccountConfig.trans.editTitle);
                $('#bankAccountModal').modal('show');
            }
        });
    };

    window.toggleAccountStatus = function(id) {
        Swal.fire({
            title: window.BankAccountConfig.trans.areYouSure,
            text: window.BankAccountConfig.trans.confirmStatusChange,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.BankAccountConfig.trans.yesToggle,
            cancelButtonText: window.BankAccountConfig.trans.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                let url = window.BankAccountConfig.urls.toggle.replace(':id', id);
                $.post(url, function(response) {
                    if (response.success) {
                        fetchAccounts(currentPage);
                        Swal.fire('Updated!', response.message, 'success');
                    }
                });
            }
        });
    };

    window.deleteAccount = function(id) {
        Swal.fire({
            title: window.BankAccountConfig.trans.areYouSure,
            text: window.BankAccountConfig.trans.deleteDesc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.BankAccountConfig.trans.yesDelete,
            cancelButtonText: window.BankAccountConfig.trans.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                let url = window.BankAccountConfig.urls.destroy.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            fetchAccounts(currentPage);
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    }
                });
            }
        });
    };

    // Form submit
    $('#bankAccountForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $('#account_id').val();
        let url = id 
            ? window.BankAccountConfig.urls.update.replace(':id', id)
            : window.BankAccountConfig.urls.store;
        
        if (id) {
            formData.append('_method', 'PUT');
        }

        let $btn = $('#saveBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> ' + window.BankAccountConfig.trans.processing);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#bankAccountModal').modal('hide');
                    fetchAccounts(currentPage);
                    Swal.fire('Success', response.message, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    Object.keys(errors).forEach(key => {
                        errorMsg += errors[key][0] + '<br>';
                    });
                    Swal.fire('Validation Error', errorMsg, 'error');
                } else {
                    Swal.fire('Error', window.BankAccountConfig.trans.unexpectedError, 'error');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ' + window.BankAccountConfig.trans.saveChanges);
            }
        });
    });
});
