<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title') | Sistem Informasi Tea Heaven</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Iaf Enterprise Resource Planning">
    <meta name="keywords"
        content="Laravel 11 Bootstrap IAF ERP">
    <meta name="author" content="iaf">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ URL::asset('build/images/ico32.ico') }}" type="image/x-icon">
    @yield('css')

    @include('layouts.head-css')
</head>

<body data-pc-preset="preset-6" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr"
    data-pc-theme="light">

    @include('layouts.loader')

    @if (View::hasSection('auth-v2'))
    <div class="auth-main v2">
        <div class="bg-overlay bg-dark"></div>
        <div class="auth-wrapper">
            <div class="auth-sidecontent">
                @include('layouts.authFooter')
            </div>
            @else
            <div class="auth-main v1">
                <div class="auth-wrapper">
                    @endif
                    @yield('content')
                    @if (!View::hasSection('auth-v2'))
                    {{-- @include('layouts.authFooter') --}}
                    @endif
                </div>
            </div>
            @if (View::hasSection('auth-v2'))
        </div>
        @endif
        @include('layouts.customizer')

        @include('layouts.footerjs')

        @yield('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>
</body>

</html>