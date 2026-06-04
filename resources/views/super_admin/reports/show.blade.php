{{--
    Halaman: Detail Visual Laporan Pelayanan (Super Admin)
    Role   : Super Admin
    Route  : admin.reports.show (GET)
--}}
@extends('layouts.private')

@section('title', 'Detail Analitik Laporan — MPP Kota Sawahlunto')

@section('content')
    @php
        $summary = $report->data_summary ?? [];
        $total = $summary['total_visitors'] ?? 0;
        $completed = $summary['completed'] ?? 0;
        $skipped = $summary['skipped'] ?? 0;
        $waiting = $summary['waiting'] ?? 0;
        $serving = $summary['serving'] ?? 0;
        $avgSeconds = $summary['average_service_time_seconds'] ?? 0;

        // Hitung max untuk skala grafik
        $dailyData = $summary['daily'] ?? [];
        $maxTotal = collect($dailyData)->max('total') ?: 1;

        function formatDurationSuper($seconds) {
            if ($seconds <= 0) return '0s';
            $m = floor($seconds / 60);
            $s = $seconds % 60;
            return ($m > 0 ? "{$m}m " : "") . "{$s}s";
        }
    @endphp

    <div class="max-w-7xl mx-auto space-y-6 pb-16">

        {{-- Header & Navigasi Balik --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}"
                    class="w-10 h-10 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-full transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Tinjau Analitik Pelayanan</h1>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                        {{ $report->title }}
                    </p>
                </div>
            </div>

            <div class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md">
                Diterima: {{ $report->updated_at->translatedFormat('d M Y, H:i') }}
            </div>
        </div>

        {{-- Metrik Ringkasan Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card 1 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Pengunjung</p>
                <h3 class="text-3xl font-extrabold text-ink dark:text-white mt-2 font-mono">{{ $total }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Warga berkunjung</p>
                <div class="absolute right-4 bottom-4 p-2 bg-primary/5 dark:bg-accent-teal/5 text-primary dark:text-accent-teal rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Selesai Dilayani</p>
                <h3 class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-2 font-mono">{{ $completed }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">
                    {{ $total > 0 ? round(($completed / $total) * 100, 1) : 0 }}% tingkat keberhasilan
                </p>
                <div class="absolute right-4 bottom-4 p-2 bg-green-500/5 text-green-600 dark:text-green-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Terlewat / Skipped</p>
                <h3 class="text-3xl font-extrabold text-red-500 dark:text-red-400 mt-2 font-mono">{{ $skipped }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">
                    {{ $total > 0 ? round(($skipped / $total) * 100, 1) : 0 }}% warga tidak hadir
                </p>
                <div class="absolute right-4 bottom-4 p-2 bg-red-500/5 text-red-500 dark:text-red-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rerata Durasi Layanan</p>
                <h3 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-2 font-mono">{{ formatDurationSuper($avgSeconds) }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Dari antrean selesai</p>
                <div class="absolute right-4 bottom-4 p-2 bg-blue-500/5 text-blue-600 dark:text-blue-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Grafik Kunjungan Harian (Tailwind CSS Bar Chart) --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Grafik Tren Kunjungan Harian
                    </h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">
                        Visualisasi volume antrean per hari pada rentang laporan.
                    </p>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 bg-green-500 rounded-xs"></span>
                        <span class="text-muted dark:text-on-dark-soft">Selesai</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 bg-status-skipped rounded-xs"></span>
                        <span class="text-muted dark:text-on-dark-soft">Terlewat</span>
                    </div>
                </div>
            </div>

            @if (empty($dailyData))
                <div class="h-48 flex items-center justify-center text-xs text-muted dark:text-on-dark-soft font-body border-2 border-dashed border-hairline dark:border-white/10 rounded-lg">
                    Data harian kosong
                </div>
            @else
                <div class="pt-6">
                    {{-- Chart Container --}}
                    <div class="relative h-64 flex items-end gap-2 sm:gap-4 border-b border-l border-hairline dark:border-white/10 px-4 pb-1">
                        {{-- Y-Axis Grid Guidelines --}}
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pr-4">
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="h-0 w-full"></div>
                        </div>

                        {{-- Bar loop --}}
                        @foreach ($dailyData as $day)
                            @php
                                $pctTotal = ($day['total'] / $maxTotal) * 100;
                                $pctCompleted = $day['total'] > 0 ? ($day['completed'] / $day['total']) * 100 : 0;
                                $pctSkipped = $day['total'] > 0 ? ($day['skipped'] / $day['total']) * 100 : 0;
                            @endphp
                            <div class="flex-1 flex flex-col items-center group relative h-full justify-end z-1">
                                {{-- Popover Tooltip --}}
                                <div class="absolute bottom-full mb-2 bg-slate-900 dark:bg-slate-800 text-white text-[10px] rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg border border-white/10">
                                    <p class="font-bold border-b border-white/10 pb-1 mb-1">{{ $day['date'] }}</p>
                                    <p class="flex justify-between gap-4"><span>Kunjungan:</span> <span class="font-bold">{{ $day['total'] }}</span></p>
                                    <p class="flex justify-between gap-4 text-green-400"><span>Selesai:</span> <span class="font-bold">{{ $day['completed'] }}</span></p>
                                    <p class="flex justify-between gap-4 text-red-400"><span>Terlewat:</span> <span class="font-bold">{{ $day['skipped'] }}</span></p>
                                </div>

                                {{-- Bars --}}
                                <div class="w-full flex gap-1 items-end justify-center" style="height: {{ max($pctTotal, 6) }}%">
                                    {{-- Selesai Bar --}}
                                    <div class="w-3 sm:w-4 bg-green-500 rounded-t-xs hover:bg-green-400 transition-colors" style="height: {{ max($pctCompleted, 5) }}%"></div>
                                    {{-- Terlewat Bar --}}
                                    <div class="w-3 sm:w-4 bg-status-skipped rounded-t-xs hover:bg-red-400 transition-colors" style="height: {{ max($pctSkipped, 5) }}%"></div>
                                </div>

                                {{-- X Label --}}
                                <span class="text-[10px] font-semibold text-muted dark:text-on-dark-soft mt-2 font-mono whitespace-nowrap overflow-hidden">
                                    {{ $day['date'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Tabel Kinerja per Loket --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-hairline dark:border-white/10">
                <h3 class="font-bold text-ink dark:text-white font-display flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Tabel Performa Loket / Counter
                </h3>
                <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">
                    Rincian data performa kecepatan pelayanan dan jumlah transaksi per loket.
                </p>
            </div>

            @if (empty($summary['counters']))
                <div class="p-8 text-center text-xs text-muted dark:text-on-dark-soft font-body">
                    Tidak ada data loket.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-hairline dark:border-white/10 bg-surface-soft/40 dark:bg-white/2 text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                                <th class="px-6 py-4">Nama Loket</th>
                                <th class="px-6 py-4">Instansi</th>
                                <th class="px-6 py-4 text-center">Total Antrean</th>
                                <th class="px-6 py-4 text-center">Selesai</th>
                                <th class="px-6 py-4 text-center">Terlewat</th>
                                <th class="px-6 py-4 text-center">Rata-Rata Pelayanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline dark:divide-white/5 text-sm font-body">
                            @foreach ($summary['counters'] as $counter)
                                <tr class="hover:bg-surface-soft/20 dark:hover:bg-white/1 transition-colors">
                                    <td class="px-6 py-4 font-bold text-ink dark:text-white font-display">
                                        {{ $counter['counter_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-muted dark:text-on-dark-soft font-semibold">
                                        {{ $counter['department_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-ink dark:text-white">
                                        {{ $counter['total'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-semibold text-green-600 dark:text-green-400">
                                        {{ $counter['completed'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-semibold text-red-500 dark:text-red-400">
                                        {{ $counter['skipped'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono text-ink dark:text-white">
                                        {{ formatDurationSuper($counter['average_service_time_seconds'] ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Metadata Pengirim --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4">
            <h3 class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2">
                Log Pengiriman Laporan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs font-body">
                <div>
                    <span class="text-muted dark:text-on-dark-soft block">Dibuat & Dikirim Oleh:</span>
                    <span class="font-bold text-ink dark:text-white mt-0.5 block">
                        {{ $report->creator ? $report->creator->name : 'Front Office' }}
                    </span>
                </div>
                <div>
                    <span class="text-muted dark:text-on-dark-soft block">Rentang Tanggal Laporan:</span>
                    <span class="font-mono font-bold text-ink dark:text-white mt-0.5 block">
                        {{ $report->start_date->translatedFormat('d M Y') }} s/d {{ $report->end_date->translatedFormat('d M Y') }}
                    </span>
                </div>
                <div>
                    <span class="text-muted dark:text-on-dark-soft block">Diterima Pada:</span>
                    <span class="font-mono font-bold text-ink dark:text-white mt-0.5 block">
                        {{ $report->updated_at->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
@endsection
