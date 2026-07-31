@extends('layouts.bidder')

@section('title', __('مسوداتي'))

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
    
    /* Progress Bar for Draft */
    .draft-progress-container {
        background: rgba(0,0,0,0.02);
        padding: 1.2rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .progress-wrapper {
        flex-grow: 1;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }
    .progress {
        height: 8px;
        border-radius: 10px;
        background-color: var(--border-color);
    }
    .progress-bar {
        background: linear-gradient(90deg, var(--primary), #8b5cf6);
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(139, 92, 246, 0.4);
    }
    
    /* Quick Actions */
    .btn-resume {
        background: linear-gradient(135deg, var(--primary), #6d28d9);
        color: white;
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-resume:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(109, 40, 217, 0.3);
        color: white;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px dashed var(--border-color);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="garage-header fade-in">
        <h1 class="garage-title">{{ __('مسوداتي') }}</h1>
        <a href="{{ route('bidder.garage.create') }}" class="btn-add">
            <i class="fa-solid fa-plus"></i> {{ __('إكمال الإضافة') }}
        </a>
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
