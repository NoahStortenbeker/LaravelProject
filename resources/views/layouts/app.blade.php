<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FootLab')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-logged-in" content="{{ Auth::check() ? '1' : '0' }}">
    <meta name="auth-user-id" content="{{ Auth::id() ?? '' }}">
    <meta name="auth-is-admin" content="{{ (Auth::user()->is_admin ?? false) ? '1' : '0' }}">

    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
</head>
@php
    $hideHeader = request()->routeIs('checkout.payment', 'checkout.confirmation');
@endphp
<body class="fade-in {{ $hideHeader ? 'no_header' : '' }}">

    @if(! $hideHeader)
        @include('components.header')  {{-- header/navbar --}}
    @endif

    <main>
        @yield('content')
    </main>

    @include('components.footer')  {{-- footer --}}
</body>
</html>
