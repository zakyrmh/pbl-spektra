{{-- Admin FO Dashboard --}}
<div class="space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <span class="text-xs font-semibold text-primary dark:text-accent-teal uppercase tracking-wider font-display">Front Office Active</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Dashboard Admin Front Office</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Layanan verifikasi kedatangan online dan pencetakan tiket mandiri warga (walk-in).</p>
        </div>
        <div class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md" id="fo-live-clock">
            Loading waktu...
        </div>
    </div>

    <!-- Metrik Ringkas FO -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Card 1: Antrean FO Saat Ini -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Antrean FO Saat Ini</p>
                    <h3 id="foStatAntrean" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono">18</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Warga di ruang tunggu loket depan</p>
                </div>
                <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg border border-status-waiting/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Tiket Dicetak -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Tiket Dicetak Hari Ini</p>
                    <h3 id="foStatTiket" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono">142</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Gabungan online check-in + walk-in</p>
                </div>
                <div class="p-3 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Working Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Fast-Track Verification Module (Spans 5 cols) -->
        <div class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-hairline dark:border-white/10">
                    <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="font-bold text-ink dark:text-white font-display">Verifikasi Cepat Booking</h3>
                </div>
                
                <p class="text-xs text-muted dark:text-on-dark-soft leading-relaxed font-body">
                    Masukkan kode booking online warga (tertera di tiket PDF atau WA) untuk check-in kedatangan mereka secara langsung.
                </p>

                <div class="space-y-3 pt-2">
                    <div>
                        <label for="txtBookingCode" class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Kode Booking</label>
                        <div class="flex gap-2">
                            <input type="text" id="txtBookingCode" placeholder="Contoh: A-015" class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                            <button type="button" onclick="verifyBookingCode()" class="h-11 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                Verifikasi
                            </button>
                        </div>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-hairline dark:border-white/10"></div>
                        <span class="flex-shrink mx-4 text-xs font-semibold text-muted dark:text-on-dark-soft uppercase font-display">Atau</span>
                        <div class="flex-grow border-t border-hairline dark:border-white/10"></div>
                    </div>

                    <button type="button" onclick="simulateQrScanner()" class="w-full h-11 flex items-center justify-center gap-2 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-md text-xs border border-hairline dark:border-white/10 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Simulasi Scan QR Scanner
                    </button>
                </div>
            </div>

            <!-- Citizen verification details panel (Hidden by default, shown dynamically) -->
            <div id="pnlVerifyDetails" class="hidden mt-6 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-md p-4 space-y-4 transition-all">
                <div class="flex items-center justify-between pb-2 border-b border-hairline dark:border-white/10">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Hasil Deteksi Tiket</span>
                    <span class="text-xs bg-status-waiting/20 text-status-waiting px-2 py-0.5 rounded-full font-bold uppercase tracking-wider font-display" id="verifyTicketCode">A-015</span>
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
                        <span class="font-bold text-primary dark:text-accent-teal" id="verifyService">Cetak KTP-el</span>
                    </div>
                </div>
                <button type="button" onclick="confirmCheckIn()" class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer">
                    Konfirmasi Kedatangan (Check-In)
                </button>
            </div>
        </div>

        <!-- Right: Kios Cetak Tiket Mandiri (Spans 7 cols) -->
        <div class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
            <div class="flex items-center gap-2 pb-4 border-b border-hairline dark:border-white/10 mb-4">
                <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <h3 class="font-bold text-ink dark:text-white font-display">Kios Cetak Tiket Mandiri (Walk-In)</h3>
            </div>
            
            <p class="text-xs text-muted dark:text-on-dark-soft mb-4 font-body">Klik salah satu Instansi, pilih jenis layanan, kemudian cetak tiket antrean langsung untuk warga.</p>

            <!-- Instansi Selection Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="kioskInstansiGrid">
                <button type="button" onclick="selectKioskTenant('Dukcapil', ['Cetak KTP-el', 'Pembuatan KK Baru', 'Keterangan Domisili'])" class="kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-xs">DK</div>
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Dukcapil</span>
                </button>
                <button type="button" onclick="selectKioskTenant('Samsat', ['Pajak Tahunan Motor', 'Mutasi Kendaraan', 'Cetak STNK Baru'])" class="kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 flex items-center justify-center font-bold text-xs">SM</div>
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Samsat</span>
                </button>
                <button type="button" onclick="selectKioskTenant('Imigrasi', ['Pembuatan Paspor Baru', 'Perpanjang Paspor', 'Izin Tinggal Kitas'])" class="kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">IM</div>
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Imigrasi</span>
                </button>
                <button type="button" onclick="selectKioskTenant('BPJS Kesehatan', ['Pendaftaran PPU', 'Perubahan Data PBI', 'Pengaduan Layanan'])" class="kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 flex items-center justify-center font-bold text-xs">BP</div>
                    <span class="text-xs font-bold text-ink dark:text-white font-display">BPJS Kesehatan</span>
                </button>
                <button type="button" onclick="selectKioskTenant('Bapenda', ['Bayar PBB', 'Cetak Surat Ketetapan Pajak', 'Konsultasi Pajak Daerah'])" class="kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold text-xs">BP</div>
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Bapenda</span>
                </button>
            </div>

            <!-- Service Selection (Hidden by default, shown when Tenant selected) -->
            <div id="kioskServiceBlock" class="hidden mt-6 space-y-4 border-t border-hairline dark:border-white/10 pt-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Instansi: <span id="kioskSelectedTenantText" class="text-primary dark:text-accent-teal">Disdukcapil</span></span>
                    <button type="button" onclick="resetKiosk()" class="text-xs text-status-skipped hover:underline font-semibold focus-visible:outline-none">Pilih Ulang Instansi</button>
                </div>
                <div>
                    <label for="selKioskService" class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Pilih Layanan</label>
                    <select id="selKioskService" class="w-full h-11 bg-surface-soft dark:bg-surface-dark border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                        <!-- Filled dynamically -->
                    </select>
                </div>
                <button type="button" onclick="printWalkInTicket()" class="w-full h-11 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shadow-md">
                    Cetak Tiket Mandiri
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom: Live Feed / Table (Recent Check-Ins) -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-hairline dark:border-white/10">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Daftar Kedatangan Terkini</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Daftar warga yang baru saja check-in FO atau cetak tiket hari ini.</p>
            </div>
            <span class="bg-primary/10 text-primary dark:text-accent-teal text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse">
                Live Feed
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                        <th class="py-3 px-6">Nama Warga</th>
                        <th class="py-3 px-4">Kode Tiket</th>
                        <th class="py-3 px-4">Instansi Tujuan</th>
                        <th class="py-3 px-4">Jenis Kedatangan</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-6">Status</th>
                    </tr>
                </thead>
                <tbody id="foLiveFeedBody" class="text-xs divide-y divide-hairline dark:divide-white/5">
                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-6 font-bold text-ink dark:text-white">Supardi Wijaya</td>
                        <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">A-014</td>
                        <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">Dukcapil</td>
                        <td class="py-3 px-4 text-muted dark:text-on-dark-soft">Online Booking</td>
                        <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">12:00</td>
                        <td class="py-3 px-6">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold border border-green-200/50">
                                <span class="w-1 h-1 rounded-full bg-green-500"></span>Check-In FO
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-6 font-bold text-ink dark:text-white">Anita Rahman</td>
                        <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">W-101</td>
                        <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">Samsat</td>
                        <td class="py-3 px-4 text-muted dark:text-on-dark-soft">Walk-In (Tiket Mandiri)</td>
                        <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">11:58</td>
                        <td class="py-3 px-6">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold border border-green-200/50">
                                <span class="w-1 h-1 rounded-full bg-green-500"></span>Cetak Kios
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-6 font-bold text-ink dark:text-white">M. Fadhil</td>
                        <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">A-013</td>
                        <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">Imigrasi</td>
                        <td class="py-3 px-4 text-muted dark:text-on-dark-soft">Online Booking</td>
                        <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">11:55</td>
                        <td class="py-3 px-6">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold border border-green-200/50">
                                <span class="w-1 h-1 rounded-full bg-green-500"></span>Check-In FO
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

<!-- JavaScript Simulation Logic for FO -->
<script>
    // Stats state
    let foStats = {
        antreanFO: 18,
        tiketDicetak: 142
    };

    // Live clock
    function updateClock() {
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
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
    function verifyBookingCode() {
        const input = document.getElementById('txtBookingCode');
        const code = input.value.trim().toUpperCase();
        
        if (code === '') {
            alert("Silakan masukkan kode booking terlebih dahulu!");
            return;
        }

        // Mock booking details database
        const db = {
            'A-015': { name: 'Rahmat Hidayat', nik: '1373021408990002', tenant: 'Disdukcapil', service: 'Cetak KTP-el' },
            'B-490': { name: 'Eko Sulistyo', nik: '1373010502930005', tenant: 'Samsat', service: 'Pajak Tahunan Motor' },
            'C-210': { name: 'Siti Aminah', nik: '1373034907950001', tenant: 'Imigrasi', service: 'Pembuatan Paspor Baru' }
        };

        const result = db[code] || { name: 'Pengunjung Umum', nik: '1373' + Math.floor(Math.random() * 900000) + 'xxxx', tenant: 'Disdukcapil', service: 'Konsultasi Layanan' };

        document.getElementById('verifyTicketCode').innerText = code;
        document.getElementById('verifyName').innerText = result.name;
        document.getElementById('verifyNik').innerText = result.nik;
        document.getElementById('verifyTenant').innerText = result.tenant;
        document.getElementById('verifyService').innerText = result.service;

        const pnl = document.getElementById('pnlVerifyDetails');
        pnl.classList.remove('hidden');
        
        createToast('Tiket Ditemukan', `Kode booking ${code} berhasil diverifikasi. Silakan klik Konfirmasi.`, 'info');
    }

    function simulateQrScanner() {
        const codes = ['A-015', 'B-490', 'C-210'];
        const randomCode = codes[Math.floor(Math.random() * codes.length)];
        document.getElementById('txtBookingCode').value = randomCode;
        verifyBookingCode();
    }

    function confirmCheckIn() {
        const code = document.getElementById('verifyTicketCode').innerText;
        const name = document.getElementById('verifyName').innerText;
        const tenant = document.getElementById('verifyTenant').innerText;

        // Modify stats
        if (foStats.antreanFO > 0) foStats.antreanFO--;
        foStats.tiketDicetak++;

        document.getElementById('foStatAntrean').innerText = foStats.antreanFO;
        document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

        // Hide details panel
        document.getElementById('pnlVerifyDetails').classList.add('hidden');
        document.getElementById('txtBookingCode').value = '';

        // Add to live feed
        addLiveFeedRow(name, code, tenant, 'Online Booking', 'Check-In FO');
        
        createToast('Check-In Sukses', `Warga ${name} (${code}) telah check-in untuk loket ${tenant}.`, 'success');
    }

    // Kiosk Module
    let kioskSelectedTenant = '';
    function selectKioskTenant(tenantName, services) {
        kioskSelectedTenant = tenantName;
        document.getElementById('kioskSelectedTenantText').innerText = tenantName;
        
        const select = document.getElementById('selKioskService');
        select.innerHTML = '';
        services.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s;
            opt.innerText = s;
            select.appendChild(opt);
        });

        // Toggle visibility
        document.getElementById('kioskServiceBlock').classList.remove('hidden');
        
        // Highlight active button
        document.querySelectorAll('.kiosk-tenant-btn').forEach(btn => {
            const span = btn.querySelector('span');
            if (span.innerText === tenantName) {
                btn.className = 'kiosk-tenant-btn p-4 bg-primary/10 border border-primary text-primary rounded-lg flex flex-col items-center text-center gap-2 transition-all';
            } else {
                btn.className = 'kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all cursor-pointer';
            }
        });
    }

    function resetKiosk() {
        document.getElementById('kioskServiceBlock').classList.add('hidden');
        document.querySelectorAll('.kiosk-tenant-btn').forEach(btn => {
            btn.className = 'kiosk-tenant-btn p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg flex flex-col items-center text-center gap-2 hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all cursor-pointer';
        });
        kioskSelectedTenant = '';
    }

    function printWalkInTicket() {
        if (!kioskSelectedTenant) return;
        
        const service = document.getElementById('selKioskService').value;
        const ticketNum = 'W-' + Math.floor(Math.random() * 900 + 100);

        foStats.tiketDicetak++;
        document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

        // Add to live feed
        const names = ['Ahmad Syarif', 'Budi Santoso', 'Laila Sari', 'Megawati', 'Roni Wijaya'];
        const randomName = names[Math.floor(Math.random() * names.length)];
        
        addLiveFeedRow(randomName, ticketNum, kioskSelectedTenant, 'Walk-In (Tiket Mandiri)', 'Cetak Kios');
        
        createToast('Tiket Dicetak', `Tiket ${ticketNum} berhasil dicetak untuk ${randomName} tujuan ${kioskSelectedTenant}.`, 'success');
        resetKiosk();
    }

    // Helper functions
    function addLiveFeedRow(name, code, tenant, type, status) {
        const tbody = document.getElementById('foLiveFeedBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors';
        
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

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
            iconHtml = `<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            borderClr = 'border-l-4 border-amber-500';
            iconHtml = `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            borderClr = 'border-l-4 border-blue-500';
            iconHtml = `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        toast.className = `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
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
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 50);

        // Auto remove after 4s
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-[-10px]');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }
</script>
