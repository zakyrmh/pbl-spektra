{{--
    Halaman: Inbox Laporan Masuk (Super Admin)
    Role   : Super Admin
    Route  : admin.reports.index (GET)
--}}
@extends('layouts.private')

@section('title', 'Laporan & Analitik — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-16">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Laporan & Analitik Pelayanan</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                Daftar laporan kompilasi kinerja loket dan antrean warga yang telah dikirim oleh Front Office.
            </p>
        </div>

        {{-- Tabel Daftar Laporan Masuk --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            @if ($reports->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center">
                    <div class="p-4 bg-primary/5 dark:bg-accent-teal/5 text-primary dark:text-accent-teal rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-ink dark:text-white font-display">Belum Ada Laporan Masuk</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body max-w-xs mt-1.5">
                        Belum ada laporan pelayanan dari Front Office yang dikirim ke Super Admin.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-hairline dark:border-white/10 bg-surface-soft/40 dark:bg-white/2 text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                                <th class="px-6 py-4">Nama Laporan</th>
                                <th class="px-6 py-4">Periode Tanggal</th>
                                <th class="px-6 py-4 text-center">Total Kunjungan</th>
                                <th class="px-6 py-4">Front Office</th>
                                <th class="px-6 py-4">Tanggal Terima</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline dark:divide-white/5 text-sm font-body">
                            @foreach ($reports as $report)
                                <tr class="hover:bg-surface-soft/20 dark:hover:bg-white/1 transition-colors">
                                    <td class="px-6 py-4 font-bold text-ink dark:text-white font-display">
                                        {{ $report->title }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-muted dark:text-on-dark-soft">
                                        {{ $report->start_date->translatedFormat('d M Y') }} - {{ $report->end_date->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-ink dark:text-white">
                                        {{ $report->data_summary['total_visitors'] ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-ink dark:text-white">
                                        {{ $report->creator ? $report->creator->name : 'Sistem' }}
                                    </td>
                                    <td class="px-6 py-4 text-muted dark:text-on-dark-soft font-mono text-xs">
                                        {{ $report->updated_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.reports.show', $report->id) }}"
                                            class="h-9 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-md text-xs transition-all shadow-sm hover:shadow inline-flex items-center gap-1 cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Tinjau Kinerja
                                        </a>
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
