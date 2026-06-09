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
            <form id="addVehicleForm">
                @csrf
                <div class="modal-body">

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
            <form id="editVehicleForm">
                @csrf
                <input type="hidden" id="edit_vehicle_id">
                <div class="modal-body">

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
                                    <select id="edit_status" name="status" class="form-control" required>
                                        <option value="approved">🟢 {{ __('Approved') }}</option>
                                        <option value="pending">📅 {{ __('Pending Review') }}</option>
                                        <option value="rejected">❌ {{ __('Rejected') }}</option>
                                    </select>
                                </div>
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

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#addVehicleModal').on('show.bs.modal', function() {
            $('#addVehicleForm')[0].reset();
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
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#addVehicleModal').modal('hide');
                        $('#addVehicleForm')[0].reset();
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
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editVehicleModal').modal('hide');
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
</script>
@endsection
