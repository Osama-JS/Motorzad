@extends('layouts.admin')

@section('title', 'إدارة الاسئلة الشائعة')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .table td { vertical-align: middle; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('FAQs Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('FAQs') }}</div>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New FAQ') }}
    </button>
</div>

<div class="row mb-4 g-3">
    <!-- Total FAQs -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card blue h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('Total FAQs') }}</div>
            </div>
        </div>
    </div>
    <!-- Active FAQs -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card green h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">{{ __('Active FAQs') }}</div>
            </div>
        </div>
    </div>
    <!-- Inactive FAQs -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stat-card red h-100 stat-card-compact">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['inactive'] }}</div>
                <div class="stat-label">{{ __('Inactive FAQs') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                    <input type="text" id="filter_search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search FAQs...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filter_status" class="form-select select2-init">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-secondary w-100" id="btn-filter">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('FAQs List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="faqs-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Question') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will fill this -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('modals')
<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="faqForm">
                @csrf
                <input type="hidden" name="_method" id="faqMethod" value="POST">
                <input type="hidden" id="faqId">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold fs-4 d-flex align-items-center gap-2" id="faqModalLabel">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-red)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span id="modalTitleText">{{ __('Add New FAQ') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    
                    <div class="row g-4">
                        <!-- Arabic Section -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-none" style="background: var(--bg-body); border-radius: var(--radius-lg);">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color: var(--brand-blue-light);">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">AR</span>
                                        {{ __('Arabic Content') }}
                                    </h6>
                                    
                                    <div class="form-group mb-4">
                                        <label for="question_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                            <span>{{ __('Question') }} <span class="text-danger">*</span></span>
                                            <x-translate-button from="#question_ar" to="#question_en" />
                                        </label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg px-3 py-2" id="question_ar" name="question_ar" placeholder="أدخل السؤال بالعربية..." required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="answer_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                            <span>{{ __('Answer') }} <span class="text-danger">*</span></span>
                                            <x-translate-button from="#answer_ar" to="#answer_en" />
                                        </label>
                                        <textarea class="form-control px-3 py-2" id="answer_ar" name="answer_ar" rows="6" placeholder="أدخل الإجابة بالعربية..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- English Section -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-none" style="background: var(--bg-body); border-radius: var(--radius-lg);">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2 text-primary">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">EN</span>
                                        {{ __('English Content') }}
                                    </h6>
                                    
                                    <div class="form-group mb-4">
                                        <label for="question_en" class="form-label fw-bold">{{ __('Question') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg px-3 py-2" id="question_en" name="question_en" placeholder="Enter question in English..." required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="answer_en" class="form-label fw-bold">{{ __('Answer') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control px-3 py-2" id="answer_en" name="answer_en" rows="6" placeholder="Enter answer in English..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top" style="border-color: var(--border-light) !important;">
                        <label class="checkbox-item w-100 mb-0 d-flex align-items-center p-3 rounded" style="background: var(--bg-body);">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <div class="ms-3">
                                <div class="check-label fw-bold fs-6">{{ __('Active Status') }}</div>
                                <div class="check-sub text-muted mt-1">{{ __('Make this FAQ visible to users immediately.') }}</div>
                            </div>
                        </label>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-ghost px-4 py-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" id="saveBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Save Data') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let table;
    $(document).ready(function() {
        // Initialize Select2 for filters
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-init').select2({
            dir: dir,
            minimumResultsForSearch: 10
        });

        table = $('#faqs-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.faqs.data') }}",
            columns: [
                { data: 'id' },
                { data: 'question' },
                { data: 'is_active' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "sProcessing": "{{ __('Loading...') }}",
                "sLengthMenu": "{{ __('Show _MENU_ entries') }}",
                "sZeroRecords": "{{ __('No matching records found') }}",
                "sInfo": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                "sSearch": "{{ __('Search:') }}",
                "oPaginate": {
                    "sFirst": "{{ __('First') }}",
                    "sPrevious": "{{ __('Previous') }}",
                    "sNext": "{{ __('Next') }}",
                    "sLast": "{{ __('Last') }}"
                }
            }
        });

        // Custom search filter for FAQ active state checkboxes in the DOM
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'faqs-table') return true;
                
                let statusVal = $('#filter_status').val();
                if (!statusVal) return true;
                
                let rowNode = table.row(dataIndex).node();
                if (!rowNode) return true;
                
                let isChecked = $(rowNode).find('.form-check-input').is(':checked');
                
                if (statusVal === 'active' && isChecked) return true;
                if (statusVal === 'inactive' && !isChecked) return true;
                
                return false;
            }
        );

        // Bind filter search input
        $('#filter_search').on('keyup keypress change', function() {
            table.search(this.value).draw();
        });

        // Bind status filter dropdown
        $('#filter_status').on('change', function() {
            table.draw();
        });

        // Bind filter button
        $('#btn-filter').on('click', function() {
            let searchVal = $('#filter_search').val();
            table.search(searchVal);
            table.draw();
        });
    });

    function deleteFaq(id) {
        if (confirm('{{ __("Are you sure?") }}')) {
            $.ajax({
                url: "{{ route('admin.faqs.index') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#faqs-table').DataTable().ajax.reload(null, false);
                        toastr.success('{{ __("FAQ deleted successfully.") }}');
                    }
                }
            });
        }
    }

    function toggleStatus(id) {
        $.ajax({
            url: "{{ route('admin.faqs.index') }}/" + id + "/toggle-active",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('{{ __("An error occurred.") }}');
                $('#faqs-table').DataTable().ajax.reload(null, false); // Revert the switch if error
            }
        });
    }

    // Modal Actions
    var faqModal = new bootstrap.Modal(document.getElementById('faqModal'));

    function openAddModal() {
        $('#faqForm')[0].reset();
        $('#faqMethod').val('POST');
        $('#faqId').val('');
        $('#faqModalLabel').text('{{ __("Add New FAQ") }}');
        faqModal.show();
    }

    function editFaq(id, q_ar, q_en, a_ar, a_en, is_active) {
        $('#faqForm')[0].reset();
        $('#faqMethod').val('PUT');
        $('#faqId').val(id);
        
        $('#question_ar').val(q_ar);
        $('#question_en').val(q_en);
        $('#answer_ar').val(a_ar);
        $('#answer_en').val(a_en);
        $('#is_active').prop('checked', is_active == 1);
        
        $('#faqModalLabel').text('{{ __("Edit FAQ") }}');
        faqModal.show();
    }

    $('#faqForm').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#faqId').val();
        let url = id ? "{{ route('admin.faqs.index') }}/" + id : "{{ route('admin.faqs.store') }}";
        
        let formData = $(this).serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    faqModal.hide();
                    $('#faqs-table').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        toastr.error(errors[key][0]);
                    }
                } else {
                    toastr.error('{{ __("An error occurred.") }}');
                }
            }
        });
    });
</script>
@endsection
