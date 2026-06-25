<div class="notifications-list">
    @forelse($notifications as $notif)
        @php
            $isMock = !($notif instanceof \Illuminate\Notifications\DatabaseNotification);
            $id = $notif->id;
            $isRead = $notif->read_at !== null;
            
            if ($isMock) {
                $title = app()->getLocale() === 'ar' ? $notif->data['title_ar'] : $notif->data['title_en'];
                $message = app()->getLocale() === 'ar' ? $notif->data['message_ar'] : $notif->data['message_en'];
                $actionUrl = $notif->data['action_url'] ?? '#';
                $icon = $notif->data['icon'] ?? 'fa-bell';
                $iconColor = $notif->data['icon_color'] ?? '#3b82f6';
                $bgColor = $notif->data['bg_color'] ?? 'rgba(59, 130, 246, 0.08)';
            } else {
                $title = $notif->data['title_' . app()->getLocale()] ?? ($notif->data['title'] ?? __('Notification'));
                $message = $notif->data['message_' . app()->getLocale()] ?? ($notif->data['message'] ?? ($notif->data['body'] ?? ''));
                $actionUrl = $notif->data['action_url'] ?? '#';
                
                $type = strtolower($notif->type);
                if (str_contains($type, 'kyc')) {
                    $icon = 'fa-user-check';
                    $iconColor = '#10b981';
                    $bgColor = 'rgba(16, 185, 129, 0.08)';
                } elseif (str_contains($type, 'outbid')) {
                    $icon = 'fa-gavel';
                    $iconColor = '#f59e0b';
                    $bgColor = 'rgba(245, 158, 11, 0.08)';
                } elseif (str_contains($type, 'wallet') || str_contains($type, 'deposit') || str_contains($type, 'withdraw')) {
                    $icon = 'fa-wallet';
                    $iconColor = '#3b82f6';
                    $bgColor = 'rgba(59, 130, 246, 0.08)';
                } elseif (str_contains($type, 'won')) {
                    $icon = 'fa-trophy';
                    $iconColor = '#8b5cf6';
                    $bgColor = 'rgba(139, 92, 246, 0.08)';
                } else {
                    $icon = 'fa-bell';
                    $iconColor = '#e53e3e';
                    $bgColor = 'rgba(229, 62, 62, 0.08)';
                }
            }
            
            // Format created_at to human readable (diffForHumans)
            $timeAgo = $isMock ? $notif->created_at->diffForHumans() : $notif->created_at->diffForHumans();
        @endphp

        <div class="notif-row-card {{ !$isRead ? 'unread' : '' }}" data-id="{{ $id }}" data-url="{{ $actionUrl }}">
            {{-- Circular Icon --}}
            <div class="notif-icon-wrap" style="background: {{ $bgColor }}; color: {{ $iconColor }};">
                <i class="fa-solid {{ $icon }}" style="font-size: 1.2rem;"></i>
            </div>

            {{-- Text Info --}}
            <div class="notif-body">
                <div class="notif-title-row">
                    <h4 class="notif-title">{{ $title }}</h4>
                    <span class="notif-time">{{ $timeAgo }}</span>
                </div>
                <p class="notif-desc">{{ $message }}</p>
            </div>

            {{-- Status Indicators / Actions --}}
            <div class="notif-actions">
                @if(!$isRead)
                    <span class="notif-unread-dot" title="{{ app()->getLocale() === 'ar' ? 'تحديد كمقروء' : 'Mark as read' }}"></span>
                @else
                    <span class="notif-read-check">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 5rem 2rem; border-radius: var(--radius-xl); text-align: center; color: var(--text-muted);">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:1.5rem; opacity:0.4;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <h4 style="font-weight:800; color:var(--text); margin-bottom:0.5rem;">{{ app()->getLocale() === 'ar' ? 'صندوق الإشعارات فارغ' : 'No notifications yet' }}</h4>
            <p style="font-size:0.95rem; max-width:400px; margin:0 auto;">{{ app()->getLocale() === 'ar' ? 'لا توجد أي إشعارات جديدة حالياً. سنقوم بإعلامك فور حدوث أي نشاط في حسابك.' : 'You have no notifications at the moment. We will notify you when there is activity on your account.' }}</p>
        </div>
    @endforelse
</div>

{{-- Pagination Links --}}
@if(!$usingMock && method_exists($notifications, 'links'))
    <div style="margin-top: 2.5rem;" class="pagination-wrapper">
        {{ $notifications->links() }}
    </div>
@endif
