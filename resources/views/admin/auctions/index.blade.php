@extends('layouts.admin')

@section('title', __('Auctions'))

@section('css')
<style>
    .modal-backdrop { --bs-backdrop-zindex: 0 !important; }
    .modal { z-index: 1050 !important; }
    .dataTables_wrapper { padding: 1rem; color: var(--text-color); }
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        background-color: var(--bg-input);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 12px;
    }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: var(--text-muted); margin-top: 1rem; }
    .table td { vertical-align: middle; }
    .form-group label { margin-bottom: 0.5rem; display: block; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); }
    .table-responsive { border-radius: var(--radius-lg); border: 1px solid var(--border); margin-top: 0.5rem; }
</style>
@endsection

@section('actions')
<div style="font-size:0.85rem; color:var(--text-muted);">{{ __('Total:') }} <span>{{ $stats['total'] }}</span> {{ __('Auction') }}</div>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Auctions Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Auctions') }}</div>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuctionModal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Auction') }}
    </button>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card blue h-100">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">{{ __('Total Auctions') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-value">{{ $stats['live'] }}</div>
            <div class="stat-label">{{ __('Live Auctions') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
        <div class="stat-card gold h-100">
            <div class="stat-value">{{ $stats['scheduled'] }}</div>
            <div class="stat-label">{{ __('Scheduled Auctions') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card p-3 border rounded shadow-sm h-100 bg-white">
            <div class="stat-value text-info font-weight-bold fs-4">{{ $stats['completed'] }}</div>
            <div class="stat-label text-muted">{{ __('Completed Auctions') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Auctions List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="auctions-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('Image') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Vehicle') }}</th>
                    <th>{{ __('Start Price') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Start Time') }}</th>
                    <th>{{ __('End Time') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Auction Modal -->
<div class="modal fade" id="addAuctionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <div class="modal-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div class="modal-title-text">
                        <span>{{ __('Add New Auction') }}</span>
                        <span class="modal-subtitle">{{ __('Fill in the data to create a new auction') }}</span>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addAuctionForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <!-- Section: صورة ومركبة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                            {{ __('Image & Vehicle') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        {{ __('Auction Image (Optional)') }}
                                    </label>
                                    <div class="file-upload-zone" id="addImageZone">
                                        <input type="file" name="image" accept="image/*" onchange="handleFilePreview(this, 'addImagePreview')">
                                        <div class="file-upload-content">
                                            <div class="upload-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            </div>
                                            <div class="upload-text">{{ __('Drag the image here or') }} <span class="upload-highlight">{{ __('click to choose') }}</span></div>
                                            <div class="upload-hint">{{ __('PNG, JPG, WEBP — Max size 2MB') }}</div>
                                        </div>
                                    </div>
                                    <div class="file-upload-preview" id="addImagePreview">
                                        <img class="preview-thumb" src="" alt="preview">
                                        <div class="preview-info">
                                            <div class="preview-name"></div>
                                            <div class="preview-size"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-2.2-3.3C13 5.6 12 5 10.8 5H5.6c-.8 0-1.6.5-1.9 1.2l-.9 2.1C2.3 9.5 2 10.8 2 12.1V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                                        {{ __('Vehicle') }}
                                    </label>
                                    <select name="vehicle_id" class="form-control" required>
                                        <option value="">{{ __('Select Vehicle...') }}</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->title }} ({{ $vehicle->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المعلومات الأساسية -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </div>
                            {{ __('Basic Information') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4a2 2 0 012-2h8.5L20 7.5V20a2 2 0 01-2 2H6a2 2 0 01-2-2v-3"/></svg>
                                        {{ __('Title (Arabic)') }}
                                    </label>
                                    <input type="text" name="title_ar" class="form-control" placeholder="{{ __('Enter auction title in Arabic') }}" required>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4a2 2 0 012-2h8.5L20 7.5V20a2 2 0 01-2 2H6a2 2 0 01-2-2v-3"/></svg>
                                        {{ __('Title (English)') }}
                                    </label>
                                    <input type="text" name="title_en" class="form-control" placeholder="{{ __('Enter auction title in English') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ __('Location') }}
                                    </label>
                                    <input type="text" name="location" class="form-control" placeholder="{{ __('Example: Riyadh, Saudi Arabia') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: التسعير -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            {{ __('Pricing and Finance') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                        {{ __('Start Price') }}
                                    </label>
                                    <input type="number" step="0.01" name="start_price" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        {{ __('Reserve Price') }}
                                    </label>
                                    <input type="number" step="0.01" name="reserve_price" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        {{ __('Deposit Amount') }}
                                    </label>
                                    <input type="number" step="0.01" name="deposit_amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                        {{ __('Minimum Bid Increment') }}
                                    </label>
                                    <input type="number" step="0.01" name="min_bid_increment" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                        {{ __('Buy Now Price') }}
                                    </label>
                                    <input type="number" step="0.01" name="buy_now_price" class="form-control" placeholder="{{ __('Optional') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="14.31" y1="8" x2="20.05" y2="17.94"/><line x1="9.69" y1="8" x2="21.17" y2="8"/></svg>
                                        {{ __('Commission Rate (%)') }}
                                    </label>
                                    <input type="number" step="0.01" name="commission_rate" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: التوقيت -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            {{ __('Timing and Scheduling') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                                        {{ __('Start Time') }}
                                    </label>
                                    <input type="datetime-local" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ __('End Time') }}
                                    </label>
                                    <input type="datetime-local" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                                        {{ __('Auto Extend (Minutes)') }}
                                    </label>
                                    <input type="number" name="auto_extend_minutes" class="form-control" value="0" placeholder="0">
                                </div>
                                <div class="col-md-6 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        {{ __('Status') }}
                                    </label>
                                    <select name="status" class="form-control" required>
                                        <option value="draft">📝 {{ __('Draft') }}</option>
                                        <option value="scheduled">📅 {{ __('Scheduled') }}</option>
                                        <option value="live">🟢 {{ __('Live') }}</option>
                                        <option value="completed">✅ {{ __('Completed') }}</option>
                                        <option value="cancelled">❌ {{ __('Cancelled') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: الإعدادات -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            </div>
                            {{ __('Settings') }}
                        </div>
                        <div class="section-body">
                            <div class="toggle-switch-group">
                                <label class="toggle-switch-item" for="add_deposit_required">
                                    <div class="toggle-label">
                                        <div class="toggle-icon icon-gold">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        </div>
                                        <span class="toggle-label-text">{{ __('Deposit Required?') }}</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="deposit_required" id="add_deposit_required" value="1">
                                        <span class="toggle-slider"></span>
                                    </div>
                                </label>
                                <label class="toggle-switch-item" for="add_is_featured">
                                    <div class="toggle-label">
                                        <div class="toggle-icon icon-red">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        </div>
                                        <span class="toggle-label-text">{{ __('Featured Auction?') }}</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="is_featured" id="add_is_featured" value="1">
                                        <span class="toggle-slider"></span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section: الوصف -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            {{ __('Detailed Description') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea name="description_ar" class="form-control" rows="3" placeholder="{{ __('Enter auction description in Arabic...') }}"></textarea>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="description_en" class="form-control" rows="3" placeholder="{{ __('Enter auction description in English...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Save Auction') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Auction Modal -->
<div class="modal fade" id="editAuctionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <div class="modal-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div class="modal-title-text">
                        <span>{{ __('Edit Auction') }}</span>
                        <span class="modal-subtitle">{{ __('Modify the auction details below') }}</span>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAuctionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_auction_id">
                <div class="modal-body">

                    <!-- Section: صورة ومركبة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                            {{ __('Image & Vehicle') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        {{ __('Auction Image (Optional)') }}
                                    </label>
                                    <div class="file-upload-zone" id="editImageZone">
                                        <input type="file" name="image" accept="image/*" onchange="handleFilePreview(this, 'editImagePreview')">
                                        <div class="file-upload-content">
                                            <div class="upload-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            </div>
                                            <div class="upload-text">{{ __('Drag the image here or') }} <span class="upload-highlight">{{ __('click to choose') }}</span></div>
                                            <div class="upload-hint">{{ __('PNG, JPG, WEBP — Max size 2MB') }}</div>
                                            <div class="upload-hint text-warning mt-1">{{ __('Leave blank to keep current image') }}</div>
                                        </div>
                                    </div>
                                    <div class="file-upload-preview" id="editImagePreview">
                                        <img class="preview-thumb" src="" alt="preview">
                                        <div class="preview-info">
                                            <div class="preview-name"></div>
                                            <div class="preview-size"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-2.2-3.3C13 5.6 12 5 10.8 5H5.6c-.8 0-1.6.5-1.9 1.2l-.9 2.1C2.3 9.5 2 10.8 2 12.1V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                                        {{ __('Vehicle') }}
                                    </label>
                                    <select id="edit_vehicle_id" name="vehicle_id" class="form-control" required>
                                        <option value="">{{ __('Select Vehicle...') }}</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->title }} ({{ $vehicle->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المعلومات الأساسية -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </div>
                            {{ __('Basic Information') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4a2 2 0 012-2h8.5L20 7.5V20a2 2 0 01-2 2H6a2 2 0 01-2-2v-3"/></svg>
                                        {{ __('Title (Arabic)') }}
                                    </label>
                                    <input type="text" id="edit_title_ar" name="title_ar" class="form-control" placeholder="{{ __('Enter auction title in Arabic') }}" required>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4a2 2 0 012-2h8.5L20 7.5V20a2 2 0 01-2 2H6a2 2 0 01-2-2v-3"/></svg>
                                        {{ __('Title (English)') }}
                                    </label>
                                    <input type="text" id="edit_title_en" name="title_en" class="form-control" placeholder="{{ __('Enter auction title in English') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ __('Location') }}
                                    </label>
                                    <input type="text" id="edit_location" name="location" class="form-control" placeholder="{{ __('Example: Riyadh, Saudi Arabia') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: التسعير والمالية -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            {{ __('Pricing and Finance') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                        {{ __('Start Price') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_start_price" name="start_price" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        {{ __('Reserve Price') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_reserve_price" name="reserve_price" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        {{ __('Deposit Amount') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_deposit_amount" name="deposit_amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                        {{ __('Minimum Bid Increment') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_min_bid_increment" name="min_bid_increment" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                        {{ __('Buy Now Price') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_buy_now_price" name="buy_now_price" class="form-control" placeholder="{{ __('Optional') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="14.31" y1="8" x2="20.05" y2="17.94"/><line x1="9.69" y1="8" x2="21.17" y2="8"/></svg>
                                        {{ __('Commission Rate (%)') }}
                                    </label>
                                    <input type="number" step="0.01" id="edit_commission_rate" name="commission_rate" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: التوقيت والجدولة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            {{ __('Timing and Scheduling') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                                        {{ __('Start Time') }}
                                    </label>
                                    <input type="datetime-local" id="edit_start_time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ __('End Time') }}
                                    </label>
                                    <input type="datetime-local" id="edit_end_time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                                        {{ __('Auto Extend (Minutes)') }}
                                    </label>
                                    <input type="number" id="edit_auto_extend_minutes" name="auto_extend_minutes" class="form-control" placeholder="0">
                                </div>
                                <div class="col-md-6 mb-0 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        {{ __('Status') }}
                                    </label>
                                    <select id="edit_status" name="status" class="form-control" required>
                                        <option value="draft">📝 {{ __('Draft') }}</option>
                                        <option value="scheduled">📅 {{ __('Scheduled') }}</option>
                                        <option value="live">🟢 {{ __('Live') }}</option>
                                        <option value="completed">✅ {{ __('Completed') }}</option>
                                        <option value="cancelled">❌ {{ __('Cancelled') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: الإعدادات -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2 2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            </div>
                            {{ __('Settings') }}
                        </div>
                        <div class="section-body">
                            <div class="toggle-switch-group">
                                <label class="toggle-switch-item" for="edit_deposit_required">
                                    <div class="toggle-label">
                                        <div class="toggle-icon icon-gold">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        </div>
                                        <span class="toggle-label-text">{{ __('Deposit Required?') }}</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="deposit_required" id="edit_deposit_required" value="1">
                                        <span class="toggle-slider"></span>
                                    </div>
                                </label>
                                <label class="toggle-switch-item" for="edit_is_featured">
                                    <div class="toggle-label">
                                        <div class="toggle-icon icon-red">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        </div>
                                        <span class="toggle-label-text">{{ __('Featured Auction?') }}</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="is_featured" id="edit_is_featured" value="1">
                                        <span class="toggle-slider"></span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section: الوصف -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            {{ __('Detailed Description') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea id="edit_description_ar" name="description_ar" class="form-control" rows="3" placeholder="{{ __('Enter auction description in Arabic...') }}"></textarea>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea id="edit_description_en" name="description_en" class="form-control" rows="3" placeholder="{{ __('Enter auction description in English...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Update Auction') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    var auctionsDataUrl = "{{ route('admin.auctions.data') }}";
    let updateAuctionUrl = "{{ route('admin.auctions.update', ':id') }}";
</script>

<script>
    let auctionsTable;

    function formatDateTimeForInput(dateStr) {
        if (!dateStr) return '';
        let d = new Date(dateStr);
        let tzoffset = (new Date()).getTimezoneOffset() * 60000;
        let localISOTime = (new Date(d - tzoffset)).toISOString().slice(0, 16);
        return localISOTime;
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#addAuctionModal').on('show.bs.modal', function() {
            $('#addAuctionForm')[0].reset();
        });

        auctionsTable = $('#auctions-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: auctionsDataUrl,
            columns: [
                { data: 'image', orderable: false, searchable: false },
                { data: 'title' },
                { data: 'vehicle' },
                { data: 'start_price' },
                { data: 'status' },
                { data: 'start_time' },
                { data: 'end_time' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "sProcessing": "{{ __('Loading...') }}",
                "sLengthMenu": "{{ __('Show _MENU_ entries') }}",
                "sZeroRecords": "{{ __('No matching records found') }}",
                "sInfo": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                "sSearch": "{{ __('Search:') }}",
                "oPaginate": {
                    "sFirst": "{{ __('First') }}",
                    "sPrevious": "{{ __('Previous') }}",
                    "sNext": "{{ __('Next') }}",
                    "sLast": "{{ __('Last') }}"
                }
            }
        });

        $('#addAuctionForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text("{{ __('Saving...') }}");

            $.ajax({
                url: "{{ route('admin.auctions.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#addAuctionModal').modal('hide');
                        $('#addAuctionForm')[0].reset();
                        auctionsTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error("{{ __('An unexpected error occurred') }}");
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('Save Auction') }}");
                }
            });
        });

        $('#editAuctionForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text("{{ __('Updating...') }}");

            const id = $('#edit_auction_id').val();
            const url = updateAuctionUrl.replace(':id', id);
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editAuctionModal').modal('hide');
                        auctionsTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error("{{ __('An unexpected error occurred') }}");
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('Update Auction') }}");
                }
            });
        });
    });

    function editAuction(id) {
        let url = "{{ route('admin.auctions.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const auction = response.auction;
                $('#edit_auction_id').val(auction.id);
                $('#edit_title_ar').val(auction.title_ar);
                $('#edit_title_en').val(auction.title_en);
                $('#edit_vehicle_id').val(auction.vehicle_id);
                $('#edit_start_price').val(auction.start_price);
                $('#edit_reserve_price').val(auction.reserve_price);
                $('#edit_deposit_amount').val(auction.deposit_amount);
                $('#edit_min_bid_increment').val(auction.min_bid_increment);
                $('#edit_status').val(auction.status);
                $('#edit_description_ar').val(auction.description_ar);
                $('#edit_description_en').val(auction.description_en);
                
                // الحقول الجديدة
                $('#edit_location').val(auction.location);
                $('#edit_buy_now_price').val(auction.buy_now_price);
                $('#edit_auto_extend_minutes').val(auction.auto_extend_minutes);
                $('#edit_commission_rate').val(auction.commission_rate);
                $('#edit_deposit_required').prop('checked', auction.deposit_required);
                $('#edit_is_featured').prop('checked', auction.is_featured);
                
                $('#edit_start_time').val(formatDateTimeForInput(auction.start_time));
                $('#edit_end_time').val(formatDateTimeForInput(auction.end_time));
                
                $('#editAuctionModal').modal('show');
            }
        });
    }

    function deleteAuction(id) {
        let url = "{{ route('admin.auctions.destroy', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Delete Auction?') }}",
            text: "{{ __('This action cannot be undone!') }}",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, delete!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            auctionsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
