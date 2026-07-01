{{-- Cancellation Modal Overlay --}}
<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak>

    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-6 md:p-8 max-w-md w-full border border-hairline dark:border-white/10 shadow-2xl transform transition-all duration-300 relative"
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        @click.away="modalOpen = false">

        <button type="button" @click="modalOpen = false"
            class="absolute top-4 right-4 text-muted hover:text-ink dark:hover:text-white p-1 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors cursor-pointer">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display mb-2">Konfirmasi
            Pembatalan</h3>
        <p class="text-xs text-muted dark:text-on-dark-soft mb-6 font-body">Anda akan membatalkan reservasi antrean
            berikut. Aksi ini tidak dapat diurungkan.</p>

        <div
            class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 p-4 rounded-lg text-xs space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-muted font-medium">Kode Antrean</span>
                <span class="font-mono font-bold text-primary dark:text-accent-teal" x-text="bookingCode"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted font-medium">Nama Warga</span>
                <span class="font-bold text-ink dark:text-white" x-text="userName"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted font-medium">Layanan</span>
                <span class="font-bold text-ink dark:text-white" x-text="serviceName"></span>
            </div>
        </div>

        <form :action="actionUrl" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="reason" class="block text-sm font-bold text-ink dark:text-white font-display">
                    Alasan Pembatalan
                </label>
                <textarea id="reason" name="reason" rows="3" required x-model="reason"
                    placeholder="Contoh: Dokumen persyaratan tidak lengkap, atau atas permohonan warga..."
                    class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all"></textarea>
                <p class="text-[10px] text-muted dark:text-on-dark-soft font-body">Minimal 5 karakter. Alasan ini
                    akan dicantumkan pada notifikasi email warga.</p>
            </div>

            <div class="pt-4 border-t border-hairline dark:border-white/10 flex justify-end gap-3">
                <button type="button" @click="modalOpen = false"
                    class="h-11 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                    Kembali
                </button>
                <button type="submit" :disabled="reason.trim().length < 5"
                    :class="reason.trim().length < 5 ? 'opacity-50 cursor-not-allowed bg-red-600/50' :
                        'bg-red-600 hover:bg-red-700'"
                    class="h-11 px-6 text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batalkan Booking
                </button>
            </div>
        </form>

    </div>
</div>
