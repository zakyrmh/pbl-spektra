@extends('layouts.private')

@section('title', 'Dashboard - MPP Kota Sawahlunto')

@section('content')
    @php
        $role = Auth::user()->role ?? 'pengunjung';
        if ($role === 'warga') $role = 'pengunjung';
    @endphp

    @if ($role === 'pengunjung')
        {{-- Visitor Dashboard --}}
        <div class="space-y-6 pb-16">
            <!-- Greeting & Account Info Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden transition-all duration-300 hover:shadow-indigo-500/10">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-blue-100 text-xs font-semibold uppercase tracking-wider mb-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                            Live Dashboard
                        </div>
                        <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name ?? 'Pengunjung' }}!</h2>
                        <p class="text-sm text-blue-100 mt-1" id="live-date-display">Mengambil data waktu...</p>
                    </div>
                    
                    @if(empty(Auth::user()->nik))
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 max-w-md">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="font-bold text-sm text-white">Profil Belum Lengkap</h4>
                                <p class="text-xs text-blue-100 mt-0.5">NIK Anda belum terverifikasi. Lengkapi NIK Anda pada menu profil untuk mempermudah pendaftaran dan pencetakan tiket di MPP.</p>
                                <a href="#" class="inline-flex items-center gap-1 text-xs font-bold text-amber-300 hover:text-amber-200 mt-2 transition-colors">
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
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left/Main: Active Ticket & Live Tracking (Spans 2 cols on Large Screens) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Ticket Hero Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/50 shadow-md overflow-hidden relative">
                        <!-- Top Ticket Header Pattern -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 text-white flex justify-between items-center">
                            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider">
                                <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-sm"></span>
                                Tiket Antrean Aktif Hari Ini
                            </span>
                            <span class="text-xs bg-white/20 px-2.5 py-1 rounded-full font-medium">Buka s.d 15:00</span>
                        </div>

                        <!-- Main Ticket Body -->
                        <div class="p-6 md:p-8 space-y-8">
                            <!-- Tenant & Service Title -->
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider">Instansi & Layanan</h3>
                                    <h4 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mt-1">Dinas Kependudukan & Pencatatan Sipil</h4>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mt-0.5">Cetak Kartu Tanda Penduduk Elektronik (KTP-el)</p>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-2xl shrink-0">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Live Ticket Queue Counter -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-700/30 rounded-2xl p-6 border border-gray-200/60 dark:border-gray-700/50">
                                <div class="col-span-2 sm:col-span-1 border-b sm:border-b-0 sm:border-r border-gray-200/60 dark:border-gray-700/50 pb-4 sm:pb-0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Nomor Antrean Anda</p>
                                    <p class="text-4xl md:text-5xl font-extrabold text-blue-600 dark:text-blue-400 mt-1 tracking-tight">A-015</p>
                                </div>
                                <div class="sm:border-r border-gray-200/60 dark:border-gray-700/50 sm:pl-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Panggilan Sekarang</p>
                                    <p class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mt-1 tracking-tight" id="live-current-queue">A-010</p>
                                </div>
                                <div class="sm:pl-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Sisa Antrean</p>
                                    <p class="text-4xl md:text-5xl font-extrabold text-amber-500 mt-1 tracking-tight" id="live-remaining-queues">5 Orang</p>
                                </div>
                            </div>

                            <!-- Stepper Flow -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Perjalanan Antrean</h4>
                                
                                <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-2">
                                    <!-- Stepper Line Connector (Desktop Only) -->
                                    <div class="hidden md:block absolute top-[18px] left-[5%] right-[5%] h-1 bg-gray-200 dark:bg-gray-700 z-0">
                                        <div class="h-full bg-blue-500 transition-all duration-500" id="stepper-progress-line" style="width: 33.33%;"></div>
                                    </div>

                                    <!-- Step 1: Terbooking -->
                                    <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-1-el">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight">Terbooking</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Dari Rumah</p>
                                        </div>
                                    </div>

                                    <!-- Step 2: Terkonfirmasi FO -->
                                    <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-2-el">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 border-2 border-green-500 transition-all duration-300" id="step-2-badge">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight">Check-In FO</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Terkonfirmasi</p>
                                        </div>
                                    </div>

                                    <!-- Step 3: Menunggu Panggilan -->
                                    <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-3-el">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border-2 border-blue-500 animate-pulse transition-all duration-300" id="step-3-badge">
                                            3
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 leading-tight">Menunggu</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Panggilan Gerai</p>
                                        </div>
                                    </div>

                                    <!-- Step 4: Sedang Dilayani -->
                                    <div class="flex items-center md:flex-col md:text-center gap-3 md:gap-2 z-10 flex-1 w-full" id="step-4-el">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 border-2 border-gray-200 dark:border-gray-600 transition-all duration-300" id="step-4-badge">
                                            4
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 leading-tight">Dilayani</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Di Gerai Instansi</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code Modal Open Button -->
                            <div class="pt-2">
                                <button type="button" onclick="openQrModal()" class="w-full flex items-center justify-center gap-2 py-4 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-lg hover:shadow-indigo-500/25 active:scale-98 transition-all">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    Tampilkan QR Code / Kode Unik FO
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Grid -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pintasan Aksi Utama</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <!-- Booking Baru -->
                            <a href="#" class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">Ambil Antrean</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Booking layanan baru</p>
                            </a>
                            <!-- Riwayat -->
                            <a href="#" class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">Riwayat Layanan</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Semua data antrean</p>
                            </a>
                            <!-- Panduan -->
                            <a href="#" class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">Panduan MPP</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Syarat & info layanan</p>
                            </a>
                            <!-- Pengaduan -->
                            <a href="#" class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-red-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">Pusat Bantuan</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Pengaduan & chat</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: MPP Live Density & Active Tenants (Spans 1 col) -->
                <div class="space-y-6">
                    <!-- Live Building Density -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-200 dark:border-gray-700/50 shadow-md relative overflow-hidden">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center justify-between">
                            Kepadatan Gedung MPP
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                        </h3>

                        <div class="flex items-center gap-4">
                            <!-- SVG Gauge Ring -->
                            <div class="relative w-20 h-20 shrink-0">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <!-- Background circle -->
                                    <path class="text-gray-100 dark:text-gray-700" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <!-- Dynamic Progress circle -->
                                    <path id="density-gauge-circle" class="text-blue-600 transition-all duration-1000 ease-out" stroke-dasharray="45, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-sm font-extrabold text-gray-900 dark:text-white" id="density-percentage-text">45%</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Status Kepadatan</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-white mt-0.5 flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500" id="density-status-dot"></span>
                                    <span id="density-status-text">Normal</span>
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" id="density-status-desc">Kondisi kondusif, waktu antrean singkat.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Busiest Tenants List -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-200 dark:border-gray-700/50 shadow-md">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Tenant Teramai Hari Ini</h3>
                        
                        <div class="space-y-4">
                            <!-- Tenant 1: Disdukcapil -->
                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-gray-900 dark:text-white">Disdukcapil</span>
                                    <span class="text-blue-600 dark:text-blue-400" id="tenant-1-counter">18 Antrean</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-1000" id="tenant-1-progress" style="width: 78%;"></div>
                                </div>
                            </div>
                            <!-- Tenant 2: Imigrasi -->
                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-gray-900 dark:text-white">Imigrasi</span>
                                    <span class="text-indigo-600 dark:text-indigo-400" id="tenant-2-counter">12 Antrean</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000" id="tenant-2-progress" style="width: 52%;"></div>
                                </div>
                            </div>
                            <!-- Tenant 3: Samsat -->
                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-gray-900 dark:text-white">Samsat</span>
                                    <span class="text-amber-500" id="tenant-3-counter">6 Antrean</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000" id="tenant-3-progress" style="width: 32%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Simulation Controller (Interactive Sandbox) -->
            <div class="bg-slate-900 text-slate-100 rounded-3xl p-6 border border-slate-800 shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 bottom-0 translate-x-8 translate-y-8 opacity-10 pointer-events-none">
                    <svg class="w-48 h-48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                </div>
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
                            <h4 class="font-extrabold text-sm tracking-wide uppercase">Sandbox Simulasi Real-time</h4>
                        </div>
                        <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded-md text-slate-400 font-bold uppercase">Demo Mode</span>
                    </div>
                    
                    <p class="text-xs text-slate-400 leading-relaxed max-w-2xl">Sandbox client-side ini mensimulasikan data live antrean MPP. Anda dapat membiarkannya berjalan otomatis (polling client-side) atau mengontrolnya secara manual untuk melihat transisi visual, kemajuan stepper, status kepadatan, dan alarm panggilan.</p>
                    
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="button" onclick="advanceSimQueue()" class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold rounded-xl text-xs transition-all shadow-md">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Panggil Antrean (+1)
                        </button>
                        <button type="button" onclick="toggleSimAuto()" id="btn-auto-sim" class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold rounded-xl text-xs transition-all shadow-md">
                            <span class="w-2 h-2 bg-white rounded-full animate-ping" id="auto-ping"></span>
                            <span>Auto-Play: ON (5s)</span>
                        </button>
                        <button type="button" onclick="resetSim()" class="flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 font-bold rounded-xl text-xs transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89" /></svg>
                            Reset
                        </button>
                        <div class="ml-auto text-[10px] text-slate-500 font-mono" id="sim-status-log">Status: Menunggu Antrean...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom QR Code Modal Overlay -->
        <div id="qr-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 max-w-sm w-full border border-gray-100 dark:border-gray-700/50 shadow-2xl relative transform scale-95 transition-transform duration-300" id="qr-modal-card">
                <!-- Close Button -->
                <button onclick="closeQrModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:white p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Body -->
                <div class="text-center space-y-6 pt-2">
                    <div>
                        <h3 class="font-extrabold text-xl text-gray-900 dark:text-white leading-tight">Check-In Front Office</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Scan kode ini di meja FO MPP Sawahlunto</p>
                    </div>

                    <!-- Pixel-perfect Mock QR Code -->
                    <div class="bg-gray-50 dark:bg-white p-6 rounded-2xl border border-gray-100 inline-block shadow-inner mx-auto relative group">
                        <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Positioning Squares -->
                            <rect x="0" y="0" width="25" height="25" fill="#1e293b" />
                            <rect x="3" y="3" width="19" height="19" fill="#ffffff" />
                            <rect x="6" y="6" width="13" height="13" fill="#2563eb" />

                            <rect x="75" y="0" width="25" height="25" fill="#1e293b" />
                            <rect x="78" y="3" width="19" height="19" fill="#ffffff" />
                            <rect x="81" y="6" width="13" height="13" fill="#2563eb" />

                            <rect x="0" y="75" width="25" height="25" fill="#1e293b" />
                            <rect x="3" y="78" width="19" height="19" fill="#ffffff" />
                            <rect x="6" y="81" width="13" height="13" fill="#2563eb" />

                            <!-- Small alignment squares -->
                            <rect x="70" y="70" width="10" height="10" fill="#1e293b" />
                            <rect x="72" y="72" width="6" height="6" fill="#ffffff" />
                            <rect x="74" y="74" width="2" height="2" fill="#2563eb" />

                            <!-- Randomly scattered blocks mimicking QR patterns -->
                            <rect x="30" y="2" width="10" height="4" fill="#1e293b" />
                            <rect x="45" y="5" width="8" height="5" fill="#1e293b" />
                            <rect x="60" y="3" width="5" height="15" fill="#2563eb" />
                            <rect x="35" y="12" width="12" height="6" fill="#1e293b" />
                            
                            <rect x="2" y="30" width="15" height="5" fill="#1e293b" />
                            <rect x="25" y="28" width="6" height="12" fill="#2563eb" />
                            <rect x="38" y="32" width="20" height="8" fill="#1e293b" />
                            <rect x="65" y="25" width="8" height="12" fill="#1e293b" />
                            
                            <rect x="5" y="50" width="12" height="6" fill="#2563eb" />
                            <rect x="25" y="48" width="15" height="10" fill="#1e293b" />
                            <rect x="48" y="45" width="25" height="5" fill="#1e293b" />
                            <rect x="80" y="35" width="12" height="15" fill="#2563eb" />
                            
                            <rect x="35" y="65" width="15" height="15" fill="#1e293b" />
                            <rect x="55" y="60" width="10" height="10" fill="#2563eb" />
                            <rect x="68" y="55" width="8" height="8" fill="#1e293b" />
                            
                            <rect x="30" y="85" width="25" height="6" fill="#2563eb" />
                            <rect x="60" y="82" width="6" height="12" fill="#1e293b" />
                            
                            <!-- Custom logo in the middle -->
                            <rect x="40" y="40" width="20" height="20" fill="#ffffff" rx="2" />
                            <circle cx="50" cy="50" r="8" fill="#2563eb" />
                            <circle cx="50" cy="50" r="5" fill="#ffffff" />
                        </svg>
                        
                        <!-- Pulse Ring around QR -->
                        <div class="absolute inset-0 border-4 border-blue-500/0 rounded-2xl group-hover:border-blue-500/20 transition-all duration-700 animate-pulse pointer-events-none"></div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">KODE BOOKING</div>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-wider font-mono">A-015</div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                            Telah Terkonfirmasi FO
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-[240px] mx-auto leading-relaxed">Dekatkan layar ponsel Anda ke alat pemindai kode di loket Front Office.</p>
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
                return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
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
                step3Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border-2 border-blue-500 animate-pulse transition-all duration-300";
                step3Badge.innerText = "3";
                
                const step4Badge = document.getElementById('step-4-badge');
                step4Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 border-2 border-gray-200 dark:border-gray-600 transition-all duration-300";
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
                        step4Badge.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border-2 border-blue-500 animate-pulse transition-all duration-300";
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
                // Update queue counters
                document.getElementById('live-current-queue').innerText = `A-0${simState.currentQueue}`;
                document.getElementById('live-remaining-queues').innerText = `${simState.totalRemaining} Orang`;
                
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
                    dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-blue-500';
                    circle.className = 'text-blue-600 transition-all duration-1000 ease-out';
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

            // Play clean chime/beep sound
            function triggerAlertSound() {
                const aud = document.getElementById('notif-sound');
                if (aud) {
                    aud.currentTime = 0;
                    aud.play().catch(e => console.log("Audio play prevented by browser policy. Interaction needed first."));
                }
            }

            // QR Code Modal controllers
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

            // Close QR Code Modal
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
        </script>

    @elseif ($role === 'admin_fo')
        {{-- Admin FO Dashboard --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin Front Office</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem Antrean Digital MPP</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Modul Manajemen Antrean</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selamat datang. Modul pemanggilan dan manajemen antrean Front Office sedang dalam tahap pengembangan.</p>
                </div>
            </div>
        </div>

    @elseif ($role === 'admin_gerai')
        {{-- Admin Gerai Dashboard --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin Gerai</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem Antrean Digital MPP</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.999 2.999 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.999 2.999 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Modul Layanan Gerai</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selamat datang. Modul layanan khusus untuk instansi gerai Anda sedang dalam tahap pengembangan.</p>
                </div>
            </div>
        </div>

    @elseif ($role === 'super_admin')
        {{-- Super Admin Dashboard --}}
        <div class="space-y-6 pb-12">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider">Live Monitoring Active</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">Pusat Kendali & Kinerja MPP</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pemantauan real-time arus kunjungan, efisiensi loket, dan performa gerai instansi.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button id="btnSimulationToggle" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-850 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300 transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pause Simulasi</span>
                    </button>
                    <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 hover:shadow-lg transition-all cursor-pointer">
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
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                        <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Kunjungan Hari Ini</p>
                            <h3 id="statTotalKunjungan" class="text-3xl font-extrabold text-gray-800 dark:text-white mt-2 transition-all">342</h3>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 relative z-10">
                        <span class="text-green-600 dark:text-green-400 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>+12%</span>
                        </span>
                        <span>vs. rata-rata harian</span>
                    </div>
                </div>

                <!-- Card 2: Menunggu Konfirmasi FO -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                        <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                        </svg>
                    </div>
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Menunggu Konfirmasi FO</p>
                            <h3 id="statMenungguFO" class="text-3xl font-extrabold text-gray-800 dark:text-white mt-2 transition-all">18</h3>
                        </div>
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 relative z-10">
                        <span class="text-amber-600 dark:text-amber-400 font-bold flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Sedang</span>
                        </span>
                        <span>Antrean di loket depan</span>
                    </div>
                </div>

                <!-- Card 3: Sedang Dilayani -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                        <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z" />
                        </svg>
                    </div>
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sedang Dilayani di Gerai</p>
                            <h3 id="statSedangDilayani" class="text-3xl font-extrabold text-gray-800 dark:text-white mt-2 transition-all">24</h3>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 relative z-10">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Aktif</span>
                        <span>di loket gerai instansi</span>
                    </div>
                </div>

                <!-- Card 4: Tenant Aktif -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                        <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                        </svg>
                    </div>
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Tenant Aktif</p>
                            <h3 id="statTenantAktif" class="text-3xl font-extrabold text-gray-800 dark:text-white mt-2 transition-all">12 <span class="text-lg font-medium text-gray-400">/ 15</span></h3>
                        </div>
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 relative z-10">
                        <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-purple-600 h-full rounded-full" style="width: 80%"></div>
                        </div>
                        <span class="shrink-0 text-[10px] font-bold text-purple-600 dark:text-purple-400">80% Buka</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Line Chart: Tren Kedatangan -->
                <div class="lg:col-span-7 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">Tren Kedatangan Pengunjung</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Membandingkan arus masuk Booking Online vs. On-site per jam</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase">Online</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase">On-site</span>
                            </div>
                        </div>
                    </div>
                    <div id="chartTrenKedatangan" class="w-full h-80 min-h-[320px]"></div>
                </div>

                <!-- Bar Chart: Top Tenant -->
                <div class="lg:col-span-5 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs flex flex-col">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">Top Tenant Terpadat</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Instansi dengan volume antrean tertinggi hari ini</p>
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <div id="chartTopTenant" class="w-full h-80 min-h-[320px]"></div>
                    </div>
                </div>
            </div>

            <!-- Table & FO Widget Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Tabel Pemantauan Live Tenant (The Core Feature) -->
                <div class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">Pemantauan Live Tenant</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Metrik real-time keaktifan loket dan beban antrean instansi</p>
                        </div>
                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse">
                            Auto Refreshing
                        </span>
                    </div>

                    <div class="overflow-x-auto -mx-6">
                        <table id="tblLiveTenant" class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-700/20 text-gray-400 dark:text-gray-500 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100/80 dark:border-gray-700">
                                    <th class="py-3 px-6">Nama Instansi</th>
                                    <th class="py-3 px-4">Loket</th>
                                    <th class="py-3 px-4">Menunggu</th>
                                    <th class="py-3 px-4">Rerata Pelayanan</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/50">
                                <!-- Dukcapil -->
                                <tr data-instansi="Dukcapil" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                DK
                                            </div>
                                            <span>Dukcapil</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600 dark:text-gray-300">3 Loket</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <span class="queue-count">24</span> <span class="text-xs font-normal text-gray-400">orang</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-300">12 Menit / Orang</td>
                                    <td class="py-4 px-4">
                                        <span class="status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            Padat
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button onclick="tegurTenant('Dukcapil')" class="btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer">
                                            Tegur
                                        </button>
                                    </td>
                                </tr>
                                <!-- Imigrasi -->
                                <tr data-instansi="Imigrasi" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                IM
                                            </div>
                                            <span>Imigrasi</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600 dark:text-gray-300">2 Loket</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <span class="queue-count">5</span> <span class="text-xs font-normal text-gray-400">orang</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-300">20 Menit / Orang</td>
                                    <td class="py-4 px-4">
                                        <span class="status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Lancar
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button onclick="tegurTenant('Imigrasi')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg text-xs font-bold transition-all border border-gray-100 dark:border-gray-700/80 cursor-not-allowed" disabled>
                                            Tegur
                                        </button>
                                    </td>
                                </tr>
                                <!-- Bapenda -->
                                <tr data-instansi="Bapenda" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                BP
                                            </div>
                                            <span>Bapenda</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600 dark:text-gray-300">1 Loket</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <span class="queue-count">0</span> <span class="text-xs font-normal text-gray-400">orang</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-300">8 Menit / Orang</td>
                                    <td class="py-4 px-4">
                                        <span class="status-badge bg-gray-50 dark:bg-gray-700/40 text-gray-500 dark:text-gray-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Kosong
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button onclick="tegurTenant('Bapenda')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg text-xs font-bold transition-all border border-gray-100 dark:border-gray-700/80 cursor-not-allowed" disabled>
                                            Tegur
                                        </button>
                                    </td>
                                </tr>
                                <!-- Samsat -->
                                <tr data-instansi="Samsat" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                SM
                                            </div>
                                            <span>Samsat</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600 dark:text-gray-300">2 Loket</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <span class="queue-count">12</span> <span class="text-xs font-normal text-gray-400">orang</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-300">15 Menit / Orang</td>
                                    <td class="py-4 px-4">
                                        <span class="status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            Padat
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button onclick="tegurTenant('Samsat')" class="btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer">
                                            Tegur
                                        </button>
                                    </td>
                                </tr>
                                <!-- BPJS Kesehatan -->
                                <tr data-instansi="BPJS Kesehatan" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                BP
                                            </div>
                                            <span>BPJS Kesehatan</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600 dark:text-gray-300">1 Loket</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <span class="queue-count">3</span> <span class="text-xs font-normal text-gray-400">orang</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-300">10 Menit / Orang</td>
                                    <td class="py-4 px-4">
                                        <span class="status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Lancar
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button onclick="tegurTenant('BPJS Kesehatan')" class="btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg text-xs font-bold transition-all border border-gray-100 dark:border-gray-700/80 cursor-not-allowed" disabled>
                                            Tegur
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Widget Booking Online (FO Efficiency) -->
                <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/50 shadow-xs flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">Alur Booking Online & FO</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Efisiensi petugas Front Office memproses verifikasi kode unik</p>
                    </div>

                    <!-- Gauge / Speed visual indicator -->
                    <div class="py-6 flex flex-col items-center justify-center relative">
                        <div class="relative w-40 h-40 flex items-center justify-center">
                            <!-- SVG Gauge Arc -->
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <!-- Background Circle -->
                                <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-gray-100 dark:text-gray-700" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-linecap="round" />
                                <!-- Progress Circle (Color varies based on value: green/yellow/red) -->
                                <circle id="gaugeProgressArc" cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-emerald-500" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="140" stroke-linecap="round" />
                            </svg>
                            <!-- Inner Content -->
                            <div class="absolute flex flex-col items-center justify-center text-center">
                                <span id="valCheckInTime" class="text-3xl font-extrabold text-gray-800 dark:text-white">2.4</span>
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase">Menit / Tiket</span>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <span id="badgeCheckInStatus" class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                Efisien / Lancar
                            </span>
                            <p class="text-[11px] text-gray-400 dark:text-gray-550 mt-2 max-w-[220px] mx-auto leading-relaxed">
                                Target check-in FO: <span class="font-bold text-gray-700 dark:text-gray-300">&lt; 3.0 menit</span>. Saat ini tidak terjadi penumpukan (bottleneck).
                            </p>
                        </div>
                    </div>

                    <!-- Live feed events (simulates Laravel Reverb) -->
                    <div class="border-t border-gray-100 dark:border-gray-700/50 pt-4 mt-auto">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-550 uppercase tracking-widest">Live Activity Feed</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        </div>
                        <div id="liveActivityFeed" class="space-y-2 max-h-[110px] overflow-y-auto pr-1 text-xs text-gray-500 dark:text-gray-400 font-mono">
                            <div class="flex items-start gap-1">
                                <span class="text-[10px] text-gray-400">17:59</span>
                                <span class="text-gray-700 dark:text-gray-300 font-semibold shrink-0">&bull; System:</span>
                                <span class="text-gray-600 dark:text-gray-400">WebSocket monitoring aktif.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="text-[10px] text-gray-400">17:58</span>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold shrink-0">&bull; FO Admin:</span>
                                <span class="text-gray-600 dark:text-gray-400">Verifikasi tiket B-490 selesai.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Toast Container -->
        <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    @else
        {{-- Unknown Role --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8 flex flex-col items-center justify-center min-h-[60vh]">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Role Tidak Dikenali</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">Silakan hubungi administrator untuk mengatur hak akses Anda.</p>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-medium hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        Kembali ke Login
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection

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
            colors: ['#2563eb', '#6366f1'], // blue-600, indigo-500
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
                borderColor: '#f1f5f9',
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
            colors: ['#ef4444', '#f97316', '#3b82f6', '#10b981', '#6366f1'], // red, orange, blue, emerald, indigo
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
                borderColor: '#f1f5f9',
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
                <span class="text-[10px] text-gray-405 dark:text-gray-500">${hours}:${minutes}</span>
                <span class="text-blue-600 dark:text-blue-400 font-semibold shrink-0">&bull; ${user}:</span>
                <span class="text-gray-700 dark:text-gray-300">${action}</span>
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
            toast.className = 'bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-lg border-gray-100 dark:border-gray-750 flex gap-3 pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-300';
            
            let iconColor = 'text-blue-500';
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
                iconColor = 'text-amber-500';
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
                    <p class="text-xs font-bold text-gray-800 dark:text-white">${title}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
        // Radius of 40 has circumference of 2 * pi * r = 251.2
        // We only cover 3/4 of the circle, i.e., 188.4. Offset calculation:
        // A check-in time of 6 min is 100% load (offset = 62.8), 0 min is 0% load (offset = 251.2)
        function updateFOGauge(val) {
            valCheckInTime.innerText = val.toFixed(1);
            
            // Limit value between 0.5 and 6
            const percent = Math.min(Math.max((val - 0.5) / 5.5, 0), 1);
            const offset = 251.2 - (188.4 * percent);
            
            gaugeProgressArc.setAttribute('stroke-dashoffset', offset);

            // Color and label changes based on performance
            if (val < 3.0) {
                gaugeProgressArc.setAttribute('class', 'text-emerald-500');
                badgeCheckInStatus.setAttribute('class', 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider');
                badgeCheckInStatus.innerText = "Efisien / Lancar";
            } else if (val < 5.0) {
                gaugeProgressArc.setAttribute('class', 'text-amber-500');
                badgeCheckInStatus.setAttribute('class', 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider');
                badgeCheckInStatus.innerText = "Menumpuk (Sedang)";
            } else {
                gaugeProgressArc.setAttribute('class', 'text-rose-500');
                badgeCheckInStatus.setAttribute('class', 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider');
                badgeCheckInStatus.innerText = "BOTTLENECK!";
            }
        }

        function runSimulationStep() {
            // Random events
            const rand = Math.random();
            
            if (rand < 0.25) {
                // New Online Booking confirmed at FO
                stats.totalKunjungan += 1;
                if (stats.menungguFO > 0) stats.menungguFO -= 1;
                stats.sedangDilayani += 1;
                
                // Select random tenant to increase queue
                const tenants = ['Dukcapil', 'Samsat', 'BPJS Kesehatan', 'Imigrasi'];
                const selected = tenants[Math.floor(Math.random() * tenants.length)];
                stats.queues[selected] += 1;

                // Update UI elements
                statTotalKunjungan.innerText = stats.totalKunjungan;
                statMenungguFO.innerText = stats.menungguFO;
                statSedangDilayani.innerText = stats.sedangDilayani;
                
                // Update table row
                updateTableRow(selected);

                // Add to activity feed
                const codes = ['A', 'B', 'C', 'D'];
                const randCode = codes[Math.floor(Math.random() * codes.length)] + '-' + Math.floor(Math.random() * 900 + 100);
                addActivityFeed('Front Office', `Tiket ${randCode} telah dikonfirmasi untuk Gerai ${selected}`);
                createToast('Check-in Berhasil', `Tiket ${randCode} dikonfirmasi untuk gerai ${selected}.`, 'success');
            } 
            else if (rand < 0.50) {
                // Someone finished service at a counter
                if (stats.sedangDilayani > 0) stats.sedangDilayani -= 1;
                
                // Select random tenant with queues to decrease
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

            // Slight fluctuation in FO processing speed
            const speedChange = (Math.random() - 0.5) * 0.4;
            stats.foCheckInTime = Math.min(Math.max(stats.foCheckInTime + speedChange, 1.2), 5.8);
            updateFOGauge(stats.foCheckInTime);

            // Bottleneck toast if check-in time exceeds threshold
            if (stats.foCheckInTime > 5.0 && rand < 0.1) {
                createToast(
                    'Peringatan Bottleneck FO!',
                    `Waktu antrean verifikasi loket depan melebihi 5 menit.`,
                    'warning'
                );
            }

            // Update chart lines data dynamically every minute/step
            if (Math.random() < 0.20) {
                const currentDataOnline = [...chartTren.w.config.series[0].data];
                const currentDataOnsite = [...chartTren.w.config.series[1].data];
                
                // Shift data and append new
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

            // Determine status, color and Nudge button availability
            if (count >= 15) {
                statusEl.setAttribute('class', 'status-badge bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>Padat`;
                btnTegur.removeAttribute('disabled');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30 cursor-pointer');
            } else if (count >= 4) {
                statusEl.setAttribute('class', 'status-badge bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lancar`;
                btnTegur.setAttribute('disabled', 'true');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg text-xs font-bold transition-all border border-gray-100 dark:border-gray-700 cursor-not-allowed');
            } else {
                statusEl.setAttribute('class', 'status-badge bg-gray-50 dark:bg-gray-700/40 text-gray-500 dark:text-gray-400 px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5');
                statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Kosong`;
                btnTegur.setAttribute('disabled', 'true');
                btnTegur.setAttribute('class', 'btn-tegur px-3 py-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg text-xs font-bold transition-all border border-gray-100 dark:border-gray-700 cursor-not-allowed');
            }

            // Update bar chart data dynamically based on the updated queue sizes
            const order = ['Dukcapil', 'Samsat', 'Imigrasi', 'BPJS Kesehatan', 'Bapenda'];
            const barData = order.map(t => stats.queues[t] * 4); // scaled
            chartTop.updateSeries([{ name: 'Volume Antrean', data: barData }]);
        }

        // Start / Pause Simulation
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
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

