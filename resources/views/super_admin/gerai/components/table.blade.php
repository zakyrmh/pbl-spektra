{{-- Desktop Layout Table --}}
<div class="hidden md:block bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-xs font-bold uppercase tracking-wider font-display border-b border-hairline dark:border-white/10">
                <th class="px-6 py-4">Logo</th>
                <th class="px-6 py-4">Nama Gerai</th>
                <th class="px-6 py-4">Kode Prefix</th>
                <th class="px-6 py-4">Deskripsi</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-hairline-soft dark:divide-white/5 text-body-md text-ink dark:text-white">
            @forelse($departments as $dept)
            <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/5 transition-colors duration-150">
                <td class="px-6 py-3.5">
                    @if($dept->logo)
                    <img src="{{ Storage::disk('public')->url($dept->logo) }}" alt="Logo {{ $dept->name }}" class="w-10 h-10 object-contain rounded-lg bg-surface-soft dark:bg-white/5 p-1 border border-hairline dark:border-white/10">
                    @else
                    <div class="w-10 h-10 bg-primary/10 dark:bg-accent-teal/10 text-primary dark:text-accent-teal rounded-lg flex items-center justify-center font-bold text-xs uppercase border border-primary/20 dark:border-accent-teal/20 font-display">
                        {{ substr($dept->name, 0, 2) }}
                        {{$dept->logo}}
                    </div>
                    @endif
                </td>
                <td class="px-6 py-3.5 font-bold font-display text-ink dark:text-white">
                    {{ $dept->name }}
                </td>
                <td class="px-6 py-3.5 font-mono font-bold text-primary dark:text-accent-teal">
                    <span class="bg-primary/10 dark:bg-accent-teal/10 px-2.5 py-1 rounded-lg text-xs border border-primary/20 dark:border-accent-teal/20">{{ $dept->inisial }}</span>
                </td>
                <td class="px-6 py-3.5 text-muted dark:text-on-dark-soft max-w-xs truncate text-body-sm">
                    {{ $dept->description ?? '-' }}
                </td>
                <td class="px-6 py-3.5 text-right space-x-3">
                    <button onclick="openEditGeraiModal({{ json_encode($dept) }})" class="text-primary hover:text-primary-hover dark:text-accent-teal dark:hover:text-accent-teal/80 font-semibold cursor-pointer">Edit</button>
                    <form action="{{ route('config.departments.destroy', $dept) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Gerai ini? Semua loket dan layanan terkait akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-status-skipped hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold pl-1 cursor-pointer bg-transparent border-0">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-muted dark:text-on-dark-soft">
                    Belum ada data Gerai terdaftar. Klik "Tambah Gerai" untuk menambahkan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
