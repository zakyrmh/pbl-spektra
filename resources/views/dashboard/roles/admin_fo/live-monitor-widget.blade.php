<!-- Live Queue Monitor Widget (Gerai Status Board) -->
<div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between h-full space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-hairline dark:border-white/10">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-primary/10 text-primary dark:text-accent-teal rounded-md">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-ink dark:text-white font-display text-sm">Status Live Gerai & Loket MPP</h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Pantauan sisa antrean dan keaktifan gerai secara real-time</p>
            </div>
        </div>

        <a href="{{ route('display.index') }}" target="_blank"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-primary dark:text-accent-teal hover:underline bg-surface-soft dark:bg-white/5 rounded-pill border border-hairline dark:border-white/10 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            <span>Layar Display TV</span>
        </a>
    </div>

    <!-- Department Grid List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 overflow-y-auto max-h-[380px] pr-1">
        @forelse($departments as $dept)
            <div class="p-3.5 rounded-lg border border-hairline dark:border-white/10 bg-surface-soft/60 dark:bg-white/5 space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-7 h-7 rounded-full bg-primary/10 text-primary dark:text-accent-teal font-mono font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $dept->inisial ?? 'GR' }}
                        </span>
                        <span class="font-bold text-xs text-ink dark:text-white truncate font-display">{{ $dept->name }}</span>
                    </div>

                    @if($dept->is_open)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/10 text-green-700 dark:text-green-400 border border-green-500/20 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            BUKA
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/20 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            TUTUP
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-1 pt-1.5 border-t border-hairline/60 dark:border-white/5 text-center text-[10px]">
                    <div class="bg-amber-500/5 dark:bg-amber-400/5 p-1.5 rounded border border-amber-500/10">
                        <span class="text-muted dark:text-on-dark-soft block">Menunggu</span>
                        <span class="font-mono font-bold text-amber-700 dark:text-amber-400 text-xs">{{ $dept->waiting_count ?? 0 }}</span>
                    </div>
                    <div class="bg-blue-500/5 dark:bg-blue-400/5 p-1.5 rounded border border-blue-500/10">
                        <span class="text-muted dark:text-on-dark-soft block">Dilayani</span>
                        <span class="font-mono font-bold text-blue-600 dark:text-accent-teal text-xs">{{ $dept->serving_count ?? 0 }}</span>
                    </div>
                    <div class="bg-gray-500/5 dark:bg-gray-400/5 p-1.5 rounded border border-gray-500/10">
                        <span class="text-muted dark:text-on-dark-soft block">Selesai</span>
                        <span class="font-mono font-bold text-ink dark:text-white text-xs">{{ $dept->completed_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-8 text-center text-xs text-muted dark:text-on-dark-soft italic">
                Belum ada gerai terdaftar.
            </div>
        @endforelse
    </div>
</div>
