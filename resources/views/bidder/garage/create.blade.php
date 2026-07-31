@extends('layouts.bidder')

@section('title', __('إضافة سيارة جديدة'))

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    .garage-header {
        margin-bottom: 2rem;
    }
    .garage-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    .form-control {
        background-color: var(--bg-body);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    .form-control:focus {
        background-color: var(--bg-body);
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem var(--primary-glow);
        color: var(--text-primary);
    }
    
    /* Image Studio Styles */
    .image-upload-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        background: var(--bg-body);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .image-upload-wrapper:hover, .image-upload-wrapper.dragover {
        border-color: var(--primary);
        background: var(--primary-glow);
    }
    .studio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .studio-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        aspect-ratio: 4/3;
        background: #000;
        cursor: grab;
    }
    .studio-item.sortable-ghost {
        opacity: 0.4;
    }
    .studio-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .studio-item-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        padding: 0.5rem;
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        backdrop-filter: blur(4px);
    }
    .studio-item-actions button {
        background: none;
        border: none;
        color: white;
        font-size: 0.9rem;
        cursor: pointer;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        transition: 0.2s;
    }
    .studio-item-actions button:hover {
        background: rgba(255,255,255,0.2);
    }
    .btn-cover {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(0,0,0,0.6);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        backdrop-filter: blur(4px);
    }
    .studio-item.is-cover {
        border: 2px solid var(--brand-gold);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
    }
    .studio-item.is-cover .btn-cover {
        background: var(--brand-gold);
        border-color: var(--brand-gold);
        color: #000;
    }

    .btn-submit {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-submit:hover {
        background: var(--primary-light);
        color: white;
    }
    .btn-draft {
        background: var(--bg-body);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-draft:hover {
        background: var(--border-color);
        color: var(--text-primary);
    }
    .btn-vin {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.3s;
    }
    .btn-vin:hover {
        box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
        transform: translateY(-1px);
        color: white;
    }
    .skeleton-loading {
        animation: skeleton 1.5s infinite alternate;
    }
    @keyframes skeleton {
        0% { background-color: var(--bg-body); }
        100% { background-color: var(--border-color); }
    }
    
    /* Damage Map */
    .damage-map-container {
        position: relative;
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-body);
        overflow: hidden;
        cursor: crosshair;
    }
    .car-blueprint {
        width: 100%;
        height: auto;
        display: block;
        opacity: 0.8;
    }
    .damage-pin {
        position: absolute;
        width: 24px;
        height: 24px;
        background: #ef4444;
        border: 2px solid white;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        z-index: 10;
        animation: pulse 2s infinite;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }
    .damage-pin:hover {
        transform: translate(-50%, -50%) scale(1.2);
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    /* Premium Stepper */
    .premium-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 2rem 1rem;
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }
    .premium-stepper::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 3rem;
        right: 3rem;
        height: 3px;
        background: var(--border-color);
        transform: translateY(-50%);
        z-index: 1;
    }
    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    .step-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--text-secondary);
        transition: all 0.4s ease;
    }
    .step-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.4s ease;
    }
    
    /* States */
    .step-item.active .step-circle {
        border-color: var(--primary);
        background: var(--primary);
        color: white;
        box-shadow: 0 0 15px var(--primary-glow);
        transform: scale(1.1);
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
    
    /* Hide default bootstrap tab styling inside premium stepper */
    .premium-stepper button {
        background: none;
        border: none;
        padding: 0;
        outline: none;
    }
    
    /* Validation shake */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .is-invalid-shake {
        animation: shake 0.4s;
        border-color: #ef4444 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="garage-header fade-in">
        <h1 class="garage-title">{{ __('إضافة سيارة جديدة') }}</h1>
        <p class="text-secondary">{{ __('أدخل تفاصيل سيارتك ليتم مراجعتها من قبل الإدارة وإدراجها في المزاد.') }}</p>
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
    <div class="premium-stepper fade-in" style="animation-delay: 0.1s;">
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

    <form id="vehicleForm" action="{{ route('bidder.garage.store') }}" method="POST" enctype="multipart/form-data" class="fade-in" style="animation-delay: 0.2s;">
        @csrf
        <input type="hidden" name="vehicle_id" id="vehicle_id" value="">
        <input type="hidden" name="action" id="formAction" value="submit">

        <div class="tab-content" id="wizardTabsContent">
            
            <!-- Step 1: Basic Info -->
            <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                <div class="form-card">
                    <h5 class="mb-4" style="color: var(--primary);"><i class="fa-solid fa-car"></i> {{ __('المعلومات الأساسية') }}</h5>
                    <div class="row">
                        <div class="col-md-12 form-group mb-4">
                            <label class="form-label">{{ __('رقم الهيكل (VIN)') }}</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="vin_number" name="vin_number" class="form-control" value="{{ old('vin_number') }}" placeholder="أدخل رقم الهيكل المكون من 17 حرفاً">
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
                            <input type="text" id="make_ar" name="make_ar" class="form-control" value="{{ old('make_ar') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الشركة المصنعة (إنجليزي)') }} *</label>
                                @if(app()->getLocale() == 'en')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('make_en', 'make_ar')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للعربية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="make_en" name="make_en" class="form-control" value="{{ old('make_en') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الموديل (عربي)') }} *</label>
                                @if(app()->getLocale() == 'ar')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('model_ar', 'model_en')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للإنجليزية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="model_ar" name="model_ar" class="form-control" value="{{ old('model_ar') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">{{ __('الموديل (إنجليزي)') }} *</label>
                                @if(app()->getLocale() == 'en')
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="translateField('model_en', 'model_ar')"><i class="fa-solid fa-language"></i> {{ __('ترجمة للعربية') }}</button>
                                @endif
                            </div>
                            <input type="text" id="model_en" name="model_en" class="form-control" value="{{ old('model_en') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('سنة الصنع') }} *</label>
                            <input type="number" id="year" name="year" class="form-control" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('year') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">{{ __('بلد المنشأ') }}</label>
                            <input type="text" id="country_of_origin" name="country_of_origin" class="form-control" value="{{ old('country_of_origin') }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 mt-4">
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
                            <input type="number" name="mileage" class="form-control" value="{{ old('mileage') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('نوع الوقود') }}</label>
                            <input type="text" id="fuel_type" name="fuel_type" class="form-control" value="{{ old('fuel_type') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">{{ __('ناقل الحركة') }}</label>
                            <input type="text" id="transmission" name="transmission" class="form-control" value="{{ old('transmission') }}">
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
                        <textarea id="description_ar" name="description_ar" class="form-control" rows="5">{{ old('description_ar') }}</textarea>
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
                        <textarea id="description_en" name="description_en" class="form-control" rows="5">{{ old('description_en') }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
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
                <div class="d-flex justify-content-between mt-4">
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

                <div class="d-flex justify-content-between align-items-center mt-4 p-4" style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevTab('step3')"><i class="fa-solid fa-arrow-right me-2"></i> {{ __('السابق') }}</button>
                    
                    <div id="summaryCard" class="text-center mx-3 flex-grow-1" style="background: var(--bg-body); padding: 10px; border-radius: 8px; border: 1px dashed var(--primary);">
                        <!-- Filled by JS -->
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span id="autoSaveStatus" class="text-secondary small fw-bold" style="opacity: 0; transition: opacity 0.3s;"><i class="fa-solid fa-cloud-arrow-up"></i> {{ __('تم الحفظ') }}</span>
                        <button type="button" onclick="submitForm('submit')" class="btn btn-submit">
                            <i class="fa-solid fa-paper-plane"></i> {{ __('إرسال للمراجعة') }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // ===== PREMIUM TABS NAVIGATION & VALIDATION =====
    function updateStepperState(currentStepId) {
        // Remove active class from all
        document.querySelectorAll('.step-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active to current
        let currentItem = document.getElementById('stepper-' + currentStepId);
        if (currentItem) {
            currentItem.classList.add('active');
            // Mark previous as completed
            let stepNum = parseInt(currentStepId.replace('step', ''));
            for(let i = 1; i < stepNum; i++) {
                document.getElementById('stepper-step' + i).classList.add('completed');
                document.getElementById('step' + i + '-tab').disabled = false;
            }
        }
    }

    function validateStep(stepId) {
        if (stepId === 'step1') {
            const requiredFields = ['make_ar', 'make_en', 'model_ar', 'model_en', 'year'];
            let isValid = true;
            requiredFields.forEach(field => {
                let el = document.getElementById(field);
                if (!el.value.trim()) {
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
        return true; // Other steps are optional or handled differently
    }

    function nextTab(targetTabId) {
        // Determine current tab by finding active pane
        let currentPane = document.querySelector('.tab-pane.active');
        let currentStepId = currentPane.id;
        
        if (validateStep(currentStepId)) {
            let triggerEl = document.querySelector('button[data-bs-target="#' + targetTabId + '"]');
            triggerEl.disabled = false;
            let tab = new bootstrap.Tab(triggerEl);
            tab.show();
            updateStepperState(targetTabId);
        }
    }
    
    function prevTab(targetTabId) {
        let triggerEl = document.querySelector('button[data-bs-target="#' + targetTabId + '"]');
        let tab = new bootstrap.Tab(triggerEl);
        tab.show();
        updateStepperState(targetTabId);
    }
    
    // ===== TRANSLATION (Simulated) =====
    function translateField(fromId, toId) {
        let fromEl = document.getElementById(fromId);
        let toEl = document.getElementById(toId);
        
        if (!fromEl.value.trim()) {
            toastr.warning('يرجى تعبئة الحقل باللغة العربية أولاً قبل الترجمة.');
            fromEl.classList.add('is-invalid-shake');
            setTimeout(() => fromEl.classList.remove('is-invalid-shake'), 400);
            return;
        }

        // Add loading state
        let originalPlaceholder = toEl.placeholder;
        toEl.value = '';
        toEl.placeholder = 'جارِ الترجمة السحرية...';
        toEl.disabled = true;
        
        // Simulate API delay
        setTimeout(() => {
            toEl.disabled = false;
            toEl.placeholder = originalPlaceholder;
            
            // Basic mock translation (In real app, this calls an API like Google Translate)
            let mockTranslation = fromEl.value + ' (Translated)';
            // simple dictionary for car makes
            const dict = {
                'تويوتا': 'Toyota', 'هوندا': 'Honda', 'نيسان': 'Nissan', 'فورد': 'Ford', 'شيفروليه': 'Chevrolet',
                'كامري': 'Camry', 'اكورد': 'Accord', 'التيما': 'Altima', 'موستنج': 'Mustang', 'تاهو': 'Tahoe'
            };
            
            let words = fromEl.value.split(' ');
            let translatedWords = words.map(w => dict[w] || w);
            toEl.value = translatedWords.join(' ');
            
            toastr.success('تمت الترجمة بنجاح ✨');
            
            // trigger change for auto-save
            toEl.dispatchEvent(new Event('change'));
        }, 800);
    }

    // Fix SVG Map rendering when tab becomes visible
    document.addEventListener('shown.bs.tab', function (event) {
        let targetId = event.target.getAttribute('data-bs-target').substring(1);
        updateStepperState(targetId);
        if (targetId === 'step4') {
            window.dispatchEvent(new Event('resize'));
            
            // Build summary card before submit
            buildSummaryCard();
        }
    });

    function buildSummaryCard() {
        let title = document.getElementById('make_ar').value + ' ' + document.getElementById('model_ar').value + ' ' + document.getElementById('year').value;
        let summaryContainer = document.getElementById('summaryCard');
        if (summaryContainer) {
            summaryContainer.innerHTML = '<strong>' + title + '</strong><br>' + '<span class="text-secondary">جاهزة للإرسال والمراجعة.</span>';
        }
    }

    // ===== VIN DECODER =====
    document.getElementById('btnDecodeVin').addEventListener('click', function() {
        const vin = document.getElementById('vin_number').value;
        if(vin.length < 10) {
            toastr.error('يرجى إدخال رقم هيكل صحيح.');
            return;
        }

        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الاستعلام...';
        btn.disabled = true;

        // Apply skeleton loading class
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
        .catch(err => {
            toastr.error('حدث خطأ أثناء الاتصال بالمزود.');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            fields.forEach(f => {
                let el = document.getElementById(f);
                if(el) el.classList.remove('skeleton-loading');
            });
        });
    });


    // ===== PHOTO STUDIO =====
    let images = [];
    let primaryId = null;
    let cropper = null;
    let currentEditId = null;

    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const studioGrid = document.getElementById('studioGrid');

    // Drag & Drop Handlers
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
            if(!file.type.startsWith('image/')) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const id = 'img_' + Math.random().toString(36).substr(2, 9);
                images.push({
                    id: id,
                    file: file,
                    dataUrl: e.target.result,
                    name: file.name
                });
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
        return new File([u8arr], filename, {type:mime});
    }

    window.submitForm = function(actionType) {
        document.getElementById('formAction').value = actionType;
        
        if(images.length > 0) {
            const dataTransfer = new DataTransfer();
            let primaryIndex = 0;
            
            images.forEach((img, index) => {
                const file = dataURLtoFile(img.dataUrl, img.name);
                dataTransfer.items.add(file);
                if(img.id === primaryId) {
                    primaryIndex = index;
                }
            });
            
            document.getElementById('finalImagesInput').files = dataTransfer.files;
            document.getElementById('primaryImageIndex').value = primaryIndex;
        }

        document.getElementById('vehicleForm').submit();
    }

    // ===== DAMAGE MAP =====
    let damagePoints = [];
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
