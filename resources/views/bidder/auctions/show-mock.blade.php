@extends('layouts.bidder')

@section('title', $auctionData['title_' . app()->getLocale()])

@section('css')
<style>
/* Car Diagram Styling */
.car-part {
    transition: fill 0.2s, stroke 0.2s, filter 0.2s;
}
.car-part.damage-scratch {
    fill: rgba(245, 158, 11, 0.35) !important;
    stroke: #f59e0b !important;
    filter: drop-shadow(0px 0px 4px rgba(245, 158, 11, 0.4));
}
.car-part.damage-dent {
    fill: rgba(239, 68, 68, 0.35) !important;
    stroke: #ef4444 !important;
    filter: drop-shadow(0px 0px 4px rgba(239, 68, 68, 0.4));
}
.car-part.damage-repainted {
    fill: rgba(16, 185, 129, 0.35) !important;
    stroke: #10b981 !important;
    filter: drop-shadow(0px 0px 4px rgba(16, 185, 129, 0.4));
}
.car-part.damage-broken {
    fill: rgba(168, 85, 247, 0.35) !important;
    stroke: #a855f7 !important;
    filter: drop-shadow(0px 0px 4px rgba(168, 85, 247, 0.4));
}
/* ===== PREMIUM SINGLE AUCTION VIEW ===== */
.auc-detail-layout {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.detail-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    padding: 1.75rem;
}

/* Image Showcase */
.image-showcase {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    background: #0b0f19;
}
.main-img-wrap {
    position: relative;
    padding-top: 56.25%; /* 16:9 */
}
.main-img-wrap img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-thumbs {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.thumb-item {
    flex: 1;
    aspect-ratio: 16/10;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
    background: #0b0f19;
}
.thumb-item:hover {
    opacity: 0.9;
}
.thumb-item.active {
    border-color: var(--brand-red);
}
.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Tabs */
.detail-tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
    gap: 1.5rem;
}
.detail-tab-btn {
    padding: 0.75rem 0.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-muted);
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
}
.detail-tab-btn:hover {
    color: var(--text);
}
.detail-tab-btn.active {
    color: var(--brand-red-light);
    border-bottom-color: var(--brand-red);
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

/* Specs Grid */
.specs-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}
.spec-box {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.spec-box-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(229, 62, 62, 0.08);
    color: var(--brand-red);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.spec-box-icon svg {
    width: 20px;
    height: 20px;
}
.spec-box-info {
    display: flex;
    flex-direction: column;
}
.spec-box-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
}
.spec-box-value {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text);
}

/* Bidding Sidebar Card */
.bid-panel {
    background: linear-gradient(180deg, var(--bg-card) 0%, rgba(229, 62, 62, 0.02) 100%);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.status-badge-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.timer-panel {
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price-summary {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.price-summary .value {
    font-size: 1.75rem;
    font-weight: 900;
    color: var(--brand-red-light);
}

/* Bidding Form */
.bid-form-group {
    margin-bottom: 1.25rem;
}
.bid-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.bid-input-wrap input {
    width: 100%;
    background: var(--bg-input);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1rem 1rem 4.5rem;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text);
    text-align: center;
    transition: all 0.3s;
}
html[dir="rtl"] .bid-input-wrap input {
    padding: 1rem 4.5rem 1rem 1rem;
}
.bid-input-wrap input:focus {
    outline: none;
    border-color: var(--brand-red);
    box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.15);
}
.currency-suffix {
    position: absolute;
    left: 1.25rem;
    font-weight: 800;
    font-size: 1rem;
    color: var(--text-secondary);
}
html[dir="rtl"] .currency-suffix {
    left: auto;
    right: 1.25rem;
}

.quick-bids {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
.quick-bid-btn {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--text);
    cursor: pointer;
    transition: all 0.3s;
}
.quick-bid-btn:hover {
    background: rgba(229, 62, 62, 0.08);
    border-color: var(--brand-red);
    color: var(--brand-red-light);
}

.btn-submit-bid {
    width: 100%;
    background: linear-gradient(135deg, var(--brand-red), #991b1b);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 5px 20px rgba(229, 62, 62, 0.25);
    transition: all 0.3s;
}
.btn-submit-bid:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
}

/* Bids feed */
.bids-feed-box {
    margin-top: 2rem;
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
}
.feed-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.feed-header h4 {
    font-weight: 800;
    margin: 0;
}
.feed-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 250px;
    overflow-y: auto;
}
.feed-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0.85rem;
    background: var(--bg-hover);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    font-size: 0.8rem;
    transition: all 0.3s;
}
.feed-item.new-bid {
    animation: highlight-green 2s ease-out;
}
@keyframes highlight-green {
    0% { background: rgba(16, 185, 129, 0.2); }
    100% { background: var(--bg-hover); }
}

@media(max-width: 992px) {
    .auc-detail-layout {
        grid-template-columns: 1fr;
    }
    .bid-panel {
        position: static;
    }
}
</style>
@endsection

@section('content')

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('bidder.auctions.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        {{ app()->getLocale() === 'ar' ? 'العودة للمزادات' : 'Back to Auctions' }}
    </a>
</div>

<div class="auc-detail-layout">
    {{-- Left Side: Images & Info --}}
    <div>
        <div class="detail-card">
            {{-- Image slider --}}
            <div class="image-showcase">
                <div class="main-img-wrap">
                    <img id="mainShowcaseImg" src="{{ $auctionData['image'] }}" alt="{{ $auctionData['title_' . app()->getLocale()] }}">
                </div>
            </div>

            <div class="gallery-thumbs">
                <div class="thumb-item active" onclick="switchImage(this, '{{ $auctionData['image'] }}')">
                    <img src="{{ $auctionData['image'] }}" alt="Main image">
                </div>
                <div class="thumb-item" onclick="switchImage(this, 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&fit=crop')">
                    <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&fit=crop" alt="Side view">
                </div>
                <div class="thumb-item" onclick="switchImage(this, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&fit=crop')">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&fit=crop" alt="Interior">
                </div>
                <div class="thumb-item" onclick="switchImage(this, 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&fit=crop')">
                    <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&fit=crop" alt="Engine">
                </div>
            </div>

            <h1 style="font-size: 1.8rem; font-weight: 900; color: var(--text); margin-bottom: 1.5rem;">
                {{ $auctionData['title_' . app()->getLocale()] }}
            </h1>

            {{-- Tabs --}}
            <div class="detail-tabs">
                <button class="detail-tab-btn active" onclick="switchTab(this, 'specsTab')">
                    {{ app()->getLocale() === 'ar' ? 'المواصفات التقنية' : 'Specifications' }}
                </button>
                <button class="detail-tab-btn" onclick="switchTab(this, 'descTab')">
                    {{ app()->getLocale() === 'ar' ? 'تقرير الفحص والوصف' : 'Description & Issues' }}
                </button>
                <button class="detail-tab-btn" onclick="switchTab(this, 'priceTab')">
                    {{ app()->getLocale() === 'ar' ? 'تحليل الأسعار' : 'Price History' }}
                </button>
            </div>

            {{-- Specs Tab Content --}}
            <div id="specsTab" class="tab-content active">
                <div class="specs-detail-grid">
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'سنة الصنع' : 'Year' }}</span>
                            <span class="spec-box-value">{{ $auctionData['year'] }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Condition' }}</span>
                            <span class="spec-box-value">{{ ucfirst($auctionData['condition']) }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'ناقل الحركة' : 'Transmission' }}</span>
                            <span class="spec-box-value">{{ ucfirst($auctionData['transmission']) }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'عداد المسافة' : 'Mileage' }}</span>
                            <span class="spec-box-value">{{ number_format($auctionData['mileage']) }} KM</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'سعة المحرك' : 'Engine' }}</span>
                            <span class="spec-box-value">{{ $auctionData['engine_capacity'] }}</span>
                        </div>
                    </div>
                    <div class="spec-box">
                        <div class="spec-box-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                        <div class="spec-box-info">
                            <span class="spec-box-label">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</span>
                            <span class="spec-box-value">{{ $auctionData['location'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Tab Content --}}
            <div id="descTab" class="tab-content">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.75rem;">{{ app()->getLocale() === 'ar' ? 'وصف المركبة' : 'Vehicle Description' }}</h3>
                <p style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem; margin-bottom: 1.5rem;">
                    {{ $auctionData['description_' . app()->getLocale()] }}
                </p>
                <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.15); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <strong style="color: #f59e0b; display: block; margin-bottom: 0.25rem;">⚠️ {{ app()->getLocale() === 'ar' ? 'ملاحظات الفحص والعيوب' : 'Inspection & Wear Notes' }}</strong>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        @if(isset($auctionData['damage_points']) && count($auctionData['damage_points']) > 0)
                            {{ app()->getLocale() === 'ar' ? 'توجد بعض ملاحظات الفحص الموضحة بالرسم البياني أدناه.' : 'Inspection notes mapped in the diagram below.' }}
                        @else
                            {{ app()->getLocale() === 'ar' ? 'السيارة نظيفة جداً وخالية من المشاكل والعيوب.' : 'Excellent condition, no wear or defects registered.' }}
                        @endif
                    </span>
                </div>

                @if(isset($auctionData['damage_points']) && count($auctionData['damage_points']) > 0)
                    @php
                        $damagePointsMap = [];
                        $damagePointsDetails = [];
                        foreach ($auctionData['damage_points'] as $p) {
                            if (isset($p['part']) && isset($p['type'])) {
                                $damagePointsMap[$p['part']] = $p['type'];
                                $damagePointsDetails[$p['part']] = $p;
                            }
                        }

                        $renderPart = function($partId, $defaultLabelAr, $defaultLabelEn) use ($damagePointsMap, $damagePointsDetails) {
                            $class = isset($damagePointsMap[$partId]) ? 'damage-' . $damagePointsMap[$partId] : '';
                            $titleHtml = '';
                            if (isset($damagePointsDetails[$partId])) {
                                $dp = $damagePointsDetails[$partId];
                                $typeT = match($dp['type']) {
                                    'scratch' => __('Scratch'),
                                    'dent' => __('Dent'),
                                    'repainted' => __('Repainted'),
                                    'broken' => __('Broken'),
                                    default => $dp['type']
                                };
                                $label = app()->getLocale() === 'ar' ? ($dp['label_ar'] ?? $defaultLabelAr) : ($dp['label_en'] ?? $defaultLabelEn);
                                $note = !empty($dp['note']) ? ' (' . $dp['note'] . ')' : '';
                                $titleHtml = '<title>' . e($label . ': ' . $typeT . $note) . '</title>';
                            }
                            return [
                                'class' => $class,
                                'title' => $titleHtml
                            ];
                        };
                    @endphp

                    <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: 12px; margin-top: 1rem;">
                        <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-warning"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'تقرير الفحص البصري وهيكل السيارة' : 'Visual Inspection & Body Report' }}
                        </h4>
                        
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center mb-3 mb-md-0" style="background: rgba(30, 41, 59, 0.03); padding: 15px; border-radius: 12px; border: 1px solid var(--border);">
                                <svg viewBox="0 0 600 350" class="car-diagram-svg read-only-diagram" style="width:100%; height:auto; max-width:420px;">
                                    <rect x="5" y="5" width="590" height="340" rx="15" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="2"/>
                                    
                                    <!-- Front Bumper -->
                                    @php $p = $renderPart('front_bumper', 'صدام أمامي', 'Front Bumper'); @endphp
                                    <path d="M 240,60 C 270,55 330,55 360,60 L 360,75 C 330,72 270,72 240,75 Z" class="car-part {{ $p['class'] }}" data-part="front_bumper" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Hood -->
                                    @php $p = $renderPart('hood', 'الكبوت (غطاء المحرك)', 'Hood'); @endphp
                                    <path d="M 243,78 L 357,78 L 350,130 L 250,130 Z" class="car-part {{ $p['class'] }}" data-part="hood" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Windshield -->
                                    @php $p = $renderPart('windshield', 'الزجاج الأمامي', 'Windshield'); @endphp
                                    <path d="M 252,133 L 348,133 L 342,160 L 258,160 Z" class="car-part {{ $p['class'] }}" data-part="windshield" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Roof -->
                                    @php $p = $renderPart('roof', 'السقف', 'Roof'); @endphp
                                    <rect x="256" y="163" width="88" height="70" rx="5" class="car-part {{ $p['class'] }}" data-part="roof" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</rect>
                                    <!-- Rear Windshield -->
                                    @php $p = $renderPart('rear_windshield', 'الزجاج الخلفي', 'Rear Windshield'); @endphp
                                    <path d="M 258,236 L 342,236 L 348,260 L 252,260 Z" class="car-part {{ $p['class'] }}" data-part="rear_windshield" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Trunk / Tailgate -->
                                    @php $p = $renderPart('trunk', 'الشنطة', 'Trunk'); @endphp
                                    <path d="M 250,263 L 350,263 L 355,310 L 245,310 Z" class="car-part {{ $p['class'] }}" data-part="trunk" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Rear Bumper -->
                                    @php $p = $renderPart('rear_bumper', 'صدام خلفي', 'Rear Bumper'); @endphp
                                    <path d="M 240,313 C 270,318 330,318 360,313 L 360,325 C 330,322 270,322 240,325 Z" class="car-part {{ $p['class'] }}" data-part="rear_bumper" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    
                                    <!-- Left Front Fender -->
                                    @php $p = $renderPart('left_fender_front', 'رفرف أمامي أيسر', 'Left Front Fender'); @endphp
                                    <path d="M 200,65 Q 235,68 238,100 L 238,125 L 205,125 Z" class="car-part {{ $p['class'] }}" data-part="left_fender_front" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Left Front Door -->
                                    @php $p = $renderPart('left_door_front', 'باب أمامي أيسر', 'Left Front Door'); @endphp
                                    <rect x="205" y="128" width="46" height="50" class="car-part {{ $p['class'] }}" data-part="left_door_front" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</rect>
                                    <!-- Left Rear Door -->
                                    @php $p = $renderPart('left_door_rear', 'باب خلفي أيسر', 'Left Rear Door'); @endphp
                                    <rect x="205" y="181" width="46" height="50" class="car-part {{ $p['class'] }}" data-part="left_door_rear" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</rect>
                                    <!-- Left Rear Fender -->
                                    @php $p = $renderPart('left_fender_rear', 'رفرف خلفي أيسر', 'Left Rear Fender'); @endphp
                                    <path d="M 205,234 L 238,234 L 238,270 Q 235,302 200,305 Z" class="car-part {{ $p['class'] }}" data-part="left_fender_rear" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    
                                    <!-- Right Front Fender -->
                                    @php $p = $renderPart('right_fender_front', 'رفرف أمامي أيمن', 'Right Front Fender'); @endphp
                                    <path d="M 400,65 Q 365,68 362,100 L 362,125 L 395,125 Z" class="car-part {{ $p['class'] }}" data-part="right_fender_front" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    <!-- Right Front Door -->
                                    @php $p = $renderPart('right_door_front', 'باب أمامي أيمن', 'Right Front Door'); @endphp
                                    <rect x="349" y="128" width="46" height="50" class="car-part {{ $p['class'] }}" data-part="right_door_front" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</rect>
                                    <!-- Right Rear Door -->
                                    @php $p = $renderPart('right_door_rear', 'باب خلفي أيمن', 'Right Rear Door'); @endphp
                                    <rect x="349" y="181" width="46" height="50" class="car-part {{ $p['class'] }}" data-part="right_door_rear" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</rect>
                                    <!-- Right Rear Fender -->
                                    @php $p = $renderPart('right_fender_rear', 'رفرف خلفي أيمن', 'Right Rear Fender'); @endphp
                                    <path d="M 395,234 L 362,234 L 362,270 Q 365,302 400,305 Z" class="car-part {{ $p['class'] }}" data-part="right_fender_rear" fill="rgba(0,0,0,0.02)" stroke="#94a3b8" stroke-width="1.5">{!! $p['title'] !!}</path>
                                    
                                    <!-- Wheels -->
                                    <rect x="180" y="85" width="20" height="35" rx="5" fill="#334155" />
                                    <rect x="400" y="85" width="20" height="35" rx="5" fill="#334155" />
                                    <rect x="180" y="245" width="20" height="35" rx="5" fill="#334155" />
                                    <rect x="400" y="245" width="20" height="35" rx="5" fill="#334155" />
                                </svg>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush px-0" style="font-size:0.85rem; padding-left:0; padding-right:0;">
                                    @foreach($auctionData['damage_points'] as $point)
                                        @php
                                            $label = app()->getLocale() === 'ar' ? ($point['label_ar'] ?? $point['part']) : ($point['label_en'] ?? $point['part']);
                                            $badgeColor = match($point['type']) {
                                                'scratch' => 'warning',
                                                'dent' => 'danger',
                                                'repainted' => 'success',
                                                'broken' => 'purple',
                                                default => 'secondary'
                                            };
                                            $typeText = match($point['type']) {
                                                'scratch' => __('Scratch'),
                                                'dent' => __('Dent'),
                                                'repainted' => __('Repainted'),
                                                'broken' => __('Broken'),
                                                default => $point['type']
                                            };
                                        @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-start px-0 bg-transparent border-0 mb-2">
                                            <div class="ms-2 me-auto text-start">
                                                <div class="fw-bold" style="color:var(--text); font-weight:700;">{{ $label }}</div>
                                                @if(!empty($point['note']))
                                                    <span class="text-muted small">{{ $point['note'] }}</span>
                                                @endif
                                            </div>
                                            <span class="badge bg-{{ $badgeColor }}" style="{{ $point['type'] === 'broken' ? 'background-color:#a855f7 !important;' : '' }}">{{ $typeText }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Price History Tab Content --}}
            <div id="priceTab" class="tab-content">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.75rem;">
                    {{ app()->getLocale() === 'ar' ? 'منحنى تطور سعر المزايدة بمرور الوقت' : 'Price Progression Curve Over Time' }}
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">
                    {{ app()->getLocale() === 'ar' ? 'يوضح هذا الرسم البياني تاريخ وتغير الأسعار ووتيرة المزايدة للمساعدة في تخطيط عروضك.' : 'This chart displays the history of bids and price progression to help you plan your bids.' }}
                </p>
                <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: 12px; height: 320px; position: relative; margin-top: 1rem;">
                    <canvas id="priceHistoryChart" style="width: 100%; height: 100%; max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Side: Bidding Module --}}
    <div>
        <div class="bid-panel">
            <div class="status-badge-panel">
                <span class="w-badge status approved" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.4rem;">
                    <span class="pulse-dot" style="background:#10b981; box-shadow:0 0 8px #10b981;"></span>
                    {{ app()->getLocale() === 'ar' ? 'مزايدة حية' : 'Live Auction' }}
                </span>
                
                <div class="timer-panel">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span id="countdownTimer">03:14:02</span>
                </div>
            </div>

            <div class="price-summary">
                <div class="price-block">
                    <span class="price-label">{{ app()->getLocale() === 'ar' ? 'المزايدة الحالية' : 'Current Price' }}</span>
                    <span class="value" id="currentPriceVal">{{ number_format($auctionData['current_price']) }}</span>
                </div>
                <span style="font-weight: 800; color: var(--text-secondary);">SAR</span>
            </div>

            {{-- Bid Mode Selector --}}
            <div class="bid-mode-selector mb-3" style="display: flex; gap: 0.5rem; background: var(--bg-hover); padding: 0.25rem; border-radius: 12px; border: 1px solid var(--border);">
                <button type="button" class="btn btn-sm flex-grow-1 py-2 rounded-pill bid-mode-btn" id="btnModeManual" onclick="setBidMode('manual')" style="font-weight: 700; font-size: 0.8rem; border: none; background: var(--brand-red); color: white; transition: all 0.3s;">
                    {{ app()->getLocale() === 'ar' ? 'مزايدة عادية' : 'Manual Bid' }}
                </button>
                <button type="button" class="btn btn-sm flex-grow-1 py-2 rounded-pill bid-mode-btn text-muted" id="btnModeAuto" onclick="setBidMode('auto')" style="font-weight: 700; font-size: 0.8rem; border: none; background: transparent; color: var(--text-muted); transition: all 0.3s;">
                    {{ app()->getLocale() === 'ar' ? 'مزايدة تلقائية' : 'Auto Bid' }}
                </button>
            </div>

            {{-- Manual Bid Group --}}
            <div class="bid-form-group" id="manualBidGroup">
                <label class="form-label" id="bidAmountLabel" style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                    {{ app()->getLocale() === 'ar' ? 'قيمة المزايدة الحالية' : 'Bid Amount' }}
                </label>
                <div class="bid-input-wrap">
                    <input type="number" id="bidAmountInput" value="{{ $auctionData['current_price'] + $auctionData['min_bid_increment'] }}" min="{{ $auctionData['current_price'] + $auctionData['min_bid_increment'] }}" step="{{ $auctionData['min_bid_increment'] }}">
                    <span class="currency-suffix">SAR</span>
                </div>
                <small style="display: block; margin-top: 0.5rem; font-weight: 700; color: var(--text-muted); text-align: center;">
                    {{ app()->getLocale() === 'ar' ? 'الزيادة الدنيا' : 'Minimum increment' }}: +{{ number_format($auctionData['min_bid_increment']) }} SAR
                </small>
            </div>

            {{-- Auto Bid Group (Hidden initially) --}}
            <div class="bid-form-group" id="autoBidLimitGroup" style="display: none; margin-bottom: 1rem;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                    {{ app()->getLocale() === 'ar' ? 'الحد الأقصى للمزايدة التلقائية' : 'Maximum Auto Bid Limit' }}
                </label>
                <div class="bid-input-wrap">
                    <input type="number" id="maxAutoBidInput" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل أقصى سعر' : 'Enter max amount' }}" min="{{ $auctionData['current_price'] + $auctionData['min_bid_increment'] * 2 }}" step="{{ $auctionData['min_bid_increment'] }}">
                    <span class="currency-suffix">SAR</span>
                </div>
                <small style="display: block; margin-top: 0.5rem; font-size: 0.7rem; color: var(--text-muted); text-align: center;">
                    {{ app()->getLocale() === 'ar' ? 'سيقوم النظام بالمزايدة بالنيابة عنك حتى هذا الحد' : 'System will automatically bid up to this limit' }}
                </small>
            </div>

            <div class="quick-bids">
                <button class="quick-bid-btn" id="quickBidBtn1" onclick="applyQuickBid(this)">+1,000</button>
                <button class="quick-bid-btn" id="quickBidBtn2" onclick="applyQuickBid(this)">+5,000</button>
                <button class="quick-bid-btn" id="quickBidBtn3" onclick="applyQuickBid(this)">+10,000</button>
            </div>

            <button class="btn-submit-bid" onclick="placeBidNow()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                {{ app()->getLocale() === 'ar' ? 'تقديم عرض المزايدة' : 'Place Your Bid' }}
            </button>

            <div style="margin-top: 1.5rem; background: var(--bg-hover); border: 1px solid var(--border); padding: 1rem; border-radius: 12px; display: flex; gap: 0.75rem; align-items: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #10b981; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                <div style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;">
                    <strong>{{ app()->getLocale() === 'ar' ? 'تأمين المزاد مفعل' : 'Deposit Protection Active' }}</strong><br>
                    {{ app()->getLocale() === 'ar' ? 'تأمين المشاركة مطلوب ومحجوز من محفظتك ويسترد فوراً حال خسارة المزاد.' : 'Participation deposit is locked from wallet and refunded instantly if you lose the auction.' }}
                </div>
            </div>

            {{-- Interactive Bid Feed --}}
            <div class="bids-feed-box">
                <div class="feed-header">
                    <h4>{{ app()->getLocale() === 'ar' ? 'سجل المزايدات الحية' : 'Live Bids Log' }}</h4>
                    <span class="w-badge" style="background: rgba(229,62,62,0.1); color: var(--brand-red-light); font-weight: 800;" id="bidsCountBadge">
                        {{ $auctionData['bids_count'] }} {{ app()->getLocale() === 'ar' ? 'مزايدة' : 'Bids' }}
                    </span>
                </div>
                
                <div class="feed-list" id="bidsFeedList">
                    <div class="feed-item">
                        <span style="font-weight: 800; color: var(--brand-red-light);">{{ number_format($auctionData['current_price']) }} SAR</span>
                        <span style="opacity: 0.65;">{{ app()->getLocale() === 'ar' ? 'أنت (مزايد)' : 'You (Bidder)' }}</span>
                    </div>
                    <div class="feed-item">
                        <span style="font-weight: 800;">{{ number_format($auctionData['current_price'] - 5000) }} SAR</span>
                        <span>مزايد #4102</span>
                    </div>
                    <div class="feed-item">
                        <span style="font-weight: 800;">{{ number_format($auctionData['current_price'] - 10000) }} SAR</span>
                        <span>مزايد #1822</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
    $priceHistory = [];
    $startPrice = $auctionData['start_price'];
    $currentPrice = $auctionData['current_price'];
    $increment = $auctionData['min_bid_increment'];
    $bidsCount = $auctionData['bids_count'];

    // Start with starting price
    $priceHistory[] = [
        'time' => now()->subMinutes($bidsCount * 10)->format('Y-m-d H:i:s'),
        'amount' => $startPrice,
        'bidder' => app()->getLocale() === 'ar' ? 'سعر البداية' : 'Starting Price'
    ];

    // Generate intermediate mock bids
    $tempPrice = $startPrice;
    for ($i = 1; $i < $bidsCount; $i++) {
        $tempPrice += $increment * rand(1, 2);
        if ($tempPrice >= $currentPrice) {
            break;
        }
        $priceHistory[] = [
            'time' => now()->subMinutes(($bidsCount - $i) * 10)->format('Y-m-d H:i:s'),
            'amount' => (float)$tempPrice,
            'bidder' => (app()->getLocale() === 'ar' ? 'مزايد #' : 'Bidder #') . rand(1000, 9999)
        ];
    }

    // Add current price as the latest bid if it's higher than the last generated price
    if ($currentPrice > $tempPrice || count($priceHistory) === 1) {
        $priceHistory[] = [
            'time' => now()->subMinutes(2)->format('Y-m-d H:i:s'),
            'amount' => (float)$currentPrice,
            'bidder' => app()->getLocale() === 'ar' ? 'مزايد #' : 'Bidder #' . rand(1000, 9999)
        ];
    }
@endphp
<script>
// Image gallery switcher
function switchImage(thumb, src) {
    document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
    thumb.classList.add('active');
    document.getElementById('mainShowcaseImg').src = src;
}

let priceHistoryData = @json($priceHistory);
let priceChart = null;

function renderPriceHistoryChart() {
    const chartCtx = document.getElementById('priceHistoryChart')?.getContext('2d');
    if (!chartCtx) return;
    
    const labels = priceHistoryData.map(item => {
        const d = new Date(item.time);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    });
    const dataPoints = priceHistoryData.map(item => item.amount);
    const tooltips = priceHistoryData.map(item => `${item.bidder}: ${item.amount.toLocaleString()} SAR`);

    if (priceChart) {
        priceChart.destroy();
    }

    const strokeColor = '#ef4444'; // var(--brand-red)
    const gridColor = 'rgba(255, 255, 255, 0.08)';
    const textColor = 'rgba(255, 255, 255, 0.6)';

    priceChart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ app()->getLocale() === "ar" ? "سعر المزايدة" : "Bid Price" }}',
                data: dataPoints,
                borderColor: strokeColor,
                backgroundColor: 'rgba(239, 68, 68, 0.08)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: strokeColor,
                pointBorderColor: '#0f172a',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return tooltips[context.dataIndex];
                        }
                    },
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: 'Inter, system-ui',
                            size: 10
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: 'Inter, system-ui',
                            size: 10
                        },
                        callback: function(value) {
                            return value.toLocaleString() + ' SAR';
                        }
                    }
                }
            }
        }
    });
}

// Tabs switcher
function switchTab(btn, tabId) {
    document.querySelectorAll('.detail-tab-btn').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
    
    if (tabId === 'priceTab') {
        setTimeout(renderPriceHistoryChart, 50);
    }
}

// Apply quick bid button addition
const minIncrement = {{ $auctionData['min_bid_increment'] }};

function roundToNiceNumber(val) {
    if (val < 5000) {
        return Math.round(val / 100) * 100;
    } else if (val < 20000) {
        return Math.round(val / 500) * 500;
    } else if (val < 100000) {
        return Math.round(val / 1000) * 1000;
    } else {
        return Math.round(val / 5000) * 5000;
    }
}

function updateQuickBidButtons(currentPrice) {
    const val1 = minIncrement;
    let val2 = Math.max(minIncrement * 2, roundToNiceNumber(currentPrice * 0.02));
    let val3 = Math.max(minIncrement * 5, roundToNiceNumber(currentPrice * 0.05));
    
    if (val2 <= val1) val2 = val1 + minIncrement;
    if (val3 <= val2) val3 = val2 + minIncrement;

    const btn1 = document.getElementById('quickBidBtn1');
    const btn2 = document.getElementById('quickBidBtn2');
    const btn3 = document.getElementById('quickBidBtn3');

    if (btn1) {
        btn1.textContent = `+${val1.toLocaleString()}`;
        btn1.setAttribute('data-amount', val1);
    }
    if (btn2) {
        btn2.textContent = `+${val2.toLocaleString()}`;
        btn2.setAttribute('data-amount', val2);
    }
    if (btn3) {
        btn3.textContent = `+${val3.toLocaleString()}`;
        btn3.setAttribute('data-amount', val3);
    }
}

function applyQuickBid(btn) {
    const addAmt = parseInt(btn.getAttribute('data-amount'));
    const currentPrice = parseInt(document.getElementById('currentPriceVal').textContent.replace(/,/g, ''));
    const input = document.getElementById('bidAmountInput');
    input.value = currentPrice + addAmt;
}

// Initial update on load
document.addEventListener('DOMContentLoaded', () => {
    const initialPrice = parseInt(document.getElementById('currentPriceVal').textContent.replace(/,/g, ''));
    if (!isNaN(initialPrice)) {
        updateQuickBidButtons(initialPrice);
    }
});

// Countdown timer script
let timeInSec = 11642; // 03:14:02
const timerEl = document.getElementById('countdownTimer');
const timerInterval = setInterval(() => {
    if (timeInSec <= 0) {
        clearInterval(timerInterval);
        timerEl.textContent = "Closed";
        return;
    }
    timeInSec--;
    const h = Math.floor(timeInSec / 3600);
    const m = Math.floor((timeInSec % 3600) / 60);
    const s = timeInSec % 60;
    
    const formatted = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    timerEl.textContent = formatted;
}, 1000);

let bidMode = 'manual';

function setBidMode(mode) {
    bidMode = mode;
    const btnManual = document.getElementById('btnModeManual');
    const btnAuto = document.getElementById('btnModeAuto');
    const autoGroup = document.getElementById('autoBidLimitGroup');
    const quickBids = document.querySelector('.quick-bids');

    if (mode === 'manual') {
        btnManual.style.background = 'var(--brand-red)';
        btnManual.style.color = 'white';
        btnManual.classList.remove('text-muted');

        btnAuto.style.background = 'transparent';
        btnAuto.style.color = 'var(--text-muted)';
        btnAuto.classList.add('text-muted');

        if (autoGroup) autoGroup.style.display = 'none';
        if (quickBids) quickBids.style.display = 'grid';
    } else {
        btnAuto.style.background = 'var(--brand-red)';
        btnAuto.style.color = 'white';
        btnAuto.classList.remove('text-muted');

        btnManual.style.background = 'transparent';
        btnManual.style.color = 'var(--text-muted)';
        btnManual.classList.add('text-muted');

        if (autoGroup) autoGroup.style.display = 'block';
        if (quickBids) quickBids.style.display = 'none';
    }
}

// Place bid handler via AJAX
function placeBidNow() {
    const btn = document.querySelector('.btn-submit-bid');
    const input = document.getElementById('bidAmountInput');
    const bidAmount = parseInt(input.value);

    const isAutoBid = bidMode === 'auto';
    const maxAutoBid = isAutoBid ? parseInt(document.getElementById('maxAutoBidInput').value) : null;

    if (isAutoBid && (isNaN(maxAutoBid) || maxAutoBid < bidAmount)) {
        toastr.error('{{ app()->getLocale() === "ar" ? "يجب أن يكون الحد الأقصى أعلى من أو يساوي قيمة البداية" : "Maximum limit must be greater than or equal to starting bid amount" }}');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg>';

    fetch(`{{ route('bidder.auctions.bid', $auctionData['id']) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            amount: bidAmount,
            is_auto_bid: isAutoBid,
            max_auto_bid: maxAutoBid
        })
    })
    .then(async res => {
        const data = await res.json();
        btn.disabled = false;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg> {{ app()->getLocale() === "ar" ? "تقديم عرض المزايدة" : "Place Your Bid" }}';

        if (res.ok && data.success) {
            // Update current price
            document.getElementById('currentPriceVal').textContent = data.new_price.toLocaleString();
            
            // Update quick bid buttons
            updateQuickBidButtons(data.new_price);
            
            // Add to feed list
            const feedList = document.getElementById('bidsFeedList');
            const newItem = document.createElement('div');
            newItem.className = 'feed-item new-bid';
            newItem.innerHTML = `<span style="font-weight: 800; color: var(--brand-red-light);">${data.new_price.toLocaleString()} SAR</span><span>{{ app()->getLocale() === 'ar' ? 'أنت (مزايد)' : 'You (Bidder)' }}</span>`;
            feedList.insertBefore(newItem, feedList.firstChild);

            // Update counters
            document.getElementById('bidsCountBadge').textContent = `${data.bids_count} {{ app()->getLocale() === 'ar' ? 'مزايدات' : 'Bids' }}`;

            // Set next bid recommendation
            input.value = data.new_price + minIncrement;
            input.min = data.new_price + minIncrement;

            // Update max auto bid input min
            const maxInput = document.getElementById('maxAutoBidInput');
            if (maxInput) {
                maxInput.min = data.new_price + minIncrement * 2;
            }

            // Simulate auto-extend if time remaining is less than 2 minutes (120 seconds)
            if (typeof timeInSec !== 'undefined' && timeInSec > 0 && timeInSec <= 120) {
                timeInSec = 120;
                toastr.info('{{ app()->getLocale() === "ar" ? "تم تمديد المزاد دقيقتين إضافيتين لمنع القنص!" : "Auction extended by 2 minutes to prevent sniping!" }}');
            }

            toastr.success(data.message);

            // Update chart data
            priceHistoryData.push({
                time: new Date().toISOString().slice(0, 19).replace('T', ' '),
                amount: data.new_price,
                bidder: '{{ app()->getLocale() === "ar" ? "أنت (مزايد)" : "You (Bidder)" }}'
            });
            if (priceChart) {
                renderPriceHistoryChart();
            }

            // Simulate other bidder outbidding after 7 seconds if not in auto-bid mode
            setTimeout(() => {
                const outbidAmount = data.new_price + minIncrement;
                document.getElementById('currentPriceVal').textContent = outbidAmount.toLocaleString();
                
                // Update quick bid buttons dynamically for the new outbid price
                updateQuickBidButtons(outbidAmount);
                
                const outbidItem = document.createElement('div');
                outbidItem.className = 'feed-item new-bid';
                outbidItem.innerHTML = `<span style="font-weight: 800;">${outbidAmount.toLocaleString()} SAR</span><span>مزايد #1905</span>`;
                feedList.insertBefore(outbidItem, feedList.firstChild);
                
                document.getElementById('bidsCountBadge').textContent = `${data.bids_count + 1} {{ app()->getLocale() === 'ar' ? 'مزايدات' : 'Bids' }}`;
                
                input.value = outbidAmount + minIncrement;
                input.min = outbidAmount + minIncrement;

                // Update chart data for outbid
                priceHistoryData.push({
                    time: new Date().toISOString().slice(0, 19).replace('T', ' '),
                    amount: outbidAmount,
                    bidder: '{{ app()->getLocale() === "ar" ? "مزايد #1905" : "Bidder #1905" }}'
                });
                if (priceChart) {
                    renderPriceHistoryChart();
                }
                
                // Simulate auto-extend for outbid
                if (typeof timeInSec !== 'undefined' && timeInSec > 0 && timeInSec <= 120) {
                    timeInSec = 120;
                    toastr.info('{{ app()->getLocale() === "ar" ? "تم تمديد المزاد دقيقتين إضافيتين لمنع القنص!" : "Auction extended by 2 minutes to prevent sniping!" }}');
                }

                toastr.warning('{{ app()->getLocale() === "ar" ? "تنبيه: تم تقديم عرض أعلى منك!" : "Alert: You have been outbid!" }}');
            }, 7000);

        } else {
            toastr.error(data.message || 'Failed to submit bid');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg> {{ app()->getLocale() === "ar" ? "تقديم عرض المزايدة" : "Place Your Bid" }}';
        toastr.error('Connection error.');
    });
}
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
