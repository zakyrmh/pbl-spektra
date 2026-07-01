<style>
    /* ── QR Reader container reset ─────────────────────────────────── */
    /*
     * CATATAN DEBUGGING:
     * Semua rule yang sebelumnya menyembunyikan elemen di dalam #qr-reader
     * (display:none pada #qr-reader__dashboard, __header_message, dll.)
     * telah DIHAPUS agar library dapat merender video dan UI-nya secara bebas.
     * Ini penting saat debugging — jangan tambahkan kembali rule hide/none
     * pada child element #qr-reader sampai masalah kamera terselesaikan.
     */
    #qr-reader {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        background: #000 !important;
    }
    /* Pastikan video yang diinjek library mengisi container sepenuhnya */
    #qr-reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        /* display TIDAK di-override — biarkan library yang mengontrol */
    }
    /* Semua child elemen #qr-reader dibiarkan visible untuk debugging */

    /* ── Custom scanning overlay ──────────────────────────────────── */
    .qr-scan-box {
        position: absolute;
        width: 200px;
        height: 200px;
        z-index: 20;
    }
    .qr-scan-box .corner {
        position: absolute;
        width: 22px;
        height: 22px;
        border-color: #14b8a6; /* accent-teal */
        border-style: solid;
    }
    .qr-scan-box .corner.tl { top: 0; left: 0; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
    .qr-scan-box .corner.tr { top: 0; right: 0; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
    .qr-scan-box .corner.bl { bottom: 0; left: 0; border-width: 0 0 3px 3px; border-radius: 0 0 4px 0; }
    .qr-scan-box .corner.br { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

    .qr-scan-box .scan-laser {
        position: absolute;
        left: 4px;
        right: 4px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #14b8a6, transparent);
        box-shadow: 0 0 6px 1px rgba(20, 184, 166, 0.7);
        animation: scan-laser-move 2.4s ease-in-out infinite;
        top: 0;
    }
    @keyframes scan-laser-move {
        0%   { top: 4px;   opacity: 0.9; }
        50%  { top: calc(100% - 6px); opacity: 1; }
        100% { top: 4px;   opacity: 0.9; }
    }

    /* Dark vignette around the scan box */
    .qr-scan-vignette {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 210px 210px at center, transparent 48%, rgba(0,0,0,0.55) 49%);
        pointer-events: none;
        z-index: 15;
    }
</style>
