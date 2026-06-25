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
<div id="kyc-container">
    @include('kyc.partials.content')
</div>
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

function loadKycContent(url) {
    $('#kyc-container').css('opacity', '0.5');

    BidderAjax.get(url, {}, {
        onSuccess: function(response) {
            $('#kyc-container').css('opacity', '1');
            if (response.success && response.html) {
                $('#kyc-container').html(response.html);
                initKycEventListeners();
                window.history.pushState(null, null, url);
            } else {
                toastr.error('Failed to load identity verification page.');
            }
        },
        onError: function() {
            $('#kyc-container').css('opacity', '1');
            toastr.error('Failed to load identity verification page.');
        }
    });
}

function initKycEventListeners() {
    // Form submit handler via AJAX
    const form = document.getElementById('kycForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('kycSubmitBtn');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg> {{ __("Uploading...") }}';

            BidderAjax.post(form.action, new FormData(form), {
                onSuccess: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        loadKycContent("{{ route('kyc.index') }}");
                    } else {
                        toastr.error(data.message || '{{ __("حدث خطأ") }}');
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                },
                onError: function() {
                    toastr.error('{{ __("حدث خطأ أثناء الرفع") }}');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            });
        });
    }

    // Drag & drop
    document.querySelectorAll('.upload-zone').forEach(zone => {
        // Clear old event listeners by removing elements and cloning, but standard addEventListener on re-rendered content is fine.
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
            const input = zone.querySelector('input[type=file]');
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            input.files = dt.files;
            
            // Trigger onChange preview inline function
            const previewId = input.id === 'idInput' ? 'idPreview' : 'selfiePreview';
            const nameId = input.id === 'idInput' ? 'idName' : 'selfieName';
            previewFile(input, previewId, nameId);
        });
    });

    // Make sure file inputs trigger previewFile on manual file selection as well
    const idInput = document.getElementById('idInput');
    if (idInput) {
        idInput.addEventListener('change', function() {
            previewFile(this, 'idPreview', 'idName');
        });
    }

    const selfieInput = document.getElementById('selfieInput');
    if (selfieInput) {
        selfieInput.addEventListener('change', function() {
            previewFile(this, 'selfiePreview', 'selfieName');
        });
    }
}

$(document).ready(function() {
    initKycEventListeners();

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        loadKycContent(window.location.href);
    });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
