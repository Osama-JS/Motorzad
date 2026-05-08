@extends('layouts.admin')

@section('title', 'إدارة الاسئلة الشائعة')

@section('css')
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
                                        <label for="question_ar" class="form-label fw-bold">{{ __('Question') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg px-3 py-2" id="question_ar" name="question_ar" placeholder="أدخل السؤال بالعربية..." required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="answer_ar" class="form-label fw-bold">{{ __('Answer') }} <span class="text-danger">*</span></label>
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
<script>
    $(document).ready(function() {
        var table = $('#faqs-table').DataTable({
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
