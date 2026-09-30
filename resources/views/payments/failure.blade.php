<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('تعذر إتمام عملية الدفع — موتورزاد') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <style>
        :root {
            --danger-color: #ef4444;
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
                radial-gradient(at 50% 0%, rgba(239, 68, 68, 0.12) 0px, transparent 60%);
        }

        .failure-card {
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

        .failure-icon-wrapper {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.12);
            border: 2px solid rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .message-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
            color: #fca5a5;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .btn-retry {
            background: var(--danger-gradient);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            width: 100%;
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.75rem;
        }

        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(239, 68, 68, 0.5);
            color: #ffffff;
        }

        .btn-outline-custom {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 1.75rem;
            border-radius: 14px;
            width: 100%;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="failure-card">
    <!-- Failure Icon -->
    <div class="failure-icon-wrapper">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </div>

    <!-- Title & Description -->
    <h2 class="h4 fw-bold text-white mb-1">{{ __('تعذر إتمام عملية الدفع') }}</h2>
    <p class="text-muted small mb-0">{{ __('لم يتم خصم أي مبالغ من حسابك المصرفي لهذه العملية.') }}</p>

    <!-- Error Message -->
    <div class="message-box">
        <strong>{{ __('سبب الرفض:') }}</strong>
        <div>{{ $message }}</div>
    </div>

    <!-- Actions -->
    @if($retryUrl)
        <a href="{{ $retryUrl }}" class="btn btn-retry">
            🔄 {{ __('إعادة المحاولة ببطاقة أخرى') }}
        </a>
    @endif

    @if($source === 'app')
        <button type="button" class="btn btn-outline-custom" onclick="closeWebViewAndReturn();">
            {{ __('العودة للتطبيق') }}
        </button>
    @else
        <a href="{{ route('bidder.wallet.index') }}" class="btn btn-outline-custom">
            {{ __('العودة إلى محفظتي') }}
        </a>
    @endif
</div>

<script>
    // Prepare failure payload for Flutter Application WebView
    const paymentResult = {
        status: 'failed',
        transaction_id: '{{ $transaction?->id ?? "" }}',
        merchant_transaction_id: '{{ $transaction?->merchant_transaction_id ?? "" }}',
        amount: {{ (float) ($transaction?->amount ?? 0) }},
        message: '{{ addslashes($message) }}'
    };

    // Broadcast failure through JavaScript Channel to Flutter WebView
    function notifyFlutterApp() {
        if (window.FlutterBridge && typeof window.FlutterBridge.postMessage === 'function') {
            window.FlutterBridge.postMessage(JSON.stringify(paymentResult));
        }
        if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.FlutterBridge) {
            window.webkit.messageHandlers.FlutterBridge.postMessage(JSON.stringify(paymentResult));
        }
    }

    function closeWebViewAndReturn() {
        notifyFlutterApp();
        try { window.close(); } catch(e) {}
    }

    window.addEventListener('DOMContentLoaded', function() {
        notifyFlutterApp();
    });
</script>

</body>
</html>
