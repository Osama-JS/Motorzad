@extends('layouts.landing')

@section('title', __('Contact Us') . ' - Motorzad')

@push('styles')
<style>
/* ─── Contact Page Styles ─── */
.contact-page {
    padding-top: 5rem;
    min-height: 100vh;
    background: var(--bg);
    overflow-x: hidden;
}

.contact-hero {
    position: relative;
    padding: 4rem 1rem 3rem;
    text-align: center;
    background:
        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(229,62,62,0.08) 0%, transparent 70%),
        var(--bg-card);
    border-bottom: 1px solid var(--border);
    margin-bottom: 3rem;
}

.contact-hero h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 900;
    color: var(--text);
    margin-bottom: 1rem;
}
.contact-hero h1 .accent {
    background: linear-gradient(135deg, var(--red), var(--gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.contact-hero p {
    color: var(--text-sec);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.1rem;
    line-height: 1.6;
}

.contact-layout {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 5rem;
}

/* ─── Contact Info Cards ─── */
.contact-info-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.info-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 2rem;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    transition: var(--transition);
}
.info-card:hover {
    border-color: rgba(229,62,62,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transform: translateY(-2px);
}
.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(229,62,62,0.1);
    color: var(--red);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.info-icon svg {
    width: 24px;
    height: 24px;
}
.info-content h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.5rem;
}
.info-content p {
    color: var(--text-sec);
    font-size: 1rem;
    margin: 0;
    line-height: 1.5;
}

/* ─── Contact Form ─── */
.contact-form-container {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.02);
}
.contact-form-container h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.5rem;
}
.contact-form-container > p {
    color: var(--text-sec);
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.5rem;
}
.form-control {
    width: 100%;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.9rem 1.2rem;
    color: var(--text);
    font-family: inherit;
    transition: var(--transition);
}
.form-control:focus {
    outline: none;
    border-color: var(--red);
    box-shadow: 0 0 0 4px rgba(229,62,62,0.1);
}
textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-submit {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: var(--radius);
    background: var(--red);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
}
.btn-submit:hover {
    background: var(--red-light);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(229,62,62,0.25);
}

@media (max-width: 992px) {
    .contact-layout {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .contact-form-container {
        padding: 2rem 1.5rem;
    }
    .info-card {
        padding: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<main class="contact-page">

    <!-- Hero -->
    <div class="contact-hero">
        <div class="section-container">
            <h1>{{ __('Contact') }} <span class="accent">{{ __('Us') }}</span></h1>
            <p>{{ __('We are here to help you. Whether you have an inquiry about auctions, need technical support, or want to share feedback, reach out to us!') }}</p>
        </div>
    </div>

    <div class="section-container">
        <div class="contact-layout">
            
            <!-- Left Side: Contact Info -->
            <div class="contact-info">
                <div class="contact-info-list">
                    <div class="info-card">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div class="info-content">
                            <h3>{{ __('Phone Number') }}</h3>
                            <p dir="ltr">{{ \App\Models\Setting::get('support_phone', '+966 50 000 0000') }}</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div class="info-content">
                            <h3>{{ __('Email Address') }}</h3>
                            <p>{{ \App\Models\Setting::get('support_email', 'support@motorzad.com') }}</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="info-content">
                            <h3>{{ __('Head Office') }}</h3>
                            <p>{{ app()->getLocale() === 'ar' ? 'الرياض، المملكة العربية السعودية' : 'Riyadh, Saudi Arabia' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="contact-form-container">
                <h2>{{ __('Send us a Message') }}</h2>
                <p>{{ __('Fill out the form below and our support team will get back to you shortly.') }}</p>

                <form id="contactForm" action="{{ route('frontend.contact.store') }}" method="POST">
                    @csrf
                    <!-- Alert placeholders -->
                    <div id="formSuccess" class="alert alert-success" style="display:none; padding:1rem; border-radius:8px; background:rgba(34,197,94,0.1); color:#16a34a; margin-bottom:1.5rem; border:1px solid #bbf7d0;"></div>
                    <div id="formError" class="alert alert-danger" style="display:none; padding:1rem; border-radius:8px; background:rgba(239,68,68,0.1); color:#dc2626; margin-bottom:1.5rem; border:1px solid #fecaca;"></div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Full Name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Enter your full name') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Email Address') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Enter your email') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Subject') }}</label>
                        <input type="text" name="subject" class="form-control" placeholder="{{ __('How can we help you?') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Message') }}</label>
                        <textarea name="message" class="form-control" placeholder="{{ __('Type your message here...') }}" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text">{{ __('Send Message') }}</span>
                        <span class="btn-loader" style="display:none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin"><circle cx="12" cy="12" r="10"/><path d="M12 2v4"/></svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<style>
.spin { animation: spin 1s linear infinite; }
@keyframes spin { 100% { transform: rotate(360deg); } }
.btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
</style>
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    const successAlert = document.getElementById('formSuccess');
    const errorAlert = document.getElementById('formError');

    // Reset alerts
    successAlert.style.display = 'none';
    errorAlert.style.display = 'none';
    
    // Loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            successAlert.textContent = data.message;
            successAlert.style.display = 'block';
            form.reset();
        } else {
            errorAlert.textContent = data.message || '{{ __("An error occurred. Please try again.") }}';
            errorAlert.style.display = 'block';
        }
    })
    .catch(error => {
        errorAlert.textContent = '{{ __("An error occurred. Please try again.") }}';
        errorAlert.style.display = 'block';
    })
    .finally(() => {
        submitBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';
    });
});
</script>
@endpush
@endsection
