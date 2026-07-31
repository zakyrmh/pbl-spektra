<!-- Slide-Over Drawer: Kios Cetak Tiket Mandiri (Walk-In) -->
<div x-cloak x-show="ticketDrawerOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <!-- Backdrop Overlay -->
    <div x-show="ticketDrawerOpen"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeTicketDrawer()"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        <div x-show="ticketDrawerOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-200 sm:duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md sm:max-w-lg bg-canvas dark:bg-surface-dark-elevated shadow-2xl border-l border-hairline dark:border-white/10 flex flex-col justify-between overflow-y-auto">

            <!-- Drawer Header -->
            <div class="p-6 border-b border-hairline dark:border-white/10 flex items-center justify-between bg-surface-soft/50 dark:bg-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-primary/10 text-primary dark:text-accent-teal rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-ink dark:text-white font-display text-base" id="slide-over-title">Cetak Karcis Walk-In Baru</h3>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body">Penerbitan nomor antrean langsung di stasiun FO</p>
                    </div>
                </div>
                <button type="button" @click="closeTicketDrawer()"
                        class="p-2 text-muted hover:text-ink dark:hover:text-white rounded-full bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Drawer Form Body -->
            <div class="p-6 space-y-4 flex-1 font-body text-sm" id="kioskWalkInForm">
                <!-- NIK Input -->
                <div class="space-y-1.5">
                    <label for="txtWalkInNik" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                        NIK Warga (16 Digit) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="txtWalkInNik" x-ref="nikField" maxlength="16" placeholder="Contoh: 1373021408990002"
                            class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3.5 font-bold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <button type="button" onclick="checkVisitorNik()"
                            class="h-11 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shrink-0">
                            Cek NIK
                        </button>
                    </div>
                </div>

                <!-- Nama Lengkap Input -->
                <div class="space-y-1.5">
                    <label for="txtWalkInName" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                        Nama Lengkap Warga <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="txtWalkInName" disabled placeholder="Nama warga (otomatis / isi jika baru)"
                        class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3.5 font-bold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-60 disabled:cursor-not-allowed">
                </div>

                <!-- Nomor Telepon Input -->
                <div class="space-y-1.5">
                    <label for="txtWalkInPhone" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                        Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="txtWalkInPhone" disabled placeholder="Contoh: 081234567890"
                        class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3.5 font-mono font-bold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-60 disabled:cursor-not-allowed"
                        oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                </div>

                <!-- Instansi Utama & Multi-Gerai Waterfall Selection -->
                <div class="space-y-1.5" x-data="{ selectedWalkInDept: '' }">
                    <label for="selWalkInDept" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                        Instansi Utama <span class="text-rose-500">*</span>
                    </label>
                    <select id="selWalkInDept" x-model="selectedWalkInDept"
                        class="w-full h-11 bg-surface-soft dark:bg-surface-dark border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3.5 font-bold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        <option value="">-- Pilih Instansi Utama --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>

                    <!-- Multi-Gerai Waterfall Selection (Gerai Lanjutan: Gerai 2, 3, 4, dst) -->
                    <div class="space-y-2 pt-3 border-t border-dashed border-hairline dark:border-white/10 mt-3" x-show="selectedWalkInDept" x-cloak>
                        <label class="text-xs font-bold text-ink dark:text-white font-display flex items-center justify-between">
                            <span>Instansi Lanjutan (Multi-Gerai Waterfall Queue)</span>
                            <span class="text-[10px] font-normal px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-accent-teal rounded-full font-mono">Gerai 2, 3, 4...</span>
                        </label>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft font-body leading-relaxed">
                            Nomor antrean gerai lanjutan akan <strong>diterbitkan otomatis</strong> ("Selesai Dulu Baru Antre") begitu gerai sebelumnya menekan Selesai.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto pr-1">
                            @foreach ($departments as $dept)
                                <label class="flex items-center gap-2 p-2 rounded-md border border-hairline dark:border-white/10 bg-surface-soft/50 dark:bg-white/5 cursor-pointer hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all text-xs"
                                    x-show="selectedWalkInDept != '{{ $dept->id }}'">
                                    <input type="checkbox" value="{{ $dept->id }}" class="chk-next-dept w-3.5 h-3.5 text-primary dark:text-accent-teal border-hairline rounded focus:ring-primary cursor-pointer">
                                    <span class="text-xs font-medium text-ink dark:text-white">{{ $dept->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Keperluan / Layanan -->
                <div class="space-y-1.5">
                    <label for="txtWalkInPurpose" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                        Keperluan / Layanan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="txtWalkInPurpose" rows="2" placeholder="Contoh: Pengurusan KTP-el dan Perizinan Usaha..."
                        class="w-full bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md p-3 text-xs focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal font-body"></textarea>
                </div>

                <!-- Priority Checkbox -->
                <div class="flex items-center gap-2.5 p-3 rounded-lg border border-amber-500/20 bg-amber-500/5 select-none">
                    <input type="checkbox" id="chkWalkInPriority" value="1"
                           class="w-4.5 h-4.5 text-primary dark:text-accent-teal border-hairline rounded focus:ring-primary dark:focus:ring-accent-teal cursor-pointer">
                    <label for="chkWalkInPriority" class="text-xs font-bold text-amber-800 dark:text-amber-400 cursor-pointer select-none">
                        🌟 Pengunjung Prioritas (Kelompok Rentan / Disabilitas)
                    </label>
                </div>
            </div>

            <!-- Drawer Footer Action -->
            <div class="p-6 border-t border-hairline dark:border-white/10 bg-surface-soft/50 dark:bg-white/5 flex items-center justify-between gap-3">
                <button type="button" @click="closeTicketDrawer()"
                        class="h-11 px-5 bg-canvas hover:bg-surface-soft text-ink dark:text-white dark:bg-white/5 border border-hairline dark:border-white/10 font-semibold rounded-pill text-xs transition-all cursor-pointer">
                    Batal (Esc)
                </button>

                <button type="button" onclick="printWalkInTicket()"
                        class="h-11 px-6 bg-primary hover:bg-primary-hover text-on-primary font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Tiket Mandiri</span>
                </button>
            </div>

        </div>
    </div>
</div>
