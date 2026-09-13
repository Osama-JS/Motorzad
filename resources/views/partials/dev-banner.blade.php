@php
    $devModeEnabled = \App\Models\Setting::get('development_mode') == '1';
    $devModeMsg = \App\Models\Setting::get('development_mode_message');
    if (empty($devModeMsg)) {
        $devModeMsg = __('تنبيه: المنصة حالياً في وضع التطوير والتحسين المستمر. قد تكون بعض البيانات والميزات تجريبية.');
    }
@endphp

@if($devModeEnabled)
<style>
    /* Development Mode Top Ticker Banner */
    .motorzad-dev-banner {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 38px;
        z-index: 999999;
        background: linear-gradient(90deg, #18181b 0%, #27272a 20%, #450a0a 50%, #27272a 80%, #18181b 100%);
        border-bottom: 2px solid #ef4444;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.45);
        display: flex;
        align-items: center;
        overflow: hidden;
        font-family: 'Tajawal', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #ffffff;
        direction: rtl;
        user-select: none;
    }

    body.has-dev-banner {
        padding-top: 38px !important;
    }

    body.has-dev-banner .navbar {
        top: 38px !important;
    }

    body.has-dev-banner .sidebar {
        top: 38px !important;
        height: calc(100vh - 38px) !important;
    }

    body.has-dev-banner .sidebar-overlay {
        top: 38px !important;
    }

    /* Warning Badge */
    .dev-banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #ffffff;
        padding: 0 1rem;
        height: 100%;
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        white-space: nowrap;
        flex-shrink: 0;
        z-index: 3;
        box-shadow: -3px 0 10px rgba(0,0,0,0.3);
    }

    .dev-pulse-dot {
        width: 7px;
        height: 7px;
        background-color: #ffffff;
        border-radius: 50%;
        display: inline-block;
        animation: devDotPulse 1.4s infinite ease-in-out;
    }

    @keyframes devDotPulse {
        0% { transform: scale(0.85); opacity: 0.8; box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
        70% { transform: scale(1.15); opacity: 1; box-shadow: 0 0 0 5px rgba(255, 255, 255, 0); }
        100% { transform: scale(0.85); opacity: 0.8; box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }

    /* Ticker Container & Marquee Track */
    .dev-marquee-container {
        flex: 1;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        height: 100%;
        mask-image: linear-gradient(to right, transparent, black 25px, black calc(100% - 25px), transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 25px, black calc(100% - 25px), transparent);
    }

    .dev-marquee-track {
        display: inline-flex;
        white-space: nowrap;
        animation: devTickerMarquee 35s linear infinite;
        cursor: default;
    }

    .dev-marquee-track:hover {
        animation-play-state: paused;
    }

    .dev-marquee-block {
        display: inline-flex;
        align-items: center;
        gap: 2rem;
        padding-left: 2rem;
        font-size: 0.86rem;
        font-weight: 600;
        color: #fef08a;
    }

    .dev-star-sep {
        color: #f59e0b;
        font-size: 0.8rem;
        opacity: 0.85;
    }

    @keyframes devTickerMarquee {
        0% {
            transform: translateX(0%);
        }
        100% {
            transform: translateX(50%);
        }
    }

    [dir="ltr"] .dev-marquee-track {
        animation: devTickerMarqueeLtr 35s linear infinite;
    }

    @keyframes devTickerMarqueeLtr {
        0% {
            transform: translateX(0%);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .motorzad-dev-banner {
            height: 34px;
        }
        body.has-dev-banner {
            padding-top: 34px !important;
        }
        body.has-dev-banner .navbar {
            top: 34px !important;
        }
        body.has-dev-banner .sidebar {
            top: 34px !important;
            height: calc(100vh - 34px) !important;
        }
        .dev-banner-badge {
            font-size: 0.72rem;
            padding: 0 0.6rem;
            gap: 0.3rem;
        }
        .dev-marquee-block {
            font-size: 0.78rem;
            gap: 1.2rem;
            padding-left: 1.2rem;
        }
    }
</style>

<div class="motorzad-dev-banner" id="motorzadDevBanner" role="alert" aria-live="polite">
    <div class="dev-banner-badge">
        <span class="dev-pulse-dot"></span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
        <span>{{ __('وضع التطوير') }}</span>
    </div>
    <div class="dev-marquee-container">
        <div class="dev-marquee-track">
            <div class="dev-marquee-block">
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
            </div>
            <div class="dev-marquee-block" aria-hidden="true">
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
                <span>{{ $devModeMsg }}</span>
                <span class="dev-star-sep">✦</span>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        if (document.body) {
            document.body.classList.add('has-dev-banner');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('has-dev-banner');
            });
        }
    })();
</script>
@endif
