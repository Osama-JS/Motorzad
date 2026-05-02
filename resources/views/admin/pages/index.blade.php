@extends('layouts.admin')

@section('title', 'إدارة الصفحات')

@section('css')
<style>
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .table td { vertical-align: middle; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Pages Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Pages') }}</div>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Page') }}
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Pages List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="pages-table" class="table table-striped w-100" style="text-align: right;">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Title (Arabic)') }}</th>
                    <th>{{ __('Title (English)') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Show in Footer') }}</th>
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

@section('js')
<script>
    $(document).ready(function() {
        $('#pages-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.pages.data') }}",
            columns: [
                { data: 'id' },
                { data: 'title_ar' },
                { data: 'title_en' },
                { data: 'slug' },
                { data: 'is_active' },
                { data: 'show_in_footer' },
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

    function deletePage(id) {
        if (confirm('{{ __("Are you sure you want to delete this page?") }}')) {
            $.ajax({
                url: "{{ route('admin.pages.index') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    }
</script>
@endsection
