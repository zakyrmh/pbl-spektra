<!-- Left: Fast-Track Verification Module (Spans 5 cols) -->
<div
    class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
    <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-hairline dark:border-white/10">
            <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h3 class="font-bold text-ink dark:text-white font-display">Verifikasi Cepat Booking</h3>
        </div>

        <p class="text-xs text-muted dark:text-on-dark-soft leading-relaxed font-body">
            Masukkan kode booking online warga (tertera di tiket PDF/WA) atau NIK warga untuk check-in kedatangan mereka
            secara langsung.
        </p>

        <div class="space-y-3 pt-2">
            <div>
                <label for="txtBookingCode"
                    class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Kode Booking / NIK Warga</label>
                <div class="flex gap-2">
                    <input type="text" id="txtBookingCode" placeholder="Contoh: 550e8400-e29b-41d4-a716-446655440000 atau NIK 16 digit"
                        class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                    <button type="button" onclick="verifyBookingCode()"
                        class="h-11 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        Verifikasi
                    </button>
                </div>
                <p class="text-[10px] text-muted dark:text-on-dark-soft mt-1.5 font-body">
                    * Masukkan 36 karakter kode unik (UUID) booking atau 16 digit NIK warga untuk pencarian.
                </p>
            </div>

            <div class="relative flex items-center py-2">
                <div class="grow border-t border-hairline dark:border-white/10"></div>
                <span
                    class="shrink mx-4 text-xs font-semibold text-muted dark:text-on-dark-soft uppercase font-display">Atau</span>
                <div class="grow border-t border-hairline dark:border-white/10"></div>
            </div>

            <button type="button" @click="openQrScanner()"
                class="w-full h-11 flex items-center justify-center gap-2 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-md text-xs border border-hairline dark:border-white/10 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Mulai Scan QR Code
            </button>
        </div>
    </div>

    <!-- Citizen verification details panel (Hidden by default, shown dynamically) -->
    <div id="pnlVerifyDetails"
        class="hidden mt-6 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-md p-4 space-y-4 transition-all">
        <div class="flex items-center justify-between pb-2 border-b border-hairline dark:border-white/10">
            <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Hasil
                Deteksi Tiket</span>
            <span
                class="text-xs bg-status-waiting/20 text-status-waiting px-2 py-0.5 rounded-full font-bold uppercase tracking-wider font-display"
                id="verifyTicketCode">550e8400-e29b-41d4-a716-446655440000</span>
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
                <span class="font-bold text-primary dark:text-accent-teal" id="verifyService">Cetak
                    KTP-el</span>
            </div>
        </div>
        <button type="button" onclick="confirmCheckIn()"
            class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer">
            Konfirmasi Kedatangan (Check-In)
        </button>
    </div>
</div>
