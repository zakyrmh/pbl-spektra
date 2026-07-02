<!-- Bottom: Live Feed / Table (Recent Check-Ins) -->
<div
    class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-hairline dark:border-white/10">
        <div>
            <h3 class="font-bold text-ink dark:text-white font-display">Daftar Kedatangan Terkini</h3>
            <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Daftar warga yang baru saja
                check-in FO atau cetak tiket hari ini.</p>
        </div>
        <span
            class="bg-primary/10 text-primary dark:text-accent-teal text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse">
            Live Feed
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                    <th class="py-3 px-6">Nama Warga</th>
                    <th class="py-3 px-4">Kode Tiket</th>
                    <th class="py-3 px-4">Instansi Tujuan</th>
                    <th class="py-3 px-4">Jenis Kedatangan</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="foLiveFeedBody" class="text-xs divide-y divide-hairline dark:divide-white/5">
                @forelse($recentQueues as $q)
                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-6 font-bold text-ink dark:text-white">
                            {{ $q->user?->name ?? 'Walk-In Citizen' }}
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">
                            {{ $q->queue_number }}
                        </td>
                        <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">
                            {{ $q->department?->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-muted dark:text-on-dark-soft">
                            {{ $q->checked_in_at && $q->created_at->diffInSeconds($q->checked_in_at) > 5 ? 'Online Booking' : 'Walk-In (Tiket Mandiri)' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">
                            {{ $q->created_at->format('H:i') }}
                        </td>
                        <td class="py-3 px-6">
                            @php
                                $status = $q->status;
                                $badgeClass = match ($status) {
                                    'Waiting',
                                    'Checked-In'
                                        => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200/50',
                                    'Serving'
                                        => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200/50',
                                    'Completed'
                                        => 'bg-gray-100 dark:bg-gray-800/50 text-gray-700 dark:text-gray-400 border-gray-200/50',
                                    'Skipped'
                                        => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200/50',
                                    default
                                        => 'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 border-gray-200/50',
                                };
                                $dotClass = match ($status) {
                                    'Waiting', 'Checked-In' => 'bg-amber-500',
                                    'Serving' => 'bg-green-500',
                                    'Completed' => 'bg-gray-500',
                                    'Skipped' => 'bg-red-500',
                                    default => 'bg-gray-500',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                                <span class="w-1 h-1 rounded-full {{ $dotClass }}"></span>{{ $status }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right">
                            @if ($status === 'Waiting' || $status === 'Checked-In' || $status === 'Booked')
                                <button type="button"
                                    @click="openCancelModal('{{ route('admin.fo.bookings.cancel', $q->id) }}', '{{ $q->booking_code ?? $q->queue_number }}', '{{ $q->user ? addslashes($q->user->name) : 'Walk-In Citizen' }}', '{{ addslashes($q->service?->name ?? ($q->counter?->name ?? '-')) }}')"
                                    class="h-8 px-3.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/20 dark:hover:bg-red-950/40 dark:text-red-400 border border-red-200/60 dark:border-red-900/40 text-[10px] font-bold rounded-pill inline-flex items-center gap-1 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-red-500/20 transition-all cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batal
                                </button>
                            @else
                                <span class="text-muted text-[10px] font-medium">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-muted dark:text-on-dark-soft font-medium">
                            Belum ada aktivitas kedatangan hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
