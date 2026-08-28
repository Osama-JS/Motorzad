@extends('layouts.bidder')

@section('title', __('مزاداتي'))

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
        left: -15px;
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
        align-items: stretch;
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
        margin-left: 0.5rem;
    }
    
    /* Quick Actions */
    .quick-actions {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        z-index: 10;
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
    
    .empty-state i.fa-gavel {
        font-size: 6rem;
        color: var(--text-secondary);
        opacity: 0.3;
        margin-bottom: 2rem;
        display: inline-block;
        transition: transform 0.4s;
    }
    
    .empty-state:hover i.fa-gavel {
        transform: scale(1.1) translateX(-10px);
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
                <i class="fa-solid fa-gavel"></i>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ __('مزاداتي') }}</h1>
                <p class="hero-subtitle">{{ __('تابع حالة مزاداتك والمزايدات المقدمة عليها بكل سهولة.') }}</p>
            </div>
        </div>
        <div class="hero-decoration">
            <i class="fa-solid fa-gavel"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row fade-in" style="animation-delay: 0.1s;">
        <div class="col-12">
            <!-- Premium Stats Header -->
            <div class="stats-header">
                <div class="stat-card primary">
                    <i class="fa-solid fa-gavel stat-watermark" style="color: var(--primary);"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('إجمالي المزادات') }}</p>
                        <div class="stat-icon" style="background: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.1); color: var(--primary);">
                            <i class="fa-solid fa-gavel"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['total'] }}</h3>
                </div>
                
                <div class="stat-card success">
                    <i class="fa-solid fa-broadcast-tower stat-watermark" style="color: #10b981;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('نشطة الآن') }}</p>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fa-solid fa-broadcast-tower"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['live'] }}</h3>
                </div>

                <div class="stat-card warning">
                    <i class="fa-solid fa-calendar-alt stat-watermark" style="color: #f59e0b;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('مجدولة') }}</p>
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fa-solid fa-calendar-alt"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['scheduled'] }}</h3>
                </div>
                
                <div class="stat-card secondary">
                    <i class="fa-solid fa-flag-checkered stat-watermark" style="color: #64748b;"></i>
                    <div class="stat-header-row">
                        <p class="stat-title">{{ __('منتهية') }}</p>
                        <div class="stat-icon" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                    </div>
                    <h3 class="stat-value" style="position: relative; z-index: 2;">{{ $stats['ended'] }}</h3>
                </div>
            </div>

            @forelse($auctions as $auction)
                <div class="vehicle-card">
                    <div class="vehicle-card-inner">
                        <img src="{{ $auction->vehicle->primary_image_url ?? asset('images/placeholder.png') }}" class="vehicle-img" alt="{{ $auction->title_ar }}">
                        <div class="vehicle-info">
                            <div class="vehicle-title">{{ $auction->title_ar }}</div>
                            <div class="vehicle-meta mb-2" style="flex-wrap: wrap;">
                                <span><i class="fa-solid fa-money-bill-wave text-primary"></i> يبدأ من: {{ number_format($auction->start_price) }} ر.س</span>
                                @if($auction->highestBid)
                                <span class="text-success fw-bold"><i class="fa-solid fa-arrow-up-right-dots"></i> أعلى مزايدة: {{ number_format($auction->highestBid->amount) }} ر.س</span>
                                @else
                                <span class="text-muted"><i class="fa-solid fa-minus"></i> لا توجد مزايدات بعد</span>
                                @endif
                                <span><i class="fa-solid fa-users text-primary"></i> المزايدات: {{ $auction->bids->count() }}</span>
                            </div>
                            <div class="vehicle-meta">
                                <span><i class="fa-solid fa-clock text-primary"></i> البداية: {{ \Carbon\Carbon::parse($auction->start_time)->format('Y-m-d H:i') }}</span>
                                <span><i class="fa-solid fa-clock-rotate-left text-primary"></i> النهاية: {{ \Carbon\Carbon::parse($auction->end_time)->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="quick-actions d-flex gap-2 position-absolute" style="top: 1.5rem; left: 1.5rem; z-index: 10;">
                            <a href="{{ route('bidder.garage.auctions.show', $auction->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="fa-solid fa-eye me-1"></i> {{ __('معاينة') }}
                            </a>
                            <a href="{{ route('bidder.garage.auctions.bids', $auction->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm bg-white">
                                <i class="fa-solid fa-users me-1"></i> {{ __('المزايدات') }}
                            </a>
                            @if($auction->status === 'scheduled')
                            <a href="{{ route('bidder.garage.auctions.edit', $auction->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 shadow-sm bg-white">
                                <i class="fa-solid fa-pen me-1"></i> {{ __('تعديل') }}
                            </a>
                            @endif
                        </div>
                        
                        @if($auction->status === 'live')
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6 shadow-sm">
                                <i class="fa-solid fa-circle-dot fa-fade me-1"></i> نشط
                            </span>
                        </div>
                        @elseif($auction->status === 'scheduled')
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6 shadow-sm">
                                <i class="fa-solid fa-clock me-1"></i> مجدول
                            </span>
                        </div>
                        @elseif($auction->status === 'ended')
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6 shadow-sm">
                                <i class="fa-solid fa-flag-checkered me-1"></i> منتهي
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-gavel text-muted mb-4" style="font-size: 5rem; opacity: 0.5;"></i>
                    <h4 class="text-primary fw-bold">{{ __('ليس لديك أي مزادات بعد!') }}</h4>
                    <p class="text-secondary mb-4">{{ __('ابدأ بنشر مزاد لإحدى سياراتك المعتمدة لتلقي العروض من المشترين.') }}</p>
                    <a href="{{ route('bidder.garage.index') }}" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                        <i class="fa-solid fa-car me-2"></i> {{ __('انتقل لمعرض سياراتي') }}
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $auctions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
