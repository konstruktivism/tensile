<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Albert+Sans" rel="stylesheet">

    <link rel="icon" href="{{ Vite::asset('resources/img/favicon.svg') }}" type="image/svg+xml">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased h-full">
<div id="app" class="h-full flex flex-col justify-between">
    @include('layouts.header')

    <main class="p-6 h-full">
        @yield('content')
    </main>

    @include('layouts.footer')
</div>
</body>
</html>
