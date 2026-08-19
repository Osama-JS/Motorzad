@extends('layouts.bidder')

@section('title', __('سياراتي المعروضة'))

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
    /* ==========================================================================
       ADAPTIVE LUXURY THEME
       ========================================================================== */
    :root {
        --font-primary: 'Tajawal', 'Outfit', sans-serif;
        --primary-glow: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.25);
        --radius-lg: 20px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    body {
        font-family: var(--font-primary);
    }

    /* Page Header - Hero Card */
    .garage-hero-card {
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--primary-glow) 100%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 3rem;
        margin-bottom: 2.5rem;
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
        width: 75px;
        height: 75px;
        background: var(--bg-body);
        border: 2px solid var(--border-color);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: var(--primary);
        box-shadow: 0 15px 30px var(--primary-glow);
        transform: rotate(8deg);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .garage-hero-card:hover .hero-icon {
        transform: rotate(0deg) scale(1.1);
        border-color: var(--primary);
    }

    .hero-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 5px 0;
        letter-spacing: -0.5px;
    }

    .hero-subtitle {
        color: var(--text-secondary);
        font-size: 1.1rem;
        margin: 0;
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

    .btn-add {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.85rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 15px var(--primary-glow);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .btn-add:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.4);
    }

    /* Stats Header */
    .stats-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 4px;
        background: transparent;
        transition: all 0.4s ease;
    }

    .stat-card.primary::before { background: var(--primary); }
    .stat-card.warning::before { background: #f59e0b; }
    .stat-card.success::before { background: #10b981; }
    .stat-card.secondary::before { background: #64748b; }
    
    .stat-watermark {
        position: absolute;
        left: -15px; /* Left side for RTL */
        bottom: -20px;
        font-size: 7rem;
        opacity: 0.03;
        transform: rotate(-15deg);
        pointer-events: none;
        transition: all 0.5s ease;
        z-index: 0;
    }

    .stat-card:hover .stat-watermark {
        opacity: 0.08;
        transform: rotate(0deg) scale(1.1);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    }
    
    .stat-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .stat-title {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        transition: transform 0.4s;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.15) rotate(5deg);
    }
    
    .stat-value {
        margin: 0;
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -1px;
    }

    /* Premium Vehicle Card */
    .vehicle-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .vehicle-card:hover {
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: var(--primary);
        transform: translateY(-2px);
    }
    
    .vehicle-card-inner {
        display: flex;
        align-items: stretch; /* Make image full height */
        padding: 1.5rem;
        gap: 2rem;
        position: relative;
    }
    
    .vehicle-img {
        width: 220px;
        height: 140px;
        border-radius: var(--radius-md);
        object-fit: cover;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: transform 0.5s ease;
    }
    
    .vehicle-card:hover .vehicle-img {
        transform: scale(1.03);
    }
    
    .vehicle-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .vehicle-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
    }
    
    .vehicle-meta {
        font-size: 0.95rem;
        color: var(--text-secondary);
        display: flex;
        gap: 2rem;
        font-weight: 500;
    }
    
    .vehicle-meta i {
        color: var(--primary);
        margin-left: 0.5rem; /* Arabic RTL */
    }
    
    /* Quick Actions */
    .quick-actions {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem; /* Arabic RTL */
        z-index: 10;
    }
    
    /* Action Buttons inside Card */
    .card-actions-bottom {
        position: absolute;
        bottom: 1.5rem;
        left: 1.5rem; /* Arabic RTL */
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-gradient {
        border: none;
        border-radius: 50px;
        padding: 0.6rem 1.5rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        color: white !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .btn-gradient-success { background: linear-gradient(135deg, #10b981, #059669); }
    .btn-gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    
    /* Pipeline Tracker / Stepper */
    .stepper-wrapper {
        background: var(--bg-body);
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        position: relative;
        border-top: 1px solid var(--border-color);
    }
    
    .stepper-wrapper::before {
        content: '';
        position: absolute;
        top: 45px;
        left: 60px;
        right: 60px;
        height: 3px;
        background: var(--border-color);
        z-index: 1;
    }
    
    .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        z-index: 2;
        gap: 0.75rem;
    }
    
    .stepper-item .step-counter {
        position: relative;
        z-index: 5;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--border-color);
        color: var(--text-secondary);
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 1.1rem;
    }
    
    /* Animations & Gradients for Stepper */
    .stepper-item.completed .step-counter {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: transparent;
        color: white;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }
    
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 var(--primary-glow); }
        70% { box-shadow: 0 0 0 15px rgba(99, 102, 241, 0); }
        100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
    }
    
    .stepper-item.active .step-counter {
        background: linear-gradient(135deg, var(--primary), #8b5cf6);
        border-color: transparent;
        color: white;
        animation: pulse-ring 2s infinite;
        transform: scale(1.15);
    }
    
    .stepper-item.rejected .step-counter {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        border-color: transparent;
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
    }
    
    .stepper-item .step-name {
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-align: center;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stepper-item.completed .step-name {
        color: #10b981;
    }
    
    .stepper-item.active .step-name {
        color: var(--primary);
    }
    
    .stepper-item.rejected .step-name {
        color: #ef4444;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 6rem 2rem;
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 2px dashed var(--border-color);
        transition: all 0.3s;
    }
    
    .empty-state:hover {
        border-color: var(--primary);
        background: var(--bg-body);
    }
    
    .empty-state i.fa-car-side {
        font-size: 6rem;
        color: var(--text-secondary);
        opacity: 0.3;
        margin-bottom: 2rem;
        display: inline-block;
        transition: transform 0.4s;
    }
    
    .empty-state:hover i.fa-car-side {
        transform: scale(1.1) translateX(-10px); /* Move forward in RTL */
        color: var(--primary);
        opacity: 0.5;
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
                <h1 class="hero-title">{{ __('سياراتي المعروضة') }}</h1>
                <p class="hero-subtitle">{{ __('تابع حالة سياراتك، قم بإدارتها، وأطلق المزادات بكل سهولة.') }}</p>
            </div>
        </div>
        <a href="{{ route('bidder.garage.create') }}" class="btn-add">
            <i class="fa-solid fa-plus"></i> {{ __('إضافة سيارة جديدة') }}
        </a>
        <div class="hero-decoration">
            <i class="fa-solid fa-gauge-high"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row fade-in" style="animation-delay: 0.1s;">
        <div class="col-12">
            <!-- Premium Stats Header -->
            <div class="stats-header">
                <div class="stat-card primary">
                    <i class="fa-solid fa-car-side stat-watermark" style="color: var(--primary);"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('إجمالي المعروض') }}</p>
                        <div class="stat-icon" style="background: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.1); color: var(--primary);">
                            <i class="fa-solid fa-car"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['total'] }}</h3>
                </div>
                
                <div class="stat-card warning">
                    <i class="fa-solid fa-clock-rotate-left stat-watermark" style="color: #f59e0b;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('قيد المراجعة') }}</p>
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['pending'] }}</h3>
                </div>
                
                <div class="stat-card success">
                    <i class="fa-solid fa-check-double stat-watermark" style="color: #10b981;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('تم الاعتماد') }}</p>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['approved'] }}</h3>
                </div>
                
                <div class="stat-card secondary">
                    <i class="fa-solid fa-file-lines stat-watermark" style="color: #64748b;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('مسوداتي') }}</p>
                        <div class="stat-icon" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['draft'] }}</h3>
                </div>
            </div>

            @forelse($vehicles as $vehicle)
                <div class="vehicle-card">
                    <div class="vehicle-card-inner">
                        <img src="{{ $vehicle->primary_image_url ?? asset('images/placeholder.png') }}" class="vehicle-img" alt="{{ $vehicle->title }}">
                        <div class="vehicle-info">
                            <div class="vehicle-title">{{ $vehicle->title }}</div>
                            <div class="vehicle-meta">
                                <span><i class="fa-solid fa-barcode text-primary"></i> {{ $vehicle->vin_number ?? __('N/A') }}</span>
                                <span><i class="fa-solid fa-calendar text-primary"></i> {{ $vehicle->year }}</span>
                            </div>
                        </div>
                        
                        <div class="quick-actions dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 35px; height: 35px;">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-eye text-primary me-2"></i> {{ __('معاينة التفاصيل') }}</a></li>
                                @if($vehicle->status === 'pending' || $vehicle->status === 'rejected')
                                <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-pen text-warning me-2"></i> {{ __('تعديل الطلب') }}</a></li>
                                @endif
                                
                                @php
                                    $auction = \App\Models\Auction::where('vehicle_id', $vehicle->id)->first();
                                @endphp
                                
                                @if($vehicle->status === 'approved' && !$auction)
                                <li>
                                    <a class="dropdown-item py-2 text-success fw-bold" href="{{ route('bidder.garage.auctions.create', $vehicle->id) }}">
                                        <i class="fa-solid fa-rocket me-2"></i> {{ __('إطلاق المزاد 🚀') }}
                                    </a>
                                </li>
                                @elseif($auction)
                                <li>
                                    <a class="dropdown-item py-2 text-warning fw-bold" href="{{ route('bidder.garage.auctions.edit', $auction->id) }}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i> {{ __('تعديل المزاد') }}
                                    </a>
                                </li>
                                <li><span class="dropdown-item py-2 text-muted"><i class="fa-solid fa-check-circle me-2"></i> {{ __('تم إطلاق المزاد') }}</span></li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($vehicle->status === 'approved' && !$auction)
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <a href="{{ route('bidder.garage.auctions.create', $vehicle->id) }}" class="btn btn-success rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                <i class="fa-solid fa-rocket me-2"></i> {{ __('إطلاق المزاد') }}
                            </a>
                        </div>
                        @elseif($auction)
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <a href="{{ route('bidder.garage.auctions.edit', $auction->id) }}" class="btn btn-warning rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none; color: white;">
                                <i class="fa-solid fa-pen-to-square me-2"></i> {{ __('تعديل المزاد') }}
                            </a>
                        </div>
                        @endif
                    </div>
                    
                    <div class="stepper-wrapper">
                        <!-- Step 1: Draft -->
                        <div class="stepper-item completed">
                            <div class="step-counter"><i class="fa-solid fa-check"></i></div>
                            <div class="step-name">{{ __('مسودة') }}</div>
                        </div>
                        
                        <!-- Step 2: Pending Admin Review -->
                        <div class="stepper-item {{ $vehicle->status === 'pending' ? 'active' : ($vehicle->status === 'approved' || $vehicle->status === 'rejected' ? 'completed' : '') }}">
                            <div class="step-counter">
                                @if($vehicle->status === 'pending')
                                    2
                                @else
                                    <i class="fa-solid fa-check"></i>
                                @endif
                            </div>
                            <div class="step-name">{{ __('قيد المراجعة') }}</div>
                        </div>

                        <!-- Step 3: Approved / Rejected -->
                        @if($vehicle->status === 'rejected')
                            <div class="stepper-item rejected">
                                <div class="step-counter"><i class="fa-solid fa-xmark"></i></div>
                                <div class="step-name">{{ __('مرفوضة') }}</div>
                            </div>
                        @else
                            <div class="stepper-item {{ $vehicle->status === 'approved' ? 'active' : '' }}">
                                <div class="step-counter">
                                    @if($vehicle->status === 'approved')
                                        3
                                    @else
                                        3
                                    @endif
                                </div>
                                <div class="step-name">{{ __('تم الاعتماد') }}</div>
                            </div>
                        @endif

                        <!-- Step 4: Scheduled (Mock) -->
                        <div class="stepper-item">
                            <div class="step-counter">4</div>
                            <div class="step-name">{{ __('مجدولة') }}</div>
                        </div>

                        <!-- Step 5: Sold (Mock) -->
                        <div class="stepper-item">
                            <div class="step-counter">5</div>
                            <div class="step-name">{{ __('تم البيع') }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <!-- SVG Illustration -->
                    <i class="fa-solid fa-car-side text-muted mb-4" style="font-size: 5rem; opacity: 0.5;"></i>
                    <h4 class="text-primary fw-bold">{{ __('معرضك فارغ حالياً!') }}</h4>
                    <p class="text-secondary mb-4">{{ __('ابدأ الآن بإضافة أول سيارة لك لعرضها في المزاد وابدأ بتحقيق الأرباح.') }}</p>
                    <a href="{{ route('bidder.garage.create') }}" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                        <i class="fa-solid fa-plus me-2"></i> {{ __('إضافة سيارة جديدة') }}
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
