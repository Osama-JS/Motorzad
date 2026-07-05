<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} #{{ $transaction->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 40px 20px;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }
        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0,0,0,0.03);
            overflow: hidden;
            position: relative;
        }
        .invoice-header {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .invoice-header-left h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--primary);
        }
        .invoice-header-left p {
            margin: 8px 0 0;
            color: #9ca3af;
            font-size: 15px;
        }
        .invoice-header-right {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .invoice-header-right .inv-number {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px;
        }
        .invoice-header-right .inv-date {
            color: #9ca3af;
            font-size: 14px;
        }
        .invoice-body {
            padding: 40px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .info-block {
            background: #f9fafb;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .info-block h3 {
            margin: 0 0 15px;
            font-size: 14px;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            font-weight: 700;
        }
        .info-block p {
            margin: 0 0 8px;
            font-size: 15px;
            font-weight: 500;
        }
        .info-block p:last-child {
            margin-bottom: 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--primary-dark);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-top: 10px;
        }
        .table-wrapper {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f9fafb;
            padding: 16px 20px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
        }
        td {
            padding: 20px;
            font-size: 15px;
            font-weight: 500;
            border-bottom: 1px solid var(--border-color);
        }
        tr:last-child td {
            border-bottom: none;
        }
        .amount-col {
            font-weight: 800;
            font-size: 16px;
        }
        .amount-col.credit { color: var(--primary); }
        .amount-col.debit { color: #ef4444; }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-box {
            width: 300px;
            background: #f9fafb;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 15px;
            color: var(--text-muted);
            font-weight: 500;
        }
        .totals-row.final {
            margin-bottom: 0;
            padding-top: 15px;
            border-top: 2px dashed var(--border-color);
            color: var(--text-main);
            font-size: 22px;
            font-weight: 800;
        }
        .invoice-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }
        .print-actions {
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: white;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-print:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(0,0,0,0.02);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        @media print {
            body { background: white; padding: 0; }
            .invoice-wrapper { box-shadow: none; border: none; max-width: 100%; border-radius: 0; }
            .print-actions { display: none; }
            .info-block { border: 1px solid #ddd; }
            .table-wrapper { border: 1px solid #ddd; }
            .totals-box { border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            {{ __('طباعة / حفظ PDF') }}
        </button>
    </div>

    <div class="invoice-wrapper">
        <div class="watermark">{{ config('app.name', 'Motorzad') }}</div>
        
        <div class="invoice-header">
            <div class="invoice-header-left">
                <h1>{{ config('app.name', 'Motorzad') }}</h1>
                <p>{{ __('إيصال مالي إلكتروني') }}</p>
            </div>
            <div class="invoice-header-right">
                <div class="inv-number">#TXN-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="inv-date">{{ $transaction->created_at->translatedFormat('d F Y - h:i A') }}</div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="info-grid">
                <div class="info-block" style="position: relative; z-index: 1;">
                    <h3>{{ __('معلومات العميل') }}</h3>
                    <p style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 12px;">{{ $user->full_name }}</p>
                    <p style="color: var(--text-muted);"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> {{ $user->email }}</p>
                    @if($user->phone)
                    <p style="color: var(--text-muted);"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> {{ $user->phone }}</p>
                    @endif
                    <div class="status-badge">{{ __('الحالة: مكتملة بنجاح') }}</div>
                </div>
                
                <div class="info-block" style="position: relative; z-index: 1;">
                    <h3>{{ __('معلومات المنصة') }}</h3>
                    <p style="font-weight: 700; color: var(--text-main);">{{ config('app.name', 'Motorzad') }} {{ __('للمزادات') }}</p>
                    <p style="color: var(--text-muted);">{{ __('المملكة العربية السعودية') }}</p>
                    <p style="color: var(--text-muted);">{{ __('الرقم الضريبي (VAT):') }} <strong>300000000000003</strong></p>
                    <p style="color: var(--text-muted);">support@motorzad.com</p>
                </div>
            </div>

            <div class="table-wrapper" style="position: relative; z-index: 1;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('رقم العملية') }}</th>
                            <th>{{ __('الوصف') }}</th>
                            <th>{{ __('نوع العملية') }}</th>
                            <th style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}">{{ __('المبلغ') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="color: var(--text-muted);">#TXN-{{ $transaction->id }}</td>
                            <td>{{ $transaction->description ?: ($transaction->type === 'credit' ? __('إيداع رصيد في المحفظة') : __('سحب رصيد من المحفظة')) }}</td>
                            <td>
                                @if($transaction->type === 'credit')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--primary); font-weight: 700;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ __('إيداع') }}
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #ef4444; font-weight: 700;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg> {{ __('سحب') }}
                                    </span>
                                @endif
                            </td>
                            <td class="amount-col {{ $transaction->type }}" style="text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} {{ __('ر.س') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="totals-section" style="position: relative; z-index: 1;">
                <div class="totals-box">
                    <div class="totals-row">
                        <span>{{ __('المبلغ الفرعي:') }}</span>
                        <span>{{ number_format($transaction->amount, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>{{ __('ضريبة القيمة المضافة (0%):') }}</span>
                        <span>0.00</span>
                    </div>
                    <div class="totals-row final">
                        <span>{{ __('الإجمالي:') }}</span>
                        <span style="color: var(--primary);">{{ number_format($transaction->amount, 2) }} <small style="font-size: 14px;">{{ __('ر.س') }}</small></span>
                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                <p style="margin: 0 0 8px; font-weight: 700; color: var(--text-main);">{{ __('شكراً لثقتكم في موتورزاد') }}</p>
                <p style="margin: 0;">{{ __('هذا إيصال إلكتروني تم إنشاؤه آلياً ولا يتطلب توقيعاً يدوياً.') }}</p>
                <p style="margin: 4px 0 0;">&copy; {{ date('Y') }} {{ config('app.name', 'Motorzad') }}. {{ __('جميع الحقوق محفوظة.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
