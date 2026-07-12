<!-- Right: Kios Cetak Tiket Mandiri (Walk-In) (Spans 7 cols) -->
<div
    class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
    <div class="flex items-center gap-2 pb-4 border-b border-hairline dark:border-white/10 mb-4">
        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        <h3 class="font-bold text-ink dark:text-white font-display">Kios Cetak Tiket Mandiri (Walk-In)</h3>
    </div>

    <p class="text-xs text-muted dark:text-on-dark-soft mb-4 font-body">Klik salah satu Instansi, pilih jenis
        layanan, kemudian cetak tiket antrean langsung untuk warga.</p>

    <!-- Walk-In Form -->
    <div class="space-y-4" id="kioskWalkInForm">
        <!-- NIK Input -->
        <div>
            <label for="txtWalkInNik"
                class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">NIK
                Warga (16 Digit)</label>
            <div class="flex gap-2">
                <input type="text" id="txtWalkInNik" maxlength="16" placeholder="Masukkan 16 digit NIK"
                    class="flex-1 h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold font-mono placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <button type="button" onclick="checkVisitorNik()"
                    class="h-11 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-blue-500/50 cursor-pointer shadow-sm">
                    Cek NIK
                </button>
            </div>
        </div>

        <!-- Nama Lengkap Input -->
        <div>
            <label for="txtWalkInName"
                class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Nama
                Lengkap</label>
            <input type="text" id="txtWalkInName" disabled
                placeholder="Nama warga (otomatis / isi jika baru)"
                class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-50 disabled:cursor-not-allowed">
        </div>

        <!-- Nomor Telepon Input -->
        <div>
            <label for="txtWalkInPhone"
                class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Nomor
                Telepon / HP</label>
            <input type="text" id="txtWalkInPhone" disabled
                placeholder="Nomor telepon warga (otomatis / isi jika baru)"
                class="w-full h-11 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal disabled:opacity-50 disabled:cursor-not-allowed"
                oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
        </div>

        <!-- Instansi Selection -->
        <div>
            <label for="selWalkInDept"
                class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Instansi
                Tujuan</label>
            <select id="selWalkInDept"
                class="w-full h-11 bg-surface-soft dark:bg-surface-dark border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md px-3 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                <option value="">-- Pilih Instansi --</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Keperluan -->
        <div>
            <label for="txtWalkInPurpose"
                class="block text-xs font-semibold text-ink dark:text-white uppercase tracking-wider mb-2 font-display">Keperluan</label>
            <textarea id="txtWalkInPurpose" rows="2" placeholder="Tuliskan keperluan kedatangan secara singkat..."
                class="w-full bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md p-3 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal"></textarea>
        </div>

        <!-- Priority Checkbox -->
        <div class="flex items-center gap-2 py-1.5 select-none">
            <input type="checkbox" id="chkWalkInPriority" value="1"
                   class="w-4.5 h-4.5 text-primary dark:text-accent-teal border-hairline rounded focus:ring-primary dark:focus:ring-accent-teal cursor-pointer">
            <label for="chkWalkInPriority" class="text-xs font-bold text-ink dark:text-white cursor-pointer select-none">
                Pengunjung Prioritas (Kelompok Rentan)
            </label>
        </div>

        <div class="pt-2">
            <button type="button" onclick="printWalkInTicket()"
                class="w-full h-11 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer shadow-md">
                Cetak Tiket Mandiri
            </button>
        </div>
    </div>
</div>
