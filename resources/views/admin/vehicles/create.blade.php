@extends('layouts.admin')

@section('title', __('Add New Vehicle'))

@section('css')
<style>
    /* Car Diagram Styling */
    .car-part {
        cursor: pointer;
        transition: fill 0.2s, stroke 0.2s, filter 0.2s;
    }
    .car-part:hover {
        fill: rgba(59, 130, 246, 0.15);
        stroke: #3b82f6;
    }
    .car-part.damage-scratch {
        fill: rgba(245, 158, 11, 0.3) !important;
        stroke: #f59e0b !important;
        filter: drop-shadow(0px 0px 4px rgba(245, 158, 11, 0.4));
    }
    .car-part.damage-dent {
        fill: rgba(239, 68, 68, 0.3) !important;
        stroke: #ef4444 !important;
        filter: drop-shadow(0px 0px 4px rgba(239, 68, 68, 0.4));
    }
    .car-part.damage-repainted {
        fill: rgba(16, 185, 129, 0.3) !important;
        stroke: #10b981 !important;
        filter: drop-shadow(0px 0px 4px rgba(16, 185, 129, 0.4));
    }
    .car-part.damage-broken {
        fill: rgba(168, 85, 247, 0.3) !important;
        stroke: #a855f7 !important;
        filter: drop-shadow(0px 0px 4px rgba(168, 85, 247, 0.4));
    }
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
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Manufacturer (Make) - Arabic') }}</span>
                                <x-translate-button from="#make_ar" to="#make_en" />
                            </label>
                            <input type="text" name="make_ar" id="make_ar" class="form-control" placeholder="مثال: تويوتا" value="{{ old('make_ar') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Manufacturer (Make) - English') }}</label>
                            <input type="text" name="make_en" id="make_en" class="form-control" placeholder="Example: Toyota" value="{{ old('make_en') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Model - Arabic') }}</span>
                                <x-translate-button from="#model_ar" to="#model_en" />
                            </label>
                            <input type="text" name="model_ar" id="model_ar" class="form-control" placeholder="مثال: كامري" value="{{ old('model_ar') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Model - English') }}</label>
                            <input type="text" name="model_en" id="model_en" class="form-control" placeholder="Example: Camry" value="{{ old('model_en') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span>{{ __('Color - Arabic') }}</span>
                                <x-translate-button from="#color_ar" to="#color_en" />
                            </label>
                            <input type="text" name="color_ar" id="color_ar" class="form-control" placeholder="مثال: أسود" value="{{ old('color_ar') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('Color - English') }}</label>
                            <input type="text" name="color_en" id="color_en" class="form-control" placeholder="Example: Black" value="{{ old('color_en') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Year') }}</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('VIN Number') }}</label>
                            <div class="input-group">
                                <input type="text" name="vin_number" id="vinNumberInput" class="form-control" placeholder="VIN" value="{{ old('vin_number') }}">
                                <button type="button" id="btnDecodeVin" class="btn btn-outline-secondary" style="border:1px solid #cbd5e1; border-top-right-radius:10px; border-bottom-right-radius:10px;" title="{{ __('Decode VIN') }}">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </button>
                            </div>
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
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Engine Capacity') }}</label>
                            <input type="text" name="engine_capacity" class="form-control" placeholder="{{ __('Example: 2.5L') }}" value="{{ old('engine_capacity') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Cylinders') }}</label>
                            <input type="number" name="cylinders" class="form-control" placeholder="{{ __('Example: 4') }}" value="{{ old('cylinders') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('Country of Origin') }}</label>
                            <input type="text" name="country_of_origin" class="form-control" placeholder="{{ __('Example: Japan') }}" value="{{ old('country_of_origin') }}">
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
                                <x-translate-button from="#description_ar" to="#description_en" type="editor" />
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
            
            <!-- Interactive Damage & Wear Plotter Card -->
            <div class="premium-card">
                <div class="card-header-premium">
                    <div class="icon-wrapper bg-light-warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    {{ __('Interactive Wear & Damage Plotter') }}
                </div>
                <div class="card-body-premium">
                    <p class="text-muted mb-4" style="font-size:0.9rem;">
                        {{ __('Click on the vehicle parts in the diagram to register damages (scratches, dents, repaints, or broken parts).') }}
                    </p>
                    <div class="row">
                        <!-- Car Diagram Column -->
                        <div class="col-md-7 text-center mb-4 mb-md-0" style="background: rgba(30, 41, 59, 0.03); padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border); min-height: 380px; display:flex; align-items:center; justify-content:center;">
                            <svg viewBox="0 0 600 350" class="car-diagram-svg" style="width:100%; height:auto; max-width:550px;">
                                <defs>
                                    <filter id="glow-damage" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3" result="blur" />
                                        <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                    </filter>
                                </defs>
                                <!-- Outer Frame -->
                                <rect x="5" y="5" width="590" height="340" rx="15" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="2"/>
                                
                                <!-- Top-Down View Car Body -->
                                <!-- Front Bumper -->
                                <path d="M 240,60 C 270,55 330,55 360,60 L 360,75 C 330,72 270,72 240,75 Z" class="car-part" data-part="front_bumper" data-label-ar="صدام أمامي" data-label-en="Front Bumper" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Hood -->
                                <path d="M 243,78 L 357,78 L 350,130 L 250,130 Z" class="car-part" data-part="hood" data-label-ar="الكبوت (غطاء المحرك)" data-label-en="Hood" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Windshield -->
                                <path d="M 252,133 L 348,133 L 342,160 L 258,160 Z" class="car-part" data-part="windshield" data-label-ar="الزجاج الأمامي" data-label-en="Windshield" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Roof -->
                                <rect x="256" y="163" width="88" height="70" rx="5" class="car-part" data-part="roof" data-label-ar="السقف" data-label-en="Roof" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Rear Windshield -->
                                <path d="M 258,236 L 342,236 L 348,260 L 252,260 Z" class="car-part" data-part="rear_windshield" data-label-ar="الزجاج الخلفي" data-label-en="Rear Windshield" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Trunk / Tailgate -->
                                <path d="M 250,263 L 350,263 L 355,310 L 245,310 Z" class="car-part" data-part="trunk" data-label-ar="الشنطة" data-label-en="Trunk" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Rear Bumper -->
                                <path d="M 240,313 C 270,318 330,318 360,313 L 360,325 C 330,322 270,322 240,325 Z" class="car-part" data-part="rear_bumper" data-label-ar="صدام خلفي" data-label-en="Rear Bumper" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                
                                <!-- Left Front Fender -->
                                <path d="M 200,65 Q 235,68 238,100 L 238,125 L 205,125 Z" class="car-part" data-part="left_fender_front" data-label-ar="رفرف أمامي أيسر" data-label-en="Left Front Fender" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Left Front Door -->
                                <rect x="205" y="128" width="46" height="50" class="car-part" data-part="left_door_front" data-label-ar="باب أمامي أيسر" data-label-en="Left Front Door" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Left Rear Door -->
                                <rect x="205" y="181" width="46" height="50" class="car-part" data-part="left_door_rear" data-label-ar="باب خلفي أيسر" data-label-en="Left Rear Door" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Left Rear Fender -->
                                <path d="M 205,234 L 238,234 L 238,270 Q 235,302 200,305 Z" class="car-part" data-part="left_fender_rear" data-label-ar="رفرف خلفي أيسر" data-label-en="Left Rear Fender" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                
                                <!-- Right Front Fender -->
                                <path d="M 400,65 Q 365,68 362,100 L 362,125 L 395,125 Z" class="car-part" data-part="right_fender_front" data-label-ar="رفرف أمامي أيمن" data-label-en="Right Front Fender" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Right Front Door -->
                                <rect x="349" y="128" width="46" height="50" class="car-part" data-part="right_door_front" data-label-ar="باب أمامي أيمن" data-label-en="Right Front Door" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Right Rear Door -->
                                <rect x="349" y="181" width="46" height="50" class="car-part" data-part="right_door_rear" data-label-ar="باب خلفي أيمن" data-label-en="Right Rear Door" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                <!-- Right Rear Fender -->
                                <path d="M 395,234 L 362,234 L 362,270 Q 365,302 400,305 Z" class="car-part" data-part="right_fender_rear" data-label-ar="رفرف خلفي أيمن" data-label-en="Right Rear Fender" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5" />
                                
                                <!-- Wheels -->
                                <rect x="180" y="85" width="20" height="35" rx="5" fill="#334155" />
                                <rect x="400" y="85" width="20" height="35" rx="5" fill="#334155" />
                                <rect x="180" y="245" width="20" height="35" rx="5" fill="#334155" />
                                <rect x="400" y="245" width="20" height="35" rx="5" fill="#334155" />
                            </svg>
                        </div>
                        
                        <!-- Side Controls Column -->
                        <div class="col-md-5">
                            <div class="p-3 border rounded" style="background: var(--bg-card-solid); border-color: var(--border);">
                                <h6 class="mb-3" style="font-weight:700; color: var(--text);">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-primary me-1"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    {{ __('Add wear / damage details') }}
                                </h6>
                                
                                <div id="selectedPartAlert" class="alert alert-secondary py-2 px-3 mb-3" style="font-size:0.85rem; font-weight:600; border-radius:10px;">
                                    {{ __('Selected part:') }} <span id="selectedPartLabel" class="text-primary">{{ __('None') }}</span>
                                </div>
                                
                                <input type="hidden" id="activePartId" value="">
                                
                                <div class="form-group mb-3">
                                    <label class="form-label" style="font-size:0.85rem;">{{ __('Damage / Defect Type') }}</label>
                                    <select id="damageTypeSelect" class="form-select" disabled>
                                        <option value="">-- {{ __('Select Type') }} --</option>
                                        <option value="scratch">{{ __('Scratch / Superficial Wear') }} ({{ __('Orange') }})</option>
                                        <option value="dent">{{ __('Dent / Body Damage') }} ({{ __('Red') }})</option>
                                        <option value="repainted">{{ __('Repainted panel') }} ({{ __('Green') }})</option>
                                        <option value="broken">{{ __('Broken / Cracked') }} ({{ __('Purple') }})</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label" style="font-size:0.85rem;">{{ __('Notes (Optional)') }}</label>
                                    <textarea id="damageNoteText" class="form-control" rows="2" placeholder="{{ __('E.g. Minor scratch 5cm') }}" disabled></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" id="btnAddDamage" class="btn btn-primary btn-sm flex-grow-1 py-2 rounded-pill" style="font-weight:700;" disabled>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><polyline points="20 6 9 17 4 12"/></svg>
                                        {{ __('Apply') }}
                                    </button>
                                    <button type="button" id="btnDeleteDamage" class="btn btn-danger btn-sm py-2 px-3 rounded-pill" style="font-weight:700; display:none;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- List of issues -->
                            <div class="mt-4">
                                <h6 style="font-weight:700; font-size:0.9rem; color: var(--text-secondary);" class="mb-3">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    {{ __('Plotted Issues List') }}
                                </h6>
                                <div class="table-responsive" style="max-height:180px; overflow-y:auto; border: 1px solid var(--border); border-radius:8px;">
                                    <table class="table table-sm table-hover mb-0" style="font-size:0.8rem; vertical-align: middle;">
                                        <thead class="table-dark" style="position: sticky; top:0;">
                                            <tr>
                                                <th>{{ __('Part') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="damageTableBody">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">{{ __('No issues added yet.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="damage_points" id="damagePointsInput" value="{{ old('damage_points') }}">
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
                            <label class="form-label d-flex justify-content-between align-items-center w-100">
                                <span class="d-flex align-items-center">
                                    <svg class="label-icon me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    {{ __('Issues / Defects - Arabic') }}
                                </span>
                                <x-translate-button from="#issues_ar" to="#issues_en" />
                            </label>
                            <textarea name="issues_ar" id="issues_ar" class="form-control" rows="2" placeholder="مثال: خدوش في الباب الأيسر">{{ old('issues_ar') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                {{ __('Issues / Defects - English') }}
                            </label>
                            <textarea name="issues_en" id="issues_en" class="form-control" rows="2" placeholder="Example: Scratches on the left door">{{ old('issues_en') }}</textarea>
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

        // VIN Decoder API Integration
        $('#btnDecodeVin').click(function() {
            var vin = $('input[name="vin_number"]').val();
            if (!vin || vin.trim() === '') {
                toastr.warning('{{ __("Please enter a VIN number first") }}');
                return;
            }

            var btn = $(this);
            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            toastr.info('{{ __("Decoding VIN...") }}');

            $.ajax({
                url: '{{ route("admin.vehicles.decode-vin") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    vin: vin
                },
                success: function(response) {
                    toastr.clear();
                    if (response.success && response.data) {
                        var data = response.data;
                        if (data.make) {
                            $('input[name="make_ar"]').val(data.make);
                            $('input[name="make_en"]').val(data.make);
                        }
                        if (data.model) {
                            $('input[name="model_ar"]').val(data.model);
                            $('input[name="model_en"]').val(data.model);
                        }
                        if (data.year) $('input[name="year"]').val(data.year);
                        if (data.engine_capacity) $('input[name="engine_capacity"]').val(data.engine_capacity);
                        if (data.country_of_origin) $('input[name="country_of_origin"]').val(data.country_of_origin);
                        
                        if (data.fuel_type) {
                            var fuel = data.fuel_type.toLowerCase();
                            var fuelValue = '';
                            if (fuel.includes('petrol') || fuel.includes('gasoline')) {
                                fuelValue = 'petrol';
                            } else if (fuel.includes('diesel')) {
                                fuelValue = 'diesel';
                            } else if (fuel.includes('electric')) {
                                fuelValue = 'electric';
                            } else if (fuel.includes('hybrid')) {
                                fuelValue = 'hybrid';
                            }
                            if (fuelValue) {
                                $('select[name="fuel_type"]').val(fuelValue);
                            }
                        }

                        if (data.transmission) {
                            var trans = data.transmission.toLowerCase();
                            var transValue = '';
                            if (trans.includes('manual')) {
                                transValue = 'manual';
                            } else {
                                transValue = 'automatic'; // standard fallback
                            }
                            $('select[name="transmission"]').val(transValue);
                        }

                        toastr.success('{{ __("VIN decoded successfully and fields populated!") }}');
                    } else {
                        toastr.error('{{ __("Could not decode VIN. Please enter data manually.") }}');
                    }
                },
                error: function(xhr) {
                    toastr.clear();
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '{{ __("Error decoding VIN.") }}';
                    toastr.error(msg);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Interactive Wear & Damage Plotter logic
        let damagePoints = {};
        
        try {
            let initialVal = $('#damagePointsInput').val();
            if (initialVal) {
                let parsed = JSON.parse(initialVal);
                if (Array.isArray(parsed)) {
                    parsed.forEach(p => {
                        if (p.part) damagePoints[p.part] = p;
                    });
                } else {
                    damagePoints = parsed;
                }
            }
        } catch (e) {
            console.error("Error parsing initial damage points", e);
            damagePoints = {};
        }

        function colorizeDiagram() {
            $('.car-diagram-svg .car-part').each(function() {
                let partId = $(this).data('part');
                $(this).removeClass('damage-scratch damage-dent damage-repainted damage-broken');
                if (damagePoints[partId]) {
                    let type = damagePoints[partId].type;
                    $(this).addClass('damage-' + type);
                }
            });
        }
        
        colorizeDiagram();
        renderDamageTable();

        // Diagram Part Click Listener
        $('.car-diagram-svg .car-part').click(function() {
            let partId = $(this).data('part');
            let labelAr = $(this).data('label-ar');
            let labelEn = $(this).data('label-en');
            let currentLang = '{{ app()->getLocale() }}';
            let label = currentLang === 'ar' ? labelAr : labelEn;

            $('.car-diagram-svg .car-part').css('stroke-width', '1.5');
            $(this).css('stroke-width', '3');

            $('#activePartId').val(partId);
            $('#selectedPartLabel').text(label);
            $('#selectedPartAlert').removeClass('alert-secondary alert-warning').addClass('alert-warning');

            // Enable Controls
            $('#damageTypeSelect').prop('disabled', false);
            $('#damageNoteText').prop('disabled', false);
            $('#btnAddDamage').prop('disabled', false);

            if (damagePoints[partId]) {
                $('#damageTypeSelect').val(damagePoints[partId].type);
                $('#damageNoteText').val(damagePoints[partId].note || '');
                $('#btnDeleteDamage').show();
                $('#btnAddDamage').html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg> {{ __("Update") }}');
            } else {
                $('#damageTypeSelect').val('');
                $('#damageNoteText').val('');
                $('#btnDeleteDamage').hide();
                $('#btnAddDamage').html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> {{ __("Apply") }}');
            }
        });

        // Add/Update Button Click Listener
        $('#btnAddDamage').click(function() {
            let partId = $('#activePartId').val();
            let type = $('#damageTypeSelect').val();
            let note = $('#damageNoteText').val();

            if (!partId) return;
            if (!type) {
                toastr.warning('{{ __("Please select a damage type.") }}');
                return;
            }

            let pathEl = $('.car-diagram-svg .car-part[data-part="' + partId + '"]');
            let labelAr = pathEl.data('label-ar');
            let labelEn = pathEl.data('label-en');

            damagePoints[partId] = {
                part: partId,
                label_ar: labelAr,
                label_en: labelEn,
                type: type,
                note: note
            };

            colorizeDiagram();
            saveAndRefresh();
            resetControls();
            toastr.success('{{ __("Wear details added successfully.") }}');
        });

        // Delete Button Click Listener
        $('#btnDeleteDamage').click(function() {
            let partId = $('#activePartId').val();
            if (!partId) return;

            delete damagePoints[partId];
            
            colorizeDiagram();
            saveAndRefresh();
            resetControls();
            toastr.info('{{ __("Wear details removed.") }}');
        });

        // Delete from table
        $(document).on('click', '.delete-damage-row', function() {
            let partId = $(this).data('part-id');
            if (partId && damagePoints[partId]) {
                delete damagePoints[partId];
                colorizeDiagram();
                saveAndRefresh();
                resetControls();
                toastr.info('{{ __("Wear details removed.") }}');
            }
        });

        function saveAndRefresh() {
            let arr = Object.values(damagePoints);
            $('#damagePointsInput').val(JSON.stringify(arr));
            renderDamageTable();
        }

        function resetControls() {
            $('#activePartId').val('');
            $('#selectedPartLabel').text('{{ __("None") }}');
            $('#selectedPartAlert').removeClass('alert-warning').addClass('alert-secondary');
            $('#damageTypeSelect').val('').prop('disabled', true);
            $('#damageNoteText').val('').prop('disabled', true);
            $('#btnAddDamage').prop('disabled', true).html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> {{ __("Apply") }}');
            $('#btnDeleteDamage').hide();
            $('.car-diagram-svg .car-part').css('stroke-width', '1.5');
        }

        function renderDamageTable() {
            let tbody = $('#damageTableBody');
            tbody.empty();

            let arr = Object.values(damagePoints);
            if (arr.length === 0) {
                tbody.append('<tr><td colspan="3" class="text-center text-muted py-3">{{ __("No issues added yet.") }}</td></tr>');
                return;
            }

            let typeLabels = {
                scratch: '{{ __("Scratch") }}',
                dent: '{{ __("Dent") }}',
                repainted: '{{ __("Repainted") }}',
                broken: '{{ __("Broken") }}'
            };

            let badgeColors = {
                scratch: 'warning',
                dent: 'danger',
                repainted: 'success',
                broken: 'purple'
            };

            let currentLang = '{{ app()->getLocale() }}';

            arr.forEach(p => {
                let label = currentLang === 'ar' ? p.label_ar : p.label_en;
                let badgeColor = badgeColors[p.type] || 'secondary';
                let typeText = typeLabels[p.type] || p.type;
                let noteHtml = p.note ? `<br><small class="text-muted">${p.note}</small>` : '';

                tbody.append(`
                    <tr>
                        <td><strong>${label}</strong>${noteHtml}</td>
                        <td><span class="badge bg-${badgeColor}" style="${p.type === 'broken' ? 'background-color:#a855f7 !important;' : ''}">${typeText}</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-damage-row" data-part-id="${p.part}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
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
