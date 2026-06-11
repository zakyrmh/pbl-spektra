<header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 bg-canvas dark:bg-surface-dark-elevated border-b border-hairline dark:border-white/10 z-10 shrink-0">
    {{-- Left: Mobile Menu Button & Title --}}
    <div class="flex items-center gap-4">
        <button type="button" @click="sidebarOpen = !sidebarOpen"
            class="md:hidden w-11 h-11 flex items-center justify-center text-muted dark:text-on-dark-soft hover:text-primary dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/5 rounded-full transition-all duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="hidden lg:block text-title-lg font-display font-semibold text-ink dark:text-white">
            @yield('title', 'Dashboard')
        </h1>
    </div>

    {{-- Right: Notifications & User Menu --}}
    <div class="flex items-center gap-4">

        {{-- Light/Dark Mode Toggle --}}
        <button onclick="toggleTheme()" type="button" class="w-11 h-11 flex items-center justify-center text-muted dark:text-on-dark-soft hover:text-primary dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/5 rounded-full transition-all duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer" aria-label="Toggle theme">
            <!-- Moon icon (shows in light mode) -->
            <svg class="w-6 h-6 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
            </svg>
            <!-- Sun icon (shows in dark mode) -->
            <svg class="w-6 h-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
        </button>
        
        {{-- Notification Bell --}}
        <a href="{{ route('notifications.index') }}" class="relative w-11 h-11 flex items-center justify-center text-muted dark:text-on-dark-soft hover:text-primary dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/5 rounded-full transition-all duration-150 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal" aria-label="Notifikasi">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            @php
                $unreadCount = Auth::user()->unreadNotifications()->count();
            @endphp
            @if ($unreadCount > 0)
                <span class="absolute top-2 right-2 block w-2.5 h-2.5 rounded-full bg-status-skipped ring-2 ring-canvas dark:ring-surface-dark-elevated shadow-sm animate-pulse"></span>
            @endif
        </a>

        {{-- Divider --}}
        <div class="hidden sm:block w-px h-8 bg-hairline dark:bg-white/10"></div>

        {{-- User Menu --}}
        <div class="relative flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-body-sm font-semibold text-ink dark:text-white leading-tight">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs font-medium text-gray-500">
                    {{ Auth::user()->role_label }}
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

<script>
    function toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }
</script>

