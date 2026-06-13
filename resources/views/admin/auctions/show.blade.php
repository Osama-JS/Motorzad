@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل المزاد - ' . $auction->title : 'Auction Details - ' . $auction->title)

@section('css')
<style>
    /* Premium UI & UX Variables & Theme styles */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
        --shadow-glow-blue: 0 0 20px rgba(59, 130, 246, 0.2);
        --shadow-glow-purple: 0 0 20px rgba(139, 92, 246, 0.25);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.85);
        --glass-border: rgba(51, 65, 85, 0.8);
    }

    .premium-panel {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: var(--shadow-premium);
        margin-bottom: 30px;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }
    .premium-panel:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
    }

    .panel-header-premium {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 28px;
        border-bottom: 1px solid var(--glass-border);
        background: rgba(248, 250, 252, 0.4);
    }
    [data-theme="dark"] .panel-header-premium {
        background: rgba(15, 23, 42, 0.3);
    }

    .panel-header-premium h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-color);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .panel-body-premium {
        padding: 28px;
    }

    /* Gradient Stats upgrade */
    .stat-card-gradient {
        position: relative;
        border-radius: 20px;
        padding: 24px;
        color: #ffffff;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
    }
    .stat-card-gradient:hover {
        transform: scale(1.02);
    }
    .stat-card-gradient::after {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50px;
        right: -50px;
    }
    .scg-purple { background: linear-gradient(135deg, #6366f1, #a855f7); }
    .scg-emerald { background: linear-gradient(135deg, #059669, #10b981); }
    .scg-amber { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .scg-blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }

    .scg-value {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .scg-label {
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    .scg-icon {
        position: absolute;
        bottom: 20px;
        right: 20px;
        font-size: 2.2rem;
        opacity: 0.25;
    }
    html[dir="rtl"] .scg-icon {
        right: auto;
        left: 20px;
    }

    /* Premium Gallery */
    .gallery-container {
        position: relative;
    }
    .main-image-viewport {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 16/10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        background: #0f172a;
    }
    .main-image-viewport img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }
    .gallery-overlay-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }
    html[dir="rtl"] .gallery-overlay-badge {
        right: auto;
        left: 20px;
    }
    .gallery-thumbnails-grid {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .gallery-thumb-item {
        width: 80px;
        height: 55px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        opacity: 0.7;
        flex-shrink: 0;
    }
    .gallery-thumb-item.active {
        border-color: #6366f1;
        opacity: 1;
        transform: scale(1.05);
    }
    .gallery-thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Tabs Design */
    .premium-tabs {
        border-bottom: 2px solid var(--glass-border);
        margin-bottom: 24px;
        gap: 8px;
    }
    .premium-tab-link {
        border: none !important;
        background: none !important;
        color: var(--text-muted) !important;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 12px 20px !important;
        border-bottom: 3px solid transparent !important;
        transition: all 0.2s ease;
        border-radius: 0 !important;
    }
    .premium-tab-link:hover {
        color: var(--text-color) !important;
    }
    .premium-tab-link.active {
        color: #6366f1 !important;
        border-bottom-color: #6366f1 !important;
    }

    /* Specs Grid view */
    .specs-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media(max-width: 576px) {
        .specs-grid { grid-template-columns: 1fr; }
    }
    .spec-item {
        background: rgba(248, 250, 252, 0.5);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    [data-theme="dark"] .spec-item {
        background: rgba(15, 23, 42, 0.2);
    }
    .spec-item-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .spec-item-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-color);
    }

    /* Timeline Bids Feed */
    .timeline-bids-feed {
        position: relative;
        padding-left: 20px;
    }
    html[dir="rtl"] .timeline-bids-feed {
        padding-left: 0;
        padding-right: 20px;
    }
    .timeline-bids-feed::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 35px;
        width: 2px;
        background: var(--glass-border);
    }
    html[dir="rtl"] .timeline-bids-feed::before {
        left: auto;
        right: 35px;
    }

    .timeline-bid-item {
        position: relative;
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }
    .timeline-bid-item:last-child {
        margin-bottom: 0;
    }

    .tbi-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #6366f1;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        z-index: 1;
        box-shadow: 0 0 0 4px var(--glass-bg);
        flex-shrink: 0;
    }
    .tbi-content {
        flex-grow: 1;
        background: rgba(248, 250, 252, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: all 0.2s ease;
    }
    [data-theme="dark"] .tbi-content {
        background: rgba(15, 23, 42, 0.2);
    }
    .tbi-content:hover {
        background: rgba(248, 250, 252, 1);
        border-color: rgba(99, 102, 241, 0.3);
    }
    [data-theme="dark"] .tbi-content:hover {
        background: rgba(15, 23, 42, 0.4);
    }

    /* Gold styling for leading bid */
    .timeline-bid-item.leading-bid .tbi-avatar {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        box-shadow: 0 0 0 4px var(--glass-bg), 0 0 15px rgba(245, 158, 11, 0.4);
    }
    .timeline-bid-item.leading-bid .tbi-content {
        border-color: rgba(245, 158, 11, 0.4);
        background: rgba(254, 243, 199, 0.3);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.05);
    }
    .timeline-bid-item.leading-bid .tbi-content:hover {
        background: rgba(254, 243, 199, 0.4);
    }

    .tbi-price {
        font-size: 1.15rem;
        font-weight: 800;
        color: #6366f1;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .timeline-bid-item.leading-bid .tbi-price {
        color: #d97706;
    }
    .tbi-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 4px;
    }
    .tbi-badge-lead { background: #fef3c7; color: #b45309; }
    .tbi-badge-auto { background: #faf5ff; color: #a855f7; }

    /* General Pills */
    .pill-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Live Countdown Clock style */
    .countdown-widget {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    .countdown-segment {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 4px 8px;
        min-width: 45px;
        text-align: center;
    }
    .countdown-segment-val {
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1;
    }
    .countdown-segment-lbl {
        font-size: 0.6rem;
        font-weight: 600;
        opacity: 0.8;
        text-transform: uppercase;
        margin-top: 2px;
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-extrabold">{{ app()->getLocale() === 'ar' ? 'تفاصيل المزاد' : 'Auction Details' }}</h1>
        <div class="breadcrumb mb-0">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.auctions.index') }}">{{ __('Auctions') }}</a> / 
            <span class="text-muted">{{ $auction->title }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.auctions.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 rounded-pill">
            <i class="fa-solid fa-arrow-left"></i> <span>{{ app()->getLocale() === 'ar' ? 'الرجوع للقائمة' : 'Back to List' }}</span>
        </a>
        <a href="{{ route('admin.auctions.edit', $auction->id) }}" class="btn btn-primary d-flex align-items-center gap-2 px-4 rounded-pill">
            <i class="fa-solid fa-pen-to-square"></i> <span>{{ __('Edit') }}</span>
        </a>
    </div>
</div>

{{-- Top Row: Upgraded Stats Grid --}}
<div class="row mb-4">
    <div class="col-12 col-md-3 mb-3 mb-md-0">
        <div class="stat-card-gradient scg-purple">
            <div class="scg-value">{{ number_format($auction->winning_bid_amount ?? $auction->start_price, 2) }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'السعر الحالي / النهائي' : 'Current / Final Price' }}</div>
            <i class="fa-solid fa-tags scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-md-0">
        <div class="stat-card-gradient scg-blue">
            <div class="scg-value">{{ number_format($auction->start_price, 2) }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'السعر الابتدائي' : 'Starting Price' }}</div>
            <i class="fa-solid fa-flag-checkered scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-md-0">
        <div class="stat-card-gradient scg-emerald">
            <div class="scg-value">{{ $auction->bids->count() }}</div>
            <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'إجمالي المزايدات' : 'Total Bids' }}</div>
            <i class="fa-solid fa-gavel scg-icon"></i>
        </div>
    </div>
    <div class="col-12 col-md-3">
        @if($auction->status === 'live' && $auction->end_time && $auction->end_time->isFuture())
            <div class="stat-card-gradient scg-amber" id="countdownCard">
                <div class="countdown-segment-val" style="display:none;" id="rawEndTime">{{ $auction->end_time->toIso8601String() }}</div>
                <div class="countdown-widget">
                    <div class="countdown-segment">
                        <div class="countdown-segment-val" id="cd-d">00</div>
                        <div class="countdown-segment-lbl">{{ app()->getLocale() === 'ar' ? 'يوم' : 'D' }}</div>
                    </div>
                    <div class="countdown-segment">
                        <div class="countdown-segment-val" id="cd-h">00</div>
                        <div class="countdown-segment-lbl">{{ app()->getLocale() === 'ar' ? 'ساعة' : 'H' }}</div>
                    </div>
                    <div class="countdown-segment">
                        <div class="countdown-segment-val" id="cd-m">00</div>
                        <div class="countdown-segment-lbl">{{ app()->getLocale() === 'ar' ? 'دقيقة' : 'M' }}</div>
                    </div>
                    <div class="countdown-segment">
                        <div class="countdown-segment-val" id="cd-s">00</div>
                        <div class="countdown-segment-lbl">{{ app()->getLocale() === 'ar' ? 'ثانية' : 'S' }}</div>
                    </div>
                </div>
                <div class="scg-label text-center mt-2">{{ app()->getLocale() === 'ar' ? 'الوقت المتبقي' : 'Time Remaining' }}</div>
                <i class="fa-solid fa-clock scg-icon"></i>
            </div>
        @else
            <div class="stat-card-gradient scg-amber">
                <div class="scg-value">{{ $auction->deposits->count() }}</div>
                <div class="scg-label">{{ app()->getLocale() === 'ar' ? 'الضمانات المدفوعة' : 'Deposits Paid' }}</div>
                <i class="fa-solid fa-wallet scg-icon"></i>
            </div>
        @endif
    </div>
</div>

<div class="row">
    {{-- Left Column: Media & Specifications Directory --}}
    <div class="col-lg-7">
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3>
                    <i class="fa-solid fa-folder-open text-primary"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'بيانات ومواصفات المزاد' : 'Auction specs directory' }}</span>
                </h3>
                @php
                    $statusClass = match($auction->status) {
                        'live' => 'status-live',
                        'scheduled' => 'status-scheduled',
                        'completed', 'sold', 'ended' => 'status-completed',
                        'cancelled' => 'status-cancelled',
                        default => 'status-draft',
                    };
                    $statusLabel = match($auction->status) {
                        'live' => __('Live'),
                        'scheduled' => __('Scheduled'),
                        'completed', 'sold', 'ended' => __('Completed'),
                        'cancelled' => __('Cancelled'),
                        default => __($auction->status),
                    };
                @endphp
                <span class="status-indicator {{ $statusClass }}">
                    <i class="fa-solid fa-circle text-xs"></i>
                    {{ $statusLabel }}
                </span>
            </div>
            <div class="panel-body-premium">
                {{-- Interactive Gallery View --}}
                @php
                    $auctionImg = $auction->image ? asset('storage/' . $auction->image) : null;
                    $images = collect();
                    if ($auctionImg) {
                        $images->push($auctionImg);
                    }
                    if ($auction->vehicle) {
                        foreach ($auction->vehicle->images as $vimg) {
                            $images->push(asset('storage/' . $vimg->image_path));
                        }
                    }
                @endphp

                @if($images->isNotEmpty())
                    <div class="gallery-container mb-4">
                        <div class="main-image-viewport">
                            <img src="{{ $images->first() }}" id="mainGalleryViewport" alt="Vehicle image">
                            <div class="gallery-overlay-badge">
                                <span class="badge bg-dark text-white bg-opacity-75 px-3 py-2 fs-6 rounded">
                                    {{ $auction->vehicle ? $auction->vehicle->year : '' }} {{ $auction->vehicle ? $auction->vehicle->make : '' }}
                                </span>
                            </div>
                        </div>
                        @if($images->count() > 1)
                            <div class="gallery-thumbnails-grid">
                                @foreach($images as $idx => $imgUrl)
                                    <div class="gallery-thumb-item {{ $idx === 0 ? 'active' : '' }}" onclick="swapGalleryImage(this, '{{ $imgUrl }}')">
                                        <img src="{{ $imgUrl }}" alt="Thumb">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div style="background:rgba(226, 232, 240, 0.4); height:260px; border-radius:16px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#94a3b8;" class="mb-4">
                        <i class="fa-solid fa-images fa-4x mb-3"></i>
                        <span>{{ __('No Image Available') }}</span>
                    </div>
                @endif

                {{-- Tabbed Spec Layout --}}
                <ul class="nav nav-tabs premium-tabs" id="specTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link premium-tab-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane" type="button" role="tab">
                            🚗 {{ app()->getLocale() === 'ar' ? 'المواصفات الأساسية' : 'Basic Specs' }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link premium-tab-link" id="engine-tab" data-bs-toggle="tab" data-bs-target="#engine-tab-pane" type="button" role="tab">
                            ⚙️ {{ app()->getLocale() === 'ar' ? 'المحرك والأداء' : 'Engine & Specs' }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link premium-tab-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-tab-pane" type="button" role="tab">
                            💸 {{ app()->getLocale() === 'ar' ? 'إعدادات المزاد المالية' : 'Auction Terms' }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link premium-tab-link" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarks-tab-pane" type="button" role="tab">
                            📝 {{ app()->getLocale() === 'ar' ? 'الوصف والأوصاف' : 'Descriptions' }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="specTabsContent">
                    {{-- Tab 1: Basic specs --}}
                    <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel">
                        <div class="specs-grid">
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-car"></i> {{ app()->getLocale() === 'ar' ? 'المركبة' : 'Vehicle' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle ? $auction->vehicle->title : 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-industry"></i> {{ app()->getLocale() === 'ar' ? 'الشركة المصنعة' : 'Make' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->make ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-cube"></i> {{ app()->getLocale() === 'ar' ? 'الموديل' : 'Model' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->model ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-calendar"></i> {{ app()->getLocale() === 'ar' ? 'سنة الصنع' : 'Year' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->year ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-gauge-high"></i> {{ app()->getLocale() === 'ar' ? 'عداد الكيلومترات' : 'Odometer' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle && $auction->vehicle->mileage ? number_format($auction->vehicle->mileage) . ' km' : 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-palette"></i> {{ app()->getLocale() === 'ar' ? 'اللون' : 'Color' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->color ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-barcode"></i> {{ app()->getLocale() === 'ar' ? 'رقم الهيكل VIN' : 'VIN' }}</span>
                                <span class="spec-item-value"><code>{{ $auction->vehicle->vin_number ?? 'N/A' }}</code></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-id-card"></i> {{ app()->getLocale() === 'ar' ? 'رقم اللوحة' : 'Plate Number' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->plate_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 2: Engine Specs --}}
                    <div class="tab-pane fade" id="engine-tab-pane" role="tabpanel">
                        <div class="specs-grid">
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-gear"></i> {{ app()->getLocale() === 'ar' ? 'ناقل الحركة' : 'Transmission' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle && $auction->vehicle->transmission ? __($auction->vehicle->transmission) : 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-gas-pump"></i> {{ app()->getLocale() === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle && $auction->vehicle->fuel_type ? __($auction->vehicle->fuel_type) : 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-bolt"></i> {{ app()->getLocale() === 'ar' ? 'سعة المحرك' : 'Engine Capacity' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->engine_capacity ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-circle-nodes"></i> {{ app()->getLocale() === 'ar' ? 'الأسطوانات' : 'Cylinders' }}</span>
                                <span class="spec-item-value">{{ $auction->vehicle->cylinders ?? 'N/A' }}</span>
                            </div>
                            <div class="spec-item" style="grid-column: span 2;">
                                <span class="spec-item-label"><i class="fa-solid fa-shield-halved"></i> {{ app()->getLocale() === 'ar' ? 'حالة المركبة والتقييم' : 'Vehicle Condition' }}</span>
                                <span class="spec-item-value">
                                    @if($auction->vehicle)
                                        <span class="badge bg-dark text-white px-3 py-2 border rounded-pill">{{ __($auction->vehicle->condition) }}</span>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 3: Auction settings --}}
                    <div class="tab-pane fade" id="settings-tab-pane" role="tabpanel">
                        <div class="specs-grid">
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-money-bill-wave"></i> {{ app()->getLocale() === 'ar' ? 'السعر الاحتياطي' : 'Reserve Price' }}</span>
                                <span class="spec-item-value">{{ $auction->reserve_price ? number_format($auction->reserve_price, 2) : __('Not Set') }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-bolt-lightning"></i> {{ app()->getLocale() === 'ar' ? 'الشراء الفوري' : 'Buy Now Price' }}</span>
                                <span class="spec-item-value">{{ $auction->buy_now_price ? number_format($auction->buy_now_price, 2) : __('Not Set') }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-arrow-up-wide-short"></i> {{ app()->getLocale() === 'ar' ? 'الحد الأدنى لزيادة المزايدة' : 'Min Increment' }}</span>
                                <span class="spec-item-value">{{ number_format($auction->min_bid_increment, 2) }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-wallet"></i> {{ app()->getLocale() === 'ar' ? 'الضمان المالي للمشاركة' : 'Required Deposit' }}</span>
                                <span class="spec-item-value">
                                    @if($auction->deposit_required)
                                        <span class="text-danger font-weight-bold">{{ number_format($auction->deposit_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">{{ __('Not Required') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-hourglass-start"></i> {{ app()->getLocale() === 'ar' ? 'وقت بدء المزاد' : 'Start Time' }}</span>
                                <span class="spec-item-value">{{ $auction->start_time ? $auction->start_time->format('Y-m-d H:i') : '-' }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-item-label"><i class="fa-solid fa-hourglass-end"></i> {{ app()->getLocale() === 'ar' ? 'وقت انتهاء المزاد' : 'End Time' }}</span>
                                <span class="spec-item-value">{{ $auction->end_time ? $auction->end_time->format('Y-m-d H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 4: Descriptions --}}
                    <div class="tab-pane fade" id="remarks-tab-pane" role="tabpanel">
                        <div class="d-flex flex-column gap-4">
                            <div>
                                <h6 class="font-weight-bold text-dark border-bottom pb-2">{{ app()->getLocale() === 'ar' ? 'الوصف باللغة العربية' : 'Arabic Description' }}</h6>
                                <div class="bg-light p-3 rounded" style="white-space: pre-wrap; font-size: 0.95rem;">{!! $auction->description_ar ?? 'لا يوجد وصف' !!}</div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-dark border-bottom pb-2">{{ app()->getLocale() === 'ar' ? 'الوصف باللغة الإنجليزية' : 'English Description' }}</h6>
                                <div class="bg-light p-3 rounded" style="white-space: pre-wrap; font-size: 0.95rem;">{!! $auction->description_en ?? 'No description' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Bids & Deposits feed --}}
    <div class="col-lg-5">
        {{-- Emergency Controls Panel --}}
        @if(!in_array($auction->status, ['sold', 'cancelled']))
            <div class="premium-panel border border-danger">
                <div class="panel-header-premium bg-danger bg-opacity-10 text-danger" style="border-bottom: 1px solid var(--glass-border);">
                    <h3 style="color:#dc2626; font-weight:800; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-circle-exclamation text-danger"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إجراءات الطوارئ والتحكم المباشر' : 'Emergency & Live Controls' }}</span>
                    </h3>
                </div>
                <div class="panel-body-premium p-4">
                    {{-- Pause / Resume --}}
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-2" style="font-size:0.9rem;">{{ app()->getLocale() === 'ar' ? 'حالة المزاد الحالية' : 'Current Auction State' }}</h6>
                        <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3" style="background:#f8fafc !important; border: 1px solid var(--glass-border);">
                            <div>
                                @if($auction->is_paused)
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6" style="background:#fef3c7 !important; color:#d97706 !important;">
                                        <i class="fa-solid fa-circle-pause"></i> {{ app()->getLocale() === 'ar' ? 'موقوف مؤقتاً' : 'Temporarily Paused' }}
                                    </span>
                                @else
                                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6" style="background:#dcfce7 !important; color:#15803d !important;">
                                        <i class="fa-solid fa-circle-play"></i> {{ app()->getLocale() === 'ar' ? 'نشط / جاهز' : 'Active / Ready' }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                @if($auction->is_paused)
                                    <button onclick="togglePause('resume')" class="btn btn-success d-flex align-items-center gap-1.5 px-3 py-2 rounded-pill font-weight-bold" style="font-size:0.8rem; background:#10b981; border:none;">
                                        <i class="fa-solid fa-play"></i> {{ app()->getLocale() === 'ar' ? 'استئناف المزاد' : 'Resume Auction' }}
                                    </button>
                                @else
                                    <button onclick="togglePause('pause')" class="btn btn-warning text-dark d-flex align-items-center gap-1.5 px-3 py-2 rounded-pill font-weight-bold" style="font-size:0.8rem; background:#f59e0b; border:none;">
                                        <i class="fa-solid fa-pause"></i> {{ app()->getLocale() === 'ar' ? 'إيقاف مؤقت' : 'Pause Auction' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Manual Extension --}}
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-2" style="font-size:0.9rem;">{{ app()->getLocale() === 'ar' ? 'تمديد المزاد يدوياً' : 'Manual Time Extension' }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button onclick="extendAuction(5)" class="btn btn-outline-primary rounded-pill px-3 py-1.5 font-weight-bold" style="font-size: 0.8rem; border-color:#6366f1; color:#6366f1;">+5 {{ app()->getLocale() === 'ar' ? 'دقائق' : 'Mins' }}</button>
                            <button onclick="extendAuction(10)" class="btn btn-outline-primary rounded-pill px-3 py-1.5 font-weight-bold" style="font-size: 0.8rem; border-color:#6366f1; color:#6366f1;">+10 {{ app()->getLocale() === 'ar' ? 'دقائق' : 'Mins' }}</button>
                            <button onclick="extendAuction(30)" class="btn btn-outline-primary rounded-pill px-3 py-1.5 font-weight-bold" style="font-size: 0.8rem; border-color:#6366f1; color:#6366f1;">+30 {{ app()->getLocale() === 'ar' ? 'دقيقة' : 'Mins' }}</button>
                            <button onclick="extendAuction(60)" class="btn btn-outline-primary rounded-pill px-3 py-1.5 font-weight-bold" style="font-size: 0.8rem; border-color:#6366f1; color:#6366f1;">+1 {{ app()->getLocale() === 'ar' ? 'ساعة' : 'Hour' }}</button>
                        </div>
                    </div>

                    {{-- Force End / Closure --}}
                    <div>
                        <h6 class="font-weight-bold text-dark mb-2" style="font-size:0.9rem;">{{ app()->getLocale() === 'ar' ? 'الإنهاء الفوري للمزاد' : 'Immediate Closure' }}</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <button onclick="forceEnd('complete')" class="btn btn-primary w-100 py-2 rounded-3 font-weight-bold d-flex align-items-center justify-content-center gap-1.5" style="font-size:0.8rem; background:#6366f1; border:none;">
                                    <i class="fa-solid fa-check-double"></i> {{ app()->getLocale() === 'ar' ? 'إرساء وإتمام البيع' : 'Complete & Sell' }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button onclick="forceEnd('cancel')" class="btn btn-danger w-100 py-2 rounded-3 font-weight-bold d-flex align-items-center justify-content-center gap-1.5" style="font-size:0.8rem; background:#ef4444; border:none;">
                                    <i class="fa-solid fa-ban"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء ورد الضمانات' : 'Cancel & Refund' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Winner Highlight panel --}}
        @if($auction->winner)
            @php
                $isClosed = in_array($auction->status, ['completed', 'sold', 'ended']);
            @endphp
            <div class="premium-panel border border-warning" style="background: linear-gradient(135deg, rgba(254,243,199,0.3), rgba(253,230,138,0.2));">
                <div class="panel-body-premium p-4 d-flex align-items-center gap-3">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                        <i class="fa-solid fa-trophy fa-xl"></i>
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark font-weight-bold text-uppercase px-3 py-1 rounded-pill mb-2">
                            {{ $isClosed ? (app()->getLocale() === 'ar' ? 'الفائز النهائي بالمزاد' : 'Final Winner') : (app()->getLocale() === 'ar' ? 'المتزايد المتصدر حالياً' : 'Current Leader') }}
                        </span>
                        <h4 class="mb-0 text-dark font-weight-bold" style="font-size: 1.15rem;">{{ $auction->winner->name }}</h4>
                        <div class="text-secondary mt-1" style="font-size: 0.8rem;">
                            <i class="fa-regular fa-envelope"></i> {{ $auction->winner->email }}
                            @if($auction->winner->phone)
                                <br><i class="fa-solid fa-phone"></i> {{ $auction->winner->phone }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Bids Timeline Feed --}}
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3>
                    <i class="fa-solid fa-fire text-danger"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سجل المزايدات التفاعلي' : 'Interactive bids history' }}</span>
                </h3>
            </div>
            <div class="panel-body-premium">
                @if($auction->bids->isNotEmpty())
                    <div class="timeline-bids-feed">
                        @foreach($auction->bids as $idx => $bid)
                            @php
                                $isFirst = ($idx === 0);
                                $initials = mb_substr($bid->user->name, 0, 1);
                                $colors = ['#3b82f6', '#10b981', '#a855f7', '#f59e0b', '#ec4899', '#06b6d4'];
                                $avatarColor = $colors[$bid->user->id % count($colors)];
                            @endphp
                            <div class="timeline-bid-item {{ $isFirst ? 'leading-bid' : '' }}">
                                <div class="tbi-avatar" style="{{ !$isFirst ? 'background:' . $avatarColor : '' }}">
                                    @if($isFirst)
                                        <i class="fa-solid fa-award"></i>
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div class="tbi-content">
                                    <div>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ $bid->user->name }}</h6>
                                        <small class="text-secondary" style="font-size: 0.75rem;">
                                            {{ $bid->created_at->diffForHumans() }}
                                            <span class="mx-1">•</span>
                                            {{ $bid->created_at->format('H:i:s') }}
                                        </small>
                                        @if($bid->ip_address)
                                            <div class="text-muted mt-1" style="font-size: 0.7rem;">IP: <code>{{ $bid->ip_address }}</code></div>
                                        @endif
                                    </div>
                                    <div class="tbi-price">
                                        <span>{{ number_format($bid->amount, 2) }}</span>
                                        @if($isFirst)
                                            <span class="tbi-badge tbi-badge-lead"><i class="fa-solid fa-trophy"></i> {{ app()->getLocale() === 'ar' ? 'المتصدر' : 'Leading' }}</span>
                                        @endif
                                        @if($bid->is_auto_bid)
                                            <span class="tbi-badge tbi-badge-auto" title="Limit: {{ $bid->max_auto_bid }}"><i class="fa-solid fa-robot"></i> {{ app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-gavel fa-2x text-secondary opacity-50"></i>
                        </div>
                        <p class="mb-0 font-weight-bold" style="font-size: 0.95rem;">{{ app()->getLocale() === 'ar' ? 'لا توجد مزايدات على هذا المزاد بعد.' : 'No bids placed yet.' }}</p>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'سيظهر سجل المزايدات التفاعلي هنا فور تقديم المزايدات.' : 'Interactive bidding feed will appear here.' }}</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Deposits Paid Card --}}
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3>
                    <i class="fa-solid fa-wallet text-success"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الضمانات المالية المسجلة' : 'Registered deposit guarantees' }}</span>
                </h3>
            </div>
            <div class="panel-body-premium p-0">
                @if($auction->deposits->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 text-sm">
                            <thead>
                                <tr class="bg-light">
                                    <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}</th>
                                    <th class="py-3 text-end">{{ app()->getLocale() === 'ar' ? 'مبلغ الضمان' : 'Deposit' }}</th>
                                    <th class="py-3 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auction->deposits as $deposit)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <strong>{{ $deposit->user->name }}</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $deposit->user->email }}</div>
                                        </td>
                                        <td class="py-3 text-end font-weight-bold text-success">
                                            {{ number_format($deposit->amount, 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($deposit->status === 'held')
                                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-lock"></i> {{ app()->getLocale() === 'ar' ? 'محتجز' : 'Held' }}</span>
                                            @elseif($deposit->status === 'released')
                                                <span class="badge bg-success px-3 py-1 rounded-pill"><i class="fa-solid fa-lock-open"></i> {{ app()->getLocale() === 'ar' ? 'مسترجع' : 'Released' }}</span>
                                            @elseif($deposit->status === 'forfeited')
                                                <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-ban"></i> {{ app()->getLocale() === 'ar' ? 'مصادَر' : 'Forfeited' }}</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $deposit->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-receipt fa-2x text-secondary opacity-50"></i>
                        </div>
                        <p class="mb-0 font-weight-bold" style="font-size: 0.95rem;">{{ app()->getLocale() === 'ar' ? 'لا يوجد أي ضمانات مالية مسددة.' : 'No deposits paid yet.' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Swap main gallery image smoothly
    function swapGalleryImage(thumbElement, imageUrl) {
        const viewport = document.getElementById('mainGalleryViewport');
        if (!viewport) return;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.gallery-thumb-item').forEach(el => el.classList.remove('active'));
        
        // Add active class to clicked thumbnail
        thumbElement.classList.add('active');
        
        // Fade out, change source, fade in
        viewport.style.opacity = '0.3';
        setTimeout(() => {
            viewport.src = imageUrl;
            viewport.style.opacity = '1';
        }, 150);
    }

    // Dynamic Live countdown timer
    document.addEventListener('DOMContentLoaded', function() {
        const rawEndTimeEl = document.getElementById('rawEndTime');
        if (!rawEndTimeEl) return;

        const endTimeStr = rawEndTimeEl.textContent.trim();
        const endTime = new Date(endTimeStr).getTime();

        const cdD = document.getElementById('cd-d');
        const cdH = document.getElementById('cd-h');
        const cdM = document.getElementById('cd-m');
        const cdS = document.getElementById('cd-s');

        function updateCountdown() {
            const now = new Date().getTime();
            const diff = endTime - now;

            if (diff <= 0) {
                // Timer expired
                if (cdD) cdD.textContent = '00';
                if (cdH) cdH.textContent = '00';
                if (cdM) cdM.textContent = '00';
                if (cdS) cdS.textContent = '00';
                
                const countdownCard = document.getElementById('countdownCard');
                if (countdownCard) {
                    countdownCard.style.background = 'linear-gradient(135deg, #ef4444, #b91c1c)';
                }
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            if (cdD) cdD.textContent = String(days).padStart(2, '0');
            if (cdH) cdH.textContent = String(hours).padStart(2, '0');
            if (cdM) cdM.textContent = String(minutes).padStart(2, '0');
            if (cdS) cdS.textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });

    // Emergency Live Action AJAX calls
    function togglePause(action) {
        let url = action === 'pause' ? "{{ route('admin.auctions.pause', $auction->id) }}" : "{{ route('admin.auctions.resume', $auction->id) }}";
        let title = action === 'pause' ? "{{ __('Pause Auction?') }}" : "{{ __('Resume Auction?') }}";
        let text = action === 'pause' ? "{{ __('Bidders will not be able to place new bids.') }}" : "{{ __('Bidders will be allowed to place bids again.') }}";

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Confirm') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("{{ __('Something went wrong!') }}");
                    }
                });
            }
        });
    }

    function extendAuction(minutes) {
        Swal.fire({
            title: "{{ __('Extend Auction Time?') }}",
            text: "{{ __('Do you want to extend this auction by ') }}" + minutes + " {{ __('minutes?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Confirm') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.auctions.extend', $auction->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        minutes: minutes
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("{{ __('Something went wrong!') }}");
                    }
                });
            }
        });
    }

    function forceEnd(action) {
        let title = action === 'complete' ? "{{ __('Force End and Sell?') }}" : "{{ __('Force Cancel Auction?') }}";
        let text = action === 'complete' 
            ? "{{ __('This will end the auction immediately and award it to the current highest bidder (if reserve met).') }}" 
            : "{{ __('This will cancel the auction and refund all deposits immediately. This action cannot be undone!') }}";
        let confirmBtnColor = action === 'complete' ? '#10b981' : '#ef4444';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Confirm') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.auctions.force-end', $auction->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("{{ __('Something went wrong!') }}");
                    }
                });
            }
        });
    }
</script>
@endsection
