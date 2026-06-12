@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل المركبة - ' . $vehicle->title : 'Vehicle Details - ' . $vehicle->title)

@section('css')
<style>
    /* Premium UI & UX Variables & Theme styles */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --shadow-premium: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
        --shadow-glow-blue: 0 0 20px rgba(59, 130, 246, 0.2);
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
        border: 1px solid var(--glass-border);
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
    .thumbnail-item {
        width: 80px;
        height: 60px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        flex-shrink: 0;
        opacity: 0.7;
    }
    .thumbnail-item:hover, .thumbnail-item.active {
        border-color: var(--primary);
        transform: translateY(-2px);
        opacity: 1;
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Specs Grid */
    .specs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    .spec-card {
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
    }
    [data-theme="dark"] .spec-card {
        background: rgba(15, 23, 42, 0.25);
    }
    .spec-card:hover {
        background: rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    [data-theme="dark"] .spec-card:hover {
        background: rgba(15, 23, 42, 0.4);
    }
    .spec-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .spec-details {
        display: flex;
        flex-direction: column;
    }
    .spec-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .spec-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-color);
        margin-top: 2px;
    }

    /* Features Badges */
    .feature-pill {
        background: rgba(99, 102, 241, 0.08);
        color: var(--primary);
        border: 1px solid rgba(99, 102, 241, 0.15);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 4px;
        transition: all 0.2s;
    }
    .feature-pill:hover {
        background: var(--primary);
        color: #fff;
        transform: scale(1.05);
    }

    /* Tab Custom Styling */
    .nav-tabs-premium {
        border-bottom: 2px solid var(--glass-border);
        gap: 10px;
    }
    .nav-tabs-premium .nav-link {
        border: none;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.9rem;
        padding: 12px 20px;
        background: transparent;
        position: relative;
        transition: all 0.2s;
    }
    .nav-tabs-premium .nav-link.active {
        color: var(--primary);
        background: transparent;
    }
    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary);
        border-radius: 2px;
    }

    /* Status timeline card */
    .timeline-card {
        border-left: 2px solid var(--glass-border);
        padding-left: 20px;
        margin-left: 10px;
        position: relative;
    }
    html[dir="rtl"] .timeline-card {
        border-left: none;
        border-right: 2px solid var(--glass-border);
        padding-left: 0;
        padding-right: 20px;
        margin-left: 0;
        margin-right: 10px;
    }
    .timeline-dot {
        position: absolute;
        left: -6px;
        top: 2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary);
    }
    html[dir="rtl"] .timeline-dot {
        left: auto;
        right: -6px;
    }
</style>
@endsection

@section('content')
<div class="page-header mb-4">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1 style="font-weight: 800; letter-spacing: -0.5px; margin: 0;">{{ $vehicle->title }}</h1>
            <div>
                @if($vehicle->status === 'approved')
                    <span class="status-indicator status-live" style="background:#dcfce7; color:#15803d; padding:6px 14px; border-radius:50px; font-weight:700; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-check"></i> {{ __('Approved') }}</span>
                @elseif($vehicle->status === 'pending')
                    <span class="status-indicator status-scheduled" style="background:#fef3c7; color:#b45309; padding:6px 14px; border-radius:50px; font-weight:700; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-clock"></i> {{ __('Pending') }}</span>
                @else
                    <span class="status-indicator status-cancelled" style="background:#fee2e2; color:#b91c1c; padding:6px 14px; border-radius:50px; font-weight:700; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-xmark"></i> {{ __('Rejected') }}</span>
                @endif
            </div>
        </div>
        <div class="breadcrumb" style="font-size: 0.85rem; margin-top: 8px;">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.vehicles.index') }}">{{ __('Vehicles') }}</a> / 
            {{ $vehicle->title }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill font-weight-bold" style="border-width: 2px;">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('Back to List') }}
        </a>
        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 text-white font-weight-bold rounded-pill" style="border: none; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25);">
            <i class="fa-solid fa-pen-to-square"></i>
            {{ __('Edit Vehicle') }}
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column (Gallery, Specs, Description) -->
    <div class="col-lg-8 col-12">
        <!-- Gallery Panel -->
        <div class="premium-panel">
            <div class="panel-body-premium">
                <div class="gallery-container">
                    <div class="main-image-viewport">
                        <img id="main-gallery-img" src="{{ $vehicle->primary_image_url ?? asset('images/default-vehicle.png') }}" alt="{{ $vehicle->title }}">
                        <div class="gallery-overlay-badge">
                            <span class="badge bg-black bg-opacity-70 text-white px-3 py-2 rounded-pill" style="font-weight: 700; backdrop-filter: blur(4px);">
                                <i class="fa-solid fa-camera me-1"></i> <span id="gallery-count">1</span> / {{ count($vehicle->images) ?: 1 }}
                            </span>
                        </div>
                    </div>
                    
                    @if(count($vehicle->images) > 0)
                        <div class="gallery-thumbnails-grid">
                            @foreach($vehicle->images as $index => $img)
                                <div class="thumbnail-item {{ $img->is_primary ? 'active' : '' }}" onclick="switchMainImage('{{ asset('storage/' . $img->image_path) }}', {{ $index + 1 }}, this)">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Thumbnail">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Specifications Panel -->
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3><i class="fa-solid fa-gears text-primary"></i> {{ __('Vehicle Specifications') }}</h3>
            </div>
            <div class="panel-body-premium">
                <div class="specs-grid">
                    <!-- Make & Model -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-car-side"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Make') }} / {{ __('Model') }}</span>
                            <span class="spec-value">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                        </div>
                    </div>
                    <!-- Year -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-calendar"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Year') }}</span>
                            <span class="spec-value">{{ $vehicle->year }}</span>
                        </div>
                    </div>
                    <!-- Transmission -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-circle-dot"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Transmission') }}</span>
                            <span class="spec-value">{{ $vehicle->transmission ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <!-- Fuel Type -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-gas-pump"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Fuel Type') }}</span>
                            <span class="spec-value">{{ $vehicle->fuel_type ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <!-- Engine -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-bolt"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Engine Capacity') }} / {{ __('Cylinders') }}</span>
                            <span class="spec-value">{{ $vehicle->engine_capacity ?? 'N/A' }} / {{ $vehicle->cylinders ?? 'N/A' }} Cyl</span>
                        </div>
                    </div>
                    <!-- Mileage -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-gauge"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Mileage') }}</span>
                            <span class="spec-value">{{ number_format($vehicle->mileage ?? 0) }} km</span>
                        </div>
                    </div>
                    <!-- VIN Number -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-barcode"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('VIN Number') }}</span>
                            <span class="spec-value">{{ $vehicle->vin_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <!-- Plate Number -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-rectangle-list"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Plate Number') }}</span>
                            <span class="spec-value">{{ $vehicle->plate_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <!-- Origin -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-globe"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Country of Origin') }}</span>
                            <span class="spec-value">{{ $vehicle->country_of_origin ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <!-- Color -->
                    <div class="spec-card">
                        <div class="spec-icon-wrapper"><i class="fa-solid fa-palette"></i></div>
                        <div class="spec-details">
                            <span class="spec-label">{{ __('Color') }}</span>
                            <span class="spec-value">{{ $vehicle->color ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Panels -->
        <div class="premium-panel">
            <div class="panel-body-premium p-0">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs nav-tabs-premium px-4 pt-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="ar-desc-tab" data-bs-toggle="tab" data-bs-target="#ar-desc" type="button" role="tab">{{ __('Description (AR)') }}</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="en-desc-tab" data-bs-toggle="tab" data-bs-target="#en-desc" type="button" role="tab">{{ __('Description (EN)') }}</button>
                    </li>
                </ul>
                <div class="tab-content p-4">
                    <div class="tab-pane fade show active" id="ar-desc" role="tabpanel">
                        <p style="white-space: pre-line; line-height: 1.8; color: var(--text-color);">{{ $vehicle->description_ar ?: __('No Arabic description available.') }}</p>
                    </div>
                    <div class="tab-pane fade" id="en-desc" role="tabpanel">
                        <p style="white-space: pre-line; line-height: 1.8; color: var(--text-color);">{{ $vehicle->description_en ?: __('No English description available.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column (Review workflow, Features, Issues, Submitter info) -->
    <div class="col-lg-4 col-12">
        <!-- Review Action Panel -->
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3><i class="fa-solid fa-circle-check text-primary"></i> {{ __('Review & Status') }}</h3>
            </div>
            <div class="panel-body-premium">
                <div class="mb-4">
                    <span class="text-muted d-block font-weight-bold mb-1" style="font-size:0.8rem; text-transform:uppercase;">{{ __('Current Status') }}</span>
                    @if($vehicle->status === 'approved')
                        <div class="alert alert-success d-flex align-items-center gap-2 border-0" style="border-radius:12px;">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <div><strong>{{ __('Approved & Active') }}</strong></div>
                        </div>
                    @elseif($vehicle->status === 'pending')
                        <div class="alert alert-warning d-flex align-items-center gap-2 border-0" style="border-radius:12px;">
                            <i class="fa-solid fa-circle-exclamation fs-5"></i>
                            <div><strong>{{ __('Pending Review') }}</strong></div>
                        </div>
                    @else
                        <div class="alert alert-danger d-flex align-items-center gap-2 border-0 mb-2" style="border-radius:12px;">
                            <i class="fa-solid fa-circle-xmark fs-5"></i>
                            <div><strong>{{ __('Rejected') }}</strong></div>
                        </div>
                        @if($vehicle->rejection_reason)
                            <div class="p-3 bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 rounded-3 mb-3" style="font-size:0.85rem;">
                                <strong>{{ __('Rejection Reason:') }}</strong> {{ $vehicle->rejection_reason }}
                            </div>
                        @endif
                    @endif
                </div>

                @if($vehicle->status === 'pending')
                    <div class="d-grid gap-2">
                        <button onclick="approveVehicle({{ $vehicle->id }})" class="btn btn-success py-2.5 rounded-pill font-weight-bold d-flex align-items-center justify-content-center gap-2" style="border:none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-check"></i> {{ __('Approve & Verify') }}
                        </button>
                        <button onclick="rejectVehicle({{ $vehicle->id }})" class="btn btn-danger py-2.5 rounded-pill font-weight-bold d-flex align-items-center justify-content-center gap-2" style="border:none; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25);">
                            <i class="fa-solid fa-xmark"></i> {{ __('Reject') }}
                        </button>
                    </div>
                @else
                    <div class="d-grid">
                        <button onclick="reconsiderStatus({{ $vehicle->id }})" class="btn btn-outline-primary py-2 rounded-pill font-weight-bold d-flex align-items-center justify-content-center gap-2" style="border-width:2px;">
                            <i class="fa-solid fa-arrows-spin"></i> {{ __('Change Status') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submitter Panel -->
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3><i class="fa-solid fa-user text-primary"></i> {{ __('Submitter Info') }}</h3>
            </div>
            <div class="panel-body-premium">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:50px; height:50px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">
                        {{ strtoupper(substr($vehicle->submittedBy->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <strong class="d-block text-color fs-6">{{ $vehicle->submittedBy->name ?? __('Unknown Submitter') }}</strong>
                        <span class="text-muted d-block" style="font-size:0.8rem;"><i class="fa-solid fa-envelope me-1"></i> {{ $vehicle->submittedBy->email ?? '-' }}</span>
                        <span class="text-muted d-block" style="font-size:0.8rem;"><i class="fa-solid fa-clock me-1"></i> {{ $vehicle->created_at ? $vehicle->created_at->format('Y-m-d') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Panel -->
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3><i class="fa-solid fa-list-check text-primary"></i> {{ __('Features') }}</h3>
            </div>
            <div class="panel-body-premium">
                @if(is_array($vehicle->features) && count($vehicle->features) > 0)
                    <div class="d-flex flex-wrap" style="margin: -4px;">
                        @foreach($vehicle->features as $feature)
                            <span class="feature-pill"><i class="fa-solid fa-circle-check"></i> {{ $feature }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted" style="font-size:0.9rem;">{{ __('No specifications or features listed.') }}</span>
                @endif
            </div>
        </div>

        <!-- Issues Panel -->
        <div class="premium-panel">
            <div class="panel-header-premium">
                <h3><i class="fa-solid fa-circle-exclamation text-danger"></i> {{ __('Known Issues') }}</h3>
            </div>
            <div class="panel-body-premium">
                @if($vehicle->issues)
                    <div class="p-3 bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 rounded-3" style="font-size:0.9rem; line-height:1.6; color:var(--text-color) !important;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $vehicle->issues }}
                    </div>
                @else
                    <div class="p-3 bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-3" style="font-size:0.9rem;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ __('No major issues reported.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function switchMainImage(url, count, element) {
        // Switch thumbnail active state
        $('.thumbnail-item').removeClass('active');
        $(element).addClass('active');

        // Swap main image
        $('#main-gallery-img').fadeOut(150, function() {
            $(this).attr('src', url).fadeIn(150);
            $('#gallery-count').text(count);
        });
    }

    function approveVehicle(id) {
        let url = "{{ route('admin.vehicles.approve', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Approve Vehicle?') }}",
            text: "{{ __('Are you sure you want to approve this vehicle?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, approve!') }}",
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
                            location.reload();
                        }
                    }
                });
            }
        });
    }

    function rejectVehicle(id) {
        let url = "{{ route('admin.vehicles.reject', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Reject Vehicle') }}",
            input: 'textarea',
            inputLabel: "{{ __('Reason for Rejection') }}",
            inputPlaceholder: "{{ __('Please write the reason for rejecting the vehicle...') }}",
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Reject') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            inputValidator: (value) => {
                if (!value) {
                    return "{{ __('You must write a reason for rejection!') }}";
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        rejection_reason: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    }
                });
            }
        });
    }

    function reconsiderStatus(id) {
        Swal.fire({
            title: "{{ __('Reconsider Status') }}",
            text: "{{ __('Choose the new status for this vehicle:') }}",
            icon: 'question',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            denyButtonColor: '#ef4444',
            confirmButtonText: "{{ __('Approve') }}",
            denyButtonText: "{{ __('Reject') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                approveVehicle(id);
            } else if (result.isDenied) {
                rejectVehicle(id);
            }
        });
    }
</script>
@endsection
