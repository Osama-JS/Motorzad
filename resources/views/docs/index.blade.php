<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل مبرمج الموبايل | Motorzad API</title>
    <meta name="description" content="الدليل الشامل لمبرمج تطبيق الموبايل لمنصة موتور زاد - مزادات السيارات">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6C63FF;
            --primary-dark: #5A52D5;
            --primary-glow: rgba(108, 99, 255, 0.15);
            --secondary: #FF6584;
            --accent: #43E97B;
            --warning: #F7B731;
            --danger: #FC5C65;
            --bg-dark: #0D0F1A;
            --bg-card: #141726;
            --bg-card-hover: #1A1F35;
            --bg-sidebar: #10131E;
            --border: rgba(255, 255, 255, 0.07);
            --text-primary: #F0F2FF;
            --text-secondary: #8892B0;
            --text-muted: #4E5A7A;
            --get: #43E97B;
            --post: #6C63FF;
            --put: #F7B731;
            --delete: #FC5C65;
            --sidebar-width: 280px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        /* ── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-left: 1px solid var(--border);
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            padding-bottom: 2rem;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--primary) 0%, #8B5CF6 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sidebar-brand h2 {
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .sidebar-brand span {
            display: block;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.7);
            margin-top: 2px;
            font-weight: 400;
        }

        .sidebar-section {
            padding: 1.25rem 1rem 0.5rem;
        }

        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0 0.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: var(--primary-glow);
            color: var(--primary);
        }

        .sidebar-link .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--text-muted);
            flex-shrink: 0;
        }

        .sidebar-link:hover .dot, .sidebar-link.active .dot {
            background: var(--primary);
        }

        /* ── Main Content ─── */
        .main {
            margin-right: var(--sidebar-width);
            flex: 1;
            padding: 0 2.5rem 4rem;
            max-width: calc(100vw - var(--sidebar-width));
        }

        /* ── Hero Header ─── */
        .hero {
            padding: 4rem 0 3rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(108,99,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.8rem;
            background: var(--primary-glow);
            border: 1px solid rgba(108,99,255,0.3);
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 900;
            line-height: 1.2;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.05rem;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 650px;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
        }

        .hero-stat .number {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary);
        }

        .hero-stat .label {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* ── Section ─── */
        .section {
            margin-bottom: 4rem;
            scroll-margin-top: 2rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-glow);
            border: 1px solid rgba(108,99,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .section h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .section .section-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.2rem;
        }

        /* ── Cards ─── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.25s ease;
        }

        .card:hover {
            border-color: rgba(108,99,255,0.25);
            background: var(--bg-card-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        /* ── API Endpoint ─── */
        .endpoint {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .endpoint-header:hover {
            background: rgba(255,255,255,0.02);
        }

        .method-badge {
            font-family: 'Fira Code', monospace;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            min-width: 52px;
            text-align: center;
            flex-shrink: 0;
        }

        .method-badge.get { background: rgba(67,233,123,0.15); color: var(--get); border: 1px solid rgba(67,233,123,0.3); }
        .method-badge.post { background: rgba(108,99,255,0.15); color: var(--post); border: 1px solid rgba(108,99,255,0.3); }
        .method-badge.put { background: rgba(247,183,49,0.15); color: var(--put); border: 1px solid rgba(247,183,49,0.3); }
        .method-badge.delete { background: rgba(252,92,101,0.15); color: var(--delete); border: 1px solid rgba(252,92,101,0.3); }

        .endpoint-path {
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            color: var(--text-primary);
            flex: 1;
            direction: ltr;
            text-align: left;
        }

        .endpoint-summary {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-right: auto;
        }

        .endpoint-body {
            padding: 1.25rem;
            border-top: 1px solid var(--border);
            background: rgba(0,0,0,0.2);
        }

        .endpoint-desc {
            color: var(--text-secondary);
            font-size: 0.88rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        /* ── Auth Badge ─── */
        .auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .auth-badge.private {
            background: rgba(252,92,101,0.1);
            color: var(--danger);
            border: 1px solid rgba(252,92,101,0.2);
        }

        .auth-badge.public {
            background: rgba(67,233,123,0.1);
            color: var(--accent);
            border: 1px solid rgba(67,233,123,0.2);
        }

        /* ── Code Block ─── */
        .code-block {
            background: #090B14;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 1.25rem;
            font-family: 'Fira Code', monospace;
            font-size: 0.8rem;
            line-height: 1.8;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            margin: 0.75rem 0;
            position: relative;
        }

        .code-block .comment { color: #4E5A7A; }
        .code-block .key { color: #79C0FF; }
        .code-block .value { color: #A5D6FF; }
        .code-block .string { color: #A8FF78; }
        .code-block .method { color: #E5C07B; }
        .code-block .url { color: #C3E88D; }
        .code-block .header-name { color: #FF79C6; }

        .code-lang {
            position: absolute;
            top: 0.5rem;
            right: 0.75rem;
            font-size: 0.65rem;
            color: var(--text-muted);
            font-family: 'Cairo', sans-serif;
        }

        /* ── Param Table ─── */
        .param-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
            margin: 0.75rem 0;
        }

        .param-table th {
            text-align: right;
            padding: 0.6rem 0.75rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            background: rgba(0,0,0,0.2);
        }

        .param-table td {
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: var(--text-secondary);
            vertical-align: top;
        }

        .param-table tr:last-child td { border-bottom: none; }

        .param-name {
            font-family: 'Fira Code', monospace;
            font-size: 0.78rem;
            color: #79C0FF;
            background: rgba(121,192,255,0.06);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            white-space: nowrap;
        }

        .param-required {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
        }

        .param-required.yes { background: rgba(252,92,101,0.1); color: var(--danger); }
        .param-required.no { background: rgba(78,90,122,0.2); color: var(--text-muted); }
        .param-type {
            font-family: 'Fira Code', monospace;
            font-size: 0.72rem;
            color: var(--warning);
        }

        /* ── Alert boxes ─── */
        .alert {
            display: flex;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-size: 0.87rem;
            line-height: 1.7;
        }

        .alert-icon { font-size: 1.1rem; flex-shrink: 0; }

        .alert.info { background: rgba(108,99,255,0.08); border: 1px solid rgba(108,99,255,0.2); color: #A8B5D5; }
        .alert.warning { background: rgba(247,183,49,0.08); border: 1px solid rgba(247,183,49,0.2); color: #D4B766; }
        .alert.success { background: rgba(67,233,123,0.08); border: 1px solid rgba(67,233,123,0.2); color: #7EC899; }
        .alert.danger { background: rgba(252,92,101,0.08); border: 1px solid rgba(252,92,101,0.2); color: #D4888E; }

        /* ── Flow Steps ─── */
        .flow {
            display: flex;
            flex-direction: column;
            gap: 0;
            position: relative;
        }

        .flow::before {
            content: '';
            position: absolute;
            right: 19px;
            top: 20px;
            bottom: 20px;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary), transparent);
        }

        .flow-step {
            display: flex;
            gap: 1.25rem;
            padding: 0 0 1.75rem 0;
            position: relative;
        }

        .flow-step:last-child { padding-bottom: 0; }

        .step-num {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            box-shadow: 0 0 20px rgba(108,99,255,0.4);
        }

        .step-content h4 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
        }

        .step-content p {
            font-size: 0.87rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* ── Tags ─── */
        .tag {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .tag-blue { background: rgba(108,99,255,0.15); color: var(--primary); }
        .tag-green { background: rgba(67,233,123,0.15); color: var(--accent); }
        .tag-yellow { background: rgba(247,183,49,0.15); color: var(--warning); }
        .tag-red { background: rgba(252,92,101,0.15); color: var(--danger); }

        /* ── Divider ─── */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 2.5rem 0;
        }

        /* ── Feature Grid ─── */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.25s;
            text-align: center;
        }

        .feature-card:hover {
            border-color: rgba(108,99,255,0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .feature-card .icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .feature-card h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
        }

        .feature-card p {
            font-size: 0.78rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ── Response Example ─── */
        .response-tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 0;
        }

        .resp-tab {
            padding: 0.5rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }

        .resp-tab.success { color: var(--accent); border-color: var(--accent); }
        .resp-tab.error { color: var(--danger); border-color: var(--danger); }

        /* ── Status Badges ─── */
        .status-row {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .status-badge {
            font-family: 'Fira Code', monospace;
            font-size: 0.72rem;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .status-200 { background: rgba(67,233,123,0.1); color: var(--accent); }
        .status-201 { background: rgba(67,233,123,0.1); color: var(--accent); }
        .status-400, .status-401, .status-403, .status-422 { background: rgba(252,92,101,0.1); color: var(--danger); }

        /* ── Mobile responsive ─── */
        @media (max-width: 900px) {
            :root { --sidebar-width: 0px; }
            .sidebar { display: none; }
            .main { margin-right: 0; max-width: 100%; padding: 0 1.25rem 3rem; }
            .hero h1 { font-size: 2rem; }
        }

        /* Sticky top nav */
        .top-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(13, 15, 26, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top-nav-brand {
            font-weight: 800;
            font-size: 1rem;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .swagger-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 1rem;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .swagger-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(108,99,255,0.4);
        }

        .section-anchor {
            display: block;
            height: 80px;
            margin-top: -80px;
            visibility: hidden;
        }
    </style>
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>🚗 Motorzad API</h2>
        <span>دليل مبرمج الموبايل v1.0</span>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">نظرة عامة</div>
        <a href="#overview" class="sidebar-link"><span class="dot"></span> مقدمة عن المنصة</a>
        <a href="#architecture" class="sidebar-link"><span class="dot"></span> آلية العمل</a>
        <a href="#auth-flow" class="sidebar-link"><span class="dot"></span> خطوات المصادقة</a>
        <a href="#base-setup" class="sidebar-link"><span class="dot"></span> الإعداد الأساسي</a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">المصادقة والحسابات</div>
        <a href="#auth-api" class="sidebar-link"><span class="dot"></span> التسجيل وتسجيل الدخول</a>
        <a href="#otp-api" class="sidebar-link"><span class="dot"></span> نظام OTP</a>
        <a href="#profile-api" class="sidebar-link"><span class="dot"></span> الملف الشخصي</a>
        <a href="#kyc-api" class="sidebar-link"><span class="dot"></span> التوثيق (KYC)</a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">المزادات والمركبات</div>
        <a href="#vehicles-api" class="sidebar-link"><span class="dot"></span> المركبات</a>
        <a href="#auctions-api" class="sidebar-link"><span class="dot"></span> المزادات</a>
        <a href="#bidding-api" class="sidebar-link"><span class="dot"></span> آلية المزايدة</a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">المحفظة والمالية</div>
        <a href="#wallet-api" class="sidebar-link"><span class="dot"></span> المحفظة</a>
        <a href="#orders-api" class="sidebar-link"><span class="dot"></span> الطلبات</a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">الخدمات الأخرى</div>
        <a href="#notifications-api" class="sidebar-link"><span class="dot"></span> الإشعارات</a>
        <a href="#support-api" class="sidebar-link"><span class="dot"></span> الدعم الفني</a>
        <a href="#general-api" class="sidebar-link"><span class="dot"></span> البيانات العامة</a>
    </div>
</aside>

<!-- ═══════════ MAIN ═══════════ -->
<div class="main">

    <!-- Top Nav -->
    <nav class="top-nav">
        <span class="top-nav-brand">📋 دليل API - موتور زاد</span>
        <a href="/api/documentation" class="swagger-link" target="_blank">
            🔗 فتح Swagger التفاعلي
        </a>
    </nav>

    <!-- Hero -->
    <div class="hero" id="overview">
        <a name="overview"></a>
        <div class="hero-badge">📱 Mobile Developer Guide</div>
        <h1>دليل مبرمج تطبيق موبايل موتور زاد</h1>
        <p>
            هذا الدليل الشامل والتقني مخصص لمبرمج تطبيق الموبايل. يشرح فيه آلية عمل منصة موتور زاد لمزادات السيارات، 
            وطريقة التفاعل مع جميع نقاط الـ API بشكل احترافي ومرتب لكي تتمكن من بناء تطبيق موبايل متكامل وآمن.
        </p>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="number">+26</span>
                <span class="label">نقطة API</span>
            </div>
            <div class="hero-stat">
                <span class="number">8</span>
                <span class="label">أقسام رئيسية</span>
            </div>
            <div class="hero-stat">
                <span class="number">Laravel</span>
                <span class="label">Sanctum Auth</span>
            </div>
            <div class="hero-stat">
                <span class="number">REST</span>
                <span class="label">JSON API</span>
            </div>
        </div>
    </div>


    <!-- ═══ ARCHITECTURE ═══ -->
    <section class="section" id="architecture">
        <a class="section-anchor" name="architecture"></a>
        <div class="section-header">
            <div class="section-icon">🏗️</div>
            <div>
                <h2>آلية عمل المنصة</h2>
                <p class="section-desc">فهم آلية عمل المنصة أساسي قبل البدء بالبرمجة</p>
            </div>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <span class="icon">👤</span>
                <h3>إنشاء الحساب</h3>
                <p>يسجل المستخدم بالبريد الإلكتروني أو رقم الجوال، ويؤكد بـ OTP</p>
            </div>
            <div class="feature-card">
                <span class="icon">🪪</span>
                <h3>التوثيق (KYC)</h3>
                <p>لرفع مزاد أو المزايدة يجب أن يوثق المستخدم هويته بصورة الهوية الوطنية</p>
            </div>
            <div class="feature-card">
                <span class="icon">🚗</span>
                <h3>إضافة مركبة</h3>
                <p>يضيف البائع بيانات سيارته وصورها، وتنتظر موافقة الإدارة</p>
            </div>
            <div class="feature-card">
                <span class="icon">🔨</span>
                <h3>إنشاء مزاد</h3>
                <p>بعد قبول المركبة، يضع البائع تفاصيل المزاد (السعر، المدة، الضمان)</p>
            </div>
            <div class="feature-card">
                <span class="icon">💰</span>
                <h3>المزايدة</h3>
                <p>يزايد المشترون وتدعم المنصة المزايدة التلقائية وتمديد الوقت</p>
            </div>
            <div class="feature-card">
                <span class="icon">🏆</span>
                <h3>الفوز والدفع</h3>
                <p>يُنشأ طلب للفائز يستكمل فيه بيانات الدفع وطريقة التوصيل</p>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ AUTH FLOW ═══ -->
    <section class="section" id="auth-flow">
        <a class="section-anchor" name="auth-flow"></a>
        <div class="section-header">
            <div class="section-icon">🔐</div>
            <div>
                <h2>خطوات المصادقة (Authentication Flow)</h2>
                <p class="section-desc">الخطوات التسلسلية التي يجب على المستخدم اتباعها</p>
            </div>
        </div>

        <div class="flow">
            <div class="flow-step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>التسجيل أو تسجيل الدخول</h4>
                    <p>يمكن التسجيل بـ <code>POST /api/auth/register</code> أو الدخول بـ <code>POST /api/auth/login</code>. عند النجاح يُرجع <code>access_token</code>.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>حفظ الـ Token</h4>
                    <p>احفظ الـ <code>access_token</code> في Secure Storage (مثل <code>flutter_secure_storage</code>). أضفه في كل طلب كـ Header: <code>Authorization: Bearer TOKEN</code>.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>تأكيد البريد الإلكتروني (اختياري)</h4>
                    <p>إذا أراد المستخدم تفعيل بريده الإلكتروني، أرسل OTP بـ <code>POST /api/auth/email/verify</code>. يمكن تخطي هذه الخطوة.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">4</div>
                <div class="step-content">
                    <h4>التوثيق بالهوية (KYC) - إلزامي للمزايدة ورفع المزادات</h4>
                    <p>لتمكين المستخدم من المزايدة أو رفع مزادات، يجب رفع صورة هويته بـ <code>POST /api/kyc</code>. ستظل حالة الـ KYC <code>pending</code> حتى تراجعها الإدارة.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">5</div>
                <div class="step-content">
                    <h4>اكتمال الحساب ✓</h4>
                    <p>بعد قبول الـ KYC، يستطيع المستخدم المزايدة وإضافة مركبات ومزادات بشكل كامل.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ BASE SETUP ═══ -->
    <section class="section" id="base-setup">
        <a class="section-anchor" name="base-setup"></a>
        <div class="section-header">
            <div class="section-icon">⚙️</div>
            <div>
                <h2>الإعداد الأساسي</h2>
                <p class="section-desc">الإعدادات التي يجب مراعاتها في كل طلب</p>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem; font-size:1rem;">الـ Headers المطلوبة في كل طلب</h3>
            <div class="code-block">
                <span class="code-lang">HTTP Headers</span>
<span class="comment">// مطلوب في كل الطلبات</span>
<span class="header-name">Accept</span>: application/json
<span class="header-name">Content-Type</span>: application/json
<span class="header-name">Accept-Language</span>: ar  <span class="comment">// أو "en" للإنجليزية</span>

<span class="comment">// مطلوب فقط في الطلبات التي تتطلب تسجيل دخول</span>
<span class="header-name">Authorization</span>: Bearer {access_token}
            </div>
        </div>

        <div class="alert info">
            <span class="alert-icon">💡</span>
            <div>
                <strong>نصيحة:</strong> أرسل <code>Accept-Language: ar</code> لكي تأتيك رسائل الخطأ والنجاح باللغة العربية تلقائياً، وهذا يسهل عرضها للمستخدم مباشرة دون الحاجة للترجمة.
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem; font-size:1rem;">هيكل الاستجابة القياسي</h3>
            <div class="code-block">
                <span class="code-lang">JSON Response</span>
{
  <span class="key">"success"</span>: <span class="string">true</span>,          <span class="comment">// أو false عند الخطأ</span>
  <span class="key">"message"</span>: <span class="string">"تمت العملية بنجاح"</span>,
  <span class="key">"data"</span>: { ... }          <span class="comment">// البيانات المطلوبة</span>
}

<span class="comment">// عند الخطأ</span>
{
  <span class="key">"success"</span>: <span class="string">false</span>,
  <span class="key">"message"</span>: <span class="string">"رسالة الخطأ"</span>,
  <span class="key">"errors"</span>: { <span class="key">"field"</span>: [<span class="string">"رسالة تحقق"</span>] }
}
            </div>
        </div>

        <div class="alert warning">
            <span class="alert-icon">⚠️</span>
            <div>
                <strong>أخطاء HTTP الشائعة:</strong><br>
                <code>401 Unauthorized</code> → الـ Token منتهي أو غير موجود، أعد تسجيل الدخول.<br>
                <code>403 Forbidden</code> → ليس لديك صلاحية للوصول لهذا المورد.<br>
                <code>422 Unprocessable</code> → خطأ في التحقق من البيانات، تحقق من حقل <code>errors</code>.<br>
                <code>404 Not Found</code> → المورد غير موجود.
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ AUTH API ═══ -->
    <section class="section" id="auth-api">
        <a class="section-anchor" name="auth-api"></a>
        <div class="section-header">
            <div class="section-icon">🔑</div>
            <div>
                <h2>المصادقة والحسابات</h2>
                <p class="section-desc">تسجيل، دخول، استرجاع كلمة مرور، الملف الشخصي</p>
            </div>
        </div>

        <!-- Register -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/register</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تسجيل مستخدم جديد (مزايد). عند النجاح يُرجع <code>access_token</code> جاهز للاستخدام مباشرة.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">first_name</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>الاسم الأول</td></tr>
                        <tr><td><span class="param-name">last_name</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>الاسم الأخير</td></tr>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">email</span></td><td><span class="param-required yes">نعم</span></td><td>البريد الإلكتروني (فريد)</td></tr>
                        <tr><td><span class="param-name">phone</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>رقم الجوال بدون كود الدولة</td></tr>
                        <tr><td><span class="param-name">country_code</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>كود الدولة مثل <code>+966</code></td></tr>
                        <tr><td><span class="param-name">password</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>كلمة المرور (8 أحرف على الأقل)</td></tr>
                        <tr><td><span class="param-name">password_confirmation</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>تأكيد كلمة المرور</td></tr>
                    </tbody>
                </table>
                <div class="status-row">
                    <span class="status-badge status-201">201 Created</span>
                    <span class="status-badge status-422">422 Validation Error</span>
                </div>
            </div>
        </div>

        <!-- Login -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/login</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تسجيل الدخول بالبريد الإلكتروني وكلمة المرور. يُرجع <code>access_token</code>.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">email</span></td><td><span class="param-required yes">نعم</span></td><td>البريد الإلكتروني</td></tr>
                        <tr><td><span class="param-name">password</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>كلمة المرور</td></tr>
                    </tbody>
                </table>
                <div class="status-row">
                    <span class="status-badge status-200">200 OK</span>
                    <span class="status-badge status-401">401 Unauthorized</span>
                </div>
            </div>
        </div>

        <!-- Forgot Password -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/forgot-password</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">يرسل OTP لاسترجاع كلمة المرور على البريد الإلكتروني.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">email</span></td><td><span class="param-required yes">نعم</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reset Password -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/reset-password</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">إعادة تعيين كلمة المرور بعد التحقق من الـ OTP.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">email</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">otp</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">password</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">password_confirmation</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logout -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/logout</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تسجيل الخروج وإلغاء الـ Token الحالي. لا يتطلب body.</p>
                <div class="status-row"><span class="status-badge status-200">200 OK</span></div>
            </div>
        </div>

        <!-- Get me -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/auth/me</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">جلب بيانات المستخدم الحالي. استخدم هذه النقطة عند فتح التطبيق للتحقق من صحة الـ Token وتحميل بيانات المستخدم.</p>
                <div class="code-block">
                    <span class="code-lang">Response Data</span>
{
  <span class="key">"data"</span>: {
    <span class="key">"user"</span>: {
      <span class="key">"id"</span>: <span class="string">1</span>,
      <span class="key">"first_name"</span>: <span class="string">"أحمد"</span>,
      <span class="key">"kyc_status"</span>: <span class="string">"approved"</span>, <span class="comment">// pending | approved | rejected</span>
      <span class="key">"auto_bid_enabled"</span>: <span class="string">false</span>,
      <span class="key">"wallet"</span>: { <span class="key">"balance"</span>: <span class="string">1500.00</span> }
    }
  }
}
                </div>
            </div>
        </div>

        <!-- Update Profile -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge put">PUT</span>
                <span class="endpoint-path">/api/auth/profile</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تحديث بيانات الملف الشخصي (الاسم، الجوال، الخ).</p>
            </div>
        </div>

        <!-- Auto Bid Settings -->
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auth/auto-bid-settings</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تفعيل أو تعطيل ميزة المزايدة التلقائية. الإعداد الافتراضي هو معطل (<code>false</code>). يجب تفعيله قبل استخدام <code>is_auto_bid</code> في المزايدة.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">auto_bid_enabled</span></td><td><span class="param-type">boolean</span></td><td><code>true</code> لتفعيل / <code>false</code> لتعطيل</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ OTP ═══ -->
    <section class="section" id="otp-api">
        <a class="section-anchor" name="otp-api"></a>
        <div class="section-header">
            <div class="section-icon">📲</div>
            <div>
                <h2>نظام الـ OTP</h2>
                <p class="section-desc">التحقق عبر رمز يُرسل على الجوال أو البريد</p>
            </div>
        </div>

        <div class="alert info">
            <span class="alert-icon">ℹ️</span>
            يستخدم نظام OTP لتسجيل الدخول بدون كلمة مرور عبر الجوال أو للتحقق من رقم الهاتف/البريد.
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/otp/send</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">يُرسل رمز OTP مكون من 6 أرقام. يمكن الإرسال على الجوال أو البريد.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">phone</span></td><td><span class="param-type">string</span></td><td><span class="param-required no">اختياري</span></td><td>رقم الجوال (إذا أردت الإرسال على الجوال)</td></tr>
                        <tr><td><span class="param-name">country_code</span></td><td><span class="param-type">string</span></td><td><span class="param-required no">اختياري</span></td><td>كود الدولة مثل <code>+966</code></td></tr>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">email</span></td><td><span class="param-required no">اختياري</span></td><td>البريد الإلكتروني (إذا أردت الإرسال على البريد)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/otp/verify</span>
                <span class="auth-badge public">🌐 عام</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">يتحقق من صحة الـ OTP. إذا كان المستخدم موجوداً يدخله، وإن لم يكن يُنشئ حساباً تلقائياً ويُرجع <code>access_token</code>.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">phone / email</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">أحدهما</span></td></tr>
                        <tr><td><span class="param-name">code</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ KYC ═══ -->
    <section class="section" id="kyc-api">
        <a class="section-anchor" name="kyc-api"></a>
        <div class="section-header">
            <div class="section-icon">🪪</div>
            <div>
                <h2>التوثيق بالهوية (KYC)</h2>
                <p class="section-desc">إلزامي لأي عمليات مزايدة أو رفع مزادات</p>
            </div>
        </div>

        <div class="alert warning">
            <span class="alert-icon">⚠️</span>
            <div>
                <strong>مهم:</strong> قبل أي محاولة مزايدة أو رفع مزاد، تحقق من <code>kyc_status</code> في بيانات المستخدم. إذا لم يكن <code>approved</code> ابعد المستخدم لشاشة التوثيق.
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <h4 style="margin-bottom:0.5rem; font-size:0.9rem;">حالات الـ KYC</h4>
                <p style="font-size:0.82rem; color:var(--text-secondary)">
                    <span class="tag tag-yellow">pending</span> → بانتظار مراجعة الإدارة<br>
                    <span class="tag tag-green">approved</span> → تم التوثيق بنجاح ✓<br>
                    <span class="tag tag-red">rejected</span> → مرفوض، يمكن إعادة الرفع
                </p>
            </div>
            <div class="card">
                <h4 style="margin-bottom:0.5rem; font-size:0.9rem;">ما يجب رفعه</h4>
                <p style="font-size:0.82rem; color:var(--text-secondary)">صورة وجه الهوية الوطنية، وصورة ظهر الهوية (اختياري)، وصورة سيلفي مع الهوية (اختياري).</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/kyc</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">جلب حالة طلب التوثيق الحالي للمستخدم.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/kyc</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">رفع طلب توثيق جديد. يجب إرسال الملفات كـ <code>multipart/form-data</code>.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">id_front</span></td><td><span class="param-type">file (image)</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">id_back</span></td><td><span class="param-type">file (image)</span></td><td><span class="param-required no">لا</span></td></tr>
                        <tr><td><span class="param-name">selfie</span></td><td><span class="param-type">file (image)</span></td><td><span class="param-required no">لا</span></td></tr>
                    </tbody>
                </table>
                <div class="alert info">
                    <span class="alert-icon">💡</span>
                    تذكر تغيير الـ Content-Type إلى <code>multipart/form-data</code> عند رفع الصور.
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ VEHICLES ═══ -->
    <section class="section" id="vehicles-api">
        <a class="section-anchor" name="vehicles-api"></a>
        <div class="section-header">
            <div class="section-icon">🚗</div>
            <div>
                <h2>إدارة المركبات</h2>
                <p class="section-desc">إضافة وتعديل مركبات البائع</p>
            </div>
        </div>

        <div class="alert info">
            <span class="alert-icon">ℹ️</span>
            <div>
                المركبة بعد إضافتها تكون في حالة <code>pending</code> حتى تراجعها الإدارة. <strong>التعديل والحذف متاح فقط إذا كانت المركبة في حالة <code>pending</code> أو <code>draft</code>.</strong>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/vehicles/my</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">استعراض جميع مركبات المستخدم الحالي. يُرجع قائمة مُصفحة (Paginated).</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/vehicles</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">إضافة مركبة جديدة للمستخدم. للحصول على قائمة المصنعين والموديلات المتاحة، استدعِ أولاً <code>GET /api/general/vehicle-options</code>.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">make_ar / make_en</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>اسم الشركة المصنعة بالعربي والإنجليزي</td></tr>
                        <tr><td><span class="param-name">model_ar / model_en</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>اسم الموديل</td></tr>
                        <tr><td><span class="param-name">year</span></td><td><span class="param-type">integer</span></td><td><span class="param-required yes">نعم</span></td><td>سنة الصنع</td></tr>
                        <tr><td><span class="param-name">color_ar / color_en</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>اللون</td></tr>
                        <tr><td><span class="param-name">vin_number</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>رقم الهيكل (فريد)</td></tr>
                        <tr><td><span class="param-name">mileage</span></td><td><span class="param-type">integer</span></td><td><span class="param-required yes">نعم</span></td><td>عداد الكيلومترات</td></tr>
                        <tr><td><span class="param-name">fuel_type</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>نوع الوقود (petrol, diesel, electric)</td></tr>
                        <tr><td><span class="param-name">transmission</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>ناقل الحركة (automatic, manual)</td></tr>
                        <tr><td><span class="param-name">cylinders</span></td><td><span class="param-type">integer</span></td><td><span class="param-required yes">نعم</span></td><td>عدد الأسطوانات</td></tr>
                        <tr><td><span class="param-name">condition</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td>حالة السيارة (excellent, good, fair, poor)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/vehicles/{id}/images</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">رفع صور للمركبة. يُرسل كـ <code>multipart/form-data</code>. يمكن رفع حتى 10 صور مرة واحدة.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">images[]</span></td><td><span class="param-type">file[] (image)</span></td><td><span class="param-required yes">نعم</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ AUCTIONS ═══ -->
    <section class="section" id="auctions-api">
        <a class="section-anchor" name="auctions-api"></a>
        <div class="section-header">
            <div class="section-icon">🔨</div>
            <div>
                <h2>المزادات</h2>
                <p class="section-desc">استعراض وإدارة المزادات</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/auctions</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">استعراض جميع المزادات المتاحة. يدعم الفلترة والبحث عبر query parameters.</p>
                <table class="param-table">
                    <thead><tr><th>المعامل</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">status</span></td><td>فلتر بالحالة: <code>live</code>, <code>scheduled</code>, <code>ended</code></td></tr>
                        <tr><td><span class="param-name">make</span></td><td>فلتر بالشركة المصنعة</td></tr>
                        <tr><td><span class="param-name">search</span></td><td>كلمة بحث حرة</td></tr>
                        <tr><td><span class="param-name">page</span></td><td>رقم الصفحة (للـ Pagination)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/auctions/{id}</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">عرض تفاصيل مزاد واحد بالكامل مع السعر الحالي وعدد المزايدات وقائمة الصور.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auctions/{id}/watch</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">إضافة مزاد لقائمة المراقبة أو إزالته منها (Toggle). للحصول على قائمة المزادات المراقبة: <code>GET /api/auctions/watchlist</code>.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/auctions/my</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">المزادات التي أنشأها المستخدم الحالي (كبائع).</p>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ BIDDING ═══ -->
    <section class="section" id="bidding-api">
        <a class="section-anchor" name="bidding-api"></a>
        <div class="section-header">
            <div class="section-icon">💰</div>
            <div>
                <h2>آلية المزايدة</h2>
                <p class="section-desc">الأهم في التطبيق - اقرأ بعناية</p>
            </div>
        </div>

        <div class="alert danger">
            <span class="alert-icon">🚨</span>
            <div>
                <strong>شروط يجب التحقق منها قبل المزايدة:</strong><br>
                1. يجب أن يكون الـ KYC بحالة <code>approved</code><br>
                2. إذا طلب المزاد وديعة (<code>deposit_required: true</code>)، يجب على المستخدم دفعها أولاً<br>
                3. لا يمكن المزايدة على المزادات الخاصة بك كبائع<br>
                4. يجب أن يكون السعر المزايد أعلى من <code>current_price + bid_increment</code>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/auctions/{id}/bid</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">تقديم عرض مزايدة على مزاد نشط.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">amount</span></td><td><span class="param-type">number</span></td><td><span class="param-required yes">نعم</span></td><td>مبلغ المزايدة. يجب أن يكون أعلى من السعر الحالي + زيادة المزايدة</td></tr>
                        <tr><td><span class="param-name">is_auto_bid</span></td><td><span class="param-type">boolean</span></td><td><span class="param-required no">لا</span></td><td>إذا كان <code>true</code>، يعمل كـ Proxy Bid. يجب تفعيل <code>auto_bid_enabled</code> أولاً من الإعدادات</td></tr>
                    </tbody>
                </table>
                <div class="code-block">
                    <span class="code-lang">مثال طلب</span>
{
  <span class="key">"amount"</span>: <span class="string">15000</span>,
  <span class="key">"is_auto_bid"</span>: <span class="string">false</span>
}
                </div>
                <div class="code-block">
                    <span class="code-lang">استجابة نجاح</span>
{
  <span class="key">"success"</span>: <span class="string">true</span>,
  <span class="key">"message"</span>: <span class="string">"تمت المزايدة بنجاح!"</span>,
  <span class="key">"current_price"</span>: <span class="string">15000</span>,
  <span class="key">"bids_count"</span>: <span class="string">12</span>,
  <span class="key">"end_time"</span>: <span class="string">"2025-07-01T20:00:00Z"</span>
}
                </div>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/auctions/{id}/bids</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">عرض قائمة عروض المزايدة لمزاد معين. استخدم هذا لعرض سجل المزايدات في شاشة التفاصيل.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/my/bids</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">مزايداتي - جميع عروض المزايدة التي قدمها المستخدم.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/my/won</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">المزادات التي فاز بها المستخدم. استخدم هذا لعرض شاشة "مشترياتي".</p>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ WALLET ═══ -->
    <section class="section" id="wallet-api">
        <a class="section-anchor" name="wallet-api"></a>
        <div class="section-header">
            <div class="section-icon">👛</div>
            <div>
                <h2>المحفظة والمالية</h2>
                <p class="section-desc">إدارة الرصيد والمعاملات المالية</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/wallet</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">عرض رصيد المحفظة. استخدم هذا في الشاشة الرئيسية للمستخدم.</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge get">GET</span>
                <span class="endpoint-path">/api/wallet/transactions</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">سجل جميع حركات المحفظة (إيداع، سحب، عمولات).</p>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/wallet/deposit</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">طلب إيداع رصيد. يرسل الطلب للمراجعة اليدوية من الإدارة. استخدم <code>GET /api/bank-accounts</code> أولاً لعرض أرقام حسابات المنصة للمستخدم.</p>
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">amount</span></td><td><span class="param-type">number</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">bank_account_id</span></td><td><span class="param-type">integer</span></td><td><span class="param-required yes">نعم</span></td></tr>
                        <tr><td><span class="param-name">transfer_receipt</span></td><td><span class="param-type">file</span></td><td><span class="param-required no">لا</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge post">POST</span>
                <span class="endpoint-path">/api/wallet/withdraw</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">طلب سحب رصيد. يجب إضافة الحساب البنكي أولاً عبر <code>PUT /api/bank-details</code>.</p>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ ORDERS ═══ -->
    <section class="section" id="orders-api">
        <a class="section-anchor" name="orders-api"></a>
        <div class="section-header">
            <div class="section-icon">📦</div>
            <div>
                <h2>الطلبات (ما بعد الفوز)</h2>
                <p class="section-desc">عند الفوز بمزاد يُنشأ تلقائياً طلب</p>
            </div>
        </div>

        <div class="flow">
            <div class="flow-step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>الفوز بالمزاد</h4>
                    <p>عند انتهاء المزاد يُنشأ تلقائياً "طلب" (<code>Order</code>) للمزايد الفائز.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>عرض تفاصيل الطلب</h4>
                    <p>استخدم <code>GET /api/orders/{id}</code> لعرض المبالغ الإجمالية (سعر المزاد، العمولة، الضريبة، خصم الوديعة).</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>استكمال الطلب (Checkout)</h4>
                    <p>أرسل <code>PUT /api/orders/{id}/checkout</code> مع طريقة الدفع (<code>wallet</code> أو <code>bank_transfer</code>) وطريقة الاستلام (<code>pickup</code> أو <code>delivery</code>).</p>
                </div>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method-badge put">PUT</span>
                <span class="endpoint-path">/api/orders/{id}/checkout</span>
                <span class="auth-badge private">🔒 يتطلب Token</span>
            </div>
            <div class="endpoint-body">
                <table class="param-table">
                    <thead><tr><th>الحقل</th><th>النوع</th><th>إلزامي</th><th>الوصف</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">delivery_type</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td><code>pickup</code> أو <code>delivery</code></td></tr>
                        <tr><td><span class="param-name">payment_method</span></td><td><span class="param-type">string</span></td><td><span class="param-required yes">نعم</span></td><td><code>wallet</code> أو <code>bank_transfer</code></td></tr>
                        <tr><td><span class="param-name">delivery_address</span></td><td><span class="param-type">string</span></td><td><span class="param-required no">إذا delivery</span></td><td>عنوان التوصيل</td></tr>
                        <tr><td><span class="param-name">delivery_phone</span></td><td><span class="param-type">string</span></td><td><span class="param-required no">إذا delivery</span></td><td>رقم الجوال للتوصيل</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ NOTIFICATIONS ═══ -->
    <section class="section" id="notifications-api">
        <a class="section-anchor" name="notifications-api"></a>
        <div class="section-header">
            <div class="section-icon">🔔</div>
            <div>
                <h2>الإشعارات</h2>
                <p class="section-desc">إشعارات داخل التطبيق و Push Notifications</p>
            </div>
        </div>

        <div class="card-grid">
            <div class="endpoint">
                <div class="endpoint-header">
                    <span class="method-badge get">GET</span>
                    <span class="endpoint-path">/api/notifications</span>
                    <span class="auth-badge private">🔒 Token</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">جلب قائمة الإشعارات.</p>
                </div>
            </div>
            <div class="endpoint">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/api/notifications/fcm-token</span>
                    <span class="auth-badge private">🔒 Token</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">حفظ رمز FCM للإشعارات. أرسل <code>fcm_token</code> بعد تسجيل الدخول مباشرة.</p>
                </div>
            </div>
        </div>

        <div class="alert success">
            <span class="alert-icon">✅</span>
            <strong>نصيحة:</strong> بعد تسجيل الدخول مباشرة، استدعِ <code>POST /api/notifications/fcm-token</code> لحفظ رمز Firebase لاستقبال Push Notifications.
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ SUPPORT ═══ -->
    <section class="section" id="support-api">
        <a class="section-anchor" name="support-api"></a>
        <div class="section-header">
            <div class="section-icon">🎧</div>
            <div>
                <h2>الدعم الفني (تذاكر الشات)</h2>
                <p class="section-desc">نظام تذاكر مدمج يعمل كشات بين المستخدم والإدارة</p>
            </div>
        </div>

        <div class="flow">
            <div class="flow-step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>إنشاء تذكرة</h4>
                    <p>أرسل <code>POST /api/support</code> مع <code>subject</code> و <code>message</code>. سيُنشأ محادثة جديدة.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>عرض شاشة الشات</h4>
                    <p>استدعِ <code>GET /api/support/{id}</code> لعرض جميع رسائل التذكرة. كل رسالة فيها <code>is_admin</code> تعرف من أرسلها.</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>الرد</h4>
                    <p>أرسل <code>POST /api/support/{id}/reply</code> مع <code>message</code> للرد في نفس المحادثة.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- ═══ GENERAL ═══ -->
    <section class="section" id="general-api">
        <a class="section-anchor" name="general-api"></a>
        <div class="section-header">
            <div class="section-icon">🌐</div>
            <div>
                <h2>البيانات العامة</h2>
                <p class="section-desc">بيانات ثابتة لا تتطلب مصادقة</p>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <h4 style="font-size:0.9rem; margin-bottom:0.5rem;">
                    <span class="method-badge get" style="font-size:0.65rem;">GET</span>
                    <code>/api/general/vehicle-options</code>
                </h4>
                <p style="font-size:0.82rem; color:var(--text-secondary)">يُرجع قائمة الشركات المصنعة مع موديلاتها لاستخدامها في القوائم المنسدلة عند إضافة مركبة أو فلترة المزادات.</p>
            </div>
            <div class="card">
                <h4 style="font-size:0.9rem; margin-bottom:0.5rem;">
                    <span class="method-badge get" style="font-size:0.65rem;">GET</span>
                    <code>/api/general/faqs</code>
                </h4>
                <p style="font-size:0.82rem; color:var(--text-secondary)">الأسئلة الشائعة. استخدمها في شاشة المساعدة أو "حول التطبيق".</p>
            </div>
            <div class="card">
                <h4 style="font-size:0.9rem; margin-bottom:0.5rem;">
                    <span class="method-badge get" style="font-size:0.65rem;">GET</span>
                    <code>/api/general/settings</code>
                </h4>
                <p style="font-size:0.82rem; color:var(--text-secondary)">إعدادات المنصة العامة (شروط الاستخدام، سياسة الخصوصية، التواصل الاجتماعي).</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <div style="margin-top:4rem; padding:2rem; background:var(--bg-card); border-radius:14px; border:1px solid var(--border); text-align:center;">
        <p style="color:var(--text-muted); font-size:0.82rem;">
            للتوثيق التفاعلي الكامل مع إمكانية تجربة الـ API مباشرة، افتح
            <a href="/api/documentation" style="color:var(--primary); text-decoration:none; font-weight:600;">Swagger Documentation</a>
        </p>
        <p style="color:var(--text-muted); font-size:0.72rem; margin-top:0.5rem;">
            Motorzad Platform API v1.0 — powered by Laravel Sanctum
        </p>
    </div>

</div>

<script>
    // Sidebar active link on scroll
    const sections = document.querySelectorAll('.section');
    const links = document.querySelectorAll('.sidebar-link');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                links.forEach(l => l.classList.remove('active'));
                const id = entry.target.getAttribute('id');
                const active = document.querySelector(`.sidebar-link[href="#${id}"]`);
                if (active) active.classList.add('active');
            }
        });
    }, { threshold: 0.3 });

    sections.forEach(s => observer.observe(s));
</script>

</body>
</html>
