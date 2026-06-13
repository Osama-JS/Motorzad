@extends('layouts.admin')

@section('title', __('Edit Auction'))

@section('css')
<style>
    /* Premium UI Enhancements */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .premium-card {
        background: #ffffff;
        border: none;
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
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 700;
        font-size: 1.15rem;
        color: #1e293b;
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
    .bg-light-primary { background: #eff6ff; color: #3b82f6; }
    .bg-light-success { background: #f0fdf4; color: #22c55e; }
    .bg-light-warning { background: #fffbeb; color: #f59e0b; }
    .bg-light-danger { background: #fef2f2; color: #ef4444; }
    .bg-light-purple { background: #faf5ff; color: #a855f7; }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }
    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    
    /* File Upload */
    .file-upload-zone {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload-zone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
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
        color: #94a3b8;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .upload-text {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }
    .upload-highlight { color: #3b82f6; }
    .upload-hint { font-size: 0.85rem; color: #64748b; }
    
    .file-upload-preview { 
        display: none; 
        margin-top: 16px; 
        align-items: center; 
        gap: 16px; 
        padding: 16px; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        background: #fff; 
    }
    .file-upload-preview.has-file { display: flex; }
    .preview-thumb { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
    .preview-info { flex: 1; min-width: 0; }
    .preview-name { font-size: 0.95rem; font-weight: 600; color: #1e293b; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .preview-size { font-size: 0.8rem; color: #64748b; }

    /* Custom Toggle Switch */
    .custom-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .custom-toggle:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .toggle-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .toggle-title {
        font-weight: 600;
        color: #334155;
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
        background-color: #cbd5e1;
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
    
    /* CKEditor Custom Styling for Premium look */
    .ck-editor__editable_inline {
        min-height: 250px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        border-color: #e2e8f0 !important;
        background: #ffffff !important;
    }
    .ck.ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        border-color: #e2e8f0 !important;
        background: #f8fafc !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 style="font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ __('Edit Auction') }}</h1>
        <div class="breadcrumb" style="color: #64748b; font-size: 0.95rem;">
            <a href="{{ route('admin.dashboard') }}" style="color: #3b82f6; text-decoration: none;">{{ __('Dashboard') }}</a> 
            <span class="mx-2">/</span> 
            <a href="{{ route('admin.auctions.index') }}" style="color: #3b82f6; text-decoration: none;">{{ __('Auctions') }}</a> 
            <span class="mx-2">/</span> 
            {{ __('Edit') }}
        </div>
    </div>
    <a href="{{ route('admin.auctions.index') }}" class="btn btn-light" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back') }}
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger" style="border-radius: 12px; padding: 20px; background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
    <div class="d-flex align-items-center mb-2" style="font-weight: 700;">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ __('Please fix the following errors:') }}
    </div>
    <ul class="mb-0 ps-4">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.auctions.update', $auction->id) }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" name="title_ar" id="title_ar" class="form-control" placeholder="{{ __('Enter auction title in Arabic') }}" value="{{ old('title_ar', $auction->title_ar) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Title (English)') }}</label>
                            <input type="text" name="title_en" id="title_en" class="form-control" placeholder="{{ __('Enter auction title in English') }}" value="{{ old('title_en', $auction->title_en) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Location (Arabic)') }}</span>
                                <x-translate-button from="#location_ar" to="#location_en" />
                            </label>
                            <input type="text" name="location_ar" id="location_ar" class="form-control" placeholder="مثال: الرياض، المملكة العربية السعودية" value="{{ old('location_ar', $auction->location_ar) }}">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label">{{ __('Location (English)') }}</label>
                            <input type="text" name="location_en" id="location_en" class="form-control" placeholder="Example: Riyadh, Saudi Arabia" value="{{ old('location_en', $auction->location_en) }}">
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
                        <select name="vehicle_id" class="form-control form-select" required>
                            <option value="">{{ __('Select Vehicle...') }}</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $auction->vehicle_id) == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->title }} ({{ $vehicle->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('Auction Main Image') }} <span class="text-muted fw-normal ms-1">({{ __('Optional - Leave blank to keep current') }})</span></label>
                        <div class="file-upload-zone" id="addImageZone">
                            <input type="file" name="image" accept="image/*" onchange="handleFilePreview(this, 'addImagePreview')">
                            <div class="upload-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <div class="upload-text">{{ __('Drag the image here or') }} <span class="upload-highlight">{{ __('click to browse') }}</span></div>
                            <div class="upload-hint">{{ __('Supported formats: PNG, JPG, WEBP. Max size: 2MB') }}</div>
                        </div>
                        <div class="file-upload-preview {{ $auction->image ? 'has-file' : '' }}" id="addImagePreview">
                            <img class="preview-thumb" src="{{ $auction->image ? asset('storage/' . $auction->image) : '' }}" alt="preview">
                            <div class="preview-info">
                                <div class="preview-name">{{ $auction->image ? __('Current Image') : '' }}</div>
                                <div class="preview-size"></div>
                            </div>
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
                        <div class="col-md-6 form-group mb-3 mb-md-0">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Description (Arabic)') }}</span>
                                <x-translate-button from="#description_ar" to="#description_en" type="editor" />
                            </label>
                            <textarea name="description_ar" id="description_ar" class="form-control" rows="5" placeholder="{{ __('Enter auction description in Arabic...') }}">{{ old('description_ar', $auction->description_ar) }}</textarea>
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" id="description_en" class="form-control" rows="5" placeholder="{{ __('Enter auction description in English...') }}">{{ old('description_en', $auction->description_en) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="sidebar-sticky">
                
                <!-- Status & Publish -->
                <div class="premium-card mb-4" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <div class="card-body-premium">
                        <div class="form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control form-select" required style="font-weight: 600;">
                                <option value="draft" {{ old('status', $auction->status) == 'draft' ? 'selected' : '' }}>📝 {{ __('Draft') }}</option>
                                <option value="scheduled" {{ old('status', $auction->status) == 'scheduled' ? 'selected' : '' }}>📅 {{ __('Scheduled') }}</option>
                                <option value="live" {{ old('status', $auction->status) == 'live' ? 'selected' : '' }}>🟢 {{ __('Live') }}</option>
                                <option value="completed" {{ old('status', $auction->status) == 'completed' ? 'selected' : '' }}>✅ {{ __('Completed') }}</option>
                                <option value="cancelled" {{ old('status', $auction->status) == 'cancelled' ? 'selected' : '' }}>❌ {{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-publish">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ __('Update Auction') }}
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
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', $auction->start_time ? $auction->start_time->format('Y-m-d\TH:i') : '') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('End Time') }}</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time', $auction->end_time ? $auction->end_time->format('Y-m-d\TH:i') : '') }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('Auto Extend (Minutes)') }}</label>
                            <input type="number" name="auto_extend_minutes" class="form-control" value="{{ old('auto_extend_minutes', $auction->auto_extend_minutes) }}" placeholder="0">
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
                                <input type="number" step="0.01" name="start_price" class="form-control" placeholder="0.00" value="{{ old('start_price', $auction->start_price) }}" required>
                                <span class="input-group-text bg-light text-muted border-start-0">{{ config('app.currency', 'SAR') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Reserve Price') }}</label>
                            <input type="number" step="0.01" name="reserve_price" class="form-control" placeholder="0.00" value="{{ old('reserve_price', $auction->reserve_price) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Min Bid Increment') }}</label>
                            <input type="number" step="0.01" name="min_bid_increment" class="form-control" placeholder="0.00" value="{{ old('min_bid_increment', $auction->min_bid_increment) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Buy Now Price') }} <span class="text-muted fw-normal ms-1">({{ __('Optional') }})</span></label>
                            <input type="number" step="0.01" name="buy_now_price" class="form-control" placeholder="0.00" value="{{ old('buy_now_price', $auction->buy_now_price) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Commission Rate (%)') }}</label>
                            <input type="number" step="0.01" name="commission_rate" class="form-control" placeholder="0.00" value="{{ old('commission_rate', $auction->commission_rate) }}">
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('Deposit Amount') }}</label>
                            <input type="number" step="0.01" name="deposit_amount" class="form-control" placeholder="0.00" value="{{ old('deposit_amount', $auction->deposit_amount) }}" required>
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
                                <input type="checkbox" name="deposit_required" value="1" {{ old('deposit_required', $auction->deposit_required) ? 'checked' : '' }}>
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
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $auction->is_featured) ? 'checked' : '' }}>
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
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    const editors = {};
    window.editors = editors;

    function initializeEditor(selector) {
        ClassicEditor
            .create(document.querySelector(selector), {
                language: '{{ app()->getLocale() }}',
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
            })
            .then(editor => {
                editors[selector] = editor;
            })
            .catch(error => {
                console.error(error);
            });
    }

    $(document).ready(function() {
        if (document.querySelector('#description_ar')) {
            initializeEditor('#description_ar');
        }
        if (document.querySelector('#description_en')) {
            initializeEditor('#description_en');
        }
    });

    function handleFilePreview(input, previewId) {
        const previewContainer = document.getElementById(previewId);
        if (!previewContainer) return;

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const thumb = previewContainer.querySelector('.preview-thumb');
                const nameEl = previewContainer.querySelector('.preview-name');
                const sizeEl = previewContainer.querySelector('.preview-size');

                if (thumb) thumb.src = e.target.result;
                if (nameEl) nameEl.textContent = file.name;
                if (sizeEl) sizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

                previewContainer.classList.add('has-file');
            };

            reader.readAsDataURL(file);
        } else {
            const thumb = previewContainer.querySelector('.preview-thumb');
            const nameEl = previewContainer.querySelector('.preview-name');
            const sizeEl = previewContainer.querySelector('.preview-size');

            if (thumb) thumb.src = '';
            if (nameEl) nameEl.textContent = '';
            if (sizeEl) sizeEl.textContent = '';

            previewContainer.classList.remove('has-file');
        }
    }
</script>
@endsection
