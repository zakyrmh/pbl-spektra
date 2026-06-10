{{--
    Halaman: Daftar Laporan Pelayanan (Front Office)
    Role   : Admin Front Office
    Route  : reports.index (GET)
--}}
@extends('layouts.private')

@section('title', 'Kelola Laporan Pelayanan — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-16">

        {{-- Header & Aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Kelola Laporan Pelayanan</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                    Buat, edit, tinjau, dan kirimkan laporan agregat pelayanan antrean MPP Kota Sawahlunto.
                </p>
            </div>
            <div>
                <a href="{{ route('reports.create') }}"
                    class="h-11 px-5 bg-primary hover:bg-primary-hover text-white font-bold rounded-lg text-xs transition-all shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Laporan Baru
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-status-serving/10 border border-status-serving/30 rounded-lg animate-fade-in"
                role="alert" id="alert-success">
                <svg class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-serving font-display">Sukses</p>
                    <p class="text-sm text-green-800 dark:text-green-300 font-body mt-0.5">{!! session('success') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()"
                    class="shrink-0 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="flex items-start gap-3 p-4 bg-status-waiting/10 border border-status-waiting/30 rounded-lg animate-fade-in"
                role="alert" id="alert-warning">
                <svg class="w-5 h-5 text-status-waiting shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-waiting font-display">Peringatan</p>
                    <p class="text-sm text-amber-800 dark:text-amber-300 font-body mt-0.5">{!! session('warning') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()"
                    class="shrink-0 text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Tabel Daftar Laporan --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            @if ($reports->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center">
                    <div class="p-4 bg-primary/5 dark:bg-accent-teal/5 text-primary dark:text-accent-teal rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-ink dark:text-white font-display">Belum Ada Laporan</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body max-w-xs mt-1.5">
                        Anda belum membuat laporan pelayanan antrean. Silakan klik tombol "Buat Laporan Baru" di atas.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-hairline dark:border-white/10 bg-surface-soft/40 dark:bg-white/2 text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                                <th class="px-6 py-4">Periode & Judul</th>
                                <th class="px-6 py-4">Total Antrean</th>
                                <th class="px-6 py-4 font-mono">Dibuat Oleh</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline dark:divide-white/5 text-sm font-body">
                            @foreach ($reports as $report)
                                <tr class="hover:bg-surface-soft/20 dark:hover:bg-white/1 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-ink dark:text-white block font-display">
                                            {{ $report->title }}
                                        </span>
                                        <span class="text-xs text-muted dark:text-on-dark-soft mt-0.5 block font-mono">
                                            {{ $report->start_date->translatedFormat('d M Y') }} - {{ $report->end_date->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-ink dark:text-white font-mono">
                                            {{ $report->data_summary['total_visitors'] ?? 0 }}
                                        </span>
                                        <span class="text-xs text-muted dark:text-on-dark-soft">antrean</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-ink dark:text-white block">
                                            {{ $report->creator ? $report->creator->name : 'Sistem' }}
                                        </span>
                                        <span class="text-xs text-muted dark:text-on-dark-soft font-mono block">
                                            {{ $report->created_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($report->isLocked())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-status-serving/12 text-status-serving rounded-full text-xs font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-status-serving"></span>
                                                Terkirim
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-status-waiting/12 text-status-waiting rounded-full text-xs font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-status-waiting"></span>
                                                Draft / Belum Dikirim
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            {{-- Selalu Tampilkan Pratinjau --}}
                                            <a href="{{ route('reports.show', $report->id) }}"
                                                title="Lihat Detail"
                                                class="h-9 px-3 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-md text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                                <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Pratinjau
                                            </a>

                                            @if (!$report->isLocked())
                                                {{-- Edit Runtutan --}}
                                                <a href="{{ route('reports.edit', $report->id) }}"
                                                    title="Ubah Tanggal"
                                                    class="h-9 px-3 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40 rounded-md text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-blue-500">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Ubah
                                                </a>

                                                {{-- Kirim --}}
                                                <form action="{{ route('reports.send', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin mengirim laporan ini ke Super Admin? Setelah dikirim, laporan akan dikunci dan tidak dapat diubah lagi.')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="h-9 px-3 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800/40 rounded-md text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                        </svg>
                                                        Kirim
                                                    </button>
                                                </form>

                                                {{-- Hapus --}}
                                                <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft laporan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="h-9 px-3 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/40 rounded-md text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-red-500">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H10a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($reports->hasPages())
                    <div class="px-6 py-4 border-t border-hairline dark:border-white/10">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
