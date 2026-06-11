{{--
    Halaman: Pratinjau Detail Laporan (Front Office)
    Role   : Admin Front Office
    Route  : reports.show (GET)
--}}
@extends('layouts.private')

@section('title', 'Pratinjau Laporan — MPP Kota Sawahlunto')

@section('content')
    @php
        $summary = $report->data_summary ?? [];
        $total = $summary['total_visitors'] ?? 0;
        $completed = $summary['completed'] ?? 0;
        $skipped = $summary['skipped'] ?? 0;
        $waiting = $summary['waiting'] ?? 0;
        $serving = $summary['serving'] ?? 0;
        $avgSeconds = $summary['average_service_time_seconds'] ?? 0;
        $dailyData = $summary['daily'] ?? [];
        $maxTotal = collect($dailyData)->max('total') ?: 1;

        if (!function_exists('formatDuration')) {
            function formatDuration($seconds) {
                if ($seconds <= 0) return '0s';
                $m = floor($seconds / 60);
                $s = $seconds % 60;
                return ($m > 0 ? "{$m}m " : "") . "{$s}s";
            }
        }
    @endphp

    <div class="max-w-7xl mx-auto space-y-6 pb-16">

        {{-- Header & Navigasi Balik --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}"
                    class="w-10 h-10 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-full transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Detail Laporan Pelayanan</h1>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                        {{ $report->title }}
                    </p>
                </div>
            </div>

            {{-- Status Badge --}}
            <div>
                @if ($report->isLocked())
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-status-serving/10 text-status-serving border border-status-serving/30 rounded-full text-xs font-bold font-display">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-status-serving"></span>
                        </span>
                        STATUS: TERKIRIM & TERKUNCI
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-status-waiting/10 text-status-waiting border border-status-waiting/30 rounded-full text-xs font-bold font-display">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-waiting opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-status-waiting"></span>
                        </span>
                        STATUS: DRAFT (BELUM DIKIRIM)
                    </span>
                @endif
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

        {{-- Metrik Ringkasan Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card 1 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Pengunjung</p>
                <h3 class="text-3xl font-extrabold text-ink dark:text-white mt-2 font-mono">{{ $total }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Jumlah warga terdaftar</p>
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
                    {{ $total > 0 ? round(($completed / $total) * 100, 1) : 0 }}% dari total kunjungan
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
                    {{ $total > 0 ? round(($skipped / $total) * 100, 1) : 0 }}% dari total kunjungan
                </p>
                <div class="absolute right-4 bottom-4 p-2 bg-red-500/5 text-red-500 dark:text-red-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rerata Waktu Layanan</p>
                <h3 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-2 font-mono">{{ formatDuration($avgSeconds) }}</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Dari antrean Completed</p>
                <div class="absolute right-4 bottom-4 p-2 bg-blue-500/5 text-blue-600 dark:text-blue-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Grid Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Detail Metadata Laporan --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4">
                <h3 class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Laporan
                </h3>
                <div class="space-y-3 text-xs font-body">
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Dibuat Oleh:</span>
                        <span class="font-bold text-ink dark:text-white">{{ $report->creator ? $report->creator->name : 'Sistem' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Email Pembuat:</span>
                        <span class="font-mono text-ink dark:text-white">{{ $report->creator ? $report->creator->email : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Tanggal Dibuat:</span>
                        <span class="font-mono text-ink dark:text-white">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Periode Awal:</span>
                        <span class="font-mono text-ink dark:text-white">{{ $report->start_date->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-on-dark-soft">Periode Akhir:</span>
                        <span class="font-mono text-ink dark:text-white">{{ $report->end_date->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Status Lainnya --}}
            <div class="lg:col-span-2 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4">
                <h3 class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Status Antrean Tambahan
                </h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-lg">
                        <span class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Waiting</span>
                        <span class="block text-2xl font-bold mt-1 text-ink dark:text-white font-mono">{{ $waiting }}</span>
                    </div>
                    <div class="p-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-lg">
                        <span class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Serving</span>
                        <span class="block text-2xl font-bold mt-1 text-ink dark:text-white font-mono">{{ $serving }}</span>
                    </div>
                    <div class="p-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 rounded-lg">
                        <span class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Rasio Sukses</span>
                        <span class="block text-2xl font-bold mt-1 text-ink dark:text-white font-mono">
                            {{ $total > 0 ? round(($completed / $total) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Kunjungan Harian --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Tren Kunjungan Harian
                    </h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">
                        Visualisasi volume antrean per hari pada rentang laporan ini.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 bg-green-500 rounded-sm"></span>
                        <span class="text-muted dark:text-on-dark-soft">Selesai</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 bg-red-500 rounded-sm"></span>
                        <span class="text-muted dark:text-on-dark-soft">Terlewat</span>
                    </div>
                </div>
            </div>

            @if (empty($dailyData))
                <div class="h-48 flex items-center justify-center text-xs text-muted dark:text-on-dark-soft font-body border-2 border-dashed border-hairline dark:border-white/10 rounded-lg">
                    Data harian tidak tersedia
                </div>
            @else
                <div class="pt-4">
                    <div class="relative h-56 flex items-end gap-1 sm:gap-2 border-b border-l border-hairline dark:border-white/10 px-2 pb-1 overflow-x-auto">
                        {{-- Grid Lines --}}
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pr-2">
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="border-t border-dashed border-hairline dark:border-white/5 h-0 w-full"></div>
                            <div class="h-0 w-full"></div>
                        </div>

                        @foreach ($dailyData as $day)
                            @php
                                $pctTotal = ($day['total'] / $maxTotal) * 100;
                                $pctCompleted = $day['total'] > 0 ? ($day['completed'] / $day['total']) * 100 : 0;
                                $pctSkipped   = $day['total'] > 0 ? ($day['skipped'] / $day['total']) * 100 : 0;
                            @endphp
                            <div class="flex-1 min-w-[36px] flex flex-col items-center group relative h-full justify-end z-10">
                                {{-- Tooltip --}}
                                <div class="absolute bottom-full mb-2 bg-slate-900 dark:bg-slate-800 text-white text-[10px] rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-20 shadow-lg border border-white/10">
                                    <p class="font-bold border-b border-white/10 pb-1 mb-1">{{ $day['date'] }}</p>
                                    <p class="flex justify-between gap-3"><span>Total:</span> <span class="font-bold">{{ $day['total'] }}</span></p>
                                    <p class="flex justify-between gap-3 text-green-400"><span>Selesai:</span> <span class="font-bold">{{ $day['completed'] }}</span></p>
                                    <p class="flex justify-between gap-3 text-red-400"><span>Terlewat:</span> <span class="font-bold">{{ $day['skipped'] }}</span></p>
                                </div>
                                {{-- Bars --}}
                                <div class="w-full flex gap-0.5 items-end justify-center" style="height: {{ max($pctTotal, 4) }}%">
                                    <div class="flex-1 bg-green-500 hover:bg-green-400 rounded-t-sm transition-colors" style="height: {{ max($pctCompleted, 4) }}%"></div>
                                    <div class="flex-1 bg-red-500 hover:bg-red-400 rounded-t-sm transition-colors" style="height: {{ max($pctSkipped, 4) }}%"></div>
                                </div>
                                {{-- Label --}}
                                <span class="text-[9px] font-semibold text-muted dark:text-on-dark-soft mt-1.5 font-mono truncate max-w-full text-center">
                                    {{ $day['date'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Statistik Kinerja per Loket / Booth --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-hairline dark:border-white/10">
                <h3 class="font-bold text-ink dark:text-white font-display flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Kinerja Pelayanan per Loket / Gerai
                </h3>
                <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">
                    Statistik kunjungan warga dan durasi pelayanan di setiap loket.
                </p>
            </div>
            
            @if (empty($summary['counters']))
                <div class="p-8 text-center text-xs text-muted dark:text-on-dark-soft font-body">
                    Tidak ada aktivitas pelayanan per loket pada periode ini.
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
                                <th class="px-6 py-4 text-center">Avg Waktu Layan</th>
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
                                        {{ formatDuration($counter['average_service_time_seconds'] ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm">
            <div>
                <a href="{{ route('reports.index') }}"
                    class="h-11 px-5 inline-flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-pill text-xs font-bold transition-all">
                    Kembali ke Daftar Laporan
                </a>
            </div>

            @if (!$report->isLocked())
                <div class="flex items-center gap-3">
                    {{-- Ubah --}}
                    <a href="{{ route('reports.edit', $report->id) }}"
                        class="h-11 px-5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40 rounded-pill text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Ubah Tanggal
                    </a>

                    {{-- Hapus --}}
                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="h-11 px-5 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/40 rounded-pill text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H10a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Draft
                        </button>
                    </form>

                    {{-- Kirim --}}
                    <form action="{{ route('reports.send', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin mengirim laporan ini ke Super Admin? Setelah dikirim, laporan akan dikunci dan tidak dapat diubah lagi.')">
                        @csrf
                        <button type="submit"
                            class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Ke Super Admin
                        </button>
                    </form>
                </div>
            @else
                <div class="text-xs text-muted dark:text-on-dark-soft font-body flex items-center gap-1.5 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Laporan ini telah dikirim dan dikunci dari pengeditan.</span>
                </div>
            @endif
        </div>
    </div>
@endsection
