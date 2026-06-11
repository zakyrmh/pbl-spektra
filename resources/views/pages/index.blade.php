@extends('layouts.public')

@section('title', 'Sistem Antrean Digital - MPP Kota Sawahlunto')

@section('content')
<!-- 1. HERO SECTION -->
<section id="home" class="relative overflow-hidden bg-canvas py-16 lg:py-24">
    <!-- Subtle Background Accents -->
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.primary/5),transparent)]"></div>
    <div class="absolute top-0 right-0 -z-10 w-96 h-96 bg-accent-teal/5 rounded-full blur-3xl" aria-hidden="true"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Column: Branding and CTAs -->
            <div class="lg:col-span-7 space-y-6 text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-pill bg-primary/10 text-primary border border-primary/20">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse" aria-hidden="true"></span>
                    <span class="font-display font-semibold text-caption uppercase tracking-wider">Layanan Publik Digital Resmi</span>
                </div>
                
                <h1 class="font-display font-extrabold text-ink tracking-tight text-display-md sm:text-display-lg leading-tight">
                    Sistem Antrean Digital <br class="hidden sm:inline">
                    <span class="text-primary">Mal Pelayanan Publik</span> <br>
                    Kota Sawahlunto
                </h1>
                
                <p class="text-body-md sm:text-title-sm text-body leading-relaxed max-w-xl">
                    Sederhana, Cepat, dan Transparan. Ambil nomor antrean secara online dari mana saja tanpa perlu mengantre lama di lokasi.
                </p>
                
                <!-- Trust Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-hairline-soft">
                    <div>
                        <span class="block font-mono font-bold text-ink text-title-lg sm:text-display-sm">{{ $totalInstansi }}</span>
                        <span class="text-caption text-muted">Instansi Aktif</span>
                    </div>
                    <div>
                        <span class="block font-mono font-bold text-ink text-title-lg sm:text-display-sm">{{ $rataWaktuTunggu }}</span>
                        <span class="text-caption text-muted">Rata-rata Tunggu</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Ringkasan Status Loket Saat Ini (Umum) -->
            <div class="lg:col-span-5 relative">
                <!-- Decorative backdrops -->
                <div class="absolute -top-6 -left-6 w-72 h-72 bg-primary/5 rounded-full -z-10 blur-xl" aria-hidden="true"></div>
                <div class="absolute -bottom-6 -right-6 w-72 h-72 bg-accent-teal/5 rounded-full -z-10 blur-xl" aria-hidden="true"></div>
                
                <!-- Ringkasan Status Loket Saat Ini Card Mockup -->
                <div class="bg-canvas border border-hairline rounded-xl p-6 shadow-xl relative overflow-hidden" 
                     x-data="{ 
                        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) 
                     }"
                     x-init="setInterval(() => { 
                        time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                     }, 1000)">
                    
                    <!-- Card Header -->
                    <div class="flex justify-between items-center pb-4 border-b border-hairline-soft mb-6">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-full bg-primary/10 text-primary">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                            <span class="font-display font-bold text-ink text-body-sm">Ringkasan Status Loket Saat Ini</span>
                        </div>
                        <span class="font-mono text-caption text-muted" x-text="time">12:34:56</span>
                    </div>
                    
                    <!-- Status Main Grid -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Loket 1 -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-surface-soft border border-hairline-soft">
                                <div class="text-left">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Loket 01 (Disdukcapil)</span>
                                    <span class="block font-display font-semibold text-ink text-body-sm">Perekaman KTP-el</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Dilayani</span>
                                    <span class="font-mono font-bold text-primary text-title-md">A-021</span>
                                </div>
                            </div>
                            
                            <!-- Loket 2 -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-surface-soft border border-hairline-soft">
                                <div class="text-left">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Loket 02 (DPMPTSP)</span>
                                    <span class="block font-display font-semibold text-ink text-body-sm">Izin Usaha (NIB)</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Dilayani</span>
                                    <span class="font-mono font-bold text-primary text-title-md">B-008</span>
                                </div>
                            </div>
                            
                            <!-- Loket 3 -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-surface-soft border border-hairline-soft">
                                <div class="text-left">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Loket 03 (Bapenda)</span>
                                    <span class="block font-display font-semibold text-ink text-body-sm">Pembayaran PBB</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[11px] text-muted uppercase font-semibold">Dilayani</span>
                                    <span class="font-mono font-bold text-primary text-title-md">C-015</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- General Status Badges / Overview -->
                        <div class="pt-4 border-t border-hairline-soft grid grid-cols-2 gap-4">
                            <div class="text-left">
                                <span class="block text-[11px] text-muted uppercase font-semibold">Total Antrean Aktif</span>
                                <span class="block font-display font-bold text-ink text-body-md">28 Orang</span>
                            </div>
                            <div class="text-left">
                                <span class="block text-[11px] text-muted uppercase font-semibold">Status Gedung</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-pill bg-[rgba(5,150,105,0.12)] text-[#065F46] text-caption font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#059669] animate-pulse" aria-hidden="true"></span>
                                    Lancar
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Mini Monitor Widget -->
                <div class="absolute -bottom-6 -left-6 bg-surface-dark text-on-dark rounded-lg p-4 border border-surface-dark-elevated shadow-lg hidden sm:block max-w-[200px]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-accent-teal animate-pulse" aria-hidden="true"></span>
                        <span class="font-display font-medium text-[10px] text-on-dark-soft uppercase">Panggilan Terkini</span>
                    </div>
                    <div class="font-mono text-title-lg font-bold text-accent-teal leading-none">A-021</div>
                    <div class="text-[10px] text-on-dark-soft mt-1">Dipanggil ke <span class="text-on-dark font-semibold">Loket 01</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. LIVE QUEUE MONITOR -->
<section id="live-monitor" class="py-20 bg-surface-soft border-y border-hairline-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-12 space-y-4">
            <h2 class="font-display font-bold text-ink text-display-md">
                Pantauan Antrean Real-Time
            </h2>
            <p class="text-body-md text-muted">
                Pantau kondisi kepadatan layanan di Mal Pelayanan Publik secara langsung sebelum Anda berkunjung untuk menghemat waktu.
            </p>
        </div>
        
        <!-- Dashboard Card Container -->
        <div class="bg-canvas border border-hairline rounded-xl shadow-sm overflow-hidden" 
             x-data="{
                searchQuery: '',
                statusFilter: 'all',
                queues: [],
                loading: true,
                async fetchQueues() {
                    try {
                        const response = await fetch('/api/live-queues');
                        if (!response.ok) throw new Error('Network response was not ok');
                        this.queues = await response.json();
                    } catch (error) {
                        console.error('Gagal mengambil data antrean:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                init() {
                    this.fetchQueues();
                    setInterval(() => {
                        this.fetchQueues();
                    }, 10000); // Polling every 10 seconds
                },
                filteredQueues() {
                    if (this.loading) return [];
                    return this.queues.filter(item => {
                        const agencyStr = item.agency || '';
                        const serviceStr = item.service || '';
                        const matchesSearch = agencyStr.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                               serviceStr.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesFilter = this.statusFilter === 'all' || 
                                               (this.statusFilter === 'lancar' && item.status === 'Lancar') ||
                                               (this.statusFilter === 'padat' && item.status === 'Padat');
                        return matchesSearch && matchesFilter;
                    });
                }
             }">
            
            <!-- Control Panel -->
            <div class="p-6 border-b border-hairline-soft flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Search input -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           class="block w-full pl-10 pr-4 py-2 border border-hairline rounded-pill bg-canvas text-ink text-body-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-shadow" 
                           placeholder="Cari instansi atau jenis layanan...">
                </div>
                
                <!-- Filter buttons -->
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <span class="text-caption text-muted font-semibold uppercase tracking-wider mr-2">Status:</span>
                    <button @click="statusFilter = 'all'" 
                            :class="statusFilter === 'all' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-soft text-body hover:bg-surface-strong'"
                            class="px-4 py-1.5 text-caption font-semibold rounded-pill transition-colors cursor-pointer">
                        Semua
                    </button>
                    <button @click="statusFilter = 'lancar'" 
                            :class="statusFilter === 'lancar' ? 'bg-[#059669] text-white shadow-sm' : 'bg-surface-soft text-body hover:bg-surface-strong'"
                            class="px-4 py-1.5 text-caption font-semibold rounded-pill transition-colors cursor-pointer">
                        Lancar
                    </button>
                    <button @click="statusFilter = 'padat'" 
                            :class="statusFilter === 'padat' ? 'bg-[#DC2626] text-white shadow-sm' : 'bg-surface-soft text-body hover:bg-surface-strong'"
                            class="px-4 py-1.5 text-caption font-semibold rounded-pill transition-colors cursor-pointer">
                        Padat
                    </button>
                </div>
            </div>
            
            <!-- Mobile View: Card-based Layout -->
            <div class="block md:hidden divide-y divide-hairline-soft bg-canvas">
                <!-- Loading State -->
                <div x-show="loading" class="p-8 text-center text-muted text-body-sm flex flex-col items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-primary mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memuat data antrean...</span>
                </div>

                <template x-for="(item, index) in filteredQueues()" :key="index">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="font-display font-bold text-ink text-body-md" x-text="item.agency"></h4>
                                <p class="text-body-sm text-muted" x-text="item.service"></p>
                            </div>
                            <!-- Status Badge -->
                            <span :class="item.status === 'Lancar' ? 'bg-[rgba(5,150,105,0.12)] text-[#065F46]' : 'bg-[rgba(220,38,38,0.12)] text-[#991B1B]'" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-medium shrink-0">
                                <span :class="item.status === 'Lancar' ? 'bg-[#059669]' : 'bg-[#DC2626]'" class="w-1.5 h-1.5 rounded-full" aria-hidden="true"></span>
                                <span x-text="item.status"></span>
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-hairline-soft/50 text-left">
                          <div>
                              <span class="block text-[11px] text-muted uppercase font-semibold">Nomor Loket</span>
                              <span class="font-display font-bold text-ink text-body-sm" x-text="item.counter"></span>
                          </div>
                          <div>
                              <span class="block text-[11px] text-muted uppercase font-semibold">Sedang Melayani</span>
                              <span class="font-mono font-bold text-primary text-body-sm" x-text="item.current"></span>
                          </div>
                          <div>
                              <span class="block text-[11px] text-muted uppercase font-semibold">Sisa Antrean</span>
                              <span class="font-display font-bold text-ink text-body-sm" x-text="item.waiting + ' Orang'"></span>
                          </div>
                        </div>
                    </div>
                </template>
                <div x-show="!loading && filteredQueues().length === 0" class="p-8 text-center text-muted text-body-sm">
                    Tidak ada instansi atau layanan yang cocok dengan filter pencarian.
                </div>
            </div>
            
            <!-- Desktop View: Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft text-title-sm text-muted border-b border-hairline">
                            <th class="px-6 py-4 font-semibold">Nama Instansi / Layanan</th>
                            <th class="px-6 py-4 font-semibold text-center">Nomor Loket</th>
                            <th class="px-6 py-4 font-semibold text-center">Nomor Sedang Melayani</th>
                            <th class="px-6 py-4 font-semibold text-center">Jumlah Antrean Menunggu</th>
                            <th class="px-6 py-4 font-semibold text-center">Status Kepadatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        <!-- Loading State -->
                        <tr x-show="loading">
                            <td colspan="5" class="px-6 py-8 text-center text-muted text-body-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-primary mx-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memuat data antrean...</span>
                                </div>
                            </td>
                        </tr>

                        <template x-for="(item, index) in filteredQueues()" :key="index">
                            <tr class="hover:bg-surface-soft/40 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="font-display font-bold text-ink text-body-md" x-text="item.agency"></div>
                                    <div class="text-body-sm text-muted" x-text="item.service"></div>
                                </td>
                                <td class="px-6 py-5 text-center font-display font-semibold text-body-md text-ink" x-text="item.counter"></td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-block font-mono font-bold text-primary text-title-lg bg-primary/5 px-4 py-1.5 rounded-lg border border-primary/10" x-text="item.current"></span>
                                </td>
                                <td class="px-6 py-5 text-center font-display font-bold text-body-md text-ink">
                                    <span x-text="item.waiting"></span> <span class="text-body-sm text-muted font-normal">Orang</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span :class="item.status === 'Lancar' ? 'bg-[rgba(5,150,105,0.12)] text-[#065F46]' : 'bg-[rgba(220,38,38,0.12)] text-[#991B1B]'" 
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold">
                                        <span :class="item.status === 'Lancar' ? 'bg-[#059669]' : 'bg-[#DC2626]'" class="w-2 h-2 rounded-full" aria-hidden="true"></span>
                                        <span x-text="item.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!loading && filteredQueues().length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-muted text-body-sm">
                                Tidak ada instansi atau layanan yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- 3. ALUR PENGGUNAAN -->
<section id="alur" class="py-20 bg-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-pill bg-primary/10 text-primary border border-primary/20">
                <span class="font-display font-semibold text-caption uppercase tracking-wider">Langkah Mudah</span>
            </div>
            <h2 class="font-display font-bold text-ink text-display-md">
                Alur Pengambilan Antrean Online
            </h2>
            <p class="text-body-md text-muted">
                Dapatkan nomor antrean Anda hanya dalam beberapa menit dengan mengikuti 4 langkah mudah berikut.
            </p>
        </div>
        
        <!-- Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            <!-- Connecting line for desktop (hidden on mobile) -->
            <div class="hidden lg:block absolute top-16 left-16 right-16 h-0.5 bg-hairline -z-10" aria-hidden="true"></div>
            
            <!-- Step 1 -->
            <div class="space-y-4 text-center lg:text-left bg-canvas p-6 rounded-lg border border-hairline-soft lg:border-none lg:p-0">
                <div class="mx-auto lg:mx-0 w-12 h-12 rounded-full bg-primary text-on-primary font-display font-bold text-title-md flex items-center justify-between shadow-md ring-8 ring-primary/10">
                    <span class="w-full text-center">1</span>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-sm">1. Pilih Instansi & Layanan</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Cari instansi pemerintah yang dituju (misal: Disdukcapil, Bapenda) lalu tentukan jenis layanan administrasi yang diperlukan.
                    </p>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="space-y-4 text-center lg:text-left bg-canvas p-6 rounded-lg border border-hairline-soft lg:border-none lg:p-0">
                <div class="mx-auto lg:mx-0 w-12 h-12 rounded-full bg-primary text-on-primary font-display font-bold text-title-md flex items-center justify-between shadow-md ring-8 ring-primary/10">
                    <span class="w-full text-center">2</span>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-sm">2. Tentukan Waktu Kunjungan</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Pilih hari kedatangan dan pilih sesi jam kunjungan yang tersedia agar Anda dapat menyesuaikan jadwal pribadi.
                    </p>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="space-y-4 text-center lg:text-left bg-canvas p-6 rounded-lg border border-hairline-soft lg:border-none lg:p-0">
                <div class="mx-auto lg:mx-0 w-12 h-12 rounded-full bg-primary text-on-primary font-display font-bold text-title-md flex items-center justify-between shadow-md ring-8 ring-primary/10">
                    <span class="w-full text-center">3</span>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-sm">3. Isi Data Diri (NIK & WA)</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Masukkan NIK dan nomor WhatsApp aktif untuk verifikasi data diri dan menerima info pemanggilan secara real-time.
                    </p>
                </div>
            </div>
            
            <!-- Step 4 -->
            <div class="space-y-4 text-center lg:text-left bg-canvas p-6 rounded-lg border border-hairline-soft lg:border-none lg:p-0">
                <div class="mx-auto lg:mx-0 w-12 h-12 rounded-full bg-accent-teal text-white font-display font-bold text-title-md flex items-center justify-between shadow-md ring-8 ring-accent-teal/10">
                    <span class="w-full text-center">4</span>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-sm">4. Simpan Tiket Digital</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Simpan tiket digital berupa QR Code unik di HP Anda. Tunjukkan QR Code ini pada mesin kiosk saat tiba di lokasi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. FITUR UTAMA SISTEM -->
<section id="fitur" class="py-20 bg-surface-soft border-t border-hairline-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <h2 class="font-display font-bold text-ink text-display-md">
                Keunggulan Antrean Digital
            </h2>
            <p class="text-body-md text-muted">
                Dirancang untuk memudahkan warga Kota Sawahlunto dalam mengakses layanan publik yang lebih transparan dan efisien.
            </p>
        </div>
        
        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1: Booking Fleksibel -->
            <div class="bg-canvas border border-hairline rounded-lg p-8 hover:-translate-y-1 hover:shadow-md transition-all duration-200 space-y-5">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-md">Booking Fleksibel</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Pilih tanggal kunjungan dan sesi jam kedatangan yang paling sesuai dengan waktu luang Anda. Kapasitas kuota per sesi dibatasi demi ketertiban bersama.
                    </p>
                </div>
            </div>
            
            <!-- Feature 2: Notifikasi Transparan -->
            <div class="bg-canvas border border-hairline rounded-lg p-8 hover:-translate-y-1 hover:shadow-md transition-all duration-200 space-y-5">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-md">Notifikasi Transparan</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Pantau pergerakan sisa nomor antrean secara langsung dari HP Anda. Anda tidak perlu berada di ruang tunggu MPP secara terus-menerus.
                    </p>
                </div>
            </div>
            
            <!-- Feature 3: Manajemen Kuota Harian -->
            <div class="bg-canvas border border-hairline rounded-lg p-8 hover:-translate-y-1 hover:shadow-md transition-all duration-200 space-y-5">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="font-display font-bold text-ink text-title-md">Kuota Real-Time</h3>
                    <p class="text-body-sm text-body leading-relaxed">
                        Informasi ketersediaan sisa kuota layanan diupdate otomatis. Dapatkan kepastian pelayanan publik secara transparan sebelum memulai kunjungan Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. INFORMASI OPERASIONAL & KONTAK -->
<section id="kontak" class="py-20 bg-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <h2 class="font-display font-bold text-ink text-display-md">
                Informasi & Kontak Bantuan
            </h2>
            <p class="text-body-md text-muted">
                Mempunyai pertanyaan atau kendala seputar pendaftaran antrean? Tim helpdesk kami siap melayani Anda.
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- Left Column: Operating Hours -->
            <div class="lg:col-span-5 bg-surface-soft border border-hairline rounded-xl p-8 flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-primary/10 text-primary rounded-lg">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-ink text-title-md">Jam Operasional</h3>
                            <p class="text-body-sm text-muted">Waktu pelayanan loket fisik MPP</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-hairline-soft">
                            <span class="font-display font-semibold text-ink text-body-sm">Senin - Kamis</span>
                            <span class="font-mono font-bold text-primary text-body-sm">08.00 - 15.30 WIB</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-hairline-soft">
                            <span class="font-display font-semibold text-ink text-body-sm">Jumat</span>
                            <span class="font-mono font-bold text-primary text-body-sm">08.00 - 16.00 WIB</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="font-display font-semibold text-muted text-body-sm">Sabtu - Minggu</span>
                            <span class="font-display font-semibold text-muted-soft text-body-sm">Tutup</span>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-hairline-soft/80 mt-6 lg:mt-0 text-caption text-muted flex items-start gap-2">
                    <svg class="h-5 w-5 text-accent-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pengambilan antrean online dapat dilakukan 24 jam. Namun verifikasi tiket di lokasi hanya dilayani selama jam kerja di atas.</span>
                </div>
            </div>
            
            <!-- Right Column: Location & Contact Cards -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-8">
                <!-- Location Info -->
                <div class="bg-canvas border border-hairline rounded-xl p-8 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-ink text-title-sm">Alamat Fisik</h3>
                            <p class="text-body-sm text-body leading-relaxed mt-2">
                                Gedung Mal Pelayanan Publik (MPP) Kota Sawahlunto <br>
                                Jl. Jenderal Sudirman No. 1, Kota Sawahlunto, Sumatera Barat, 27411
                            </p>
                        </div>
                    </div>
                    
                    <a href="https://maps.google.com" target="_blank" class="inline-flex items-center text-primary font-display font-semibold text-body-sm hover:underline mt-4">
                        Lihat Rute di Google Maps
                        <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
                
                <!-- Contact Info -->
                <div class="bg-canvas border border-hairline rounded-xl p-8 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-ink text-title-sm">Kontak Pengaduan</h3>
                            <p class="text-body-sm text-body leading-relaxed mt-2">
                                Hubungi nomor pelayanan kami jika Anda mendapati kendala sistem atau ketidaksesuaian antrean.
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mt-4">
                        <a href="https://wa.me/628112345678" target="_blank" class="w-full inline-flex items-center justify-center bg-[#25D366] hover:bg-[#20ba5a] text-white font-display font-semibold text-caption py-2.5 px-4 rounded-md transition-colors gap-2">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.46h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/>
                            </svg>
                            WhatsApp Helpdesk
                        </a>
                        <a href="mailto:mpp@sawahluntokota.go.id" class="w-full inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-caption py-2.5 px-4 rounded-md transition-colors gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Kirim Email Resmi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
