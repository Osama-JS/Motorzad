@extends('layouts.bidder')

@section('title', isset($isEdit) && $isEdit ? __('تعديل المزاد') : __('إطلاق المزاد'))

@section('css')
<style>
    /* ==========================================================================
       ULTRA PREMIUM ADAPTIVE LUXURY THEME (AUCTION)
       ========================================================================== */
    :root {
        --font-primary: 'Tajawal', 'Outfit', sans-serif;
        --primary-glow: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.25);
        --neon-primary: #8b5cf6;
        --radius-xl: 24px;
        --radius-lg: 16px;
        --radius-md: 12px;
    }

    body { font-family: var(--font-primary); }

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

    /* Premium Cards */
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 3.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 15px 40px rgba(0,0,0,0.03);
        transition: all 0.4s ease;
        position: relative;
    }
    
    .form-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        box-shadow: 0 25px 60px rgba(0,0,0,0.06);
    }

    /* Massive Action Bar */
    .action-bar-massive {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem 3.5rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    .action-bar-massive:hover {
        border-color: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.3);
        box-shadow: 0 25px 60px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.1);
    }
    .action-bar-massive::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 6px; height: 100%;
        background: var(--primary);
    }
    .action-bar-text h4 {
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    .action-bar-text p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 1.1rem;
    }
    .btn-massive {
        background: linear-gradient(135deg, var(--primary), #8b5cf6);
        color: white;
        border: none;
        padding: 1.25rem 4rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.3rem;
        box-shadow: 0 15px 35px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.4);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    .btn-massive::after {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
        transform: skewX(-20deg);
        animation: shimmer 3s infinite;
    }
    .btn-massive:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(var(--bs-primary-rgb, 99, 102, 241), 0.6);
        color: white;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        20% { left: 200%; }
        100% { left: 200%; }
    }
    @media (max-width: 768px) {
        .action-bar-massive {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
            padding: 2rem 1.5rem;
        }
        .action-bar-massive::before {
            width: 100%; height: 6px; top: 0; left: 0;
        }
        .btn-massive { width: 100%; justify-content: center; }
    }

    .form-card h5 {
        font-weight: 800;
        font-size: 1.5rem;
        margin-bottom: 3rem !important;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 15px;
        letter-spacing: 0.5px;
    }
    
    .form-card h5 i {
        background: var(--primary-glow);
        color: var(--primary);
        padding: 12px;
        border-radius: 12px;
        font-size: 1.2rem;
    }
    
    /* Ultra Premium Inputs */
    .form-label {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 1rem;
        color: var(--text-primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .input-group {
        background: var(--bg-body);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
    }

    .input-group:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-glow), inset 0 2px 5px rgba(0,0,0,0.02);
    }

    .form-control, .form-select {
        border: none !important;
        background: transparent !important;
        color: var(--text-primary);
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: none !important;
    }
    
    .input-group-text {
        border: none !important;
        background: rgba(139, 92, 246, 0.05);
        font-weight: 800;
        color: var(--primary);
        padding: 0 1.5rem;
        border-right: 1px solid var(--border-color) !important;
    }

    /* Pricing Simulator */
    .pricing-gauge {
        background: var(--bg-body);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        text-align: center;
        margin-bottom: 3rem;
        border: 1px dashed rgba(139, 92, 246, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .pricing-gauge::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at center, rgba(139, 92, 246, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .gauge-meter {
        height: 16px;
        background: var(--border-color);
        border-radius: 50px;
        margin: 2rem 0;
        overflow: visible; /* To allow glow */
        position: relative;
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .gauge-fill {
        height: 100%;
        background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
        width: 0%;
        border-radius: 50px;
        transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); /* Bouncy */
        position: relative;
    }
    
    .gauge-fill::after {
        content: '';
        position: absolute;
        right: -8px;
        top: -4px;
        width: 24px;
        height: 24px;
        background: white;
        border: 4px solid #10b981;
        border-radius: 50%;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
        transition: border-color 0.8s ease;
    }

    .gauge-text {
        font-weight: 800;
        color: var(--text-secondary);
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    
    /* Custom Radio Cards */
    .radio-card-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    
    .radio-card input { display: none; }
    
    .radio-card .card-content {
        border: 2px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2.5rem 2rem;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
        background: var(--bg-body);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .radio-card:hover .card-content {
        border-color: rgba(139, 92, 246, 0.4);
        transform: translateY(-5px);
    }
    
    .radio-card input:checked ~ .card-content {
        border-color: var(--primary);
        background: var(--primary-glow);
        transform: translateY(-8px);
    }
    
    .radio-card input:checked ~ .card-content::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 15px;
        right: 15px;
        color: var(--primary);
        font-size: 1.5rem;
    }
    
    .radio-card .card-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        color: var(--text-secondary);
        transition: all 0.4s;
    }
    
    .radio-card input:checked ~ .card-content .card-icon {
        color: var(--primary);
        transform: scale(1.15) translateY(-5px);
        filter: drop-shadow(0 10px 10px rgba(139, 92, 246, 0.3));
    }
    
    .radio-card h5 {
        font-weight: 800;
        margin-bottom: 0.75rem !important;
        color: var(--text-primary);
        justify-content: center;
    }
    
    /* Live Preview - The Crown Jewel */
    .live-preview {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 30px;
        color: #ffffff;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 30px 60px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
    }
    
    .live-preview::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 60%);
        pointer-events: none;
        z-index: 0;
    }
    
    .preview-tag {
        position: absolute;
        top: 30px;
        left: -45px;
        background: linear-gradient(90deg, #ef4444, #dc2626);
        color: white;
        padding: 8px 50px;
        transform: rotate(-45deg);
        font-size: 0.85rem;
        font-weight: 900;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.5);
        letter-spacing: 2px;
    }
    
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }
    
    .preview-timer {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 800;
        font-family: 'Outfit', monospace;
        font-size: 1.5rem;
        animation: pulse-border 2s infinite;
        border: 1px solid rgba(239, 68, 68, 0.5);
        letter-spacing: 2px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .preview-timer::before {
        content: '';
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        box-shadow: 0 0 10px #ef4444;
        animation: blink 1s infinite;
    }
    
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3); }
        70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    /* Buttons */
    .btn-submit {
        background: linear-gradient(135deg, var(--primary), var(--neon-primary));
        border: none;
        color: white;
        padding: 1.25rem 3.5rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.2rem;
        letter-spacing: 1px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 15px 30px rgba(139, 92, 246, 0.4);
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .btn-submit::after {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
        transform: skewX(-20deg);
        animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        20% { left: 200%; }
        100% { left: 200%; }
    }
    
    .btn-submit:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.6);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="garage-hero-card fade-in">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fa-solid fa-gavel"></i>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ isset($isEdit) && $isEdit ? __('تعديل تفاصيل المزاد') : __('إطلاق مزاد جديد') }}</h1>
                <p class="hero-subtitle">{{ __('قم بتهيئة إعدادات المزاد بدقة للسيارة المعتمدة:') }} <strong>{{ $vehicle->title }}</strong></p>
            </div>
        </div>
        <a href="{{ route('bidder.garage.index') }}" class="btn-add">
            <i class="fa-solid fa-arrow-right"></i> {{ __('العودة للمعرض') }}
        </a>
        <div class="hero-decoration">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($isEdit) && $isEdit ? route('bidder.garage.auctions.update', $auction->id) : route('bidder.garage.auctions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

        <div class="row">
            <!-- Right Column: Form -->
            <div class="col-lg-7">
                <!-- 1. Pricing Strategy -->
                <div class="form-card fade-in" style="animation-delay: 0.1s;">
                    <h5 class="mb-4"><i class="fa-solid fa-tags text-primary me-2"></i> {{ __('استراتيجية التسعير') }}</h5>
                    
                    <div class="pricing-gauge">
                        <i class="fa-solid fa-gauge-high mb-2" style="font-size: 2rem; color: var(--text-secondary);" id="gaugeIcon"></i>
                        <div class="gauge-meter">
                            <div class="gauge-fill" id="gaugeFill" style="width: 10%;"></div>
                        </div>
                        <div class="gauge-text" id="gaugeText">{{ __('أدخل الأسعار لرؤية التقييم') }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('سعر البداية (Start Price)') }} *</label>
                            <div class="input-group">
                                <input type="number" name="start_price" id="start_price" class="form-control" placeholder="مثال: 50000" value="{{ old('start_price', isset($isEdit) && $isEdit ? $auction->start_price : '') }}" required oninput="updateGauge()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('السعر الذي سيبدأ منه المزاد.') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('السعر المستهدف (Reserve Price)') }}</label>
                            <div class="input-group">
                                <input type="number" name="reserve_price" id="reserve_price" class="form-control" placeholder="مثال: 70000" value="{{ old('reserve_price', isset($isEdit) && $isEdit ? $auction->reserve_price : '') }}" oninput="updateGauge()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('الحد الأدنى الذي تقبل البيع به.') }}</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-warning"><i class="fa-solid fa-bolt"></i> {{ __('سعر الشراء الفوري (Buy it Now)') }}</label>
                            <div class="input-group">
                                <input type="number" name="buy_now_price" id="buy_now_price" class="form-control" placeholder="اختياري" value="{{ old('buy_now_price', isset($isEdit) && $isEdit ? $auction->buy_now_price : '') }}" oninput="updatePreview()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('سعر يتيح للمزايد شراء السيارة فوراً وإغلاق المزاد.') }}</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label"><i class="fa-solid fa-arrow-up-right-dots text-primary me-1"></i> {{ __('الحد الأدنى للزيادة في المزايدة (Min Bid Increment)') }} *</label>
                            <div class="input-group">
                                <input type="number" name="min_bid_increment" id="min_bid_increment" class="form-control" placeholder="مثال: 500" value="{{ old('min_bid_increment', isset($isEdit) && $isEdit ? $auction->min_bid_increment : 500) }}" required>
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('أقل مبلغ يمكن للمزايد إضافته فوق السعر الحالي للمزاد.') }}</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Scheduling -->
                <div class="form-card fade-in" style="animation-delay: 0.2s;">
                    <h5 class="mb-4"><i class="fa-regular fa-calendar-check text-primary me-2"></i> {{ __('جدولة المزاد') }}</h5>
                    <div class="alert alert-info" style="background: rgba(14, 165, 233, 0.1); border: none; color: #0ea5e9;">
                        <i class="fa-solid fa-lightbulb me-2"></i> {{ __('الأوقات الذهبية: نهاية الأسبوع مساءً تجذب تفاعلاً أعلى بنسبة 40%!') }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('وقت البدء') }} *</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', isset($isEdit) && $isEdit ? $auction->start_time->format('Y-m-d\TH:i') : '') }}" required onchange="updatePreview()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('وقت الانتهاء') }} *</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', isset($isEdit) && $isEdit ? $auction->end_time->format('Y-m-d\TH:i') : '') }}" required onchange="updatePreview()">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fa-solid fa-clock-rotate-left text-info me-1"></i> {{ __('دقائق التمديد التلقائي (Auto Extend Minutes)') }}</label>
                            <div class="input-group">
                                <input type="number" name="auto_extend_minutes" id="auto_extend_minutes" class="form-control" placeholder="مثال: 5" value="{{ old('auto_extend_minutes', isset($isEdit) && $isEdit ? $auction->auto_extend_minutes : 0) }}" min="0">
                                <span class="input-group-text">دقائق</span>
                            </div>
                            <small class="text-muted">{{ __('إذا تمت المزايدة في الدقائق الأخيرة، سيتم تمديد المزاد بهذه المدة تلقائياً لإتاحة الفرصة للآخرين. اتركها 0 للإلغاء.') }}</small>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Left Column: Live Preview -->
            <div class="col-lg-5">
                <div>
                    <h5 class="mb-3"><i class="fa-solid fa-eye text-primary me-2"></i> {{ __('معاينة المزاد (كيف سيبدو؟)') }}</h5>
                    <div class="live-preview shadow-lg">
                        <div class="preview-tag">LIVE PREVIEW</div>
                        <div class="preview-header">
                            <div>
                                <div style="font-size: 0.8rem; color: #9ca3af;">{{ __('الوقت المتبقي') }}</div>
                                <div class="preview-timer">48:15:32</div>
                            </div>
                            <div class="text-end">
                                <div style="font-size: 0.8rem; color: #9ca3af;">{{ __('عدد المزايدين') }}</div>
                                <div style="font-weight: bold; font-size: 1.2rem;"><i class="fa-solid fa-users me-1"></i> 12</div>
                            </div>
                        </div>
                        
                        <div class="text-center mb-4">
                            <img src="{{ $vehicle->primary_image_url ?? asset('images/placeholder.png') }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;" alt="Car">
                            <h4 style="font-weight: 700;">{{ $vehicle->title }}</h4>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3 p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                            <div>
                                <span style="font-size: 0.8rem; color: #9ca3af; display: block;">{{ __('أعلى مزايدة') }}</span>
                                <span style="font-size: 1.5rem; font-weight: bold; color: #10b981;" id="preview_current_bid">0 ر.س</span>
                            </div>
                            <div class="text-end" id="preview_buy_now_container" style="display: none;">
                                <span style="font-size: 0.8rem; color: #9ca3af; display: block;"><i class="fa-solid fa-bolt text-warning"></i> {{ __('الشراء الفوري') }}</span>
                                <span style="font-size: 1.2rem; font-weight: bold; color: #f59e0b;" id="preview_buy_now">0 ر.س</span>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-success w-100" style="padding: 1rem; font-weight: 700; font-size: 1.2rem;" disabled>
                            {{ __('المزايدة الآن') }}
                        </button>
                    </div>

                    <!-- 3. Location (Moved) -->
                    <div class="form-card fade-in mt-4" style="animation-delay: 0.25s;">
                        <h5 class="mb-4"><i class="fa-solid fa-map-location-dot text-primary me-2"></i> {{ __('موقع المزاد / السيارة') }}</h5>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('الموقع بالعربية') }} *</label>
                                <input type="text" name="location_ar" id="location_ar" class="form-control" placeholder="مثال: الرياض، حي الياسمين" value="{{ old('location_ar', isset($isEdit) && $isEdit ? $auction->location_ar : '') }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('الموقع بالإنجليزية') }} *</label>
                                <input type="text" name="location_en" id="location_en" class="form-control" placeholder="e.g. Riyadh, Al Yasmin" value="{{ old('location_en', isset($isEdit) && $isEdit ? $auction->location_en : '') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Strictness Mode (Moved) -->
                    <div class="form-card fade-in" style="animation-delay: 0.3s;">
                        <h5 class="mb-4"><i class="fa-solid fa-shield-halved text-primary me-2"></i> {{ __('وضعيات المزاد') }}</h5>
                        <div class="radio-card-wrapper" style="grid-template-columns: 1fr; gap: 1rem;">
                            <label class="radio-card">
                                <input type="radio" name="bidding_mode" value="open" {{ (!isset($isEdit) || (isset($isEdit) && !$auction->deposit_required)) ? 'checked' : '' }}>
                                <div class="card-content" style="padding: 1.5rem;">
                                    <div class="card-icon" style="font-size: 2rem; margin-bottom: 1rem;"><i class="fa-solid fa-users"></i></div>
                                    <h5 style="font-size: 1.2rem; margin-bottom: 0.5rem !important;">{{ __('المزاد المرن') }}</h5>
                                    <p class="text-muted small mb-0">{{ __('بدون ضمان مالي. مشاركات أكثر ولكن جدية أقل.') }}</p>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="bidding_mode" value="strict" {{ (isset($isEdit) && $isEdit && $auction->deposit_required) ? 'checked' : '' }}>
                                <div class="card-content" style="padding: 1.5rem;">
                                    <div class="card-icon" style="font-size: 2rem; margin-bottom: 1rem;"><i class="fa-solid fa-lock"></i></div>
                                    <h5 style="font-size: 1.2rem; margin-bottom: 0.5rem !important;">{{ __('مزاد النخبة') }}</h5>
                                    <p class="text-muted small mb-0">{{ __('يتطلب ضمان (10%). مشاركات أقل وجدية 100%.') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Massive Action Bar -->
        <div class="action-bar-massive fade-in" style="animation-delay: 0.4s;">
            <div class="action-bar-text">
                <h4>{{ isset($isEdit) && $isEdit ? __('جاهز لحفظ التعديلات؟') : __('جاهز لإطلاق المزاد؟') }}</h4>
                <p>{{ __('بمجرد الاعتماد سيتم تطبيق التحديثات على المنصة فوراً.') }}</p>
            </div>
            <button type="submit" class="btn-massive">
                <i class="fa-solid {{ isset($isEdit) && $isEdit ? 'fa-save' : 'fa-rocket' }}"></i> 
                {{ isset($isEdit) && $isEdit ? __('حفظ التعديلات') : __('اعتماد وإطلاق المزاد') }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    function updateGauge() {
        let start = parseFloat(document.getElementById('start_price').value) || 0;
        let reserve = parseFloat(document.getElementById('reserve_price').value) || 0;
        
        let fill = document.getElementById('gaugeFill');
        let text = document.getElementById('gaugeText');
        let icon = document.getElementById('gaugeIcon');
        
        if (start === 0 && reserve === 0) {
            fill.style.width = '10%';
            fill.style.background = '#e2e8f0';
            text.innerText = 'أدخل الأسعار لرؤية التقييم';
            icon.style.color = 'var(--text-secondary)';
            return;
        }

        document.getElementById('preview_current_bid').innerText = new Intl.NumberFormat().format(start) + ' ر.س';
        
        if (reserve === 0) {
            fill.style.width = '100%';
            fill.style.background = '#10b981'; // Green
            text.innerText = 'بدون سعر مستهدف! فرصة البيع ممتازة 100%';
            text.style.color = '#10b981';
            icon.style.color = '#10b981';
            return;
        }
        
        let diff = (reserve - start) / start;
        
        if (diff <= 0.2) {
            fill.style.width = '90%';
            fill.style.background = '#10b981';
            text.innerText = 'استراتيجية ممتازة! الأسعار متقاربة وفرصة البيع عالية.';
            text.style.color = '#10b981';
            icon.style.color = '#10b981';
        } else if (diff <= 0.5) {
            fill.style.width = '60%';
            fill.style.background = '#f59e0b';
            text.innerText = 'استراتيجية متوسطة. قد تحتاج لوقت أطول للوصول للسعر المستهدف.';
            text.style.color = '#f59e0b';
            icon.style.color = '#f59e0b';
        } else {
            fill.style.width = '30%';
            fill.style.background = '#ef4444';
            text.innerText = 'سعر مستهدف مرتفع جداً مقارنة بالبداية! قد يقلل من حماس المزايدين.';
            text.style.color = '#ef4444';
            icon.style.color = '#ef4444';
        }
    }
    
    function updatePreview() {
        let buyNow = parseFloat(document.getElementById('buy_now_price').value) || 0;
        let buyNowContainer = document.getElementById('preview_buy_now_container');
        let buyNowText = document.getElementById('preview_buy_now');
        
        if (buyNow > 0) {
            buyNowContainer.style.display = 'block';
            buyNowText.innerText = new Intl.NumberFormat().format(buyNow) + ' ر.س';
        } else {
            buyNowContainer.style.display = 'none';
        }
    }
</script>
@endsection
