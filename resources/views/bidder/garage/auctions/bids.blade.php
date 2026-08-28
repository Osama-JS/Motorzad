@extends('layouts.bidder')

@section('title', __('مزايدات المزاد') . ' - ' . $auction->title_ar)

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
    /* ==========================================================================
       ADAPTIVE LUXURY THEME - BIDS HISTORY (GARAGE MATCHED)
       ========================================================================== */
    :root {
        --font-primary: 'Tajawal', 'Outfit', sans-serif;
        --primary-glow: rgba(var(--bs-primary-rgb, 99, 102, 241), 0.25);
        --radius-xl: 24px;
        --radius-lg: 20px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }
    
    body {
        font-family: var(--font-primary);
        background-color: #f8fafc;
    }
    
    /* Page Header - Hero Card */
    .garage-hero-card {
        background: linear-gradient(135deg, white 0%, var(--primary-glow) 100%);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: var(--radius-lg);
        padding: 2.5rem 3rem;
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
        background: white;
        border: 2px solid rgba(0,0,0,0.05);
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
        color: #1e293b;
        margin: 0 0 5px 0;
        letter-spacing: -0.5px;
    }
    
    .hero-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
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

    .header-badge {
        background: white;
        border: 1px solid rgba(0,0,0,0.1);
        color: #64748b;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    
    /* Modern Cards */
    .glass-card {
        background: white;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .section-title i {
        color: var(--primary);
        background: var(--primary-glow);
        padding: 0.5rem;
        border-radius: 10px;
    }
    
    /* Bids Table */
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    
    .custom-table th {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        padding: 1rem;
        border: none;
    }
    
    .custom-table td {
        background: white;
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .custom-table tr td:first-child {
        border-left: 1px solid #f1f5f9;
        border-top-right-radius: var(--radius-md);
        border-bottom-right-radius: var(--radius-md);
    }
    
    .custom-table tr td:last-child {
        border-right: 1px solid #f1f5f9;
        border-top-left-radius: var(--radius-md);
        border-bottom-left-radius: var(--radius-md);
    }
    
    .custom-table tbody tr {
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }
    
    .custom-table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .bidder-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), #4338ca);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        margin-left: 15px;
        box-shadow: 0 4px 10px var(--primary-glow);
    }
    /* Luxury Modal */
    .luxury-modal .modal-content {
        border-radius: 24px;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    
    .luxury-modal .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }
    
    .luxury-modal .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    
    .luxury-modal .icon-box.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }
    
    .luxury-modal .icon-box.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
    }
    
    .luxury-modal .modal-title {
        font-weight: 800;
        font-size: 1.4rem;
        letter-spacing: -0.5px;
    }
    
    .luxury-modal .modal-body {
        padding: 2.5rem 2rem;
        font-size: 1.15rem;
        line-height: 1.7;
    }
    
    .luxury-modal .modal-footer {
        padding: 1.5rem 2rem;
        background: #f8fafc;
        border-top: 1px solid rgba(0,0,0,0.03);
    }
    
    .luxury-modal .btn {
        padding: 0.85rem 1.75rem;
        font-weight: 800;
        border-radius: 14px;
        transition: all 0.3s ease;
        font-size: 1.05rem;
    }
    
    .luxury-modal .btn:hover {
        transform: translateY(-3px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid fade-in">
    <!-- Hero Header (Matched with Garage Index) -->
    <div class="garage-hero-card">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ __('المزايدات على:') }} {{ $auction->title_ar }}</h1>
                <p class="hero-subtitle mt-2">
                    <span class="header-badge bg-white shadow-sm">
                        <i class="fa-solid fa-gavel text-primary me-1"></i> {{ __('السعر المبدئي:') }} {{ number_format($auction->start_price) }} ر.س
                    </span>
                    <span class="header-badge bg-white border-success text-success shadow-sm">
                        <i class="fa-solid fa-arrow-trend-up me-1"></i> {{ __('أعلى مزايدة:') }} {{ $auction->highestBid ? number_format($auction->highestBid->amount) : 0 }} ر.س
                    </span>
                </p>
            </div>
        </div>
        <a href="{{ route('bidder.garage.auctions.show', $auction->id) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold bg-white shadow-sm">
            <i class="fa-solid fa-arrow-right me-2"></i> {{ __('عودة للمزاد') }}
        </a>
        <div class="hero-decoration">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h4 class="m-0 fw-bold text-dark">
                        <i class="fa-solid fa-list-ol text-primary me-2"></i> {{ __('قائمة المزايدات الشاملة') }} 
                        <span class="badge bg-primary ms-2 rounded-pill">{{ $bids->total() }}</span>
                    </h4>
                </div>
                
                @if($bids->count() > 0)
                <div class="table-responsive" style="min-height: 400px;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>{{ __('المزايد') }}</th>
                                <th>{{ __('مبلغ المزايدة') }}</th>
                                <th>{{ __('وقت المزايدة') }}</th>
                                <th>{{ __('حالة المزايدة') }}</th>
                                <th>{{ __('الإجراء') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bids as $bid)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bidder-avatar">
                                            {{ mb_substr($bid->user->first_name ?? $bid->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $bid->user->first_name }} {{ $bid->user->last_name }}</div>
                                            <div class="small text-muted">@<!-- -->{{ $bid->user->username ?? 'user'.$bid->user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold fs-4 {{ $loop->first && $bids->currentPage() == 1 ? 'text-success' : 'text-dark' }}">
                                        {{ number_format($bid->amount) }} <small class="fs-6 text-muted">ر.س</small>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $bid->created_at->format('Y-m-d h:i A') }}</div>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> {{ $bid->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($loop->first && $bids->currentPage() == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-trophy me-1"></i> المتصدر الحالي
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i> تم تجاوزها
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($auction->status === 'live')
                                        <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#acceptBidModal{{ $bid->id }}">
                                            <i class="fa-solid fa-check me-1"></i> {{ __('قبول مبكر') }}
                                        </button>


                                    @else
                                        <span class="text-muted"><i class="fa-solid fa-minus"></i></span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4 pt-3 border-top">
                    {{ $bids->links() }}
                </div>
                @else
                <div class="text-center py-5 my-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 120px; height: 120px;">
                        <i class="fa-solid fa-comment-slash text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    </div>
                    <h4 class="text-dark fw-bold">{{ __('لا توجد أي مزايدات على هذا المزاد حتى الآن!') }}</h4>
                    <p class="text-muted fs-5">{{ __('بمجرد أن يقوم المشترون بتقديم عروضهم، ستظهر جميع التفاصيل هنا.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@if($auction->status === 'live')
    <!-- Accept Bid Modals -->
    @foreach($bids as $bid)
    <div class="modal fade luxury-modal text-start" id="acceptBidModal{{ $bid->id }}" tabindex="-1" aria-labelledby="acceptBidModalLabel{{ $bid->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <div class="d-flex align-items-center gap-4">
                        <div class="icon-box success">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <h5 class="modal-title text-success mb-0" id="acceptBidModalLabel{{ $bid->id }}">{{ __('تأكيد قبول المزايدة') }}</h5>
                    </div>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h4 class="fw-bold text-dark mb-3">{{ __('هل أنت متأكد من قبول المزايدة بقيمة') }} <span class="text-success">{{ number_format($bid->amount) }} ر.س</span>؟</h4>
                    <p class="text-muted mb-0 fs-6">{{ __('قبول هذه المزايدة سيؤدي إلى إنهاء المزاد فوراً وإعلانه كمزاد "مباع" لصالح') }} <span class="fw-bold text-dark">{{ $bid->user->first_name }} {{ $bid->user->last_name }}</span>.</p>
                </div>
                <div class="modal-footer justify-content-center gap-3">
                    <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">{{ __('تراجع وإلغاء') }}</button>
                    <form action="{{ route('bidder.garage.auctions.accept-bid', ['id' => $auction->id, 'bidId' => $bid->id]) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success shadow-lg">
                            {{ __('تأكيد القبول') }} <i class="fa-solid fa-check ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نقل المودال إلى عنصر body لحل مشكلة التداخل (z-index) مع الحاويات
        $('.luxury-modal').appendTo('body');
    });
</script>
@endsection
