@extends('layouts.bidder')

@section('title', __('إطلاق المزاد'))

@section('css')
<style>
    .auction-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .auction-header h2 {
        font-weight: 700;
        color: var(--primary);
    }
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    
    /* Pricing Simulator */
    .pricing-gauge {
        background: rgba(0,0,0,0.02);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 1.5rem;
        border: 1px dashed var(--border-color);
    }
    .gauge-meter {
        height: 10px;
        background: #e2e8f0;
        border-radius: 5px;
        margin: 1rem 0;
        overflow: hidden;
        position: relative;
    }
    .gauge-fill {
        height: 100%;
        background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
        width: 0%;
        transition: width 0.5s ease;
    }
    .gauge-text {
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    /* Custom Radio Cards */
    .radio-card-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .radio-card {
        position: relative;
        display: block;
        cursor: pointer;
    }
    .radio-card input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .radio-card .card-content {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        text-align: center;
    }
    .radio-card input:checked ~ .card-content {
        border-color: var(--primary);
        background: rgba(139, 92, 246, 0.05);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
    }
    .radio-card .card-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--text-secondary);
    }
    .radio-card input:checked ~ .card-content .card-icon {
        color: var(--primary);
    }
    .radio-card h5 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    /* Live Preview */
    .live-preview {
        background: #111827;
        border-radius: 16px;
        color: white;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .preview-tag {
        position: absolute;
        top: 10px;
        left: -30px;
        background: #ef4444;
        color: white;
        padding: 5px 30px;
        transform: rotate(-45deg);
        font-size: 0.7rem;
        font-weight: bold;
        z-index: 2;
    }
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .preview-timer {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: bold;
        font-family: monospace;
        font-size: 1.2rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="auction-header fade-in">
        <h2><i class="fa-solid fa-gavel"></i> {{ __('إطلاق مزاد جديد') }}</h2>
        <p class="text-secondary">{{ __('قم بتهيئة إعدادات مزادك للسيارة المعتمدة: ') }} <strong>{{ $vehicle->title }}</strong></p>
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

    <form action="{{ route('bidder.garage.auctions.store') }}" method="POST">
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
                                <input type="number" name="start_price" id="start_price" class="form-control" placeholder="مثال: 50000" required oninput="updateGauge()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('السعر الذي سيبدأ منه المزاد.') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('السعر المستهدف (Reserve Price)') }}</label>
                            <div class="input-group">
                                <input type="number" name="reserve_price" id="reserve_price" class="form-control" placeholder="مثال: 70000" oninput="updateGauge()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('الحد الأدنى الذي تقبل البيع به.') }}</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-warning"><i class="fa-solid fa-bolt"></i> {{ __('سعر الشراء الفوري (Buy it Now)') }}</label>
                            <div class="input-group">
                                <input type="number" name="buy_now_price" id="buy_now_price" class="form-control" placeholder="اختياري" oninput="updatePreview()">
                                <span class="input-group-text">ر.س</span>
                            </div>
                            <small class="text-muted">{{ __('سعر يتيح للمزايد شراء السيارة فوراً وإغلاق المزاد.') }}</small>
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
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control" required onchange="updatePreview()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('وقت الانتهاء') }} *</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control" required onchange="updatePreview()">
                        </div>
                    </div>
                </div>

                <!-- 3. Strictness Mode -->
                <div class="form-card fade-in" style="animation-delay: 0.3s;">
                    <h5 class="mb-4"><i class="fa-solid fa-shield-halved text-primary me-2"></i> {{ __('وضعيات المزاد') }}</h5>
                    <div class="radio-card-wrapper">
                        <label class="radio-card">
                            <input type="radio" name="bidding_mode" value="open" checked>
                            <div class="card-content">
                                <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                                <h5>{{ __('المزاد المرن') }}</h5>
                                <p class="text-muted small mb-0">{{ __('بدون ضمان مالي. مشاركات أكثر ولكن جدية أقل.') }}</p>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="bidding_mode" value="strict">
                            <div class="card-content">
                                <div class="card-icon"><i class="fa-solid fa-lock"></i></div>
                                <h5>{{ __('مزاد النخبة') }}</h5>
                                <p class="text-muted small mb-0">{{ __('يتطلب ضمان (10% من سعر البداية). مشاركات أقل وجدية 100%.') }}</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="text-end mb-5 fade-in" style="animation-delay: 0.4s;">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow" style="border-radius: 12px;">
                        <i class="fa-solid fa-rocket me-2"></i> {{ __('إرسال المزاد للإدارة') }}
                    </button>
                </div>
            </div>

            <!-- Left Column: Live Preview -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 100px;">
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
                </div>
            </div>
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
