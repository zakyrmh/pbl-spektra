<header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 bg-canvas dark:bg-surface-dark-elevated border-b border-hairline dark:border-white/10 z-10 shrink-0">
    {{-- Left: Mobile Menu Button & Title --}}
    <div class="flex items-center gap-4">
        <button type="button"
            class="md:hidden w-11 h-11 flex items-center justify-center text-muted dark:text-on-dark-soft hover:text-primary dark:hover:text-white transition-colors duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal rounded-md">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="hidden sm:block text-title-lg font-display font-semibold text-ink dark:text-white">
            @yield('title', 'Dashboard')
        </h1>
    </div>

    {{-- Right: Notifications & User Menu --}}
    <div class="flex items-center gap-4">
        
        {{-- Notification Bell --}}
        <button class="relative w-11 h-11 flex items-center justify-center text-muted dark:text-on-dark-soft hover:text-primary dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/5 rounded-full transition-all duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span class="absolute top-2 right-2 block w-2.5 h-2.5 rounded-full bg-status-skipped ring-2 ring-canvas dark:ring-surface-dark-elevated shadow-sm animate-pulse"></span>
        </button>

        {{-- Divider --}}
        <div class="hidden sm:block w-px h-8 bg-hairline dark:bg-white/10"></div>

        {{-- User Menu --}}
        <div class="relative flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-body-sm font-semibold text-ink dark:text-white leading-tight">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-caption font-medium text-muted dark:text-on-dark-soft capitalize">
                    {{ str_replace('_', ' ', Auth::user()->role) }}
                </p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="m-0 pl-2">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 h-11 px-4 rounded-md text-button font-semibold text-status-skipped dark:text-red-400 bg-status-skipped/12 dark:bg-status-skipped/20 hover:bg-status-skipped/20 dark:hover:bg-status-skipped/30 transition-all duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

