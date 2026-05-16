@extends('layouts.bidder')
@section('title', __('Identity Verification'))
@section('css')
<style>
/* ===== KYC PAGE STYLES ===== */
.kyc-hero { background: linear-gradient(135deg, rgba(26,26,46,.95), rgba(22,33,62,.98)); border: 1px solid rgba(255,255,255,.08); border-radius: var(--radius-xl); padding: 2.5rem; color: white; position: relative; overflow: hidden; margin-bottom: 1.5rem; }
.kyc-hero::before { content:''; position:absolute; inset:0; background: radial-gradient(circle at 80% 20%, rgba(245,158,11,.15), transparent 50%), radial-gradient(circle at 20% 80%, rgba(59,130,246,.15), transparent 50%); pointer-events:none; }
.kyc-hero-inner { position:relative; z-index:2; display:flex; align-items:center; justify-content:space-between; gap:2rem; flex-wrap:wrap; }
.kyc-hero-text h1 { font-size:2rem; font-weight:900; margin-bottom:.5rem; }
.kyc-hero-text p { opacity:.75; font-size:1rem; }
.kyc-level-badge { text-align:center; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12); border-radius:var(--radius-xl); padding:1.5rem 2.5rem; backdrop-filter:blur(12px); }
.kyc-level-num { font-family:'Orbitron',sans-serif; font-size:3rem; font-weight:900; background:linear-gradient(135deg,#f59e0b,#f97316); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; line-height:1; }
.kyc-level-label { font-size:.75rem; text-transform:uppercase; letter-spacing:2px; opacity:.6; margin-top:.25rem; }

/* Steps */
.kyc-steps { display:flex; gap:0; margin-bottom:2rem; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-xl); overflow:hidden; }
.kyc-step { flex:1; padding:1.25rem 1rem; text-align:center; display:flex; align-items:center; gap:.75rem; justify-content:center; border-right:1px solid var(--border); font-size:.85rem; font-weight:700; color:var(--text-muted); transition:all .3s; }
.kyc-step:last-child { border-right:none; }
.kyc-step.active { background:rgba(229,62,62,.08); color:var(--brand-red-light); border-bottom:3px solid var(--brand-red); }
.kyc-step.done { background:rgba(16,185,129,.07); color:#10b981; }
.kyc-step-num { width:28px; height:28px; border-radius:50%; background:var(--bg-input); display:flex; align-items:center; justify-content:center; font-size:.8rem; font-family:'Orbitron',sans-serif; flex-shrink:0; }
.kyc-step.active .kyc-step-num { background:var(--brand-red); color:white; }
.kyc-step.done .kyc-step-num { background:#10b981; color:white; }

/* Status Cards */
.kyc-status-card { border-radius:var(--radius-xl); padding:3rem 2rem; text-align:center; border:1px solid var(--border); background:var(--bg-card); }
.kyc-status-icon { width:90px; height:90px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
.kyc-status-icon svg { width:44px; height:44px; }
.kyc-status-icon.pending { background:rgba(245,158,11,.15); color:#f59e0b; box-shadow:0 0 40px rgba(245,158,11,.2); animation:pulse-slow 3s infinite; }
.kyc-status-icon.approved { background:rgba(16,185,129,.15); color:#10b981; box-shadow:0 0 40px rgba(16,185,129,.2); }
.kyc-status-icon.rejected { background:rgba(239,68,68,.15); color:#ef4444; box-shadow:0 0 40px rgba(239,68,68,.2); }
@keyframes pulse-slow { 0%,100%{transform:scale(1);} 50%{transform:scale(1.05);} }
.kyc-status-card h2 { font-size:1.6rem; font-weight:900; margin-bottom:.75rem; }
.kyc-status-card p { color:var(--text-muted); max-width:400px; margin:0 auto; line-height:1.6; }
.kyc-timeline { display:flex; align-items:center; justify-content:center; gap:1rem; margin-top:2rem; flex-wrap:wrap; }
.kyc-tl-item { display:flex; align-items:center; gap:.5rem; font-size:.8rem; color:var(--text-muted); }
.kyc-tl-dot { width:10px; height:10px; border-radius:50%; }

/* Form */
.kyc-form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-xl); overflow:hidden; }
.kyc-form-header { padding:1.5rem; border-bottom:1px solid var(--border); background:linear-gradient(135deg, rgba(229,62,62,.06), rgba(245,158,11,.04)); }
.kyc-form-header h2 { font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:.75rem; }
.kyc-form-body { padding:2rem; }
.kyc-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.kyc-field label { display:block; font-size:.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:.5rem; }
.kyc-field input, .kyc-field select { width:100%; background:var(--bg-input); border:1px solid var(--border); border-radius:10px; padding:.75rem 1rem; color:var(--text); font-size:.9rem; transition:all .3s; }
.kyc-field input:focus, .kyc-field select:focus { outline:none; border-color:var(--brand-red); box-shadow:0 0 0 3px rgba(229,62,62,.1); }
.kyc-field.full { grid-column:1/-1; }

/* Upload Zones */
.upload-zones { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-top:1.5rem; }
.upload-zone { border:2px dashed var(--border); border-radius:var(--radius-xl); padding:2rem 1rem; text-align:center; cursor:pointer; transition:all .3s; position:relative; overflow:hidden; }
.upload-zone:hover, .upload-zone.dragover { border-color:var(--brand-red); background:rgba(229,62,62,.05); }
.upload-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
.upload-zone-icon { width:60px; height:60px; margin:0 auto 1rem; border-radius:50%; background:var(--bg-hover); display:flex; align-items:center; justify-content:center; transition:all .3s; }
.upload-zone:hover .upload-zone-icon { background:rgba(229,62,62,.12); color:var(--brand-red-light); }
.upload-zone-icon svg { width:28px; height:28px; color:var(--text-secondary); }
.upload-zone h4 { font-size:.9rem; font-weight:800; margin-bottom:.35rem; }
.upload-zone p { font-size:.75rem; color:var(--text-muted); line-height:1.4; }
.upload-preview { display:none; margin-top:1rem; }
.upload-preview img { max-height:100px; border-radius:8px; border:1px solid var(--border); }
.upload-name { font-size:.75rem; color:#10b981; margin-top:.5rem; font-weight:600; }

/* Rejected Note */
.kyc-rejected-note { background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); border-radius:var(--radius-lg); padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:1rem; align-items:flex-start; }
.kyc-rejected-note svg { flex-shrink:0; color:#ef4444; margin-top:.15rem; }

/* Security badges */
.kyc-security { display:flex; gap:1rem; margin-top:1.5rem; flex-wrap:wrap; }
.kyc-sec-badge { display:flex; align-items:center; gap:.5rem; font-size:.75rem; color:var(--text-muted); background:var(--bg-hover); border:1px solid var(--border); border-radius:8px; padding:.5rem .85rem; }
.kyc-sec-badge svg { width:14px; height:14px; color:#10b981; }

/* Submit btn */
.kyc-submit { display:flex; align-items:center; justify-content:space-between; padding:1.5rem 2rem; border-top:1px solid var(--border); background:var(--bg-hover); flex-wrap:wrap; gap:1rem; }
.btn-kyc-submit { background:linear-gradient(135deg,var(--brand-red),#991b1b); color:white; border:none; padding:.85rem 2.5rem; border-radius:12px; font-weight:800; font-size:1rem; cursor:pointer; display:flex; align-items:center; gap:.75rem; transition:all .3s; }
.btn-kyc-submit:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(229,62,62,.4); }

@media(max-width:768px){ .kyc-grid,.upload-zones{grid-template-columns:1fr;} .kyc-steps{flex-direction:column;} .kyc-steps .kyc-step{border-right:none; border-bottom:1px solid var(--border);} }
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="kyc-hero">
    <div class="kyc-hero-inner">
        <div class="kyc-hero-text">
            <div class="hero-badge" style="margin-bottom:1rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                {{ __('KYC Verification') }}
            </div>
            <h1>{{ __('Identity Verification') }}</h1>
            <p>{{ __('Verify your identity to unlock all platform features and increase your trust level.') }}</p>
            <div class="kyc-security">
                <span class="kyc-sec-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>{{ __('End-to-End Encrypted') }}</span>
                <span class="kyc-sec-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>{{ __('Data Protected') }}</span>
                <span class="kyc-sec-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>{{ __('Reviewed by Admins') }}</span>
            </div>
        </div>
        <div class="kyc-level-badge">
            <div class="kyc-level-num">{{ $user->kyc_level }}</div>
            <div class="kyc-level-label">{{ __('KYC Level') }}</div>
            <div style="margin-top:.75rem;">
                @if($user->status === 'approved')
                    <span class="w-badge status approved">✅ {{ __('Verified') }}</span>
                @elseif($user->status === 'pending')
                    <span class="w-badge status pending">⏳ {{ __('Pending') }}</span>
                @else
                    <span class="w-badge status rejected">❌ {{ __('Not Verified') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Steps --}}
<div class="kyc-steps">
    <div class="kyc-step {{ (!$latestRequest || $latestRequest->status === 'rejected') ? 'active' : 'done' }}">
        <div class="kyc-step-num">{{ (!$latestRequest || $latestRequest->status === 'rejected') ? '1' : '✓' }}</div>
        <span>{{ __('Submit Documents') }}</span>
    </div>
    <div class="kyc-step {{ $latestRequest && $latestRequest->status === 'pending' ? 'active' : ($latestRequest && $latestRequest->status !== 'rejected' ? 'done' : '') }}">
        <div class="kyc-step-num">2</div>
        <span>{{ __('Under Review') }}</span>
    </div>
    <div class="kyc-step {{ $latestRequest && $latestRequest->status === 'approved' ? 'done' : '' }}">
        <div class="kyc-step-num">{{ $latestRequest && $latestRequest->status === 'approved' ? '✓' : '3' }}</div>
        <span>{{ __('Verified') }}</span>
    </div>
</div>

{{-- Content by Status --}}
@if(!$latestRequest || $latestRequest->status === 'rejected')

    @if($latestRequest && $latestRequest->status === 'rejected')
    <div class="kyc-rejected-note">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <strong style="display:block; margin-bottom:.25rem; color:#ef4444;">{{ __('Your previous request was rejected') }}</strong>
            <span style="font-size:.85rem; color:var(--text-muted);">{{ $latestRequest->admin_note ?? __('Please re-submit with clearer documents.') }}</span>
        </div>
    </div>
    @endif

    <form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data" id="kycForm">
        @csrf
        <div class="kyc-form-card">
            <div class="kyc-form-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ __('Personal Information') }}
                </h2>
            </div>
            <div class="kyc-form-body">
                <div class="kyc-grid">
                    <div class="kyc-field">
                        <label>{{ __('Full Name (as in ID)') }}</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required placeholder="{{ __('Full legal name') }}">
                        @error('full_name')<span style="color:#ef4444; font-size:.75rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="kyc-field">
                        <label>{{ __('Country') }}</label>
                        <input type="text" name="country" value="{{ old('country', $user->country) }}" required placeholder="{{ __('e.g. Saudi Arabia') }}">
                        @error('country')<span style="color:#ef4444; font-size:.75rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="kyc-field">
                        <label>{{ __('ID / Residence Number') }}</label>
                        <input type="text" name="id_number" value="{{ old('id_number', $user->id_number) }}" required placeholder="{{ __('National ID number') }}">
                        @error('id_number')<span style="color:#ef4444; font-size:.75rem;">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Upload Zones --}}
                <div class="upload-zones">
                    <div class="upload-zone" id="idZone">
                        <input type="file" name="id_image" accept="image/*" required id="idInput" onchange="previewFile(this,'idPreview','idName')">
                        <div class="upload-zone-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 10h8M8 14h4"/></svg>
                        </div>
                        <h4>{{ __('ID Card Front') }}</h4>
                        <p>{{ __('Upload a clear photo of the front of your national ID card.') }}</p>
                        <div class="upload-preview" id="idPreview"><img src="" alt="preview"><div class="upload-name" id="idName"></div></div>
                        @error('id_image')<span style="color:#ef4444; font-size:.75rem; display:block; margin-top:.5rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="upload-zone" id="selfieZone">
                        <input type="file" name="selfie_image" accept="image/*" required id="selfieInput" onchange="previewFile(this,'selfiePreview','selfieName')">
                        <div class="upload-zone-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </div>
                        <h4>{{ __('Selfie with ID') }}</h4>
                        <p>{{ __('Upload a selfie holding your ID next to your face clearly.') }}</p>
                        <div class="upload-preview" id="selfiePreview"><img src="" alt="preview"><div class="upload-name" id="selfieName"></div></div>
                        @error('selfie_image')<span style="color:#ef4444; font-size:.75rem; display:block; margin-top:.5rem;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="kyc-submit">
                <div style="font-size:.8rem; color:var(--text-muted);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; margin-inline-end:.35rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ __('Your data is encrypted and secure') }}
                </div>
                <button type="submit" class="btn-kyc-submit" id="kycSubmitBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ __('Submit for Verification') }}
                </button>
            </div>
        </div>
    </form>

@elseif($latestRequest->status === 'pending')
    <div class="kyc-status-card">
        <div class="kyc-status-icon pending">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h2>{{ __('Under Review') }}</h2>
        <p>{{ __('Your documents are being reviewed by our team. You will be notified once the process is complete. This usually takes 1-3 business days.') }}</p>
        <div class="kyc-timeline">
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:#10b981;"></div>{{ __('Submitted') }}</div>
            <div style="flex:1; max-width:60px; height:2px; background:linear-gradient(90deg,#10b981,#f59e0b);"></div>
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:#f59e0b;"></div>{{ __('In Review') }}</div>
            <div style="flex:1; max-width:60px; height:2px; background:var(--border);"></div>
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:var(--border);"></div>{{ __('Verified') }}</div>
        </div>
        <p style="margin-top:1.5rem; font-size:.8rem;">{{ __('Submitted on') }}: <strong>{{ $latestRequest->created_at->translatedFormat('d M Y - h:i A') }}</strong></p>
    </div>

@elseif($latestRequest->status === 'approved')
    <div class="kyc-status-card">
        <div class="kyc-status-icon approved">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
        </div>
        <h2>{{ __('Identity Verified!') }}</h2>
        <p>{{ __('Your identity has been verified successfully. You now have full access to all platform features including bidding and wallet operations.') }}</p>
        <div class="kyc-timeline" style="margin-top:2rem;">
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:#10b981;"></div>{{ __('Submitted') }}</div>
            <div style="flex:1; max-width:60px; height:2px; background:#10b981;"></div>
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:#10b981;"></div>{{ __('Reviewed') }}</div>
            <div style="flex:1; max-width:60px; height:2px; background:#10b981;"></div>
            <div class="kyc-tl-item"><div class="kyc-tl-dot" style="background:#10b981;"></div>{{ __('Verified') }} ✅</div>
        </div>
        <div style="margin-top:2rem;">
            <a href="{{ route('bidder.dashboard') }}" class="btn-kyc-submit" style="display:inline-flex; text-decoration:none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                {{ __('Go to Dashboard') }}
            </a>
        </div>
    </div>
@endif

@endsection

@section('js')
<script>
function previewFile(input, previewId, nameId) {
    const file = input.files[0];
    if (!file) return;
    const preview = document.getElementById(previewId);
    const nameEl = document.getElementById(nameId);
    preview.style.display = 'block';
    preview.querySelector('img').src = URL.createObjectURL(file);
    nameEl.textContent = '✓ ' + file.name;
    input.closest('.upload-zone').style.borderColor = '#10b981';
}

document.getElementById('kycForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('kycSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg> {{ __("Uploading...") }}';
});

// Drag & drop
document.querySelectorAll('.upload-zone').forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');
        const input = zone.querySelector('input[type=file]');
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
