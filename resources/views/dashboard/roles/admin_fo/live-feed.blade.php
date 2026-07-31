<!-- Live Activity Feed Table -->
<div class="bg-canvas dark:bg-surface-dark-elevated p-5 sm:p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-3">
        <div>
            <h3 class="font-extrabold text-ink dark:text-white font-display text-base">Daftar Kedatangan & Karcis Terkini</h3>
            <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Data warga yang baru saja melakukan check-in FO atau penerbitan karcis hari ini</p>
        </div>
        <span class="bg-primary/10 text-primary dark:text-accent-teal text-[10px] font-bold px-2.5 py-1 rounded-pill uppercase tracking-wider animate-pulse font-mono border border-primary/20">
            Live Stream
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10 font-display">
                    <th class="py-3 px-4">Nama Warga</th>
                    <th class="py-3 px-4">Nomor Antrean</th>
                    <th class="py-3 px-4">Instansi Tujuan</th>
                    <th class="py-3 px-4">Kanal Kedatangan</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="foLiveFeedBody" class="text-xs divide-y divide-hairline dark:divide-white/5 font-body">
                @forelse($recentQueues as $q)
                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3.5 px-4 font-bold text-ink dark:text-white">
                            {{ $q->user?->name ?? 'Warga (Walk-In)' }}
                            @if($q->is_priority)
                                <span class="ml-1 text-[10px] text-amber-600 dark:text-accent-gold font-bold">🌟 Prioritas</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-primary dark:text-accent-teal text-sm">
                            {{ $q->queue_number ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-ink dark:text-white">
                            {{ $q->department?->name ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft">
                            @if($q->checked_in_at && $q->created_at->diffInSeconds($q->checked_in_at) > 5)
                                <span class="inline-flex items-center gap-1 text-blue-600 dark:text-accent-teal font-medium">📱 Booking Online</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-slate-700 dark:text-gray-300 font-medium">🎫 Walk-In FO</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-muted dark:text-on-dark-soft">
                            {{ $q->created_at->format('H:i') }}
                        </td>
                        <td class="py-3.5 px-4">
                            @php
                                $statusVal = $q->status->value ?? $q->status;
                                $badgeClass = match ($statusVal) {
                                    'Waiting', 'Checked-In', 'Booked' => 'bg-amber-500/10 text-amber-800 dark:text-amber-400 border-amber-500/20',
                                    'Serving' => 'bg-green-500/10 text-green-800 dark:text-green-400 border-green-500/20',
                                    'Completed' => 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20',
                                    'Skipped', 'Cancelled' => 'bg-red-500/10 text-red-800 dark:text-red-400 border-red-500/20',
                                    default => 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20',
                                };
                                $dotClass = match ($statusVal) {
                                    'Waiting', 'Checked-In', 'Booked' => 'bg-amber-500',
                                    'Serving' => 'bg-green-500',
                                    'Completed' => 'bg-gray-500',
                                    'Skipped', 'Cancelled' => 'bg-red-500',
                                    default => 'bg-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-[10px] font-bold border {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                <span>{{ $statusVal }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            @if (in_array($statusVal, ['Waiting', 'Checked-In', 'Booked']))
                                <button type="button"
                                    @click="openCancelModal('{{ route('admin.fo.bookings.cancel', $q->id) }}', '{{ $q->booking_code ?? $q->queue_number }}', '{{ $q->user ? addslashes($q->user->name) : 'Warga' }}', '{{ addslashes($q->purpose ?? '') }}')"
                                    class="h-7 px-3 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/20 dark:hover:bg-red-950/40 dark:text-red-400 border border-red-200/60 dark:border-red-900/40 text-[10px] font-bold rounded-pill inline-flex items-center gap-1 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-red-500/20 transition-all cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Batalkan</span>
                                </button>
                            @else
                                <span class="text-muted text-[10px] font-medium">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-muted dark:text-on-dark-soft font-medium">
                            Belum ada aktivitas kedatangan terdaftar hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
