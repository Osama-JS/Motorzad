@extends('layouts.bidder')

@section('title', __('الاشتراك كبائع'))

@section('css')
<style>
    /* Ultra Premium Seller Subscription UI */
    .seller-subscription-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 0 4rem;
        position: relative;
    }

    /* Abstract Background */
    .seller-subscription-wrapper::before {
        content: '';
        position: absolute;
        top: -100px;
        left: 50%;
        transform: translateX(-50%);
        width: 100vw;
        height: 600px;
        background: radial-gradient(circle at 50% 0%, rgba(245, 158, 11, 0.08) 0%, transparent 60%),
                    radial-gradient(circle at 20% 40%, rgba(229, 62, 62, 0.05) 0%, transparent 50%);
        z-index: -1;
        pointer-events: none;
    }

    .premium-seller-card {
        background: var(--bg-card);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 32px;
        padding: 4rem 3rem;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.04), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    html[data-bs-theme="dark"] .premium-seller-card {
        border-color: rgba(255, 255, 255, 0.05);
        background: linear-gradient(145deg, var(--bg-card) 0%, rgba(20, 20, 30, 0.6) 100%);
    }

    /* Top decorative gradient line */
    .premium-seller-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #f59e0b, #ef4444, #3b82f6);
        background-size: 200% auto;
        animation: gradientMove 3s ease infinite;
    }
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .seller-hero-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%);
        color: #f59e0b;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2.5rem;
        position: relative;
        box-shadow: 0 15px 35px rgba(245, 158, 11, 0.15);
        transform: rotate(-5deg);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .seller-hero-icon:hover {
        transform: rotate(0deg) scale(1.1);
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 20px 40px rgba(245, 158, 11, 0.3);
    }
    .seller-hero-icon svg {
        width: 46px;
        height: 46px;
    }

    .premium-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        background: linear-gradient(to right, var(--text), var(--text-muted));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }

    .premium-desc {
        font-size: 1.15rem;
        color: var(--text-muted);
        max-width: 650px;
        margin: 0 auto 3rem;
        line-height: 1.7;
    }

    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 4rem;
        text-align: right;
    }
    html[dir="ltr"] .features-grid { text-align: left; }

    .feature-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: 24px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    html[data-bs-theme="dark"] .feature-card {
        border-color: rgba(255, 255, 255, 0.04);
        background: rgba(0, 0, 0, 0.15);
    }
    .feature-card:hover {
        transform: translateY(-8px);
        background: var(--bg-card);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: rgba(245, 158, 11, 0.3);
    }

    .f-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }
    .feature-card:hover .f-icon-wrapper {
        background: #f59e0b;
        color: white;
        transform: scale(1.1) rotate(5deg);
    }

    .feature-card h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text);
    }
    .feature-card p {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* Actions */
    .action-area {
        position: relative;
        display: inline-block;
    }
    .btn-upgrade-premium {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
        padding: 1.2rem 3.5rem;
        font-size: 1.15rem;
        font-weight: 800;
        border-radius: 100px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    .btn-upgrade-premium:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(245, 158, 11, 0.4);
    }
    .btn-upgrade-premium svg {
        transition: transform 0.3s ease;
    }
    .btn-upgrade-premium:hover svg {
        transform: translateX(app()->getLocale() === 'ar' ? -5px : 5px);
    }

    /* Status Badges */
    .seller-status-box {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 2rem;
        border-radius: 100px;
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .alert-rejected {
        background: rgba(239, 68, 68, 0.05);
        border: 1px dashed rgba(239, 68, 68, 0.3);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: right;
        margin-bottom: 2.5rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    html[dir="ltr"] .alert-rejected { text-align: left; }
    .alert-rejected-icon {
        color: #ef4444;
        font-size: 1.5rem;
        margin-top: 0.2rem;
    }
    .alert-rejected-content h5 {
        color: #ef4444;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    .alert-rejected-content p {
        color: var(--text-muted);
        margin: 0;
        font-size: 0.95rem;
    }

    @media(max-width: 768px) {
        .premium-seller-card { padding: 3rem 1.5rem; }
        .premium-title { font-size: 2rem; }
        .seller-hero-icon { width: 80px; height: 80px; margin-bottom: 2rem; }
        .btn-upgrade-premium { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')
<div class="seller-subscription-wrapper">
    <div class="premium-seller-card">
        
        <div class="seller-hero-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </div>

        <h1 class="premium-title">{{ app()->getLocale() === 'ar' ? 'انطلق كبائع محترف' : 'Start as a Professional Seller' }}</h1>

        @if($isSeller)
            <div class="seller-status-box status-active">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ app()->getLocale() === 'ar' ? 'أنت مشترك كبائع بالفعل' : 'You are already a seller' }}
            </div>
            <p class="premium-desc">
                {{ app()->getLocale() === 'ar' ? 'حسابك يمتلك صلاحيات البائع بالكامل. يمكنك الآن الوصول إلى لوحة تحكم البائعين لإضافة مركباتك وإدارة مزاداتك ومتابعة الأرباح.' : 'Your account has full seller privileges. You can now access the seller dashboard to add your vehicles, manage auctions, and track earnings.' }}
            </p>
            <a href="{{ route('bidder.garage.index') }}" class="btn-upgrade-premium" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                {{ app()->getLocale() === 'ar' ? 'الذهاب إلى كراج البائع' : 'Go to Seller Garage' }}
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        @elseif(isset($pendingRequest) && $pendingRequest)
            <div class="seller-status-box status-pending">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ app()->getLocale() === 'ar' ? 'طلبك قيد المراجعة' : 'Your request is under review' }}
            </div>
            <p class="premium-desc">
                {{ app()->getLocale() === 'ar' ? 'لقد استلمنا طلب الترقية الخاص بك وهو الآن قيد المراجعة من قبل الإدارة. سيتم إشعارك فور الموافقة.' : 'We have received your upgrade request and it is currently being reviewed by the administration. You will be notified upon approval.' }}
            </p>
        @else
            @if(isset($rejectedRequest) && $rejectedRequest)
                <div class="alert-rejected">
                    <div class="alert-rejected-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div class="alert-rejected-content">
                        <h5>{{ app()->getLocale() === 'ar' ? 'تم رفض طلبك السابق' : 'Your previous request was rejected' }}</h5>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'السبب:' : 'Reason:' }}</strong> {{ $rejectedRequest->admin_notes ?? (app()->getLocale() === 'ar' ? 'لم يتم توضيح سبب محدد' : 'No specific reason provided') }}</p>
                        <p class="mt-2 text-sm">{{ app()->getLocale() === 'ar' ? 'يمكنك تقديم طلب جديد الآن وتأكد من استيفاء جميع الشروط.' : 'You can submit a new request now, ensuring all conditions are met.' }}</p>
                    </div>
                </div>
            @endif
            
            <p class="premium-desc">
                {{ app()->getLocale() === 'ar' ? 'قم بترقية حسابك لتتمكن من إضافة سياراتك وعرضها في منصة موترزاد والوصول لآلاف المشترين المحتملين.' : 'Upgrade your account to add and display your cars on the Motorzad platform and reach thousands of potential buyers.' }}
            </p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="f-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div>
                        <h4>{{ app()->getLocale() === 'ar' ? 'عرض متميز للسيارات' : 'Premium Car Display' }}</h4>
                        <p>{{ app()->getLocale() === 'ar' ? 'احصل على مساحة لعرض سياراتك في قوائم مزاداتنا بصور عالية الدقة وتفاصيل شاملة تجذب المشترين.' : 'Get space to display your cars in our auction lists with high-res photos and comprehensive details.' }}</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="f-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <h4>{{ app()->getLocale() === 'ar' ? 'وصول واسع للمشترين' : 'Broad Buyer Reach' }}</h4>
                        <p>{{ app()->getLocale() === 'ar' ? 'تصل مزاداتك لآلاف المزايدين الموثقين الجاهزين للشراء والمتابعين لمنصتنا بشكل يومي.' : 'Your auctions reach thousands of verified bidders ready to buy and following our platform daily.' }}</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="f-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <div>
                        <h4>{{ app()->getLocale() === 'ar' ? 'عوائد أعلى لسيارتك' : 'Higher Returns' }}</h4>
                        <p>{{ app()->getLocale() === 'ar' ? 'نظام المزايدة التنافسي المفتوح يضمن لك الحصول على أفضل قيمة سوقية ممكنة لسيارتك بكل شفافية.' : 'Our competitive open bidding system ensures you get the best possible market value for your car.' }}</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="f-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div>
                        <h4>{{ app()->getLocale() === 'ar' ? 'معاملات آمنة وموثوقة' : 'Secure Transactions' }}</h4>
                        <p>{{ app()->getLocale() === 'ar' ? 'نضمن لك حقوقك المالية عبر نظام محفظة إلكتروني مغلق وعمليات دفع موثقة وتتبع شامل.' : 'We guarantee your financial rights through a secure electronic wallet system and documented payments.' }}</p>
                    </div>
                </div>
            </div>

            <div class="action-area">
                <form action="{{ route('bidder.become-seller.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-upgrade-premium">
                        {{ app()->getLocale() === 'ar' ? 'تقديم طلب الترقية الآن' : 'Submit Upgrade Request Now' }}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
