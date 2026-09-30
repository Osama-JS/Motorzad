<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('بوابة الدفع الإلكتروني الآمن — موتورزاد') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <style>
        :root {
            --primary: #e53e3e;
            --primary-hover: #c53030;
            --bg-body: #0b0f19;
            --bg-card: #151c28;
            --bg-card-subtle: #1c2638;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-green: #10b981;
        }

        body {
            font-family: 'Tajawal', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            margin: 0;
            background-image: 
                radial-gradient(at 0% 0%, rgba(229, 62, 62, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.06) 0px, transparent 50%);
        }

        .payment-container {
            width: 100%;
            max-width: 580px;
        }

        .payment-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .payment-header {
            padding: 1.75rem 2rem 1.25rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02) 0%, transparent 100%);
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .summary-box {
            background: var(--bg-card-subtle);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin: 1.5rem 2rem 0.5rem;
            border: 1px solid var(--border-color);
        }

        .amount-display {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .currency-tag {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-inline-start: 0.35rem;
        }

        .widget-area {
            padding: 1rem 2rem 2rem;
        }

        /* HyperPay Payment Widget Styles Override */
        .wpwl-container {
            max-width: 100% !important;
            margin: 0 auto !important;
            direction: ltr !important;
        }
        .wpwl-form {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
        .wpwl-label {
            color: #cbd5e1 !important;
            font-family: 'Tajawal', sans-serif !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.4rem !important;
        }
        .wpwl-control {
            background: #0f1523 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 10px !important;
            color: #f8fafc !important;
            height: 48px !important;
            font-size: 1rem !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease !important;
        }
        .wpwl-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.2) !important;
            outline: none !important;
        }
        .wpwl-button-pay {
            background: linear-gradient(135deg, #e53e3e 0%, #b91c1c 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            font-family: 'Tajawal', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
            height: 52px !important;
            margin-top: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(229, 62, 62, 0.4) !important;
            transition: all 0.25s ease !important;
            width: 100% !important;
        }
        .wpwl-button-pay:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 15px 30px -5px rgba(229, 62, 62, 0.5) !important;
        }
        .wpwl-hint {
            color: #64748b !important;
            font-size: 0.78rem !important;
        }
        .wpwl-has-error .wpwl-control {
            border-color: #ef4444 !important;
        }

        .trust-footer {
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.25);
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .countdown-timer {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
            color: #fbbf24;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <div class="payment-card">
        
        <!-- Header -->
        <div class="payment-header text-center">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="brand-badge">
                    <span>🛡️</span>
                    <span>{{ __('معاملة مشفرة 256-bit') }}</span>
                </div>
                <div class="brand-badge">
                    <span>⏳</span>
                    <span>{{ __('تنتهي الجلسة:') }} <span id="timer" class="countdown-timer">15:00</span></span>
                </div>
            </div>

            <h1 class="h5 fw-bold mb-1 text-white">{{ __('بوابة الدفع الإلكتروني') }}</h1>
            <p class="text-muted small mb-0">{{ __('شحن الرصيد الفوري لمحفظة موتورزاد') }}</p>
        </div>

        <!-- Order Summary Box -->
        <div class="summary-box">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">{{ __('رقم العملية المرجعي:') }}</span>
                <span class="font-monospace text-light small fw-bold" dir="ltr">{{ $transaction->merchant_transaction_id }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">{{ __('وسيلة الدفع المختارة:') }}</span>
                <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5" style="border-radius: 6px; font-size: 0.8rem;">
                    {{ $transaction->brand_name }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: var(--border-color) !important;">
                <span class="fw-bold text-white">{{ __('إجمالي المبلغ المستحق:') }}</span>
                <div>
                    <span class="amount-display">{{ number_format($transaction->amount, 2) }}</span>
                    <span class="currency-tag">ر.س</span>
                </div>
            </div>
        </div>

        <!-- HyperPay Official Widget Form -->
        <div class="widget-area">
            <form action="{{ $returnUrl }}" class="paymentWidgets" data-brands="{{ $widgetBrands }}"></form>
        </div>

        <!-- Trust Badges Footer -->
        <div class="trust-footer">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span class="fw-semibold text-light">{{ __('الدفع محمي ببروتوكول التحقق الثنائي (3D Secure)') }}</span>
            </div>
            <span>{{ __('متوافق بالكامل مع معايير الأمان المصرفية العالمية PCI-DSS') }}</span>
        </div>

    </div>

    @if($source === 'web' && auth()->check())
    <div class="text-center mt-3">
        <a href="{{ route('bidder.wallet.index') }}" class="text-muted text-decoration-none small">
            ← {{ __('إلغاء والعودة إلى محفظتي') }}
        </a>
    </div>
    @endif
</div>

<!-- HyperPay Widget Script -->
<script src="{{ $scriptUrl }}"></script>

<!-- Countdown Timer Script -->
<script>
    (function() {
        let totalSeconds = 15 * 60; // 15 minutes
        const timerEl = document.getElementById('timer');
        
        const interval = setInterval(function() {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(interval);
                timerEl.innerText = "00:00";
                alert("{{ __('انتهت مهلة جلسة الدفع. يرجى إعادة المحاولة.') }}");
                window.location.reload();
                return;
            }
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            timerEl.innerText = (mins < 10 ? "0" : "") + mins + ":" + (secs < 10 ? "0" : "") + secs;
        }, 1000);
    })();
</script>

</body>
</html>
