<header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
    <div class="flex items-center">
        <!-- Mobile Hamburger -->
        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

         <!-- Desktop Hamburger -->
         <button @click="desktopSidebarOpen = !desktopSidebarOpen" class="text-gray-500 focus:outline-none hidden lg:block mr-4">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="relative mx-4 lg:mx-0">
           {{ $header ?? '' }}
        </div>
    </div>

    <div class="flex items-center">
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = ! dropdownOpen" class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none">
                <img class="h-full w-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random" alt="Your avatar">
            </button>

            <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>

            <div x-show="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-20" style="display: none;">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profile</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Log out</a>
                </form>
            </div>
        </div>
    </div>
</header>