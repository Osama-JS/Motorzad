@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'إنشاء تذكرة جديدة' : 'Create New Ticket')

@section('css')
<style>
/* Page Layout */
.support-create-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    margin-top: 1rem;
}

/* Info Sidebar */
.support-info-col {
    flex: 1;
    min-width: 300px;
    max-width: 400px;
}
.info-card {
    background: linear-gradient(145deg, var(--bg-card) 0%, rgba(15, 23, 42, 0.6) 100%);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.info-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(229, 62, 62, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    z-index: 0;
}
.info-content {
    position: relative;
    z-index: 1;
}
.info-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 20px rgba(229, 62, 62, 0.3);
}
.info-title {
    font-size: 1.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
    color: var(--text);
}
.info-text {
    color: var(--text-muted);
    line-height: 1.6;
    margin-bottom: 2rem;
    font-size: 0.95rem;
}
.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: var(--text);
    font-weight: 600;
}
.feature-item i {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Form Container */
.support-form-col {
    flex: 2;
    min-width: 300px;
}
.form-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 3rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.05);
}
.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}
.form-title {
    font-size: 1.5rem;
    font-weight: 900;
    margin: 0;
}
.btn-back {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
    text-decoration: none;
    font-weight: 700;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    background: var(--bg-body);
    transition: all 0.3s;
}
.btn-back:hover {
    color: var(--text);
    background: var(--border);
}

/* Modern Inputs with Icons inside */
.form-group {
    margin-bottom: 2rem;
    position: relative;
}
.form-label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 800;
    color: var(--text);
    font-size: 0.95rem;
}
.input-icon-wrapper {
    position: relative;
}
.input-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 1.1rem;
    transition: color 0.3s;
}
html[dir="rtl"] .input-icon { right: 1.25rem; }
html[dir="ltr"] .input-icon { left: 1.25rem; }

.form-control {
    width: 100%;
    padding: 1rem 1.25rem;
    background: var(--bg-body);
    border: 2px solid transparent;
    border-radius: 14px;
    color: var(--text);
    font-size: 1rem;
    transition: all 0.3s ease;
}
html[dir="rtl"] .form-control { padding-right: 3rem; }
html[dir="ltr"] .form-control { padding-left: 3rem; }

textarea.form-control {
    padding-top: 1.25rem;
}
html[dir="rtl"] textarea.form-control ~ .input-icon { top: 1.5rem; transform: none; }
html[dir="ltr"] textarea.form-control ~ .input-icon { top: 1.5rem; transform: none; }

.form-control:focus {
    outline: none;
    border-color: var(--brand-red);
    background: var(--bg-card);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}
.form-control:focus + .input-icon,
.form-control:not(:placeholder-shown) + .input-icon {
    color: var(--brand-red);
}

.help-text {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-submit {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    padding: 1.2rem;
    border: none;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    width: 100%;
    font-size: 1.1rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 10px 20px rgba(229, 62, 62, 0.25);
    margin-top: 1rem;
}
.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(229, 62, 62, 0.35);
}

@media(max-width: 768px) {
    .support-info-col {
        max-width: 100%;
    }
    .form-card {
        padding: 1.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="support-create-wrapper">
    
    <!-- Left Sidebar: Info Card -->
    <div class="support-info-col">
        <div class="info-card">
            <div class="info-content">
                <div class="info-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="info-title">{{ app()->getLocale() === 'ar' ? 'كيف يمكننا مساعدتك؟' : 'How can we help?' }}</h3>
                <p class="info-text">
                    {{ app()->getLocale() === 'ar' ? 'نحن متواجدون للرد على جميع استفساراتك وحل أي مشكلات تواجهك في منصة موتورزاد بأسرع وقت ممكن.' : 'We are here to answer all your inquiries and solve any issues you face on the Motorzad platform as quickly as possible.' }}
                </p>
                <ul class="feature-list">
                    <li class="feature-item">
                        <i class="fas fa-bolt"></i>
                        {{ app()->getLocale() === 'ar' ? 'استجابة سريعة' : 'Fast Response' }}
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        {{ app()->getLocale() === 'ar' ? 'حماية بياناتك' : 'Data Protection' }}
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-user-tie"></i>
                        {{ app()->getLocale() === 'ar' ? 'فريق متخصص' : 'Dedicated Team' }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Side: The Form -->
    <div class="support-form-col">
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">{{ app()->getLocale() === 'ar' ? 'تذكرة دعم جديدة' : 'New Support Ticket' }}</h2>
                <a href="{{ route('bidder.support.index') }}" class="btn-back">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> 
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
            
            <form action="{{ route('bidder.support.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="subject">
                        {{ app()->getLocale() === 'ar' ? 'موضوع المشكلة' : 'Subject' }}
                    </label>
                    <div class="input-icon-wrapper">
                        <input type="text" name="subject" id="subject" class="form-control" required value="{{ old('subject') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل عنواناً مختصراً (مثال: مشكلة في شحن المحفظة)' : 'Enter a brief subject...' }}">
                        <i class="fas fa-heading input-icon"></i>
                    </div>
                    @error('subject')
                        <div class="text-danger mt-2 text-sm font-weight-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">
                        {{ app()->getLocale() === 'ar' ? 'التفاصيل الكاملة' : 'Full Details' }}
                    </label>
                    <div class="input-icon-wrapper">
                        <textarea name="message" id="message" rows="6" class="form-control" required placeholder="{{ app()->getLocale() === 'ar' ? 'اشرح لنا المشكلة بالتفصيل لنتمكن من مساعدتك...' : 'Explain the issue in detail...' }}">{{ old('message') }}</textarea>
                        <i class="fas fa-align-right input-icon"></i>
                    </div>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ app()->getLocale() === 'ar' ? 'يرجى تقديم تفاصيل كافية مثل أرقام العمليات أو المزادات المعنية لتسريع الحل.' : 'Please provide sufficient details such as transaction or auction IDs to speed up resolution.' }}
                    </div>
                    @error('message')
                        <div class="text-danger mt-2 text-sm font-weight-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    {{ app()->getLocale() === 'ar' ? 'تأكيد وإرسال التذكرة' : 'Submit Ticket' }}
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
