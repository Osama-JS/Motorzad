<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('تمت عملية الدفع بنجاح — موتورزاد') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <style>
        :root {
            --success-color: #10b981;
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --bg-body: #0b0f19;
            --bg-card: #151c28;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
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
                radial-gradient(at 50% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 60%);
        }

        .success-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-icon-wrapper {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.12);
            border: 2px solid rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .amount-highlight {
            font-size: 2.4rem;
            font-weight: 900;
            color: var(--success-color);
            margin: 0.5rem 0;
        }

        .details-table {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem;
            margin: 1.5rem 0;
            text-align: start;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.88rem;
        }

        .details-row:not(:last-child) {
            border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        }

        .btn-action {
            background: var(--success-gradient);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            width: 100%;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.5);
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="success-card">
    <!-- Success Icon -->
    <div class="success-icon-wrapper">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>

    <!-- Title & Description -->
    <h2 class="h4 fw-bold text-white mb-1">{{ __('تمت عملية الشحن بنجاح!') }}</h2>
    <p class="text-muted small mb-3">{{ __('تم إضافة الرصيد إلى محفظتك المالية ويمكنك الآن استخدامه في المزايدة فوراً.') }}</p>

    <!-- Amount Display -->
    <div class="amount-highlight">
        +{{ number_format((float) $amount, 2) }} <small style="font-size: 1.1rem; color: var(--text-muted);">ر.س</small>
    </div>

    <!-- Receipt Details Table -->
    <div class="details-table">
        <div class="details-row">
            <span class="text-muted">{{ __('حالة المعاملة:') }}</span>
            <span class="badge bg-success-subtle text-success px-2 py-1">{{ __('مكتملة ومدفوعة') }}</span>
        </div>
        <div class="details-row">
            <span class="text-muted">{{ __('الرقم المرجعي:') }}</span>
            <span class="font-monospace text-light fw-bold" dir="ltr">{{ $merchantId ?: ($transaction?->merchant_transaction_id ?? 'N/A') }}</span>
        </div>
        <div class="details-row">
            <span class="text-muted">{{ __('وسيلة الدفع:') }}</span>
            <span class="text-light fw-semibold">{{ $transaction?->brand_name ?? strtoupper($brand) }}</span>
        </div>
        @if($transaction?->card_last4)
        <div class="details-row">
            <span class="text-muted">{{ __('البطاقة:') }}</span>
            <span class="font-monospace text-light">**** {{ $transaction->card_last4 }}</span>
        </div>
        @endif
        <div class="details-row">
            <span class="text-muted">{{ __('وقت المعاملة:') }}</span>
            <span class="text-muted" dir="ltr">{{ now()->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <!-- Actions -->
    @if($source === 'app')
        <button type="button" class="btn btn-action" onclick="closeWebViewAndReturn();">
            {{ __('العودة للتطبيق') }}
        </button>
        <p class="text-muted small mt-2 mb-0">{{ __('سيتم إغلاق الصفحة والعودة للتطبيق تلقائياً...') }}</p>
    @else
        <a href="{{ route('bidder.wallet.index') }}" class="btn btn-action">
            {{ __('العودة للمحفظة') }} (<span id="countdown">5</span>)
        </a>
    @endif
</div>

<script>
    // Prepare result payload for Flutter Application WebView
    const paymentResult = {
        status: 'success',
        transaction_id: '{{ $transaction?->id ?? "" }}',
        merchant_transaction_id: '{{ $merchantId ?: ($transaction?->merchant_transaction_id ?? "") }}',
        amount: {{ (float) $amount }},
        brand: '{{ $brand }}',
        message: '{{ __("Payment completed successfully") }}'
    };

    // Broadcast result through JavaScript Channel to Flutter WebView
    function notifyFlutterApp() {
        // Standard Flutter JavaScript Channel
        if (window.FlutterBridge && typeof window.FlutterBridge.postMessage === 'function') {
            window.FlutterBridge.postMessage(JSON.stringify(paymentResult));
        }
        // iOS WebKit message handler fallback
        if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.FlutterBridge) {
            window.webkit.messageHandlers.FlutterBridge.postMessage(JSON.stringify(paymentResult));
        }
    }

    function closeWebViewAndReturn() {
        notifyFlutterApp();
        // Fallback: try window.close if possible
        try { window.close(); } catch(e) {}
    }

    // Automatically notify Flutter immediately upon load
    window.addEventListener('DOMContentLoaded', function() {
        notifyFlutterApp();

        @if($source === 'web')
            // Auto-redirect web users back to wallet after 5 seconds
            let seconds = 5;
            const cdEl = document.getElementById('countdown');
            const timer = setInterval(function() {
                seconds--;
                if (cdEl) cdEl.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = "{{ route('bidder.wallet.index') }}";
                }
            }, 1000);
        @endif
    });
</script>

</body>
</html>
