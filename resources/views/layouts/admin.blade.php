<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Motorazad Admin Dashboard - لوحة إدارة مزادات السيارات">
    <title>Motorazad — @yield('title', __('Dashboard'))</title>
   
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
    @else
        <!-- Assuming you have standard bootstrap, or you can point to a CDN if it doesn't exist. Typically vendor/bootstrap/css/bootstrap.min.css -->
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    @endif
    <!-- DataTables CSS -->
      <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    
    <!-- Pusher & Echo JS for WebSockets -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: window.location.hostname,
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '{{ url("/broadcasting/auth") }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });
    </script>

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
    @include('layouts.admin.sidebar')

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="main-content">
        @include('layouts.admin.topbar')

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

    {{-- ========== NOTIFICATIONS MODAL ========== --}}
    @if(Auth::check())
        <div class="modal fade" id="unreadNotificationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-bell me-2"></i> {{ __('New Notification') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-envelope-open-text text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="mb-2 fw-bold" id="notif-title">{{ __('System Alert') }}</h4>
                        <p class="text-muted mb-0" id="notif-body" style="font-size: 1.1rem;">
                            {{ __('You have a new notification.') }}
                        </p>
                        <a href="#" id="notif-action-btn" class="btn btn-outline-primary mt-3" target="_blank" style="display: none;">{{ __('View Details') }}</a>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <form id="notif-read-form" action="" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary px-4">{{ __('Okay, I understand') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @yield('modals')

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

            // Close sidebar on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            // Close sidebar when clicking a nav item on mobile
            document.querySelectorAll('.sidebar .nav-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        closeSidebar();
                    }
                });
            });

            // Real-Time Notification Modal dynamically via Laravel Echo (Reverb/WebSockets)
            @if(Auth::check())
            
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .notification((notification) => {
                        console.log('New Notification Received:', notification);
                        
                        // Populate Modal
                        document.getElementById('notif-title').innerText = notification.title || '{{ __('System Alert') }}';
                        document.getElementById('notif-body').innerText = notification.body || '{{ __('You have a new notification.') }}';
                        
                        const actionBtn = document.getElementById('notif-action-btn');
                        if(notification.action_url) {
                            actionBtn.href = notification.action_url;
                            actionBtn.style.display = 'inline-block';
                        } else {
                            actionBtn.style.display = 'none';
                        }

                        // Update Form action
                        let markReadUrl = '{{ route("admin.notifications.mark_read", ":id") }}';
                        markReadUrl = markReadUrl.replace(':id', notification.id);
                        document.getElementById('notif-read-form').action = markReadUrl;

                        // Show Modal
                        const modalEl = document.getElementById('unreadNotificationModal');
                        if (modalEl) {
                            const notificationModal = new bootstrap.Modal(modalEl);
                            notificationModal.show();
                        }
                    });
            } else {
                console.warn('Laravel Echo is not defined.');
            }
            @endif
        });
    </script>
    @yield('js')
</body>
</html>
