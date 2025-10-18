<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @include('layouts.head-shared')
    <!-- Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @endif

    @stack('style')

    <!-- Page-specific head scripts (scripts that need to be in <head>) -->
    @stack('head-script')
</head>

<body x-data="{ page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">
    <x-admin.preloader />
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
        <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
            <!-- Form -->
            <div class="flex flex-col flex-1 w-full lg:w-1/2">
                <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                    <div class="w-full max-w-md py-5 mx-auto">
                        @if (request()->route()->getName() !== 'admin.login')
                            <div class="w-full max-w-md py-2 mx-auto">
                                <a href="{{ route('admin.login') }}"
                                    class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke=""
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Back to login
                                </a>
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <div class="relative items-center hidden w-full h-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/2">
                <div class="flex items-center justify-center z-1">
                    <!-- ===== Common Grid Shape Start ===== -->
                    <x-admin.common-grid-shape />
                    <div class="flex flex-col items-center max-w-xs">
                        <a href="{{ route('admin.login') }}" class="block mb-4">
                            <img src="{{ config('app.brand.logo.dark') }}" alt="{{ config('app.name') }}" />
                        </a>
                        <p class="text-center text-gray-400 dark:text-white/60">
                            Free and Open-Source Tailwind CSS Admin Dashboard Template
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-admin.theme-mode />
    @include('layouts.footer-shared')
    @stack('script')
</body>

</html>
