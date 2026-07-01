{{-- QR Scanner Modal — DI DALAM scope x-data --}}
<div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    x-show="qrScannerOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak>

    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-6 max-w-lg w-full border border-hairline dark:border-white/10 shadow-2xl transform transition-all duration-300 relative"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0">

        {{-- Modal Header --}}
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display">Scan QR Code Booking</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Arahkan QR Code tiket warga ke kamera untuk memindai.</p>
            </div>
            <button type="button" @click="closeQrScanner()"
                class="ml-4 shrink-0 text-muted hover:text-ink dark:hover:text-white p-1.5 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Camera Selector --}}
        <div x-show="scanState === 'idle' || scanState === 'scanning'" class="mb-3">
            <label class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-1.5 font-display">Pilih Kamera</label>
            <div class="flex gap-2 items-center">
                {{-- Loading state --}}
                <div x-show="availableCameras.length === 0 && !cameraLoadError"
                    class="flex-1 h-9 flex items-center gap-2 px-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-md text-xs text-muted">
                    <svg class="animate-spin w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mendeteksi kamera...
                </div>

                {{-- Error loading cameras --}}
                <div x-show="cameraLoadError !== ''"
                    class="flex-1 h-9 flex items-center gap-2 px-3 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/40 rounded-md text-xs text-red-600 dark:text-red-400">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span x-text="cameraLoadError" class="truncate"></span>
                </div>

                {{-- Camera dropdown --}}
                <select x-show="availableCameras.length > 0"
                    x-model="selectedCameraId"
                    @change="switchCamera()"
                    class="flex-1 h-9 text-xs bg-surface-soft dark:bg-surface-dark border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-teal cursor-pointer">
                    <template x-for="cam in availableCameras" :key="cam.id">
                        <option :value="cam.id" x-text="cam.label || ('Kamera ' + (availableCameras.indexOf(cam) + 1))"></option>
                    </template>
                </select>

                {{-- Refresh camera list button --}}
                <button type="button" @click="loadAvailableCameras($data)"
                    title="Muat ulang daftar kamera"
                    class="h-9 w-9 shrink-0 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-md transition-colors cursor-pointer">
                    <svg class="w-4 h-4 text-muted dark:text-on-dark-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17" />
                    </svg>
                </button>
            </div>
            <p x-show="availableCameras.length > 1" class="text-[10px] text-muted dark:text-on-dark-soft mt-1 font-body">
                Terdeteksi <span class="font-bold" x-text="availableCameras.length"></span> kamera. Pilih kamera yang sesuai.
            </p>
        </div>

        {{-- Idle / Initializing State --}}
        <div x-show="scanState === 'idle'" class="border border-hairline dark:border-white/10 rounded-lg overflow-hidden bg-surface-soft dark:bg-white/5 flex flex-col items-center justify-center text-center" style="height:300px">
            <svg class="animate-spin h-10 w-10 text-primary dark:text-accent-teal mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-semibold text-ink dark:text-white">Menginisialisasi Kamera...</p>
            <p class="text-xs text-muted mt-1">Pastikan Anda mengizinkan akses kamera di browser.</p>
        </div>

        {{-- Camera Preview UI --}}
        <div x-show="scanState === 'scanning'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="rounded-lg overflow-hidden bg-black w-full relative"
             style="height: 300px;"
             id="qr-preview-wrapper">

            {{-- Gunakan style inline agar dimensi pasti ter-resolve --}}
            <div id="qr-reader" style="width: 100%; height: 100%;"></div>

            {{-- Corner-bracket scanning overlay --}}
            <div class="absolute inset-0 pointer-events-none z-10 flex items-center justify-center">
                <div class="qr-scan-box">
                    <span class="corner tl"></span>
                    <span class="corner tr"></span>
                    <span class="corner bl"></span>
                    <span class="corner br"></span>
                    <div class="scan-laser"></div>
                </div>
                <div class="qr-scan-vignette"></div>
            </div>

            {{-- Camera label badge --}}
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10 pointer-events-none">
                <span class="text-[10px] bg-black/60 text-white px-2 py-0.5 rounded-full font-mono"
                      x-text="availableCameras.find(c => c.id === selectedCameraId)?.label || 'Kamera Aktif'"></span>
            </div>
        </div>

        {{-- Processing State: Memverifikasi QR Code --}}
        <div x-show="scanState === 'processing'" class="border border-hairline dark:border-white/10 rounded-lg overflow-hidden bg-surface-soft dark:bg-white/5 flex flex-col items-center justify-center text-center" style="height:300px">
            <svg class="animate-spin h-10 w-10 text-primary dark:text-accent-teal mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-semibold text-ink dark:text-white">Memverifikasi QR Code...</p>
            <p class="text-xs text-muted mt-1">Sedang mencari data booking warga di server...</p>
        </div>

        {{-- Verifying State: Mengeksekusi Check-In --}}
        <div x-show="scanState === 'verifying'" class="border border-hairline dark:border-white/10 rounded-lg overflow-hidden bg-surface-soft dark:bg-white/5 flex flex-col items-center justify-center text-center" style="height:300px">
            <svg class="animate-spin h-10 w-10 text-primary dark:text-accent-teal mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-semibold text-ink dark:text-white">Memproses Check-In...</p>
            <p class="text-xs text-muted mt-1">Sedang mencatat kedatangan warga ke database...</p>
        </div>

        {{-- Found State: Rincian Data Booking Ditemukan — Menunggu Konfirmasi --}}
        <div x-show="scanState === 'found'" class="border border-blue-200 dark:border-blue-700/50 bg-blue-50 dark:bg-blue-950/30 rounded-lg p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-blue-700 dark:text-blue-300 font-display">DATA BOOKING DITEMUKAN</h4>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Periksa rincian berikut sebelum melakukan check-in.</p>
                </div>
            </div>
            <template x-if="scanResult">
                <div class="bg-white dark:bg-white/5 border border-blue-100 dark:border-blue-800/40 rounded-md p-4 space-y-2.5 text-xs text-left">
                    <div class="flex justify-between items-center border-b border-blue-100 dark:border-blue-800/30 pb-2.5">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Kode Booking:</span>
                        <span class="font-mono font-black text-blue-600 dark:text-blue-300" x-text="scanResult.booking_code || scannedCode"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Nama Warga:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.user_name"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">NIK:</span>
                        <span class="font-mono text-ink dark:text-white" x-text="scanResult.nik || '-'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Instansi Tujuan:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.department_name"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Layanan / Loket:</span>
                        <span class="font-bold text-primary dark:text-accent-teal" x-text="scanResult.service_name || '-'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Keperluan:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.purpose || '-'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Status Saat Ini:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-bold uppercase text-[10px] tracking-wider" x-text="scanResult.current_status || 'Booked'"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Success Result --}}
        <div x-show="scanState === 'success'" class="border border-status-serving/20 bg-status-serving/10 rounded-lg p-5 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-status-serving text-white rounded-full flex items-center justify-center mb-4 shadow-lg shadow-status-serving/20 animate-bounce">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h4 class="text-lg font-black text-status-serving font-display">CHECK-IN BERHASIL!</h4>
            <p class="text-xs text-green-800 dark:text-green-300 font-body mt-1">Kedatangan warga telah sukses dicatat di database.</p>
            <template x-if="scanResult">
                <div class="w-full mt-4 bg-canvas dark:bg-white/5 border border-status-serving/20 rounded-md p-4 space-y-2 text-xs text-left text-ink dark:text-white">
                    <div class="flex justify-between border-b border-hairline dark:border-white/5 pb-2">
                        <span class="text-muted dark:text-on-dark-soft">Nomor Antrean:</span>
                        <span class="font-mono font-black text-base text-status-serving" x-text="scanResult.queue_number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Nama Warga:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.user_name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Instansi:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.department_name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Layanan / Loket:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.service_name || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Keperluan:</span>
                        <span class="font-bold text-ink dark:text-white" x-text="scanResult.purpose || '-'"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Error Result --}}
        <div x-show="scanState === 'error'" class="border border-status-skipped/20 bg-status-skipped/10 rounded-lg p-5 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-status-skipped text-white rounded-full flex items-center justify-center mb-4 shadow-lg shadow-status-skipped/20 animate-pulse">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h4 class="text-lg font-black text-status-skipped font-display">GAGAL!</h4>
            <p class="text-xs text-red-800 dark:text-red-300 font-body mt-2 font-bold" x-text="scanMessage"></p>
            <p class="text-[11px] text-muted dark:text-on-dark-soft mt-1">Silakan scan ulang QR Code booking tiket yang valid.</p>
        </div>

        {{-- Manual input fallback — sembunyikan saat found/verifying/success --}}
        <div x-show="scanState !== 'processing' && scanState !== 'verifying' && scanState !== 'found' && scanState !== 'success'" class="mt-3">
            <label class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider mb-1 font-display">Atau masukkan kode secara manual</label>
            <div class="flex gap-2">
                <input type="text" id="manualQrInput"
                    placeholder="Paste kode booking di sini..."
                    class="flex-1 h-9 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 text-xs font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-teal">
                <button type="button" onclick="submitManualQrCode()"
                    class="h-9 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-md text-xs transition-all cursor-pointer">
                    Kirim
                </button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 flex justify-end gap-3">

            {{-- Tombol Tutup: hanya saat idle/scanning --}}
            <button type="button" x-show="scanState === 'scanning' || scanState === 'idle'" @click="closeQrScanner()"
                class="h-10 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                Tutup
            </button>

            {{-- Disabled saat processing/verifying --}}
            <button type="button" x-show="scanState === 'processing' || scanState === 'verifying'" disabled
                class="h-10 px-5 bg-surface-soft opacity-50 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 cursor-not-allowed">
                Memproses...
            </button>

            {{-- Tombol Batal & Verifikasi: muncul saat data booking ditemukan (state 'found') --}}
            <div x-show="scanState === 'found'" class="flex gap-2 w-full justify-end">
                <button type="button" @click="resetScanner()"
                    class="h-10 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="button" @click="verifyScannedBooking()"
                    class="h-10 px-6 bg-green-600 hover:bg-green-500 text-white font-bold rounded-pill text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-md shadow-green-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi & Check-In
                </button>
            </div>

            {{-- Tombol Scan Lagi & Tutup: setelah success atau error --}}
            <div x-show="scanState === 'success' || scanState === 'error'" class="flex gap-2 w-full justify-end">
                <button type="button" @click="resetScanner()"
                    class="h-10 px-5 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs transition-all cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17" />
                    </svg>
                    Scan Lagi
                </button>
                <button type="button" @click="closeQrScanner()"
                    class="h-10 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>

    </div>
</div>
