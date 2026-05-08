<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Motorazad Admin Dashboard - لوحة إدارة مزادات السيارات">
    <title>Motorazad — @yield('title', 'لوحة التحكم')</title>
   
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
        });
    </script>
    @yield('js')
</body>
</html>
