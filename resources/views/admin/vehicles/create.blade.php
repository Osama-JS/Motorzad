@extends('layouts.admin')

@section('title', __('Add New Vehicle'))

@section('css')
<style>
    /* Premium UI & Theme Styling */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.85);
        --glass-border: rgba(51, 65, 85, 0.8);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .premium-card {
        background: var(--bg-card);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: var(--shadow-premium);
        margin-bottom: 24px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
    }
    
    .card-header-premium {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 24px;
        background: rgba(248, 250, 252, 0.4);
        border-bottom: 1px solid var(--glass-border);
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--text);
    }
    [data-theme="dark"] .card-header-premium {
        background: rgba(15, 23, 42, 0.3);
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
    .bg-light-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
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
        color: var(--text-secondary);
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
        background-color: var(--bg-card-solid);
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 4px var(--brand-blue-glow);
        color: var(--text);
    }
    
    /* File Upload */
    .file-upload-zone {
        position: relative;
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 2.5rem 2rem;
        text-align: center;
        background: var(--bg-input);
        transition: all 0.2s;
        cursor: pointer;
    }
    .file-upload-zone:hover {
        border-color: var(--brand-blue);
        background: var(--bg-hover);
    }
    .file-upload-zone input[type="file"] {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .upload-icon {
        color: var(--text-muted);
        margin-bottom: 12px;
    }
    .upload-icon svg { width: 40px; height: 40px; }
    .upload-text {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }
    .upload-highlight {
        color: var(--brand-blue);
    }
    .upload-hint {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    /* Checkbox Grid */
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        background: var(--bg-input);
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 6px;
    }
    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .check-label {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-secondary);
    }

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
        background: linear-gradient(135deg, var(--primary), #4f46e5);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
        transition: all 0.3s;
    }
    .btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(229, 62, 62, 0.3);
    }
    
    /* CKEditor Custom Styling for Premium look */
    .ck-editor__editable_inline {
        min-height: 250px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        border-color: var(--border) !important;
        background: var(--bg-card-solid) !important;
        color: var(--text) !important;
    }
    .ck.ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        border-color: var(--border) !important;
        background: var(--bg-input) !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:focus {
        border-color: var(--brand-blue) !important;
        box-shadow: 0 0 0 3px var(--brand-blue-glow) !important;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 style="font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ __('Add New Vehicle') }}</h1>
        <div class="breadcrumb" style="color: #64748b; font-size: 0.95rem;">
            <a href="{{ route('admin.dashboard') }}" style="color: #3b82f6; text-decoration: none;">{{ __('Dashboard') }}</a> 
            <span class="mx-2">/</span> 
            <a href="{{ route('admin.vehicles.index') }}" style="color: #3b82f6; text-decoration: none;">{{ __('Vehicles') }}</a> 
            <span class="mx-2">/</span> 
            {{ __('Create') }}
        </div>
    </div>
    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back') }}
    </a>
</div>

<form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            
            <!-- Basic Information Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-2.2-3.3C13 5.6 12 5 10.8 5H5.6c-.8 0-1.6.5-1.9 1.2l-.9 2.1C2.3 9.5 2 10.8 2 12.1V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    </div>
                    {{ __('Manufacturer & Model') }}
                </div>
                <div class="card-body-premium">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Manufacturer (Make)') }}</label>
                            <input type="text" name="make" class="form-control" placeholder="{{ __('Example: Toyota') }}" value="{{ old('make') }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Model') }}</label>
                            <input type="text" name="model" class="form-control" placeholder="{{ __('Example: Camry') }}" value="{{ old('model') }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Year') }}</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('VIN Number') }}</label>
                            <input type="text" name="vin_number" class="form-control" placeholder="VIN" value="{{ old('vin_number') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Color') }}</label>
                            <input type="text" name="color" class="form-control" placeholder="{{ __('Example: Black') }}" value="{{ old('color') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Plate Number') }}</label>
                            <input type="text" name="plate_number" class="form-control" placeholder="A B C 1 2 3 4" value="{{ old('plate_number') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Specifications Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    </div>
                    {{ __('Technical Specifications') }}
                </div>
                <div class="card-body-premium">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Mileage') }}</label>
                            <input type="number" name="mileage" class="form-control" placeholder="{{ __('km') }}" value="{{ old('mileage') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Fuel Type') }}</label>
                            <select name="fuel_type" class="form-select">
                                <option value="">{{ __('Not Specified') }}</option>
                                <option value="petrol" {{ old('fuel_type') === 'petrol' ? 'selected' : '' }}>{{ __('Petrol') }}</option>
                                <option value="diesel" {{ old('fuel_type') === 'diesel' ? 'selected' : '' }}>{{ __('Diesel') }}</option>
                                <option value="electric" {{ old('fuel_type') === 'electric' ? 'selected' : '' }}>{{ __('Electric') }}</option>
                                <option value="hybrid" {{ old('fuel_type') === 'hybrid' ? 'selected' : '' }}>{{ __('Hybrid') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Transmission') }}</label>
                            <select name="transmission" class="form-select">
                                <option value="">{{ __('Not Specified') }}</option>
                                <option value="automatic" {{ old('transmission') === 'automatic' ? 'selected' : '' }}>{{ __('Automatic') }}</option>
                                <option value="manual" {{ old('transmission') === 'manual' ? 'selected' : '' }}>{{ __('Manual') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Engine Capacity') }}</label>
                            <input type="text" name="engine_capacity" class="form-control" placeholder="{{ __('Example: 2.5L') }}" value="{{ old('engine_capacity') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Cylinders') }}</label>
                            <input type="number" name="cylinders" class="form-control" placeholder="{{ __('Example: 4') }}" value="{{ old('cylinders') }}">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('Features') }}</label>
                        <div class="checkbox-grid">
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="sunroof" {{ is_array(old('features')) && in_array('sunroof', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Sunroof') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="leather_seats" {{ is_array(old('features')) && in_array('leather_seats', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Leather Seats') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="rear_camera" {{ is_array(old('features')) && in_array('rear_camera', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Rear Camera') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="sensors" {{ is_array(old('features')) && in_array('sensors', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Sensors') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="navigation_system" {{ is_array(old('features')) && in_array('navigation_system', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Navigation System') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="cruise_control" {{ is_array(old('features')) && in_array('cruise_control', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Cruise Control') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="keyless_entry" {{ is_array(old('features')) && in_array('keyless_entry', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Keyless Entry') }}</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="features[]" value="bluetooth" {{ is_array(old('features')) && in_array('bluetooth', old('features')) ? 'checked' : '' }}>
                                <span class="check-label">{{ __('Bluetooth') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Images Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>
                    {{ __('Vehicle Images') }}
                </div>
                <div class="card-body-premium">
                    <div class="file-upload-zone">
                        <input type="file" name="images[]" accept="image/*" multiple onchange="handleMultipleFilesPreview(this, 'vehicleImagesPreview', 'primary_image_index')">
                        <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
                        <div class="file-upload-content">
                            <div class="upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <div class="upload-text">{{ __('Drag the images here or') }} <span class="upload-highlight">{{ __('click to choose') }}</span></div>
                            <div class="upload-hint">{{ __('PNG, JPG, WEBP — Max size 2MB per image') }}</div>
                        </div>
                    </div>
                    <div class="row g-2 mt-3" id="vehicleImagesPreview"></div>
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
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary translate-btn" data-from="#description_ar" data-to="#description_en" data-type="editor" style="text-decoration: none; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    {{ __('Translate to English') }}
                                </button>
                            </label>
                            <textarea name="description_ar" id="description_ar" class="form-control" rows="5" placeholder="{{ __('Enter vehicle description in Arabic...') }}">{{ old('description_ar') }}</textarea>
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="form-label">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" id="description_en" class="form-control" rows="5" placeholder="{{ __('Enter vehicle description in English...') }}">{{ old('description_en') }}</textarea>
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
                    <div class="card-header-premium" style="padding: 16px 20px;">
                        <div class="icon-wrapper bg-light-success" style="width: 32px; height: 32px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <span style="font-size: 1rem;">{{ __('Publish Settings') }}</span>
                    </div>
                    <div class="card-body-premium" style="padding: 20px;">
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select" required>
                                <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>🟢 {{ __('Approved') }}</option>
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>📅 {{ __('Pending Review') }}</option>
                                <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>❌ {{ __('Rejected') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                {{ __('Issues / Defects') }}
                            </label>
                            <textarea name="issues" class="form-control" rows="3" placeholder="{{ __('List any issues or defects. Leave empty if none.') }}">{{ old('issues') }}</textarea>
                        </div>

                        <hr style="margin: 20px 0; border-color: #e2e8f0;">

                        <button type="submit" class="btn-publish mb-2">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ __('Save Vehicle') }}
                        </button>
                        
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light w-100" style="padding: 12px; border-radius: 12px; font-weight: 600; border: 1px solid #cbd5e1;">
                            {{ __('Cancel') }}
                        </a>

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

        // Translation logic
        async function translateText(text, fromLang = 'ar', toLang = 'en') {
            if (!text || text.trim() === '') return '';
            try {
                const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${fromLang}&tl=${toLang}&dt=t&q=${encodeURIComponent(text)}`;
                const response = await fetch(url);
                const data = await response.json();
                if (data && data[0]) {
                    return data[0].map(x => x[0]).join('');
                }
                return text;
            } catch (e) {
                console.error('Translation error:', e);
                throw e;
            }
        }

        async function translateHtml(htmlStr, fromLang = 'ar', toLang = 'en') {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlStr;

            const textNodes = [];
            function findTextNodes(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    if (node.nodeValue.trim() !== '') {
                        textNodes.push(node);
                    }
                } else {
                    for (let child of node.childNodes) {
                        findTextNodes(child);
                    }
                }
            }
            findTextNodes(tempDiv);

            for (let node of textNodes) {
                try {
                    const translated = await translateText(node.nodeValue, fromLang, toLang);
                    node.nodeValue = translated;
                } catch (err) {
                    console.error('Failed to translate node:', node.nodeValue, err);
                }
            }

            return tempDiv.innerHTML;
        }

        $(document).on('click', '.translate-btn', async function() {
            const btn = $(this);
            const fromSelector = btn.data('from');
            const toSelector = btn.data('to');
            const isEditor = btn.data('type') === 'editor';
            
            let sourceText = '';
            if (isEditor) {
                const editorInstance = editors[fromSelector];
                if (editorInstance) {
                    sourceText = editorInstance.getData();
                }
            } else {
                sourceText = $(fromSelector).val();
            }

            if (!sourceText || sourceText.trim() === '') {
                toastr.warning('{{ __("Please enter text first") }}');
                return;
            }

            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{ __("Translating...") }}');

            try {
                let translated = '';
                if (isEditor) {
                    translated = await translateHtml(sourceText, 'ar', 'en');
                    const targetEditorInstance = editors[toSelector];
                    if (targetEditorInstance) {
                        targetEditorInstance.setData(translated);
                    }
                } else {
                    translated = await translateText(sourceText, 'ar', 'en');
                    $(toSelector).val(translated);
                }
                toastr.success('{{ __("Translated successfully") }}');
            } catch (error) {
                toastr.error('{{ __("Translation failed") }}');
            } finally {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Multiple Images Preview Handler
    function handleMultipleFilesPreview(input, previewId, primaryInputId) {
        const previewContainer = document.getElementById(previewId);
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        const files = input.files;
        if (files && files.length > 0) {
            document.getElementById(primaryInputId).value = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3 vehicle-img-preview-col';
                    col.dataset.index = i;
                    col.style.position = 'relative';
                    col.style.cursor = 'pointer';

                    const borderStyle = (i === 0) ? '3px solid #3b82f6' : '1px solid #e2e8f0';
                    const badgeDisplay = (i === 0) ? 'block' : 'none';

                    col.innerHTML = `
                        <div class="card p-1 text-center bg-dark" style="border: ${borderStyle}; border-radius: 8px; position: relative; overflow: hidden; height:120px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius: 6px;" alt="">
                            <span class="badge bg-primary primary-badge" style="position:absolute; top:8px; right:8px; display: ${badgeDisplay}; font-size:10px;">{{ __('Primary') }}</span>
                            <div class="hover-overlay" style="position:absolute; inset:0; background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; opacity:0; transition:0.2s;">
                                <span class="text-white" style="font-size:11px; font-weight:bold;">{{ __('Set Primary') }}</span>
                            </div>
                        </div>
                    `;

                    const card = col.querySelector('.card');
                    const overlay = col.querySelector('.hover-overlay');
                    col.addEventListener('mouseenter', () => overlay.style.opacity = '1');
                    col.addEventListener('mouseleave', () => overlay.style.opacity = '0');

                    col.addEventListener('click', function() {
                        document.getElementById(primaryInputId).value = i;
                        previewContainer.querySelectorAll('.vehicle-img-preview-col').forEach(el => {
                            el.querySelector('.card').style.border = '1px solid #e2e8f0';
                            el.querySelector('.primary-badge').style.display = 'none';
                        });
                        card.style.border = '3px solid #3b82f6';
                        col.querySelector('.primary-badge').style.display = 'block';
                    });

                    previewContainer.appendChild(col);
                };

                reader.readAsDataURL(file);
            }
        }
    }
</script>
@endsection
