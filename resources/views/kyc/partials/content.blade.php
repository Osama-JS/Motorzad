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
                        <input type="file" name="id_image" accept="image/*" required id="idInput">
                        <div class="upload-zone-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 10h8M8 14h4"/></svg>
                        </div>
                        <h4>{{ __('ID Card Front') }}</h4>
                        <p>{{ __('Upload a clear photo of the front of your national ID card.') }}</p>
                        <div class="upload-preview" id="idPreview"><img src="" alt="preview"><div class="upload-name" id="idName"></div></div>
                        @error('id_image')<span style="color:#ef4444; font-size:.75rem; display:block; margin-top:.5rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="upload-zone" id="selfieZone">
                        <input type="file" name="selfie_image" accept="image/*" required id="selfieInput">
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
        <div style="margin-top:2.5rem; display:flex; justify-content:center; gap:1rem; flex-wrap:wrap;">
            <a href="{{ route('bidder.auctions.index') }}" class="btn-kyc-submit" style="display:inline-flex; text-decoration:none; background:linear-gradient(135deg, var(--brand-red), #991b1b); align-items:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-inline-end:.5rem;"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                {{ app()->getLocale() === 'ar' ? 'ابدأ المزايدة الآن' : 'Start Bidding Now' }}
            </a>
            <a href="{{ route('bidder.dashboard') }}" class="btn-kyc-submit" style="display:inline-flex; text-decoration:none; background:var(--bg-hover); border:1px solid var(--border); color:var(--text); align-items:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-inline-end:.5rem;"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                {{ __('Go to Dashboard') }}
            </a>
        </div>
    </div>
@endif
