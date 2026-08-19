@extends('layouts.bidder')

@section('title', __('مسوداتي'))

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
    /* ==========================================================================
       ADAPTIVE LUXURY THEME (DRAFTS)
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
    
    /* Progress Bar for Draft */
    .draft-progress-container {
        background: var(--bg-body);
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .progress-wrapper {
        flex-grow: 1;
    }
    
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }
    
    .progress {
        height: 10px;
        border-radius: 50px;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        overflow: visible;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, var(--primary), #8b5cf6);
        border-radius: 50px;
        box-shadow: 0 0 10px var(--primary-glow);
        position: relative;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        right: 0;
        top: -4px;
        width: 18px;
        height: 18px;
        background: #fff;
        border: 3px solid var(--primary);
        border-radius: 50%;
        box-shadow: 0 0 10px var(--primary-glow);
    }
    
    /* Quick Actions */
    .btn-resume {
        background: linear-gradient(135deg, var(--primary), #6d28d9);
        color: white !important;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s;
        box-shadow: 0 5px 15px var(--primary-glow);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-resume:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.4);
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
    
    .empty-state i.fa-file-pen {
        font-size: 6rem;
        color: var(--text-secondary);
        opacity: 0.3;
        margin-bottom: 2rem;
        display: inline-block;
        transition: transform 0.4s;
    }
    
    .empty-state:hover i.fa-file-pen {
        transform: scale(1.1) rotate(5deg);
        color: var(--primary);
        opacity: 0.5;
    }
    
    .dropdown-menu {
        border-radius: var(--radius-md);
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="garage-hero-card fade-in">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ __('مسوداتي') }}</h1>
                <p class="hero-subtitle">{{ __('أكمل تفاصيل سياراتك التي لم تقم بنشرها بعد لتبدأ في بيعها.') }}</p>
            </div>
        </div>
        <a href="{{ route('bidder.garage.create') }}" class="btn-add">
            <i class="fa-solid fa-plus"></i> {{ __('إضافة سيارة جديدة') }}
        </a>
        <div class="hero-decoration">
            <i class="fa-solid fa-folder-open"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12 fade-in" style="animation-delay: 0.1s;">
            @forelse($vehicles as $vehicle)
                @php
                    // Simulate completion percentage based on what's filled
                    $completedFields = 0;
                    $totalFields = 5;
                    if($vehicle->make_ar) $completedFields++;
                    if($vehicle->year) $completedFields++;
                    if($vehicle->description_ar) $completedFields++;
                    if(!empty($vehicle->images)) $completedFields++;
                    if(!empty($vehicle->damage_points)) $completedFields++;
                    
                    $percentage = ($completedFields / $totalFields) * 100;
                    if($percentage == 0) $percentage = 15; // minimum visual progress
                @endphp
                
                <div class="vehicle-card">
                    <div class="vehicle-card-inner">
                        <img src="{{ $vehicle->primary_image_url ?? asset('images/placeholder.png') }}" class="vehicle-img" alt="{{ $vehicle->title }}">
                        <div class="vehicle-info">
                            <div class="vehicle-title">{{ $vehicle->title ?: __('مسودة سيارة بدون اسم') }}</div>
                            <div class="vehicle-meta">
                                <span><i class="fa-solid fa-barcode text-primary"></i> {{ $vehicle->vin_number ?? __('بدون رقم هيكل') }}</span>
                                <span><i class="fa-solid fa-calendar text-primary"></i> {{ $vehicle->year ?? '---' }}</span>
                                <span class="ms-3"><i class="fa-solid fa-clock text-muted"></i> {{ __('آخر تحديث: :time', ['time' => $vehicle->updated_at->diffForHumans()]) }}</span>
                            </div>
                        </div>
                        <div class="dropdown" style="position: absolute; top: 1.5rem; left: 1.5rem;">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 35px; height: 35px;">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-sm border-0">
                                <li>
                                    <form action="#" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذه المسودة؟') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger py-2"><i class="fa-solid fa-trash me-2"></i> {{ __('حذف المسودة') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="draft-progress-container">
                        <div class="progress-wrapper">
                            <div class="progress-label">
                                <span><i class="fa-solid fa-bars-progress text-primary me-1"></i> {{ __('اكتمال البيانات') }}</span>
                                <span class="text-primary">{{ round($percentage) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div>
                            <!-- In a real app, this should link to the edit route with the draft ID -->
                            <a href="{{ route('bidder.garage.create', ['id' => $vehicle->id]) }}" class="btn-resume">
                                {{ __('استكمال الإضافة') }} <i class="fa-solid fa-arrow-left ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <!-- SVG Illustration -->
                    <i class="fa-solid fa-file-pen text-muted mb-4" style="font-size: 5rem; opacity: 0.5;"></i>
                    <h4 class="text-primary fw-bold">{{ __('لا توجد مسودات حالياً!') }}</h4>
                    <p class="text-secondary mb-4">{{ __('السيارات التي تبدأ بإضافتها ولا تكتمل تظهر هنا كمسودة لتكملها لاحقاً.') }}</p>
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
