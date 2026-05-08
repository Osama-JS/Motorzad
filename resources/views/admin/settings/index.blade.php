@extends('layouts.admin')
@section('title', 'إعدادات النظام')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Settings') }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Settings') }}
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem;">
    <div class="stat-card red">
        <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></div>
        <div class="stat-value">{{ $stats['total_settings'] }}</div>
        <div class="stat-label">{{ __('Total Settings') }}</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div class="stat-value" style="font-size:1rem;">{{ $stats['last_updated'] }}</div>
        <div class="stat-label">{{ __('Last Updated') }}</div>
    </div>
    <div class="stat-card {{ \App\Models\Setting::get('maintenance_mode')=='1' ? 'blue' : 'green' }}">
        <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
        <div class="stat-value" style="font-size:1rem;">{{ \App\Models\Setting::get('maintenance_mode')=='1' ? __('Maintenance') : __('Active') }}</div>
        <div class="stat-label">{{ __('Site Status') }}</div>
    </div>
</div>

<form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="two-col" style="grid-template-columns: 260px 1fr;">
    {{-- Sidebar Navigation --}}
    <div class="col-aside">
        <div class="card" style="position:sticky; top:90px;">
            <div class="card-body" style="padding:0.5rem;">
                <div class="settings-nav">
                    <a href="#" class="settings-nav-item active" data-tab="general">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span>{{ __('Site Identity') }}</span>
                    </a>
                    <a href="#" class="settings-nav-item" data-tab="about">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>{{ __('About Us') }}</span>
                    </a>
                    <a href="#" class="settings-nav-item" data-tab="social">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span>{{ __('Social Media') }}</span>
                    </a>
                    <a href="#" class="settings-nav-item" data-tab="apps">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        <span>{{ __('Mobile Apps') }}</span>
                    </a>
                    <a href="#" class="settings-nav-item" data-tab="system">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        <span>{{ __('System') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Panels --}}
    <div class="col-wide">
        {{-- General --}}
        <div class="settings-panel active" id="panel-general">
            <div class="card">
                <div class="card-header"><h2>{{ __('Site Identity and Basic Info') }}</h2></div>
                <div class="card-body">
                    <div class="row"><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Site Name (Arabic)') }}</label>
                        <input type="text" name="site_name_ar" class="form-control" value="{{ \App\Models\Setting::get('site_name_ar') }}">
                    </div><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Site Name (English)') }}</label>
                        <input type="text" name="site_name_en" class="form-control" value="{{ \App\Models\Setting::get('site_name_en') }}" dir="ltr">
                    </div></div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Description (Arabic)') }}</label>
                        <textarea name="site_description_ar" class="form-control" rows="2">{{ \App\Models\Setting::get('site_description_ar') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Description (English)') }}</label>
                        <textarea name="site_description_en" class="form-control" rows="2" dir="ltr">{{ \App\Models\Setting::get('site_description_en') }}</textarea>
                    </div>
                    <div class="row"><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Email Address') }}</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ \App\Models\Setting::get('contact_email') }}">
                    </div><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Phone Number') }}</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ \App\Models\Setting::get('contact_phone') }}" dir="ltr">
                    </div></div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Primary Color') }}</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" id="colorPicker" name="primary_color" value="{{ \App\Models\Setting::get('primary_color', '#3b4bd3') }}" style="width:50px;height:40px;border:1px solid var(--border-light);border-radius:var(--radius);cursor:pointer;padding:2px;">
                            <code id="colorCode" style="color:var(--text-secondary);">{{ \App\Models\Setting::get('primary_color', '#3b4bd3') }}</code>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top:1.25rem;">
                <div class="card-header"><h2>{{ __('Logo and Favicon') }}</h2></div>
                <div class="card-body">
                    <div class="row"><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Site Logo') }}</label>
                        <div class="upload-zone" onclick="this.querySelector('input').click()">
                            <img id="logo-preview" src="{{ \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : 'https://placehold.co/200x80/0e1421/94a3b8?text=Logo' }}">
                            <span>{{ __('Click to change logo') }}</span>
                            <input type="file" name="site_logo" hidden onchange="previewImage(this,'logo-preview')">
                        </div>
                    </div><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Site Favicon') }}</label>
                        <div class="upload-zone" onclick="this.querySelector('input').click()">
                            <img id="favicon-preview" src="{{ \App\Models\Setting::get('site_favicon') ? asset(\App\Models\Setting::get('site_favicon')) : 'https://placehold.co/64x64/0e1421/94a3b8?text=F' }}" style="max-height:48px;">
                            <span>{{ __('Click to change favicon') }}</span>
                            <input type="file" name="site_favicon" hidden onchange="previewImage(this,'favicon-preview')">
                        </div>
                    </div></div>
                </div>
            </div>
        </div>

        {{-- About --}}
        <div class="settings-panel" id="panel-about">
            <div class="card">
                <div class="card-header"><h2>{{ __('Our Story') }}</h2></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Our Story (Arabic)') }}</label>
                        <textarea name="story_ar" class="form-control" rows="5">{{ \App\Models\Setting::get('story_ar') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Our Story (English)') }}</label>
                        <textarea name="story_en" class="form-control" rows="5" dir="ltr">{{ \App\Models\Setting::get('story_en') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top:1.25rem;">
                <div class="card-header"><h2>{{ __('Mission and Vision') }}</h2></div>
                <div class="card-body">
                    <div class="row"><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Mission (Arabic)') }}</label>
                        <textarea name="mission_ar" class="form-control" rows="3">{{ \App\Models\Setting::get('mission_ar') }}</textarea>
                    </div><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Mission (English)') }}</label>
                        <textarea name="mission_en" class="form-control" rows="3" dir="ltr">{{ \App\Models\Setting::get('mission_en') }}</textarea>
                    </div></div>
                    <div class="row"><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Vision (Arabic)') }}</label>
                        <textarea name="vision_ar" class="form-control" rows="3">{{ \App\Models\Setting::get('vision_ar') }}</textarea>
                    </div><div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Vision (English)') }}</label>
                        <textarea name="vision_en" class="form-control" rows="3" dir="ltr">{{ \App\Models\Setting::get('vision_en') }}</textarea>
                    </div></div>
                </div>
            </div>
        </div>

        {{-- Social --}}
        <div class="settings-panel" id="panel-social">
            <div class="card">
                <div class="card-header"><h2>{{ __('Social Media Links') }}</h2></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Facebook') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#1877f2;"><i class="fa-brands fa-facebook-f"></i></span>
                            <input type="url" name="facebook_url" class="form-control" value="{{ \App\Models\Setting::get('facebook_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Twitter (X)') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#1da1f2;"><i class="fa-brands fa-x-twitter"></i></span>
                            <input type="url" name="twitter_url" class="form-control" value="{{ \App\Models\Setting::get('twitter_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Instagram') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#e4405f;"><i class="fa-brands fa-instagram"></i></span>
                            <input type="url" name="instagram_url" class="form-control" value="{{ \App\Models\Setting::get('instagram_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('LinkedIn') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#0a66c2;"><i class="fa-brands fa-linkedin-in"></i></span>
                            <input type="url" name="linkedin_url" class="form-control" value="{{ \App\Models\Setting::get('linkedin_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('TikTok') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#000000;"><i class="fa-brands fa-tiktok"></i></span>
                            <input type="url" name="tiktok_url" class="form-control" value="{{ \App\Models\Setting::get('tiktok_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Snapchat') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#FFFC00; text-shadow: 0px 0px 1px #000;"><i class="fa-brands fa-snapchat"></i></span>
                            <input type="url" name="snapchat_url" class="form-control" value="{{ \App\Models\Setting::get('snapchat_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('YouTube') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#FF0000;"><i class="fa-brands fa-youtube"></i></span>
                            <input type="url" name="youtube_url" class="form-control" value="{{ \App\Models\Setting::get('youtube_url') }}" dir="ltr"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('WhatsApp') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#25D366;"><i class="fa-brands fa-whatsapp"></i></span>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ \App\Models\Setting::get('whatsapp_number') }}" dir="ltr" placeholder="Example: +966500000000"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Telegram') }}</label>
                            <div class="input-with-icon"><span class="input-icon" style="color:#0088cc;"><i class="fa-brands fa-telegram"></i></span>
                            <input type="url" name="telegram_url" class="form-control" value="{{ \App\Models\Setting::get('telegram_url') }}" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Apps --}}
        <div class="settings-panel" id="panel-apps">
            <div class="card">
                <div class="card-header"><h2>{{ __('Mobile Apps') }}</h2></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Minimum Required Version') }}</label>
                        <input type="text" name="app_min_version" class="form-control" value="{{ \App\Models\Setting::get('app_min_version') }}" dir="ltr" style="max-width:200px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Google Play Link (Android)') }}</label>
                        <input type="url" name="android_url" class="form-control" value="{{ \App\Models\Setting::get('android_url') }}" dir="ltr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('App Store Link (iOS)') }}</label>
                        <input type="url" name="ios_url" class="form-control" value="{{ \App\Models\Setting::get('ios_url') }}" dir="ltr">
                    </div>
                </div>
            </div>
        </div>

        {{-- System --}}
        <div class="settings-panel" id="panel-system">
            <div class="card">
                <div class="card-header"><h2>{{ __('System Settings') }}</h2></div>
                <div class="card-body">
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <strong>{{ __('Maintenance Mode') }}</strong>
                            <span>{{ __('Show maintenance page to all visitors') }}</span>
                        </div>
                        <label class="switch"><input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\Setting::get('maintenance_mode')=='1'?'checked':'' }}><span class="slider"></span></label>
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <strong>{{ __('Hotels Page') }}</strong>
                            <span>{{ __('Show hotels section in the frontend') }}</span>
                        </div>
                        <label class="switch"><input type="checkbox" name="show_hotels_page" value="1" {{ \App\Models\Setting::get('show_hotels_page')=='1'?'checked':'' }}><span class="slider"></span></label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary px-5" id="saveBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span id="saveBtnText">{{ __('Save Changes') }}</span>
                <span id="saveBtnLoading" class="d-none">{{ __('Saving...') }}</span>
            </button>
        </div>
    </div>
</div>
</form>
@endsection

@section('css')
<style>
.settings-nav{display:flex;flex-direction:column;gap:2px;}
.settings-nav-item{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;color:var(--text-secondary);text-decoration:none;border-radius:var(--radius);font-weight:500;font-size:.88rem;transition:var(--transition);position:relative;}
.settings-nav-item:hover{background:var(--bg-hover);color:var(--text);}
.settings-nav-item.active{background:var(--brand-red-glow);color:var(--brand-red-light);font-weight:700;}
.settings-nav-item.active::before{content:'';position:absolute;right:0;top:25%;height:50%;width:3px;background:var(--brand-red);border-radius:3px 0 0 3px;}
.settings-nav-item svg{opacity:.6;flex-shrink:0;}
.settings-nav-item.active svg,.settings-nav-item:hover svg{opacity:1;}
.settings-panel{display:none;animation:panelIn .35s ease;}
.settings-panel.active{display:block;}
@keyframes panelIn{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
.upload-zone{border:2px dashed var(--border-light);border-radius:var(--radius-lg);padding:1.5rem;text-align:center;cursor:pointer;transition:var(--transition);background:var(--bg-input);}
.upload-zone:hover{border-color:var(--brand-red);background:var(--brand-red-glow);}
.upload-zone img{max-height:70px;margin-bottom:.5rem;object-fit:contain;}
.upload-zone span{display:block;font-size:.78rem;color:var(--text-muted);}
.input-with-icon{position:relative;}
.input-with-icon .input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-weight:900;font-size:1.1rem;z-index:2;}
.input-with-icon .form-control{padding-left:2.5rem;}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 0;border-bottom:1px solid var(--border);}
.toggle-row:last-child{border-bottom:none;}
.toggle-info strong{display:block;font-size:.9rem;margin-bottom:.15rem;}
.toggle-info span{font-size:.78rem;color:var(--text-muted);}
.switch{position:relative;width:52px;height:28px;flex-shrink:0;}
.switch input{opacity:0;width:0;height:0;}
.slider{position:absolute;inset:0;background:var(--bg-input);border:1px solid var(--border-light);border-radius:100px;cursor:pointer;transition:.3s;}
.slider::before{content:'';position:absolute;height:20px;width:20px;right:3px;bottom:3px;background:var(--text-muted);border-radius:50%;transition:.3s;}
.switch input:checked+.slider{background:var(--brand-red);border-color:var(--brand-red);}
.switch input:checked+.slider::before{transform:translateX(-24px);background:#fff;}
@media(max-width:1024px){.two-col{grid-template-columns:1fr !important;}}
@media(max-width:768px){
    .settings-nav{flex-direction:row;overflow-x:auto;gap:4px;padding-bottom:0.5rem;-webkit-overflow-scrolling:touch;}
    .settings-nav-item{white-space:nowrap;padding:.6rem .85rem;font-size:.8rem;}
    .settings-nav-item.active::before{display:none;}
    .col-aside .card{position:static !important;}
    .upload-zone{padding:1rem;}
    .toggle-row{flex-wrap:wrap;gap:0.75rem;padding:1rem 0;}
    .toggle-info{flex:1;min-width:200px;}
}
</style>
@endsection

@section('js')
<script>
function previewImage(input,id){
    if(input.files&&input.files[0]){var r=new FileReader();r.onload=function(e){document.getElementById(id).src=e.target.result;};r.readAsDataURL(input.files[0]);}
}
$(function(){
    // Tab navigation
    $('.settings-nav-item').on('click',function(e){
        e.preventDefault();
        $('.settings-nav-item').removeClass('active');
        $(this).addClass('active');
        $('.settings-panel').removeClass('active');
        $('#panel-'+$(this).data('tab')).addClass('active');
    });
    // Color picker sync
    $('#colorPicker').on('input',function(){$('#colorCode').text(this.value);});
    // AJAX Submit
    $('#settings-form').on('submit',function(e){
        e.preventDefault();
        var btn=$('#saveBtn'),fd=new FormData(this);
        if(!fd.has('maintenance_mode'))fd.append('maintenance_mode','0');
        if(!fd.has('show_hotels_page'))fd.append('show_hotels_page','0');
        btn.prop('disabled',true);$('#saveBtnText').addClass('d-none');$('#saveBtnLoading').removeClass('d-none');
        $.ajax({url:$(this).attr('action'),method:'POST',data:fd,processData:false,contentType:false,
            success:function(r){
                if(r.success){Swal.fire({icon:'success',title:'تم بنجاح',text:r.message,timer:2000,showConfirmButton:false});
                if(r.logo_url)$('#logo-preview').attr('src',r.logo_url);if(r.favicon_url)$('#favicon-preview').attr('src',r.favicon_url);}
                else Swal.fire({icon:'error',title:'خطأ',text:r.message});
            },
            error:function(x){Swal.fire({icon:'error',title:'خطأ',text:x.responseJSON?.message||'حدث خطأ'});},
            complete:function(){btn.prop('disabled',false);$('#saveBtnText').removeClass('d-none');$('#saveBtnLoading').addClass('d-none');}
        });
    });
});
</script>
@endsection
