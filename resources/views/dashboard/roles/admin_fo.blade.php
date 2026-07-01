{{-- Admin FO Dashboard --}}
<div class="space-y-6 pb-16" x-data="{
    modalOpen: false,
    actionUrl: '',
    bookingCode: '',
    userName: '',
    serviceName: '',
    reason: '',
    qrScannerOpen: false,
    scanState: 'idle', // idle, scanning, processing, found, verifying, success, error
    scanMessage: '',
    scanResult: null,
    scannedCode: '',
    availableCameras: [],
    selectedCameraId: '',
    cameraLoadError: '',
    openCancelModal(url, code, name, service) {
        console.log('openCancelModal called:', { url, code, name, service });
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

        // Kirim this (alpine instance) ke loadAvailableCameras
        await loadAvailableCameras(this);
    },
    closeQrScanner() {
        this.qrScannerOpen = false;
        this.scanState = 'idle';
        stopQrScannerBackend();
    },
    async switchCamera() {
        await stopQrScannerBackend();
        this.scanState = 'scanning';
        this.scanMessage = '';
        this.scanResult = null;

        await this.$nextTick();

        // Delay untuk memastikan elemen visible sebelum init
        setTimeout(() => {
            startQrScannerBackend(this.selectedCameraId, this);
        }, 200);
    },
    resetScanner() {
        this.scanState = 'idle';
        this.scanMessage = '';
        this.scanResult = null;
        this.scannedCode = '';
        this.cameraLoadError = '';

        this.$nextTick(() => {
            loadAvailableCameras(this);
        });
    },
    async verifyScannedBooking() {
        if (!this.scannedCode) return;
        this.scanState = 'verifying';
        this.scanMessage = '';
        await executeCheckIn(this.scannedCode, this);
    }
}">
    <!-- Header Banner -->
    @include('dashboard.roles.admin_fo.header')

    <!-- Metrik Ringkas FO -->
    @include('dashboard.roles.admin_fo.stats')

    <!-- Main Working Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Fast-Track Verification Module -->
        @include('dashboard.roles.admin_fo.verification-panel')

        <!-- Right: Kios Cetak Tiket Mandiri -->
        @include('dashboard.roles.admin_fo.kiosk-panel')
    </div>

    <!-- Bottom: Live Feed / Table (Recent Check-Ins) -->
    @include('dashboard.roles.admin_fo.live-feed')

    <!-- Cancellation Modal Overlay -->
    @include('dashboard.roles.admin_fo.cancel-modal')

    <!-- QR Scanner Modal -->
    @include('dashboard.roles.admin_fo.qr-modal')

</div>{{-- ← Tutup div utama x-data --}}

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
</div>

<!-- JavaScript Logic for FO -->
@include('dashboard.roles.admin_fo.scripts')

<!-- Custom CSS styles -->
@include('dashboard.roles.admin_fo.styles')
