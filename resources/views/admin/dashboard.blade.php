@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="page-header">
    <div>
        <h1>لوحة التحكم</h1>
        <div class="breadcrumb">مرحباً بك في نظام إدارة Motorazad</div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card red">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-value">{{ $stats['users_count'] }}</div>
        <div class="stat-label">إجمالي المستخدمين</div>
    </div>

    <div class="stat-card gold">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['roles_count'] }}</div>
        <div class="stat-label">الأدوار المُعرّفة</div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-value">{{ $stats['permissions_count'] }}</div>
        <div class="stat-label">الصلاحيات المتاحة</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-label">المزادات النشطة</div>
    </div>
</div>

{{-- Welcome Hero --}}
<div class="welcome-hero">
    <div class="hero-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        لوحة تحكم المدير
    </div>
    <h2>مرحباً بك في <span class="highlight">MOTORAZAD</span></h2>
    <p>
        من هنا تتحكم بكل شيء — إدارة المستخدمين، تعريف الأدوار والصلاحيات،
        ومراقبة نشاط المزادات بكل سهولة ومرونة. ابدأ رحلتك الآن.
    </p>
    <div class="btn-group">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            إدارة المستخدمين
        </a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            إضافة دور جديد
        </a>
    </div>
</div>
@endsection
