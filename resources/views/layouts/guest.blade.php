<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-logged-in" content="{{ Auth::check() ? '1' : '0' }}">
    <meta name="auth-user-id" content="{{ Auth::id() ?? '' }}">
    <meta name="auth-is-admin" content="{{ (Auth::user()->is_admin ?? false) ? '1' : '0' }}">
    <title>{{ config('app.name', 'FootLab') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS -->
    @vite([
    'resources/sass/app.scss',
    'resources/js/app.js',
])
</head>
<body>
    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
