@extends('layouts.private')

@section('title', 'Monitor Antrean - MPP Kota Sawahlunto')

@section('content')
<div class="space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm transition-colors duration-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span id="sync-pulse" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary dark:bg-accent-teal opacity-75"></span>
                    <span id="sync-dot" class="relative inline-flex rounded-full h-3 w-3 bg-primary dark:bg-accent-teal"></span>
                </span>
                <span class="text-xs font-semibold text-primary dark:text-accent-teal uppercase tracking-wider font-display" id="sync-status">Live Monitoring</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Monitor Kepadatan Antrean</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Pantau status kepadatan gerai instansi di Mal Pelayanan Publik secara real-time.</p>
        </div>
        <div class="flex flex-col sm:items-end gap-1.5">
            <span class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md" id="live-clock">
                Loading waktu...
            </span>
            <span class="text-[11px] text-muted dark:text-on-dark-soft font-body flex items-center gap-1">
                <svg class="w-3.5 h-3.5 animate-spin hidden" id="refresh-spinner" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Terakhir diupdate: <span id="last-update" class="font-mono">Menghubungkan...</span>
            </span>
        </div>
    </div>

    <!-- Metrik Data Global -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Menunggu -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Menunggu</p>
                    <h3 id="metric-waiting" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono transition-all duration-300">{{ $metrics['total_waiting'] }}</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Warga dalam antrean tunggu</p>
                </div>
                <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg border border-status-waiting/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-status-waiting"></div>
        </div>

        <!-- Card 2: Total Dilayani -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Dilayani</p>
                    <h3 id="metric-serving" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono transition-all duration-300">{{ $metrics['total_serving'] }}</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Sedang dilayani di loket aktif</p>
                </div>
                <div class="p-3 bg-status-serving/10 text-status-serving rounded-lg border border-status-serving/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-status-serving"></div>
        </div>

        <!-- Card 3: Rerata Waktu Tunggu -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rerata Waktu Tunggu</p>
                    <h3 id="metric-wait-time" class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono transition-all duration-300"><span id="avg-time-val">{{ $metrics['average_wait_time'] }}</span> <span class="text-lg font-bold text-muted dark:text-on-dark-soft font-display">menit</span></h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Estimasi tunggu per tiket</p>
                </div>
                <div class="p-3 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-primary"></div>
        </div>
    </div>

    <!-- Tabel Kepadatan Gerai -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm transition-colors duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-hairline dark:border-white/10">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display text-lg">Status Kepadatan Gerai</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Tingkat kesibukan loket gerai berdasarkan antrean aktif saat ini.</p>
            </div>
            <div class="flex items-center gap-4">
                <!-- Search bar -->
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted dark:text-on-dark-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search-input" placeholder="Cari Instansi / Inisial..." class="w-full h-9 pl-9 pr-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-xs text-ink dark:text-white rounded-md placeholder:text-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-hairline dark:border-white/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                        <th class="py-3.5 px-6 font-display">Nama Instansi / Gerai</th>
                        <th class="py-3.5 px-4 font-display text-center">Inisial</th>
                        <th class="py-3.5 px-4 font-display text-center">Antrean Menunggu</th>
                        <th class="py-3.5 px-4 font-display text-center">Sedang Dilayani</th>
                        <th class="py-3.5 px-6 font-display text-center">Tingkat Kepadatan</th>
                    </tr>
                </thead>
                <tbody id="monitor-table-body" class="text-xs divide-y divide-hairline dark:divide-white/5">
                    @forelse ($departments as $dept)
                        <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors duration-150 dept-row" data-name="{{ strtolower($dept->name) }}" data-inisial="{{ strtolower($dept->inisial) }}">
                            <td class="py-3 px-6">
                                <div class="font-bold text-ink dark:text-white font-display text-sm">{{ $dept->name }}</div>
                                <div class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 line-clamp-1 font-body">{{ $dept->description }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal text-center text-sm">
                                {{ $dept->inisial }}
                            </td>
                            <td class="py-3 px-4 font-mono text-ink dark:text-white text-center font-bold text-sm">
                                {{ $dept->waitingCount }}
                            </td>
                            <td class="py-3 px-4 font-mono text-ink dark:text-white text-center font-bold text-sm">
                                {{ $dept->servingCount }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold font-display {{ $dept->densityClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dept->densityDot }} animate-pulse"></span>
                                    {{ $dept->density }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-muted dark:text-on-dark-soft font-body">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <span>Tidak ada data instansi terdaftar.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live Clock function
    function updateClock() {
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const clockEl = document.getElementById('live-clock');
        if (clockEl) {
            clockEl.innerText = `${dateStr} | ${timeStr}`;
        }
    }

    // Polling logic
    function pollQueueData() {
        const spinner = document.getElementById('refresh-spinner');
        const updateText = document.getElementById('last-update');
        const syncPulse = document.getElementById('sync-pulse');
        const syncDot = document.getElementById('sync-dot');
        const syncStatus = document.getElementById('sync-status');

        if (spinner) spinner.classList.remove('hidden');

        // Fetch JSON from the same endpoint
        fetch('{{ route("admin.fo.monitor") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            // Update global metrics
            document.getElementById('metric-waiting').innerText = data.metrics.total_waiting;
            document.getElementById('metric-serving').innerText = data.metrics.total_serving;
            document.getElementById('avg-time-val').innerText = data.metrics.average_wait_time;

            // Update table body
            const tbody = document.getElementById('monitor-table-body');
            
            if (data.departments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-12 text-center text-muted dark:text-on-dark-soft font-body">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span>Tidak ada data instansi terdaftar.</span>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                data.departments.forEach(dept => {
                    html += `
                        <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors duration-150 dept-row" data-name="${dept.name.toLowerCase()}" data-inisial="${dept.inisial.toLowerCase()}">
                            <td class="py-3 px-6">
                                <div class="font-bold text-ink dark:text-white font-display text-sm">${dept.name}</div>
                                <div class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 line-clamp-1 font-body">${dept.description || ''}</div>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal text-center text-sm">
                                ${dept.inisial}
                            </td>
                            <td class="py-3 px-4 font-mono text-ink dark:text-white text-center font-bold text-sm">
                                ${dept.waiting_count}
                            </td>
                            <td class="py-3 px-4 font-mono text-ink dark:text-white text-center font-bold text-sm">
                                ${dept.serving_count}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold font-display ${dept.density_class}">
                                    <span class="w-1.5 h-1.5 rounded-full ${dept.density_dot} animate-pulse"></span>
                                    ${dept.density}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                
                // Re-apply search filter if active
                filterTable();
            }

            // Update timestamp
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            if (updateText) updateText.innerText = timeStr;

            // Flash sync dot green for success
            if (syncPulse && syncDot && syncStatus) {
                syncPulse.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75";
                syncDot.className = "relative inline-flex rounded-full h-3 w-3 bg-green-500";
                syncStatus.innerText = "Live Monitoring (Aktif)";
                
                setTimeout(() => {
                    syncPulse.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-primary dark:bg-accent-teal opacity-75";
                    syncDot.className = "relative inline-flex rounded-full h-3 w-3 bg-primary dark:bg-accent-teal";
                    syncStatus.innerText = "Live Monitoring";
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Polling error:', error);
            if (updateText) updateText.innerText = 'Koneksi Terputus';
            if (syncPulse && syncDot && syncStatus) {
                syncPulse.className = "absolute inline-flex h-full w-full rounded-full bg-status-skipped opacity-75";
                syncDot.className = "relative inline-flex rounded-full h-3 w-3 bg-status-skipped";
                syncStatus.innerText = "Koneksi Terputus";
            }
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
    }

    // Search filter function
    function filterTable() {
        const searchVal = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.dept-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const inisial = row.getAttribute('data-inisial');
            
            if (name.includes(searchVal) || inisial.includes(searchVal)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize clock
        updateClock();
        setInterval(updateClock, 1000);

        // Set last updated time initially
        const now = new Date();
        document.getElementById('last-update').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        // Start polling every 5 seconds
        setInterval(pollQueueData, 5000);

        // Bind search input event
        document.getElementById('search-input').addEventListener('input', filterTable);
    });
</script>
@endsection
