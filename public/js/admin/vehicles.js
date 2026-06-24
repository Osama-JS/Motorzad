$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.VehicleConfig.csrf
        }
    });

    let currentPage = 1;
    let currentData = [];
    let currentView = localStorage.getItem('vehicles_view_mode') || 'table';

    window.fetchVehicles = function(page = 1) {
        currentPage = page;
        $('#custom-vehicles-tbody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.VehicleConfig.trans.loading + '</td></tr>');
        $('#grid-view-container').html('<div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> ' + window.VehicleConfig.trans.loading + '</div>');
        
        let search = $('#filter_search').val();
        let status = $('#filter_status').val();
        let perPage = $('#filter_per_page').val();

        $.ajax({
            url: window.VehicleConfig.urls.data,
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
                    $('#custom-vehicles-tbody').html('<tr><td colspan="5" class="text-center py-4 text-danger">' + window.VehicleConfig.trans.errorLoading + '</td></tr>');
                    $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.VehicleConfig.trans.errorLoading + '</div>');
                }
            },
            error: function() {
                $('#custom-vehicles-tbody').html('<tr><td colspan="5" class="text-center py-4 text-danger">' + window.VehicleConfig.trans.errorLoading + '</td></tr>');
                $('#grid-view-container').html('<div class="col-12 text-center py-4 text-danger">' + window.VehicleConfig.trans.errorLoading + '</div>');
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
        localStorage.setItem('vehicles_view_mode', mode);
        
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
            container.html('<div class="col-12 text-center py-4 text-muted">' + window.VehicleConfig.trans.noRecords + '</div>');
            return;
        }

        data.forEach(vehicle => {
            let card = `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="vehicle-grid-card border-0 bg-white rounded-4 overflow-hidden position-relative" style="box-shadow: 0 4px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; height: 100%;">
                        <div class="position-relative">
                            <img src="${vehicle.image_url}" class="w-100 vehicle-card-img-top" alt="${vehicle.raw_title}">
                            <div class="position-absolute top-0 end-0 p-3">
                                ${vehicle.status}
                            </div>
                            <div class="quick-actions-overlay position-absolute w-100 h-100 top-0 start-0 d-flex justify-content-center align-items-center gap-2">
                                <a href="${vehicle.view_url}" class="btn btn-light rounded-circle shadow-sm" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#0ea5e9;" title="${window.VehicleConfig.trans.view}"><i class="fa-solid fa-eye"></i></a>
                                <a href="${vehicle.edit_url}" class="btn btn-light rounded-circle shadow-sm" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:var(--primary);" title="${window.VehicleConfig.trans.edit}"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button onclick="deleteVehicle(${vehicle.id})" class="btn btn-light rounded-circle shadow-sm" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#ef4444;" title="${window.VehicleConfig.trans.delete}"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="mb-1 text-truncate fw-bold" style="font-size: 1.1rem; color: var(--text-color);">${vehicle.raw_title}</h5>
                            <div class="text-muted small mb-3">
                                <i class="fa-solid fa-car me-1"></i> ${vehicle.make} ${vehicle.model} &bull; ${vehicle.year}
                            </div>
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <span class="d-block" style="font-size:0.75rem;">${window.VehicleConfig.trans.vin}</span>
                                    <span class="fw-medium text-dark" dir="ltr">${vehicle.vin_number}</span>
                                </div>
                            </div>
                            ${vehicle.quick_actions ? `<div class="mt-3 d-flex gap-2">${vehicle.quick_actions}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderTable(data) {
        let tbody = $('#custom-vehicles-tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">' + window.VehicleConfig.trans.noRecords + '</td></tr>');
            return;
        }

        data.forEach(vehicle => {
            let tr = `
                <tr>
                    <td>${vehicle.image}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">${vehicle.title}</span>
                            <span class="text-muted small">${vehicle.make} ${vehicle.model} - ${vehicle.year}</span>
                        </div>
                    </td>
                    <td dir="ltr" class="text-end">${vehicle.vin_number}</td>
                    <td>${vehicle.status}</td>
                    <td class="text-center">${vehicle.actions}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }

    function renderPagination(pagination) {
        let container = $('#custom-pagination');
        container.empty();

        if (!pagination || pagination.total === 0) return;

        let info = `<div class="text-muted small">${window.VehicleConfig.trans.showing} ${(pagination.current_page - 1) * parseInt($('#filter_per_page').val() || 10) + 1} ${window.VehicleConfig.trans.to} ${Math.min(pagination.current_page * parseInt($('#filter_per_page').val() || 10), pagination.total)} ${window.VehicleConfig.trans.of} ${pagination.total} ${window.VehicleConfig.trans.entries}</div>`;
        
        let ul = `<ul class="pagination custom-pagination mb-0">`;
        
        pagination.links.forEach(link => {
            if (link.url === null) {
                ul += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            } else {
                let activeClass = link.active ? 'active' : '';
                let pageNumMatch = link.url.match(/page=(\d+)/);
                let pageNum = pageNumMatch ? pageNumMatch[1] : 1;
                ul += `<li class="page-item ${activeClass}"><button class="page-link" onclick="fetchVehicles(${pageNum})">${link.label}</button></li>`;
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
        fetchVehicles(1);
    });

    $('#btn-filter').on('click', function() {
        fetchVehicles(1);
    });

    $('#filter_search').on('keypress', function(e) {
        if(e.which == 13) {
            fetchVehicles(1);
        }
    });

    // Initialize View Mode
    toggleView(currentView);
    fetchVehicles(1);

    // Global Action Functions
    window.deleteVehicle = function(id) {
        let url = window.VehicleConfig.urls.destroy.replace(':id', id);
        
        Swal.fire({
            title: window.VehicleConfig.trans.deleteVehicleTitle,
            text: window.VehicleConfig.trans.deleteVehicleText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.VehicleConfig.trans.yesDelete,
            cancelButtonText: window.VehicleConfig.trans.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            fetchVehicles(currentPage);
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(window.VehicleConfig.trans.deleteVehicleError);
                    }
                });
            }
        });
    };

    window.approveVehicle = function(id) {
        let url = window.VehicleConfig.urls.approve.replace(':id', id);
        
        Swal.fire({
            title: window.VehicleConfig.trans.approveVehicleTitle,
            text: window.VehicleConfig.trans.approveVehicleText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.VehicleConfig.trans.yesApprove,
            cancelButtonText: window.VehicleConfig.trans.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            fetchVehicles(currentPage);
                        }
                    }
                });
            }
        });
    };

    window.rejectVehicle = function(id) {
        let url = window.VehicleConfig.urls.reject.replace(':id', id);
        
        Swal.fire({
            title: window.VehicleConfig.trans.rejectVehicleTitle,
            input: 'textarea',
            inputLabel: window.VehicleConfig.trans.reasonForRejection,
            inputPlaceholder: window.VehicleConfig.trans.rejectPlaceholder,
            inputAttributes: {
                'aria-label': window.VehicleConfig.trans.reasonForRejection
            },
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.VehicleConfig.trans.reject,
            cancelButtonText: window.VehicleConfig.trans.cancel,
            inputValidator: (value) => {
                if (!value) {
                    return window.VehicleConfig.trans.mustWriteReason;
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        rejection_reason: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            fetchVehicles(currentPage);
                        }
                    }
                });
            }
        });
    };
});
