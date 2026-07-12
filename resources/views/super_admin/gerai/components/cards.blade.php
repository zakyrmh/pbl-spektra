{{-- Mobile Layout Cards --}}
<div class="grid grid-cols-1 gap-4 md:hidden">
    @forelse($departments as $dept)
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-5 border border-hairline dark:border-white/10 shadow-xs flex flex-col space-y-4">
        <div class="flex items-center gap-4">
            @if($dept->logo)
            <img src="{{ Storage::disk('public')->url($dept->logo) }}" alt="Logo {{ $dept->name }}" class="w-12 h-12 object-contain rounded-lg bg-surface-soft dark:bg-white/5 p-1 border border-hairline dark:border-white/10 shrink-0">
            @else
            <div class="w-12 h-12 bg-primary/10 dark:bg-accent-teal/10 text-primary dark:text-accent-teal rounded-lg flex items-center justify-center font-bold text-sm uppercase border border-primary/20 dark:border-accent-teal/20 font-display shrink-0">
                {{ substr($dept->name, 0, 2) }}
            </div>
            @endif
            <div class="min-w-0">
                <h4 class="font-bold font-display text-ink dark:text-white leading-tight truncate text-body-md">{{ $dept->name }}</h4>
                <span class="inline-block bg-primary/10 dark:bg-accent-teal/10 text-primary dark:text-accent-teal px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold border border-primary/20 dark:border-accent-teal/20 mt-1.5">{{ $dept->inisial }}</span>
            </div>
        </div>
        @if($dept->description)
        <p class="text-body-sm text-muted dark:text-on-dark-soft/70 line-clamp-2">
            {{ $dept->description }}
        </p>
        @endif
        <div class="flex items-center justify-end gap-3 border-t border-hairline-soft dark:border-white/5 pt-3">
            <button onclick="openEditGeraiModal({{ json_encode($dept) }})" class="inline-flex items-center justify-center h-10 px-4 text-xs font-semibold text-primary dark:text-accent-teal bg-primary/5 hover:bg-primary/10 dark:bg-accent-teal/5 dark:hover:bg-accent-teal/10 rounded-pill transition-all cursor-pointer">Edit</button>
            <button type="button" onclick="openDeleteGeraiModal('{{ route('config.departments.destroy', $dept) }}', '{{ addslashes($dept->name) }}')" class="inline-flex items-center justify-center h-10 px-4 text-xs font-semibold text-red-500 bg-red-500/5 hover:bg-red-500/10 rounded-pill transition-all cursor-pointer">Hapus</button>
        </div>
    </div>
    @empty
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg p-8 border border-hairline dark:border-white/10 text-center text-muted dark:text-on-dark-soft font-body">
        Belum ada data Gerai terdaftar. Klik "Tambah Gerai" untuk menambahkan.
    </div>
    @endforelse
</div>
