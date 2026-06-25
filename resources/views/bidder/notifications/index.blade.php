@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications')

@section('css')
<style>
/* ===== PREMIUM NOTIFICATIONS STYLES ===== */
.notif-header {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(22, 33, 62, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}
.notif-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(229, 62, 62, 0.1), transparent 50%), 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1), transparent 50%);
    pointer-events: none;
}
.notif-header-inner {
    position: relative;
    z-index: 2;
}
.notif-header h1 {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}
.notif-header p {
    opacity: 0.8;
    font-size: 1.05rem;
    max-width: 600px;
}

.btn-mark-all {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: white;
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-mark-all:hover {
    background: white;
    color: #1a1a2e;
    transform: translateY(-1px);
}

/* Notifications list row card */
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}
.notif-row-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    display: flex;
    gap: 1.25rem;
    align-items: center;
    transition: all 0.25s ease;
    cursor: pointer;
    position: relative;
}
.notif-row-card:hover {
    transform: translateY(-2px);
    border-color: var(--text-muted);
}
.notif-row-card.unread {
    background: linear-gradient(to right, rgba(229, 62, 62, 0.02), var(--bg-card));
    border-left: 4px solid var(--brand-red);
}
html[dir="rtl"] .notif-row-card.unread {
    border-left: none;
    border-right: 4px solid var(--brand-red);
}

.notif-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notif-body {
    flex: 1;
}
.notif-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
    gap: 1rem;
}
.notif-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text);
    margin: 0;
}
.notif-time {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
}
.notif-desc {
    font-size: 0.88rem;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.45;
}

.notif-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
}
.notif-unread-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: var(--brand-red);
    box-shadow: 0 0 8px var(--brand-red);
    display: inline-block;
    cursor: pointer;
    transition: transform 0.2s;
}
.notif-unread-dot:hover {
    transform: scale(1.3);
}
.notif-read-check {
    color: var(--text-muted);
    opacity: 0.6;
}

@media(max-width: 768px) {
    .notif-row-card {
        padding: 1rem;
        gap: 0.75rem;
    }
    .notif-title-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.15rem;
    }
    .notif-time {
        margin-top: 0.15rem;
    }
}
</style>
@endsection

@section('content')
<div class="notif-header">
    <div class="notif-header-inner">
        <h1>{{ app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'ابق على اطلاع دائم بآخر التحديثات، حالة المزايدات، العمليات المالية، وتوثيق الحساب.' : 'Stay updated with the latest alerts, bidding updates, wallet operations, and KYC status.' }}</p>
    </div>
    
    <button class="btn-mark-all" id="btnMarkAllRead">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ app()->getLocale() === 'ar' ? 'تحديد الكل كمقروء' : 'Mark all as read' }}
    </button>
</div>

{{-- Main List Container --}}
<div id="notifications-container">
    @include('bidder.notifications.partials.list')
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Intercept AJAX pagination clicks
    $(document).on('click', '#notifications-container .pagination-wrapper a, #notifications-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadNotifications(url);
        }
    });

    function loadNotifications(url) {
        $('#notifications-container').css('opacity', '0.5');

        BidderAjax.get(url, {}, {
            onSuccess: function(response) {
                $('#notifications-container').css('opacity', '1');
                if (response.success && response.html) {
                    $('#notifications-container').html(response.html);
                    window.history.pushState(null, null, url);
                } else {
                    toastr.error('Failed to load notifications.');
                }
            },
            onError: function() {
                $('#notifications-container').css('opacity', '1');
                toastr.error('Failed to load notifications.');
            }
        });
    }

    window.addEventListener('popstate', function() {
        loadNotifications(window.location.href);
    });

    // Mark Single Notification as Read
    $(document).on('click', '.notif-row-card', function(e) {
        // Prevent click if clicking a link or button inside description
        if ($(e.target).closest('a, button').length) {
            return;
        }

        const card = $(this);
        const id = card.data('id');
        const actionUrl = card.data('url');
        const isUnread = card.hasClass('unread');

        if (isUnread) {
            markAsRead(id, card);
        }

        // Redirect after a brief delay if url is valid
        if (actionUrl && actionUrl !== '#') {
            setTimeout(function() {
                window.location.href = actionUrl;
            }, 250);
        }
    });

    // Click directly on unread dot
    $(document).on('click', '.notif-unread-dot', function(e) {
        e.stopPropagation(); // Prevent card redirect click
        const dot = $(this);
        const card = dot.closest('.notif-row-card');
        const id = card.data('id');
        markAsRead(id, card);
    });

    function markAsRead(id, card) {
        const url = `/bidder/notifications/${id}/read`;
        
        BidderAjax.post(url, {}, {
            onSuccess: function(response) {
                if (response.success) {
                    // Update layout visually
                    card.removeClass('unread');
                    card.find('.notif-actions').html(
                        '<span class="notif-read-check">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' +
                        '</span>'
                    );

                    // Update dynamic counts in sidebar and topbar
                    decrementNotificationBadge();
                }
            }
        });
    }

    // Mark All as Read
    $('#btnMarkAllRead').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        const url = '/bidder/notifications/all/read';

        BidderAjax.post(url, {}, {
            onSuccess: function(response) {
                btn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    toastr.success(window.laravel_locale === 'ar' ? 'تم تحديد جميع الإشعارات كمقروءة' : 'All notifications marked as read');
                    
                    // Update all cards visually
                    $('.notif-row-card.unread').each(function() {
                        const card = $(this);
                        card.removeClass('unread');
                        card.find('.notif-actions').html(
                            '<span class="notif-read-check">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' +
                            '</span>'
                        );
                    });

                    // Clear dynamic badges
                    clearNotificationBadges();
                } else {
                    toastr.error('Failed to mark notifications.');
                }
            },
            onError: function() {
                btn.prop('disabled', false).html(originalHtml);
                toastr.error('Failed to mark notifications.');
            }
        });
    });

    function decrementNotificationBadge() {
        // Decrement sidebar count
        const sidebarBadge = $('#sidebarNotifCount');
        if (sidebarBadge.length) {
            let count = parseInt(sidebarBadge.text());
            if (!isNaN(count) && count > 1) {
                sidebarBadge.text(count - 1);
            } else {
                sidebarBadge.remove();
                // Also hide topbar notification dot if 0
                $('#headerNotifDot').remove();
            }
        }
    }

    function clearNotificationBadges() {
        $('#sidebarNotifCount').remove();
        $('#headerNotifDot').remove();
    }
});
</script>
@endsection
