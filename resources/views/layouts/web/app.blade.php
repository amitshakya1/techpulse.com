<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="{{ $author }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- External CSS from CDN (e.g., Font Awesome, custom libraries) -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> --}}

    <!-- Local CSS from public folder -->
    {{-- <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> --}}

    <!-- Vite Compiled Assets (Your main CSS/JS) -->
    @vite(['resources/css/web.css', 'resources/js/web.js'])

    <!-- Page-specific styles from child views -->
    @stack('style')

    <!-- Page-specific head scripts (scripts that need to be in <head>) -->
    @stack('head-script')
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.web.header')
        {{-- @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset --}}
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        @include('layouts.web.footer')
    </div>

    <!-- External JS from CDN -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

    <!-- Local JS from public folder -->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}

    <!-- Page-specific scripts from child views -->
    @stack('script')
</body>

</html>
