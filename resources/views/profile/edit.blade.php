@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.bidder')

@section('title', __('Profile'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/wallet-profile.css') }}">
<style>
/* ===== PREMIUM PROFILE MASTERPIECE STYLES ===== */
.profile-grid {
    display: grid;
    grid-template-columns: 2fr 1.1fr;
    gap: 2.5rem;
    margin-bottom: 4rem;
}
.profile-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    margin-bottom: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
}
.profile-card:hover {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
    border-color: var(--border-light);
}
.profile-card-header {
    padding: 1.75rem 2rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, rgba(229, 62, 62, 0.04), rgba(245, 158, 11, 0.02));
}
.profile-card-header h2 {
    font-size: 1.25rem;
    font-weight: 900;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: var(--text);
}
.profile-card-header h2 svg {
    color: var(--brand-red);
}
.profile-card-body {
    padding: 2.5rem;
}

/* Photo Upload Trigger */
.avatar-upload-container {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 2.5rem;
    border-radius: 50%;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 4px solid var(--bg-card);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.avatar-upload-container:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(229, 62, 62, 0.25);
}
.avatar-preview {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: #0b0f19;
}
.avatar-edit-btn {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--brand-red);
    color: white;
    border: 3px solid var(--bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: all 0.3s;
}
html[dir="rtl"] .avatar-edit-btn {
    right: auto;
    left: 2px;
}
.avatar-edit-btn:hover {
    transform: scale(1.1);
    background: #991b1b;
}

/* Form Styles */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
.form-group-full {
    grid-column: 1 / -1;
    margin-bottom: 1.5rem;
}
.form-field {
    display: flex;
    flex-direction: column;
}
.form-field label {
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.6rem;
    transition: color 0.3s;
}
.form-field:focus-within label {
    color: var(--brand-red-light);
}
.form-field input, .form-field select, .form-field textarea {
    width: 100%;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.85rem 1.1rem;
    color: var(--text);
    font-size: 0.92rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
}
.form-field input:focus, .form-field select:focus, .form-field textarea:focus {
    outline: none;
    border-color: var(--brand-red);
    background: var(--bg-card);
    box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.12), inset 0 2px 4px rgba(0,0,0,0.01);
}

/* Custom dropdown chevron aligned with language dir */
.form-field select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1.1rem center;
    background-size: 0.85rem;
    padding-right: 2.75rem;
}
html[dir="rtl"] .form-field select {
    background-position: left 1.1rem center;
    padding-left: 2.75rem;
    padding-right: 1.1rem;
}

/* Section divider titles */
.profile-section-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin: 2.25rem 0 1.25rem;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid var(--border);
    color: var(--brand-red-light);
    font-size: 0.9rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.profile-section-title svg {
    color: var(--brand-red-light);
    opacity: 0.85;
}
.profile-section-title:first-of-type {
    margin-top: 1rem;
}

.submit-btn {
    background: linear-gradient(135deg, var(--brand-red), #991b1b);
    color: white;
    border: none;
    padding: 0.9rem 3rem;
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.98rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.25);
}
.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
}
.submit-btn:active {
    transform: translateY(0);
}

.delete-zone {
    background: rgba(239, 68, 68, 0.02);
    border: 1px dashed rgba(239, 68, 68, 0.2);
    border-radius: 14px;
    padding: 1.5rem;
    transition: all 0.3s;
}
.delete-zone:hover {
    background: rgba(239, 68, 68, 0.04);
    border-color: rgba(239, 68, 68, 0.35);
}

@media(max-width: 992px) {
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
@media(max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
}
</style>
@endsection

@section('content')

{{-- ===== HERO BANNER ===== --}}
<div class="wallet-hero-card">
    <div class="wallet-hero-bg"></div>
    <div class="wallet-hero-content">
        <div class="wallet-hero-left">
            <div class="wallet-hero-avatar">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->full_name }}" id="bannerAvatar">
                <div class="wallet-verified-badge {{ $user->status === 'approved' ? 'verified' : '' }}">
                    @if($user->status === 'approved')
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @endif
                </div>
            </div>
            <div class="wallet-hero-info">
                <h1>{{ $user->full_name }}</h1>
                <div class="wallet-hero-email">{{ $user->email }}</div>
                <div class="wallet-hero-badges">
                    <span class="w-badge kyc">{{ __('KYC Level') }} {{ $user->kyc_level }}</span>
                    @if($user->status === 'approved')
                        <span class="w-badge status approved">{{ __('Verified') }}</span>
                    @elseif($user->status === 'pending')
                        <span class="w-badge status pending">{{ __('Pending') }}</span>
                    @else
                        <span class="w-badge status rejected">{{ __('Not Verified') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="profile-grid">
    {{-- Left: Edit Details --}}
    <div>
        <div class="profile-card">
            <div class="profile-card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ __('Profile Information') }}
                </h2>
            </div>
            
            <div class="profile-card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileUpdateForm">
                    @csrf
                    @method('PATCH')
                    
                    {{-- Avatar upload trigger --}}
                    <div class="avatar-upload-container">
                        <img src="{{ $user->profile_photo_url }}" alt="Avatar Preview" class="avatar-preview" id="avatarPreview">
                        <label for="avatarInput" class="avatar-edit-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </label>
                        <input type="file" name="profile_photo" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                    </div>

                    {{-- Section 1: Core Details --}}
                    <div class="profile-section-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'معلومات الحساب الأساسية' : 'Core Account Details' }}</span>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="name">{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'Username' }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="{{ __('Username') }}">
                            @error('name')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="{{ __('Email') }}">
                            @error('email')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Section 2: Personal Details --}}
                    <div class="profile-section-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'البيانات الشخصية والتعريفية' : 'Personal Details' }}</span>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="first_name">{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }}</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="{{ __('First Name') }}">
                            @error('first_name')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="form-field">
                            <label for="last_name">{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Last Name' }}</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="{{ __('Last Name') }}">
                            @error('last_name')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="date_of_birth">{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth' }}</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="gender">{{ app()->getLocale() === 'ar' ? 'الجنس' : 'Gender' }}</label>
                            <select name="gender" id="gender">
                                <option value="" disabled selected>{{ app()->getLocale() === 'ar' ? 'اختر الجنس' : 'Select Gender' }}</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'ذكر' : 'Male' }}</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'أنثى' : 'Female' }}</option>
                                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'آخر' : 'Other' }}</option>
                            </select>
                            @error('gender')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Section 3: Contact & Address --}}
                    <div class="profile-section-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'معلومات الاتصال والموقع' : 'Contact & Location' }}</span>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="phone">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone' }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +966 50 000 0000">
                            @error('phone')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="address">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" placeholder="{{ __('Address') }}">
                            @error('address')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="country">{{ app()->getLocale() === 'ar' ? 'البلد' : 'Country' }}</label>
                            <input type="text" name="country" id="country" value="{{ old('country', $user->country) }}" placeholder="{{ __('Country') }}">
                            @error('country')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="city">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}" placeholder="{{ __('City') }}">
                            @error('city')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="submit-btn" id="updateProfileBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: Password & Settings --}}
    <div>
        {{-- Password Card --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    {{ __('Update Password') }}
                </h2>
            </div>
            
            <div class="profile-card-body">
                <form method="POST" action="{{ route('password.update') }}" id="passwordUpdateForm">
                    @csrf
                    @method('put')

                    <div class="form-group-full">
                        <div class="form-field">
                            <label for="update_password_current_password">{{ __('Current Password') }}</label>
                            <input type="password" name="current_password" id="update_password_current_password" required placeholder="••••••••">
                            @error('current_password', 'updatePassword')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group-full">
                        <div class="form-field">
                            <label for="update_password_password">{{ __('New Password') }}</label>
                            <input type="password" name="password" id="update_password_password" required placeholder="••••••••">
                            @error('password', 'updatePassword')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group-full">
                        <div class="form-field">
                            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" id="update_password_password_confirmation" required placeholder="••••••••">
                            @error('password_confirmation', 'updatePassword')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="submit-btn" style="width: 100%; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Account Card --}}
        <div class="profile-card" style="border-color: rgba(239, 68, 68, 0.25);">
            <div class="profile-card-header" style="background: rgba(239, 68, 68, 0.03);">
                <h2 style="color: #ef4444;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #ef4444;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    {{ __('Delete Account') }}
                </h2>
            </div>
            
            <div class="profile-card-body">
                <div class="delete-zone">
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>
                    <button class="submit-btn" style="background: #ef4444; width: 100%; justify-content: center;" onclick="confirmDeleteAccount()">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Account Modal simulation --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl);">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title" style="font-weight: 800; color: #ef4444;">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="btn-close" onclick="closeDeleteModal()" style="filter: var(--theme-close-btn-filter);"></button>
                </div>
                
                <div class="modal-body" style="padding: 1.5rem;">
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    
                    <div class="form-field">
                        <label for="password">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" required placeholder="••••••••">
                        @error('password', 'userDeletion')<span style="color:#ef4444; font-size:.75rem; margin-top:0.25rem;">{{ $message }}</span>@enderror
                    </div>
                </div>
                
                <div class="modal-footer" style="border-top: 1px solid var(--border); display: flex; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()" style="font-weight: 700;">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" style="font-weight: 800; background: #ef4444;">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade" id="modalBackdrop" style="display: none;"></div>

@endsection

@section('js')
<script>
// Avatar instant upload preview
function previewAvatar(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
        document.getElementById('bannerAvatar').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Delete account modal functions
function confirmDeleteAccount() {
    document.getElementById('deleteAccountModal').style.display = 'block';
    document.getElementById('deleteAccountModal').classList.add('show');
    document.getElementById('modalBackdrop').style.display = 'block';
    document.getElementById('modalBackdrop').classList.add('show');
    document.body.classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteAccountModal').style.display = 'none';
    document.getElementById('deleteAccountModal').classList.remove('show');
    document.getElementById('modalBackdrop').style.display = 'none';
    document.getElementById('modalBackdrop').classList.remove('show');
    document.body.classList.remove('modal-open');
}

// Toast status indicators
@if(session('status') === 'profile-updated')
    toastr.success('{{ __("Profile updated successfully.") }}');
@endif

@if(session('status') === 'password-updated')
    toastr.success('{{ __("Password updated successfully.") }}');
@endif

@if($errors->userDeletion->any())
    document.addEventListener('DOMContentLoaded', function() {
        confirmDeleteAccount();
    });
@endif

$(document).ready(function() {
    const savingText = "{{ app()->getLocale() === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}";

    // AJAX Profile Form Update
    $('#profileUpdateForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#updateProfileBtn');
        const originalHtml = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + savingText);

        // Clear error highlights & messages
        form.find('span[style*="#ef4444"], .error-msg').remove();
        form.find('input, select, textarea').css('border-color', '');

        const formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    toastr.success(response.message || 'Profile updated successfully.');
                    
                    // Update user info across UI
                    if (response.user) {
                        $('.wallet-hero-info h1').text(response.user.full_name);
                        $('.wallet-hero-email').text(response.user.email);
                        $('#bannerAvatar').attr('src', response.user.profile_photo_url);
                        $('#avatarPreview').attr('src', response.user.profile_photo_url);
                        
                        // Update topbar & sidebar user display
                        $('.topbar .user-name, .sidebar-user .name').text(response.user.full_name);
                        $('.topbar .avatar, .sidebar-user .avatar').each(function() {
                            if ($(this).find('img').length) {
                                $(this).find('img').attr('src', response.user.profile_photo_url);
                            } else {
                                $(this).text(response.user.full_name.charAt(0).toUpperCase());
                            }
                        });
                    }
                } else {
                    toastr.error('Failed to update profile.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        if (input.length) {
                            input.css('border-color', '#ef4444');
                            input.after(`<span class="error-msg text-danger mt-1 small" style="color:#ef4444; font-size:.75rem; margin-top:0.25rem; display:block;">${messages[0]}</span>`);
                        }
                    });
                    toastr.error("{{ __('Please correct the errors below.') }}");
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });

    // AJAX Password Form Update
    $('#passwordUpdateForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalHtml = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + savingText);

        // Clear error highlights & messages
        form.find('span[style*="#ef4444"], .error-msg').remove();
        form.find('input').css('border-color', '');

        const formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    toastr.success(response.message || 'Password updated successfully.');
                    // Clear inputs
                    form.find('input[type="password"]').val('');
                } else {
                    toastr.error('Failed to update password.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        if (input.length) {
                            input.css('border-color', '#ef4444');
                            input.after(`<span class="error-msg text-danger mt-1 small" style="color:#ef4444; font-size:.75rem; margin-top:0.25rem; display:block;">${messages[0]}</span>`);
                        }
                    });
                    toastr.error("{{ __('Please correct the errors below.') }}");
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>
<style>
.modal-open { overflow: hidden; }
.modal.show { display: block; opacity: 1; }
.modal-backdrop.show { opacity: 0.5; }
</style>
@endsection
