@extends('layouts.private')

@section('title', 'Laporan & Analitik — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-bold text-primary dark:text-accent-teal uppercase tracking-widest font-display">Pusat Laporan & Data</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Laporan & Analitik Kinerja</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Tinjau laporan kinerja pelayanan yang dikirim oleh Front Office dan unduh visualisasi data & rekapitulasi lengkap.</p>
            </div>
        </div>



        {{-- Reports List Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($reports as $report)
                @php
                    $summary = $report->data_summary;
                    $totalVisitors = $summary['total_visitors'] ?? 0;
                    $avgServiceTime = $summary['avg_service_time'] ?? 0;
                    $avgWaitingTime = $summary['avg_waiting_time'] ?? 0;
                @endphp
                <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs hover:shadow-md hover:border-primary/20 dark:hover:border-accent-teal/30 transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    {{-- Card Header --}}
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h3 class="text-base font-bold text-ink dark:text-white font-display tracking-tight line-clamp-1" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </h3>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-body">
                                    {{ $report->start_date->format('d M Y') }} - {{ $report->end_date->format('d M Y') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-status-serving/10 text-status-serving tracking-wider shrink-0 select-none">
                                Terkirim
                            </span>
                        </div>

                        {{-- Quick Metrics Grid --}}
                        <div class="grid grid-cols-3 gap-2 border-y border-hairline dark:border-white/10 py-3.5">
                            <div class="text-center">
                                <span class="block text-lg font-bold text-ink dark:text-white font-mono">{{ number_format($totalVisitors) }}</span>
                                <span class="text-[10px] text-muted dark:text-on-dark-soft uppercase font-medium">Pengunjung</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-ink dark:text-white font-mono">{{ $avgServiceTime }}m</span>
                                <span class="text-[10px] text-muted dark:text-on-dark-soft uppercase font-medium">Melayani</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-ink dark:text-white font-mono">{{ $avgWaitingTime }}m</span>
                                <span class="text-[10px] text-muted dark:text-on-dark-soft uppercase font-medium">Tunggu</span>
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="flex items-center justify-between text-xs text-muted dark:text-on-dark-soft font-body">
                            <span class="truncate">Oleh: {{ $report->creator?->name ?? 'Front Office' }}</span>
                            <span class="shrink-0">{{ $report->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- Card Action --}}
                    <div class="bg-surface-soft dark:bg-white/3 border-t border-hairline dark:border-white/10 px-6 py-4 flex items-center justify-between">
                        <span class="text-xs text-muted dark:text-on-dark-soft font-body select-none">Laporan Kinerja Terkunci</span>
                        <a href="{{ route('admin.reports.show', $report) }}" 
                           class="h-9 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-pill shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            Lihat Detail
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted dark:text-on-dark-soft bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 select-none">
                    <svg class="w-16 h-16 mx-auto text-muted/30 dark:text-on-dark-soft/20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4m-8 0H4" />
                    </svg>
                    <h3 class="text-base font-bold text-ink dark:text-white font-display mb-1">Belum Ada Laporan Masuk</h3>
                    <p class="text-sm font-body max-w-sm mx-auto">Laporan kinerja pelayanan yang dikirim oleh Admin Front Office akan muncul di halaman ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
