<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title') | Tea Heaven Admin</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Tea Heaven - Teh Hijau Premium Indonesia">
    <meta name="keywords" content="admin, dashboard, tea, green tea, organic">
    <meta name="author" content="Tea Heaven">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

    <!-- System Fonts Alternative -->
    <style>
        :root {
            --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>

    <!-- Font Awesome -->
    <link href="{{ URL::asset('build/fonts/fontawesome.css') }}" rel="stylesheet">

    @yield('css')

    @include('layouts.head-css')

    <style>
        :root {
            --primary: #014421;
            --secondary: #F5F3E7;
            --tertiary: #7B5E57;
        }

        body {
            font-family: var(--font-family), sans-serif;
            background-color: #f8f9fa;
        }

        .auth-main {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--secondary);
            padding: 2rem 0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #013319;
            border-color: #013319;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        a {
            color: var(--primary);
        }

        a:hover {
            color: #013319;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(1, 68, 33, 0.25);
        }
    </style>
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

    <!-- SweetAlert2 -->
    <script src="{{ asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    @yield('scripts')
</body>

</html>
