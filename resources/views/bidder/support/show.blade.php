@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'تذكرة #' . $ticket->id : 'Ticket #' . $ticket->id)

@section('css')
<style>
.support-show-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    height: calc(100vh - 120px);
    min-height: 600px;
    margin-top: 1rem;
}

/* --- Left Sidebar (Ticket Info) --- */
.ticket-sidebar {
    flex: 1;
    min-width: 280px;
    max-width: 320px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
.sidebar-header {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    padding: 2rem 1.5rem;
    color: white;
    position: relative;
}
.sidebar-header::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: -15px;
    right: -15px;
    height: 30px;
    background: var(--bg-card);
    border-radius: 50%;
}
.btn-back {
    color: white;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.2);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    transition: all 0.3s;
}
.btn-back:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}
.ticket-id-badge {
    background: rgba(0,0,0,0.2);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 800;
    display: inline-block;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
}
.ticket-subject-title {
    font-size: 1.25rem;
    font-weight: 900;
    margin: 0;
    position: relative;
    z-index: 2;
    line-height: 1.4;
}

.sidebar-body {
    padding: 1.5rem;
    flex: 1;
}
.info-group {
    margin-bottom: 1.5rem;
}
.info-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-value {
    font-weight: 800;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.info-value i {
    color: var(--brand-red);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
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
    animation: pulse 2s infinite;
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
@keyframes pulse {
    0% { transform: scale(0.9); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.3; }
    100% { transform: scale(0.9); opacity: 1; }
}

/* --- Right Chat Area --- */
.chat-window {
    flex: 3;
    min-width: 300px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.05);
}
.chat-header {
    padding: 1.25rem 2rem;
    border-bottom: 1px solid var(--border);
    background: var(--bg-body);
    display: flex;
    align-items: center;
    gap: 1rem;
}
.support-agent-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(229, 62, 62, 0.3);
}
.agent-info h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
}
.agent-status {
    font-size: 0.85rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
}
.agent-status::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
}

/* Messages Area */
.chat-messages {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz4KPC9zdmc+') repeat var(--bg-body);
    scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 6px; }
.chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

.message-wrapper {
    display: flex;
    flex-direction: column;
    max-width: 75%;
    animation: slideUp 0.3s ease-out forwards;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.msg-user { align-self: flex-end; }
.msg-admin { align-self: flex-start; }

.message-bubble {
    padding: 1rem 1.5rem;
    border-radius: 20px;
    line-height: 1.6;
    font-size: 0.95rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    position: relative;
}
.msg-user .message-bubble {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    border-bottom-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}-radius: 4px;
}
.msg-admin .message-bubble {
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text);
    border-bottom-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}-radius: 4px;
}

.message-time {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.4rem;
    padding: 0 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
}
.msg-user .message-time { justify-content: flex-end; }
.msg-user .message-time i { color: #10b981; } /* Read tick */

/* Reply Area */
.chat-reply-area {
    padding: 1.25rem;
    background: var(--bg-card);
    border-top: 1px solid var(--border);
}
.reply-container {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    background: var(--bg-body);
    border: 2px solid transparent;
    border-radius: 20px;
    padding: 0.5rem 0.5rem 0.5rem 1rem;
    transition: all 0.3s;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
}
.reply-container:focus-within {
    border-color: var(--brand-red);
    background: var(--bg-card);
    box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.1);
}
.reply-input {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--text);
    resize: none;
    font-size: 1rem;
    padding: 0.75rem 0;
    max-height: 120px;
}
.reply-input:focus { outline: none; }
.reply-input::placeholder { color: var(--text-muted); opacity: 0.7; }

.btn-attach {
    background: transparent;
    border: none;
    color: var(--text-muted);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 0.25rem;
}
.btn-attach:hover {
    color: var(--brand-red);
    background: rgba(229, 62, 62, 0.1);
}

.btn-send {
    background: linear-gradient(135deg, var(--brand-red) 0%, #c53030 100%);
    color: white;
    border: none;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    margin-bottom: 0.15rem;
}
.btn-send:hover {
    transform: scale(1.05) rotate(5deg);
    box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
}

@media(max-width: 768px) {
    .support-show-wrapper { height: auto; flex-direction: column; }
    .ticket-sidebar { max-width: 100%; }
    .chat-window { height: 60vh; }
    .message-wrapper { max-width: 90%; }
}
</style>
@endsection

@section('content')
<div class="support-show-wrapper">

    <!-- Left Sidebar -->
    <div class="ticket-sidebar">
        <div class="sidebar-header">
            <a href="{{ route('bidder.support.index') }}" class="btn-back">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                {{ app()->getLocale() === 'ar' ? 'عودة' : 'Back' }}
            </a>
            <div class="ticket-id-badge">#{{ $ticket->id }}</div>
            <h2 class="ticket-subject-title">{{ $ticket->subject }}</h2>
        </div>
        <div class="sidebar-body">
            
            <div class="info-group">
                <div class="info-label">{{ app()->getLocale() === 'ar' ? 'حالة التذكرة' : 'Status' }}</div>
                <div class="status-badge {{ $ticket->status === 'open' ? 'status-open' : 'status-closed' }}">
                    {{ $ticket->status === 'open' ? (app()->getLocale() === 'ar' ? 'مفتوحة للمتابعة' : 'Open') : (app()->getLocale() === 'ar' ? 'مغلقة' : 'Closed') }}
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created At' }}</div>
                <div class="info-value">
                    <i class="far fa-calendar-alt"></i>
                    {{ $ticket->created_at->format('Y-m-d') }}
                </div>
                <div class="text-muted text-sm mt-1" style="font-size: 0.8rem;">
                    {{ $ticket->created_at->format('h:i A') }} ({{ $ticket->created_at->diffForHumans() }})
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">{{ app()->getLocale() === 'ar' ? 'عدد الردود' : 'Total Replies' }}</div>
                <div class="info-value">
                    <i class="far fa-comments"></i>
                    {{ $ticket->messages->count() }} {{ app()->getLocale() === 'ar' ? 'ردود' : 'Replies' }}
                </div>
            </div>

        </div>
    </div>

    <!-- Right Chat Area -->
    <div class="chat-window">
        
        <div class="chat-header">
            <div class="support-agent-avatar">
                <i class="fas fa-headset"></i>
            </div>
            <div class="agent-info">
                <h4>{{ app()->getLocale() === 'ar' ? 'فريق الدعم الفني' : 'Support Team' }}</h4>
                <div class="agent-status">{{ app()->getLocale() === 'ar' ? 'متصل وجاهز للمساعدة' : 'Online & ready to help' }}</div>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages">
            @foreach($ticket->messages as $msg)
                <div class="message-wrapper {{ $msg->is_admin ? 'msg-admin' : 'msg-user' }}">
                    <div class="message-bubble">
                        {!! nl2br(e($msg->message)) !!}
                    </div>
                    <div class="message-time">
                        {{ $msg->created_at->format('h:i A') }}
                        @if(!$msg->is_admin)
                            <i class="fas fa-check-double"></i>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="chat-reply-area">
            <form action="{{ route('bidder.support.reply', $ticket->id) }}" method="POST">
                @csrf
                <div class="reply-container">
                    <button type="button" class="btn-attach" title="{{ app()->getLocale() === 'ar' ? 'إرفاق ملف' : 'Attach file' }}">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    
                    <textarea name="message" class="reply-input" rows="1" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب رسالتك هنا...' : 'Type your message here...' }}" required oninput="this.style.height = '';this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                    
                    <button type="submit" class="btn-send" title="{{ app()->getLocale() === 'ar' ? 'إرسال' : 'Send' }}">
                        <i class="fas fa-paper-plane" style="margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 2px;"></i>
                    </button>
                </div>
                @error('message')
                    <div class="text-danger mt-2 text-sm font-weight-bold" style="padding: 0 1rem;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </form>
        </div>

    </div>

</div>
@endsection

@section('js')
<script>
    // Scroll chat to bottom on load
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        if(chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>
@endsection
