@extends('layouts.admin')

@section('title', __('إنشاء قالب نموذج جديد'))

@section('css')
<style>
    .field-card {
        background: var(--bg-card, #f8fafc);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .field-card:hover {
        border-color: rgba(220, 53, 69, 0.25);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
    .field-card.sortable-ghost {
        opacity: 0.4;
        border: 2px dashed var(--brand-red, #dc3545);
    }
    .drag-handle {
        cursor: grab;
        color: #94a3b8;
        padding: 6px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .drag-handle:hover {
        color: var(--brand-red, #dc3545);
        background: rgba(220, 53, 69, 0.08);
    }
    .drag-handle:active {
        cursor: grabbing;
    }

    /* Builder card polish */
    .builder-section-title {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--text, #1e293b);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
    }
    .builder-section-title i {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    /* Preview card polish */
    .preview-container {
        position: sticky;
        top: 100px;
    }
    .preview-header {
        background: linear-gradient(135deg, var(--brand-red, #dc3545) 0%, #b02a37 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 16px 16px 0 0;
        font-weight: 800;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Add field button polish */
    .add-field-btn {
        border: 2px dashed var(--border, rgba(220, 53, 69, 0.3));
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        color: var(--brand-red, #dc3545);
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        background: transparent;
        width: 100%;
    }
    .add-field-btn:hover {
        border-color: var(--brand-red, #dc3545);
        background: rgba(220, 53, 69, 0.04);
        transform: translateY(-2px);
    }
    .cursor-pointer { cursor: pointer; }
    .type-dropdown-item:hover { background-color: #f8fafc; }
    .input-group-merge { overflow: visible !important; }
    
    .btn-save-template {
        background: linear-gradient(135deg, var(--brand-red, #dc3545) 0%, #a71d2a 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white !important;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 6px rgba(220, 53, 69, 0.15), 0 1px 3px rgba(0, 0, 0, 0.08);
        z-index: 1;
    }
    
    .btn-save-template::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: all 0.6s ease;
        z-index: -1;
        transform: skewX(-20deg);
    }
    
    .btn-save-template:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 25px rgba(220, 53, 69, 0.35), 0 5px 10px rgba(220, 53, 69, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .btn-save-template:hover::before {
        left: 150%;
    }
    
    .btn-save-template i {
        transition: transform 0.3s ease;
    }
    
    .btn-save-template:hover i {
        transform: scale(1.15) translateY(-2px);
    }
</style>
@endsection

@section('content')
<x-admin-header :title="__('إنشاء قالب تسجيل بائعين جديد')" :breadcrumb="__('إضافة قالب')">
    <a href="{{ route('admin.form-templates.index') }}" class="btn btn-light rounded-pill border px-4 shadow-sm">
        <i class="fa-solid fa-arrow-right me-1"></i> {{ __('عودة للقائمة') }}
    </a>
</x-admin-header>

{{-- Alpine Component --}}
<div x-data="formBuilder()" class="row justify-content-center">
    <div class="col-lg-8">
        
        <div id="formErrorsContainer">
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form action="{{ route('admin.form-templates.store') }}" method="POST" id="templateForm">
            @csrf

            {{-- Template Main Info --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-body py-3 border-bottom">
                    <h6 class="mb-0 fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-gear text-danger"></i>
                        <span>{{ __('البيانات الأساسية للقالب') }}</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('اسم القالب') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" x-model="templateName" class="form-control" placeholder="{{ __('مثال: نموذج تسجيل المعارض 2026') }}" required value="{{ old('name') }}">
                            <small class="text-muted mt-1 d-block">سيظهر هذا الاسم للمدير فقط لتمييز القالب.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('وصف القالب (اختياري)') }}</label>
                            <input type="text" name="description" x-model="templateDescription" class="form-control" placeholder="{{ __('وصف داخلي للهدف من هذا القالب') }}" value="{{ old('description') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dynamic Fields Builder --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-body py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-layer-group text-danger"></i>
                        <span>{{ __('بناء الحقول (الأسئلة)') }}</span>
                    </h6>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1" x-text="fields.length + ' حقول'"></span>
                </div>
                
                <div class="card-body p-4">
                    
                    {{-- Fields List --}}
                    <div class="d-flex flex-column gap-4 mb-4" x-ref="sortableContainer">
                        <template x-for="(field, index) in fields" :key="field.id">
                            <div class="field-card bg-body position-relative">
                                {{-- Top Accent Line --}}
                                <div class="position-absolute top-0 start-0 w-100 bg-danger" style="height: 4px; opacity: 0.8; border-radius: 16px 16px 0 0;"></div>

                                {{-- Hidden Name Input (Auto-generated from label) --}}
                                <input type="hidden" :name="'fields['+index+'][name]'" :value="field.name || slugifyArabic(field.label || '')">

                                {{-- Card Header --}}
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="drag-handle">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" x-text="index + 1"></span>
                                            <span x-text="field.label || '{{ __('حقل جديد') }}'"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" @click="removeField(index)" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm transition-all" title="{{ __('حذف الحقل') }}">
                                            <i class="fa-solid fa-trash-can me-1"></i> {{ __('حذف') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="row g-4">
                                        {{-- Left Column: Core settings --}}
                                        <div class="col-lg-8 border-end-lg pe-lg-4">
                                            <div class="row g-3">
                                                {{-- Field Label --}}
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-dark">{{ __('عنوان الحقل') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-merge shadow-sm rounded-3 overflow-hidden">
                                                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-heading text-muted"></i></span>
                                                        <input type="text" x-model="field.label" :name="'fields['+index+'][label]'" class="form-control border-start-0 ps-0" placeholder="مثال: رقم السجل التجاري" required>
                                                    </div>
                                                </div>

                                                {{-- Field Type --}}
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-dark">{{ __('نوع الحقل') }}</label>
                                                    <div class="position-relative w-100" x-data="{ openTypeDropdown: false }" @click.outside="openTypeDropdown = false">
                                                        <input type="hidden" :name="'fields['+index+'][type]'" x-model="field.type">
                                                        
                                                        <div class="input-group input-group-merge shadow-sm rounded-3 cursor-pointer" @click="openTypeDropdown = !openTypeDropdown">
                                                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-list-check text-muted"></i></span>
                                                            <div class="form-select border-start-0 ps-0 d-flex align-items-center bg-body m-0" style="cursor: pointer;">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <i :class="getFieldTypeIcon(field.type) + ' text-danger opacity-75'" style="width: 20px; text-align: center;"></i>
                                                                    <span x-text="getFieldTypeLabel(field.type)" class="text-truncate"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div x-show="openTypeDropdown" 
                                                             x-transition.opacity.duration.200ms
                                                             class="position-absolute w-100 bg-body border rounded-3 shadow-lg mt-1 z-3" 
                                                             style="max-height: 250px; overflow-y: auto; display: none; left:0; top: 100%;">
                                                            <template x-for="typeObj in fieldTypes" :key="typeObj.value">
                                                                <div @click="field.type = typeObj.value; openTypeDropdown = false" 
                                                                     class="px-3 py-2 cursor-pointer transition-all d-flex align-items-center gap-2 type-dropdown-item"
                                                                     :class="field.type === typeObj.value ? 'bg-danger bg-opacity-10 text-danger fw-bold' : 'text-dark'">
                                                                    <i :class="typeObj.icon + ' ' + (field.type === typeObj.value ? 'text-danger' : 'text-muted opacity-50')" style="width: 20px; text-align: center;"></i>
                                                                    <span x-text="typeObj.label"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Options for Select/Radio/Multi-select Type --}}
                                                <div class="col-12" x-show="['select', 'radio', 'multi-select'].includes(field.type)" x-transition>
                                                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 border border-danger border-opacity-25 mt-2">
                                                        <label class="form-label fw-bold text-danger">{{ __('خيارات الحقل') }} <span class="text-danger">*</span></label>
                                                        <input type="text" x-model="field.options" :name="'fields['+index+'][options]'" class="form-control shadow-sm" placeholder="خيارات مفصولة بفاصلة (مثال: أزرق, أحمر, أخضر)">
                                                        <small class="text-danger opacity-75 mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i>افصل بين الخيارات بفاصلة ( , )</small>
                                                    </div>
                                                </div>

                                                {{-- Description for HTML/Text Block --}}
                                                <div class="col-12" x-show="field.type === 'html'" x-transition>
                                                    <div class="bg-light p-3 rounded-3 border mt-2">
                                                        <label class="form-label fw-bold text-dark">{{ __('النص أو كود HTML للعرض') }}</label>
                                                        <textarea x-model="field.description" :name="'fields['+index+'][description]'" class="form-control font-monospace shadow-sm" rows="4" dir="ltr" placeholder="<p>أدخل النص هنا...</p>"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Column: Rules & Logic --}}
                                        <div class="col-lg-4">
                                            <div class="d-flex flex-column gap-3 h-100 justify-content-start">
                                                
                                                {{-- Is Required --}}
                                                <div class="bg-light p-3 rounded-3 border" x-show="!['html', 'step-divider'].includes(field.type)" x-transition>
                                                    <div class="form-check form-switch form-check-reverse m-0 d-flex justify-content-between align-items-center px-0">
                                                        <input type="hidden" :name="'fields['+index+'][is_required]'" value="0">
                                                        <label class="form-check-label fw-bold text-dark mb-0 ms-0" :for="'req_'+index">{{ __('حقل إلزامي') }}</label>
                                                        <input class="form-check-input fs-5 m-0 me-0 ms-auto float-none" type="checkbox" x-model="field.is_required" :name="'fields['+index+'][is_required]'" value="1" :id="'req_'+index">
                                                    </div>
                                                </div>

                                                {{-- Conditional Logic Toggle --}}
                                                <div class="bg-light p-3 rounded-3 border" x-show="!['html', 'step-divider'].includes(field.type)" x-transition>
                                                    <div class="form-check form-switch form-check-reverse m-0 d-flex justify-content-between align-items-center px-0">
                                                        <label class="form-check-label fw-bold text-dark mb-0 ms-0" :for="'cond_'+index">{{ __('منطق شرطي') }}</label>
                                                        <input class="form-check-input fs-5 m-0 me-0 ms-auto float-none" type="checkbox" x-model="field.has_condition" :id="'cond_'+index">
                                                    </div>
                                                    
                                                    {{-- Condition Builder --}}
                                                    <div class="mt-3 pt-3 border-top" x-show="field.has_condition" x-transition>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold text-muted">{{ __('يظهر إذا كان الحقل:') }}</label>
                                                            <select class="form-select form-select-sm shadow-sm" x-model="field.depends_on_field_name" :name="field.has_condition ? 'fields['+index+'][depends_on_field_name]' : ''">
                                                                <option value="">{{ __('اختر الحقل...') }}</option>
                                                                <template x-for="otherField in fields.filter(f => f.id !== field.id)" :key="otherField.id">
                                                                    <option :value="otherField.name" x-text="otherField.label || 'حقل بدون عنوان'"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="form-label small fw-bold text-muted">{{ __('يساوي القيمة:') }}</label>
                                                            <input type="text" class="form-control form-control-sm shadow-sm" x-model="field.depends_on_value" :name="field.has_condition ? 'fields['+index+'][depends_on_value]' : ''" placeholder="{{ __('مثال: نعم') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <div x-show="fields.length === 0" class="text-center py-5 bg-body border border-dashed rounded-4 shadow-sm" style="border-width: 2px !important; border-color: #cbd5e1 !important;">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-layer-group fa-2x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-dark fw-bold">{{ __('لم تقم بإضافة أي حقول بعد') }}</h5>
                            <p class="text-muted mb-0">{{ __('اضغط على زر الإضافة للبدء في بناء نموذجك وتخصيصه') }}</p>
                        </div>
                    </div>

                    {{-- Add Field Button --}}
                    <div class="mt-4">
                        <button type="button" @click="addField()" class="add-field-btn">
                            <i class="fa-solid fa-plus-circle me-2 fs-5"></i> {{ __('إضافة حقل جديد') }}
                        </button>
                    </div>

                </div>
            </div>

            {{-- Submit Action --}}
            <div class="d-flex justify-content-end gap-2 mb-5 mt-4">
                <button type="submit" class="btn btn-save-template btn-lg rounded-pill px-5 py-3 fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-2 fs-5 align-middle"></i> <span class="align-middle">{{ __('حفظ واعتماد القالب') }}</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Live Preview Sidebar --}}
    <div class="col-lg-4 d-none d-lg-block">
        <div class="position-sticky" style="top: 80px;">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white py-3 border-0">
                    <h6 class="mb-0 fw-bold fs-6 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-mobile-screen"></i>
                        <span>{{ __('المعاينة الحية') }}</span>
                    </h6>
                </div>
                <div class="card-body p-4 bg-light" style="min-height: 400px; max-height: calc(100vh - 150px); overflow-y: auto;">
                    
                    {{-- Form Title Preview --}}
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-secondary-subtle" x-text="templateName || '{{ __('نموذج بدون عنوان') }}'"></h5>
                    
                    {{-- Form Description Preview --}}
                    <p class="text-muted small mb-4" x-show="templateDescription" x-text="templateDescription"></p>

                    <template x-for="field in fields" :key="field.id">
                        <div x-show="(!field.has_condition || (previewData[field.depends_on_field_name] == field.depends_on_value)) && field.type !== 'step-divider'" class="mb-4 p-3 bg-body border border-secondary-subtle rounded-3 shadow-sm transition" style="animation: fadeIn 0.3s ease;">
                            
                            <template x-if="field.type !== 'html'">
                                <div>
                                    <label class="form-label fw-bold text-dark">
                                        <span x-text="field.label || '{{ __('بدون عنوان') }}'"></span>
                                        <span x-show="field.is_required" class="text-danger ms-1">*</span>
                                    </label>

                                    <template x-if="field.type === 'text'">
                                        <input type="text" class="form-control bg-light" x-model="previewData[field.name]" placeholder="{{ __('إجابة نصية قصيرة') }}">
                                    </template>

                                    <template x-if="field.type === 'textarea'">
                                        <textarea class="form-control bg-light" rows="3" x-model="previewData[field.name]" placeholder="{{ __('إجابة نصية طويلة') }}"></textarea>
                                    </template>

                                    <template x-if="field.type === 'file'">
                                        <input type="file" class="form-control bg-light">
                                    </template>

                                    <template x-if="field.type === 'date'">
                                        <input type="date" class="form-control bg-light" x-model="previewData[field.name]">
                                    </template>

                                    <template x-if="field.type === 'select'">
                                        <select class="form-select bg-light" x-model="previewData[field.name]">
                                            <option value="">{{ __('اختر من القائمة') }}</option>
                                            <template x-for="option in (field.options || '').split(',').filter(o => o.trim() !== '')">
                                                <option :value="option.trim()" x-text="option.trim()"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <template x-if="field.type === 'multi-select'">
                                        <select class="form-select bg-light" multiple style="height: 100px;">
                                            <template x-for="option in (field.options || '').split(',').filter(o => o.trim() !== '')">
                                                <option :value="option.trim()" x-text="option.trim()"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <template x-if="field.type === 'radio'">
                                        <div>
                                            <template x-for="(option, idx) in (field.options || '').split(',').filter(o => o.trim() !== '')">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" :name="'preview_radio_' + field.name" :id="'prv_' + field.name + '_' + idx" :value="option.trim()" x-model="previewData[field.name]">
                                                    <label class="form-check-label small" :for="'prv_' + field.name + '_' + idx" x-text="option.trim()"></label>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="field.type === 'checkbox'">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" value="1" x-model="previewData[field.name]">
                                            <label class="form-check-label small" x-text="field.label || '{{ __('خيار') }}'"></label>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="field.type === 'html'">
                                <div>
                                    <h5 class="fw-bold text-dark mb-2" x-show="field.label" x-text="field.label"></h5>
                                    <div class="p-3 bg-light rounded" x-html="field.description || '<small class=\'text-muted\'>لن يظهر نص هنا لأنك لم تدخل محتوى بعد.</small>'"></div>
                                </div>
                            </template>
                        </div>
                        
                        <template x-if="field.type === 'step-divider'">
                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1 border-danger opacity-50">
                                <span class="mx-3 badge bg-danger rounded-pill px-3 py-2 shadow-sm" x-text="field.label || '{{ __('خطوة جديدة') }}'"></span>
                                <hr class="flex-grow-1 border-danger opacity-50">
                            </div>
                        </template>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="fields.length === 0" class="text-center text-muted py-5">
                        <i class="fa-solid fa-ghost fs-1 mb-3 opacity-25"></i>
                        <p class="mb-0 fw-bold">{{ __('المعاينة فارغة') }}</p>
                        <small>{{ __('قم بإضافة حقول لرؤيتها هنا') }}</small>
                    </div>

                    {{-- Submit button mockup --}}
                    <button x-show="fields.length > 0" type="button" class="btn btn-danger w-100 rounded-pill fw-bold mt-2" disabled>{{ __('إرسال الطلب') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formBuilder', () => ({
            fields: [],
            fieldIdCounter: 1,
            templateName: '{{ old('name') }}',
            templateDescription: '{{ old('description') }}',
            previewData: {},
            
            fieldTypes: [
                { value: 'text', label: 'نص قصير', icon: 'fa-solid fa-minus' },
                { value: 'textarea', label: 'نص طويل', icon: 'fa-solid fa-align-justify' },
                { value: 'file', label: 'مرفق (صورة / PDF)', icon: 'fa-solid fa-paperclip' },
                { value: 'select', label: 'قائمة منسدلة', icon: 'fa-solid fa-list' },
                { value: 'multi-select', label: 'تحديد متعدد', icon: 'fa-solid fa-list-check' },
                { value: 'radio', label: 'أزرار اختيار', icon: 'fa-regular fa-circle-dot' },
                { value: 'checkbox', label: 'مربع اختيار', icon: 'fa-regular fa-square-check' },
                { value: 'date', label: 'تاريخ', icon: 'fa-solid fa-calendar-days' },
                { value: 'html', label: 'كتلة نص / HTML', icon: 'fa-solid fa-code' },
                { value: 'step-divider', label: 'فاصل خطوات', icon: 'fa-solid fa-shoe-prints' }
            ],
            getFieldTypeLabel(val) {
                const ft = this.fieldTypes.find(f => f.value === val);
                return ft ? ft.label : 'اختر النوع';
            },
            getFieldTypeIcon(val) {
                const ft = this.fieldTypes.find(f => f.value === val);
                return ft ? ft.icon : 'fa-solid fa-question';
            },

            init() {
                // Add a default first field
                this.addField();

                this.$nextTick(() => {
                    new Sortable(this.$refs.sortableContainer, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'bg-light',
                        onEnd: (evt) => {
                            if (evt.oldIndex !== evt.newIndex) {
                                // Undo SortableJS DOM mutation
                                const itemEl = evt.item;
                                evt.from.insertBefore(itemEl, evt.from.children[evt.oldIndex]);
                                
                                // Update Alpine Array
                                const movedItem = this.fields.splice(evt.oldIndex, 1)[0];
                                this.fields.splice(evt.newIndex, 0, movedItem);
                            }
                        }
                    });
                });
            },

            addField() {
                this.fields.push({
                    id: this.fieldIdCounter++,
                    name: '',
                    label: '',
                    type: 'text',
                    is_required: true,
                    options: '',
                    description: '',
                    has_condition: false,
                    depends_on_field_name: '',
                    depends_on_value: ''
                });
            },

            removeField(index) {
                if(confirm('هل أنت متأكد من حذف هذا الحقل؟')) {
                    this.fields.splice(index, 1);
                }
            }
        }))
    })

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('templateForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get submit button (there might be multiple, get the one in the top header or preview)
                let btn = document.activeElement;
                if(!btn || btn.type !== 'submit') {
                    btn = form.querySelector('button[type="submit"]');
                }
                
                let originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...';
                btn.disabled = true;

                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                let errorContainer = document.getElementById('formErrorsContainer');
                if(errorContainer) errorContainer.innerHTML = '';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(async res => {
                    const data = await res.json();
                    if(res.ok && data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ بنجاح',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            alert(data.message);
                            window.location.href = data.redirect;
                        }
                    } else if (res.status === 422) {
                        // Validation errors
                        let errorsHtml = '<div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4"><ul class="mb-0 ps-3">';
                        for(let key in data.errors) {
                            errorsHtml += `<li>${data.errors[key][0]}</li>`;
                            // Try to highlight field
                            let fieldName = key;
                            if (key.includes('.')) {
                                const parts = key.split('.');
                                fieldName = `${parts[0]}[${parts[1]}][${parts[2]}]`;
                            }
                            let field = form.querySelector(`[name="${fieldName}"]`);
                            if(field) field.classList.add('is-invalid');
                        }
                        errorsHtml += '</ul></div>';
                        
                        if(errorContainer) {
                            errorContainer.innerHTML = errorsHtml;
                        }
                        
                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    } else {
                        // Generic error
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('خطأ', data.message || 'حدث خطأ غير متوقع', 'error');
                        } else {
                            alert(data.message || 'حدث خطأ غير متوقع');
                        }
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('خطأ', 'حدث خطأ أثناء الاتصال بالخادم', 'error');
                    } else {
                        alert('حدث خطأ أثناء الاتصال بالخادم');
                    }
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
            });
        }
    });

    // Helper to generate clean API keys from Arabic/English titles
    window.slugifyArabic = function(text) {
        if (!text) return 'field_' + Math.floor(Math.random() * 1000);
        
        const arMap = {
            'أ':'a','إ':'e','آ':'a','ا':'a','ب':'b','ت':'t','ث':'th',
            'ج':'j','ح':'h','خ':'kh','د':'d','ذ':'dh','ر':'r','ز':'z',
            'س':'s','ش':'sh','ص':'s','ض':'d','ط':'t','ظ':'z','ع':'a',
            'غ':'gh','ف':'f','ق':'q','ك':'k','ل':'l','م':'m','ن':'n',
            'ه':'h','و':'w','ي':'y','ة':'a','ى':'a','ئ':'e','ء':'a','ؤ':'o',
            ' ':'_','-':'_'
        };

        let slug = '';
        for (let i = 0; i < text.length; i++) {
            let char = text[i];
            if (arMap[char]) {
                slug += arMap[char];
            } else if (/[a-zA-Z0-9_]/.test(char)) {
                slug += char.toLowerCase();
            }
        }
        
        slug = slug.replace(/_+/g, '_').replace(/^_|_$/g, '');
        return slug || 'field_' + Math.floor(Math.random() * 1000);
    };
</script>
@endsection
