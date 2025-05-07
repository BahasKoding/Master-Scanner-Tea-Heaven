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

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/simplebar.css') }}">

    <!-- Tea Heaven custom theme -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/tea-heaven-theme.css') }}">

    @yield('css')

    @include('layouts.head-css')
</head>

<body data-pc-preset="preset-6" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr"
    data-pc-theme="light">
    @include('layouts.loader')
    @include('layouts.sidebar')
    @include('layouts.topbar')

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
    <script src="{{ URL::asset('build/js/plugins/jquery.min.js') }}"></script>
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

            // Check localStorage on page load to restore sidebar state
            const savedSidebarState = localStorage.getItem('sidebarCollapsed');
            if (savedSidebarState === 'true') {
                document.body.classList.add('pc-sidebar-collapse');
            } else if (savedSidebarState === 'false') {
                document.body.classList.remove('pc-sidebar-collapse');
            }
        });
    </script>

    @include('layouts.footerjs')

    @yield('scripts')
</body>
<!-- [Body] end -->

</html>
