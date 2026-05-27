{{-- Super Admin Dashboard --}}
<div class="space-y-6 pb-12">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider font-display">Live Monitoring Active</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Pusat Kendali & Kinerja MPP</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Pemantauan real-time arus kunjungan, efisiensi loket, dan performa gerai instansi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button id="btnSimulationToggle" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-pill text-xs font-semibold border border-hairline dark:border-white/10 bg-canvas dark:bg-surface-dark-elevated hover:bg-surface-soft dark:hover:bg-white/5 text-ink dark:text-white transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                <svg class="w-4 h-4 text-accent-gold animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Pause Simulasi</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-pill text-xs font-semibold bg-primary hover:bg-primary-hover text-white transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Ekspor Laporan</span>
            </button>
        </div>
    </div>

    <!-- Top Cards Widget (Metrik Utama) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Kunjungan -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Kunjungan Hari Ini</p>
                    <h3 id="statTotalKunjungan" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">342</h3>
                </div>
                <div class="p-3 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10">
                <span class="text-green-600 dark:text-green-400 font-bold flex items-center gap-0.5 font-mono">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>+12%</span>
                </span>
                <span class="font-body">vs. harian</span>
            </div>
        </div>

        <!-- Card 2: Menunggu Konfirmasi FO -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Menunggu Konfirmasi FO</p>
                    <h3 id="statMenungguFO" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">18</h3>
                </div>
                <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg border border-status-waiting/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10 font-body">
                <span class="text-status-waiting font-bold flex items-center gap-0.5">
                    <span class="w-2 h-2 rounded-full bg-status-waiting animate-pulse"></span>
                    <span>Sedang</span>
                </span>
                <span>Antrean di loket depan</span>
            </div>
        </div>

        <!-- Card 3: Sedang Dilayani -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Sedang Dilayani di Gerai</p>
                    <h3 id="statSedangDilayani" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">24</h3>
                </div>
                <div class="p-3 bg-status-serving/10 text-status-serving rounded-lg border border-status-serving/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10 font-body">
                <span class="font-semibold text-ink dark:text-white">Aktif</span>
                <span>di loket gerai instansi</span>
            </div>
        </div>

        <!-- Card 4: Tenant Aktif -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Tenant Aktif</p>
                    <h3 id="statTenantAktif" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">12 <span class="text-lg font-medium text-muted">/ 15</span></h3>
                </div>
                <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg border border-purple-200/50">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10">
                <div class="w-full bg-surface-soft dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-purple-600 h-full rounded-full" style="width: 80%"></div>
                </div>
                <span class="shrink-0 text-[10px] font-bold text-purple-600 dark:text-purple-400">80% Buka</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Line Chart: Tren Kedatangan -->
        <div class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display">Tren Kedatangan Pengunjung</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Membandingkan arus masuk Booking Online vs. On-site per jam</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                        <span class="text-[10px] text-muted font-bold uppercase font-display">Online</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
                        <span class="text-[10px] text-muted font-bold uppercase font-display">On-site</span>
                    </div>
                </div>
            </div>
            <div id="chartTrenKedatangan" class="w-full h-80 min-h-[320px]"></div>
        </div>

        <!-- Bar Chart: Top Tenant -->
        <div class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Top Tenant Terpadat</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Instansi dengan volume antrean tertinggi hari ini</p>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <div id="chartTopTenant" class="w-full h-80 min-h-[320px]"></div>
            </div>
        </div>
    </div>

    <!-- Table & FO Widget Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Tabel Pemantauan Live Tenant -->
        <div class="lg:col-span-8 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display">Pemantauan Live Tenant</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Metrik real-time keaktifan loket dan beban antrean instansi</p>
                </div>
                <span class="bg-primary/10 text-primary dark:text-accent-teal text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse font-display">
                    Auto Refreshing
                </span>
            </div>

            <div class="overflow-x-auto -mx-6">
                <table id="tblLiveTenant" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                            <th class="py-3 px-6">Nama Instansi</th>
                            <th class="py-3 px-4">Loket</th>
                            <th class="py-3 px-4">Menunggu</th>
                            <th class="py-3 px-4">Rerata Pelayanan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-hairline dark:divide-white/5">
                        <!-- Dukcapil -->
                        <tr data-instansi="Dukcapil" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                <div class="flex items-center gap-3 font-display">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0 border border-hairline dark:border-white/10">
                                        DK
                                    </div>
                                    <span>Dukcapil</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">3 Loket</td>
                            <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                <span class="queue-count">24</span> <span class="text-xs font-normal text-muted">orang</span>
                            </td>
                            <td class="py-4 px-4 text-muted dark:text-on-dark-soft font-body">12 Menit / Orang</td>
                            <td class="py-4 px-4">
                                <span class="status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-rose-200/50 font-display">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    Padat
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button onclick="tegurTenant('Dukcapil')" class="btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50">
                                    Tegur
                                </button>
                            </td>
                        </tr>
                        <!-- Imigrasi -->
                        <tr data-instansi="Imigrasi" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                <div class="flex items-center gap-3 font-display">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0 border border-hairline dark:border-white/10">
                                        IM
                                    </div>
                                    <span>Imigrasi</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">2 Loket</td>
                            <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                <span class="queue-count">5</span> <span class="text-xs font-normal text-muted">orang</span>
                            </td>
                            <td class="py-4 px-4 text-muted dark:text-on-dark-soft font-body">20 Menit / Orang</td>
                            <td class="py-4 px-4">
                                <span class="status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-emerald-200/50 font-display">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Lancar
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button onclick="tegurTenant('Imigrasi')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-650 dark:text-gray-500 dark:hover:text-gray-450 rounded-lg text-xs font-bold transition-all border border-hairline dark:border-gray-700 cursor-not-allowed" disabled>
                                    Tegur
                                </button>
                            </td>
                        </tr>
                        <!-- Bapenda -->
                        <tr data-instansi="Bapenda" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                <div class="flex items-center gap-3 font-display">
                                    <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold text-xs shrink-0 border border-hairline dark:border-white/10">
                                        BP
                                    </div>
                                    <span>Bapenda</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">1 Loket</td>
                            <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                <span class="queue-count">0</span> <span class="text-xs font-normal text-muted">orang</span>
                            </td>
                            <td class="py-4 px-4 text-muted dark:text-on-dark-soft font-body">8 Menit / Orang</td>
                            <td class="py-4 px-4">
                                <span class="status-badge bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-hairline dark:border-white/5 font-display">
                                    <span class="w-1.5 h-1.5 rounded-full bg-muted"></span>
                                    Kosong
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button onclick="tegurTenant('Bapenda')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-655 dark:text-gray-500 dark:hover:text-gray-450 rounded-lg text-xs font-bold transition-all border border-hairline dark:border-gray-700 cursor-not-allowed" disabled>
                                    Tegur
                                </button>
                            </td>
                        </tr>
                        <!-- Samsat -->
                        <tr data-instansi="Samsat" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                <div class="flex items-center gap-3 font-display">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-400 flex items-center justify-center font-bold text-xs shrink-0 border border-hairline dark:border-white/10">
                                        SM
                                    </div>
                                    <span>Samsat</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">2 Loket</td>
                            <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                <span class="queue-count">12</span> <span class="text-xs font-normal text-muted">orang</span>
                            </td>
                            <td class="py-4 px-4 text-muted dark:text-on-dark-soft font-body">15 Menit / Orang</td>
                            <td class="py-4 px-4">
                                <span class="status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-rose-200/50 font-display">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    Padat
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button onclick="tegurTenant('Samsat')" class="btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50">
                                    Tegur
                                </button>
                            </td>
                        </tr>
                        <!-- BPJS Kesehatan -->
                        <tr data-instansi="BPJS Kesehatan" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                <div class="flex items-center gap-3 font-display">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 flex items-center justify-center font-bold text-xs shrink-0 border border-hairline dark:border-white/10">
                                        BP
                                    </div>
                                    <span>BPJS Kesehatan</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">1 Loket</td>
                            <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                <span class="queue-count">3</span> <span class="text-xs font-normal text-muted">orang</span>
                            </td>
                            <td class="py-4 px-4 text-muted dark:text-on-dark-soft font-body">10 Menit / Orang</td>
                            <td class="py-4 px-4">
                                <span class="status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-emerald-200/50 font-display">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Lancar
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button onclick="tegurTenant('BPJS Kesehatan')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-655 dark:text-gray-500 dark:hover:text-gray-455 rounded-lg text-xs font-bold transition-all border border-hairline dark:border-gray-700 cursor-not-allowed" disabled>
                                    Tegur
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Widget Booking Online (FO Efficiency) -->
        <div class="lg:col-span-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Alur Booking Online & FO</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Efisiensi petugas Front Office memproses verifikasi kode unik</p>
            </div>

            <!-- Gauge / Speed visual indicator -->
            <div class="py-6 flex flex-col items-center justify-center relative">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <!-- SVG Gauge Arc -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle -->
                        <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-surface-strong dark:text-gray-750" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-linecap="round" />
                        <!-- Progress Circle -->
                        <circle id="gaugeProgressArc" cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-emerald-500" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="140" stroke-linecap="round" />
                    </svg>
                    <!-- Inner Content -->
                    <div class="absolute flex flex-col items-center justify-center text-center">
                        <span id="valCheckInTime" class="text-3xl font-extrabold text-ink dark:text-white font-mono">2.4</span>
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Menit / Tiket</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <span id="badgeCheckInStatus" class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider font-display border border-emerald-200/50">
                        Efisien / Lancar
                    </span>
                    <p class="text-[11px] text-muted dark:text-on-dark-soft mt-2 max-w-[220px] mx-auto leading-relaxed font-body">
                        Target check-in FO: <span class="font-bold text-ink dark:text-white">&lt; 3.0 menit</span>. Saat ini tidak terjadi penumpukan (bottleneck).
                    </p>
                </div>
            </div>

            <!-- Live feed events (simulates Laravel Reverb) -->
            <div class="border-t border-hairline dark:border-white/10 pt-4 mt-auto">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">Live Activity Feed</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                </div>
                <div id="liveActivityFeed" class="space-y-2 max-h-[110px] overflow-y-auto pr-1 text-[11px] text-muted dark:text-on-dark-soft font-mono">
                    <div class="flex items-start gap-1">
                        <span class="text-[10px] text-muted">17:59</span>
                        <span class="text-ink dark:text-white font-semibold shrink-0">&bull; System:</span>
                        <span class="text-muted dark:text-on-dark-soft">WebSocket monitoring aktif.</span>
                    </div>
                    <div class="flex items-start gap-1">
                        <span class="text-[10px] text-muted">17:58</span>
                        <span class="text-primary dark:text-accent-teal font-semibold shrink-0">&bull; FO Admin:</span>
                        <span class="text-muted dark:text-on-dark-soft">Verifikasi tiket B-490 selesai.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apex Chart: Tren Kedatangan Pengunjung
        const chartTrenOptions = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#1B4FA8', '#29ABE2'], // primary, accent-teal
            dataLabels: { enabled: false },
            series: [{
                name: 'Booking Online',
                data: [45, 65, 80, 50, 40, 75, 95, 60]
            }, {
                name: 'On-site (Langsung)',
                data: [30, 40, 55, 60, 45, 50, 65, 40]
            }],
            xaxis: {
                categories: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: true } }
            },
            legend: { show: false },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            }
        };
        const chartTren = new ApexCharts(document.querySelector("#chartTrenKedatangan"), chartTrenOptions);
        chartTren.render();

        // Apex Chart: Top Tenant Terpadat
        const chartTopOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '55%',
                    distributed: true
                }
            },
            colors: ['#DC2626', '#D97706', '#1B4FA8', '#059669', '#29ABE2'], // skipped, waiting, primary, serving, teal
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff'],
                    fontWeight: 700,
                    fontSize: '11px'
                },
                formatter: function (val, opt) {
                    return Math.round(val / 4) + " Antrean";
                },
                offsetX: 8
            },
            series: [{
                name: 'Volume Antrean',
                data: [96, 48, 20, 12, 0] // Scaled: Dukcapil (24*4), Samsat (12*4), Imigrasi (5*4), BPJS (3*4), Bapenda (0)
            }],
            xaxis: {
                categories: ['Dukcapil', 'Samsat', 'Imigrasi', 'BPJS Kes.', 'Bapenda'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: false } }
            },
            legend: { show: false }
        };
        const chartTop = new ApexCharts(document.querySelector("#chartTopTenant"), chartTopOptions);
        chartTop.render();

        // Dark mode adaptability for charts
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            chartTren.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: isDark ? '#374151' : '#f1f5f9' }
            });
            chartTop.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: isDark ? '#374151' : '#f1f5f9' }
            });
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        // --- REAL TIME SIMULATION ENGINE ---
        let simulationInterval = null;
        let isSimulationRunning = true;

        // Elements
        const statTotalKunjungan = document.getElementById('statTotalKunjungan');
        const statMenungguFO = document.getElementById('statMenungguFO');
        const statSedangDilayani = document.getElementById('statSedangDilayani');
        const valCheckInTime = document.getElementById('valCheckInTime');
        const gaugeProgressArc = document.getElementById('gaugeProgressArc');
        const badgeCheckInStatus = document.getElementById('badgeCheckInStatus');
        const liveActivityFeed = document.getElementById('liveActivityFeed');
        const btnSimulationToggle = document.getElementById('btnSimulationToggle');

        // Current state values
        let stats = {
            totalKunjungan: 342,
            menungguFO: 18,
            sedangDilayani: 24,
            foCheckInTime: 2.4,
            queues: {
                'Dukcapil': 24,
                'Imigrasi': 5,
                'Bapenda': 0,
                'Samsat': 12,
                'BPJS Kesehatan': 3
            }
        };

        function addActivityFeed(user, action, timestamp = null) {
            const now = timestamp || new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const eventDiv = document.createElement('div');
            eventDiv.className = 'flex items-start gap-1 opacity-0 translate-y-1 transition-all duration-300';
            eventDiv.innerHTML = `
                <span class="text-[10px] text-muted">${hours}:${minutes}</span>
                <span class="text-primary dark:text-accent-teal font-semibold shrink-0">&bull; ${user}:</span>
                <span class="text-ink dark:text-white">${action}</span>
            `;
            
            liveActivityFeed.prepend(eventDiv);
            setTimeout(() => {
                eventDiv.classList.remove('opacity-0', 'translate-y-1');
            }, 50);

            // Limit feed list to 6 items
            while (liveActivityFeed.children.length > 6) {
                liveActivityFeed.removeChild(liveActivityFeed.lastChild);
            }
        }

        function createToast(title, message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border shadow-lg border-hairline dark:border-white/10 flex gap-3 pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-300';
            
            let iconColor = 'text-primary dark:text-accent-teal';
            let iconSvg = `
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            
            if (type === 'success') {
                iconColor = 'text-green-500';
                iconSvg = `
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;
            } else if (type === 'warning') {
                iconColor = 'text-status-waiting';
                iconSvg = `
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                `;
            }

            toast.innerHTML = `
                <div class="shrink-0 ${iconColor}">
                    ${iconSvg}
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-ink dark:text-white font-display">${title}</p>
                    <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-muted hover:text-ink dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            container.appendChild(toast);
            
            // Animation in
            setTimeout(() => {
                toast.classList.remove('translate-x-12', 'opacity-0');
            }, 50);

            // Animation out
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        window.tegurTenant = function(tenantName) {
            createToast(
                `Nudge Dikirim ke ${tenantName}`,
                `Peringatan kepadatan antrean telah diteruskan ke Admin Gerai.`,
                'warning'
            );
            addActivityFeed('Super Admin', `Mengirim teguran antrean padat ke gerai ${tenantName}`);
            
            // Highlight the table row
            const row = document.querySelector(`tr[data-instansi="${tenantName}"]`);
            if (row) {
                row.classList.add('bg-rose-50/50', 'dark:bg-rose-950/20', 'animate-pulse');
                setTimeout(() => {
                    row.classList.remove('bg-rose-50/50', 'dark:bg-rose-950/20', 'animate-pulse');
                }, 2000);
            }
        };

        // Update FO Gauge Progress Arc
        function updateFOGauge(val) {
            valCheckInTime.innerText = val.toFixed(1);
            
            const percent = Math.min(Math.max((val - 0.5) / 5.5, 0), 1);
            const offset = 251.2 - (188.4 * percent);
            
            gaugeProgressArc.setAttribute('stroke-dashoffset', offset);

            if (val < 3.0) {
                gaugeProgressArc.setAttribute('class', 'text-emerald-500');
                badgeCheckInStatus.setAttribute('class', 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-200/50');
                badgeCheckInStatus.innerText = "Efisien / Lancar";
            } else if (val < 5.0) {
                gaugeProgressArc.setAttribute('class', 'text-status-waiting');
                badgeCheckInStatus.setAttribute('class', 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-amber-200/50');
                badgeCheckInStatus.innerText = "Menumpuk (Sedang)";
            } else {
                gaugeProgressArc.setAttribute('class', 'text-status-skipped');
                badgeCheckInStatus.setAttribute('class', 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-rose-200/50');
                badgeCheckInStatus.innerText = "BOTTLENECK!";
            }
        }

        function runSimulationStep() {
            const rand = Math.random();
            
            if (rand < 0.25) {
                // New Online Booking confirmed at FO
                stats.totalKunjungan += 1;
                if (stats.menungguFO > 0) stats.menungguFO -= 1;
                stats.sedangDilayani += 1;
                
                const tenants = ['Dukcapil', 'Samsat', 'BPJS Kesehatan', 'Imigrasi'];
                const selected = tenants[Math.floor(Math.random() * tenants.length)];
                stats.queues[selected] += 1;

                statTotalKunjungan.innerText = stats.totalKunjungan;
                statMenungguFO.innerText = stats.menungguFO;
                statSedangDilayani.innerText = stats.sedangDilayani;
                
                updateTableRow(selected);

                const codes = ['A', 'B', 'C', 'D'];
                const randCode = codes[Math.floor(Math.random() * codes.length)] + '-' + Math.floor(Math.random() * 900 + 100);
                addActivityFeed('Front Office', `Tiket ${randCode} telah dikonfirmasi untuk Gerai ${selected}`);
                createToast('Check-in Berhasil', `Tiket ${randCode} dikonfirmasi untuk gerai ${selected}.`, 'success');
            } 
            else if (rand < 0.50) {
                // Someone finished service at a counter
                if (stats.sedangDilayani > 0) stats.sedangDilayani -= 1;
                
                const activeTenants = Object.keys(stats.queues).filter(t => stats.queues[t] > 0);
                if (activeTenants.length > 0) {
                    const selected = activeTenants[Math.floor(Math.random() * activeTenants.length)];
                    stats.queues[selected] -= 1;
                    updateTableRow(selected);
                    
                    addActivityFeed('Gerai ' + selected, `Pengunjung selesai dilayani.`);
                }
                
                statSedangDilayani.innerText = stats.sedangDilayani;
            } 
            else if (rand < 0.70) {
                // New Booking Code created from home (increases FO queue)
                stats.menungguFO += 1;
                statMenungguFO.innerText = stats.menungguFO;
                
                addActivityFeed('Warga (Online)', `Melakukan reservasi online baru.`);
            }

            const speedChange = (Math.random() - 0.5) * 0.4;
            stats.foCheckInTime = Math.min(Math.max(stats.foCheckInTime + speedChange, 1.2), 5.8);
            updateFOGauge(stats.foCheckInTime);

            if (stats.foCheckInTime > 5.0 && rand < 0.1) {
                createToast(
                    'Peringatan Bottleneck FO!',
                    `Waktu antrean verifikasi loket depan melebihi 5 menit.`,
                    'warning'
                );
            }

            if (Math.random() < 0.20) {
                const currentDataOnline = [...chartTren.w.config.series[0].data];
                const currentDataOnsite = [...chartTren.w.config.series[1].data];
                
                currentDataOnline.shift();
                currentDataOnline.push(Math.floor(Math.random() * 40) + 50);
                currentDataOnsite.shift();
                currentDataOnsite.push(Math.floor(Math.random() * 30) + 40);

                chartTren.updateSeries([
                    { name: 'Booking Online', data: currentDataOnline },
                    { name: 'On-site (Langsung)', data: currentDataOnsite }
                ]);
            }
        }

        function updateTableRow(tenantName) {
            const row = document.querySelector(`tr[data-instansi="${tenantName}"]`);
            if (!row) return;

            const qCountEl = row.querySelector('.queue-count');
            const statusEl = row.querySelector('.status-badge');
            const btnTegur = row.querySelector('.btn-tegur');
            const count = stats.queues[tenantName];

            qCountEl.innerText = count;

            if (count >= 15) {
                statusEl.setAttribute('class', 'status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-rose-200/50');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>Padat`;
                btnTegur.removeAttribute('disabled');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50');
            } else if (count >= 4) {
                statusEl.setAttribute('class', 'status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-emerald-200/50');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lancar`;
                btnTegur.setAttribute('disabled', 'true');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-655 dark:text-gray-500 dark:hover:text-gray-450 rounded-lg text-xs font-bold transition-all border border-hairline dark:border-gray-700 cursor-not-allowed');
            } else {
                statusEl.setAttribute('class', 'status-badge bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-hairline dark:border-white/5');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-muted"></span>Kosong`;
                btnTegur.setAttribute('disabled', 'true');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-655 dark:text-gray-500 dark:hover:text-gray-455 rounded-lg text-xs font-bold transition-all border border-hairline dark:border-gray-700 cursor-not-allowed');
            }

            const order = ['Dukcapil', 'Samsat', 'Imigrasi', 'BPJS Kesehatan', 'Bapenda'];
            const barData = order.map(t => stats.queues[t] * 4);
            chartTop.updateSeries([{ name: 'Volume Antrean', data: barData }]);
        }

        function startSimulation() {
            simulationInterval = setInterval(runSimulationStep, 4000);
        }

        function stopSimulation() {
            clearInterval(simulationInterval);
        }

        btnSimulationToggle.addEventListener('click', function() {
            if (isSimulationRunning) {
                stopSimulation();
                btnSimulationToggle.innerHTML = `
                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Mulai Simulasi</span>
                `;
                isSimulationRunning = false;
                createToast('Simulasi Dihentikan', 'Arus pembaruan data real-time dihentikan sementara.', 'info');
            } else {
                startSimulation();
                btnSimulationToggle.innerHTML = `
                    <svg class="w-4 h-4 text-accent-gold animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pause Simulasi</span>
                `;
                isSimulationRunning = true;
                createToast('Simulasi Berjalan', 'Mulai memantau event dan arus kunjungan secara live.', 'success');
            }
        });

        // Initialize
        updateFOGauge(stats.foCheckInTime);
        startSimulation();
    });
</script>
@endpush
