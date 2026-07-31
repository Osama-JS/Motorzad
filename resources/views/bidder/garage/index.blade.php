@extends('layouts.bidder')

@section('title', __('سياراتي المعروضة'))

@section('css')
<style>
    .garage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .garage-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .btn-add {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-add:hover {
        background: var(--primary-hover);
        color: white;
    }
    /* Stats Header */
    .stats-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-info h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .stat-info p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 600;
    }

    /* Premium Vehicle Card */
    .vehicle-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }
    .vehicle-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        border-color: var(--primary);
    }
    .vehicle-card-inner {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        gap: 1.5rem;
        position: relative;
    }
    .vehicle-img {
        width: 160px;
        height: 100px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .vehicle-info {
        flex-grow: 1;
    }
    .vehicle-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    .vehicle-meta {
        font-size: 0.9rem;
        color: var(--text-secondary);
        display: flex;
        gap: 1.5rem;
    }
    
    /* Quick Actions */
    .quick-actions {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem; /* Arabic RTL */
    }
    
    /* Pipeline Tracker / Stepper */
    .stepper-wrapper {
        background: rgba(0,0,0,0.02);
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        position: relative;
        border-top: 1px solid var(--border-color);
    }
    .stepper-wrapper::before {
        content: '';
        position: absolute;
        top: 36px;
        left: 50px;
        right: 50px;
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
    }
    .stepper-item .step-counter {
        position: relative;
        z-index: 5;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--border-color);
        color: var(--text-secondary);
        font-weight: bold;
        margin-bottom: 8px;
        transition: all 0.4s ease;
        font-size: 16px;
    }
    
    /* Animations & Gradients for Stepper */
    .stepper-item.completed .step-counter {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }
    
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
    }
    
    .stepper-item.active .step-counter {
        background: linear-gradient(135deg, var(--primary), #6d28d9);
        border-color: transparent;
        color: white;
        animation: pulse-ring 2s infinite;
        transform: scale(1.1);
    }
    
    .stepper-item.rejected .step-counter {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }
    
    .stepper-item .step-name {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-align: center;
        transition: all 0.3s;
    }
    .stepper-item.completed .step-name, .stepper-item.active .step-name {
        color: var(--text-primary);
    }
    .stepper-item.rejected .step-name {
        color: #ef4444;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px dashed var(--border-color);
    }
    .empty-state img {
        width: 200px;
        opacity: 0.8;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="garage-header fade-in">
        <h1 class="garage-title">{{ __('سياراتي المعروضة') }}</h1>
        <a href="{{ route('bidder.garage.create') }}" class="btn-add">
            <i class="fa-solid fa-plus"></i> {{ __('إضافة سيارة') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row fade-in" style="animation-delay: 0.1s;">
        <div class="col-12">
            <!-- Stats Header -->
            <div class="stats-header">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--primary);">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>{{ __('إجمالي المعروض') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['pending'] }}</h3>
                        <p>{{ __('قيد المراجعة') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['approved'] }}</h3>
                        <p>{{ __('تم الاعتماد') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['draft'] }}</h3>
                        <p>{{ __('مسوداتي') }}</p>
                    </div>
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
                                @if($vehicle->status === 'approved' && !\App\Models\Auction::where('vehicle_id', $vehicle->id)->exists())
                                <li>
                                    <a class="dropdown-item py-2 text-success fw-bold" href="{{ route('bidder.garage.auctions.create', $vehicle->id) }}">
                                        <i class="fa-solid fa-rocket me-2"></i> {{ __('إطلاق المزاد 🚀') }}
                                    </a>
                                </li>
                                @elseif($vehicle->status === 'approved')
                                <li><span class="dropdown-item py-2 text-muted"><i class="fa-solid fa-check-circle me-2"></i> {{ __('تم طلب المزاد') }}</span></li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($vehicle->status === 'approved' && !\App\Models\Auction::where('vehicle_id', $vehicle->id)->exists())
                        <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                            <a href="{{ route('bidder.garage.auctions.create', $vehicle->id) }}" class="btn btn-success rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                <i class="fa-solid fa-rocket me-2"></i> {{ __('إطلاق المزاد') }}
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
