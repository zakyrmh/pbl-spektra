<!-- Left Column: Fast-Track Verification Station (4 cols) -->
<div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between h-full space-y-4">
    <div class="space-y-3">
        <div class="flex items-center gap-2 pb-3 border-b border-hairline dark:border-white/10">
            <div class="p-1.5 bg-primary/10 text-primary dark:text-accent-teal rounded-md">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-ink dark:text-white font-display text-sm">Verifikasi & Check-In Booking</h3>
                <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Input Kode Booking / NIK warga kedatangan online</p>
            </div>
        </div>

        <!-- Direct Verification Form -->
        <div class="space-y-3 pt-1">
            <div>
                <label for="txtBookingCode" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider mb-1.5 font-display">
                    Kode Booking / NIK Warga
                </label>
                <div class="flex gap-2">
                    <input type="text" id="txtBookingCode" x-ref="bookingCodeField" placeholder="Kode UUID atau NIK 16 digit..."
                        class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-mono font-bold text-xs placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                    <button type="button" onclick="verifyBookingCode()"
                        class="h-11 px-4 bg-primary hover:bg-primary-hover text-on-primary font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shrink-0">
                        Verifikasi
                    </button>
                </div>
            </div>

            <div class="relative flex items-center py-1">
                <div class="grow border-t border-hairline dark:border-white/10"></div>
                <span class="shrink mx-3 text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Atau Scan QR Code</span>
                <div class="grow border-t border-hairline dark:border-white/10"></div>
            </div>

            <button type="button" @click="openQrScanner()"
                class="w-full h-11 flex items-center justify-center gap-2 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-md text-xs border border-hairline dark:border-white/10 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Buka Kamera Scan QR</span>
            </button>
        </div>
    </div>

    <!-- Citizen Verification Result Container (Dynamic Output) -->
    <div id="pnlVerifyDetails" class="hidden mt-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-lg p-4 space-y-3 transition-all">
        <div class="flex items-center justify-between pb-2 border-b border-hairline dark:border-white/10">
            <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Hasil Deteksi Booking</span>
            <span class="text-[11px] font-mono font-bold text-primary dark:text-accent-teal bg-primary/10 dark:bg-accent-teal/10 px-2 py-0.5 rounded" id="verifyTicketCode">---</span>
        </div>
        <div class="space-y-1.5 text-xs font-body">
            <div class="flex justify-between gap-2">
                <span class="text-muted dark:text-on-dark-soft">Nama Warga:</span>
                <span class="font-bold text-ink dark:text-white text-right" id="verifyName">-</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-muted dark:text-on-dark-soft">NIK Warga:</span>
                <span class="font-mono font-bold text-ink dark:text-white text-right" id="verifyNik">-</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-muted dark:text-on-dark-soft">Instansi:</span>
                <span class="font-bold text-ink dark:text-white text-right" id="verifyTenant">-</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-muted dark:text-on-dark-soft">Layanan:</span>
                <span class="font-bold text-primary dark:text-accent-teal text-right" id="verifyService">-</span>
            </div>
        </div>
        <button type="button" onclick="confirmCheckIn()"
            class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer shadow-md mt-2">
            Konfirmasi Kedatangan (Check-In)
        </button>
    </div>
</div>
