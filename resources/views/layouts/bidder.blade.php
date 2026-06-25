<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Motorazad Bidder Dashboard - لوحة تحكم المزايد">
    <title>Motorazad — @yield('title', 'لوحة المزايد')</title>
   
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bidder.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    {{-- Apply saved theme immediately to prevent flash --}}
    <script>
        (function() {
            var saved = localStorage.getItem('motorzad-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>
    @yield('css')
</head>
<body>
    {{-- ========== SIDEBAR OVERLAY ========== --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ========== SIDEBAR ========== --}}
    @include('layouts.bidder.sidebar')

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="main-content">
        @include('layouts.bidder.topbar')

        <div class="page-content fade-in">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @include('layouts.admin.scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
                });
            }

            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            document.querySelectorAll('.sidebar .nav-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        closeSidebar();
                    }
                });
            });

            // ===== Bidder Header Quick Search =====
            let bidderSearchTimeout;
            const bidderDropdown = $('#bidderSearchDropdown');

            $('#bidder_quick_search').on('keyup input', function(e) {
                let query = $(this).val();
                
                // If they press Enter, redirect to the browse page with search pre-filled
                if (e.type === 'keyup' && e.key === 'Enter') {
                    let localSearch = $('.search-filter-box input[name="search"]');
                    if (localSearch.length) {
                        localSearch.val(query);
                        $('.search-filter-box').submit();
                    } else {
                        window.location.href = "{{ route('bidder.auctions.index') }}?search=" + encodeURIComponent(query);
                    }
                    bidderDropdown.hide();
                    return;
                }

                if (query.length < 2) {
                    bidderDropdown.empty().hide();
                    return;
                }

                clearTimeout(bidderSearchTimeout);
                bidderSearchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('bidder.global-search') }}",
                        method: 'GET',
                        data: { q: query },
                        success: function(response) {
                            bidderDropdown.empty();
                            if (response.results && response.results.length > 0) {
                                response.results.forEach(category => {
                                    bidderDropdown.append(`<div class="global-search-category">${category.category}</div>`);
                                    category.items.forEach(item => {
                                        let itemImage = '';
                                        if (item.image) {
                                            itemImage = `<img src="${item.image}" class="global-search-item-img" alt="">`;
                                        } else {
                                            itemImage = `<div class="global-search-item-img d-flex align-items-center justify-content-center text-white"><i class="fas ${item.icon}"></i></div>`;
                                        }

                                        bidderDropdown.append(`
                                            <a href="${item.url}" class="global-search-item">
                                                ${itemImage}
                                                <div class="global-search-item-info">
                                                    <span class="global-search-item-title">${item.title}</span>
                                                    <span class="global-search-item-subtitle">${item.subtitle}</span>
                                                </div>
                                            </a>
                                        `);
                                    });
                                });
                                bidderDropdown.show();
                            } else {
                                bidderDropdown.empty().hide();
                            }
                        },
                        error: function() {
                            bidderDropdown.empty().hide();
                        }
                    });
                }, 300);
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-box').length) {
                    bidderDropdown.hide();
                }
            });

            $('#bidder_quick_search').on('focus', function() {
                if ($(this).val().length >= 2 && bidderDropdown.children().length > 0) {
                    bidderDropdown.show();
                }
            });

            // Initialize search input value from local auctions search (if pre-filled)
            let localSearchVal = $('.search-filter-box input[name="search"]').val();
            if (localSearchVal) {
                $('#bidder_quick_search').val(localSearchVal);
            }

            // ===== Bidder Real-Time Notifications Polling =====
            let lastUnreadCount = parseInt($('#sidebarNotifCount').text()) || 0;
            let lastNotificationId = null;

            function pollNotifications() {
                $.ajax({
                    url: "{{ route('bidder.notifications.unread_state') }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let newCount = response.unread_count;
                            let latest = response.latest_notification;
                            
                            // 1. Update header notification badge count
                            let headerDot = $('#headerNotifDot');
                            if (newCount > 0) {
                                if (headerDot.length === 0) {
                                    $('.topbar-right a[href*="/notifications"]').append('<span class="notif-dot" id="headerNotifDot">' + newCount + '</span>');
                                } else {
                                    headerDot.text(newCount);
                                }
                            } else {
                                headerDot.remove();
                            }

                            // 2. Update sidebar counter badge
                            let sidebarBadge = $('#sidebarNotifCount');
                            if (newCount > 0) {
                                if (sidebarBadge.length) {
                                    sidebarBadge.text(newCount);
                                } else {
                                    $('.sidebar-nav a[href*="/notifications"]').append('<span class="nav-count" id="sidebarNotifCount">' + newCount + '</span>');
                                }
                            } else {
                                sidebarBadge.remove();
                            }

                            // 3. Check for new notification to show Toastr alert and refresh notifications page list
                            if (latest && latest.id !== lastNotificationId) {
                                // Only alert if it's not the first initial check (so we don't spam toasts on page load)
                                if (lastNotificationId !== null) {
                                    if (window.toastr) {
                                        // Build custom localized phrase for unread notifications count
                                        let currentLang = $('html').attr('lang') || 'ar';
                                        let countPhrase = '';
                                        if (currentLang === 'ar') {
                                            if (newCount === 1) {
                                                countPhrase = 'لديك إشعار غير مقروء (1)';
                                            } else if (newCount === 2) {
                                                countPhrase = 'لديك إشعاران غير مقروئين (2)';
                                            } else {
                                                countPhrase = 'لديك إشعارات غير مقروءة (' + newCount + ')';
                                            }
                                        } else {
                                            if (newCount === 1) {
                                                countPhrase = 'You have 1 unread notification';
                                            } else {
                                                countPhrase = 'You have ' + newCount + ' unread notifications';
                                            }
                                        }

                                        let fullBody = latest.message + 
                                            '<div style="margin-top:8px; padding-top:6px; border-top:1px dashed rgba(255,255,255,0.25); font-weight:700; font-size:0.8rem;">' + 
                                            '<i class="fa fa-bell me-1"></i> ' + countPhrase + 
                                            '</div>';

                                        toastr.info(fullBody, latest.title, {
                                            timeOut: 10000,
                                            progressBar: true,
                                            closeButton: true,
                                            onclick: function() {
                                                window.location.href = latest.action_url;
                                            }
                                        });
                                    }

                                    // If we are currently on the notifications page, reload/refresh the list dynamically
                                    if ($('#notifications-container').length) {
                                        if (typeof BidderAjax !== 'undefined') {
                                            BidderAjax.get(window.location.href, {}, {
                                                onSuccess: function(listResponse) {
                                                    if (listResponse.success && listResponse.html) {
                                                        $('#notifications-container').html(listResponse.html);
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                                lastNotificationId = latest.id;
                            } else if (!latest) {
                                lastNotificationId = null;
                            }

                            lastUnreadCount = newCount;
                        }
                    }
                });
            }

            // Start polling every 10 seconds, and run once shortly after page load
            setInterval(pollNotifications, 10000);
            setTimeout(pollNotifications, 3000);
        });
    </script>
    @yield('js')
</body>
</html>
