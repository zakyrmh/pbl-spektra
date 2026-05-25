<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0 shadow-sm">
    {{-- Left: Mobile Menu Button & Title --}}
    <div class="flex items-center gap-4">
        <button type="button" class="md:hidden text-gray-500 hover:text-blue-600 focus:outline-none transition-colors p-1">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-xl font-bold text-gray-800 hidden sm:block">
            @yield('title', 'Dashboard')
        </h1>
    </div>

    {{-- Right: Notifications & User Menu --}}
    <div class="flex items-center gap-4">
        
        {{-- Notification Bell --}}
        <button class="relative p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span class="absolute top-1.5 right-1.5 block w-2.5 h-2.5 rounded-full bg-red-500 ring-2 ring-white shadow-sm"></span>
        </button>

        {{-- Divider --}}
        <div class="hidden sm:block w-px h-8 bg-gray-200"></div>

        {{-- User Menu --}}
        <div class="relative flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-800 leading-tight">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs font-medium text-gray-500">
                    {{ Auth::user()->role_label }}
                </p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="m-0 pl-2">
                @csrf
                <button type="submit"
                    class="px-3.5 py-2 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-all duration-200 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-red-500/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
