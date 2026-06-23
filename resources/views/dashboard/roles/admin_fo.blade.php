{{-- Admin FO Dashboard --}}
<div class="space-y-6 pb-16" x-data="{
    modalOpen: false,
    actionUrl: '',
    bookingCode: '',
    userName: '',
    serviceName: '',
    reason: '',
    openCancelModal(url, code, name, service) {
        console.log('openCancelModal called:', { url, code, name, service });
        this.actionUrl = url;
        this.bookingCode = code;
        this.userName = name;
        this.serviceName = service;
        this.reason = '';
        this.modalOpen = true;
    }
}">
    <!-- Header Banner -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <span
                    class="text-xs font-semibold text-primary dark:text-accent-teal uppercase tracking-wider font-display">Front
                    Office Active</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Dashboard Admin Front Office</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Layanan verifikasi kedatangan online dan
                pencetakan tiket mandiri warga (walk-in).</p>
        </div>
        <div class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md"
            id="fo-live-clock">
            Loading waktu...
        </div>
    </div>

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

    <!-- Main Working Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Fast-Track Verification Module (Spans 5 cols) -->
        <div
            class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-hairline dark:border-white/10">
                    <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="font-bold text-ink dark:text-white font-display">Verifikasi Cepat Booking</h3>
                </div>

                <p class="text-xs text-muted dark:text-on-dark-soft leading-relaxed font-body">
                    Masukkan kode booking online warga (tertera di tiket PDF/WA) atau NIK warga untuk check-in kedatangan mereka
                    secara langsung.
                </p>

                <div class="space-y-3 pt-2">
                    <div>
                        <label for="txtBookingCode"
                            class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Kode Booking / NIK Warga</label>
                        <div class="flex gap-2">
                            <input type="text" id="txtBookingCode" placeholder="Contoh: 550e8400-e29b-41d4-a716-446655440000 atau NIK 16 digit"
                                class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                            <button type="button" onclick="verifyBookingCode()"
                                class="h-11 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                Verifikasi
                            </button>
                        </div>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-1.5 font-body">
                            * Masukkan 36 karakter kode unik (UUID) booking atau 16 digit NIK warga untuk pencarian.
                        </p>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="grow border-t border-hairline dark:border-white/10"></div>
                        <span
                            class="shrink mx-4 text-xs font-semibold text-muted dark:text-on-dark-soft uppercase font-display">Atau</span>
                        <div class="grow border-t border-hairline dark:border-white/10"></div>
                    </div>

                    <button type="button" onclick="simulateQrScanner()"
                        class="w-full h-11 flex items-center justify-center gap-2 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-md text-xs border border-hairline dark:border-white/10 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Simulasi Scan QR Scanner
                    </button>
                </div>
            </div>

            <!-- Citizen verification details panel (Hidden by default, shown dynamically) -->
            <div id="pnlVerifyDetails"
                class="hidden mt-6 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-md p-4 space-y-4 transition-all">
                <div class="flex items-center justify-between pb-2 border-b border-hairline dark:border-white/10">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Hasil
                        Deteksi Tiket</span>
                    <span
                        class="text-xs bg-status-waiting/20 text-status-waiting px-2 py-0.5 rounded-full font-bold uppercase tracking-wider font-display"
                        id="verifyTicketCode">550e8400-e29b-41d4-a716-446655440000</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Nama Warga:</span>
                        <span class="font-bold text-ink dark:text-white" id="verifyName">Rahmat Hidayat</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">NIK:</span>
                        <span class="font-mono text-ink dark:text-white" id="verifyNik">1373021408990002</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Instansi Tujuan:</span>
                        <span class="font-bold text-ink dark:text-white" id="verifyTenant">Disdukcapil</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Layanan:</span>
                        <span class="font-bold text-primary dark:text-accent-teal" id="verifyService">Cetak
                            KTP-el</span>
                    </div>
                </div>
                <button type="button" onclick="confirmCheckIn()"
                    class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer">
                    Konfirmasi Kedatangan (Check-In)
                </button>
            </div>
        </div>

        <!-- Right: Kios Cetak Tiket Mandiri (Spans 7 cols) -->
        <div
            class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
            <div class="flex items-center gap-2 pb-4 border-b border-hairline dark:border-white/10 mb-4">
                <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <h3 class="font-bold text-ink dark:text-white font-display">Kios Cetak Tiket Mandiri (Walk-In)</h3>
            </div>

            <p class="text-xs text-muted dark:text-on-dark-soft mb-4 font-body">Klik salah satu Instansi, pilih jenis
                layanan, kemudian cetak tiket antrean langsung untuk warga.</p>

            <!-- Walk-In Form -->
            <div class="space-y-4" id="kioskWalkInForm">
                <!-- NIK Input -->
                <div>
                    <label for="txtWalkInNik"
                        class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">NIK
                        Warga (16 Digit)</label>
                    <div class="flex gap-2">
                        <input type="text" id="txtWalkInNik" maxlength="16" placeholder="Masukkan 16 digit NIK"
                            class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <button type="button" onclick="checkVisitorNik()"
                            class="h-11 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-blue-500/50 cursor-pointer shadow-sm">
                            Cek NIK
                        </button>
                    </div>
                </div>

                <!-- Nama Lengkap Input -->
                <div>
                    <label for="txtWalkInName"
                        class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Nama
                        Lengkap</label>
                    <input type="text" id="txtWalkInName" disabled
                        placeholder="Nama warga (otomatis / isi jika baru)"
                        class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-50 disabled:cursor-not-allowed">
                </div>

                <!-- Nomor Telepon Input -->
                <div>
                    <label for="txtWalkInPhone"
                        class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Nomor
                        Telepon / HP</label>
                    <input type="text" id="txtWalkInPhone" disabled
                        placeholder="Nomor telepon warga (otomatis / isi jika baru)"
                        class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-50 disabled:cursor-not-allowed"
                        oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                </div>

                <!-- Instansi Selection -->
                <div>
                    <label for="selWalkInDept"
                        class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Instansi
                        Tujuan</label>
                    <select id="selWalkInDept"
                        class="w-full h-11 bg-surface-soft dark:bg-surface-dark border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        <option value="">-- Pilih Instansi --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Keperluan -->
                <div>
                    <label for="txtWalkInPurpose"
                        class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Keperluan</label>
                    <textarea id="txtWalkInPurpose" rows="2" placeholder="Tuliskan keperluan kedatangan secara singkat..."
                        class="w-full bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md p-3 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal"></textarea>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="printWalkInTicket()"
                        class="w-full h-11 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shadow-md">
                        Cetak Tiket Mandiri
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    {{-- Cancellation Modal Overlay --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>

        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-6 md:p-8 max-w-md w-full border border-hairline dark:border-white/10 shadow-2xl transform transition-all duration-300 relative"
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            @click.away="modalOpen = false">

            <button type="button" @click="modalOpen = false"
                class="absolute top-4 right-4 text-muted hover:text-ink dark:hover:text-white p-1 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors cursor-pointer">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display mb-2">Konfirmasi
                Pembatalan</h3>
            <p class="text-xs text-muted dark:text-on-dark-soft mb-6 font-body">Anda akan membatalkan reservasi antrean
                berikut. Aksi ini tidak dapat diurungkan.</p>

            <div
                class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 p-4 rounded-lg text-xs space-y-2 mb-6">
                <div class="flex justify-between">
                    <span class="text-muted font-medium">Kode Antrean</span>
                    <span class="font-mono font-bold text-primary dark:text-accent-teal" x-text="bookingCode"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted font-medium">Nama Warga</span>
                    <span class="font-bold text-ink dark:text-white" x-text="userName"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted font-medium">Layanan</span>
                    <span class="font-bold text-ink dark:text-white" x-text="serviceName"></span>
                </div>
            </div>

            <form :action="actionUrl" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="reason" class="block text-sm font-bold text-ink dark:text-white font-display">
                        Alasan Pembatalan
                    </label>
                    <textarea id="reason" name="reason" rows="3" required x-model="reason"
                        placeholder="Contoh: Dokumen persyaratan tidak lengkap, atau atas permohonan warga..."
                        class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all"></textarea>
                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-body">Minimal 5 karakter. Alasan ini
                        akan dicantumkan pada notifikasi email warga.</p>
                </div>

                <div class="pt-4 border-t border-hairline dark:border-white/10 flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false"
                        class="h-11 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                        Kembali
                    </button>
                    <button type="submit" :disabled="reason.trim().length < 5"
                        :class="reason.trim().length < 5 ? 'opacity-50 cursor-not-allowed bg-red-600/50' :
                            'bg-red-600 hover:bg-red-700'"
                        class="h-11 px-6 text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Batalkan Booking
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
</div>

<!-- JavaScript Simulation Logic for FO -->
<script>
    // Stats state
    let foStats = {
        antreanFO: {{ $todayFoQueueCount ?? 0 }},
        tiketDicetak: {{ $todayTotalPrintedTickets ?? 0 }}
    };

    // Global variable to store verified booking ID
    let currentVerifiedBookingId = null;

    // Live clock
    function updateClock() {
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateStr = d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short'
        });
        const clockEl = document.getElementById('fo-live-clock');
        if (clockEl) {
            clockEl.innerText = `${dateStr} | ${timeStr}`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
    });

    // Verification module
    async function verifyBookingCode() {
        const input = document.getElementById('txtBookingCode');
        const code = input.value.trim().toUpperCase();

        if (code === '') {
            alert("Silakan masukkan kode booking terlebih dahulu!");
            return;
        }

        try {
            const response = await fetch(`/api/fo/bookings/verify?code=${code}`);

            if (!response.ok) {
                if (response.status === 404) {
                    createToast('Tiket Tidak Ditemukan', `Kode booking ${code} tidak terdaftar di database.`,
                        'warning');
                } else {
                    createToast('Error Sistem', `Gagal memverifikasi tiket (Status: ${response.status}).`,
                        'warning');
                }
                document.getElementById('pnlVerifyDetails').classList.add('hidden');
                currentVerifiedBookingId = null;
                return;
            }

            const data = await response.json();

            // Map JSON response to DOM elements
            document.getElementById('verifyTicketCode').innerText = data.booking_code || code;
            document.getElementById('verifyName').innerText = (data.user && data.user.name) ? data.user.name : '-';
            document.getElementById('verifyNik').innerText = (data.user && data.user.nik) ? data.user.nik : '-';

            // Nama instansi di-resolve dari relasi booking->counter->department
            const departmentName = (data.department && data.department.name) ||
                (data.counter && data.counter.department && data.counter.department.name) ||
                '-';
            document.getElementById('verifyTenant').innerText = departmentName;

            // Layanan di-resolve dari counter name atau service name
            const serviceName = (data.counter && data.counter.name) ||
                (data.service && data.service.name) ||
                '-';
            document.getElementById('verifyService').innerText = serviceName;

            currentVerifiedBookingId = data.id;

            const pnl = document.getElementById('pnlVerifyDetails');
            pnl.classList.remove('hidden');

            createToast('Tiket Ditemukan', `Kode booking ${code} berhasil diverifikasi. Silakan klik Konfirmasi.`,
                'info');
        } catch (error) {
            console.error('Error verifying booking:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk verifikasi tiket.', 'warning');
        }
    }

    function simulateQrScanner() {
        const codes = [
            '550e8400-e29b-41d4-a716-446655440000',
            'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d',
            'f81d4fae-7dec-11d0-a765-00a0c91e6bf6'
        ];
        const randomCode = codes[Math.floor(Math.random() * codes.length)];
        document.getElementById('txtBookingCode').value = randomCode;
        verifyBookingCode();
    }

    async function confirmCheckIn() {
        if (!currentVerifiedBookingId) {
            createToast('Peringatan', 'Silakan verifikasi kode booking terlebih dahulu.', 'warning');
            return;
        }

        const code = document.getElementById('verifyTicketCode').innerText;
        const name = document.getElementById('verifyName').innerText;
        const tenant = document.getElementById('verifyTenant').innerText;

        try {
            const response = await fetch(`/api/fo/bookings/${currentVerifiedBookingId}/checkin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal konfirmasi check-in (Status: ${response.status}).`;
                createToast('Gagal Check-In', errMsg, 'warning');
                return;
            }

            const data = await response.json();

            // Modify local stats and update DOM
            if (foStats.antreanFO > 0) foStats.antreanFO--;
            foStats.tiketDicetak++;

            document.getElementById('foStatAntrean').innerText = foStats.antreanFO;
            document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

            // Hide details panel
            document.getElementById('pnlVerifyDetails').classList.add('hidden');
            document.getElementById('txtBookingCode').value = '';

            // Add to live feed with state parameter 'Waiting' (instead of 'Check-In FO')
            const finalCode = data.queue_number || data.ticket_code || code;
            addLiveFeedRow(name, finalCode, tenant, 'Online Booking', 'Waiting');

            createToast('Check-In Sukses', `Warga ${name} (${finalCode}) telah check-in untuk loket ${tenant}.`,
                'success');

            // Reset global verified booking ID
            currentVerifiedBookingId = null;
        } catch (error) {
            console.error('Error confirming checkin:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk konfirmasi check-in.', 'warning');
        }
    }

    // Walk-In Module
    async function checkVisitorNik() {
        const inputNik = document.getElementById('txtWalkInNik');
        const inputName = document.getElementById('txtWalkInName');
        const inputPhone = document.getElementById('txtWalkInPhone');
        const nik = inputNik.value.trim();

        if (nik.length !== 16) {
            createToast('NIK Tidak Valid', 'Pastikan NIK terdiri dari 16 digit angka.', 'warning');
            return;
        }

        try {
            const response = await fetch(`/api/fo/visitors/check-nik?nik=${nik}`);

            if (response.status === 404) {
                inputName.value = '';
                inputName.disabled = false;
                inputPhone.value = '';
                inputPhone.disabled = false;
                inputName.focus();
                createToast('NIK Baru', 'Data tidak ditemukan. Silakan isi nama lengkap dan nomor telepon warga.',
                    'info');
                return;
            }

            if (!response.ok) {
                createToast('Gagal', 'Terjadi kesalahan saat memeriksa NIK.', 'warning');
                return;
            }

            const resData = await response.json();
            const data = resData.data || resData;

            if (data.is_found) {
                inputName.value = data.name;
                inputName.disabled = true;
                inputPhone.value = data.no_telp || '';
                inputPhone.disabled = true;
                createToast('NIK Ditemukan', `Data warga ${data.name} berhasil dimuat.`, 'success');
            } else {
                inputName.value = '';
                inputName.disabled = false;
                inputPhone.value = '';
                inputPhone.disabled = false;
                inputName.focus();
                createToast('NIK Baru', 'Data tidak ditemukan. Silakan isi nama lengkap dan nomor telepon warga.',
                    'info');
            }
        } catch (error) {
            console.error('Error checking NIK:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'warning');
        }
    }

    function resetWalkInForm() {
        document.getElementById('txtWalkInNik').value = '';
        const nameInput = document.getElementById('txtWalkInName');
        nameInput.value = '';
        nameInput.disabled = true;
        const phoneInput = document.getElementById('txtWalkInPhone');
        phoneInput.value = '';
        phoneInput.disabled = true;
        document.getElementById('selWalkInDept').value = '';
        document.getElementById('txtWalkInPurpose').value = '';
    }

    async function printWalkInTicket() {
        const nik = document.getElementById('txtWalkInNik').value.trim();
        const name = document.getElementById('txtWalkInName').value.trim();
        const phone = document.getElementById('txtWalkInPhone').value.trim();
        const deptId = document.getElementById('selWalkInDept').value;
        const purpose = document.getElementById('txtWalkInPurpose').value.trim();

        if (nik.length !== 16) {
            createToast('Peringatan', 'NIK harus 16 digit.', 'warning');
            return;
        }
        if (!name) {
            createToast('Peringatan', 'Nama lengkap tidak boleh kosong.', 'warning');
            return;
        }
        if (!phone) {
            createToast('Peringatan', 'Nomor telepon wajib diisi.', 'warning');
            return;
        }
        const phoneRegex = /^(08[0-9]{8,13}|\+628[0-9]{8,11})$/;
        if (!phoneRegex.test(phone)) {
            createToast('Peringatan',
                'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 angka).', 'warning');
            return;
        }
        if (!deptId) {
            createToast('Peringatan', 'Silakan pilih Instansi Tujuan.', 'warning');
            return;
        }
        if (!purpose) {
            createToast('Peringatan', 'Keperluan kedatangan wajib diisi.', 'warning');
            return;
        }

        try {
            const response = await fetch('/api/fo/queues/walkin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nik: nik,
                    name: name,
                    phone: phone,
                    department_id: deptId,
                    purpose: purpose
                })
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal mencetak tiket walk-in (Status: ${response.status}).`;
                createToast('Gagal Cetak', errMsg, 'warning');
                return;
            }

            const data = await response.json();
            const ticketNum = data.queue_number || data.ticket_code || 'W-000';
            const citizenName = data.visitor_name || data.name || 'Walk-In Citizen';

            const deptSelect = document.getElementById('selWalkInDept');
            const deptName = deptSelect.options[deptSelect.selectedIndex].text;

            foStats.tiketDicetak++;
            document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

            // Add to live feed with state parameter 'Waiting'
            addLiveFeedRow(citizenName, ticketNum, deptName, 'Walk-In (Tiket Mandiri)', 'Waiting');

            createToast('Tiket Dicetak',
                `Tiket ${ticketNum} berhasil dicetak untuk ${citizenName} tujuan ${deptName}.`, 'success');
            resetWalkInForm();
        } catch (error) {
            console.error('Error printing walkin ticket:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk mencetak tiket.', 'warning');
        }
    }

    // Helper functions
    function addLiveFeedRow(name, code, tenant, type, status) {
        const tbody = document.getElementById('foLiveFeedBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors';

        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        tr.innerHTML = `
            <td class="py-3 px-6 font-bold text-ink dark:text-white">${name}</td>
            <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">${code}</td>
            <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">${tenant}</td>
            <td class="py-3 px-4 text-muted dark:text-on-dark-soft">${type}</td>
            <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">${timeStr}</td>
            <td class="py-3 px-6">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold border border-green-200/50">
                    <span class="w-1 h-1 rounded-full bg-green-500"></span>${status}
                </span>
            </td>
        `;

        // Insert at top of table
        if (tbody.firstChild) {
            tbody.insertBefore(tr, tbody.firstChild);
        } else {
            tbody.appendChild(tr);
        }

        // Limit to 8 rows
        while (tbody.children.length > 8) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    // Toast Alert
    function createToast(title, message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        let borderClr = 'border-green-500';
        let bgClr = 'bg-white dark:bg-gray-800';
        let iconHtml = '';

        if (type === 'success') {
            borderClr = 'border-l-4 border-green-500';
            iconHtml =
                `<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            borderClr = 'border-l-4 border-amber-500';
            iconHtml =
                `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            borderClr = 'border-l-4 border-blue-500';
            iconHtml =
                `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        toast.className =
            `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
        toast.innerHTML = `
            <div class="shrink-0">${iconHtml}</div>
            <div class="flex-grow">
                <h5 class="text-xs font-bold text-ink dark:text-white font-display">${title}</h5>
                <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;

        container.appendChild(toast);

        // Trigger reflow & animate in
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }
        }, 50);

        // Auto remove after 4s
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => {
                    if (toast.isConnected) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 4000);
    }

    // Keep track of shown notification IDs to prevent duplicates in async race conditions
    const shownNotifications = new Set();

    async function pollNotifications() {
        try {
            const response = await fetch('/api/fo/notifications');
            if (!response.ok) return;
            const data = await response.json();
            
            if (data.notifications && data.notifications.length > 0) {
                for (const notification of data.notifications) {
                    if (shownNotifications.has(notification.id)) continue;
                    shownNotifications.add(notification.id);
                    
                    // Show dynamic toast pop-up
                    createToast(notification.title, notification.message, 'info');
                    
                    // Increment stats counter
                    foStats.antreanFO++;
                    const foStatElem = document.getElementById('foStatAntrean');
                    if (foStatElem) {
                        foStatElem.innerText = foStats.antreanFO;
                    }
                    
                    // Mark as read in background
                    fetch(`/api/fo/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).catch(err => console.error('Failed to mark notification as read:', err));
                }
            }
            
            // Update the header dot
            const dot = document.getElementById('header-notification-dot');
            if (dot) {
                if (data.unread_count > 0) {
                    dot.classList.remove('hidden');
                } else {
                    dot.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error polling notifications:', error);
        }
    }

    // Start polling immediately and then every 5 seconds
    pollNotifications();
    setInterval(pollNotifications, 5000);
</script>
