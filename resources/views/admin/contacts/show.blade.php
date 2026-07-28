@extends('layouts.admin')
@section('title', __('View Message'))

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('View Message') }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.contacts.index') }}">{{ __('Contact Messages') }}</a> / 
            {{ __('Message Details') }}
        </div>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h3 class="mb-0 fw-bold">{{ $contact->subject }}</h3>
        <span class="badge bg-light text-dark border px-3 py-2" dir="ltr">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $contact->created_at->format('Y-m-d H:i') }}
        </span>
    </div>
    <div class="card-body p-4">
        <div class="row mb-4 g-3 bg-light rounded p-3 border">
            <div class="col-md-6 d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-muted small">{{ __('Sender Name') }}</div>
                    <div class="fw-bold">{{ $contact->name }}</div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center gap-2">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <div class="text-muted small">{{ __('Email Address') }}</div>
                    <a href="mailto:{{ $contact->email }}" class="fw-bold text-decoration-none">{{ $contact->email }}</a>
                </div>
            </div>
        </div>
        
        <h5 class="fw-bold mb-3">{{ __('Message Content') }}</h5>
        <div class="message-content p-4 rounded" style="background: var(--bg-body); border: 1px solid var(--border-light); white-space: pre-wrap; font-size: 1.05rem; line-height: 1.7; color: var(--text-color);">{{ $contact->message }}</div>
        
        <div class="mt-4 pt-4 border-top d-flex justify-content-between align-items-center gap-2">
            <a href="mailto:{{ $contact->email }}?subject=RE: {{ $contact->subject }}" class="btn btn-primary px-4">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                {{ __('Reply via Email') }}
            </a>
            
            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    {{ __('Delete Message') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
