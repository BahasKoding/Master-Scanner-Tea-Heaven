<!DOCTYPE html>
<html lang="en">

<title>@yield('title') | Sistem Informasi Tea Heaven</title>
<!-- [Meta] -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="ERP Indo Agar Food." />
<meta name="author" content="phoenixcoded" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- [Favicon] icon -->
<link rel="icon" href="{{ URL::asset('build/images/ico32.ico') }}" type="image/x-icon">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

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

    @include('layouts.footerjs')

    @yield('scripts')
    <script src="{{ asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>
</body>
<!-- [Body] end -->

</html>
