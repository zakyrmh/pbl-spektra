{{-- Admin Gerai Dashboard --}}
<div class="space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50" id="loketStatusBadge">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500" id="loketStatusDot"></span>
                    <span id="loketStatusText">Loket Buka (Aktif)</span>
                </span>
                <span class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Loket #03 — Dinas Kependudukan & Pencatatan Sipil</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-2 font-display">Papan Panggil & Layanan Gerai</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Panggil nomor antrean, verifikasi berkas fisik, dan selesaikan pelayanan warga.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Status Loket:</span>
            <div class="inline-flex rounded-lg border border-hairline dark:border-white/10 p-1 bg-surface-soft dark:bg-white/5">
                <button type="button" onclick="setLoketStatus('buka')" id="btnStatusBuka" class="px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs focus-visible:outline-none cursor-pointer">Buka</button>
                <button type="button" onclick="setLoketStatus('istirahat')" id="btnStatusIstirahat" class="px-3 py-1.5 text-xs font-bold rounded-md transition-all text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5 focus-visible:outline-none cursor-pointer">Istirahat</button>
                <button type="button" onclick="setLoketStatus('tutup')" id="btnStatusTutup" class="px-3 py-1.5 text-xs font-bold rounded-md transition-all text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5 focus-visible:outline-none cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Main Call Controls & Active Citizen (Main Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Pusat Kendali Utama Panggilan (Spans 6 cols) -->
        <div class="lg:col-span-6 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="space-y-6">
                <div class="pb-3 border-b border-hairline dark:border-white/10">
                    <h3 class="font-bold text-ink dark:text-white font-display">Pusat Kendali Panggilan</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Gunakan panel ini untuk mengelola antrean loket Anda saat ini.</p>
                </div>

                <!-- Giant Active Queue Display -->
                <div class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                    <span class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Nomor Sedang Dilayani</span>
                    <span id="activeCallNumber" class="text-7xl md:text-8xl font-extrabold text-primary dark:text-accent-teal font-mono tracking-tight leading-none my-2 transition-all">A-010</span>
                    <span id="activeCallStatus" class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary dark:text-accent-teal rounded-full text-xs font-bold border border-primary/20 font-display">
                        Sedang Dilayani
                    </span>
                    <!-- Background watermarked icon -->
                    <div class="absolute right-0 bottom-0 opacity-[0.02] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Call Control Action Buttons -->
            <div class="mt-8 space-y-3">
                <button type="button" onclick="callNextQueue()" id="btnCallNext" class="w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    Panggil Berikutnya (Next Queue)
                </button>
                
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="recallActiveQueue()" id="btnRecall" class="h-11 bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                        Panggil Ulang (Recall)
                    </button>
                    <button type="button" onclick="skipActiveQueue()" id="btnSkip" class="h-11 bg-status-skipped/10 hover:bg-status-skipped/20 text-status-skipped font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50 cursor-pointer flex items-center justify-center gap-1.5 border border-status-skipped/20">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Lewati Antrean (Skip)
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Informasi Pengunjung Aktif & Berkas Checklist (Spans 6 cols) -->
        <div class="lg:col-span-6 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="pb-3 border-b border-hairline dark:border-white/10">
                    <h3 class="font-bold text-ink dark:text-white font-display">Informasi Pengunjung Aktif</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Detail warga yang sedang dilayani pada antrean aktif.</p>
                </div>

                <!-- Citizen Metadata Panel -->
                <div class="space-y-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-4 text-xs">
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-muted dark:text-on-dark-soft">Nama Warga:</span>
                        <span id="citizenName" class="col-span-2 font-bold text-ink dark:text-white text-right">Budi Santoso</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-muted dark:text-on-dark-soft">NIK Warga:</span>
                        <span id="citizenNik" class="col-span-2 font-mono text-ink dark:text-white text-right">1373021508940003</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-muted dark:text-on-dark-soft">Layanan:</span>
                        <span id="citizenService" class="col-span-2 font-bold text-primary dark:text-accent-teal text-right">Cetak Kartu Tanda Penduduk (KTP-el)</span>
                    </div>
                </div>

                <!-- Checklist Berkas Fisik -->
                <div class="space-y-3 pt-2">
                    <span class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider font-display">Verifikasi Berkas Fisik</span>
                    
                    <div class="space-y-2" id="documentChecklist">
                        <label class="flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer">
                            <input type="checkbox" checked class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                            <span class="text-xs text-ink dark:text-white font-medium">Fotokopi Kartu Keluarga (KK)</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer">
                            <input type="checkbox" checked class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                            <span class="text-xs text-ink dark:text-white font-medium">Surat Pengantar RT/RW Keterangan Rusak/Hilang</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer">
                            <input type="checkbox" class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                            <span class="text-xs text-ink dark:text-white font-medium">KTP Lama / Surat Kehilangan Kepolisian</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Complete Service Trigger -->
            <button type="button" onclick="completeActiveService()" id="btnComplete" class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-md text-xs mt-6 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer">
                Selesaikan Pelayanan & Tandai Sukses
            </button>
        </div>
    </div>

    <!-- Metrik Internal Gerai & Delay List -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Internal Stats Widget (Spans 4 cols) -->
        <div class="lg:col-span-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-4">
            <h3 class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2">Metrik Pelayanan Hari Ini</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-center">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Sisa Antrean</span>
                    <p id="geraiStatRemaining" class="text-3xl font-extrabold text-primary dark:text-accent-teal mt-1 font-mono">5</p>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft font-body">orang menunggu</span>
                </div>
                <div class="p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-center">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rerata Layanan</span>
                    <p class="text-3xl font-extrabold text-status-serving mt-1 font-mono">12</p>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft font-body">menit / warga</span>
                </div>
            </div>
            
            <div class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-3 text-xs text-muted dark:text-on-dark-soft leading-relaxed font-body">
                💡 <b>Tips Kecepatan:</b> Mintalah berkas fisik warga sebelum memencet tombol mulai untuk menghemat estimasi waktu pelayanan rata-rata.
            </div>
        </div>

        <!-- Delayed/Skipped List Table (Spans 8 cols) -->
        <div class="lg:col-span-8 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 mb-4">Daftar Antrean Tertunda / Terlewati (Skipped)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                                <th class="py-2.5 px-4">Kode Tiket</th>
                                <th class="py-2.5 px-4">Nama Warga</th>
                                <th class="py-2.5 px-4">Layanan</th>
                                <th class="py-2.5 px-4">Status</th>
                                <th class="py-2.5 px-4 text-center">Aksi Panggil Balik</th>
                            </tr>
                        </thead>
                        <tbody id="geraiSkipListBody" class="text-xs divide-y divide-hairline dark:divide-white/5">
                            <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors" data-skipped-ticket="A-008">
                                <td class="py-3 px-4 font-mono font-bold text-status-skipped">A-008</td>
                                <td class="py-3 px-4 text-ink dark:text-white font-medium">Bambang Hartono</td>
                                <td class="py-3 px-4 text-muted dark:text-on-dark-soft">Cetak KTP-el</td>
                                <td class="py-3 px-4 text-status-skipped font-bold">Terlewat</td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" onclick="recallSkipped('A-008', 'Bambang Hartono', 'Cetak KTP-el')" class="px-3 py-1.5 bg-primary/10 hover:bg-primary text-primary hover:text-white dark:text-accent-teal dark:hover:text-white dark:bg-accent-teal/10 rounded-md font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                        Panggil Balik
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors" data-skipped-ticket="A-005">
                                <td class="py-3 px-4 font-mono font-bold text-status-skipped">A-005</td>
                                <td class="py-3 px-4 text-ink dark:text-white font-medium">Dewi Lestari</td>
                                <td class="py-3 px-4 text-muted dark:text-on-dark-soft">Cetak KTP-el</td>
                                <td class="py-3 px-4 text-status-skipped font-bold">Terlewat</td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" onclick="recallSkipped('A-005', 'Dewi Lestari', 'Cetak KTP-el')" class="px-3 py-1.5 bg-primary/10 hover:bg-primary text-primary hover:text-white dark:text-accent-teal dark:hover:text-white dark:bg-accent-teal/10 rounded-md font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                        Panggil Balik
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sound alerts and Notification Toast Container -->
<audio id="bellChime" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav" preload="auto"></audio>
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

<!-- JavaScript State Control for Gerai -->
<script>
    // Gerai database simulation
    let geraiState = {
        currentNumber: 10,
        remainingQueues: 5,
        status: 'buka',
        queueList: [
            { code: 'A-011', name: 'Ronaldo Saputra', nik: '1373021811980004', service: 'Cetak KTP-el', docs: ['KK', 'Pengantar'] },
            { code: 'A-012', name: 'Lia Herlina', nik: '1373014512960002', service: 'Cetak KTP-el', docs: ['KK', 'Pengantar', 'KTP Lama'] },
            { code: 'A-013', name: 'Novianti', nik: '1373026605990001', service: 'Cetak KTP-el', docs: ['KK', 'KTP Lama'] },
            { code: 'A-014', name: 'Arief Rahman', nik: '1373030910970005', service: 'Cetak KTP-el', docs: ['KK'] },
            { code: 'A-015', name: 'Rahmat Hidayat', nik: '1373021408990002', service: 'Cetak KTP-el', docs: ['KK', 'Pengantar'] }
        ]
    };

    // Set Loket status
    function setLoketStatus(newStatus) {
        geraiState.status = newStatus;
        
        const badge = document.getElementById('loketStatusBadge');
        const dot = document.getElementById('loketStatusDot');
        const text = document.getElementById('loketStatusText');

        const btnBuka = document.getElementById('btnStatusBuka');
        const btnIstirahat = document.getElementById('btnStatusIstirahat');
        const btnTutup = document.getElementById('btnStatusTutup');

        // Reset button active classes
        [btnBuka, btnIstirahat, btnTutup].forEach(b => {
            b.className = 'px-3 py-1.5 text-xs font-bold rounded-md transition-all text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5 focus-visible:outline-none cursor-pointer';
        });

        if (newStatus === 'buka') {
            badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50';
            dot.className = 'w-1.5 h-1.5 rounded-full bg-green-500';
            text.innerText = 'Loket Buka (Aktif)';
            btnBuka.className = 'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs focus-visible:outline-none cursor-pointer';
            
            document.getElementById('btnCallNext').removeAttribute('disabled');
            document.getElementById('btnCallNext').className = 'w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-2 shadow-md';
            createToast('Status Loket', 'Loket dibuka kembali. Selamat melayani!', 'success');
        } 
        else if (newStatus === 'istirahat') {
            badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-200/50';
            dot.className = 'w-1.5 h-1.5 rounded-full bg-amber-500';
            text.innerText = 'Loket Sedang Istirahat';
            btnIstirahat.className = 'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-amber-600 dark:text-amber-400 shadow-xs focus-visible:outline-none cursor-pointer';
            
            document.getElementById('btnCallNext').setAttribute('disabled', 'true');
            document.getElementById('btnCallNext').className = 'w-full h-12 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-semibold rounded-pill text-sm transition-all cursor-not-allowed flex items-center justify-center gap-2';
            createToast('Status Loket', 'Loket sedang beristirahat sementara.', 'info');
        } 
        else {
            badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 rounded-full text-xs font-bold border border-rose-200/50';
            dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
            text.innerText = 'Loket Tutup';
            btnTutup.className = 'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-rose-600 dark:text-rose-400 shadow-xs focus-visible:outline-none cursor-pointer';
            
            document.getElementById('btnCallNext').setAttribute('disabled', 'true');
            document.getElementById('btnCallNext').className = 'w-full h-12 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-semibold rounded-pill text-sm transition-all cursor-not-allowed flex items-center justify-center gap-2';
            createToast('Status Loket', 'Loket telah ditutup.', 'warning');
        }
    }

    // Sound chime trigger
    function playBeep() {
        const sound = document.getElementById('bellChime');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(e => console.log('Audio blocked.'));
        }
    }

    // Call controls
    function callNextQueue() {
        if (geraiState.status !== 'buka') {
            alert("Status loket sedang istirahat/tutup! Silakan ubah ke Buka terlebih dahulu.");
            return;
        }

        if (geraiState.queueList.length === 0) {
            createToast('Antrean Kosong', 'Tidak ada antrean tersisa di gerai ini.', 'warning');
            return;
        }

        const citizen = geraiState.queueList.shift();
        geraiState.currentNumber = parseInt(citizen.code.split('-')[1]);
        geraiState.remainingQueues = geraiState.queueList.length;

        // Play chime sound
        playBeep();

        // Update DOM
        document.getElementById('activeCallNumber').innerText = citizen.code;
        document.getElementById('citizenName').innerText = citizen.name;
        document.getElementById('citizenNik').innerText = citizen.nik;
        document.getElementById('citizenService').innerText = citizen.service;
        document.getElementById('geraiStatRemaining').innerText = geraiState.remainingQueues;

        // Reset checklist items dynamically
        resetChecklist(citizen.docs);

        createToast('Panggilan Sukses', `Memanggil nomor ${citizen.code} (${citizen.name}) ke Loket.`, 'success');
    }

    function recallActiveQueue() {
        const activeNum = document.getElementById('activeCallNumber').innerText;
        const activeName = document.getElementById('citizenName').innerText;

        playBeep();
        createToast('Panggilan Ulang', `Mengulang panggilan nomor ${activeNum} (${activeName}).`, 'info');
    }

    function skipActiveQueue() {
        const activeNum = document.getElementById('activeCallNumber').innerText;
        const name = document.getElementById('citizenName').innerText;
        const service = document.getElementById('citizenService').innerText;

        if (activeNum === 'A-010' && geraiState.queueList.length === 5) {
            createToast('Peringatan', 'Nomor default tidak dapat dilewati.', 'warning');
            return;
        }

        // Add active citizen to skipped/delayed list table
        const tbody = document.getElementById('geraiSkipListBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors';
        tr.setAttribute('data-skipped-ticket', activeNum);
        tr.innerHTML = `
            <td class="py-3 px-4 font-mono font-bold text-status-skipped">${activeNum}</td>
            <td class="py-3 px-4 text-ink dark:text-white font-medium">${name}</td>
            <td class="py-3 px-4 text-muted dark:text-on-dark-soft">${service}</td>
            <td class="py-3 px-4 text-status-skipped font-bold">Terlewat</td>
            <td class="py-3 px-4 text-center">
                <button type="button" onclick="recallSkipped('${activeNum}', '${name}', '${service}')" class="px-3 py-1.5 bg-primary/10 hover:bg-primary text-primary hover:text-white dark:text-accent-teal dark:hover:text-white dark:bg-accent-teal/10 rounded-md font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    Panggil Balik
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        createToast('Antrean Dilewati', `Tiket ${activeNum} dilewati dan masuk daftar terlewat.`, 'warning');
        
        // Load default or wait next
        if (geraiState.queueList.length > 0) {
            callNextQueue();
        } else {
            document.getElementById('activeCallNumber').innerText = '-';
            document.getElementById('citizenName').innerText = 'Tidak ada pengunjung';
            document.getElementById('citizenNik').innerText = '-';
            document.getElementById('citizenService').innerText = '-';
        }
    }

    function completeActiveService() {
        const activeNum = document.getElementById('activeCallNumber').innerText;
        const name = document.getElementById('citizenName').innerText;

        if (activeNum === '-') {
            createToast('Gagal', 'Tidak ada pelayanan aktif untuk diselesaikan.', 'warning');
            return;
        }

        createToast('Pelayanan Selesai', `Tiket ${activeNum} (${name}) dinyatakan SUKSES dilayani.`, 'success');

        // Next queue
        if (geraiState.queueList.length > 0) {
            callNextQueue();
        } else {
            document.getElementById('activeCallNumber').innerText = '-';
            document.getElementById('citizenName').innerText = 'Tidak ada pengunjung';
            document.getElementById('citizenNik').innerText = '-';
            document.getElementById('citizenService').innerText = '-';
        }
    }

    function recallSkipped(code, name, service) {
        // Panggil balik: set as active, remove row from table
        playBeep();
        
        document.getElementById('activeCallNumber').innerText = code;
        document.getElementById('citizenName').innerText = name;
        document.getElementById('citizenNik').innerText = '1373' + Math.floor(Math.random() * 900000) + 'xxxx';
        document.getElementById('citizenService').innerText = service;

        const row = document.querySelector(`tr[data-skipped-ticket="${code}"]`);
        if (row) row.remove();

        resetChecklist(['KK', 'Pengantar']);
        createToast('Panggil Balik', `Memanggil kembali warga terlewat: ${code} (${name})`, 'success');
    }

    function resetChecklist(docs = []) {
        const list = document.getElementById('documentChecklist');
        list.innerHTML = '';

        const db = {
            'KK': 'Fotokopi Kartu Keluarga (KK)',
            'Pengantar': 'Surat Pengantar RT/RW Keterangan Rusak/Hilang',
            'KTP Lama': 'KTP Lama / Surat Kehilangan Kepolisian'
        };

        docs.forEach(doc => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer';
            
            const isChecked = Math.random() > 0.3 ? 'checked' : '';
            label.innerHTML = `
                <input type="checkbox" ${isChecked} class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                <span class="text-xs text-ink dark:text-white font-medium">${db[doc] || doc}</span>
            `;
            list.appendChild(label);
        });
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
        
        setTimeout(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 50);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-[-10px]');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }
</script>
