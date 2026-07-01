<!-- Metrik Ringkas FO -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <!-- Card 1: Antrean FO Saat Ini -->
    <div
        class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div>
                <p
                    class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                    Antrean FO Saat Ini</p>
                <h3 id="foStatAntrean" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono">
                    {{ $todayFoQueueCount ?? 0 }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Warga di ruang tunggu loket
                    depan</p>
            </div>
            <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg border border-status-waiting/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Tiket Dicetak -->
    <div
        class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div>
                <p
                    class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                    Total Tiket Dicetak Hari Ini</p>
                <h3 id="foStatTiket" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono">
                    {{ $todayTotalPrintedTickets ?? 0 }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Gabungan online check-in +
                    walk-in</p>
            </div>
            <div class="p-3 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
            </div>
        </div>
    </div>
</div>
