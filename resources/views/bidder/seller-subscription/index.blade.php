@extends('layouts.bidder')

@section('title', __('الاشتراك كبائع'))

@section('css')
<style>
    .seller-subscription-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .premium-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary), var(--brand-gold));
    }

    .premium-icon {
        width: 80px;
        height: 80px;
        background: var(--primary-glow);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
    }

    .premium-icon svg {
        width: 40px;
        height: 40px;
    }

    .premium-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }

    .premium-desc {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }

    .features-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        text-align: right;
        margin-bottom: 3rem;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--bg-body);
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .feature-icon {
        color: var(--accent-color);
        flex-shrink: 0;
    }

    .feature-content h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .feature-content p {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-secondary);
    }

    .btn-upgrade {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        border: none;
        padding: 1rem 3rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px var(--primary-glow);
    }

    .btn-upgrade:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--primary-glow);
        color: white;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        background: rgba(16, 185, 129, 0.1);
        color: #10B981;
        border-radius: 50px;
        font-weight: 600;
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    [dir="ltr"] .features-list {
        text-align: left;
    }
</style>
@endsection

@section('content')
<div class="seller-subscription-container">
    <div class="premium-card fade-in">
        <div class="premium-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </div>

        <h1 class="premium-title">{{ __('الاشتراك كبائع') }}</h1>

        @if($isSeller)
            <div class="status-badge">
                <i class="fas fa-check-circle me-2"></i>
                {{ __('أنت مشترك كبائع بالفعل') }}
            </div>
            <p class="premium-desc">
                {{ __('حسابك ممتلك لصلاحيات البائع. قريباً سيتم إطلاق لوحة تحكم مخصصة للبائعين لإدارة مزاداتك وسياراتك بكل سهولة.') }}
            </p>
        @else
            <p class="premium-desc">
                {{ __('قم بترقية حسابك لتتمكن من إضافة سياراتك وعرضها في منصة موترزاد والوصول لآلاف المشترين المحتملين.') }}
            </p>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div class="feature-content">
                        <h4>{{ __('عرض متميز للسيارات') }}</h4>
                        <p>{{ __('احصل على فرصة لعرض سياراتك في قوائم مزاداتنا مع صور عالية الدقة وتفاصيل شاملة.') }}</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="feature-content">
                        <h4>{{ __('الوصول للمشترين') }}</h4>
                        <p>{{ __('تصل مزاداتك لآلاف المزايدين الموثقين والمهتمين بشراء السيارات المتميزة.') }}</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <div class="feature-content">
                        <h4>{{ __('عوائد أعلى') }}</h4>
                        <p>{{ __('نظام المزايدة التنافسي يضمن لك الحصول على أفضل قيمة سوقية لسيارتك.') }}</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="feature-content">
                        <h4>{{ __('معاملات آمنة') }}</h4>
                        <p>{{ __('نضمن لك حقوقك المالية عبر نظام محفظة إلكتروني آمن وموثوق.') }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('bidder.become-seller.store') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-upgrade">
                    {{ __('ترقية الحساب الآن') }}
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
