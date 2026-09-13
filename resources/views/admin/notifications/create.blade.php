@extends('layouts.admin')

@section('title', __('مركز الإشعارات والبث الذكي'))

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --notif-card-bg: rgba(255, 255, 255, 0.9);
        --notif-border: rgba(226, 232, 240, 0.8);
    }
    [data-theme="dark"] {
        --notif-card-bg: rgba(30, 41, 59, 0.9);
        --notif-border: rgba(51, 65, 85, 0.8);
    }

    .broadcast-card {
        background: var(--notif-card-bg);
        border: 1px solid var(--notif-border);
        border-radius: 20px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(10px);
        overflow: hidden;
    }

    .broadcast-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid var(--notif-border);
        background: rgba(248, 250, 252, 0.5);
    }
    [data-theme="dark"] .broadcast-header {
        background: rgba(15, 23, 42, 0.4);
    }

    .audience-option-card {
        border: 2px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        background: var(--bg-card, #ffffff);
        display: flex;
        align-items: center;
        gap: 0.85rem;
        position: relative;
    }
    .audience-option-card:hover {
        border-color: var(--brand-red, #e53e3e);
        transform: translateY(-2px);
    }
    .audience-option-card.selected {
        border-color: var(--brand-red, #e53e3e);
        background: rgba(229, 62, 62, 0.04);
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.12);
    }
    .audience-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Channel Badges */
    .channel-check-box {
        display: none;
    }
    .channel-pill {
        border: 2px solid var(--border, #e2e8f0);
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-weight: 600;
        user-select: none;
    }
    .channel-check-box:checked + .channel-pill {
        border-color: var(--primary, #3b82f6);
        background: rgba(59, 130, 246, 0.08);
        color: var(--primary, #3b82f6);
    }

    /* Quick Templates Pills */
    .template-badge {
        cursor: pointer;
        background: rgba(99, 102, 241, 0.08);
        color: #4f46e5;
        border: 1px dashed rgba(99, 102, 241, 0.3);
        border-radius: 30px;
        padding: 5px 14px;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .template-badge:hover {
        background: rgba(99, 102, 241, 0.18);
        transform: scale(1.03);
    }

    /* Live Preview Mobile Frame */
    .preview-phone-mockup {
        background: #1e293b;
        border-radius: 36px;
        padding: 16px 12px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
        max-width: 320px;
        margin: 0 auto;
        border: 4px solid #334155;
    }
    .phone-screen {
        background: #f1f5f9;
        border-radius: 26px;
        min-height: 420px;
        overflow: hidden;
        position: relative;
        padding: 12px 10px;
    }
    [data-theme="dark"] .phone-screen {
        background: #0f172a;
    }
    .phone-notch {
        width: 100px;
        height: 16px;
        background: #1e293b;
        border-radius: 0 0 12px 12px;
        margin: -12px auto 14px;
    }
    .preview-push-notification {
        background: #ffffff;
        border-radius: 14px;
        padding: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-left: 4px solid var(--brand-red, #e53e3e);
        animation: pulseSubtle 3s infinite;
    }
    [data-theme="dark"] .preview-push-notification {
        background: #1e293b;
        color: #f8fafc;
    }
    @keyframes pulseSubtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-2px); }
    }
</style>
@endsection

@section('content')
<x-admin-header :title="__('مركز الإشعارات والبث الذكي')" :breadcrumb="__('إرسال إشعار')">
    <span class="badge bg-primary px-3 py-2 rounded-pill font-weight-bold">
        <i class="fa-solid fa-users me-1"></i> إجمالي المسجلين: {{ number_format($totalUsers) }}
    </span>
</x-admin-header>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5 align-middle"></i>
        <strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5 align-middle"></i>
        <strong>{{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Form Column --}}
    <div class="col-xl-8">
        <div class="broadcast-card">
            <div class="broadcast-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 42px; height: 42px; background: rgba(229, 62, 62, 0.1); color: var(--brand-red); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold fs-5">{{ __('صياغة وبث إشعار فوري') }}</h4>
                        <small class="text-muted">{{ __('إرسال تنبيهات مخصصة إلى مستخدمي المنصة وتطبيق الجوال') }}</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- Quick Templates --}}
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                        <i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i> {{ __('قوالب جاهزة سريعة:') }}
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="template-badge" onclick="applyTemplate('auction_launch')">
                            <i class="fa-solid fa-gavel"></i> انطلاق مزاد مميز
                        </span>
                        <span class="template-badge" onclick="applyTemplate('wallet_deposit')">
                            <i class="fa-solid fa-wallet"></i> تذكير بشحن المحفظة
                        </span>
                        <span class="template-badge" onclick="applyTemplate('system_maintenance')">
                            <i class="fa-solid fa-wrench"></i> صيانة وتحديث للنظام
                        </span>
                        <span class="template-badge" onclick="applyTemplate('seller_welcome')">
                            <i class="fa-solid fa-id-card"></i> ترحيب بالتجار الجدد
                        </span>
                    </div>
                </div>

                <form id="send-notification-form" action="{{ route('admin.notifications.send') }}" method="POST">
                    @csrf

                    {{-- 1. الجمهور المستهدف --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-3">
                            <span class="badge bg-danger rounded-pill me-1">1</span> {{ __('حدد الجمهور المستهدف') }} <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="target_audience" id="target_audience_input" value="all">

                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <div class="audience-option-card selected" onclick="selectAudience('all', this)">
                                    <div class="audience-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="fa-solid fa-globe"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('الكل') }}</div>
                                        <small class="text-muted">{{ $totalUsers }} مستخدم</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="audience-option-card" onclick="selectAudience('bidders', this)">
                                    <div class="audience-icon bg-success bg-opacity-10 text-success">
                                        <i class="fa-solid fa-gavel"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('المزايدين') }}</div>
                                        <small class="text-muted">{{ $totalBidders }} مزايد</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="audience-option-card" onclick="selectAudience('sellers', this)">
                                    <div class="audience-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('البائعين والتجار') }}</div>
                                        <small class="text-muted">{{ $totalSellers }} بائع</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="audience-option-card" onclick="selectAudience('specific', this)">
                                    <div class="audience-icon bg-info bg-opacity-10 text-info">
                                        <i class="fa-solid fa-user-tag"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('مستخدم محدد') }}</div>
                                        <small class="text-muted">بالرقم التعريفي</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Specific User Input Field with Searchable Select2 --}}
                        <div class="mt-3 p-3 rounded-4 border bg-white shadow-sm" id="specific_user_box" style="display: none;">
                            <label class="form-label fw-bold mb-2">
                                <i class="fa-solid fa-user-check text-primary me-1"></i> {{ __('اختر المستخدم المستهدف (ابحث بالاسم، البريد أو الهاتف)') }}
                            </label>
                            <select name="specific_user_id" id="specific_user_id" class="form-select select2-user-search">
                                <option value="">{{ __('--- اختر المستخدم من القائمة أو ابحث ---') }}</option>
                                @foreach($usersList as $userItem)
                                    <option value="{{ $userItem->id }}" data-phone="{{ $userItem->phone }}" data-email="{{ $userItem->email }}">
                                        {{ $userItem->name }} ({{ $userItem->email }}) {{ $userItem->phone ? ' - ' . $userItem->phone : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">
                                <i class="fa-solid fa-circle-info me-1"></i> {{ __('يمكنك كتابة اسم المستخدم أو بريده أو هاتفه للوصول إليه بسرعة.') }}
                            </small>
                        </div>
                    </div>

                    {{-- 2. قنوات الإرسال --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-3">
                            <span class="badge bg-danger rounded-pill me-1">2</span> {{ __('قنوات التسليم (Delivery Channels)') }} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-wrap gap-3">
                            <label>
                                <input type="checkbox" name="channels[]" value="database" class="channel-check-box" checked>
                                <div class="channel-pill">
                                    <i class="fa-solid fa-bell"></i>
                                    <span>إشعار داخلي (المنصة)</span>
                                </div>
                            </label>

                            <label>
                                <input type="checkbox" name="channels[]" value="fcm" class="channel-check-box" checked>
                                <div class="channel-pill">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                    <span>تطبيق الجوال (Push FCM)</span>
                                </div>
                            </label>

                            <label>
                                <input type="checkbox" name="channels[]" value="mail" class="channel-check-box">
                                <div class="channel-pill">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>بريد إلكتروني (Email)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 3. تفاصيل ومحتوى الإشعار --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-3">
                            <span class="badge bg-danger rounded-pill me-1">3</span> {{ __('محتوى وتفاصيل الرسالة') }}
                        </label>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('عنوان الإشعار') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-heading"></i></span>
                                <input type="text" name="title" id="notif_title" class="form-control form-control-lg" required placeholder="مثال: تم إطلاق مزاد تويوتا لاندكروزر 2024 الآن!" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('نص الرسالة') }} <span class="text-danger">*</span></label>
                            <textarea name="message" id="notif_message" class="form-control" rows="4" required placeholder="اكتب تفاصيل التنبيه الموجه للعملاء بوضوح واحترافية..." oninput="updateLivePreview()"></textarea>
                            <div class="text-end text-muted small mt-1">
                                <span id="charCount">0</span> حرف
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">{{ __('رابط التوجيه عند النقر (اختياري)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-link"></i></span>
                                <input type="url" name="action_url" id="notif_url" class="form-control" placeholder="https://motorzad.com/auctions/5" oninput="updateLivePreview()">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <button type="reset" class="btn btn-light px-4" onclick="setTimeout(updateLivePreview, 100)">
                            <i class="fa-solid fa-arrow-rotate-left me-1"></i> {{ __('إعادة تعيين') }}
                        </button>

                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm fw-bold">
                            <i class="fa-solid fa-paper-plane me-1"></i> {{ __('إرسال وبث الإشعار الآن') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Live Preview Column --}}
    <div class="col-xl-4">
        <div class="broadcast-card mb-4">
            <div class="broadcast-header">
                <h5 class="mb-0 fw-bold fs-6">
                    <i class="fa-solid fa-eye text-primary me-2"></i> {{ __('المعاينة الحية (Live Mobile Preview)') }}
                </h5>
            </div>
            <div class="card-body p-4 text-center">
                <div class="preview-phone-mockup">
                    <div class="phone-screen text-start">
                        <div class="phone-notch"></div>
                        
                        <div class="d-flex justify-content-between align-items-center text-muted small mb-3 px-2">
                            <span>09:41</span>
                            <div>
                                <i class="fa-solid fa-signal me-1"></i>
                                <i class="fa-solid fa-wifi me-1"></i>
                                <i class="fa-solid fa-battery-full"></i>
                            </div>
                        </div>

                        {{-- Push Notification Mockup --}}
                        <div class="preview-push-notification">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 22px; height: 22px; background: #e53e3e; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 10px; font-weight: 900;">
                                        M
                                    </div>
                                    <span class="fw-bold" style="font-size: 0.8rem;">MOTORZAD</span>
                                </div>
                                <span class="text-muted" style="font-size: 0.7rem;">الآن</span>
                            </div>

                            <h6 class="fw-bold mb-1 text-truncate" id="preview_title" style="font-size: 0.9rem;">
                                عنوان الإشعار التجريبي
                            </h6>
                            <p class="text-muted small mb-2" id="preview_body" style="font-size: 0.8rem; line-height: 1.4;">
                                سيظهر نص الرسالة وتفاصيل الإشعار هنا مباشرة أثناء الكتابة...
                            </p>
                            <div id="preview_link_badge" style="display: none;">
                                <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> رابط مرفق
                                </span>
                            </div>
                        </div>

                        {{-- In-app banner preview --}}
                        <div class="mt-4 p-3 rounded-3 border bg-white shadow-sm">
                            <div class="d-flex gap-2">
                                <i class="fa-solid fa-bell text-danger mt-1"></i>
                                <div>
                                    <div class="fw-bold small" id="preview_inapp_title">إشعار المنصة الداخلي</div>
                                    <div class="text-muted" style="font-size: 0.75rem;" id="preview_inapp_body">معاينة التنبيه في شريط الإشعارات العلوي للموقع.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Broadcasts Activity Card --}}
        <div class="broadcast-card">
            <div class="broadcast-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold fs-6">
                    <i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i> {{ __('آخر الإشعارات المرسلة') }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentNotifications as $item)
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark small text-truncate" style="max-width: 190px;">{{ $item->title }}</span>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $item->created_at }}</small>
                            </div>
                            <p class="text-muted small mb-2 text-truncate">{{ $item->message }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-user me-1"></i> {{ $item->recipient_name }}
                                </span>
                                @if($item->action_url)
                                    <a href="{{ $item->action_url }}" target="_blank" class="text-primary text-decoration-none small" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-link"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            {{ __('لا توجد إشعارات مرسلة مؤخراً.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Audience Selector Toggle
    function selectAudience(audience, cardEl) {
        document.querySelectorAll('.audience-option-card').forEach(el => el.classList.remove('selected'));
        cardEl.classList.add('selected');
        document.getElementById('target_audience_input').value = audience;

        const specificBox = document.getElementById('specific_user_box');
        if (audience === 'specific') {
            specificBox.style.display = 'block';
            document.getElementById('specific_user_id').focus();
        } else {
            specificBox.style.display = 'none';
        }
    }

    // Quick Templates
    const templates = {
        auction_launch: {
            title: '🔥 مزاد حصري جديد انطلق الآن!',
            message: 'ندعوكم للمشاركة في المزايدة على سيارة مرسيدس G-Class موديل 2024 بحالة الوكالة. يبدأ المزاد الآن وحتى 48 ساعة.',
            url: window.location.origin + '/auctions'
        },
        wallet_deposit: {
            title: '💳 جهّز محفظتك للمزايدة القادمة',
            message: 'احرص على شحن رصيد محفظتك عبر مدى أو البطاقات الائتمانية لتتمكن من تقديم عروضك في المزادات بدون تأخير.',
            url: window.location.origin + '/bidder/wallet'
        },
        system_maintenance: {
            title: '⚙️ ترقية وتحسين أداء المنصة',
            message: 'نود إحاطتكم بأنه سيتم إجراء صيانة دورية سريعة لتحسين سرعة البث والمزايدات المباشرة خلال منتصف الليل.',
            url: ''
        },
        seller_welcome: {
            title: '🌟 مرحباً بك كبائع معتمد في موتورزاد',
            message: 'تم تفعيل حسابك التجاري بنجاح. يمكنك الآن رفع سياراتك والبدء في جدولة أول مزاد خاص بك مباشرة.',
            url: window.location.origin + '/seller/vehicles'
        }
    };

    function applyTemplate(key) {
        if (!templates[key]) return;
        document.getElementById('notif_title').value = templates[key].title;
        document.getElementById('notif_message').value = templates[key].message;
        document.getElementById('notif_url').value = templates[key].url;
        updateLivePreview();
        toastr.info('تم تطبيق بيانات القالب بنجاح');
    }

    // Live Preview Synchronization
    function updateLivePreview() {
        const titleVal = document.getElementById('notif_title').value.trim();
        const msgVal = document.getElementById('notif_message').value.trim();
        const urlVal = document.getElementById('notif_url').value.trim();

        // Title
        const defaultTitle = 'عنوان الإشعار التجريبي';
        document.getElementById('preview_title').innerText = titleVal || defaultTitle;
        document.getElementById('preview_inapp_title').innerText = titleVal || 'إشعار المنصة الداخلي';

        // Message
        const defaultMsg = 'سيظهر نص الرسالة وتفاصيل الإشعار هنا مباشرة أثناء الكتابة...';
        document.getElementById('preview_body').innerText = msgVal || defaultMsg;
        document.getElementById('preview_inapp_body').innerText = msgVal || 'معاينة التنبيه في شريط الإشعارات العلوي للموقع.';

        // Character count
        document.getElementById('charCount').innerText = msgVal.length;

        // URL Link Badge
        document.getElementById('preview_link_badge').style.display = urlVal ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();

        // AJAX Form Submission
        const form = document.getElementById('send-notification-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري بث الإشعارات...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    toastr.success(data.message || 'تم إرسال الإشعار بنجاح لجميع المستهدفين.');
                    form.reset();
                    selectAudience('all', document.querySelector('.audience-option-card'));
                    updateLivePreview();
                } else {
                    toastr.error(data.message || 'حدث خطأ أثناء الإرسال.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('حدث خطأ غير متوقع أثناء الاتصال بالخادم.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
    });

    $(document).ready(function() {
        // Initialize Searchable Select2 for Users
        let dir = $('html').attr('dir') || 'rtl';
        $('.select2-user-search').select2({
            dir: dir,
            width: '100%',
            placeholder: "{{ __('ابحث واختر المستخدم...') }}",
            allowClear: true
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endsection
