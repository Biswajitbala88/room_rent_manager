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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ✅ Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-200">
    <div x-data="{ sidebarOpen: false, desktopSidebarOpen: true }" class="flex h-screen bg-gray-200">
        <!-- Overlay for mobile sidebar -->
        <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
            class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
            <!-- Top Header -->
            @include('layouts.topbar')

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>