<!-- Header Command Center Banner -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-5 sm:p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
    <div class="space-y-1">
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
            </span>
            <span class="text-xs font-bold text-primary dark:text-accent-teal uppercase tracking-wider font-display">Command Center · Front Office</span>
            <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-surface-soft dark:bg-white/10 text-muted dark:text-on-dark-soft rounded border border-hairline dark:border-white/10">4 Petugas Paralel</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-ink dark:text-white font-display tracking-tight">Pusat Layanan Front Office MPP</h2>
        <p class="text-xs sm:text-sm text-muted dark:text-on-dark-soft font-body max-w-2xl">
            Satu layar kerja terpadu untuk pencetakan karcis walk-in, verifikasi booking online, dan pemantauan status gerai secara real-time.
        </p>
    </div>

    <!-- Quick Action Bar & Live Clock -->
    <div class="flex flex-wrap items-center gap-3 shrink-0">
        {{-- Tombol Utama 1: Cetak Karcis Walk-In (Alt+N) --}}
        <button type="button" @click="openTicketDrawer()"
            class="h-11 px-5 bg-primary hover:bg-primary-hover text-on-primary font-semibold rounded-pill text-xs sm:text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shadow-md flex items-center gap-2 active:scale-95">
            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <span>Cetak Karcis Walk-In</span>
            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono font-bold bg-white/20 text-white rounded border border-white/30 ml-1">Alt+N</kbd>
        </button>

        {{-- Tombol Utama 2: Fast QR Check-In (Alt+C) --}}
        <button type="button" @click="openCheckinModal()"
            class="h-11 px-5 bg-canvas hover:bg-surface-soft text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/15 font-semibold rounded-pill text-xs sm:text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center gap-2 active:scale-95">
            <svg class="w-4.5 h-4.5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span>Scan QR / Check-In</span>
            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono font-bold bg-surface-soft dark:bg-white/10 text-muted dark:text-on-dark-soft rounded border border-hairline dark:border-white/10 ml-1">Alt+C</kbd>
        </button>

        {{-- Live Clock Display --}}
        <div class="hidden sm:flex items-center gap-2 text-xs font-mono font-bold text-ink dark:text-white bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3.5 h-11 rounded-pill"
            id="fo-live-clock">
            <svg class="w-4 h-4 text-muted dark:text-on-dark-soft shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>--:--:--</span>
        </div>
    </div>
</div>
