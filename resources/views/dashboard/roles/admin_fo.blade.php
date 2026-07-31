{{-- Admin FO Dashboard Command Center --}}
<div class="max-w-full 2xl:max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-16" x-data="{
    // Drawer & Modal States
    ticketDrawerOpen: false,
    checkinModalOpen: false,
    qrScannerOpen: false,
    cancelModalOpen: false,

    // Modal data for cancellation
    actionUrl: '',
    bookingCode: '',
    userName: '',
    serviceName: '',
    reason: '',

    // Scanner state
    scanState: 'idle', // idle, scanning, processing, found, verifying, success, error
    scanMessage: '',
    scanResult: null,
    scannedCode: '',
    availableCameras: [],
    selectedCameraId: '',
    cameraLoadError: '',

    // Methods
    init() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('action') === 'ticket-create') {
            this.openTicketDrawer();
        } else if (params.get('action') === 'check-in') {
            this.openCheckinModal();
        }
    },
    openTicketDrawer() {
        this.ticketDrawerOpen = true;
        this.$nextTick(() => {
            if (this.$refs.nikField) this.$refs.nikField.focus();
        });
    },
    closeTicketDrawer() {
        this.ticketDrawerOpen = false;
    },
    openCheckinModal() {
        this.openQrScanner();
    },
    closeCheckinModal() {
        this.closeQrScanner();
    },
    openCancelModal(url, code, name, service) {
        this.actionUrl = url;
        this.bookingCode = code;
        this.userName = name;
        this.serviceName = service;
        this.reason = '';
        this.modalOpen = true;
    },
    async openQrScanner() {
        this.scanState = 'idle';
        this.scanMessage = '';
        this.scanResult = null;
        this.cameraLoadError = '';
        this.availableCameras = [];
        this.selectedCameraId = '';
        this.qrScannerOpen = true;

        await this.$nextTick();
        if (typeof loadAvailableCameras === 'function') {
            await loadAvailableCameras(this);
        }
    },
    closeQrScanner() {
        this.qrScannerOpen = false;
        this.scanState = 'idle';
        if (typeof stopQrScannerBackend === 'function') {
            stopQrScannerBackend();
        }
    },
    async switchCamera() {
        if (typeof stopQrScannerBackend === 'function') {
            await stopQrScannerBackend();
        }
        this.scanState = 'scanning';
        this.scanMessage = '';
        this.scanResult = null;

        await this.$nextTick();
        setTimeout(() => {
            if (typeof startQrScannerBackend === 'function') {
                startQrScannerBackend(this.selectedCameraId, this);
            }
        }, 200);
    },
    resetScanner() {
        this.scanState = 'idle';
        this.scanMessage = '';
        this.scanResult = null;
        this.scannedCode = '';
        this.cameraLoadError = '';

        this.$nextTick(() => {
            if (typeof loadAvailableCameras === 'function') {
                loadAvailableCameras(this);
            }
        });
    },
    async verifyScannedBooking() {
        if (!this.scannedCode) return;
        this.scanState = 'verifying';
        this.scanMessage = '';
        if (typeof executeCheckIn === 'function') {
            await executeCheckIn(this.scannedCode, this);
        }
    },

    // Global keyboard shortcuts
    handleGlobalShortcuts(e) {
        if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            this.openTicketDrawer();
        } else if (e.altKey && (e.key === 'c' || e.key === 'C')) {
            e.preventDefault();
            this.openCheckinModal();
        } else if (e.key === 'Escape') {
            this.closeTicketDrawer();
            this.closeQrScanner();
            this.modalOpen = false;
        }
    }
}" @keydown.window="handleGlobalShortcuts($event)">

    <!-- Header Banner -->
    @include('dashboard.roles.admin_fo.header')

    <!-- Metrik Ringkas FO -->
    @include('dashboard.roles.admin_fo.stats')

    <!-- Main Widescreen 3-Column Command Center Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Col 1: Fast Verification Station (4 cols) -->
        <div class="lg:col-span-4 h-full">
            @include('dashboard.roles.admin_fo.verification-panel')
        </div>

        <!-- Col 2: Live Queue Monitor Widget (5 cols) -->
        <div class="lg:col-span-5 h-full">
            @include('dashboard.roles.admin_fo.live-monitor-widget')
        </div>

        <!-- Col 3: Quick Action Sidebar (3 cols) -->
        <div class="lg:col-span-3 h-full">
            <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4 flex flex-col justify-between h-full">
                <div class="space-y-2">
                    <div class="flex items-center justify-between pb-2 border-b border-hairline dark:border-white/10">
                        <span class="text-xs font-extrabold text-ink dark:text-white uppercase font-display tracking-wider">Aksi Cepat FO</span>
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-ping"></span>
                    </div>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed">
                        Sistem terpadu untuk 4 petugas FO. Gunakan tombol di bawah atau shortcut keyboard untuk kecepatan tinggi.
                    </p>
                </div>

                <div class="space-y-2.5 pt-2">
                    <button type="button" @click="openTicketDrawer()"
                        class="w-full h-11 bg-primary hover:bg-primary-hover text-on-primary font-semibold rounded-pill text-xs flex items-center justify-center gap-2 cursor-pointer shadow-md transition-all active:scale-95">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                        <span>Cetak Karcis Walk-In (Alt+N)</span>
                    </button>

                    <button type="button" @click="openCheckinModal()"
                        class="w-full h-11 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 flex items-center justify-center gap-2 cursor-pointer transition-all active:scale-95">
                        <svg class="w-4.5 h-4.5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        <span>Scan QR Check-In (Alt+C)</span>
                    </button>
                </div>

                <div class="p-3 bg-surface-soft/60 dark:bg-white/5 rounded-lg border border-hairline dark:border-white/10 text-[11px] text-muted dark:text-on-dark-soft space-y-1">
                    <span class="font-bold text-ink dark:text-white block font-display">Petunjuk Hotkeys:</span>
                    <p>· <kbd class="font-mono text-ink dark:text-white font-bold">Alt + N</kbd> : Cetak karcis baru</p>
                    <p>· <kbd class="font-mono text-ink dark:text-white font-bold">Alt + C</kbd> : Verifikasi & check-in</p>
                    <p>· <kbd class="font-mono text-ink dark:text-white font-bold">Esc</kbd> : Tutup modal/drawer</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom: Live Feed / Activity Stream Table -->
    @include('dashboard.roles.admin_fo.live-feed')

    <!-- Slide-Over Drawer: Kios Cetak Tiket Mandiri (Walk-In) -->
    @include('dashboard.roles.admin_fo.kiosk-panel')

    <!-- QR Scanner Modal -->
    @include('dashboard.roles.admin_fo.qr-modal')

    <!-- Cancellation Modal Overlay -->
    @include('dashboard.roles.admin_fo.cancel-modal')

</div>

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
</div>

<!-- JavaScript Logic for FO -->
@include('dashboard.roles.admin_fo.scripts')

<!-- Custom CSS styles -->
@include('dashboard.roles.admin_fo.styles')
