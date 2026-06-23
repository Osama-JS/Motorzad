@extends('layouts.admin')

@section('title', __('Edit Auction'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/data-views.css') }}">
<style>
    /* Premium UI Enhancements */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .premium-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .premium-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }
    
    .card-header-premium {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 24px;
        background: var(--bg-card-solid);
        border-bottom: 1px solid var(--border);
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--text);
    }
    
    .card-body-premium {
        padding: 24px;
    }

    .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-wrapper svg { width: 20px; height: 20px; }
    
    /* Colors */
    .bg-light-primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .bg-light-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-light-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-light-purple { background: rgba(168, 85, 247, 0.1); color: #a855f7; }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: var(--bg-input);
        color: var(--text);
    }
    .form-control:focus, .form-select:focus {
        background-color: var(--bg-input);
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    
    /* File Upload */
    .file-upload-zone {
        position: relative;
        border: 2px dashed var(--border);
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        background: var(--bg-input);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload-zone:hover {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.05);
    }
    .file-upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
        width: 100%;
        height: 100%;
    }
    .upload-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        color: var(--text-muted);
        background: var(--bg-card);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .upload-text {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }
    .upload-highlight { color: #3b82f6; }
    .upload-hint { font-size: 0.85rem; color: var(--text-muted); }
    
    .file-upload-preview { 
        display: none; 
        margin-top: 16px; 
        align-items: center; 
        gap: 16px; 
        padding: 16px; 
        border: 1px solid var(--border); 
        border-radius: 12px; 
        background: var(--bg-card); 
    }
    .file-upload-preview.has-file { display: flex; }
    .preview-thumb { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
    .preview-info { flex: 1; min-width: 0; }
    .preview-name { font-size: 0.95rem; font-weight: 600; color: var(--text); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .preview-size { font-size: 0.8rem; color: var(--text-muted); }

    /* Custom Toggle Switch */
    .custom-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .custom-toggle:hover {
        border-color: #cbd5e1;
    }
    .toggle-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .toggle-title {
        font-weight: 600;
        color: var(--text);
    }
    
    /* The Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--border);
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    input:checked + .slider { background-color: #3b82f6; }
    input:checked + .slider:before { transform: translateX(22px); }

    /* Sidebar Sticky */
    .sidebar-sticky {
        position: sticky;
        top: 24px;
    }

    /* Action Buttons */
    .btn-publish {
        width: 100%;
        padding: 14px;
        font-size: 1.05rem;
        font-weight: 700;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        transition: all 0.3s;
    }
    .btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ __('Edit Auction') }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.auctions.index') }}">{{ __('Auctions') }}</a> / 
            {{ __('Edit') }}
        </div>
    </div>
    <a href="{{ route('admin.auctions.index') }}" class="btn btn-light" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back') }}
    </a>
</div>

<form id="editAuctionForm" action="{{ route('admin.auctions.update', $auction->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            
            <!-- Basic Information Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    {{ __('Basic Information') }}
                </div>
                <div class="card-body-premium">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Title (Arabic)') }}</span>
                                <x-translate-button from="#title_ar" to="#title_en" />
                            </label>
                            <input type="text" name="title_ar" id="title_ar" class="form-control" placeholder="{{ __('Enter auction title in Arabic') }}" value="{{ $auction->title_ar }}" required>
                            <div class="invalid-feedback error-title_ar"></div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Title (English)') }}</label>
                            <input type="text" name="title_en" id="title_en" class="form-control" placeholder="{{ __('Enter auction title in English') }}" value="{{ $auction->title_en }}" required>
                            <div class="invalid-feedback error-title_en"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Location (Arabic)') }}</span>
                                <x-translate-button from="#location_ar" to="#location_en" />
                            </label>
                            <input type="text" name="location_ar" id="location_ar" class="form-control" placeholder="مثال: الرياض، المملكة العربية السعودية" value="{{ $auction->location_ar }}">
                            <div class="invalid-feedback error-location_ar"></div>
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label">{{ __('Location (English)') }}</label>
                            <input type="text" name="location_en" id="location_en" class="form-control" placeholder="Example: Riyadh, Saudi Arabia" value="{{ $auction->location_en }}">
                            <div class="invalid-feedback error-location_en"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle & Image Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>
                    {{ __('Media & Vehicle') }}
                </div>
                <div class="card-body-premium">
                    <div class="form-group">
                        <label class="form-label">{{ __('Select Vehicle') }}</label>
                        <select name="vehicle_id" class="form-control select2-init" style="width: 100%" required>
                            <option value="">{{ __('Select Vehicle...') }}</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ $auction->vehicle_id == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->title }} ({{ $vehicle->id }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback error-vehicle_id"></div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('Auction Images') }} <span class="text-muted fw-normal ms-1">({{ __('Optional - Upload new images') }})</span></label>
                        
                        <!-- Existing Images Grid -->
                        @if($auction->images->count() > 0 || $auction->image)
                        <div class="mb-3 p-3 bg-light rounded border">
                            <h6 class="text-muted mb-2 small text-uppercase">{{ __('Current Images') }}</h6>
                            <div class="d-flex flex-wrap gap-2" id="existing_images_container">
                                @foreach($auction->images as $img)
                                <div class="position-relative existing-image-wrapper" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);" id="existing_img_{{ $img->id }}">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-100 h-100 object-fit-cover" alt="Image">
                                    @if($img->is_primary)
                                        <span class="position-absolute bottom-0 start-0 w-100 text-center text-white" style="background: rgba(59, 130, 246, 0.8); font-size: 10px; padding: 2px 0;">Primary</span>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 lh-1" style="transform: translate(25%, -25%); border-radius: 50%;" onclick="removeExistingImage({{ $img->id }})">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>
                                @endforeach
                                
                                @if($auction->images->count() == 0 && $auction->image)
                                    <div class="position-relative existing-image-wrapper" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                        <img src="{{ asset('storage/' . $auction->image) }}" class="w-100 h-100 object-fit-cover" alt="Legacy Image">
                                        <span class="position-absolute bottom-0 start-0 w-100 text-center text-white" style="background: rgba(108, 117, 125, 0.8); font-size: 10px; padding: 2px 0;">Legacy</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div id="deleted_images_inputs"></div>
                        @endif

                        <div class="file-upload-zone" onclick="document.getElementById('images').click()">
                            <input type="file" name="images[]" id="images" class="form-control d-none" accept="image/*" multiple onchange="handleFilePreview(this, 'preview_image')">
                            <div class="upload-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <div class="upload-text">{{ __('Drag images here or') }} <span class="upload-highlight">{{ __('click to browse') }}</span></div>
                            <div class="upload-hint">{{ __('Supported formats: PNG, JPG, WEBP. Max size: 2MB per image') }}</div>
                        </div>
                        
                        <div class="invalid-feedback error-images d-block mt-2"></div>
                        
                        <div class="file-upload-preview flex-wrap mt-3" id="preview_image">
                            <!-- Previews injected via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    {{ __('Detailed Description') }}
                </div>
                <div class="card-body-premium">
                    <div class="row">
                        <div class="col-12 form-group mb-4">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Description (Arabic)') }}</span>
                                <x-translate-button from="#description_ar" to="#description_en" type="editor" />
                            </label>
                            <textarea name="description_ar" id="description_ar" class="form-control summernote">{{ $auction->description_ar }}</textarea>
                            <div class="invalid-feedback error-description_ar"></div>
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="form-label">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" id="description_en" class="form-control summernote">{{ $auction->description_en }}</textarea>
                            <div class="invalid-feedback error-description_en"></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="sidebar-sticky">
                
                <!-- Status & Publish -->
                <div class="premium-card mb-4">
                    <div class="card-body-premium">
                        <div class="form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control select2-init" style="width: 100%" required>
                                <option value="draft" {{ $auction->status == 'draft' ? 'selected' : '' }}>📝 {{ __('Draft') }}</option>
                                <option value="scheduled" {{ $auction->status == 'scheduled' ? 'selected' : '' }}>📅 {{ __('Scheduled') }}</option>
                                <option value="live" {{ $auction->status == 'live' ? 'selected' : '' }}>🟢 {{ __('Live') }}</option>
                                <option value="completed" {{ $auction->status == 'completed' ? 'selected' : '' }}>✅ {{ __('Completed') }}</option>
                                <option value="cancelled" {{ $auction->status == 'cancelled' ? 'selected' : '' }}>❌ {{ __('Cancelled') }}</option>
                            </select>
                            <div class="invalid-feedback error-status"></div>
                        </div>
                        <button type="submit" class="btn-publish" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <span>{{ __('Update Auction') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Timing Card -->
                <div class="premium-card mb-4">
                    <div class="card-header-premium" style="padding: 16px 20px; font-size: 1.05rem;">
                        <div class="icon-wrapper bg-light-success" style="width: 32px; height: 32px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        {{ __('Timing') }}
                    </div>
                    <div class="card-body-premium" style="padding: 20px;">
                        <div class="form-group">
                            <label class="form-label">{{ __('Start Time') }}</label>
                            <input type="text" name="start_time" class="form-control datepicker" value="{{ $auction->start_time ? $auction->start_time->format('Y-m-d H:i') : '' }}" required>
                            <div class="invalid-feedback error-start_time"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('End Time') }}</label>
                            <input type="text" name="end_time" class="form-control datepicker" value="{{ $auction->end_time ? $auction->end_time->format('Y-m-d H:i') : '' }}" required>
                            <div class="invalid-feedback error-end_time"></div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('Auto Extend (Minutes)') }}</label>
                            <input type="number" name="auto_extend_minutes" class="form-control" value="{{ $auction->auto_extend_minutes }}" placeholder="0">
                            <div class="invalid-feedback error-auto_extend_minutes"></div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="premium-card mb-4">
                    <div class="card-header-premium" style="padding: 16px 20px; font-size: 1.05rem;">
                        <div class="icon-wrapper bg-light-warning" style="width: 32px; height: 32px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        {{ __('Pricing') }}
                    </div>
                    <div class="card-body-premium" style="padding: 20px;">
                        <div class="form-group">
                            <label class="form-label">{{ __('Start Price') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="start_price" class="form-control" placeholder="0.00" value="{{ $auction->start_price }}" required>
                                <span class="input-group-text bg-transparent border-start-0 text-muted">{{ config('app.currency', 'SAR') }}</span>
                            </div>
                            <div class="invalid-feedback error-start_price d-block"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Reserve Price') }}</label>
                            <input type="number" step="0.01" name="reserve_price" class="form-control" placeholder="0.00" value="{{ $auction->reserve_price }}">
                            <div class="invalid-feedback error-reserve_price"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Min Bid Increment') }}</label>
                            <input type="number" step="0.01" name="min_bid_increment" class="form-control" placeholder="0.00" value="{{ $auction->min_bid_increment }}" required>
                            <div class="invalid-feedback error-min_bid_increment"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Buy Now Price') }} <span class="text-muted fw-normal ms-1">({{ __('Optional') }})</span></label>
                            <input type="number" step="0.01" name="buy_now_price" class="form-control" placeholder="0.00" value="{{ $auction->buy_now_price }}">
                            <div class="invalid-feedback error-buy_now_price"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Commission Rate (%)') }}</label>
                            <input type="number" step="0.01" name="commission_rate" class="form-control" placeholder="0.00" value="{{ $auction->commission_rate }}">
                            <div class="invalid-feedback error-commission_rate"></div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('Deposit Amount') }}</label>
                            <input type="number" step="0.01" name="deposit_amount" class="form-control" placeholder="0.00" value="{{ $auction->deposit_amount }}" required>
                            <div class="invalid-feedback error-deposit_amount"></div>
                        </div>
                    </div>
                </div>

                <!-- Switches Card -->
                <div class="premium-card mb-4">
                    <div class="card-body-premium" style="padding: 20px;">
                        <label class="custom-toggle mb-3">
                            <div class="toggle-content">
                                <div class="icon-wrapper bg-light-warning" style="width: 36px; height: 36px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                </div>
                                <span class="toggle-title">{{ __('Deposit Required?') }}</span>
                            </div>
                            <div class="switch">
                                <input type="checkbox" name="deposit_required" value="1" {{ $auction->deposit_required ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </div>
                        </label>
                        
                        <label class="custom-toggle mb-0">
                            <div class="toggle-content">
                                <div class="icon-wrapper bg-light-danger" style="width: 36px; height: 36px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                                <span class="toggle-title">{{ __('Featured Auction?') }}</span>
                            </div>
                            <div class="switch">
                                <input type="checkbox" name="is_featured" value="1" {{ $auction->is_featured ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/ar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-init').select2({
            dir: dir
        });

        // Initialize Flatpickr for datetime
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.datepicker', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });
        }

        // Initialize Summernote
        if (typeof $.fn.summernote !== 'undefined') {
            $('.summernote').summernote({
                height: 250,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        // Handle Form Submit via AJAX
        $('#editAuctionForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            let btn = $('#submitBtn');
            let spinner = btn.find('.spinner-border');
            let originalText = btn.find('span').last().text();
            
            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            btn.find('span').last().text("{{ __('Updating...') }}");

            $.ajax({
                url: url,
                method: 'POST', // Has @method('PUT') inside formData
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.auctions.index') }}";
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    btn.find('span').last().text(originalText);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            let inputName = field.includes('images.') ? 'images[]' : field;
                            let input = $('[name="' + inputName + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                                let errorClass = field.includes('images') ? '.error-images' : '.error-' + field;
                                $(errorClass).text(errors[field][0]).show();
                            }
                        }
                        toastr.error("{{ __('Please fix the validation errors.') }}");
                    } else {
                        toastr.error("{{ __('An unexpected error occurred.') }}");
                    }
                }
            });
        });
    });

    function removeExistingImage(id) {
        document.getElementById('existing_img_' + id).style.display = 'none';
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_images[]';
        input.value = id;
        document.getElementById('deleted_images_inputs').appendChild(input);
    }

    function handleFilePreview(input, previewId) {
        const previewContainer = document.getElementById(previewId);
        if (!previewContainer) return;

        previewContainer.innerHTML = '';
        previewContainer.style.display = 'flex';

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const thumbDiv = document.createElement('div');
                    thumbDiv.style.position = 'relative';
                    thumbDiv.style.width = '80px';
                    thumbDiv.style.height = '80px';
                    thumbDiv.style.borderRadius = '8px';
                    thumbDiv.style.overflow = 'hidden';
                    thumbDiv.style.border = '1px solid var(--border)';
                    thumbDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    
                    if (index === 0 && !document.querySelector('.existing-image-wrapper:not([style*="display: none"])')) {
                        const badge = document.createElement('span');
                        badge.innerText = 'Primary';
                        badge.style.position = 'absolute';
                        badge.style.bottom = '0';
                        badge.style.left = '0';
                        badge.style.right = '0';
                        badge.style.background = 'rgba(59, 130, 246, 0.8)';
                        badge.style.color = '#fff';
                        badge.style.fontSize = '10px';
                        badge.style.textAlign = 'center';
                        badge.style.padding = '2px 0';
                        thumbDiv.appendChild(badge);
                    }

                    thumbDiv.appendChild(img);
                    previewContainer.appendChild(thumbDiv);
                };

                reader.readAsDataURL(file);
            });
            previewContainer.classList.add('has-file');
        } else {
            previewContainer.style.display = 'none';
            previewContainer.classList.remove('has-file');
        }
    }
</script>
@endsection
