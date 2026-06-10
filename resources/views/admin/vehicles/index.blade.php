@extends('layouts.admin')

@section('title', __('Vehicles'))

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
<div style="font-size:0.85rem; color:var(--text-muted);">{{ __('Total:') }} <span>{{ $stats['total'] }}</span> {{ __('Vehicle') }}</div>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Vehicles Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Vehicles') }}</div>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Vehicle') }}
    </button>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card blue h-100">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">{{ __('Total Vehicles') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card green h-100">
            <div class="stat-value">{{ $stats['approved'] }}</div>
            <div class="stat-label">{{ __('Approved Vehicles') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
        <div class="stat-card gold h-100">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">{{ __('Pending Review') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card red h-100">
            <div class="stat-value">{{ $stats['rejected'] }}</div>
            <div class="stat-label">{{ __('Rejected') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Vehicles List') }}</h2>
    </div>
    <div class="table-responsive">
        <table id="vehicles-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('Image') }}</th>
                    <th>{{ __('Vehicle') }}</th>
                    <th>{{ __('VIN Number') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <div class="modal-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div class="modal-title-text">
                        <span>{{ __('Add New Vehicle') }}</span>
                        <span class="modal-subtitle">{{ __('Fill in the data to create a new vehicle') }}</span>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addVehicleForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <!-- Section: صور المركبة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                            {{ __('Vehicle Images') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        {{ __('Select Images') }}
                                    </label>
                                    <div class="file-upload-zone" id="addVehicleImagesZone">
                                        <input type="file" name="images[]" accept="image/*" multiple onchange="handleMultipleFilesPreview(this, 'addVehicleImagesPreview', 'add_primary_image_index')">
                                        <input type="hidden" name="primary_image_index" id="add_primary_image_index" value="0">
                                        <div class="file-upload-content">
                                            <div class="upload-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            </div>
                                            <div class="upload-text">{{ __('Drag the images here or') }} <span class="upload-highlight">{{ __('click to choose') }}</span></div>
                                            <div class="upload-hint">{{ __('PNG, JPG, WEBP — Max size 2MB per image') }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2" id="addVehicleImagesPreview"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: معلومات الموديل والشركة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-2.2-3.3C13 5.6 12 5 10.8 5H5.6c-.8 0-1.6.5-1.9 1.2l-.9 2.1C2.3 9.5 2 10.8 2 12.1V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                            </div>
                            {{ __('Manufacturer & Model') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                                        {{ __('Manufacturer (Make)') }}
                                    </label>
                                    <input type="text" name="make" class="form-control" required placeholder="{{ __('Example: Toyota') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                                        {{ __('Model') }}
                                    </label>
                                    <input type="text" name="model" class="form-control" required placeholder="{{ __('Example: Camry') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ __('Year') }}
                                    </label>
                                    <input type="number" name="year" class="form-control" required value="{{ date('Y') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: تفاصيل التسجيل والهيكل -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            {{ __('Registration & Identity') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        {{ __('VIN Number') }}
                                    </label>
                                    <input type="text" name="vin_number" class="form-control" placeholder="VIN">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18Z"/></svg>
                                        {{ __('Color') }}
                                    </label>
                                    <input type="text" name="color" class="form-control" placeholder="{{ __('Example: Black') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="9" x2="15" y2="21"/></svg>
                                        {{ __('Plate Number') }}
                                    </label>
                                    <input type="text" name="plate_number" class="form-control" placeholder="A B C 1 2 3 4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المواصفات الفنية والحالة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            </div>
                            {{ __('Technical Specifications') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                        {{ __('Mileage') }}
                                    </label>
                                    <input type="number" name="mileage" class="form-control" placeholder="{{ __('km') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                        {{ __('Fuel Type') }}
                                    </label>
                                    <select name="fuel_type" class="form-control">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="petrol">{{ __('Petrol') }}</option>
                                        <option value="diesel">{{ __('Diesel') }}</option>
                                        <option value="electric">{{ __('Electric') }}</option>
                                        <option value="hybrid">{{ __('Hybrid') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                        {{ __('Transmission') }}
                                    </label>
                                    <select name="transmission" class="form-control">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="automatic">{{ __('Automatic') }}</option>
                                        <option value="manual">{{ __('Manual') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/></svg>
                                        {{ __('Engine Capacity') }}
                                    </label>
                                    <input type="text" name="engine_capacity" class="form-control" placeholder="{{ __('Example: 2.5L') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                        {{ __('Cylinders') }}
                                    </label>
                                    <input type="number" name="cylinders" class="form-control" placeholder="{{ __('Example: 4') }}">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        {{ __('Status') }}
                                    </label>
                                    <select name="status" class="form-control" required>
                                        <option value="approved">🟢 {{ __('Approved') }}</option>
                                        <option value="pending">📅 {{ __('Pending Review') }}</option>
                                        <option value="rejected">❌ {{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المميزات والعيوب -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            </div>
                            {{ __('Features & Issues') }}
                        </div>
                        <div class="section-body">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    {{ __('Features') }}
                                </label>
                                <div class="checkbox-grid">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="sunroof">
                                        <span class="check-label">{{ __('Sunroof') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="leather_seats">
                                        <span class="check-label">{{ __('Leather Seats') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="rear_camera">
                                        <span class="check-label">{{ __('Rear Camera') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="sensors">
                                        <span class="check-label">{{ __('Sensors') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="navigation_system">
                                        <span class="check-label">{{ __('Navigation System') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="cruise_control">
                                        <span class="check-label">{{ __('Cruise Control') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="keyless_entry">
                                        <span class="check-label">{{ __('Keyless Entry') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="bluetooth">
                                        <span class="check-label">{{ __('Bluetooth') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    {{ __('Issues / Defects') }}
                                </label>
                                <textarea name="issues" class="form-control" rows="2" placeholder="{{ __('List any issues or defects (scratch, mechanical issues, etc.). Leave empty if none.') }}"></textarea>
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
                                    <textarea name="description_ar" class="form-control" rows="3" placeholder="{{ __('Enter vehicle description in Arabic...') }}"></textarea>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="description_en" class="form-control" rows="3" placeholder="{{ __('Enter vehicle description in English...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Save Vehicle') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <div class="modal-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div class="modal-title-text">
                        <span>{{ __('Edit Vehicle') }}</span>
                        <span class="modal-subtitle">{{ __('Modify the vehicle details below') }}</span>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editVehicleForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_vehicle_id">
                <div class="modal-body">

                    <!-- Section: معرض الصور الحالي -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 6v6l4 2"/></svg>
                            </div>
                            {{ __('Current Image Gallery') }}
                        </div>
                        <div class="section-body">
                            <div class="row g-2" id="editVehicleGallery">
                                <!-- Existing images will be loaded here via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Section: إضافة صور جديدة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                            {{ __('Add New Images') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        {{ __('Select Images') }}
                                    </label>
                                    <div class="file-upload-zone" id="editVehicleImagesZone">
                                        <input type="file" name="images[]" accept="image/*" multiple onchange="handleMultipleFilesPreview(this, 'editVehicleImagesPreview', 'edit_primary_image_index')">
                                        <input type="hidden" name="primary_image_index" id="edit_primary_image_index" value="-1">
                                        <div class="file-upload-content">
                                            <div class="upload-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            </div>
                                            <div class="upload-text">{{ __('Drag the images here or') }} <span class="upload-highlight">{{ __('click to choose') }}</span></div>
                                            <div class="upload-hint">{{ __('PNG, JPG, WEBP — Max size 2MB per image') }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2" id="editVehicleImagesPreview"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: معلومات الموديل والشركة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-2.2-3.3C13 5.6 12 5 10.8 5H5.6c-.8 0-1.6.5-1.9 1.2l-.9 2.1C2.3 9.5 2 10.8 2 12.1V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                            </div>
                            {{ __('Manufacturer & Model') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                                        {{ __('Manufacturer (Make)') }}
                                    </label>
                                    <input type="text" id="edit_make" name="make" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                                        {{ __('Model') }}
                                    </label>
                                    <input type="text" id="edit_model" name="model" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ __('Year') }}
                                    </label>
                                    <input type="number" id="edit_year" name="year" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: تفاصيل التسجيل والهيكل -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-red">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            {{ __('Registration & Identity') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        {{ __('VIN Number') }}
                                    </label>
                                    <input type="text" id="edit_vin_number" name="vin_number" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18Z"/></svg>
                                        {{ __('Color') }}
                                    </label>
                                    <input type="text" id="edit_color" name="color" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="9" x2="15" y2="21"/></svg>
                                        {{ __('Plate Number') }}
                                    </label>
                                    <input type="text" id="edit_plate_number" name="plate_number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المواصفات الفنية والحالة -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            </div>
                            {{ __('Technical Specifications') }}
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                        {{ __('Mileage') }}
                                    </label>
                                    <input type="number" id="edit_mileage" name="mileage" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                        {{ __('Fuel Type') }}
                                    </label>
                                    <select id="edit_fuel_type" name="fuel_type" class="form-control">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="petrol">{{ __('Petrol') }}</option>
                                        <option value="diesel">{{ __('Diesel') }}</option>
                                        <option value="electric">{{ __('Electric') }}</option>
                                        <option value="hybrid">{{ __('Hybrid') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                        {{ __('Transmission') }}
                                    </label>
                                    <select id="edit_transmission" name="transmission" class="form-control">
                                        <option value="">{{ __('Not Specified') }}</option>
                                        <option value="automatic">{{ __('Automatic') }}</option>
                                        <option value="manual">{{ __('Manual') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/></svg>
                                        {{ __('Engine Capacity') }}
                                    </label>
                                    <input type="text" id="edit_engine_capacity" name="engine_capacity" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                        {{ __('Cylinders') }}
                                    </label>
                                    <input type="number" id="edit_cylinders" name="cylinders" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        {{ __('Status') }}
                                    </label>
                                    <select id="edit_status" name="status" class="form-control" required onchange="if(this.value === 'rejected') { $('#rejection_reason_row').show(); } else { $('#rejection_reason_row').hide(); }">
                                        <option value="approved">🟢 {{ __('Approved') }}</option>
                                        <option value="pending">📅 {{ __('Pending Review') }}</option>
                                        <option value="rejected">❌ {{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="rejection_reason_row" style="display: none;">
                                <div class="col-md-12 mb-3 form-group">
                                    <label class="form-label">
                                        <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                        {{ __('Rejection Reason') }}
                                    </label>
                                    <textarea id="edit_rejection_reason" name="rejection_reason" class="form-control" rows="2" placeholder="{{ __('Please write the reason for rejecting the vehicle...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: المميزات والعيوب -->
                    <div class="modal-form-section">
                        <div class="section-header">
                            <div class="section-icon icon-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            </div>
                            {{ __('Features & Issues') }}
                        </div>
                        <div class="section-body">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    {{ __('Features') }}
                                </label>
                                <div class="checkbox-grid">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="sunroof" id="edit_feature_sunroof">
                                        <span class="check-label">{{ __('Sunroof') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="leather_seats" id="edit_feature_leather_seats">
                                        <span class="check-label">{{ __('Leather Seats') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="rear_camera" id="edit_feature_rear_camera">
                                        <span class="check-label">{{ __('Rear Camera') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="sensors" id="edit_feature_sensors">
                                        <span class="check-label">{{ __('Sensors') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="navigation_system" id="edit_feature_navigation_system">
                                        <span class="check-label">{{ __('Navigation System') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="cruise_control" id="edit_feature_cruise_control">
                                        <span class="check-label">{{ __('Cruise Control') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="keyless_entry" id="edit_feature_keyless_entry">
                                        <span class="check-label">{{ __('Keyless Entry') }}</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="features[]" value="bluetooth" id="edit_feature_bluetooth">
                                        <span class="check-label">{{ __('Bluetooth') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    {{ __('Issues / Defects') }}
                                </label>
                                <textarea id="edit_issues" name="issues" class="form-control" rows="2" placeholder="{{ __('List any issues or defects (scratch, mechanical issues, etc.). Leave empty if none.') }}"></textarea>
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
                                    <textarea id="edit_description_ar" name="description_ar" class="form-control" rows="3" placeholder="{{ __('Enter vehicle description in Arabic...') }}"></textarea>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea id="edit_description_en" name="description_en" class="form-control" rows="3" placeholder="{{ __('Enter vehicle description in English...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ __('Update Vehicle') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    var vehiclesDataUrl = "{{ route('admin.vehicles.data') }}";
    let updateVehicleUrl = "{{ route('admin.vehicles.update', ':id') }}";
</script>

<script>
    let vehiclesTable;

    function handleMultipleFilesPreview(input, previewId, primaryInputId) {
        const previewContainer = document.getElementById(previewId);
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        const files = input.files;
        if (files && files.length > 0) {
            document.getElementById(primaryInputId).value = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3 vehicle-img-preview-col';
                    col.dataset.index = i;
                    col.style.position = 'relative';
                    col.style.cursor = 'pointer';

                    const borderStyle = (i === 0) ? '3px solid var(--brand-red)' : '1px solid var(--border)';
                    const badgeDisplay = (i === 0) ? 'block' : 'none';

                    col.innerHTML = `
                        <div class="card p-1 text-center bg-dark" style="border: ${borderStyle}; border-radius: 8px; position: relative; overflow: hidden; height:120px;">
                            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius: 6px;" alt="">
                            <span class="badge bg-danger primary-badge" style="position:absolute; top:8px; right:8px; display: ${badgeDisplay}; font-size:10px;">${"{{ __('Primary') }}"}</span>
                            <div class="hover-overlay" style="position:absolute; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; opacity:0; transition:0.2s;">
                                <span class="text-white" style="font-size:11px; font-weight:bold;">${"{{ __('Set Primary') }}"}</span>
                            </div>
                        </div>
                    `;

                    const card = col.querySelector('.card');
                    const overlay = col.querySelector('.hover-overlay');
                    col.addEventListener('mouseenter', () => overlay.style.opacity = '1');
                    col.addEventListener('mouseleave', () => overlay.style.opacity = '0');

                    col.addEventListener('click', function() {
                        document.getElementById(primaryInputId).value = i;
                        previewContainer.querySelectorAll('.vehicle-img-preview-col').forEach(el => {
                            el.querySelector('.card').style.border = '1px solid var(--border)';
                            el.querySelector('.primary-badge').style.display = 'none';
                        });
                        card.style.border = '3px solid var(--brand-red)';
                        col.querySelector('.primary-badge').style.display = 'block';
                    });

                    previewContainer.appendChild(col);
                };

                reader.readAsDataURL(file);
            }
        }
    }

    function setPrimaryVehicleImage(imageId) {
        let url = "{{ route('admin.vehicles.set-primary-image', ':id') }}".replace(':id', imageId);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    vehiclesTable.ajax.reload(null, false);
                    const gallery = $('#editVehicleGallery');
                    gallery.find('.existing-image-col').each(function() {
                        const colId = $(this).data('image-id');
                        if (colId == imageId) {
                            $(this).find('.card').css('border', '3px solid var(--brand-red)');
                            $(this).find('.primary-badge').show();
                        } else {
                            $(this).find('.card').css('border', '1px solid var(--border)');
                            $(this).find('.primary-badge').hide();
                        }
                    });
                }
            }
        });
    }

    function deleteVehicleImage(imageId) {
        let url = "{{ route('admin.vehicles.delete-image', ':id') }}".replace(':id', imageId);
        Swal.fire({
            title: "{{ __('Delete this image?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, delete!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            vehiclesTable.ajax.reload(null, false);
                            const imageCol = $(`.existing-image-col[data-image-id="${imageId}"]`);
                            imageCol.fadeOut(300, function() {
                                $(this).remove();
                                if ($('#editVehicleGallery').find('.existing-image-col').length === 0) {
                                    $('#editVehicleGallery').html('<div class="col-12 text-center text-muted p-3">' + "{{ __('No Images Available') }}" + '</div>');
                                }
                            });
                        }
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#addVehicleModal').on('show.bs.modal', function() {
            $('#addVehicleForm')[0].reset();
            $('#addVehicleImagesPreview').html('');
            $('#add_primary_image_index').val('0');
        });

        vehiclesTable = $('#vehicles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: vehiclesDataUrl,
            columns: [
                { data: 'image', orderable: false, searchable: false },
                { data: 'title' },
                { data: 'vin_number' },
                { data: 'status' },
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

        $('#addVehicleForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text("{{ __('Saving...') }}");

            $.ajax({
                url: "{{ route('admin.vehicles.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#addVehicleModal').modal('hide');
                        $('#addVehicleForm')[0].reset();
                        $('#addVehicleImagesPreview').html('');
                        vehiclesTable.ajax.reload(null, false);
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
                    btn.prop('disabled', false).text("{{ __('Save Vehicle') }}");
                }
            });
        });

        $('#editVehicleForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text("{{ __('Updating...') }}");

            const id = $('#edit_vehicle_id').val();
            const url = updateVehicleUrl.replace(':id', id);
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
                        $('#editVehicleModal').modal('hide');
                        $('#editVehicleForm')[0].reset();
                        $('#editVehicleImagesPreview').html('');
                        vehiclesTable.ajax.reload(null, false);
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
                    btn.prop('disabled', false).text("{{ __('Update Vehicle') }}");
                }
            });
        });
    });

    function editVehicle(id) {
        let url = "{{ route('admin.vehicles.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const vehicle = response.vehicle;
                $('#edit_vehicle_id').val(vehicle.id);
                $('#edit_make').val(vehicle.make);
                $('#edit_model').val(vehicle.model);
                $('#edit_year').val(vehicle.year);
                $('#edit_vin_number').val(vehicle.vin_number);
                $('#edit_color').val(vehicle.color);
                $('#edit_plate_number').val(vehicle.plate_number);
                $('#edit_mileage').val(vehicle.mileage);
                $('#edit_fuel_type').val(vehicle.fuel_type);
                $('#edit_transmission').val(vehicle.transmission);
                $('#edit_engine_capacity').val(vehicle.engine_capacity);
                $('#edit_cylinders').val(vehicle.cylinders);
                $('#edit_status').val(vehicle.status);
                $('#edit_description_ar').val(vehicle.description_ar);
                $('#edit_description_en').val(vehicle.description_en);
                $('#edit_rejection_reason').val(vehicle.rejection_reason);

                if (vehicle.status === 'rejected') {
                    $('#rejection_reason_row').show();
                } else {
                    $('#rejection_reason_row').hide();
                }

                // ضبط المميزات والعيوب في التعديل
                $('#editVehicleModal').find('input[name="features[]"]').prop('checked', false);
                if (vehicle.features && Array.isArray(vehicle.features)) {
                    vehicle.features.forEach(feature => {
                        $(`#edit_feature_${feature}`).prop('checked', true);
                    });
                }
                $('#edit_issues').val(vehicle.issues || '');

                // تهيئة معرض الصور الحالية ومعاينة الصور الجديدة
                $('#editVehicleGallery').html('');
                $('#editVehicleImagesPreview').html('');
                $('#edit_primary_image_index').val('-1');

                if (vehicle.images && vehicle.images.length > 0) {
                    vehicle.images.forEach(img => {
                        const borderStyle = img.is_primary ? '3px solid var(--brand-red)' : '1px solid var(--border)';
                        const badgeDisplay = img.is_primary ? 'block' : 'none';
                        const url = "{{ asset('storage') }}/" + img.image_path;

                        const col = $(`
                            <div class="col-6 col-sm-4 col-md-3 existing-image-col" data-image-id="${img.id}">
                                <div class="card p-1 text-center bg-dark" style="border: ${borderStyle}; border-radius: 8px; position: relative; overflow: hidden; height:120px;">
                                    <img src="${url}" style="width:100%; height:100%; object-fit:cover; border-radius: 6px;" alt="">
                                    <span class="badge bg-danger primary-badge" style="position:absolute; top:8px; right:8px; display: ${badgeDisplay}; font-size:10px;">${"{{ __('Primary') }}"}</span>
                                    <div class="image-actions" style="position:absolute; bottom:8px; left:8px; right:8px; display:flex; gap:4px; justify-content:center; opacity:0; transition:0.2s;">
                                        <button type="button" class="btn btn-sm btn-primary py-1 px-2 set-primary-btn" title="${"{{ __('Set Primary') }}"}" style="font-size:10px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>
                                        <button type="button" class="btn btn-sm btn-danger py-1 px-2 delete-image-btn" title="${"{{ __('Delete') }}"}" style="font-size:10px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                    </div>
                                </div>
                            </div>
                        `);

                        col.find('.card').hover(
                            function() { $(this).find('.image-actions').css('opacity', '1'); },
                            function() { $(this).find('.image-actions').css('opacity', '0'); }
                        );

                        col.find('.set-primary-btn').click(function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            setPrimaryVehicleImage(img.id);
                        });

                        col.find('.delete-image-btn').click(function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            deleteVehicleImage(img.id);
                        });

                        $('#editVehicleGallery').append(col);
                    });
                } else {
                    $('#editVehicleGallery').html('<div class="col-12 text-center text-muted p-3">' + "{{ __('No Images Available') }}" + '</div>');
                }
                
                $('#editVehicleModal').modal('show');
            }
        });
    }

    function deleteVehicle(id) {
        let url = "{{ route('admin.vehicles.destroy', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Delete Vehicle?') }}",
            text: "{{ __('This action cannot be undone! Make sure this vehicle is not linked to any auction.') }}",
            icon: 'warning',
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
                            vehiclesTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error("{{ __('Could not delete vehicle, it might be linked to an auction.') }}");
                    }
                });
            }
        });
    }

    function approveVehicle(id) {
        let url = "{{ route('admin.vehicles.approve', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Approve Vehicle?') }}",
            text: "{{ __('Are you sure you want to approve this vehicle?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Yes, approve!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    }
                });
            }
        });
    }

    function rejectVehicle(id) {
        let url = "{{ route('admin.vehicles.reject', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: "{{ __('Reject Vehicle') }}",
            input: 'textarea',
            inputLabel: "{{ __('Reason for Rejection') }}",
            inputPlaceholder: "{{ __('Please write the reason for rejecting the vehicle...') }}",
            inputAttributes: {
                'aria-label': "{{ __('Reason for Rejection') }}"
            },
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Reject') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            inputValidator: (value) => {
                if (!value) {
                    return "{{ __('You must write a reason for rejection!') }}";
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        rejection_reason: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
