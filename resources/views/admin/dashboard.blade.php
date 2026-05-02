@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Dashboard') }}</h1>
        <div class="breadcrumb">{{ __('Welcome to Motorazad Management System') }}</div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card red">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-value">{{ $stats['users_count'] }}</div>
        <div class="stat-label">{{ __('Total Users') }}</div>
    </div>

    <div class="stat-card gold">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['roles_count'] }}</div>
        <div class="stat-label">{{ __('Defined Roles') }}</div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-value">{{ $stats['permissions_count'] }}</div>
        <div class="stat-label">{{ __('Available Permissions') }}</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
        </div>
        <div class="stat-value">{{ $stats['pages_count'] }}</div>
        <div class="stat-label">{{ __('Managed Pages') }}</div>
    </div>
</div>

{{-- Welcome Hero --}}
<div class="welcome-hero">
    <div class="hero-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        {{ __('Admin Dashboard') }}
    </div>
    <h2>{{ __('Welcome to') }} <span class="highlight">MOTORAZAD</span></h2>
    <p>
        {{ __('From here you can control everything — manage users, define roles and permissions, and monitor auction activity with ease and flexibility. Start your journey now.') }}
    </p>
    <div class="btn-group">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            {{ __('Manage Users') }}
        </a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('Add New Role') }}
        </a>
    </div>
</div>
@endsection
