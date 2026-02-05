<div :class="{
        'translate-x-0 ease-out': sidebarOpen,
        '-translate-x-full ease-in': !sidebarOpen,
        'lg:w-64': desktopSidebarOpen,
        'lg:w-20': !desktopSidebarOpen,
        'lg:translate-x-0': true,
        'lg:static': true,
        'lg:inset-0': true,
        'overflow-y-auto': desktopSidebarOpen, 
        'overflow-visible': !desktopSidebarOpen
    }" class="fixed z-30 inset-y-0 left-0 w-64 transition-all duration-300 transform bg-gray-900 lg:inset-auto">

    <!-- Logo -->
    <div class="flex items-center justify-center mt-8 h-10">
        <div class="flex items-center">
            <!-- Full Logo -->
            <span x-show="desktopSidebarOpen"
                class="text-white text-2xl mx-2 font-semibold transition-opacity duration-300">{{ config('app.name', 'Laravel') }}</span>
            <!-- Icon Logo (when collapsed) -->
            <span x-show="!desktopSidebarOpen"
                class="text-white text-2xl font-bold transition-opacity duration-300 hidden lg:block">L</span>
        </div>
    </div>

    <nav class="mt-10">
        <a class="flex items-center mt-4 py-2 px-6 relative group {{ request()->routeIs('dashboard') ? 'bg-gray-700 bg-opacity-25 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
            href="{{ route('dashboard') }}">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>

            <span x-show="desktopSidebarOpen" class="mx-3 transition-opacity duration-300">Dashboard</span>

            <!-- Tooltip -->
            <div x-show="!desktopSidebarOpen"
                class="absolute left-14 ml-4 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Dashboard
            </div>
        </a>

        <a class="flex items-center mt-4 py-2 px-6 relative group {{ request()->routeIs('tenants.*') ? 'bg-gray-700 bg-opacity-25 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
            href="{{ route('tenants.index') }}">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>

            <span x-show="desktopSidebarOpen" class="mx-3 transition-opacity duration-300">Tenants</span>

            <!-- Tooltip -->
            <div x-show="!desktopSidebarOpen"
                class="absolute left-14 ml-4 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Tenants
            </div>
        </a>

        <a class="flex items-center mt-4 py-2 px-6 relative group {{ request()->routeIs('invoices.*') ? 'bg-gray-700 bg-opacity-25 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
            href="{{ route('invoices.index') }}">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <span x-show="desktopSidebarOpen" class="mx-3 transition-opacity duration-300">Invoices</span>

            <!-- Tooltip -->
            <div x-show="!desktopSidebarOpen"
                class="absolute left-14 ml-4 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Invoices
            </div>
        </a>

        <a class="flex items-center mt-4 py-2 px-6 relative group {{ request()->routeIs('electricity.*') ? 'bg-gray-700 bg-opacity-25 text-gray-100' : 'text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}"
            href="{{ route('electricity.index') }}">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>

            <span x-show="desktopSidebarOpen" class="mx-3 transition-opacity duration-300">Electricity</span>

            <!-- Tooltip -->
            <div x-show="!desktopSidebarOpen"
                class="absolute left-14 ml-4 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Electricity
            </div>
        </a>
    </nav>
</div>