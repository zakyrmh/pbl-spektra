{{-- Visitor Dashboard --}}
<div class="space-y-6 pb-16">
    <!-- Greeting & Account Info Banner -->
    <div class="bg-linear-to-r from-primary to-accent-teal text-white rounded-xl p-6 shadow-xl relative overflow-hidden transition-all duration-300 hover:shadow-primary/10">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-white/90 text-xs font-semibold uppercase tracking-wider mb-1">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                    Live Dashboard
                </div>
                <h2 class="text-2xl md:text-3xl font-display font-bold tracking-tight">Halo, {{ Auth::user()->name ?? 'Pengunjung' }}!</h2>
                <p class="text-sm text-white/80 mt-1 font-body" id="live-date-display">Mengambil data waktu...</p>
            </div>
            
            @if(empty(Auth::user()->nik))
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 max-w-md">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-accent-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="font-bold text-sm text-white font-display">Profil Belum Lengkap</h4>
                        <p class="text-xs text-white/90 mt-0.5 font-body">NIK Anda belum terverifikasi. Lengkapi NIK Anda pada menu profil untuk mempermudah pendaftaran dan pencetakan tiket di MPP.</p>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-gold hover:text-accent-gold/90 mt-2 transition-colors focus-visible:outline-none focus-visible:underline">
                            Lengkapi Sekarang
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- Background decorations -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-accent-teal/15 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left/Main: Active Ticket & Live Tracking (Spans 2 cols on Large Screens) -->
        <div class="lg:col-span-2 space-y-6">
            
            @if($activeBooking)
            <!-- Ticket Hero Card -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden relative">
                <!-- Top Ticket Header Pattern -->
                <div class="bg-linear-to-r from-primary to-primary-hover px-6 py-4 text-white flex justify-between items-center">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider font-display">
                        <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-sm"></span>
                        Tiket Antrean Aktif Hari Ini
                    </span>
                    <span class="text-xs bg-white/20 px-2.5 py-1 rounded-pill font-medium">Buka s.d 15:00</span>
                </div>

                <!-- Main Ticket Body -->
                <div class="p-6 md:p-8 space-y-8">
                    <!-- Tenant & Service Title -->
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-muted dark:text-on-dark-soft text-xs font-semibold uppercase tracking-wider font-display">Instansi & Layanan</h3>
                            <h4 class="text-xl md:text-2xl font-bold text-ink dark:text-white mt-1 font-display">{{ $activeBooking->department->name }}</h4>
                            <p class="text-sm font-semibold text-primary dark:text-accent-teal mt-0.5 font-body">{{ $activeBooking->purpose }}</p>
                        </div>
                        <div class="bg-surface-soft dark:bg-white/5 text-primary dark:text-accent-teal p-3 rounded-lg shrink-0 border border-hairline dark:border-white/5">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Live Ticket Queue Counter -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 bg-surface-soft dark:bg-white/5 rounded-lg p-6 border border-hairline dark:border-white/5">
                        <div class="col-span-2 sm:col-span-1 border-b sm:border-b-0 sm:border-r border-hairline dark:border-white/10 pb-4 sm:pb-0">
                            <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Nomor Antrean Anda</p>
                            <p class="text-3xl md:text-4xl font-extrabold text-primary dark:text-accent-teal mt-1 tracking-tight font-mono">
                                {{ $activeBooking->queue_number ?: 'Belum Check-In' }}
                            </p>
                        </div>
                        <div class="sm:border-r border-hairline dark:border-white/10 sm:pl-4">
                            <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Panggilan Sekarang</p>
                            <p id="live-current-queue" class="text-3xl md:text-4xl font-extrabold text-ink dark:text-white mt-1 tracking-tight font-mono">
                                {{ $activeBooking->queue_number ? $currentServingQueue : '-' }}
                            </p>
                        </div>
                        <div class="sm:pl-4 flex flex-col justify-between">
                            @if($activeBooking->queue_number)
                            <div>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Sisa Antrean</p>
                                <p id="live-remaining-queues" class="text-3xl md:text-4xl font-extrabold text-status-waiting mt-1 tracking-tight font-mono animate-pulse">
                                    {{ $remainingQueuesCount }} Orang
                                </p>
                            </div>
                            <p id="live-waiting-time" class="text-[11px] font-semibold text-status-waiting mt-1 font-body transition-all">Estimasi: ±{{ $estimatedTime }} Menit</p>
                            @else
                            <div>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Status Booking</p>
                                <p class="text-md font-extrabold text-status-waiting mt-2 font-display">Menunggu Check-In</p>
                            </div>
                            <p class="text-[11px] font-semibold text-muted mt-1 font-body">Silakan verifikasi di loket FO</p>
                            @endif
                        </div>
                    </div>

                    <!-- Stepper Flow -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Status Perjalanan Antrean</h4>
                        
                        @php
                            $step = 1;
                            if ($activeBooking->queue_number) {
                                $step = 2; // Check-In FO
                                $status = $activeBooking->status->value ?? $activeBooking->status;
                                if ($status === 'Serving' || in_array($status, ['Completed', 'Skipped'])) {
                                    $step = 4; // Dilayani / Selesai
                                }
                            }
                            $width = match($step) {
                                1 => '12.5%',
                                2 => '37.5%',
                                3 => '62.5%',
                                4 => '87.5%',
                            };
                        @endphp
                        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-2">
                            <!-- Stepper Line Connector (Desktop Only) -->
                            <div class="hidden md:block absolute top-[18px] left-[5%] right-[5%] h-1 bg-surface-strong dark:bg-gray-700 z-0">
                                <div id="stepper-progress-line" class="h-full bg-primary dark:bg-accent-teal transition-all duration-500" style="width: {{ $width }};"></div>
                            </div>

                            <!-- Step 1: Terbooking -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Terbooking</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Dari Rumah</p>
                                </div>
                            </div>

                            <!-- Step 2: Terkonfirmasi FO -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 2 ? 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500' : 'bg-surface-strong dark:bg-gray-700 text-muted border-2 border-hairline dark:border-gray-600' }}">
                                    @if($step >= 2)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        2
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Check-In FO</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Terkonfirmasi</p>
                                </div>
                            </div>

                            <!-- Step 3: Menunggu Panggilan -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full">
                                <div id="step-3-badge" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 3 ? 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500' : ($step == 2 ? 'bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal border-2 border-primary animate-pulse' : 'bg-surface-strong dark:bg-gray-700 text-muted border-2 border-hairline dark:border-gray-600') }}">
                                    @if($step >= 3)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        3
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Menunggu</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Panggilan Gerai</p>
                                </div>
                            </div>

                            <!-- Step 4: Sedang Dilayani -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full">
                                <div id="step-4-badge" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 4 ? 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500' : ($step == 3 ? 'bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal border-2 border-primary animate-pulse' : 'bg-surface-strong dark:bg-gray-700 text-muted border-2 border-hairline dark:border-gray-600') }}">
                                    @if($step >= 4)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        4
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Dilayani</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Di Gerai Instansi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code & Actions Area -->
                    <div class="pt-2 space-y-3">
                        <button type="button" onclick="openQrModal()" class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            Tampilkan QR Code / Kode Unik FO
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Empty State Card -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm p-8 text-center space-y-6">
                <div class="w-16 h-16 bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h4 class="text-xl font-bold text-ink dark:text-white font-display">Tidak Ada Antrean Aktif</h4>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body">Anda belum memiliki tiket atau reservasi antrean aktif untuk hari ini. Silakan buat booking pelayanan baru terlebih dahulu.</p>
                </div>
                <div>
                    <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Bookingan Sekarang
                    </a>
                </div>
            </div>
            @endif

            <!-- Quick Actions Grid -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-ink dark:text-white font-display">Pintasan Aksi Utama</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Riwayat -->
                    <a href="{{ route('booking.index') }}" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all border border-hairline dark:border-white/5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-ink dark:text-white font-display">Riwayat Layanan</h4>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5 font-body">Semua data antrean</p>
                    </a>
                    <!-- Panduan -->
                    <a href="{{ route('customer.guide') }}" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-accent-gold rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-accent-gold group-hover:text-ink transition-all border border-hairline dark:border-white/5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-ink dark:text-white font-display">Panduan MPP</h4>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5 font-body">Syarat & info layanan</p>
                    </a>
                    <!-- Pengaduan -->
                    <a href="#" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-status-skipped rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-status-skipped group-hover:text-white transition-all border border-hairline dark:border-white/5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-ink dark:text-white font-display">Pusat Bantuan</h4>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5 font-body">Pengaduan & chat</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: MPP Live Density & Active Tenants (Spans 1 col) -->
        <div class="space-y-6">
            <!-- Live Building Density -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-6 border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <h3 class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-4 flex items-center justify-between font-display">
                    Kepadatan Gedung MPP
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                </h3>

                <div class="flex items-center gap-4">
                    <!-- SVG Gauge Ring -->
                    <div class="relative w-20 h-20 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-surface-strong dark:text-gray-700" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <!-- Dynamic Progress circle -->
                            <path id="density-gauge-circle" class="{{ $densityClass }} transition-all duration-1000 ease-out" stroke-dasharray="{{ $densityPercentage }}, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-extrabold text-ink dark:text-white font-mono" id="density-percentage-text">{{ $densityPercentage }}%</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Status Kepadatan</div>
                        <div class="text-lg font-extrabold text-ink dark:text-white mt-0.5 flex items-center gap-2 font-display">
                            <span class="inline-block w-2.5 h-2.5 rounded-full {{ $densityDot }}" id="density-status-dot"></span>
                            <span id="density-status-text">{{ $densityStatus }}</span>
                        </div>
                        <div class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body" id="density-status-desc">{{ $densityDescription }}</div>
                    </div>
                </div>
            </div>

            <!-- Busiest Tenants List -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-6 border border-hairline dark:border-white/10 shadow-sm">
                <h3 class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-4 font-display">Tenant Teramai Hari Ini</h3>
                
                <div class="space-y-4">
                    @forelse($topDepartments as $index => $dept)
                    <div>
                        <div class="flex justify-between items-center text-xs font-bold mb-1 font-display">
                            <span class="text-ink dark:text-white">{{ $dept['name'] }}</span>
                            <span class="text-primary dark:text-accent-teal font-mono" id="tenant-{{ $index + 1 }}-counter">{{ $dept['queues_count'] }} Antrean</span>
                        </div>
                        <div class="w-full bg-surface-soft dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-1.5 rounded-full transition-all duration-1000" id="tenant-{{ $index + 1 }}-progress" style="width: {{ $dept['progress_percent'] }}%;"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-muted font-body">Belum ada antrean masuk hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@if($activeBooking)
<!-- Custom QR Code Modal Overlay -->
<div id="qr-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-4 opacity-0 transition-opacity duration-300 hidden">
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-6 md:p-8 max-w-sm w-full border border-hairline dark:border-white/10 shadow-2xl relative transform scale-95 transition-transform duration-300" id="qr-modal-card">
        <!-- Close Button -->
        <button onclick="closeQrModal()" class="absolute top-4 right-4 text-muted hover:text-ink dark:hover:text-white p-1 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Modal Body -->
        <div class="text-center space-y-6 pt-2">
            <div>
                <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display">Check-In Front Office</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Scan kode ini di meja FO MPP Sawahlunto</p>
            </div>

            <!-- Pixel-perfect Mock QR Code -->
            <div class="bg-white p-6 rounded-lg border border-hairline inline-block shadow-inner mx-auto relative group">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=192x192&data={{ urlencode($activeBooking->booking_code) }}" 
                     alt="QR Code Booking" 
                     class="w-48 h-48 mx-auto object-contain">
            </div>

            <div class="space-y-1.5" x-data="{ copied: false }">
                <div class="text-[10px] text-muted font-bold uppercase tracking-wider font-display">KODE BOOKING</div>
                <div class="text-2xl font-extrabold text-ink tracking-wider font-mono select-all">{{ $activeBooking->booking_code }}</div>
                
                <button type="button" 
                        @click="copyToClipboard('{{ $activeBooking->booking_code }}', () => { copied = true; showSuccessToast('Berhasil disalin'); setTimeout(() => copied = false, 2000); })"
                        class="inline-flex items-center gap-1 text-[11px] font-bold text-primary dark:text-accent-teal hover:underline focus:outline-none cursor-pointer mt-1 justify-center w-full">
                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    <span x-show="!copied">Salin Kode Booking</span>
                    <span x-show="copied" class="text-status-serving flex items-center justify-center gap-1 font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Tersalin!
                    </span>
                </button>
            </div>
                @if(!in_array($activeBooking->status->value ?? $activeBooking->status, ['Booked', 'Pending']))
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                    Telah Terkonfirmasi FO
                </div>
                @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-200/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Check-In FO
                </div>
                @endif
            </div>

            <p class="text-xs text-muted dark:text-on-dark-soft max-w-[240px] mx-auto leading-relaxed font-body">Dekatkan layar ponsel Anda ke alat pemindai kode di loket Front Office.</p>
        </div>
    </div>
</div>
@endif

<script>
    // Format date helper
    function formatIndonesianDate() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const d = new Date();
        return `${days[d.getDay()]} , ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    // Init state
    document.addEventListener('DOMContentLoaded', () => {
        const liveDate = document.getElementById('live-date-display');
        if (liveDate) {
            liveDate.innerText = formatIndonesianDate();
        }

        @if($activeBooking && in_array($activeBooking->status->value ?? $activeBooking->status, ['Pending', 'Booked']))
        const bookingId = "{{ $activeBooking->id }}";
        const checkInterval = setInterval(async () => {
            try {
                const response = await fetch(`/api/booking/${bookingId}/status`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.status !== 'Pending' && data.status !== 'Booked' && data.queue_number) {
                        clearInterval(checkInterval);
                        showSuccessToast('Pendaftaran Anda telah diverifikasi oleh Front Office!');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
            } catch (error) {
                console.error('Error polling booking status:', error);
            }
        }, 3000);
        @endif
    });

    // QR Modal Controls
    function openQrModal() {
        const modal = document.getElementById('qr-modal');
        const card = document.getElementById('qr-modal-card');
        if (modal && card) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 50);
        }
    }

    function closeQrModal() {
        const modal = document.getElementById('qr-modal');
        const card = document.getElementById('qr-modal-card');
        if (modal && card) {
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    }
</script>
