@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'تذاكر الدعم الفني' : 'Support Tickets')

@section('css')
<style>
/* Page Header Section */
.support-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 3rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 3rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}
.support-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: radial-gradient(circle, rgba(229, 62, 62, 0.15) 0%, transparent 60%);
    transform: rotate(-45deg);
    pointer-events: none;
}
.support-hero::after {
    content: '';
    position: absolute;
    bottom: -20%;
    left: -10%;
    width: 40%;
    height: 100%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 60%);
    pointer-events: none;
}
.hero-content {
    position: relative;
    z-index: 2;
    color: white;
    max-width: 600px;
}
.hero-content h1 {
    font-size: 2.8rem;
    font-weight: 900;
    margin-bottom: 1rem;
    background: linear-gradient(to right, #ffffff, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}
.hero-content p {
    font-size: 1.15rem;
    opacity: 0.8;
    line-height: 1.6;
    margin: 0;
}
.hero-stats {
    position: relative;
    z-index: 2;
    display: flex;
    gap: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 1.5rem 2.5rem;
    border-radius: 20px;
}
.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 1rem;
}
.stat-item:not(:last-child) {
    border-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 1px solid rgba(255, 255, 255, 0.1);
}
.stat-value {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    line-height: 1;
    margin-bottom: 0.5rem;
}
.stat-label {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.7);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

/* Actions Bar */
.actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.section-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.section-title i {
    color: var(--brand-red);
}

.btn-new-ticket {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 16px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 10px 25px rgba(229, 62, 62, 0.3);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 1.05rem;
}
.btn-new-ticket:hover {
    transform: translateY(-5px) scale(1.02);
    color: white;
    box-shadow: 0 15px 35px rgba(229, 62, 62, 0.4);
}
.btn-new-ticket i {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}
.btn-new-ticket:hover i {
    transform: rotate(90deg);
}

/* Tickets Grid */
.tickets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 1.75rem;
}

/* Premium Card Design */
.ticket-card {
    background: var(--bg-card);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 24px;
    padding: 2.25rem;
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02), inset 0 0 0 1px rgba(255,255,255,0.05);
    z-index: 1;
}
html[data-bs-theme="dark"] .ticket-card {
    border-color: rgba(255,255,255,0.05);
    background: linear-gradient(145deg, var(--bg-card) 0%, rgba(20,20,30,0.4) 100%);
}

.ticket-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top right, rgba(229, 62, 62, 0.05) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: -1;
}
.ticket-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--brand-red), #f56565);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.ticket-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
    border-color: rgba(229, 62, 62, 0.2);
}
.ticket-card:hover::before {
    opacity: 1;
}
.ticket-card:hover::after {
    transform: scaleX(1);
}

.ticket-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}
.ticket-icon {
    width: 54px;
    height: 54px;
    background: linear-gradient(135deg, rgba(229, 62, 62, 0.1) 0%, rgba(229, 62, 62, 0.05) 100%);
    color: var(--brand-red);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 4px 10px rgba(229, 62, 62, 0.05);
}
.ticket-card:hover .ticket-icon {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    transform: scale(1.1) rotate(-8deg);
    box-shadow: 0 10px 20px rgba(229, 62, 62, 0.25);
}

.ticket-status {
    padding: 0.5rem 1.25rem;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 900;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}
.status-open {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
.status-open::before {
    content: '';
    display: block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 10px #10b981;
    animation: pulse-green 2s infinite;
}
.status-closed {
    background: rgba(100, 116, 139, 0.1);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.2);
}
.status-closed::before {
    content: '';
    display: block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #64748b;
}
@keyframes pulse-green {
    0% { transform: scale(0.9); opacity: 0.8; }
    50% { transform: scale(1.6); opacity: 0.2; }
    100% { transform: scale(0.9); opacity: 0.8; }
}

.ticket-id {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 800;
    margin-bottom: 0.5rem;
    display: inline-flex;
    align-items: center;
    background: var(--bg-body);
    padding: 0.25rem 0.75rem;
    border-radius: 8px;
}
.ticket-subject {
    font-size: 1.35rem;
    font-weight: 900;
    color: var(--text);
    text-decoration: none;
    line-height: 1.5;
    margin-bottom: 2rem;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}
.ticket-card:hover .ticket-subject {
    color: var(--brand-red);
}

.ticket-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.05);
}
html[data-bs-theme="dark"] .ticket-card-footer {
    border-top-color: rgba(255,255,255,0.05);
}
.ticket-meta {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.meta-item {
    font-size: 0.9rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-weight: 700;
}
.meta-item i {
    color: var(--text);
    opacity: 0.5;
}
.btn-view {
    background: transparent;
    color: var(--brand-red);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid rgba(229, 62, 62, 0.2);
    font-size: 1.1rem;
}
.ticket-card:hover .btn-view {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    border-color: transparent;
    transform: translateX(app()->getLocale() === 'ar' ? -8px : 8px);
    box-shadow: 0 8px 20px rgba(229, 62, 62, 0.3);
}

/* View Switcher */
.view-switcher {
    display: flex;
    background: var(--bg-body);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 14px;
    padding: 0.35rem;
    gap: 0.35rem;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
html[data-bs-theme="dark"] .view-switcher {
    border-color: rgba(255,255,255,0.05);
}
.btn-view-mode {
    background: transparent;
    border: none;
    color: var(--text-muted);
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.btn-view-mode:hover:not(.active) {
    color: var(--text);
    background: rgba(0,0,0,0.03);
}
html[data-bs-theme="dark"] .btn-view-mode:hover:not(.active) { background: rgba(255,255,255,0.03); }

.btn-view-mode.active {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    box-shadow: 0 6px 15px rgba(229, 62, 62, 0.25);
    transform: scale(1.05);
}

/* Premium List View Styles */
.tickets-list-view {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.tickets-list-view .ticket-card {
    flex-direction: row;
    align-items: center;
    padding: 1.5rem 2rem;
    gap: 2rem;
    border-radius: 20px;
    transform: none !important;
}
.tickets-list-view .ticket-card:hover {
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    transform: scale(1.01) !important;
}
.tickets-list-view .ticket-card::after {
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    transform: scaleY(0);
    transform-origin: bottom;
}
.tickets-list-view .ticket-card:hover::after {
    transform: scaleY(1);
}
.tickets-list-view .ticket-card-header {
    margin-bottom: 0;
    align-items: center;
    flex: 0 0 auto;
    width: 180px;
    justify-content: flex-start;
    gap: 1rem;
}
.tickets-list-view .ticket-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    font-size: 1.2rem;
}
.tickets-list-view .ticket-card:hover .ticket-icon {
    transform: rotate(-10deg) scale(1.1);
}
.tickets-list-view .ticket-status {
    padding: 0.4rem 1rem;
}
.tickets-list-view .ticket-id {
    display: none;
}
.tickets-list-view .ticket-subject {
    margin-bottom: 0;
    font-size: 1.2rem;
    -webkit-line-clamp: 1;
    flex: 1;
}
.tickets-list-view .ticket-card-footer {
    border-top: none;
    padding-top: 0;
    flex: 0 0 auto;
    margin-left: auto;
    gap: 2rem;
}
html[dir="rtl"] .tickets-list-view .ticket-card-footer {
    margin-left: 0;
    margin-right: auto;
}
.tickets-list-view .ticket-meta {
    flex-direction: row;
    gap: 2rem;
}
.tickets-list-view .btn-view {
    width: 42px;
    height: 42px;
}

@media(max-width: 768px) {
    .support-hero {
        padding: 2rem;
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    .hero-stats {
        width: 100%;
        justify-content: center;
    }
    .tickets-grid {
        grid-template-columns: 1fr;
    }
    .tickets-list-view .ticket-card {
        flex-direction: column;
        align-items: stretch;
    }
    .tickets-list-view .ticket-card-footer {
        margin: 0;
        border-top: 1px dashed var(--border);
        padding-top: 1.25rem;
        flex-direction: row;
        justify-content: space-between;
    }
    .tickets-list-view .ticket-meta {
        flex-direction: column;
        gap: 0.3rem;
    }
}
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="support-hero">
    <div class="hero-content">
        <h1>{{ app()->getLocale() === 'ar' ? 'مركز الدعم والمساعدة' : 'Support & Help Center' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'نحن هنا لضمان حصولك على أفضل تجربة. فريق الخبراء لدينا جاهز للرد على استفساراتك وحل أي مشاكل تواجهك فوراً.' : 'We are here to ensure you have the best experience. Our team of experts is ready to answer your inquiries immediately.' }}</p>
    </div>
    
    @php
        $totalTickets = $tickets->total();
        $openTickets = $tickets->where('status', 'open')->count();
    @endphp
    
    <div class="hero-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $totalTickets }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي التذاكر' : 'Total Tickets' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #10b981;">{{ $openTickets }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'تذاكر مفتوحة' : 'Open Tickets' }}</div>
        </div>
    </div>
</div>

<!-- Actions Bar -->
<div class="actions-bar">
    <h2 class="section-title">
        <i class="fas fa-ticket-alt"></i>
        {{ app()->getLocale() === 'ar' ? 'تذاكرك الحالية' : 'Your Tickets' }}
    </h2>
    <div class="d-flex align-items-center" style="gap: 1rem; flex-wrap: wrap;">
        <div class="view-switcher" id="viewSwitcher">
            <button class="btn-view-mode active" data-view="grid" title="{{ app()->getLocale() === 'ar' ? 'عرض شبكي' : 'Grid View' }}">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="btn-view-mode" data-view="list" title="{{ app()->getLocale() === 'ar' ? 'عرض قائمة' : 'List View' }}">
                <i class="fas fa-list"></i>
            </button>
        </div>
        <a href="{{ route('bidder.support.create') }}" class="btn-new-ticket">
            <i class="fas fa-plus"></i>
            {{ app()->getLocale() === 'ar' ? 'إنشاء تذكرة جديدة' : 'Create New Ticket' }}
        </a>
    </div>
</div>

<!-- Tickets Container -->
<div class="tickets-grid" id="ticketsContainer">
    @forelse($tickets as $ticket)
        <a href="{{ route('bidder.support.show', $ticket->id) }}" style="text-decoration: none; color: inherit;">
            <div class="ticket-card">
                <div class="ticket-card-header">
                    <div class="ticket-icon">
                        <i class="far fa-envelope-open"></i>
                    </div>
                    <div class="ticket-status {{ $ticket->status === 'open' ? 'status-open' : 'status-closed' }}">
                        {{ $ticket->status === 'open' ? (app()->getLocale() === 'ar' ? 'مفتوحة' : 'Open') : (app()->getLocale() === 'ar' ? 'مغلقة' : 'Closed') }}
                    </div>
                </div>
                
                <div class="ticket-id">#TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</div>
                <h3 class="ticket-subject">{{ $ticket->subject }}</h3>
                
                <div class="ticket-card-footer">
                    <div class="ticket-meta">
                        <span class="meta-item">
                            <i class="far fa-calendar-alt"></i>
                            {{ $ticket->created_at->format('Y-m-d') }}
                        </span>
                        <span class="meta-item">
                            <i class="far fa-comment-dots"></i>
                            {{ $ticket->messages_count }} {{ app()->getLocale() === 'ar' ? 'ردود' : 'Replies' }}
                        </span>
                    </div>
                    <div class="btn-view">
                        <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <i class="fas fa-headset empty-state-bg"></i>
            <div class="empty-state-content">
                <div class="empty-state-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>{{ app()->getLocale() === 'ar' ? 'لا توجد تذاكر حالياً' : 'No tickets found' }}</h3>
                <p>{{ app()->getLocale() === 'ar' ? 'يبدو أنك لم تقم بفتح أي تذكرة دعم فني بعد. نحن دائماً هنا في حال احتجت إلى أي مساعدة.' : 'It looks like you haven\'t opened any support tickets yet. We are always here if you need any help.' }}</p>
                <a href="{{ route('bidder.support.create') }}" class="btn-new-ticket">
                    <i class="fas fa-paper-plane"></i>
                    {{ app()->getLocale() === 'ar' ? 'افتح تذكرتك الأولى الآن' : 'Open your first ticket now' }}
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($tickets->hasPages())
    <div class="mt-5 d-flex justify-content-center">
        {{ $tickets->links() }}
    </div>
@endif

@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.btn-view-mode');
    const container = document.getElementById('ticketsContainer');
    
    if(!container || viewButtons.length === 0) return;

    // Check saved preference
    const savedView = localStorage.getItem('supportTicketViewPref') || 'grid';
    applyView(savedView);

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            applyView(view);
            localStorage.setItem('supportTicketViewPref', view);
        });
    });

    function applyView(view) {
        // Update buttons
        viewButtons.forEach(btn => {
            if(btn.getAttribute('data-view') === view) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Update container class
        if(view === 'list') {
            container.classList.remove('tickets-grid');
            container.classList.add('tickets-list-view');
        } else {
            container.classList.remove('tickets-list-view');
            container.classList.add('tickets-grid');
        }
    }
});
</script>
@endsection
