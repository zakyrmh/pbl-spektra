@extends('layouts.private')

@section('title', 'Kelola Laporan Kinerja — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16" x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        editId: null,
        editTitle: '',
        editStartDate: '',
        editEndDate: '',
        editAction: '',
        openEdit(report) {
            this.editId = report.id;
            this.editTitle = report.title;
            this.editStartDate = report.start_date;
            this.editEndDate = report.end_date;
            this.editAction = '{{ url('/fo/reports') }}/' + report.id;
            this.showEditModal = true;
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-bold text-primary dark:text-accent-teal uppercase tracking-widest font-display">Laporan & Analitik</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Kelola Laporan Kinerja</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Generate rekapitulasi data antrean, perbarui draf, dan kirimkan laporan kinerja ke Super Admin.</p>
            </div>
            <div>
                <button type="button" @click="showCreateModal = true"
                        class="h-11 px-5 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Laporan Baru
                </button>
            </div>
        </div>



        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Menyimpan</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/10">
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider w-16">No</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Judul Laporan</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Periode Rekap</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Dibuat Oleh</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Status</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Dibuat Pada</th>
                            <th class="py-3.5 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline dark:divide-white/15">
                        @forelse ($reports as $index => $report)
                            <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/2 transition-colors">
                                <td class="py-4 px-4 text-sm text-ink dark:text-white font-mono font-medium">{{ $index + 1 }}</td>
                                <td class="py-4 px-4 text-sm font-semibold text-ink dark:text-white font-display">
                                    <a href="{{ route('admin.fo.reports.show', $report) }}" class="hover:underline hover:text-primary dark:hover:text-accent-teal transition-colors">
                                        {{ $report->title }}
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-sm text-muted dark:text-on-dark-soft font-body">
                                    {{ $report->start_date->format('d M Y') }} s/d {{ $report->end_date->format('d M Y') }}
                                </td>
                                <td class="py-4 px-4 text-sm text-muted dark:text-on-dark-soft font-body">{{ $report->creator?->name ?? 'Sistem' }}</td>
                                <td class="py-4 px-4 text-xs font-bold font-display">
                                    @if ($report->status === 'Terkirim')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-serving/10 text-status-serving">
                                            <span class="w-1.5 h-1.5 rounded-full bg-status-serving"></span>
                                            Terkirim
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-waiting/10 text-status-waiting">
                                            <span class="w-1.5 h-1.5 rounded-full bg-status-waiting"></span>
                                            Draft / Belum Dikirim
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs text-muted dark:text-on-dark-soft font-body">{{ $report->created_at->format('d M Y H:i') }}</td>
                                <td class="py-4 px-4 text-sm text-right space-x-1.5">
                                    <a href="{{ route('admin.fo.reports.show', $report) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-primary hover:bg-primary/10 dark:text-accent-teal dark:hover:bg-accent-teal/10 rounded-md transition-all cursor-pointer"
                                       title="Lihat Detail Laporan">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if ($report->status !== 'Terkirim')
                                        <button type="button" 
                                                @click="openEdit({
                                                    id: {{ $report->id }},
                                                    title: '{{ addslashes($report->title) }}',
                                                    start_date: '{{ $report->start_date->toDateString() }}',
                                                    end_date: '{{ $report->end_date->toDateString() }}'
                                                })"
                                                class="inline-flex items-center justify-center w-8 h-8 text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/10 rounded-md transition-all cursor-pointer"
                                                title="Edit Laporan">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.fo.reports.send', $report) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin mengirim laporan ini ke Super Admin? Setelah dikirim, data laporan akan dikunci dan tidak dapat diubah lagi.')"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-status-serving hover:bg-status-serving/10 rounded-md transition-all cursor-pointer"
                                                    title="Kirim ke Super Admin">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.fo.reports.destroy', $report) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus draf laporan ini?')"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-status-skipped hover:bg-status-skipped/10 rounded-md transition-all cursor-pointer"
                                                    title="Hapus Laporan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-muted dark:text-on-dark-soft italic select-none" title="Data Laporan Terkunci">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Terkunci
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-sm text-muted dark:text-on-dark-soft font-body select-none">
                                    <svg class="w-12 h-12 mx-auto text-muted/30 dark:text-on-dark-soft/20 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Belum ada data laporan. Klik "Buat Laporan Baru" untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="showCreateModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
             x-transition
             x-cloak>
            <div @click.away="showCreateModal = false" 
                 class="w-full max-w-md bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-3">
                    <h3 class="text-base font-bold text-ink dark:text-white font-display">Generate Laporan Kinerja Baru</h3>
                    <button type="button" @click="showCreateModal = false" class="text-muted hover:text-ink dark:hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.fo.reports.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="create_title" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Judul Laporan</label>
                        <input type="text" id="create_title" name="title" required placeholder="Contoh: Laporan Kinerja Mei 2026"
                               class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="create_start_date" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" id="create_start_date" name="start_date" required
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label for="create_end_date" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Tanggal Akhir</label>
                            <input type="date" id="create_end_date" name="end_date" required
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-hairline dark:border-white/10">
                        <button type="button" @click="showCreateModal = false"
                                class="h-10 px-4 text-xs font-semibold text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/10 rounded-pill transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer">
                            Generate Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
             x-transition
             x-cloak>
            <div @click.away="showEditModal = false" 
                 class="w-full max-w-md bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-3">
                    <h3 class="text-base font-bold text-ink dark:text-white font-display">Perbarui Laporan Kinerja</h3>
                    <button type="button" @click="showEditModal = false" class="text-muted hover:text-ink dark:hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form :action="editAction" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="space-y-1.5">
                        <label for="edit_title" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Judul Laporan</label>
                        <input type="text" id="edit_title" name="title" x-model="editTitle" required
                               class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="edit_start_date" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" id="edit_start_date" name="start_date" x-model="editStartDate" required
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label for="edit_end_date" class="block text-xs font-bold text-ink dark:text-white font-display uppercase tracking-wider">Tanggal Akhir</label>
                            <input type="date" id="edit_end_date" name="end_date" x-model="editEndDate" required
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-hairline dark:border-white/10">
                        <button type="button" @click="showEditModal = false"
                                class="h-10 px-4 text-xs font-semibold text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white hover:bg-surface-soft dark:hover:bg-white/10 rounded-pill transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer">
                            Perbarui Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
