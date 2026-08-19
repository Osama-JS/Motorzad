@extends('layouts.bidder')

@section('title', isset($isEdit) && $isEdit ? __('تعديل بيانات السيارة') : __('إضافة سيارة جديدة'))

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
    /* ==========================================================================
       ADAPTIVE LUXURY THEME (Respects Light/Dark Mode)
       ========================================================================== */
    :root {
        --font-primary: 'Tajawal', 'Outfit', sans-serif;
        
        /* Premium Accents */
        --brand-gold: #e2b365;
        --brand-gold-glow: rgba(226, 179, 101, 0.25);
        --brand-gold-light: #f3d49b;
        
        /* Dynamic Glow based on Primary Color */
        --primary-glow: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.25);
        
        /* Radii */
        --radius-lg: 20px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    body {
        font-family: var(--font-primary);
    }

    /* Page Header - Professional Hero Card */
    .garage-hero-card {
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--primary-glow) 100%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 3rem;
        margin-bottom: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.03);
        transition: all 0.4s ease;
    }

    .garage-hero-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0; width: 6px; height: 100%;
        background: var(--primary);
        box-shadow: 0 0 15px var(--primary);
    }

    .hero-content {
        display: flex;
        align-items: center;
        gap: 2rem;
        position: relative;
        z-index: 2;
    }

    .hero-icon {
        width: 85px;
        height: 85px;
        background: var(--bg-body);
        border: 2px solid var(--border-color);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        color: var(--primary);
        box-shadow: 0 15px 30px var(--primary-glow);
        transform: rotate(8deg);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .garage-hero-card:hover .hero-icon {
        transform: rotate(0deg) scale(1.1);
        border-color: var(--primary);
    }

    .hero-text {
        display: flex;
        flex-direction: column;
    }

    .hero-title {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .hero-subtitle {
        color: var(--text-secondary);
        font-size: 1.15rem;
        margin: 0;
        max-width: 550px;
        line-height: 1.6;
    }

    .hero-decoration {
        position: absolute;
        left: 20px;
        bottom: -40px;
        font-size: 15rem;
        color: var(--primary);
        opacity: 0.04;
        z-index: 1;
        transform: rotate(-15deg);
        pointer-events: none;
    }

    /* Premium Cards */
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 3rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 15px 40px rgba(0,0,0,0.05);
        transition: all 0.4s ease;
    }
    
    .form-card:hover {
        border-color: var(--primary);
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    }

    .form-card h5 {
        font-weight: 800;
        font-size: 1.4rem;
        margin-bottom: 2.5rem !important;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .form-card h5 i {
        color: var(--primary);
        font-size: 1.5rem;
    }

    /* Input Fields */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.8rem;
        color: var(--text-primary);
        display: flex;
        justify-content: space-between;
    }

    .form-control, .form-select {
        background-color: var(--bg-body);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        border-radius: var(--radius-md);
        padding: 1.1rem 1.25rem;
        font-weight: 500;
        font-size: 1.05rem;
        transition: all 0.4s ease;
    }

    .form-control:hover, .form-select:hover {
        border-color: var(--primary);
    }

    .form-control:focus, .form-select:focus {
        background-color: var(--bg-body);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-glow);
        color: var(--text-primary);
    }

    .form-select option {
        background-color: var(--bg-card);
        color: var(--text-primary);
    }

    /* Image Studio / Upload */
    .image-upload-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-lg);
        padding: 5rem 2rem;
        text-align: center;
        background: var(--bg-body);
        cursor: pointer;
        transition: all 0.4s ease;
    }

    .image-upload-wrapper:hover, .image-upload-wrapper.dragover {
        border-color: var(--primary);
        background: var(--primary-glow);
        transform: translateY(-2px);
    }

    .image-upload-wrapper h4 {
        color: var(--text-primary) !important;
        font-weight: 700;
        margin-top: 15px;
    }

    .image-upload-wrapper i {
        color: var(--primary) !important;
    }

    .studio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .studio-item {
        position: relative;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border-color);
        aspect-ratio: 4/3;
        background: #000;
        cursor: grab;
    }

    .studio-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
        transition: all 0.5s ease;
    }
    
    .studio-item:hover img {
        opacity: 1;
        transform: scale(1.08);
    }

    .studio-item-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
        padding: 1.5rem 1rem 0.75rem;
        display: flex;
        justify-content: space-between;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .studio-item:hover .studio-item-actions {
        opacity: 1;
    }

    .studio-item-actions button {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .studio-item-actions button:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.1);
    }

    .btn-cover {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(0,0,0,0.6);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50px;
        padding: 0.4rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .studio-item.is-cover {
        border: 3px solid var(--brand-gold);
        box-shadow: 0 0 20px var(--brand-gold-glow);
    }

    .studio-item.is-cover .btn-cover {
        background: var(--brand-gold);
        color: #000;
        border-color: var(--brand-gold);
    }

    /* Buttons */
    .btn {
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        text-transform: uppercase;
    }

    .btn-primary, .btn-submit {
        background: var(--primary);
        border: none;
        color: white;
        box-shadow: 0 4px 15px var(--primary-glow);
    }

    .btn-primary:hover, .btn-submit:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.4);
    }

    .btn-secondary, .btn-draft {
        background: transparent;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover, .btn-draft:hover {
        background: var(--bg-card);
        color: var(--text-primary);
        border-color: var(--text-primary);
        transform: translateY(-3px);
    }

    .btn-vin {
        background: var(--primary-glow);
        color: var(--primary);
        border: 1px solid var(--primary);
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-vin:hover {
        background: var(--primary);
        color: white;
        box-shadow: 0 0 20px var(--primary-glow);
    }

    /* Damage Map */
    .damage-map-container {
        position: relative;
        width: 100%;
        max-width: 750px;
        margin: 2.5rem auto;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        background: var(--bg-body);
        overflow: hidden;
        cursor: crosshair;
        padding: 2rem;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
    }

    .car-blueprint {
        width: 100%;
        height: auto;
        display: block;
        opacity: 0.7;
    }

    .damage-pin {
        position: absolute;
        width: 32px;
        height: 32px;
        background: rgba(255, 59, 48, 0.9);
        border: 3px solid #fff;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        box-shadow: 0 0 20px rgba(255, 59, 48, 0.6);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 14px;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .damage-pin:hover {
        transform: translate(-50%, -50%) scale(1.3);
    }

    /* Luxury Stepper */
    .premium-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 0;
        margin-bottom: 4rem;
        background: transparent;
        border: none;
        box-shadow: none;
    }

    .premium-stepper::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: var(--border-color);
        z-index: 1;
    }

    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        width: 120px;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--bg-body);
        border: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--text-secondary);
        transition: all 0.5s ease;
        padding: 0;
    }

    .step-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.4s ease;
    }
    
    .step-item.active .step-circle {
        border-color: var(--primary);
        background: var(--bg-body);
        color: var(--primary);
        box-shadow: 0 0 25px var(--primary-glow), inset 0 0 10px var(--primary-glow);
        transform: scale(1.2);
    }
    
    .step-item.active .step-title {
        color: var(--primary);
    }
    
    .step-item.completed .step-circle {
        border-color: #10b981;
        background: #10b981;
        color: white;
    }
    
    .step-item.completed .step-title {
        color: #10b981;
    }

    /* Floating Action Bar */
    .action-bar {
        position: sticky;
        bottom: 2rem;
        background: rgba(var(--bg-card-rgb, 255,255,255), 0.85);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--border-color);
        border-radius: 100px;
        padding: 1.25rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        z-index: 100;
        margin-top: 2rem;
    }

    /* Validation Errors */
    .is-invalid-shake {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 1px #ef4444 !important;
    }
    .modal-backdrop{
        z-index: auto !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="garage-hero-card fade-in">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ __('إضافة سيارة جديدة') }}</h1>
                <p class="hero-subtitle">{{ __('أدخل تفاصيل سيارتك ليتم مراجعتها من قبل الإدارة وإدراجها في المزاد بكل سهولة ويسر.') }}</p>
            </div>
        </div>
        <div class="hero-decoration">
            <i class="fa-solid fa-gauge-high"></i>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Premium Stepper -->
    <div class="nav premium-stepper fade-in" role="tablist" style="animation-delay: 0.1s;">
        <!-- Step 1 -->
        <div class="step-item active" id="stepper-step1">
            <button class="step-circle" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab" aria-controls="step1" aria-selected="true">
                <span>1</span>
            </button>
            <span class="step-title">{{ __('الأساسية') }}</span>
        </div>
        <!-- Step 2 -->
        <div class="step-item" id="stepper-step2">
            <button class="step-circle" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab" aria-controls="step2" aria-selected="false" disabled>
                <span>2</span>
            </button>
            <span class="step-title">{{ __('الوصف') }}</span>
        </div>
        <!-- Step 3 -->
        <div class="step-item" id="stepper-step3">
            <button class="step-circle" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab" aria-controls="step3" aria-selected="false" disabled>
                <span>3</span>
            </button>
            <span class="step-title">{{ __('الصور') }}</span>
        </div>
        <!-- Step 4 -->
        <div class="step-item" id="stepper-step4">
            <button class="step-circle" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab" aria-controls="step4" aria-selected="false" disabled>
                <span>4</span>
            </button>
            <span class="step-title">{{ __('الأضرار') }}</span>
        </div>
    </div>

    <form id="vehicleForm" action="{{ isset($isEdit) && $isEdit ? route('bidder.garage.update', $vehicle->id) : route('bidder.garage.store') }}" method="POST" enctype="multipart/form-data" class="fade-in" style="animation-delay: 0.2s;">
        @csrf
        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicle->id ?? '' }}">
        <input type="hidden" name="action" id="formAction" value="submit">
        <input type="hidden" name="existing_images" id="existingImagesInput">
        <input type="hidden" name="new_images_order" id="newImagesOrderInput">

        <div class="tab-content" id="wizardTabsContent">
            
            <!-- Step 1: Basic Info -->
            <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                <div class="form-card">
                    <h5 class="mb-4" style="color: var(--primary);"><i class="fa-solid fa-car"></i> {{ __('المعلومات الأساسية') }}</h5>
                    <div class="row">
                        <div class="col-md-12 form-group mb-4">
                            <label class="form-label">{{ __('رقم الهيكل (VIN)') }}</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="vin_number" name="vin_number" class="form-control" value="{{ old('vin_number', $vehicle->vin_number ?? '') }}" placeholder="أدخل رقم الهيكل المكون من 17 حرفاً">
                                <button type="button" id="btnDecodeVin" class="btn-vin">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> {{ __('استعلام سحري') }}
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block">{{ __('سيقوم النظام بجلب بيانات السيارة تلقائياً بناءً على رقم الهيكل.') }}</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الشركة المصنعة (عربي)') }} *</label>
                                @if(app()->getLocale() == 'ar')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('make_ar', 'make_en')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للإنجليزية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="make_ar" name="make_ar" class="form-control" value="{{ old('make_ar', $vehicle->make_ar ?? '') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الشركة المصنعة (إنجليزي)') }} *</label>
                                @if(app()->getLocale() == 'en')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('make_en', 'make_ar')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للعربية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="make_en" name="make_en" class="form-control" value="{{ old('make_en', $vehicle->make_en ?? '') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الموديل (عربي)') }} *</label>
                                @if(app()->getLocale() == 'ar')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('model_ar', 'model_en')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للإنجليزية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="model_ar" name="model_ar" class="form-control" value="{{ old('model_ar', $vehicle->model_ar ?? '') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الموديل (إنجليزي)') }} *</label>
                                @if(app()->getLocale() == 'en')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('model_en', 'model_ar')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للعربية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="model_en" name="model_en" class="form-control" value="{{ old('model_en', $vehicle->model_en ?? '') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('سنة الصنع') }} <span class="text-danger">*</span></label>
                            <input type="number" id="year" name="year" class="form-control" min="1901" max="{{ date('Y') + 1 }}" value="{{ old('year', $vehicle->year ?? '') }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label">{{ __('بلد المنشأ') }}</label>
                            <input type="text" id="country_of_origin" name="country_of_origin" class="form-control" value="{{ old('country_of_origin', $vehicle->country_of_origin ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="action-bar fade-in">
                    <div></div> <!-- Empty div for flex-between spacing -->
                    <button type="button" class="btn btn-primary px-5" onclick="nextTab('step2')">{{ __('التالي') }} <i class="fa-solid fa-arrow-left ms-2"></i></button>
                </div>
            </div>

            <!-- Step 2: Description & Details -->
            <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                <div class="form-card">
                    <h5 class="mb-4" style="color: var(--primary);"><i class="fa-solid fa-circle-info"></i> {{ __('تفاصيل إضافية والوصف') }}</h5>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('الممشى') }}</label>
                            <input type="number" name="mileage" class="form-control" value="{{ old('mileage', $vehicle->mileage ?? '') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('نوع الوقود') }}</label>
                            <select id="fuel_type" name="fuel_type" class="form-select">
                                <option value="petrol" {{ old('fuel_type', $vehicle->fuel_type ?? '') == 'petrol' ? 'selected' : '' }}>{{ __('بنزين') }}</option>
                                <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type ?? '') == 'diesel' ? 'selected' : '' }}>{{ __('ديزل') }}</option>
                                <option value="electric" {{ old('fuel_type', $vehicle->fuel_type ?? '') == 'electric' ? 'selected' : '' }}>{{ __('كهرباء') }}</option>
                                <option value="hybrid" {{ old('fuel_type', $vehicle->fuel_type ?? '') == 'hybrid' ? 'selected' : '' }}>{{ __('هجين') }}</option>
                                <option value="other" {{ old('fuel_type', $vehicle->fuel_type ?? '') == 'other' ? 'selected' : '' }}>{{ __('أخرى') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('ناقل الحركة') }}</label>
                            <select id="transmission" name="transmission" class="form-select">
                                <option value="automatic" {{ old('transmission', $vehicle->transmission ?? '') == 'automatic' ? 'selected' : '' }}>{{ __('أوتوماتيك') }}</option>
                                <option value="manual" {{ old('transmission', $vehicle->transmission ?? '') == 'manual' ? 'selected' : '' }}>{{ __('عادي') }}</option>
                                <option value="cvt" {{ old('transmission', $vehicle->transmission ?? '') == 'cvt' ? 'selected' : '' }}>{{ __('تتابعي (CVT)') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                        <div class="d-flex align-items-center gap-3">
                            <label class="form-label mb-0">{{ __('وصف السيارة (عربي)') }}</label>
                            @if(app()->getLocale() == 'ar')
                            <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('description_ar', 'description_en')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للإنجليزية') }}</button>
                            @endif
                        </div>
                        <button type="button" id="btnMagicDesc" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> {{ __('توليد وصف سحري ✨') }}
                        </button>
                    </div>
                    <div class="form-group">
                        <textarea id="description_ar" name="description_ar" class="form-control" rows="5">{{ old('description_ar', $vehicle->description_ar ?? '') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                        <div class="d-flex align-items-center gap-3">
                            <label class="form-label mb-0">{{ __('وصف السيارة (إنجليزي)') }}</label>
                            @if(app()->getLocale() == 'en')
                            <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('description_en', 'description_ar')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للعربية') }}</button>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea id="description_en" name="description_en" class="form-control" rows="5">{{ old('description_en', $vehicle->description_en ?? '') }}</textarea>
                    </div>
                </div>
                <div class="action-bar fade-in">
                    <button type="button" class="btn btn-secondary px-5" onclick="prevTab('step1')"><i class="fa-solid fa-arrow-right me-2"></i> {{ __('السابق') }}</button>
                    <button type="button" class="btn btn-primary px-5" onclick="nextTab('step3')">{{ __('التالي') }} <i class="fa-solid fa-arrow-left ms-2"></i></button>
                </div>
            </div>

            <!-- Step 3: Photos -->
            <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                <div class="form-card">
                    <h5 class="mb-4" style="color: var(--primary);"><i class="fa-solid fa-camera"></i> {{ __('أستوديو الصور') }}</h5>
                    <div id="uploadZone" class="image-upload-wrapper">
                        <i class="fa-solid fa-cloud-arrow-up mb-3" style="font-size: 3.5rem; color: var(--primary);"></i>
                        <h4 class="text-primary fw-bold">{{ __('اسحب وأفلت الصور هنا') }}</h4>
                        <p class="text-secondary mb-0">{{ __('أو اضغط لتصفح الملفات (الحد الأقصى 2MB للصورة)') }}</p>
                        <input type="file" id="fileInput" multiple accept="image/jpeg,image/png,image/webp" class="d-none">
                    </div>
                    <input type="file" name="images[]" id="finalImagesInput" multiple class="d-none">
                    <input type="hidden" name="primary_image_index" id="primaryImageIndex" value="0">
                    <div id="studioGrid" class="studio-grid"></div>
                </div>
                <div class="action-bar fade-in">
                    <button type="button" class="btn btn-secondary px-5" onclick="prevTab('step2')"><i class="fa-solid fa-arrow-right me-2"></i> {{ __('السابق') }}</button>
                    <button type="button" class="btn btn-primary px-5" onclick="nextTab('step4')">{{ __('التالي') }} <i class="fa-solid fa-arrow-left ms-2"></i></button>
                </div>
            </div>

            <!-- Step 4: Damage Map & Submit -->
            <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                <div class="form-card">
                    <h5 class="mb-4" style="color: var(--primary);"><i class="fa-solid fa-car-burst"></i> {{ __('خريطة الأضرار (اختياري)') }}</h5>
                    <p class="text-secondary">{{ __('انقر على الرسم التخطيطي للسيارة لتحديد أماكن الأضرار إن وجدت (خدش، صدمة، إلخ).') }}</p>
                    <div class="damage-map-container" id="damageMapContainer">
                        <svg viewBox="0 0 800 400" class="car-blueprint" preserveAspectRatio="xMidYMid meet">
                            <path d="M 150 100 Q 200 100 250 80 L 550 80 Q 600 100 650 100 Q 750 100 750 150 L 750 250 Q 750 300 650 300 Q 600 300 550 320 L 250 320 Q 200 300 150 300 Q 50 300 50 250 L 50 150 Q 50 100 150 100 Z" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="4"/>
                            <path d="M 280 110 L 320 110 L 300 150 L 260 150 Z" fill="#94a3b8" />
                            <rect x="250" y="100" width="300" height="200" rx="20" fill="none" stroke="#cbd5e1" stroke-width="4" />
                            <path d="M 280 120 Q 280 280 280 280 L 230 250 L 230 150 Z" fill="#94a3b8" />
                            <path d="M 520 120 Q 520 280 520 280 L 580 250 L 580 150 Z" fill="#94a3b8" />
                            <rect x="160" y="70" width="60" height="20" rx="5" fill="#475569" />
                            <rect x="160" y="310" width="60" height="20" rx="5" fill="#475569" />
                            <rect x="600" y="70" width="60" height="20" rx="5" fill="#475569" />
                            <rect x="600" y="310" width="60" height="20" rx="5" fill="#475569" />
                        </svg>
                        <div id="pinsContainer"></div>
                    </div>
                    <input type="hidden" name="damage_points" id="damagePointsInput" value="[]">
                </div>

                <div class="action-bar fade-in">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevTab('step3')"><i class="fa-solid fa-arrow-right me-2"></i> {{ __('السابق') }}</button>
                    
                    <div id="summaryCard" class="text-center mx-3 flex-grow-1" style="background: var(--bg-body); padding: 10px; border-radius: 8px; border: 1px dashed var(--neon-primary);">
                        <!-- Filled by JS -->
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span id="autoSaveStatus" class="text-secondary small fw-bold" style="opacity: 0; transition: opacity 0.3s;"><i class="fa-solid fa-cloud-arrow-up"></i> {{ __('تم الحفظ') }}</span>
                        <button type="button" onclick="submitForm('submit')" class="btn btn-submit">
                            <i class="fa-solid fa-check-circle"></i> {{ __('إضافة واعتماد السيارة') }}
                        </button>
                        <button type="button" onclick="submitForm('draft')" class="btn btn-draft">
                            <i class="fa-solid fa-file-lines"></i> {{ __('حفظ كمسودة') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-0">
                <h5 class="modal-title">{{ __('قص الصورة') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0" style="max-height: 60vh; background: #000;">
                <img id="cropperImage" src="" style="max-width: 100%;">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="button" class="btn btn-primary" id="btnSaveCrop">{{ __('حفظ التعديل') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Damage Modal -->
<div class="modal fade" id="damageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header border-0">
                <h5 class="modal-title">{{ __('تحديد نوع الضرر') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select id="damageType" class="form-control mb-3">
                    <option value="خدش">خدش (Scratch)</option>
                    <option value="صدمة">صدمة (Dent)</option>
                    <option value="رش">بهتان/رش (Paint)</option>
                    <option value="كسر">كسر (Broken)</option>
                </select>
                <input type="text" id="damageNote" class="form-control" placeholder="ملاحظة إضافية (اختياري)">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                <button type="button" class="btn btn-primary" id="btnSaveDamage">{{ __('إضافة الضرر') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // ===== PREMIUM TABS NAVIGATION & VALIDATION =====
    function updateStepperState(currentStepId) {
        document.querySelectorAll('.step-item').forEach(item => item.classList.remove('active'));
        let currentItem = document.getElementById('stepper-' + currentStepId);
        if (currentItem) {
            currentItem.classList.add('active');
            let stepNum = parseInt(currentStepId.replace('step', ''));
            for(let i = 1; i < stepNum; i++) {
                document.getElementById('stepper-step' + i).classList.add('completed');
                document.getElementById('step' + i + '-tab').disabled = false;
            }
        }
    }

    window.nextTab = function(targetTabId) {
        let currentPane = document.querySelector('.tab-pane.active');
        let currentStepId = currentPane ? currentPane.id : null;
        
        if (!currentStepId || (typeof validateStep === 'function' && validateStep(currentStepId))) {
            let triggerEl = document.querySelector('button[data-bs-target="#' + targetTabId + '"]');
            triggerEl.disabled = false;
            new bootstrap.Tab(triggerEl).show();
            updateStepperState(targetTabId);
        }
    }
    
    window.prevTab = function(targetTabId) {
        new bootstrap.Tab(document.querySelector('button[data-bs-target="#' + targetTabId + '"]')).show();
        updateStepperState(targetTabId);
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    let images = [];
    let primaryId = null;
    let cropper = null;
    let currentEditId = null;
    
    @if(isset($vehicle))
        let damagePoints = {!! $vehicle->damage_points ? json_encode($vehicle->damage_points) : '[]' !!};
        @if($vehicle->images && $vehicle->images->count() > 0)
            @foreach($vehicle->images as $img)
                images.push({
                    id: 'img_existing_{{ $img->id }}',
                    file: null,
                    dataUrl: '{{ asset('storage/' . $img->image_path) }}',
                    name: 'existing_image_{{ $img->id }}.jpg',
                    isExisting: true,
                    serverId: {{ $img->id }}
                });
                @if($img->is_primary)
                    primaryId = 'img_existing_{{ $img->id }}';
                @endif
            @endforeach
            if(!primaryId && images.length > 0) primaryId = images[0].id;
        @endif
    @else
        let damagePoints = [];
    @endif

    // Stepper logic extracted above to prevent CDN blocking

    function validateStep(stepId) {
        if (stepId === 'step1') {
            const requiredFields = ['make_ar', 'make_en', 'model_ar', 'model_en', 'year'];
            let isValid = true;
            requiredFields.forEach(field => {
                let el = document.getElementById(field);
                if (el && !el.value.trim()) {
                    el.classList.add('is-invalid-shake');
                    setTimeout(() => el.classList.remove('is-invalid-shake'), 400);
                    isValid = false;
                }
            });
            if (!isValid) {
                toastr.error('يرجى تعبئة جميع الحقول المطلوبة (الشركة، الموديل، وسنة الصنع).');
            }
            return isValid;
        }
        return true;
    }
    
    function translateField(fromId, toId) {
        let fromEl = document.getElementById(fromId);
        let toEl = document.getElementById(toId);
        if (!fromEl || !toEl) return;
        
        if (!fromEl.value.trim()) {
            toastr.warning('يرجى تعبئة الحقل باللغة العربية أولاً قبل الترجمة.');
            fromEl.classList.add('is-invalid-shake');
            setTimeout(() => fromEl.classList.remove('is-invalid-shake'), 400);
            return;
        }

        let originalPlaceholder = toEl.placeholder;
        toEl.value = '';
        toEl.placeholder = 'جارِ الترجمة السحرية...';
        toEl.disabled = true;
        
        setTimeout(() => {
            toEl.disabled = false;
            toEl.placeholder = originalPlaceholder;
            const dict = {
                'تويوتا': 'Toyota', 'هوندا': 'Honda', 'نيسان': 'Nissan', 'فورد': 'Ford', 'شيفروليه': 'Chevrolet',
                'كامري': 'Camry', 'اكورد': 'Accord', 'التيما': 'Altima', 'موستنج': 'Mustang', 'تاهو': 'Tahoe'
            };
            let words = fromEl.value.split(' ');
            let translatedWords = words.map(w => dict[w] || w);
            toEl.value = translatedWords.join(' ');
            toastr.success('تمت الترجمة بنجاح ✨');
            toEl.dispatchEvent(new Event('change'));
        }, 800);
    }

    document.addEventListener('shown.bs.tab', function (event) {
        let targetId = event.target.getAttribute('data-bs-target').substring(1);
        updateStepperState(targetId);
        if (targetId === 'step4') {
            window.dispatchEvent(new Event('resize'));
            buildSummaryCard();
        }
    });

    function buildSummaryCard() {
        let title = document.getElementById('make_ar').value + ' ' + document.getElementById('model_ar').value + ' ' + document.getElementById('year').value;
        let summaryContainer = document.getElementById('summaryCard');
        if (summaryContainer) {
            summaryContainer.innerHTML = '<strong>' + title + '</strong><br>' + '<span class="text-secondary">جاهزة للإضافة والاعتماد.</span>';
        }
    }

    const btnDecodeVin = document.getElementById('btnDecodeVin');
    if (btnDecodeVin) {
        btnDecodeVin.addEventListener('click', function() {
            const vin = document.getElementById('vin_number').value;
            if(vin.length < 10) {
                toastr.error('يرجى إدخال رقم هيكل صحيح.');
                return;
            }

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الاستعلام...';
            btn.disabled = true;

            const fields = ['make_ar', 'make_en', 'model_ar', 'model_en', 'year', 'country_of_origin', 'fuel_type', 'transmission'];
            fields.forEach(f => {
                let el = document.getElementById(f);
                if(el) el.classList.add('skeleton-loading');
            });

            fetch("{{ route('bidder.garage.decode-vin') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ vin: vin })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const info = data.data;
                    document.getElementById('make_en').value = info.make || '';
                    document.getElementById('make_ar').value = info.make || '';
                    document.getElementById('model_en').value = info.model || '';
                    document.getElementById('model_ar').value = info.model || '';
                    document.getElementById('year').value = info.year || '';
                    document.getElementById('country_of_origin').value = info.country_of_origin || '';
                    document.getElementById('fuel_type').value = info.fuel_type || '';
                    document.getElementById('transmission').value = info.transmission || '';
                    toastr.success('تم جلب البيانات بنجاح!');
                } else {
                    toastr.error(data.message);
                }
            })
            .catch(err => toastr.error('حدث خطأ أثناء الاتصال بالمزود.'))
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                fields.forEach(f => {
                    let el = document.getElementById(f);
                    if(el) el.classList.remove('skeleton-loading');
                });
            });
        });
    }
    
    // ===== PHOTO STUDIO =====
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const studioGrid = document.getElementById('studioGrid');

    uploadZone.addEventListener('click', () => fileInput.click());
    uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('dragover'); });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if(!file.type.startsWith('image/')) {
                toastr.error('يرجى رفع صور فقط.');
                return;
            }
            if(file.size > 2 * 1024 * 1024) {
                toastr.error('حجم الصورة ' + file.name + ' يتجاوز 2 ميجابايت.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const id = 'img_' + Math.random().toString(36).substr(2, 9);
                images.push({ id, file, dataUrl: e.target.result, name: file.name });
                if(!primaryId) primaryId = id;
                renderGrid();
            };
            reader.readAsDataURL(file);
        });
    }

    function renderGrid() {
        studioGrid.innerHTML = '';
        images.forEach((img, index) => {
            const isCover = img.id === primaryId;
            const html = `
                <div class="studio-item ${isCover ? 'is-cover' : ''}" data-id="${img.id}">
                    <button type="button" class="btn-cover" onclick="setPrimary('${img.id}')" title="تعيين كغلاف">
                        ${isCover ? '<i class="fa-solid fa-star"></i> الغلاف' : '<i class="fa-regular fa-star"></i> تعيين كغلاف'}
                    </button>
                    <img src="${img.dataUrl}">
                    <div class="studio-item-actions">
                        <button type="button" onclick="openCropper('${img.id}')"><i class="fa-solid fa-crop"></i></button>
                        <button type="button" onclick="removeImage('${img.id}')"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </div>
            `;
            studioGrid.insertAdjacentHTML('beforeend', html);
        });
    }

    // Sortable JS
    new Sortable(studioGrid, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function (evt) {
            // Reorder the images array based on new DOM order
            const newOrder = Array.from(studioGrid.children).map(el => el.getAttribute('data-id'));
            const newImagesArray = [];
            newOrder.forEach(id => {
                const found = images.find(i => i.id === id);
                if(found) newImagesArray.push(found);
            });
            images = newImagesArray;
        },
    });

    // Global Functions for Grid Actions
    window.setPrimary = function(id) {
        primaryId = id;
        renderGrid();
    }

    window.removeImage = function(id) {
        images = images.filter(img => img.id !== id);
        if(primaryId === id && images.length > 0) primaryId = images[0].id;
        if(images.length === 0) primaryId = null;
        renderGrid();
    }

    window.openCropper = function(id) {
        currentEditId = id;
        const imgObj = images.find(img => img.id === id);
        const modalImg = document.getElementById('cropperImage');
        modalImg.src = imgObj.dataUrl;
        
        const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
        modal.show();

        document.getElementById('cropperModal').addEventListener('shown.bs.modal', function () {
            if(cropper) cropper.destroy();
            cropper = new Cropper(modalImg, {
                aspectRatio: NaN, // Free crop
                viewMode: 1,
                background: false,
            });
        }, { once: true });
    }

    document.getElementById('btnSaveCrop').addEventListener('click', function() {
        if(cropper && currentEditId) {
            const canvas = cropper.getCroppedCanvas({ maxWidth: 1920, maxHeight: 1080 });
            const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.8);
            
            const index = images.findIndex(img => img.id === currentEditId);
            if(index > -1) {
                images[index].dataUrl = croppedDataUrl;
                // We will convert dataURL back to File on submit
            }
            renderGrid();
            bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
        }
    });

    // ===== FORM SUBMISSION =====
    function dataURLtoFile(dataurl, filename) {
        let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){ u8arr[n] = bstr.charCodeAt(n); }
        
        // Fix filename extension if mime type was changed by cropper (e.g., png cropped to jpeg)
        if (mime === 'image/jpeg' && !filename.toLowerCase().endsWith('.jpg') && !filename.toLowerCase().endsWith('.jpeg')) {
            filename = filename.replace(/\.[^/.]+$/, "") + ".jpg";
        } else if (mime === 'image/png' && !filename.toLowerCase().endsWith('.png')) {
            filename = filename.replace(/\.[^/.]+$/, "") + ".png";
        }
        
        return new File([u8arr], filename, {type:mime});
    }

    window.submitForm = function(actionType) {
        document.getElementById('formAction').value = actionType;
        
        if(images.length > 0) {
            const dataTransfer = new DataTransfer();
            let primaryIndex = 0;
            let existingImagesOrder = [];
            let newImagesOrder = [];
            
            images.forEach((img, index) => {
                if(img.isExisting) {
                    existingImagesOrder.push({ serverId: img.serverId, order: index });
                } else {
                    const file = dataURLtoFile(img.dataUrl, img.name);
                    dataTransfer.items.add(file);
                    newImagesOrder.push({ order: index });
                }
                if(img.id === primaryId) {
                    primaryIndex = index;
                }
            });
            
            document.getElementById('finalImagesInput').files = dataTransfer.files;
            document.getElementById('primaryImageIndex').value = primaryIndex;
            document.getElementById('existingImagesInput').value = JSON.stringify(existingImagesOrder);
            document.getElementById('newImagesOrderInput').value = JSON.stringify(newImagesOrder);
        }

        document.getElementById('vehicleForm').submit();
    }

    // ===== DAMAGE MAP =====
    let tempDamagePoint = null;
    
    const damageMap = document.getElementById('damageMapContainer');
    const pinsContainer = document.getElementById('pinsContainer');
    let damageModalInstance = null;
    
    document.addEventListener('DOMContentLoaded', () => {
        damageModalInstance = new bootstrap.Modal(document.getElementById('damageModal'));
    });
    
    damageMap.addEventListener('click', function(e) {
        if(e.target.closest('.damage-pin')) {
            // Remove pin if clicked
            const id = e.target.closest('.damage-pin').getAttribute('data-id');
            damagePoints = damagePoints.filter(p => p.id != id);
            renderPins();
            return;
        }

        const rect = damageMap.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        
        tempDamagePoint = {
            id: Date.now(),
            x: x,
            y: y
        };
        
        document.getElementById('damageType').value = 'خدش';
        document.getElementById('damageNote').value = '';
        damageModalInstance.show();
    });

    document.getElementById('btnSaveDamage').addEventListener('click', function() {
        if(tempDamagePoint) {
            tempDamagePoint.type = document.getElementById('damageType').value;
            tempDamagePoint.note = document.getElementById('damageNote').value;
            damagePoints.push(tempDamagePoint);
            tempDamagePoint = null;
            renderPins();
            damageModalInstance.hide();
        }
    });

    function renderPins() {
        pinsContainer.innerHTML = '';
        damagePoints.forEach(point => {
            const pin = document.createElement('div');
            pin.className = 'damage-pin';
            pin.style.left = point.x + '%';
            pin.style.top = point.y + '%';
            pin.setAttribute('data-id', point.id);
            pin.setAttribute('title', point.type + (point.note ? ' - ' + point.note : ''));
            pin.innerHTML = '<i class="fa-solid fa-xmark" style="pointer-events: none;"></i>';
            pinsContainer.appendChild(pin);
        });
        document.getElementById('damagePointsInput').value = JSON.stringify(damagePoints);
    }

    // ===== MAGIC DESCRIPTION GENERATOR =====
    document.getElementById('btnMagicDesc').addEventListener('click', function() {
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التوليد...';
        btn.disabled = true;

        const payload = {
            make_ar: document.getElementById('make_ar').value,
            model_ar: document.getElementById('model_ar').value,
            make_en: document.getElementById('make_en').value,
            model_en: document.getElementById('model_en').value,
            year: document.getElementById('year').value,
            mileage: document.querySelector('input[name="mileage"]').value,
            fuel_type: document.getElementById('fuel_type').value,
            transmission: document.getElementById('transmission').value,
        };

        fetch("{{ route('bidder.garage.generate-description') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const descAr = document.getElementById('description_ar');
                const descEn = document.getElementById('description_en');
                
                descAr.value = data.description_ar;
                descEn.value = data.description_en;
                
                // Add highlight effect
                descAr.style.transition = 'box-shadow 0.3s ease';
                descEn.style.transition = 'box-shadow 0.3s ease';
                descAr.style.boxShadow = '0 0 15px var(--primary)';
                descEn.style.boxShadow = '0 0 15px var(--primary)';
                
                setTimeout(() => {
                    descAr.style.boxShadow = 'none';
                    descEn.style.boxShadow = 'none';
                }, 1500);

                toastr.success('تم توليد الوصف بنجاح!');
            }
        })
        .catch(err => toastr.error('حدث خطأ أثناء توليد الوصف.'))
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    });

    // ===== SMART AUTO-SAVE =====
    let autoSaveTimeout = null;
    const formInputs = document.querySelectorAll('#vehicleForm input:not([type="file"]), #vehicleForm textarea, #vehicleForm select');
    const autoSaveStatus = document.getElementById('autoSaveStatus');
    
    function triggerAutoSave() {
        const formData = new FormData(document.getElementById('vehicleForm'));
        // Remove images to save bandwidth during auto-save
        formData.delete('images[]');
        
        autoSaveStatus.style.opacity = '1';
        autoSaveStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

        fetch("{{ route('bidder.garage.auto-save') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('vehicle_id').value = data.vehicle_id;
                autoSaveStatus.innerHTML = '<i class="fa-solid fa-cloud-check text-success"></i> {{ __('تم الحفظ') }}';
                setTimeout(() => { autoSaveStatus.style.opacity = '0'; }, 3000);
            } else {
                autoSaveStatus.innerHTML = '<i class="fa-solid fa-circle-exclamation text-danger"></i> {{ __('خطأ بالحفظ') }}';
            }
        })
        .catch(err => {
            autoSaveStatus.innerHTML = '<i class="fa-solid fa-circle-exclamation text-danger"></i> {{ __('خطأ بالاتصال') }}';
        });
    }

    formInputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(triggerAutoSave, 3000); // Wait 3 seconds after last type
        });
        input.addEventListener('change', () => {
            clearTimeout(autoSaveTimeout);
            triggerAutoSave();
        });
    });
</script>
@endsection
