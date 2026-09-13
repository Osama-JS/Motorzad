<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق - {{ config('app.name', 'Motorzad') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0b0f19;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #f1f5f9;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0b0f19;
            padding: 40px 10px;
        }
        .main {
            background-color: #161f30;
            margin: 0 auto;
            width: 100%;
            max-width: 560px;
            border-spacing: 0;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #ef4444;
        }
        .brand-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #ffffff;
            margin: 0;
        }
        .brand-title span {
            color: #ef4444;
        }
        .content {
            padding: 35px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
        }
        .subtitle {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .otp-container {
            background: rgba(15, 23, 42, 0.8);
            border: 2px dashed #ef4444;
            border-radius: 14px;
            padding: 20px;
            margin: 0 auto 28px auto;
            max-width: 320px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 38px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #f87171;
            display: inline-block;
            margin: 0;
        }
        .badge-notice {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 25px;
        }
        .security-box {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 16px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
            text-align: right;
            border-right: 3px solid #f59e0b;
        }
        .footer {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .footer a {
            color: #94a3b8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1 class="brand-title">MOTOR<span>ZAD</span> | موتورزاد</h1>
                    <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 12px;">منصة مزادات السيارات الرائدة</p>
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="content">
                    @php
                        $purposeTitle = match($purpose ?? 'verification') {
                            'password_reset' => 'استعادة كلمة المرور',
                            'login'          => 'تسجيل الدخول إلى حسابك',
                            default          => 'التحقق من حسابك الإلكتروني',
                        };
                    @endphp

                    <div class="greeting">رمز التحقق الأمني (OTP)</div>
                    <div class="subtitle">
                        لقد تم طلب هذا الرمز لـ <strong>{{ $purposeTitle }}</strong> في منصة موتورزاد.<br>
                        يرجى إدخال الرمز التالي لإتمام العملية:
                    </div>

                    <!-- OTP Display Box -->
                    <div class="otp-container">
                        <div class="otp-code">{{ $otp }}</div>
                    </div>

                    <div class="badge-notice">
                        ⏱️ الرمز صالح لمدة {{ $expiresInMinutes ?? 5 }} دقائق فقط
                    </div>

                    <!-- Security Alert -->
                    <div class="security-box">
                        <strong>🔒 تنبيه أمني هام:</strong><br>
                        لا تشارك هذا الرمز مع أي شخص، بما في ذلك موظفي خدمة العملاء أو إدارة المنصة. إذا لم تكن أنت من قام بطلب هذا الرمز، يُرجى تجاهل هذه الرسالة فوراً.
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ config('app.name', 'Motorzad') }}.<br>
                    هذه الرسالة تم إنشاؤها تلقائياً، يرجى عدم الرد عليها.
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
