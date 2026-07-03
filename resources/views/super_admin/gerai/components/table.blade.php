{{-- Desktop Layout Table --}}
<div class="hidden md:block bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-hairline dark:border-white/10">
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Logo</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Nama Gerai</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Kode Prefix</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Deskripsi</th>
                <th class="px-5 py-3.5 text-right text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-hairline-soft dark:divide-white/5">
            @forelse($departments as $dept)
            <tr class="hover:bg-surface-soft dark:hover:bg-white/5 transition-colors duration-150 group">
                <td class="px-5 py-4">
                    @if($dept->logo)
                    <img src="{{ Storage::disk('public')->url($dept->logo) }}" alt="Logo {{ $dept->name }}" class="w-10 h-10 object-contain rounded-lg bg-surface-soft dark:bg-white/5 p-1 border border-hairline dark:border-white/10">
                    @else
                    <div class="w-10 h-10 bg-primary/10 dark:bg-accent-teal/10 text-primary dark:text-accent-teal rounded-lg flex items-center justify-center font-bold text-xs uppercase border border-primary/20 dark:border-accent-teal/20 font-display">
                        {{ substr($dept->name, 0, 2) }}
                        {{$dept->logo}}
                    </div>
                    @endif
                </td>
                <td class="px-5 py-4">
                    <p class="font-bold font-display text-ink dark:text-white leading-tight">{{ $dept->name }}</p>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center font-mono font-bold text-primary dark:text-accent-teal bg-primary/10 dark:bg-accent-teal/10 px-2.5 py-1 rounded-sm text-[11px] border border-primary/20 dark:border-accent-teal/20">{{ $dept->inisial }}</span>
                </td>
                <td class="px-5 py-4 text-muted dark:text-on-dark-soft max-w-xs truncate text-xs">
                    {{ $dept->description ?? '-' }}
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="inline-flex items-center gap-3">
                        <button
                            type="button"
                            onclick="openEditGeraiModal({{ json_encode($dept) }})"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-hover dark:text-accent-teal dark:hover:text-accent-teal/80 transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <button
                            type="button"
                            onclick="openDeleteGeraiModal('{{ route('config.departments.destroy', $dept) }}', '{{ addslashes($dept->name) }}')"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 bg-surface-soft dark:bg-white/5 rounded-lg border border-hairline dark:border-white/10 flex items-center justify-center text-muted">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Belum ada data Gerai terdaftar</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Klik "Tambah Gerai" untuk menambahkan gerai baru.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
