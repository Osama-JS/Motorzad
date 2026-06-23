$(document).ready(function() {
    // Basic setup from global WJHTAKAdmin
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.UserConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('users_view_mode') || 'table';

    window.fetchUsers = function(page = 1) {
        currentPage = page;
        $('#custom-users-tbody').html('<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.UserConfig.trans.loading + '</td></tr>');
        $('#grid-view-container').html('<div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.UserConfig.trans.loading + '</div>');
        
        let search = $('#filter_search').val();
        let role = $('#filter_role').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.UserConfig.urls.data,
            data: {
                page: page,
                search: search,
                role: role,
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
                $('#custom-users-tbody').html('<tr><td colspan="8" class="text-center py-4 text-danger">' + window.UserConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.UserConfig.trans.errorLoading + '</div>');
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
        localStorage.setItem('users_view_mode', mode);
        
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
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.UserConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(user => {
            let imgSrcMatch = user.photo.match(/src="([^"]+)"/);
            let imgSrc = imgSrcMatch ? imgSrcMatch[1] : '';
            
            let nameMatch = user.info.match(/<strong>(.*?)<\/strong>/);
            let name = nameMatch ? nameMatch[1] : '';
            let emailMatch = user.info.match(/<small[^>]*>(.*?)<\/small>/);
            let email = emailMatch ? emailMatch[1] : '';

            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="user-grid-card">
                        <div class="action-menu">
                            ${user.actions}
                        </div>
                        <img src="${imgSrc}" class="card-avatar" alt="${name}">
                        <div class="card-info">
                            <strong>${name}</strong>
                            <small>${email}</small>
                        </div>
                        <div class="card-details">
                            <div class="detail-row col-toggle-2">
                                <span class="detail-label">${window.UserConfig.trans.phone}:</span>
                                <span>${user.phone}</span>
                            </div>
                            <div class="detail-row col-toggle-3">
                                <span class="detail-label">${window.UserConfig.trans.roles}:</span>
                                <span>${user.roles}</span>
                            </div>
                            <div class="detail-row col-toggle-4">
                                <span class="detail-label">KYC:</span>
                                <span>${user.kyc_level}</span>
                            </div>
                            <div class="detail-row col-toggle-5">
                                <span class="detail-label">${window.UserConfig.trans.status}:</span>
                                <span>${user.status}</span>
                            </div>
                            <div class="detail-row col-toggle-6">
                                <span class="detail-label">${window.UserConfig.trans.verification}:</span>
                                <span>${user.verified}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-users-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">' + window.UserConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(user => {
            let tr = `
                <tr>
                    <td class="col-toggle-0">${user.photo}</td>
                    <td class="col-toggle-1">${user.info}</td>
                    <td class="col-toggle-2">${user.phone}</td>
                    <td class="col-toggle-3">${user.roles}</td>
                    <td class="col-toggle-4">${user.kyc_level}</td>
                    <td class="col-toggle-5">${user.status}</td>
                    <td class="col-toggle-6">${user.verified}</td>
                    <td class="text-center col-toggle-7">${user.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.UserConfig.trans.showing} ${(pagination.current_page - 1) * 10 + 1} ${window.UserConfig.trans.to} ${Math.min(pagination.current_page * 10, pagination.total)} ${window.UserConfig.trans.of} ${pagination.total} ${window.UserConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchUsers(${pageNum})">${link.label}</button></li>`;
            }
        });
        
        ul += `</ul>`;

        container.html(info + ul);
    }

    function applyColumnVisibility() {
        let savedVis = localStorage.getItem('users_col_visibility');
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

    // Initialize View Mode
    toggleView(currentView);

    // Handle Column Toggle check/uncheck
    $('.col-toggle').on('change', function() {
        let visArray = [];
        $('.col-toggle:checked').each(function() {
            visArray.push($(this).val());
        });
        localStorage.setItem('users_col_visibility', JSON.stringify(visArray));
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

    $('#filter_role, #filter_status').on('change', function() {
        fetchUsers(1);
    });
    
    fetchUsers(1);

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchUsers(1);
        }
    });

    // Wizard Logic
    let currentWizardStep = 1;

    function showWizardStep(n) {
        $('.wizard-step-content').removeClass('active');
        $('#step-' + n).addClass('active');

        $('.wizard-step-container').removeClass('active completed');
        $('.wizard-step-container').each(function() {
            let stepNum = parseInt($(this).data('step'));
            if (stepNum < n) {
                $(this).addClass('completed');
            } else if (stepNum === n) {
                $(this).addClass('active');
            }
        });

        if (n === 1) {
            $('#wizardPrevBtn').prop('disabled', true);
        } else {
            $('#wizardPrevBtn').prop('disabled', false);
        }

        if (n === 4) {
            $('#wizardNextBtn').hide();
            $('#wizardSubmitBtn').show();
        } else {
            $('#wizardNextBtn').show();
            $('#wizardSubmitBtn').hide();
        }
    }

    window.nextPrev = function(n) {
        if (n === 1 && !validateWizardStep(currentWizardStep)) return false;
        currentWizardStep = currentWizardStep + n;
        showWizardStep(currentWizardStep);
    };

    window.jumpToWizardStep = function(targetStep) {
        if (targetStep === currentWizardStep) return;
        if (targetStep > currentWizardStep) {
            if (!validateWizardStep(currentWizardStep)) return false;
        }
        currentWizardStep = targetStep;
        showWizardStep(currentWizardStep);
    };

    function validateWizardStep(step) {
        let isValid = true;
        let stepEl = $('#step-' + step);
        stepEl.find('input[required], select[required]').each(function() {
            if (!this.checkValidity()) {
                $(this).addClass('is-invalid');
                if ($(this).hasClass('select2-init') || $(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').addClass('border-danger');
                }
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                if ($(this).hasClass('select2-init') || $(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
                }
            }
        });
        
        if(!isValid) {
            if(typeof toastr !== 'undefined') {
                toastr.error(window.UserConfig.trans.fillRequired);
            } else {
                alert(window.UserConfig.trans.fillRequired);
            }
        }
        return isValid;
    }

    // Edit Wizard Logic
    window.currentEditWizardStep = 1;

    window.showEditWizardStep = function(n) {
        $('#editUserModal .wizard-step-content').removeClass('active');
        $('#edit-step-' + n).addClass('active');

        $('#editWizardSteps .wizard-step-container').removeClass('active completed');
        $('#editWizardSteps .wizard-step-container').each(function() {
            let stepNum = parseInt($(this).data('step'));
            if (stepNum < n) {
                $(this).addClass('completed');
            } else if (stepNum === n) {
                $(this).addClass('active');
            }
        });

        if (n === 1) {
            $('#editWizardPrevBtn').prop('disabled', true);
        } else {
            $('#editWizardPrevBtn').prop('disabled', false);
        }

        if (n === 4) {
            $('#editWizardNextBtn').hide();
            $('#editWizardSubmitBtn').show();
        } else {
            $('#editWizardNextBtn').show();
            $('#editWizardSubmitBtn').hide();
        }
    }

    window.nextEditPrev = function(n) {
        if (n === 1 && !window.validateEditWizardStep(window.currentEditWizardStep)) return false;
        window.currentEditWizardStep = window.currentEditWizardStep + n;
        window.showEditWizardStep(window.currentEditWizardStep);
    };

    window.jumpToEditWizardStep = function(targetStep) {
        if (targetStep === window.currentEditWizardStep) return;
        if (targetStep > window.currentEditWizardStep) {
            if (!window.validateEditWizardStep(window.currentEditWizardStep)) return false;
        }
        window.currentEditWizardStep = targetStep;
        window.showEditWizardStep(window.currentEditWizardStep);
    };

    window.validateEditWizardStep = function(step) {
        let isValid = true;
        let stepEl = $('#edit-step-' + step);
        stepEl.find('input[required], select[required]').each(function() {
            if (!this.checkValidity()) {
                $(this).addClass('is-invalid');
                if ($(this).hasClass('select2-init') || $(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').addClass('border-danger');
                }
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                if ($(this).hasClass('select2-init') || $(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
                }
            }
        });
        
        if(!isValid) {
            if(typeof toastr !== 'undefined') {
                toastr.error(window.UserConfig.trans.fillRequired);
            } else {
                alert(window.UserConfig.trans.fillRequired);
            }
        }
        return isValid;
    }

    $('#addUserModal').on('show.bs.modal', function() {
        $('#addUserForm')[0].reset();
        $('#addUserForm .is-invalid').removeClass('is-invalid');
        $('#addUserForm .invalid-feedback').remove();
        $('#addUserForm .select2-init').val('').trigger('change');
        
        currentWizardStep = 1;
        showWizardStep(currentWizardStep);
    });

    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        window.WJHTAKAdmin.btnLoading(btn, true);
        
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();

        $.ajax({
            url: window.UserConfig.urls.store,
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    fetchUsers(currentPage);
                    toastr.success(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstErrorStep = null;
                    
                    Object.keys(errors).forEach(key => {
                        let input = $('#addUserForm').find(`[name="${key}"]`);
                        if(input.length === 0 && key.includes('.')) {
                            let rootKey = key.split('.')[0] + '[]';
                            input = $('#addUserForm').find(`[name="${rootKey}"]`);
                        }
                        if(input.length) {
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback d-block">${errors[key][0]}</div>`);
                            
                            let stepContent = input.closest('.wizard-step-content');
                            if (stepContent.length && !firstErrorStep) {
                                firstErrorStep = parseInt(stepContent.attr('id').replace('step-', ''));
                            }
                        } else {
                            toastr.error(errors[key][0]);
                        }
                    });
                    
                    if (firstErrorStep && firstErrorStep !== currentWizardStep) {
                        currentWizardStep = firstErrorStep;
                        showWizardStep(currentWizardStep);
                    }
                } else {
                    toastr.error(window.UserConfig.trans.unexpectedError);
                }
            },
            complete: function() {
                window.WJHTAKAdmin.btnLoading(btn, false);
            }
        });
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        window.WJHTAKAdmin.btnLoading(btn, true);

        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();

        const id = $('#edit_user_id').val();
        const url = window.UserConfig.urls.update.replace(':id', id);
        const formData = $(this).serialize() + '&_method=PUT';

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    fetchUsers(currentPage);
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        let input = $('#editUserForm').find(`[name="${key}"]`);
                        if(input.length) {
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback d-block">${errors[key][0]}</div>`);
                        } else {
                            toastr.error(errors[key][0]);
                        }
                    });
                } else {
                    toastr.error(window.UserConfig.trans.unexpectedError);
                }
            },
            complete: function() {
                window.WJHTAKAdmin.btnLoading(btn, false);
            }
        });
    });
});

window.viewUser = function(id) {
    const btn = event.currentTarget ? $(event.currentTarget) : null;
    if(btn) window.WJHTAKAdmin.btnLoading(btn, true);
    
    let url = window.UserConfig.urls.show.replace(':id', id);
    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            const kyc = response.kyc_request;
            const stats = response.stats;
            const html = `
                <div class="row mb-4 g-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-card blue h-100 stat-card-compact shadow-sm border-0">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </div>
                            <div>
                                <div class="stat-value" style="font-size: 1.25rem !important;">${stats.wallet_balance}</div>
                                <div class="stat-label" style="font-size: 0.75rem !important;">${window.UserConfig.trans.walletBalance}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-card green h-100 stat-card-compact shadow-sm border-0">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                            <div>
                                <div class="stat-value" style="font-size: 1.25rem !important;">${stats.auctions_created}</div>
                                <div class="stat-label" style="font-size: 0.75rem !important;">${window.UserConfig.trans.createdAuctions}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-card gold h-100 stat-card-compact shadow-sm border-0">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            </div>
                            <div>
                                <div class="stat-value" style="font-size: 1.25rem !important;">${stats.auctions_won}</div>
                                <div class="stat-label" style="font-size: 0.75rem !important;">${window.UserConfig.trans.wonAuctions}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-card red h-100 stat-card-compact shadow-sm border-0">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            </div>
                            <div>
                                <div class="stat-value" style="font-size: 1.25rem !important;">${stats.bids_count}</div>
                                <div class="stat-label" style="font-size: 0.75rem !important;">${window.UserConfig.trans.totalBids}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="user-grid-card border-0 shadow-sm" style="background: var(--bg-card, #fff);">
                            <img src="${response.photo_url}" class="card-avatar" style="width: 120px; height: 120px; border-width: 3px;" alt="User">
                            <div class="card-info">
                                <strong>${user.first_name} ${user.last_name || ''}</strong>
                                <small dir="ltr" class="d-block mt-1">${user.email}</small>
                                <div class="mt-2">
                                    <span class="badge ${user.status === 'approved' ? 'badge-success' : (user.status === 'rejected' ? 'badge-danger' : 'badge-warning')} px-3 py-2 rounded-pill">${user.status === 'approved' ? window.UserConfig.trans.approved + ' ✅' : (user.status === 'rejected' ? window.UserConfig.trans.rejected + ' ❌' : window.UserConfig.trans.pendingVerify + ' ⏳')}</span>
                                </div>
                                <div class="mt-2">
                                    <span class="badge badge-info px-3 py-2 rounded-pill">KYC Level ${user.kyc_level}</span>
                                </div>
                                <div class="mt-3 text-start d-flex flex-wrap justify-content-center">
                                    ${response.roles.length > 0 ? response.roles.map(r => '<span class="badge bg-primary text-white me-1 mb-1">' + r + '</span>').join('') : '<span class="text-muted small">' + window.UserConfig.trans.noRoles + '</span>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm h-100" style="background: var(--bg-card, #fff);">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-2">
                                <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>${window.UserConfig.trans.personalInfo}</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 text-start" style="font-size: 0.9rem;">
                                        <tbody>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.phoneNumber}</th><td class="border-bottom-0 fw-medium" dir="ltr" style="text-align: left;">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.idNumber}</th><td class="border-bottom-0 fw-medium">${user.id_number || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.gender}</th><td class="border-bottom-0 fw-medium">${user.gender === 'male' ? window.UserConfig.trans.male : (user.gender === 'female' ? window.UserConfig.trans.female : '---')}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.dob}</th><td class="border-bottom-0 fw-medium">${user.date_of_birth || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.country}</th><td class="border-bottom-0 fw-medium">${user.country || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.city}</th><td class="border-bottom-0 fw-medium">${user.city || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0">${window.UserConfig.trans.address}</th><td class="border-bottom-0 fw-medium">${user.address || '---'}</td></tr>
                                            <tr><th style="width: 35%; color: var(--text-muted);" class="ps-4 border-bottom-0 pb-3">${window.UserConfig.trans.dateJoined}</th><td class="border-bottom-0 fw-medium pb-3">${response.created_at}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                ${kyc ? `
                <div class="card border-0 shadow-sm mt-4" style="background: var(--bg-card, #fff);">
                    <div class="card-header bg-transparent border-bottom pt-3 pb-2">
                        <h6 class="text-primary fw-bold mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>${window.UserConfig.trans.kycDocs}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="p-3 rounded text-center h-100 border" style="background: var(--bg-input, #f8f9fa); border-color: var(--border) !important;">
                                    <label class="d-block fw-semibold mb-3 text-muted">${window.UserConfig.trans.idImage}</label>
                                    <a href="${kyc.id_image_url}" target="_blank">
                                        <img src="${kyc.id_image_url}" class="img-fluid rounded shadow-sm" style="max-height: 220px; object-fit: contain;">
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded text-center h-100 border" style="background: var(--bg-input, #f8f9fa); border-color: var(--border) !important;">
                                    <label class="d-block fw-semibold mb-3 text-muted">${window.UserConfig.trans.selfieImage}</label>
                                    <a href="${kyc.selfie_image_url}" target="_blank">
                                        <img src="${kyc.selfie_image_url}" class="img-fluid rounded shadow-sm" style="max-height: 220px; object-fit: contain;">
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded" style="background: var(--bg-card, #fff); border-color: var(--border) !important;">
                                            <small class="d-block text-muted mb-1">${window.UserConfig.trans.nameInReq}</small>
                                            <strong class="fs-6">${kyc.full_name || '---'}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded" style="background: var(--bg-card, #fff); border-color: var(--border) !important;">
                                            <small class="d-block text-muted mb-1">${window.UserConfig.trans.country}</small>
                                            <strong class="fs-6">${kyc.country || '---'}</strong>
                                        </div>
                                    </div>
                                </div>
                                ${kyc.admin_note ? `<div class="p-3 rounded mt-3 text-danger border border-danger border-opacity-25" style="background: var(--danger-glow, rgba(220, 38, 38, 0.08));"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><strong>${window.UserConfig.trans.adminNote}:</strong> ${kyc.admin_note}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                ` : '<div class="alert alert-warning mt-4 text-center d-flex align-items-center justify-content-center border-0 shadow-sm"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> <span class="ms-2 fw-medium">' + window.UserConfig.trans.noKyc + '</span></div>'}
            `;
            $('#viewUserBody').html(html);
            $('#viewUserModal').modal('show');
        }
    }).always(function() {
        if(btn) {
            window.WJHTAKAdmin.btnLoading(btn, false);
            let dropdown = btn.closest('.dropdown').find('.dropdown-toggle');
            if(dropdown.length && typeof bootstrap !== 'undefined') {
                let instance = bootstrap.Dropdown.getInstance(dropdown[0]);
                if (instance) instance.hide();
            }
        }
    });
};

window.editUser = function(id) {
    const btn = event.currentTarget ? $(event.currentTarget) : null;
    if(btn) window.WJHTAKAdmin.btnLoading(btn, true);
    
    let url = window.UserConfig.urls.show.replace(':id', id);

    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            $('#edit_user_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#edit_country_code').val(user.country_code).trigger('change');
            $('#edit_phone').val(user.phone);
            $('#edit_country').val(user.country);
            $('#edit_city').val(user.city);
            $('#edit_address').val(user.address);
            $('#edit_date_of_birth').val(user.date_of_birth);
            $('#edit_id_number').val(user.id_number);
            $('#edit_status').val(user.status).trigger('change');
            $('#edit_kyc_level').val(user.kyc_level).trigger('change');
            $('#edit_gender').val(user.gender).trigger('change');
            $('#edit_password').val('');
            
            $('.edit-role-checkbox').each(function() {
                $(this).prop('checked', response.roles.includes($(this).val()));
            });
            
            window.currentEditWizardStep = 1;
            window.showEditWizardStep(window.currentEditWizardStep);
            $('#editUserForm .is-invalid').removeClass('is-invalid');
            $('#editUserForm .invalid-feedback').remove();
            
            $('#editUserModal').modal('show');
        }
    }).always(function() {
        if(btn) {
            window.WJHTAKAdmin.btnLoading(btn, false);
            let dropdown = btn.closest('.dropdown').find('.dropdown-toggle');
            if(dropdown.length && typeof bootstrap !== 'undefined') {
                let instance = bootstrap.Dropdown.getInstance(dropdown[0]);
                if (instance) instance.hide();
            }
        }
    });
};

window.updateUserStatus = function(id, status) {
    const url = window.UserConfig.urls.updateStatus.replace(':id', id);
    let title = status === 'approved' ? window.UserConfig.trans.approveVerify : window.UserConfig.trans.rejectVerify;
    let text = status === 'approved' ? window.UserConfig.trans.approveDesc : window.UserConfig.trans.rejectDesc;
    let icon = status === 'approved' ? 'success' : 'warning';
    let confirmBtn = status === 'approved' ? 'var(--bs-success, #10b981)' : 'var(--bs-danger, #ef4444)';

    Swal.fire({
        title: title,
        text: text,
        input: status === 'rejected' ? 'textarea' : null,
        inputPlaceholder: window.UserConfig.trans.writeReject,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmBtn,
        cancelButtonColor: 'var(--bs-secondary, #6c757d)',
        confirmButtonText: window.UserConfig.trans.yesConfirm,
        cancelButtonText: window.UserConfig.trans.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                method: 'POST',
                data: { 
                    status: status,
                    note: result.value || ''
                },
                success: function(response) {
                    if (response.success) {
                        fetchUsers(currentPage);
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
};

window.verifyUser = function(id) {
    Swal.fire({
        title: window.UserConfig.trans.verifyAccount,
        text: window.UserConfig.trans.verifyAccountDesc,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--bs-primary, #3085d6)',
        cancelButtonColor: 'var(--bs-secondary, #6c757d)',
        confirmButtonText: window.UserConfig.trans.yesVerify,
        cancelButtonText: window.UserConfig.trans.cancel
    }).then((result) => {
        if (result.value || result.isConfirmed) {
            $.post(window.UserConfig.urls.verify.replace(':id', id), function(response) {
                if (response.success) {
                    fetchUsers(currentPage);
                    toastr.success(response.message);
                }
            });
        }
    });
};

window.verifyIdentity = function(id) {
    Swal.fire({
        title: window.UserConfig.trans.verifyIdentityTitle,
        text: window.UserConfig.trans.verifyIdentityDesc,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: 'var(--bs-primary, #3085d6)',
        cancelButtonColor: 'var(--bs-secondary, #6c757d)',
        confirmButtonText: window.UserConfig.trans.yesVerifyIdentity,
        cancelButtonText: window.UserConfig.trans.cancel
    }).then((result) => {
        if (result.value || result.isConfirmed) {
            let url = window.UserConfig.urls.verifyIdentity.replace(':id', id);
            $.post(url, function(response) {
                if (response.success) {
                    fetchUsers(currentPage);
                    toastr.success(response.message);
                }
            });
        }
    });
};

window.deleteUser = function(id) {
    let url = window.UserConfig.urls.destroy.replace(':id', id);
    
    Swal.fire({
        title: window.UserConfig.trans.deleteAccount,
        text: window.UserConfig.trans.deleteDesc,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: 'var(--bs-danger, #d33)',
        cancelButtonColor: 'var(--bs-secondary, #6c757d)',
        confirmButtonText: window.UserConfig.trans.yesDelete,
        cancelButtonText: window.UserConfig.trans.cancel
    }).then((result) => {
        if (result.value || result.isConfirmed) {
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _method: 'DELETE'
                },
                success: function(response) {
                    if (response.success) {
                        fetchUsers(currentPage);
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
};
