<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SGP Lite') }}</title>

        <!-- Preconnect for performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="app-layout">
        <!-- Header -->
        @include('layouts.partials.navbar')

        <!-- Main Layout -->
        <div class="d-flex flex-grow-1">
            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main Content -->
            <main class="app-main flex-grow-1">
                <!-- Page Header -->
                @if(isset($header))
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        {{ $header }}
                    </div>
                </div>
                @endif

                <!-- Page Content -->
                <div class="content-wrapper">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')

        <!-- Toast Container -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay d-lg-none" onclick="toggleSidebar()"></div>

        <script>
            // Sidebar toggle for mobile
            function toggleSidebar() {
                const sidebar = document.querySelector('.app-sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                const sidebar = document.querySelector('.app-sidebar');
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                
                if (window.innerWidth < 992 && 
                    !sidebar.contains(e.target) && 
                    !sidebarToggle.contains(e.target) &&
                    sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const sidebar = document.querySelector('.app-sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        </script>

        <style>
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            @media (max-width: 991.98px) {
                .app-sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100vh;
                    width: 280px;
                    z-index: 1050;
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }

                .app-sidebar.show {
                    transform: translateX(0);
                }
            }
        </style>
    </body>
</html>
