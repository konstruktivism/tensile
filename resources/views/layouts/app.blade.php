<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ Vite::asset('resources/img/favicon.svg') }}" type="image/svg+xml">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen dark:bg-dark dark:text-gray-300 font-sans">
<div id="app" class="h-full min-h-screen flex flex-col justify-between">
    @include('layouts.header')

    @if (isset($slot))
        {{ $slot }}
    @else
        <main class="p-6 h-full flex flex-col items-center grow">
            @yield('content')
        </main>
    @endif

    @include('layouts.footer')
</div>
@stack('scripts')
</body>
</html>
