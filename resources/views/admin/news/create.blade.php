@extends('layouts.admin')

@section('title', __('Add New News'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .nav-tabs .nav-link {
        color: var(--text-muted);
        border: none;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
    }
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: var(--text-color);
    }
    .nav-tabs .nav-link.active {
        color: var(--brand-blue-light);
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--brand-blue-light);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Add New News') }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.news.index') }}">{{ __('News') }}</a> / 
            {{ __('Add New News') }}
        </div>
    </div>
    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row g-4">
        <!-- Content Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: var(--radius-lg); background: var(--bg-card); color: var(--text-color);">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-4 pt-3 border-bottom-0" id="newsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="arabic-tab" data-bs-toggle="tab" data-bs-target="#arabic" type="button" role="tab" aria-controls="arabic" aria-selected="true">
                                <span class="badge bg-primary me-2">AR</span> {{ __('Arabic Content') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="english-tab" data-bs-toggle="tab" data-bs-target="#english" type="button" role="tab" aria-controls="english" aria-selected="false">
                                <span class="badge bg-secondary me-2">EN</span> {{ __('English Content') }}
                            </button>
                        </li>
                    </ul>
                    <hr class="m-0" style="border-color: var(--border-light);">

                    <div class="tab-content p-4" id="newsTabContent">
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade show active" id="arabic" role="tabpanel" aria-labelledby="arabic-tab">
                            <div class="form-group mb-4">
                                <label for="title_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Title') }} (AR) <span class="text-danger">*</span></span>
                                    <x-translate-button from="#title_ar" to="#title_en" />
                                </label>
                                <input type="text" class="form-control form-control-lg @error('title_ar') is-invalid @enderror" id="title_ar" name="title_ar" value="{{ old('title_ar') }}" placeholder="أدخل العنوان بالعربية..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="content_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Content') }} (AR) <span class="text-danger">*</span></span>
                                    <x-translate-button from="#content_ar" to="#content_en" />
                                </label>
                                <textarea class="form-control summernote @error('content_ar') is-invalid @enderror" id="content_ar" name="content_ar" rows="6" required>{{ old('content_ar') }}</textarea>
                                @error('content_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="tags_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Tags') }} (AR) <small class="text-muted fw-normal">({{ __('Comma separated') }})</small></span>
                                    <x-translate-button from="#tags_ar" to="#tags_en" />
                                </label>
                                <input type="text" class="form-control @error('tags_ar') is-invalid @enderror" id="tags_ar" name="tags_ar" value="{{ old('tags_ar') }}" placeholder="سيارات, عروض, تحديث..." style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('tags_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <hr class="my-4" style="border-color: var(--border-light);">
                            
                            <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-search me-2"></i> {{ __('SEO Metadata') }} (AR)</h5>
                            
                            <div class="form-group mb-4">
                                <label for="meta_title_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Meta Title') }} (AR)</span>
                                    <x-translate-button from="#meta_title_ar" to="#meta_title_en" />
                                </label>
                                <input type="text" class="form-control @error('meta_title_ar') is-invalid @enderror" id="meta_title_ar" name="meta_title_ar" value="{{ old('meta_title_ar') }}" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('meta_title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="meta_description_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Meta Description') }} (AR)</span>
                                    <x-translate-button from="#meta_description_ar" to="#meta_description_en" />
                                </label>
                                <textarea class="form-control @error('meta_description_ar') is-invalid @enderror" id="meta_description_ar" name="meta_description_ar" rows="3" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">{{ old('meta_description_ar') }}</textarea>
                                @error('meta_description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label for="meta_keywords_ar" class="form-label fw-bold d-flex justify-content-between align-items-center w-100">
                                    <span>{{ __('Meta Keywords') }} (AR) <small class="text-muted fw-normal">({{ __('Comma separated') }})</small></span>
                                    <x-translate-button from="#meta_keywords_ar" to="#meta_keywords_en" />
                                </label>
                                <input type="text" class="form-control @error('meta_keywords_ar') is-invalid @enderror" id="meta_keywords_ar" name="meta_keywords_ar" value="{{ old('meta_keywords_ar') }}" placeholder="أخبار, سيارات, تحديث..." style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('meta_keywords_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- English Tab -->
                        <div class="tab-pane fade" id="english" role="tabpanel" aria-labelledby="english-tab">
                            <div class="form-group mb-4">
                                <label for="title_en" class="form-label fw-bold">{{ __('Title') }} (EN) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('title_en') is-invalid @enderror" id="title_en" name="title_en" value="{{ old('title_en') }}" placeholder="Enter title in English..." required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="content_en" class="form-label fw-bold">{{ __('Content') }} (EN) <span class="text-danger">*</span></label>
                                <textarea class="form-control summernote @error('content_en') is-invalid @enderror" id="content_en" name="content_en" rows="6" required>{{ old('content_en') }}</textarea>
                                @error('content_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="tags_en" class="form-label fw-bold">{{ __('Tags') }} (EN) <small class="text-muted fw-normal">({{ __('Comma separated') }})</small></label>
                                <input type="text" class="form-control @error('tags_en') is-invalid @enderror" id="tags_en" name="tags_en" value="{{ old('tags_en') }}" placeholder="cars, offers, update..." style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('tags_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <hr class="my-4" style="border-color: var(--border-light);">
                            
                            <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-search me-2"></i> {{ __('SEO Metadata') }} (EN)</h5>
                            
                            <div class="form-group mb-4">
                                <label for="meta_title_en" class="form-label fw-bold">{{ __('Meta Title') }} (EN)</label>
                                <input type="text" class="form-control @error('meta_title_en') is-invalid @enderror" id="meta_title_en" name="meta_title_en" value="{{ old('meta_title_en') }}" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('meta_title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="meta_description_en" class="form-label fw-bold">{{ __('Meta Description') }} (EN)</label>
                                <textarea class="form-control @error('meta_description_en') is-invalid @enderror" id="meta_description_en" name="meta_description_en" rows="3" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">{{ old('meta_description_en') }}</textarea>
                                @error('meta_description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label for="meta_keywords_en" class="form-label fw-bold">{{ __('Meta Keywords') }} (EN) <small class="text-muted fw-normal">({{ __('Comma separated') }})</small></label>
                                <input type="text" class="form-control @error('meta_keywords_en') is-invalid @enderror" id="meta_keywords_en" name="meta_keywords_en" value="{{ old('meta_keywords_en') }}" placeholder="news, cars, update..." style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                                @error('meta_keywords_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-lg); background: var(--bg-card); color: var(--text-color);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">{{ __('Publishing Settings') }}</h5>
                    
                    <div class="form-group mb-4">
                        <label for="category_id" class="form-label fw-bold">{{ __('Category') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="image" class="form-label fw-bold">{{ __('News Image') }}</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" style="background: var(--bg-input); color: var(--text-color); border: 1px solid var(--border);" onchange="previewImage(this)">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="mt-3 text-center d-none" id="imagePreviewContainer">
                            <img src="" id="imagePreview" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="checkbox-item w-100 mb-0 d-flex align-items-center p-3 rounded" style="background: var(--bg-body); border: 1px solid var(--border);">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <div class="ms-3">
                                <div class="check-label fw-bold fs-6">{{ __('Active Status') }}</div>
                                <div class="check-sub text-muted mt-1" style="font-size: 0.8rem;">{{ __('Make this news visible to users immediately.') }}</div>
                            </div>
                        </label>
                    </div>

                    <div class="form-group mb-4">
                        <label class="checkbox-item w-100 mb-0 d-flex align-items-center p-3 rounded" style="background: var(--bg-body); border: 1px solid var(--border); border-left: 4px solid var(--bs-warning);">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <div class="ms-3">
                                <div class="check-label fw-bold fs-6"><i class="fas fa-star text-warning me-1"></i> {{ __('Featured News') }}</div>
                                <div class="check-sub text-muted mt-1" style="font-size: 0.8rem;">{{ __('Pin or feature this news at the top.') }}</div>
                            </div>
                        </label>
                    </div>

                    <hr class="my-4 border-light">

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2" style="border-radius: var(--radius-md);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Save News') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewContainer').removeClass('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
