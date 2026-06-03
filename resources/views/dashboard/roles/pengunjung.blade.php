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
                            <h4 class="text-xl md:text-2xl font-bold text-ink dark:text-white mt-1 font-display">Dinas Kependudukan & Pencatatan Sipil</h4>
                            <p class="text-sm font-semibold text-primary dark:text-accent-teal mt-0.5 font-body">Cetak Kartu Tanda Penduduk Elektronik (KTP-el)</p>
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
                            <p class="text-4xl md:text-5xl font-extrabold text-primary dark:text-accent-teal mt-1 tracking-tight font-mono">A-015</p>
                        </div>
                        <div class="sm:border-r border-hairline dark:border-white/10 sm:pl-4">
                            <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Panggilan Sekarang</p>
                            <p class="text-4xl md:text-5xl font-extrabold text-ink dark:text-white mt-1 tracking-tight font-mono" id="live-current-queue">A-010</p>
                        </div>
                        <div class="sm:pl-4 flex flex-col justify-between">
                            <div>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-semibold uppercase tracking-wider font-display">Sisa Antrean</p>
                                <p class="text-4xl md:text-5xl font-extrabold text-status-waiting mt-1 tracking-tight font-mono animate-pulse" id="live-remaining-queues">5 Orang</p>
                            </div>
                            <p class="text-[11px] font-semibold text-status-waiting mt-1 font-body transition-all" id="live-waiting-time">Estimasi: ±15 Menit</p>
                        </div>
                    </div>

                    <!-- Stepper Flow -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Status Perjalanan Antrean</h4>
                        
                        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-2">
                            <!-- Stepper Line Connector (Desktop Only) -->
                            <div class="hidden md:block absolute top-[18px] left-[5%] right-[5%] h-1 bg-surface-strong dark:bg-gray-700 z-0">
                                <div class="h-full bg-primary dark:bg-accent-teal transition-all duration-500" id="stepper-progress-line" style="width: 33.33%;"></div>
                            </div>

                            <!-- Step 1: Terbooking -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-1-el">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Terbooking</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Dari Rumah</p>
                                </div>
                            </div>

                            <!-- Step 2: Terkonfirmasi FO -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-2-el">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300" id="step-2-badge">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-ink dark:text-white leading-tight font-display">Check-In FO</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Terkonfirmasi</p>
                                </div>
                            </div>

                            <!-- Step 3: Menunggu Panggilan -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-3-el">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal border-2 border-primary animate-pulse transition-all duration-300" id="step-3-badge">
                                    3
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-primary dark:text-accent-teal leading-tight font-display">Menunggu</p>
                                    <p class="text-[10px] text-muted dark:text-on-dark-soft font-medium font-body">Panggilan Gerai</p>
                                </div>
                            </div>

                            <!-- Step 4: Sedang Dilayani -->
                            <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-4-el">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-surface-strong dark:bg-gray-700 text-muted border-2 border-hairline dark:border-gray-600 transition-all duration-300" id="step-4-badge">
                                    4
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-muted dark:text-gray-500 leading-tight font-display">Dilayani</p>
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
                        <div class="flex items-center justify-between gap-3">
                            <button type="button" onclick="rescheduleQueue()" class="flex-1 h-11 flex items-center justify-center gap-1.5 px-4 bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Ubah Jadwal
                            </button>
                            <button type="button" onclick="cancelQueue()" class="flex-1 h-11 flex items-center justify-center gap-1.5 px-4 text-status-skipped hover:underline text-xs font-semibold transition-all rounded-pill focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batalkan Antrean
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-ink dark:text-white font-display">Pintasan Aksi Utama</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Booking Baru -->
                    <a href="#" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-primary dark:text-accent-teal rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all border border-hairline dark:border-white/5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-ink dark:text-white font-display">Ambil Antrean</h4>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5 font-body">Booking layanan baru</p>
                    </a>
                    <!-- Riwayat -->
                    <a href="#" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all border border-hairline dark:border-white/5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-ink dark:text-white font-display">Riwayat Layanan</h4>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5 font-body">Semua data antrean</p>
                    </a>
                    <!-- Panduan -->
                    <a href="#" class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
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
                            <path id="density-gauge-circle" class="text-primary dark:text-accent-teal transition-all duration-1000 ease-out" stroke-dasharray="45, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-extrabold text-ink dark:text-white font-mono" id="density-percentage-text">45%</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Status Kepadatan</div>
                        <div class="text-lg font-extrabold text-ink dark:text-white mt-0.5 flex items-center gap-2 font-display">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-primary" id="density-status-dot"></span>
                            <span id="density-status-text">Normal</span>
                        </div>
                        <div class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body" id="density-status-desc">Kondisi kondusif, waktu antrean singkat.</div>
                    </div>
                </div>
            </div>

            <!-- Busiest Tenants List -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-6 border border-hairline dark:border-white/10 shadow-sm">
                <h3 class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-4 font-display">Tenant Teramai Hari Ini</h3>
                
                <div class="space-y-4">
                    <!-- Tenant 1: Disdukcapil -->
                    <div>
                        <div class="flex justify-between items-center text-xs font-bold mb-1 font-display">
                            <span class="text-ink dark:text-white">Disdukcapil</span>
                            <span class="text-primary dark:text-accent-teal font-mono" id="tenant-1-counter">18 Antrean</span>
                        </div>
                        <div class="w-full bg-surface-soft dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-1.5 rounded-full transition-all duration-1000" id="tenant-1-progress" style="width: 78%;"></div>
                        </div>
                    </div>
                    <!-- Tenant 2: Imigrasi -->
                    <div>
                        <div class="flex justify-between items-center text-xs font-bold mb-1 font-display">
                            <span class="text-ink dark:text-white">Imigrasi</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-mono" id="tenant-2-counter">12 Antrean</span>
                        </div>
                        <div class="w-full bg-surface-soft dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000" id="tenant-2-progress" style="width: 52%;"></div>
                        </div>
                    </div>
                    <!-- Tenant 3: Samsat -->
                    <div>
                        <div class="flex justify-between items-center text-xs font-bold mb-1 font-display">
                            <span class="text-ink dark:text-white">Samsat</span>
                            <span class="text-status-waiting font-mono" id="tenant-3-counter">6 Antrean</span>
                        </div>
                        <div class="w-full bg-surface-soft dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-status-waiting h-1.5 rounded-full transition-all duration-1000" id="tenant-3-progress" style="width: 32%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Simulation Controller (Interactive Sandbox) -->
    <div class="bg-surface-dark text-slate-100 rounded-lg p-6 border border-white/5 shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 bottom-0 translate-x-8 translate-y-8 opacity-10 pointer-events-none">
            <svg class="w-48 h-48 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
        </div>
        <div class="relative z-10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-accent-teal rounded-full animate-pulse"></span>
                    <h4 class="font-extrabold text-sm tracking-wide uppercase font-display">Sandbox Simulasi Real-time</h4>
                </div>
                <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded-md text-on-dark-soft font-bold uppercase font-display">Demo Mode</span>
            </div>
            
            <p class="text-xs text-on-dark-soft leading-relaxed max-w-2xl font-body">Sandbox client-side ini mensimulasikan data live antrean MPP. Anda dapat membiarkannya berjalan otomatis (polling client-side) atau mengontrolnya secara manual untuk melihat transisi visual, kemajuan stepper, status kepadatan, dan alarm panggilan.</p>
            
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="button" onclick="advanceSimQueue()" class="h-11 flex items-center gap-1.5 px-4 bg-primary hover:bg-primary-hover active:scale-95 text-white font-semibold rounded-pill text-xs transition-all shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Panggil Antrean (+1)
                </button>
                <button type="button" onclick="toggleSimAuto()" id="btn-auto-sim" class="h-11 flex items-center gap-1.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-semibold rounded-pill text-xs transition-all shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <span class="w-2 h-2 bg-white rounded-full animate-ping" id="auto-ping"></span>
                    <span>Auto-Play: ON (5s)</span>
                </button>
                <button type="button" onclick="resetSim()" class="h-11 flex items-center gap-1.5 px-4 bg-white/5 hover:bg-white/10 active:scale-95 text-white font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer border border-white/10">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89" /></svg>
                    Reset
                </button>
                <div class="ml-auto text-[10px] text-on-dark-soft font-mono" id="sim-status-log">Status: Menunggu Antrean...</div>
            </div>
        </div>
    </div>
</div>

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
                <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="currentColor">
                    <!-- Positioning Squares -->
                    <rect x="0" y="0" width="25" height="25" fill="#1e293b" />
                    <rect x="3" y="3" width="19" height="19" fill="#ffffff" />
                    <rect x="6" y="6" width="13" height="13" fill="#1B4FA8" />

                    <rect x="75" y="0" width="25" height="25" fill="#1e293b" />
                    <rect x="78" y="3" width="19" height="19" fill="#ffffff" />
                    <rect x="81" y="6" width="13" height="13" fill="#1B4FA8" />

                    <rect x="0" y="75" width="25" height="25" fill="#1e293b" />
                    <rect x="3" y="78" width="19" height="19" fill="#ffffff" />
                    <rect x="6" y="81" width="13" height="13" fill="#1B4FA8" />

                    <!-- Small alignment squares -->
                    <rect x="70" y="70" width="10" height="10" fill="#1e293b" />
                    <rect x="72" y="72" width="6" height="6" fill="#ffffff" />
                    <rect x="74" y="74" width="2" height="2" fill="#1B4FA8" />

                    <!-- Randomly scattered blocks mimicking QR patterns -->
                    <rect x="30" y="2" width="10" height="4" fill="#1e293b" />
                    <rect x="45" y="5" width="8" height="5" fill="#1e293b" />
                    <rect x="60" y="3" width="5" height="15" fill="#1B4FA8" />
                    <rect x="35" y="12" width="12" height="6" fill="#1e293b" />
                    
                    <rect x="2" y="30" width="15" height="5" fill="#1e293b" />
                    <rect x="25" y="28" width="6" height="12" fill="#1B4FA8" />
                    <rect x="38" y="32" width="20" height="8" fill="#1e293b" />
                    <rect x="65" y="25" width="8" height="12" fill="#1e293b" />
                    
                    <rect x="5" y="50" width="12" height="6" fill="#1B4FA8" />
                    <rect x="25" y="48" width="15" height="10" fill="#1e293b" />
                    <rect x="48" y="45" width="25" height="5" fill="#1e293b" />
                    <rect x="80" y="35" width="12" height="15" fill="#1B4FA8" />
                    
                    <rect x="35" y="65" width="15" height="15" fill="#1e293b" />
                    <rect x="55" y="60" width="10" height="10" fill="#1B4FA8" />
                    <rect x="68" y="55" width="8" height="8" fill="#1e293b" />
                    
                    <rect x="30" y="85" width="25" height="6" fill="#1B4FA8" />
                    <rect x="60" y="82" width="6" height="12" fill="#1e293b" />
                    
                    <!-- Custom logo in the middle -->
                    <rect x="40" y="40" width="20" height="20" fill="#ffffff" rx="2" />
                    <circle cx="50" cy="50" r="8" fill="#1B4FA8" />
                    <circle cx="50" cy="50" r="5" fill="#ffffff" />
                </svg>
                
                <!-- Pulse Ring around QR -->
                <div class="absolute inset-0 border-4 border-blue-500/0 rounded-lg group-hover:border-blue-500/20 transition-all duration-700 animate-pulse pointer-events-none"></div>
            </div>

            <div class="space-y-1.5">
                <div class="text-[10px] text-muted font-bold uppercase tracking-wider font-display">KODE BOOKING</div>
                <div class="text-3xl font-extrabold text-ink tracking-wider font-mono">A-015</div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                    Telah Terkonfirmasi FO
                </div>
            </div>

            <p class="text-xs text-muted dark:text-on-dark-soft max-w-[240px] mx-auto leading-relaxed font-body">Dekatkan layar ponsel Anda ke alat pemindai kode di loket Front Office.</p>
        </div>
    </div>
</div>

<!-- Custom Notification Audio Alert -->
<audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav" preload="auto"></audio>

<!-- Simulation Javascript Logic -->
<script>
    // State variables
    let simState = {
        userQueue: 15, // A-015
        currentQueue: 10, // A-010
        totalRemaining: 5,
        autoPlay: true,
        buildingDensity: 45,
        intervalId: null
    };

    // Format date helper
    function formatIndonesianDate() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const d = new Date();
        return `${days[d.getDay()]} , ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    // Init state
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('live-date-display').innerText = formatIndonesianDate();
        
        // Start auto-play by default
        startSimTimer();
    });

    // Start Timer
    function startSimTimer() {
        if (simState.intervalId) clearInterval(simState.intervalId);
        simState.intervalId = setInterval(() => {
            advanceSimQueue();
        }, 5000);
    }

    // Stop Timer
    function stopSimTimer() {
        if (simState.intervalId) {
            clearInterval(simState.intervalId);
            simState.intervalId = null;
        }
    }

    // Toggle Timer
    function toggleSimAuto() {
        simState.autoPlay = !simState.autoPlay;
        const btn = document.getElementById('btn-auto-sim');
        const ping = document.getElementById('auto-ping');
        
        if (simState.autoPlay) {
            btn.classList.remove('bg-rose-600', 'hover:bg-rose-500');
            btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
            btn.querySelector('span:not(#auto-ping)').innerText = 'Auto-Play: ON (5s)';
            ping.classList.remove('hidden');
            startSimTimer();
        } else {
            btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
            btn.classList.add('bg-rose-600', 'hover:bg-rose-500');
            btn.querySelector('span:not(#auto-ping)').innerText = 'Auto-Play: OFF';
            ping.classList.add('hidden');
            stopSimTimer();
        }
    }

    // Reset Simulation
    function resetSim() {
        simState.currentQueue = 10;
        simState.totalRemaining = 5;
        simState.buildingDensity = 45;
        updateDOM();
        
        // Reset stepper visual elements
        const step3Badge = document.getElementById('step-3-badge');
        step3Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal border-2 border-primary animate-pulse transition-all duration-300";
        step3Badge.innerText = "3";
        
        const step4Badge = document.getElementById('step-4-badge');
        step4Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-surface-strong dark:bg-gray-700 text-muted border-2 border-hairline dark:border-gray-600 transition-all duration-300";
        step4Badge.innerText = "4";
        
        document.getElementById('stepper-progress-line').style.width = "33.33%";
        
        document.getElementById('sim-status-log').innerText = "Status: Reset ke A-010";
    }

    // Increment queue
    function advanceSimQueue() {
        if (simState.currentQueue < simState.userQueue) {
            simState.currentQueue++;
            simState.totalRemaining = simState.userQueue - simState.currentQueue;
            
            // Fluctuating building density
            simState.buildingDensity = Math.min(95, Math.max(20, simState.buildingDensity + Math.floor(Math.random() * 9) - 4));
            
            updateDOM();
            triggerAlertSound();
            
            document.getElementById('sim-status-log').innerText = `Status: Antrean dipanggil -> A-0${simState.currentQueue}`;
            
            // When it reaches user's queue
            if (simState.currentQueue === simState.userQueue) {
                // Change stepper state to "Sedang Dilayani"
                document.getElementById('stepper-progress-line').style.width = "100%";
                
                const step3Badge = document.getElementById('step-3-badge');
                step3Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300";
                step3Badge.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
                
                const step4Badge = document.getElementById('step-4-badge');
                step4Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal border-2 border-primary animate-pulse transition-all duration-300";
                step4Badge.innerText = "4";
                
                // Notify user via browser toast/alert
                setTimeout(() => {
                    alert("🔔 GILIRAN ANDA! Nomor antrean A-015 dipanggil ke Gerai Disdukcapil.");
                }, 500);
                
                document.getElementById('sim-status-log').innerText = "Status: Giliran Anda!";
                stopSimTimer();
            }
        } else {
            document.getElementById('sim-status-log').innerText = "Status: Mencapai batas antrean";
        }
    }

    // Update DOM elements
    function updateDOM() {
        if (simState.currentQueue === 0) return;
        
        // Update queue counters
        document.getElementById('live-current-queue').innerText = `A-0${simState.currentQueue}`;
        document.getElementById('live-remaining-queues').innerText = `${simState.totalRemaining} Orang`;
        
        // Update estimated waiting time
        const waitTime = simState.totalRemaining * 3;
        document.getElementById('live-waiting-time').innerText = `Estimasi: ±${waitTime} Menit`;
        
        // Update density gauge elements
        const percent = simState.buildingDensity;
        document.getElementById('density-percentage-text').innerText = `${percent}%`;
        
        const circle = document.getElementById('density-gauge-circle');
        circle.setAttribute('stroke-dasharray', `${percent}, 100`);
        
        const dot = document.getElementById('density-status-dot');
        const text = document.getElementById('density-status-text');
        const desc = document.getElementById('density-status-desc');
        
        if (percent < 40) {
            text.innerText = 'Sepi';
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-emerald-500';
            circle.className = 'text-emerald-500 transition-all duration-1000 ease-out';
            desc.innerText = 'Kondisi senggang, tidak ada antrean berarti.';
        } else if (percent < 70) {
            text.innerText = 'Normal';
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-primary';
            circle.className = 'text-primary dark:text-accent-teal transition-all duration-1000 ease-out';
            desc.innerText = 'Kondisi kondusif, waktu antrean singkat.';
        } else {
            text.innerText = 'Sangat Ramai';
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse';
            circle.className = 'text-rose-500 transition-all duration-1000 ease-out';
            desc.innerText = 'Gedung padat, waktu tunggu lebih lama.';
        }
        
        // Tenant fluctuations
        const t1 = Math.max(1, 18 + Math.floor(Math.random() * 5) - 2);
        document.getElementById('tenant-1-counter').innerText = `${t1} Antrean`;
        document.getElementById('tenant-1-progress').style.width = `${Math.min(100, Math.floor(t1 / 22 * 100))}%`;

        const t2 = Math.max(1, 12 + Math.floor(Math.random() * 3) - 1);
        document.getElementById('tenant-2-counter').innerText = `${t2} Antrean`;
        document.getElementById('tenant-2-progress').style.width = `${Math.min(100, Math.floor(t2 / 20 * 100))}%`;

        const t3 = Math.max(1, 6 + Math.floor(Math.random() * 3) - 1);
        document.getElementById('tenant-3-counter').innerText = `${t3} Antrean`;
        document.getElementById('tenant-3-progress').style.width = `${Math.min(100, Math.floor(t3 / 15 * 100))}%`;
    }

    // Play chime sound
    function triggerAlertSound() {
        const aud = document.getElementById('notif-sound');
        if (aud) {
            aud.currentTime = 0;
            aud.play().catch(e => console.log("Audio play prevented. Interaction needed."));
        }
    }

    // QR Modal Controls
    function openQrModal() {
        const modal = document.getElementById('qr-modal');
        const card = document.getElementById('qr-modal-card');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 50);
    }

    function closeQrModal() {
        const modal = document.getElementById('qr-modal');
        const card = document.getElementById('qr-modal-card');
        modal.classList.add('opacity-0');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // New action scripts
    function rescheduleQueue() {
        if (confirm("Apakah Anda yakin ingin mengubah jadwal antrean?")) {
            alert("Fitur Ubah Jadwal: Pendaftaran berhasil dipindahkan ke sesi waktu berikutnya.");
        }
    }
    
    function cancelQueue() {
        if (confirm("Apakah Anda yakin ingin membatalkan antrean ini?")) {
            simState.currentQueue = 0;
            simState.totalRemaining = 0;
            stopSimTimer();
            document.getElementById('live-current-queue').innerText = '-';
            document.getElementById('live-remaining-queues').innerText = 'Dibatalkan';
            document.getElementById('live-waiting-time').innerText = '-';
            alert("Antrean Anda telah berhasil dibatalkan.");
        }
    }
</script>
