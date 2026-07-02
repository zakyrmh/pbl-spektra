{{-- Modal: Gerai (Department) --}}
<div id="gerai-modal" class="fixed inset-0 z-50 overflow-y-auto {{ $errors->any() ? '' : 'hidden' }}" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end sm:items-center justify-center p-0 sm:p-4 text-center">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity duration-200" onclick="closeGeraiModal()"></div>
        
        {{-- Content Box (Responsive size) --}}
        <div class="relative transform overflow-hidden rounded-t-xl sm:rounded-xl bg-canvas dark:bg-surface-dark-elevated text-left shadow-2xl transition-all w-full sm:max-w-md p-6 sm:p-8 border-t sm:border border-hairline dark:border-white/10 max-h-[90vh] overflow-y-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="flex items-center justify-between pb-4 border-b border-hairline-soft dark:border-white/5">
                <h3 class="text-display-sm font-semibold text-ink dark:text-white font-display" id="gerai-modal-title">Tambah Gerai Instansi</h3>
                <button type="button" onclick="closeGeraiModal()" class="w-10 h-10 rounded-full bg-surface-card dark:bg-white/5 border border-hairline dark:border-white/10 flex items-center justify-center hover:bg-surface-strong dark:hover:bg-white/10 text-muted hover:text-ink dark:hover:text-white transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="gerai-form" action="{{ route('config.departments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-6">
                @csrf
                <input type="hidden" name="_method" id="gerai-form-method" value="POST">

                <div>
                    <label for="g-name" class="block text-title-sm font-semibold text-ink dark:text-white font-display mb-2">Nama Instansi <span class="text-status-skipped">*</span></label>
                    <input type="text" id="g-name" name="name" required class="w-full text-body-md rounded-md border border-hairline dark:border-white/15 px-4 py-3 h-12 bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white focus:border-primary focus:ring-3 focus:ring-primary/12 focus:outline-none transition-all font-body" placeholder="e.g. Dinas Kependudukan dan Catatan Sipil" value="{{ old('name') }}">
                </div>

                <div>
                    <label for="g-inisial" class="block text-title-sm font-semibold text-ink dark:text-white font-display mb-2">Kode Prefix Antrean <span class="text-status-skipped">*</span></label>
                    <input type="text" id="g-inisial" name="inisial" required maxlength="6" class="w-full text-body-md rounded-md border border-hairline dark:border-white/15 px-4 py-3 h-12 bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white focus:border-primary focus:ring-3 focus:ring-primary/12 focus:outline-none transition-all font-mono uppercase" placeholder="e.g. DDK" value="{{ old('inisial') }}">
                    <p class="text-caption text-muted dark:text-on-dark-soft mt-1.5 block font-body">Kode unik max 6 karakter huruf untuk penomoran antrean (e.g. DDK-001).</p>
                </div>

                <div>
                    <label for="g-nomor-loket" class="block text-title-sm font-semibold text-ink dark:text-white font-display mb-2">Nomor Loket / Booth <span class="text-status-skipped">*</span></label>
                    <input type="text" id="g-nomor-loket" name="nomor_loket" required class="w-full text-body-md rounded-md border border-hairline dark:border-white/15 px-4 py-3 h-12 bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white focus:border-primary focus:ring-3 focus:ring-primary/12 focus:outline-none transition-all font-body" placeholder="e.g. 01" value="{{ old('nomor_loket') }}">
                    <p class="text-caption text-muted dark:text-on-dark-soft mt-1.5 block font-body">Nomor loket fisik tempat pelayanan berlangsung (e.g. 01, 02).</p>
                </div>

                <div>
                    <label for="g-logo" class="block text-title-sm font-semibold text-ink dark:text-white font-display mb-2">Logo Instansi</label>
                    
                    {{-- Logo Preview Container --}}
                    <div id="logo-preview-container" class="hidden mb-3 items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 rounded-lg border border-hairline dark:border-white/10">
                        <img id="logo-preview" src="#" alt="Pratinjau Logo" class="w-16 h-16 object-contain rounded-md bg-canvas p-1 border border-hairline dark:border-white/10">
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-ink dark:text-white block truncate" id="logo-preview-name">logo.png</span>
                            <button type="button" onclick="clearLogoSelection()" class="text-[11px] font-semibold text-status-skipped hover:text-red-700 dark:hover:text-red-400 mt-1 cursor-pointer">Hapus Pilihan</button>
                        </div>
                    </div>

                    <input type="file" id="g-logo" name="logo" accept="image/*" onchange="previewLogo(this)" class="w-full text-body-sm text-muted dark:text-on-dark-soft file:mr-4 file:py-2.5 file:px-4 file:rounded-pill file:border-0 file:text-button file:font-semibold file:bg-primary/10 file:text-primary dark:file:bg-accent-teal/10 dark:file:text-accent-teal hover:file:bg-primary/20 dark:hover:file:bg-accent-teal/20 transition-all font-body cursor-pointer">
                    <p class="text-caption text-muted dark:text-on-dark-soft mt-1.5 block font-body">Maksimum ukuran gambar 2MB.</p>
                </div>

                <div>
                    <label for="g-desc" class="block text-title-sm font-semibold text-ink dark:text-white font-display mb-2">Deskripsi Singkat</label>
                    <textarea id="g-desc" name="description" rows="3" class="w-full text-body-md rounded-md border border-hairline dark:border-white/15 px-4 py-3 bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white focus:border-primary focus:ring-3 focus:ring-primary/12 focus:outline-none transition-all font-body" placeholder="Keterangan mengenai pelayanan gerai...">{{ old('description') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-hairline-soft dark:border-white/5">
                    <button type="button" onclick="closeGeraiModal()" class="w-full sm:w-auto inline-flex items-center justify-center h-11 px-6 text-button font-semibold text-ink dark:text-white bg-canvas dark:bg-white/5 hover:bg-surface-soft dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill transition-all duration-150 cursor-pointer">Batal</button>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center h-11 px-6 text-button font-semibold text-white bg-primary hover:bg-primary-hover active:scale-[0.98] rounded-pill shadow-md hover:shadow-lg transition-all duration-150 cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script Pendukung Modal --}}
<script>
    function previewLogo(input) {
        const container = document.getElementById('logo-preview-container');
        const preview = document.getElementById('logo-preview');
        const nameLabel = document.getElementById('logo-preview-name');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validasi ukuran file (maks 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file melebihi batas maksimum 2MB.');
                input.value = '';
                clearLogoSelection();
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                nameLabel.innerText = file.name;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            clearLogoSelection();
        }
    }

    function clearLogoSelection() {
        const input = document.getElementById('g-logo');
        const container = document.getElementById('logo-preview-container');
        const preview = document.getElementById('logo-preview');
        const nameLabel = document.getElementById('logo-preview-name');
        
        input.value = '';
        preview.src = '#';
        nameLabel.innerText = '';
        container.classList.add('hidden');
    }

    function openAddGeraiModal() {
        document.getElementById('gerai-modal-title').innerText = 'Tambah Gerai Instansi';
        document.getElementById('gerai-form').action = "{{ route('config.departments.store') }}";
        document.getElementById('gerai-form-method').value = 'POST';
        
        document.getElementById('g-name').value = '';
        document.getElementById('g-inisial').value = '';
        document.getElementById('g-nomor-loket').value = '';
        document.getElementById('g-desc').value = '';
        
        clearLogoSelection();
        
        document.getElementById('gerai-modal').classList.remove('hidden');
    }

    function openEditGeraiModal(department) {
        document.getElementById('gerai-modal-title').innerText = 'Edit Gerai Instansi';
        document.getElementById('gerai-form').action = "/konfigurasi-gerai-loket/departments/" + department.id;
        document.getElementById('gerai-form-method').value = 'PUT';
        
        document.getElementById('g-name').value = department.name;
        document.getElementById('g-inisial').value = department.inisial;
        document.getElementById('g-nomor-loket').value = department.nomor_loket || '';
        document.getElementById('g-desc').value = department.description || '';
        
        // Tampilkan logo saat ini jika ada di database
        if (department.logo) {
            const container = document.getElementById('logo-preview-container');
            const preview = document.getElementById('logo-preview');
            const nameLabel = document.getElementById('logo-preview-name');
            
            preview.src = "/storage/" + department.logo;
            nameLabel.innerText = "Logo Saat Ini";
            container.classList.remove('hidden');
        } else {
            clearLogoSelection();
        }
        
        document.getElementById('gerai-modal').classList.remove('hidden');
    }

    function closeGeraiModal() {
        document.getElementById('gerai-modal').classList.add('hidden');
        clearLogoSelection();
    }
</script>
