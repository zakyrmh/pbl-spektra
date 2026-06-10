@extends('layouts.private')

@section('title', $report->title . ' — Laporan & Analitik')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16">
        
        {{-- Navigation & Header --}}
        <div class="space-y-4">
            <a href="{{ route('admin.reports.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Laporan
            </a>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold uppercase rounded bg-status-serving/10 text-status-serving tracking-wider select-none">
                            Laporan Terkunci
                        </span>
                        <span class="text-xs text-muted dark:text-on-dark-soft font-body">
                            Periode: {{ $report->start_date->format('d M Y') }} s/d {{ $report->end_date->format('d M Y') }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">{{ $report->title }}</h1>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Dibuat oleh {{ $report->creator?->name ?? 'Front Office' }} pada {{ $report->created_at->format('d F Y, H:i') }} WIB.</p>
                </div>
                
                {{-- Action Downloads --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.export.excel', $report) }}" 
                       class="h-10 px-4 bg-green-700 hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold rounded-pill text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel
                    </a>
                    <a href="{{ route('admin.reports.export.pdf', $report) }}" 
                       class="h-10 px-4 bg-status-skipped hover:bg-status-skipped/90 text-white font-bold rounded-pill text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Unduh PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
         SUMMARY METRICS
        ═══════════════════════════════════════════════════════════════ --}}
        @php
            $summary = $report->data_summary;
            $total = $summary['total_visitors'] ?? 0;
            $completed = $summary['completed_count'] ?? 0;
            $skipped = $summary['skipped_count'] ?? 0;
            $service = $summary['avg_service_time'] ?? 0;
            $waiting = $summary['avg_waiting_time'] ?? 0;
            $rate = $summary['attendance_rate'] ?? 0;
        @endphp
        
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            {{-- Total Visitors --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Antrean</span>
                <span class="text-2xl font-extrabold text-ink dark:text-white font-mono mt-2">{{ number_format($total) }}</span>
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body mt-1">Registrasi / Kunjungan</span>
            </div>
            
            {{-- Completed --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-status-serving uppercase tracking-wider font-display">Selesai Dilayani</span>
                <span class="text-2xl font-extrabold text-status-serving font-mono mt-2">{{ number_format($completed) }}</span>
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body mt-1">Berhasil dilayani loket</span>
            </div>

            {{-- Skipped --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-status-skipped uppercase tracking-wider font-display">Lewati / Skipped</span>
                <span class="text-2xl font-extrabold text-status-skipped font-mono mt-2">{{ number_format($skipped) }}</span>
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body mt-1">Pengunjung tidak hadir</span>
            </div>

            {{-- Service Time --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rata Pelayanan</span>
                <span class="text-2xl font-extrabold text-ink dark:text-white font-mono mt-2">{{ $service }}<span class="text-xs font-bold text-muted dark:text-on-dark-soft"> mnt</span></span>
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body mt-1">Lama durasi di loket</span>
            </div>

            {{-- Waiting Time --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rata Tunggu</span>
                <span class="text-2xl font-extrabold text-ink dark:text-white font-mono mt-2">{{ $waiting }}<span class="text-xs font-bold text-muted dark:text-on-dark-soft"> mnt</span></span>
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body mt-1">Waktu tunggu panggil</span>
            </div>

            {{-- Attendance Rate --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Tingkat Kehadiran</span>
                <span class="text-2xl font-extrabold text-ink dark:text-white font-mono mt-2">{{ $rate }}%</span>
                <div class="w-full bg-surface-soft dark:bg-white/10 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-primary dark:bg-accent-teal h-full rounded-full" style="width: {{ min(100, $rate) }}%"></div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
         CHARTS VISUALIZATION
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Daily Trend Chart --}}
            <div class="lg:col-span-2 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-ink dark:text-white font-display">Tren Kunjungan Harian</h3>
                    <span class="text-xs text-muted dark:text-on-dark-soft font-body">Volume antrean per hari</span>
                </div>
                <div id="chartDailyTrend" class="w-full min-h-[300px]"></div>
            </div>

            {{-- Department Distribution Chart --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-ink dark:text-white font-display">Distribusi Kunjungan per Gerai</h3>
                    <span class="text-xs text-muted dark:text-on-dark-soft font-body">Total per instansi</span>
                </div>
                <div id="chartGeraiDistribution" class="w-full min-h-[300px]"></div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
         DETAILED DATA TABLE
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs overflow-hidden space-y-4 p-6">
            <div class="flex items-center justify-between border-b border-hairline dark:border-white/10 pb-4">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-white font-display">Rincian Transaksi Antrean</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Daftar lengkap seluruh kunjungan berstatus Completed dalam rentang periode laporan.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/10">
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider w-12">No</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">No. Antrean</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">NIK</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Nama Lengkap</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Instansi/Gerai</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Layanan</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Panggil</th>
                            <th class="py-3 px-4 text-xs font-bold text-ink dark:text-white uppercase font-display tracking-wider">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline dark:divide-white/15">
                        @forelse ($queues as $index => $q)
                            @php
                                $name = $q->visitor ? $q->visitor->name : ($q->booking?->user?->name ?? '-');
                                $nik = $q->visitor ? $q->visitor->nik : ($q->booking?->user?->nik ?? '-');
                                $deptName = $q->service?->department?->name ?? '-';
                            @endphp
                            <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/2 transition-colors text-sm">
                                <td class="py-3.5 px-4 text-ink dark:text-white font-mono font-medium">{{ $queues->firstItem() + $index }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-body whitespace-nowrap">{{ $q->queue_date instanceof \Carbon\Carbon ? $q->queue_date->format('d-m-Y') : (string) $q->queue_date }}</td>
                                <td class="py-3.5 px-4 font-bold text-primary dark:text-accent-teal font-mono">{{ $q->queue_number }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-mono">{{ $nik }}</td>
                                <td class="py-3.5 px-4 font-semibold text-ink dark:text-white font-display">{{ $name }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-body">{{ $deptName }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-body">{{ $q->service?->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-mono whitespace-nowrap">{{ $q->called_at ? \Carbon\Carbon::parse($q->called_at)->format('H:i:s') : '—' }}</td>
                                <td class="py-3.5 px-4 text-muted dark:text-on-dark-soft font-mono whitespace-nowrap">{{ $q->completed_at ? \Carbon\Carbon::parse($q->completed_at)->format('H:i:s') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-muted dark:text-on-dark-soft font-body select-none">
                                    Tidak ada data transaksi antrean untuk laporan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="pt-4 border-t border-hairline dark:border-white/10">
                {{ $queues->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Data Extraction from Blade / Eloquent variables
        const dailySeriesData = @json($report->data_summary['daily_series'] ?? []);
        const categories = dailySeriesData.map(item => item.formatted_date);
        const totalSeries = dailySeriesData.map(item => item.total);
        const completedSeries = dailySeriesData.map(item => item.completed);
        const skippedSeries = dailySeriesData.map(item => item.skipped);

        const deptData = @json($report->data_summary['per_department'] ?? []);
        const deptLabels = deptData.map(item => item.inisial);
        const deptValues = deptData.map(item => item.total_queues);

        // Apex Chart: Daily Trend Options
        const chartDailyOptions = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#1B4FA8', '#059669', '#DC2626'], // primary (blue), serving (green), skipped (red)
            dataLabels: { enabled: false },
            series: [
                {
                    name: 'Total Pengunjung',
                    data: totalSeries
                },
                {
                    name: 'Selesai Dilayani',
                    data: completedSeries
                },
                {
                    name: 'Dilewati (Skipped)',
                    data: skippedSeries
                }
            ],
            xaxis: {
                categories: categories,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: true } }
            },
            legend: { 
                show: true,
                position: 'top',
                horizontalAlign: 'right',
                labels: {
                    colors: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
                }
            },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            }
        };

        const chartDaily = new ApexCharts(document.querySelector("#chartDailyTrend"), chartDailyOptions);
        chartDaily.render();

        // Apex Chart: Department Distribution Options
        const chartGeraiOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '55%',
                    distributed: true
                }
            },
            colors: ['#1B4FA8', '#29ABE2', '#059669', '#D97706', '#8B5CF6'], // Cycle through some palette colors
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff'],
                    fontWeight: 700,
                    fontSize: '11px'
                },
                formatter: function (val) {
                    return val + " Kunjungan";
                },
                offsetX: 8
            },
            series: [{
                name: 'Total Pengunjung',
                data: deptValues
            }],
            xaxis: {
                categories: deptLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { show: false }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: false } }
            },
            legend: { show: false },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            }
        };

        const chartGerai = new ApexCharts(document.querySelector("#chartGeraiDistribution"), chartGeraiOptions);
        chartGerai.render();

        // Dark Mode adaptable charts configuration updates
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            const newGridColor = isDark ? '#374151' : '#f1f5f9';
            const legendColor = isDark ? '#f3f4f6' : '#1f2937';

            chartDaily.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: newGridColor },
                legend: { labels: { colors: legendColor } }
            });

            chartGerai.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: newGridColor }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
@endpush
