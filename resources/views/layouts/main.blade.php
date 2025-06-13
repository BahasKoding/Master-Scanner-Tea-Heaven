<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title') | Sistem Informasi Tea Heaven</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistem Informasi Tea Heaven" />
    <meta name="author" content="bahaskoding" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ URL::asset('build/images/favicon.svg') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tea Heaven custom theme -->
    {{-- <link rel="stylesheet" href="{{ URL::asset('build/css/tea-heaven-theme.css') }}"> --}}

    <!-- Responsive overrides -->
    <style>
        /* Responsive CSS for mobile and tablets */
        @media (max-width: 992px) {
            .pc-sidebar {
                z-index: 1030;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            body.mob-sidebar-active .pc-sidebar {
                transform: translateX(0);
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            }

            .pc-container {
                padding-left: 0 !important;
            }

            .pc-content {
                padding: 15px;
            }

            /* Improve buttons for touch */
            .btn {
                padding: 0.4rem 0.75rem;
                min-height: 44px;
                /* iOS touch target minimum */
            }

            /* Improve menu links for touch */
            .pc-sidebar .pc-link {
                padding: 12px 20px;
                min-height: 44px;
                display: flex;
                align-items: center;
                text-decoration: none;
                color: inherit;
                transition: all 0.2s ease;
            }

            .pc-sidebar .pc-link:hover,
            .pc-sidebar .pc-link:focus {
                background-color: rgba(0, 0, 0, 0.05);
                text-decoration: none;
                color: inherit;
            }

            /* Improve submenu items */
            .pc-sidebar .pc-submenu .pc-link {
                padding: 10px 20px 10px 40px;
                font-size: 14px;
            }

            /* Improve topbar on mobile */
            .pc-header {
                padding: 10px 15px;
            }

            /* Overlay when mobile menu is open */
            .mob-sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1025;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            body.mob-sidebar-active .mob-sidebar-overlay {
                opacity: 1;
                visibility: visible;
            }

            /* Ensure navbar content is scrollable */
            .pc-sidebar .navbar-content {
                flex: 1;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Fix for submenu toggle on mobile */
            .pc-sidebar .pc-item.pc-hasmenu.pc-trigger>.pc-submenu {
                display: block !important;
            }
        }

        @media (max-width: 576px) {
            .pc-header .pc-mob-header {
                padding: 5px 10px;
            }

            .pc-content {
                padding: 10px;
            }

            .pc-card {
                margin-bottom: 10px;
                border-radius: 4px;
            }

            h1,
            .h1 {
                font-size: 1.8rem;
            }

            h2,
            .h2 {
                font-size: 1.6rem;
            }

            h3,
            .h3 {
                font-size: 1.4rem;
            }

            .pc-header .pc-head-link {
                margin: 0 2px;
            }
        }
    </style>

    @yield('css')

    @include('layouts.head-css')
</head>

<body data-pc-preset="preset-6" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr"
    data-pc-theme="light">
    @include('layouts.loader')
    @include('layouts.sidebar')
    @include('layouts.topbar')

    <!-- Mobile sidebar overlay -->
    <div class="mob-sidebar-overlay"></div>

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @if (View::hasSection('breadcrumb-item'))
                @include('layouts.breadcrumb')
            @endif
            <!-- [ Main Content ] start -->
            @yield('content')
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('layouts.footer')
    @include('layouts.customizer')

    <!-- Core JS libraries in correct order -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/popper.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/fonts/custom-font.js') }}"></script>
    <script src="{{ URL::asset('build/js/pcoded.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/feather.min.js') }}"></script>

    <!-- Custom script for the sidebar toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar collapse for desktop
            const sidebarHideBtn = document.getElementById('sidebar-hide');
            if (sidebarHideBtn) {
                sidebarHideBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle body class for sidebar collapse
                    document.body.classList.toggle('pc-sidebar-collapse');

                    // Store the sidebar state in localStorage
                    const isSidebarCollapsed = document.body.classList.contains('pc-sidebar-collapse');
                    localStorage.setItem('sidebarCollapsed', isSidebarCollapsed);
                });
            }

            // Handle mobile sidebar collapse
            const mobileCollapseBtn = document.getElementById('mobile-collapse');
            if (mobileCollapseBtn) {
                mobileCollapseBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle body class for mobile sidebar
                    document.body.classList.toggle('mob-sidebar-active');
                });
            }

            // Add click event for sidebar overlay to close mobile menu
            const sidebarOverlay = document.querySelector('.mob-sidebar-overlay');
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    document.body.classList.remove('mob-sidebar-active');
                });
            }

            // Close mobile sidebar on window resize if window width is larger than mobile breakpoint
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    document.body.classList.remove('mob-sidebar-active');
                }
            });

            // Auto-close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    // Close sidebar if clicked outside, but not if clicking on menu items
                    if (!e.target.closest('.pc-sidebar') && !e.target.closest('#mobile-collapse')) {
                        document.body.classList.remove('mob-sidebar-active');
                    }
                }
            });

            // Close mobile sidebar when clicking on direct menu links (not submenu toggles)
            const directMenuLinks = document.querySelectorAll(
                '.pc-sidebar .pc-link:not([href="#!"]):not([href="#"])');
            directMenuLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth <= 992) {
                        // Allow navigation but close sidebar after a short delay
                        setTimeout(function() {
                            document.body.classList.remove('mob-sidebar-active');
                        }, 100);
                    }
                });
            });

            // Improve submenu toggle for touch devices
            const submenuItems = document.querySelectorAll('.pc-sidebar .pc-item.pc-hasmenu > a');
            submenuItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    if (window.innerWidth <= 992) {
                        // Only prevent default if this is a submenu toggle (href="#!")
                        const href = this.getAttribute('href');
                        if (href === '#!' || href === '#') {
                            e.preventDefault();
                            const parentItem = this.parentElement;
                            const siblingItems = parentItem.parentElement.querySelectorAll(
                                '.pc-item.pc-hasmenu');

                            // Close other submenus
                            siblingItems.forEach(function(sibling) {
                                if (sibling !== parentItem) {
                                    sibling.classList.remove('pc-trigger');
                                }
                            });

                            // Toggle current submenu
                            parentItem.classList.toggle('pc-trigger');
                        }
                        // If it's a real link, let it navigate normally
                    }
                });
            });

            // Check localStorage on page load to restore sidebar state
            const savedSidebarState = localStorage.getItem('sidebarCollapsed');
            if (savedSidebarState === 'true') {
                document.body.classList.add('pc-sidebar-collapse');
            } else if (savedSidebarState === 'false') {
                document.body.classList.remove('pc-sidebar-collapse');
            }

            // Add active class to current menu item based on URL
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.pc-sidebar .pc-link');

            menuLinks.forEach(function(link) {
                const linkHref = link.getAttribute('href');
                if (linkHref && linkHref !== '#!' && currentPath.includes(linkHref)) {
                    // Add active class to the link
                    link.classList.add('active');

                    // Add active class to parent item
                    const parentItem = link.closest('.pc-item');
                    if (parentItem) {
                        parentItem.classList.add('active');

                        // If inside submenu, open parent menu
                        const parentHasmenu = parentItem.closest('.pc-item.pc-hasmenu');
                        if (parentHasmenu) {
                            parentHasmenu.classList.add('pc-trigger');
                        }
                    }
                }
            });
        });
    </script>

    @include('layouts.footerjs')

    @yield('scripts')
</body>
<!-- [Body] end -->

</html>
