<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص الاتصال - {{ config('app.name', 'Motorzad') }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f1f5f9; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding: 40px 10px; }
        .main { background-color: #161f30; margin: 0 auto; width: 100%; max-width: 560px; border-spacing: 0; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.08); overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,0.6); }
        .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 30px; text-align: center; border-bottom: 2px solid #10b981; }
        .content { padding: 35px 30px; text-align: center; }
        .success-icon { font-size: 48px; margin-bottom: 15px; }
        .footer { background-color: #0f172a; padding: 20px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <tr>
                <td class="header">
                    <h1 style="color: #fff; margin: 0; font-size: 24px;">MOTOR<span style="color: #10b981;">ZAD</span></h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <div class="success-icon">✅</div>
                    <h2 style="color: #10b981; margin-top: 0;">تم الاتصال بخادم البريد بنجاح!</h2>
                    <p style="color: #94a3b8; font-size: 14px; line-height: 1.6;">
                        هذه رسالة اختبار تم إرسالها بنجاح من لوحة تحكم الإدارة لمنصة <strong>{{ config('app.name', 'Motorzad') }}</strong>.<br>
                        إعدادات خادم البريد (SMTP) تعمل بكفاءة تامة وتستقبل الرسائل بشكل سليم.
                    </p>
                    <div style="margin-top: 25px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 12px; font-size: 12px; color: #6ee7b7;">
                        وقت الإرسال: {{ now()->format('Y-m-d H:i:s') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ config('app.name', 'Motorzad') }}.
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
