{{-- Top Metrics --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
    {{-- Card 1: Total Gerai --}}
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-5 border border-hairline dark:border-white/10 flex items-center justify-between transition-all duration-300 hover:shadow-md shadow-sm relative overflow-hidden group">
        <div>
            <p class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-1 font-display">Total Gerai Instansi</p>
            <h3 class="text-display-sm sm:text-display-md font-bold text-ink dark:text-white font-mono leading-none">{{ $totalDepartments }}</h3>
            <p class="text-body-sm text-muted dark:text-on-dark-soft/70 mt-1.5">Dinas/lembaga terintegrasi</p>
        </div>
        <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center text-primary dark:text-accent-teal transition-all duration-300 group-hover:scale-110 shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
    </div>

    {{-- Card 2: Petugas Standby --}}
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-5 border border-hairline dark:border-white/10 flex items-center justify-between transition-all duration-300 hover:shadow-md shadow-sm relative overflow-hidden group">
        <div>
            <p class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-1 font-display">Total Petugas</p>
            <h3 class="text-display-sm sm:text-display-md font-bold text-ink dark:text-white font-mono leading-none">{{ $totalStaff }}</h3>
            <p class="text-body-sm text-muted dark:text-on-dark-soft/70 mt-1.5">Total petugas yang terdaftar</p>
        </div>
        <div class="w-12 h-12 bg-accent-teal/10 dark:bg-accent-teal/20 rounded-full flex items-center justify-center text-primary dark:text-accent-teal transition-all duration-300 group-hover:scale-110 shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
    </div>
</div>
