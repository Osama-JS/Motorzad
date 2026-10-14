{{-- Bilingual Permissions Selector Component for Add/Edit Roles --}}
@php
    $prefix = $prefix ?? 'add';
    $isEdit = $isEdit ?? false;
    $checkboxClass = $isEdit ? 'edit-perm-checkbox' : 'add-perm-checkbox';
    $containerId = $prefix . '_perm_selector_container';
    $currentLocale = app()->getLocale();
    $isEn = ($currentLocale === 'en');
    
    // Always compute grouped permissions for currentLocale
    $groupedPermissions = \App\Services\PermissionRegistry::groupPermissions($permissions ?? \Spatie\Permission\Models\Permission::all(), $currentLocale);
    $totalPermCount = isset($permissions) && is_countable($permissions) ? count($permissions) : \Spatie\Permission\Models\Permission::count();
@endphp

<div class="perm-selector-wrapper" id="{{ $containerId }}" data-prefix="{{ $prefix }}">
    {{-- Top Toolbar: Search & Global Actions --}}
    <div class="perm-toolbar p-3 mb-3 rounded-3" style="background: var(--bg-card); border: 1px solid var(--border);">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-1 perm-search-box" placeholder="{{ $isEn ? 'Search permissions by name, code, or description...' : 'بحث في الصلاحيات بالاسم أو الشرح أو الرمز...' }}">
                </div>
            </div>
            <div class="col-12 col-md-7 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1 perm-global-counter" style="background: rgba(13, 110, 253, 0.1); color: var(--primary); font-size: 0.82rem; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>{{ $isEn ? 'Selected' : 'تم اختيار' }}:</span>
                    <b class="perm-selected-count">0</b> / <span class="perm-total-count">{{ $totalPermCount }}</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-primary perm-btn-select-all px-2 py-1" style="font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    {{ $isEn ? 'Select All' : 'تحديد الكل' }}
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary perm-btn-deselect-all px-2 py-1" style="font-size: 0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    {{ $isEn ? 'Deselect All' : 'إلغاء التحديد' }}
                </button>
            </div>
        </div>

        {{-- Categories Filter Pills --}}
        <div class="perm-category-pills d-flex align-items-center gap-2 overflow-auto pt-2 mt-2 border-top" style="white-space: nowrap; scrollbar-width: thin;">
            <button type="button" class="btn btn-sm perm-filter-pill active" data-target="all" style="font-size: 0.75rem; border-radius: 20px;">
                <span>{{ $isEn ? 'All Categories' : 'عرض كافة الأقسام' }}</span>
                <span class="badge bg-secondary ms-1 rounded-pill">{{ $totalPermCount }}</span>
            </button>
            @foreach($groupedPermissions as $catKey => $category)
            <button type="button" class="btn btn-sm perm-filter-pill" data-target="{{ $catKey }}" style="font-size: 0.75rem; border-radius: 20px;">
                {!! $category['icon'] !!}
                <span class="ms-1">{{ $category['name'] }}</span>
                <span class="badge rounded-pill ms-1 pill-count" style="background: rgba(0,0,0,0.08); color: inherit;">
                    {{ count($category['permissions']) }}
                </span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Categories Sections List --}}
    <div class="perm-groups-container">
        @foreach($groupedPermissions as $catKey => $category)
        <div class="card mb-3 border perm-category-section shadow-none" data-category="{{ $catKey }}" style="background: var(--bg-card); border-color: var(--border) !important; border-radius: 10px;">
            {{-- Category Header --}}
            <div class="card-header border-bottom py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: rgba(0,0,0,0.015);">
                <div class="d-flex align-items-center gap-2">
                    <span class="perm-cat-icon-badge d-flex align-items-center justify-content-center text-{{ $category['badge_color'] }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(var(--bs-{{ $category['badge_color'] }}-rgb, 13, 110, 253), 0.1);">
                        {!! $category['icon'] !!}
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 0.92rem; color: var(--text-color);">
                            {{ $category['name'] }}
                        </h6>
                        <small class="text-muted d-none d-md-block" style="font-size: 0.75rem;">{{ $category['description'] }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge rounded-pill bg-light text-dark border px-2 py-1 cat-counter" style="font-size: 0.75rem;">
                        <b class="cat-selected-count">0</b> / {{ count($category['permissions']) }}
                    </span>
                    <div class="form-check m-0 d-flex align-items-center">
                        <input class="form-check-input perm-cat-toggle-all" type="checkbox" id="{{ $prefix }}_cat_toggle_{{ $catKey }}">
                        <label class="form-check-label small ms-1 text-muted" for="{{ $prefix }}_cat_toggle_{{ $catKey }}" style="cursor: pointer; font-size: 0.8rem; user-select: none;">
                            {{ $isEn ? 'Select Category' : 'تحديد القسم' }}
                        </label>
                    </div>
                </div>
            </div>

            {{-- Category Permissions Grid --}}
            <div class="card-body p-3">
                <div class="perm-cards-grid">
                    @foreach($category['permissions'] as $perm)
                    <label class="perm-card" for="{{ $prefix }}_perm_{{ $perm->id }}" data-keywords="{{ strtolower($perm->name . ' ' . $perm->label_ar . ' ' . $perm->label_en . ' ' . $perm->desc_ar . ' ' . $perm->desc_en) }}">
                        <div class="perm-card-header d-flex align-items-start justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $perm->id }}" 
                                       id="{{ $prefix }}_perm_{{ $perm->id }}" 
                                       class="form-check-input perm-checkbox {{ $checkboxClass }} mt-0">
                                <span class="perm-card-title fw-bold" style="font-size: 0.86rem; color: var(--text-color);">
                                    {{ $perm->label }}
                                </span>
                            </div>
                            <span class="badge perm-badge-tech" style="font-size: 0.65rem; background: rgba(0,0,0,0.05); color: var(--text-muted); font-family: monospace;">
                                {{ $perm->badge }}
                            </span>
                        </div>
                        <div class="perm-card-body ps-4 ms-1 mt-1">
                            <p class="perm-card-desc mb-1 text-muted" style="font-size: 0.76rem; line-height: 1.35;">
                                {{ $perm->desc }}
                            </p>
                            <span class="perm-slug-tag text-muted" style="font-size: 0.68rem; font-family: monospace;">
                                <code>{{ $perm->name }}</code>
                            </span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty Search Results Notice --}}
    <div class="perm-empty-search text-center py-4 d-none">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <h6 class="text-muted mb-0">{{ $isEn ? 'No permissions match your search criteria' : 'لم يتم العثور على أي صلاحيات مطابقة لكلمات البحث' }}</h6>
    </div>
</div>
