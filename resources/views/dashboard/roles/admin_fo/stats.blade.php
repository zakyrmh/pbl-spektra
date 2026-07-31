<!-- Metrik Ringkas High Density FO -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Card 1: Pending Check-In Online -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider font-display block">Menunggu Check-In</span>
                <h3 id="foStatAntrean" class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-white font-mono tracking-tight">
                    {{ $todayFoQueueCount ?? 0 }}
                </h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Booking online belum scan di FO</p>
            </div>
            <div class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg border border-amber-500/20 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Tiket Terbit Hari Ini -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-primary dark:text-accent-teal uppercase tracking-wider font-display block">Total Karcis Terbit</span>
                <h3 id="foStatTiket" class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-white font-mono tracking-tight">
                    {{ $todayTotalPrintedTickets ?? 0 }}
                </h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Gabungan online check-in + walk-in</p>
            </div>
            <div class="p-2.5 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: Status Gerai Layanan Aktif -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-green-700 dark:text-green-400 uppercase tracking-wider font-display block">Gerai Layanan Buka</span>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-white font-mono tracking-tight">
                    {{ $departments->where('is_open', true)->count() }} <span class="text-sm font-sans font-normal text-muted dark:text-on-dark-soft">/ {{ $departments->count() }}</span>
                </h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Gerai aktif melayani warga</p>
            </div>
            <div class="p-2.5 bg-green-500/10 text-green-600 dark:text-green-400 rounded-lg border border-green-500/20 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0H9m11 0h-5" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4: Kapasitas Loket & Petugas Paralel -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-700 dark:text-on-dark-soft uppercase tracking-wider font-display block">Kapasitas Petugas FO</span>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-white font-mono tracking-tight">
                    4 <span class="text-xs font-sans font-medium text-muted dark:text-on-dark-soft">Loket</span>
                </h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Petugas bekerja secara paralel</p>
            </div>
            <div class="p-2.5 bg-slate-500/10 text-slate-600 dark:text-on-dark-soft rounded-lg border border-slate-500/20 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>
