@extends('layouts.admin')

@section('title', 'تعديل الصفحة')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Edit Page:') }} {{ $page->title_ar }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.pages.index') }}">{{ __('Pages') }}</a> / {{ __('Edit') }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Page Details') }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Title (Arabic)') }}</label>
                    <input type="text" name="title_ar" class="form-control" required value="{{ old('title_ar', $page->title_ar) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Title (English)') }}</label>
                    <input type="text" name="title_en" class="form-control" required value="{{ old('title_en', $page->title_en) }}" dir="ltr">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Slug') }}</label>
                <input type="text" name="slug" class="form-control" required value="{{ old('slug', $page->slug) }}" dir="ltr">
                <small class="text-muted">{{ __('Example: about-us, privacy-policy') }}</small>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Content (Arabic)') }}</label>
                <textarea name="content_ar" class="form-control" id="content_ar" rows="10" required>{{ old('content_ar', $page->content_ar) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Content (English)') }}</label>
                <textarea name="content_en" class="form-control" id="content_en" rows="10" required dir="ltr">{{ old('content_en', $page->content_en) }}</textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $page->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ __('Active Page') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="show_in_footer" id="show_in_footer" {{ $page->show_in_footer ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_in_footer">
                            {{ __('Show in Footer') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Page') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    function initializeEditor(selector) {
        ClassicEditor
            .create(document.querySelector(selector), {
                language: '{{ app()->getLocale() }}',
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    }

    $(document).ready(function() {
        initializeEditor('#content_ar');
        initializeEditor('#content_en');
    });
</script>
@endsection
