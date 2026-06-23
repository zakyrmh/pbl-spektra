@php
    $schedules = $schedules ?? collect();
@endphp
@if (isset($isStatsDashboard) && $isStatsDashboard)
    <div class="space-y-6 pb-16">
        {{-- Header Banner --}}
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-white font-display tracking-tight">Dashboard Gerai
                    </h1>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">
                        Ringkasan statistik real-time dan analisis pelayanan untuk Instansi <span
                            class="font-semibold text-primary dark:text-accent-teal">{{ Auth::user()->department ? Auth::user()->department->name : '-' }}</span>
                    </p>
                </div>
                <!-- Buka/Tutup Gerai Toggle Switch -->
                <div
                    class="flex items-center gap-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 p-2.5 rounded-lg shrink-0 font-body">
                    <span class="text-xs font-bold text-ink dark:text-white font-display">Status Gerai:</span>
                    <span id="geraiStatusText"
                        class="px-2.5 py-0.5 rounded-pill text-xs font-bold {{ $isGeraiOpen ? 'bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300' }}">
                        {{ $isGeraiOpen ? 'BUKA' : 'TUTUP' }}
                    </span>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="geraiToggleCheckbox" class="sr-only peer"
                            {{ $isGeraiOpen ? 'checked' : '' }} onchange="confirmToggleGerai(this)">
                        <div
                            class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600 dark:peer-checked:bg-green-500">
                        </div>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.daftar-tunggu') }}"
                    class="h-11 px-5 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill flex items-center gap-2 text-sm shadow-md transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Daftar Tunggu Gerai
                </a>
                <a href="{{ route('admin.papan-panggil') }}"
                    class="h-11 px-5 bg-canvas border border-hairline text-ink dark:text-white dark:border-white/15 hover:bg-surface-soft dark:hover:bg-white/10 font-semibold rounded-pill flex items-center gap-2 text-sm transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Papan Panggil (Loket)
                </a>
            </div>
        </div>

        {{-- Cards Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 font-body">
            <!-- Total Antrean -->
            <div
                class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden">
                <div class="space-y-1">
                    <span
                        class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total
                        Antrean</span>
                    <span
                        class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $totalAntrean }}</span>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft block">Kuota terpakai hari ini</span>
                </div>
                <div class="p-3 bg-surface-soft dark:bg-white/5 text-primary dark:text-accent-teal rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    </svg>
                </div>
            </div>

            <!-- Sisa Antrean Menunggu -->
            <div
                class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-status-waiting uppercase tracking-wider font-display">Sisa
                        Antrean</span>
                    <span
                        class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $sisaAntrean }}</span>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft block">Status Checked-In</span>
                </div>
                <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Sukses Dilayani -->
            <div
                class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-status-serving uppercase tracking-wider font-display">Sukses
                        Dilayani</span>
                    <span
                        class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $suksesDilayani }}</span>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft block">Status Completed</span>
                </div>
                <div class="p-3 bg-status-serving/10 text-status-serving rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Terlewat -->
            <div
                class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-status-skipped uppercase tracking-wider font-display">Antrean
                        Batal</span>
                    <span
                        class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $terlewat }}</span>
                    <span class="text-[10px] text-muted dark:text-on-dark-soft block">Status Cancelled</span>
                </div>
                <div class="p-3 bg-status-skipped/10 text-status-skipped rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Main Grid Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 font-body">
            <!-- Left: Table (Spans 7 cols) -->
            <div
                class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs space-y-4">
                <h3
                    class="text-lg font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2">
                    Kemajuan Kuota Sesi & Status Operasional</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/15 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">
                                <th class="p-4">Layanan</th>
                                <th class="p-4">Sesi</th>
                                <th class="p-4">Kemajuan Kuota</th>
                                <th class="p-4">Status Sesi</th>
                                <th class="p-4 text-center">Buka/Tutup Sesi</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-hairline dark:divide-white/10 text-body-sm text-ink dark:text-on-dark-soft">
                            @forelse($schedules as $sched)
                                <tr class="hover:bg-black/2 dark:hover:bg-white/2 transition-colors">
                                    <td class="p-4 font-semibold">{{ $sched->purpose ?? '-' }}</td>
                                    <td class="p-4 font-mono font-bold">{{ $sched->session_name }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-mono font-bold text-primary dark:text-accent-teal">{{ $sched->quota_used }}</span>
                                            <span class="text-xs text-muted">/ {{ $sched->quota_total }}</span>
                                            @php
                                                $percentage =
                                                    $sched->quota_total > 0
                                                        ? min(
                                                            100,
                                                            round(($sched->quota_used / $sched->quota_total) * 100),
                                                        )
                                                        : 0;
                                            @endphp
                                            <div
                                                class="w-24 bg-surface-soft dark:bg-white/10 h-2 rounded-full overflow-hidden shrink-0">
                                                <div class="bg-primary dark:bg-accent-teal h-full"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span id="scheduleStatusLabel-{{ $sched->id }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-caption font-semibold {{ $sched->is_open ? 'bg-status-serving/10 text-status-serving border border-status-serving/15' : 'bg-status-skipped/10 text-status-skipped border border-status-skipped/15' }}">
                                            <span
                                                class="w-2 h-2 rounded-full {{ $sched->is_open ? 'bg-status-serving' : 'bg-status-skipped' }}"></span>
                                            {{ $sched->is_open ? 'Buka' : 'Tutup' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <!-- Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer select-none">
                                            <input type="checkbox" class="sr-only peer"
                                                {{ $sched->is_open ? 'checked' : '' }}
                                                onchange="toggleSchedule({{ $sched->id }})">
                                            <div
                                                class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-accent-teal">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-muted dark:text-on-dark-soft">
                                        Tidak ada jadwal pelayanan terdaftar hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Chart (Spans 5 cols) -->
            <div
                class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs space-y-4">
                <h3
                    class="text-lg font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2">
                    Tren Sukses vs Batal Hari Ini</h3>
                <div class="relative h-72">
                    <canvas id="hourlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div id="statsToastContainer"
        class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleSchedule(scheduleId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';

            fetch(`/admin/schedules/${scheduleId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const label = document.getElementById(`scheduleStatusLabel-${scheduleId}`);
                        if (data.is_open) {
                            label.className =
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-caption font-semibold bg-status-serving/10 text-status-serving border border-status-serving/15';
                            label.innerHTML = '<span class="w-2 h-2 rounded-full bg-status-serving"></span>Buka';
                            showStatsToast('Sukses', 'Sesi pelayanan berhasil dibuka.', 'success');
                        } else {
                            label.className =
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-caption font-semibold bg-status-skipped/10 text-status-skipped border border-status-skipped/15';
                            label.innerHTML = '<span class="w-2 h-2 rounded-full bg-status-skipped"></span>Tutup';
                            showStatsToast('Sukses', 'Sesi pelayanan berhasil ditutup.', 'warning');
                        }
                    } else {
                        showStatsToast('Gagal', data.message || 'Gagal mengubah status sesi.', 'warning');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showStatsToast('Error', 'Terjadi kesalahan sistem.', 'danger');
                });
        }

        function showStatsToast(title, message, type) {
            const container = document.getElementById('statsToastContainer');
            if (!container) return;

            let borderClass = 'border-gray-500';
            let bgDot = 'bg-gray-500';
            if (type === 'success') {
                borderClass = 'border-green-500';
                bgDot = 'bg-green-500';
            } else if (type === 'warning') {
                borderClass = 'border-yellow-500';
                bgDot = 'bg-yellow-500';
            } else if (type === 'danger') {
                borderClass = 'border-red-500';
                bgDot = 'bg-red-500';
            }

            const toast = document.createElement('div');
            toast.className =
                `bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white rounded-lg border-l-4 border-solid ${borderClass} p-4 shadow-md flex items-start gap-3 w-80 pointer-events-auto transition-all duration-300 opacity-0 translate-y-2`;
            toast.innerHTML = `
            <span class="w-2.5 h-2.5 rounded-full ${bgDot} mt-1.5 shrink-0"></span>
            <div class="flex-grow">
                <strong class="text-xs font-bold block">${title}</strong>
                <span class="text-caption text-muted dark:text-on-dark-soft mt-0.5 block">${message}</span>
            </div>
        `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('hourlyTrendChart').getContext('2d');
            const chartData = @json($chartTrenData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.categories,
                    datasets: [{
                            label: 'Sukses Dilayani',
                            data: chartData.sukses,
                            backgroundColor: '#059669',
                            borderRadius: 4
                        },
                        {
                            label: 'Pelayanan Batal',
                            data: chartData.batal,
                            backgroundColor: '#DC2626',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#6B7280'
                            },
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6B7280'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#6B7280',
                                font: {
                                    family: 'Geist Sans'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- Custom Modal Konfirmasi Buka/Tutup Gerai -->
    <div id="confirmGeraiModal"
        class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-xs hidden"
        role="dialog" aria-modal="true">
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-8 border border-hairline dark:border-white/10 shadow-xl max-w-md w-full space-y-6 transform scale-95 opacity-0 transition-all duration-300"
            id="confirmGeraiModalContent">
            <div class="flex justify-between items-start border-b border-hairline dark:border-white/10 pb-4">
                <h3 class="text-xl font-bold text-ink dark:text-white font-display" id="confirmGeraiTitle">Konfirmasi
                    Ubah Status</h3>
                <button type="button" onclick="closeConfirmGeraiModal()"
                    class="text-muted hover:text-ink dark:hover:text-white cursor-pointer transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="text-body-md text-muted dark:text-on-dark-soft font-body" id="confirmGeraiMessage">
                Apakah Anda yakin ingin mengubah status operasional gerai ini?
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-hairline dark:border-white/10">
                <button type="button" onclick="closeConfirmGeraiModal()"
                    class="h-11 px-5 font-semibold text-ink dark:text-white bg-canvas hover:bg-surface-soft dark:bg-white/5 dark:hover:bg-white/10 rounded-pill border border-hairline dark:border-white/15 flex items-center transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" id="confirmGeraiSubmitBtn"
                    class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer">
                    Ya, Ubah Status
                </button>
            </div>
        </div>
    </div>

    <script>
        let pendingToggleCheckbox = null;
        let currentToggleState = false;

        function confirmToggleGerai(checkbox) {
            pendingToggleCheckbox = checkbox;
            currentToggleState = checkbox.checked;

            // Revert visually until confirmed
            checkbox.checked = !currentToggleState;

            const modal = document.getElementById('confirmGeraiModal');
            const content = document.getElementById('confirmGeraiModalContent');
            const title = document.getElementById('confirmGeraiTitle');
            const message = document.getElementById('confirmGeraiMessage');
            const btn = document.getElementById('confirmGeraiSubmitBtn');

            if (currentToggleState) {
                title.innerText = 'Buka Operasional Gerai?';
                message.innerText =
                    'Semua sesi layanan untuk instansi Anda hari ini akan diaktifkan kembali dan warga dapat melakukan check-in.';
                btn.innerText = 'Ya, Buka Gerai';
                btn.className =
                    'h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer';
            } else {
                title.innerText = 'Tutup Operasional Gerai?';
                message.innerText =
                    'Semua sesi layanan untuk instansi Anda hari ini akan dinonaktifkan (ditutup). Petugas tidak dapat melayani antrean baru hingga gerai dibuka kembali.';
                btn.innerText = 'Ya, Tutup Gerai';
                btn.className =
                    'h-11 px-6 bg-status-skipped hover:bg-status-skipped/90 text-white font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped transition-all cursor-pointer';
            }

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeConfirmGeraiModal() {
            const modal = document.getElementById('confirmGeraiModal');
            const content = document.getElementById('confirmGeraiModalContent');

            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingToggleCheckbox = null;
            }, 300);
        }

        document.getElementById('confirmGeraiSubmitBtn').addEventListener('click', function() {
            if (!pendingToggleCheckbox) return;

            const targetState = currentToggleState;
            const checkbox = pendingToggleCheckbox;

            // Close modal
            closeConfirmGeraiModal();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';

            fetch('/admin/schedules/toggle-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_open: targetState ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        checkbox.checked = targetState;

                        const statusText = document.getElementById('geraiStatusText');
                        if (targetState) {
                            statusText.className =
                                'px-2.5 py-0.5 rounded-pill text-xs font-bold bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300';
                            statusText.innerText = 'BUKA';
                            showStatsToast('Sukses', 'Operasional gerai berhasil dibuka.', 'success');
                        } else {
                            statusText.className =
                                'px-2.5 py-0.5 rounded-pill text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300';
                            statusText.innerText = 'TUTUP';
                            showStatsToast('Sukses', 'Operasional gerai berhasil ditutup.', 'warning');
                        }

                        // Update individual session toggles
                        const schedulesCount = {{ $schedules->count() }};
                        if (schedulesCount > 0) {
                            @foreach ($schedules as $sched)
                                const subLabel = document.getElementById(
                                    'scheduleStatusLabel-{{ $sched->id }}');
                                if (subLabel) {
                                    const subRow = subLabel.closest('tr');
                                    if (subRow) {
                                        const subSwitch = subRow.querySelector('input[type="checkbox"]');
                                        if (subSwitch) subSwitch.checked = targetState;
                                    }
                                    if (targetState) {
                                        subLabel.className =
                                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-caption font-semibold bg-status-serving/10 text-status-serving border border-status-serving/15';
                                        subLabel.innerHTML =
                                            '<span class="w-2 h-2 rounded-full bg-status-serving"></span>Buka';
                                    } else {
                                        subLabel.className =
                                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill text-caption font-semibold bg-status-skipped/10 text-status-skipped border border-status-skipped/15';
                                        subLabel.innerHTML =
                                            '<span class="w-2 h-2 rounded-full bg-status-skipped"></span>Tutup';
                                    }
                                }
                            @endforeach
                        }
                    } else {
                        showStatsToast('Gagal', data.message || 'Gagal mengubah status gerai.', 'warning');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showStatsToast('Error', 'Terjadi kesalahan sistem.', 'danger');
                });
        });
    </script>
@else
    {{-- Admin Gerai Dashboard --}}
    @if (isset($noCounter) && $noCounter)
        <div
            class="flex flex-col items-center justify-center min-h-[60vh] text-center bg-canvas dark:bg-surface-dark-elevated p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-4">
            <div
                class="w-20 h-20 bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center border border-amber-200/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Gerai/Instansi Belum Ditugaskan</h1>
            <p class="text-muted dark:text-on-dark-soft max-w-md font-body">Akun Anda belum ditugaskan ke instansi
                gerai mana pun. Silakan hubungi Super Admin untuk memetakan akun Anda ke instansi pelayanan.</p>
        </div>
    @else
        @php
            $status = \Illuminate\Support\Facades\Cache::get("loket_status_{$department->id}", 'aktif');
        @endphp
        <div class="space-y-6 pb-16">
            <!-- Header Banner -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        @php
                            $badgeClass =
                                'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200/50';
                            $dotClass = 'bg-green-500';
                            $statusLabel = 'Loket Buka (Aktif)';

                            if ($status === 'istirahat') {
                                $badgeClass =
                                    'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200/50';
                                $dotClass = 'bg-amber-500';
                                $statusLabel = 'Loket Istirahat';
                            } elseif ($status === 'nonaktif') {
                                $badgeClass =
                                    'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200/50';
                                $dotClass = 'bg-rose-500';
                                $statusLabel = 'Loket Tutup';
                            }
                        @endphp
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $badgeClass }}"
                            id="loketStatusBadge">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}" id="loketStatusDot"></span>
                            <span id="loketStatusText">{{ $statusLabel }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $department->is_open ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200/50' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200/50' }}"
                            id="instansiStatusBadge">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $department->is_open ? 'bg-green-500' : 'bg-rose-500' }}"
                                id="instansiStatusDot"></span>
                            <span id="instansiStatusText">Instansi:
                                {{ $department->is_open ? 'Buka' : 'Tutup' }}</span>
                        </span>
                        <span class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Loket
                            {{ $department->nomor_loket }} — {{ $department->name }}</span>
                    </div>
                    <h2 class="text-2xl font-bold text-ink dark:text-white mt-2 font-display">Papan Panggil & Layanan
                        Gerai</h2>
                    <p class="text-sm text-muted dark:text-on-dark-soft font-body">Panggil nomor antrean dan selesaikan pelayanan warga.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    {{-- Status Instansi --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Status
                            Instansi:</span>
                        <div
                            class="inline-flex rounded-lg border border-hairline dark:border-white/10 p-1 bg-surface-soft dark:bg-white/5">
                            <button type="button" onclick="toggleInstansiStatus()" id="btnInstansiStatus"
                                class="px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer {{ $department->is_open ? 'bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs' : 'bg-canvas dark:bg-surface-dark-elevated text-rose-600 dark:text-rose-400 shadow-xs' }}">
                                {{ $department->is_open ? 'BUKA (Aktif)' : 'TUTUP (Terkunci)' }}
                            </button>
                        </div>
                    </div>

                    {{-- Status Loket --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-muted dark:text-on-dark-soft font-semibold font-display">Status
                            Loket:</span>
                        <div
                            class="inline-flex rounded-lg border border-hairline dark:border-white/10 p-1 bg-surface-soft dark:bg-white/5">
                            <button type="button" onclick="setLoketStatus('aktif')" id="btnStatusBuka"
                                class="px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer {{ $status === 'aktif' ? 'bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs' : 'text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5' }}">Buka</button>
                            <button type="button" onclick="setLoketStatus('istirahat')" id="btnStatusIstirahat"
                                class="px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer {{ $status === 'istirahat' ? 'bg-canvas dark:bg-surface-dark-elevated text-amber-600 dark:text-amber-400 shadow-xs' : 'text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5' }}">Istirahat</button>
                            <button type="button" onclick="setLoketStatus('nonaktif')" id="btnStatusTutup"
                                class="px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer {{ $status === 'nonaktif' ? 'bg-canvas dark:bg-surface-dark-elevated text-rose-600 dark:text-rose-400 shadow-xs' : 'text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5' }}">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Call Controls & Active Citizen (Main Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left: Pusat Kendali Utama Panggilan (Spans 6 cols) -->
                <div
                    class="lg:col-span-6 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="pb-3 border-b border-hairline dark:border-white/10">
                            <h3 class="font-bold text-ink dark:text-white font-display">Pusat Kendali Panggilan</h3>
                            <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Gunakan panel ini
                                untuk mengelola antrean loket Anda saat ini.</p>
                        </div>

                        <!-- Giant Active Queue Display -->
                        <div
                            class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                            <span
                                class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Nomor
                                Sedang Dilayani</span>
                            <span id="activeCallNumber"
                                class="text-7xl md:text-8xl font-extrabold text-primary dark:text-accent-teal font-mono tracking-tight leading-none my-2 transition-all">{{ $activeQueue ? $activeQueue->queue_number : '-' }}</span>
                            <span id="activeCallStatus"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary dark:text-accent-teal rounded-full text-xs font-bold border border-primary/20 font-display">
                                {{ $activeQueue ? 'Sedang Dilayani' : 'Belum Ada Panggilan' }}
                            </span>
                            <!-- Background watermarked icon -->
                            <div
                                class="absolute right-0 bottom-0 opacity-[0.02] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Call Control Action Buttons -->
                    <div class="mt-8 space-y-3">
                        @php
                            $isNextDisabled = $status !== 'aktif';
                        @endphp
                        <button type="button" onclick="callNextQueue()" id="btnCallNext"
                            {{ $isNextDisabled ? 'disabled' : '' }}
                            class="w-full h-12 font-semibold rounded-pill text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-2 shadow-md {{ $isNextDisabled ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-primary hover:bg-primary-hover text-white' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                            Panggil Berikutnya (Next Queue)
                        </button>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="recallActiveQueue()" id="btnRecall"
                                class="h-11 bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                                Panggil Ulang (Recall)
                            </button>
                            <button type="button" onclick="skipActiveQueue()" id="btnSkip"
                                class="h-11 bg-status-skipped/10 hover:bg-status-skipped/20 text-status-skipped font-semibold rounded-pill text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50 cursor-pointer flex items-center justify-center gap-1.5 border border-status-skipped/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Lewati Antrean (Skip)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Informasi Pengunjung Aktif & Berkas Checklist (Spans 6 cols) -->
                <div
                    class="lg:col-span-6 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="pb-3 border-b border-hairline dark:border-white/10">
                            <h3 class="font-bold text-ink dark:text-white font-display">Informasi Pengunjung Aktif</h3>
                            <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Detail warga yang
                                sedang dilayani pada antrean aktif.</p>
                        </div>

                        <!-- Citizen Metadata Panel -->
                        <div
                            class="space-y-3 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-4 text-xs">
                            <div class="grid grid-cols-3 gap-1">
                                <span class="text-muted dark:text-on-dark-soft">Nama Warga:</span>
                                <span id="citizenName"
                                    class="col-span-2 font-bold text-ink dark:text-white text-right">
                                    {{ $activeQueue ? ($activeQueue->user ? $activeQueue->user->name : 'Warga') : '-' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-1">
                                <span class="text-muted dark:text-on-dark-soft">NIK Warga:</span>
                                <span id="citizenNik"
                                    class="col-span-2 font-mono text-ink dark:text-white text-right">
                                    {{ $activeQueue ? ($activeQueue->user ? $activeQueue->user->nik : '-') : '-' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-1">
                                <span class="text-muted dark:text-on-dark-soft">Layanan:</span>
                                <span id="citizenService"
                                    class="col-span-2 font-bold text-primary dark:text-accent-teal text-right">
                                    {{ $activeQueue ? $activeQueue->purpose ?? 'Layanan Umum' : '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Checklist Dokumen Persyaratan -->
                        <div class="space-y-3 mt-4">
                            <h4 class="text-xs font-bold text-ink dark:text-white font-display">Checklist Berkas Persyaratan</h4>
                            <div id="documentChecklist" class="space-y-2">
                                @if($activeQueue)
                                    @php
                                        $purpose = strtolower($activeQueue->purpose ?? '');
                                        if (str_contains($purpose, 'kk') || str_contains($purpose, 'keluarga')) {
                                            $docs = ['Fotokopi Kartu Keluarga (KK)', 'Surat Pengantar RT/RW Keterangan Rusak/Hilang', 'KTP Asli Pemohon'];
                                        } elseif (str_contains($purpose, 'ktp') || str_contains($purpose, 'identitas')) {
                                            $docs = ['Fotokopi Kartu Keluarga (KK)', 'Surat Pengantar RT/RW Keterangan Rusak/Hilang', 'KTP Lama / Surat Kehilangan Kepolisian'];
                                        } elseif (str_contains($purpose, 'akta') || str_contains($purpose, 'lahir')) {
                                            $docs = ['Surat Keterangan Lahir dari Bidan/Rumah Sakit', 'Fotokopi Buku Nikah Orang Tua', 'Fotokopi KK Orang Tua'];
                                        } else {
                                            $docs = ['KTP Asli / NIK Pengunjung', 'Berkas Persyaratan Layanan Utama'];
                                        }
                                    @endphp
                                    @foreach($docs as $doc)
                                        <label class="flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer">
                                            <input type="checkbox" class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                            <span class="text-xs text-ink dark:text-white font-medium">{{ $doc }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-xs text-muted dark:text-on-dark-soft italic">
                                        Belum ada antrean yang dipanggil.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Complete Service Trigger & Forward -->
                        <div class="flex flex-col gap-2 mt-6">
                            <button type="button" onclick="completeActiveService()" id="btnComplete"
                                class="w-full h-11 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-green-500/50 cursor-pointer">
                                Selesaikan Pelayanan &amp; Tandai Sukses
                            </button>
                            <button type="button" onclick="openForwardModal()" id="btnForward"
                                class="w-full h-10 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-semibold rounded-md text-xs transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-blue-500/50 cursor-pointer flex items-center justify-center gap-1.5 border border-blue-500/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                                Oper Antrean ke Instansi Lain
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Metrik Internal Gerai & Delay List -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Internal Stats Widget (Spans 4 cols) -->
                <div
                    class="lg:col-span-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-4">
                    <h3
                        class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2">
                        Metrik Pelayanan Hari Ini</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-center">
                            <span
                                class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Sisa
                                Antrean</span>
                            <p id="geraiStatRemaining"
                                class="text-3xl font-extrabold text-primary dark:text-accent-teal mt-1 font-mono">
                                {{ $remainingCount }}</p>
                            <span class="text-[10px] text-muted dark:text-on-dark-soft font-body">orang
                                menunggu</span>
                        </div>
                        <div
                            class="p-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-center">
                            <span
                                class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Rerata
                                Layanan</span>
                            <p class="text-3xl font-extrabold text-status-serving mt-1 font-mono">
                                {{ $avgServiceTime }}</p>
                            <span class="text-[10px] text-muted dark:text-on-dark-soft font-body">menit /
                                warga</span>
                        </div>
                    </div>

                    <div
                        class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-3 text-xs text-muted dark:text-on-dark-soft leading-relaxed font-body">
                        💡 <b>Tips Kecepatan:</b> Mintalah berkas fisik warga sebelum memencet tombol mulai untuk
                        menghemat estimasi waktu pelayanan rata-rata.
                    </div>
                </div>

                <!-- Delayed/Skipped List Table (Spans 8 cols) -->
                <div
                    class="lg:col-span-8 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <h3
                            class="font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 mb-4">
                            Daftar Antrean Tertunda / Terlewati (Skipped)</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                                        <th class="py-2.5 px-4">Kode Antrean</th>
                                        <th class="py-2.5 px-4">Nama Warga</th>
                                        <th class="py-2.5 px-4">Layanan</th>
                                        <th class="py-2.5 px-4">Status</th>
                                        <th class="py-2.5 px-4 text-center">Aksi Panggil Balik</th>
                                    </tr>
                                </thead>
                                <tbody id="geraiSkipListBody"
                                    class="text-xs divide-y divide-hairline dark:divide-white/5">
                                    @foreach ($skippedQueues as $sq)
                                        <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors"
                                            data-skipped-ticket="{{ $sq->queue_number }}">
                                            <td class="py-3 px-4 font-mono font-bold text-status-skipped">
                                                {{ $sq->queue_number }}</td>
                                            <td class="py-3 px-4 text-ink dark:text-white font-medium">
                                                {{ $sq->user ? $sq->user->name : 'Warga' }}
                                            </td>
                                            <td class="py-3 px-4 text-muted dark:text-on-dark-soft">
                                                {{ $sq->purpose }}
                                            </td>
                                            <td class="py-3 px-4 text-status-skipped font-bold">Terlewat</td>
                                            <td class="py-3 px-4 text-center">
                                                <button type="button"
                                                    onclick="recallSkipped({{ $sq->id }}, '{{ $sq->queue_number }}', '{{ $sq->user ? $sq->user->name : 'Warga' }}', '{{ $sq->purpose }}')"
                                                    class="px-3 py-1.5 bg-primary/10 hover:bg-primary text-primary hover:text-white dark:text-accent-teal dark:hover:text-white dark:bg-accent-teal/10 rounded-md font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                                    Panggil Balik
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($skippedQueues->isEmpty())
                                        <tr id="noSkippedRow">
                                            <td colspan="5"
                                                class="py-4 text-center text-muted dark:text-on-dark-soft italic">
                                                Belum ada antrean terlewat hari ini.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sound alerts and Notification Toast Container -->
            <audio id="bellChime" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav"
                preload="auto"></audio>
            <div id="toastContainer"
                class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

            <!-- JavaScript State Control for Gerai -->
            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    '{{ csrf_token() }}';

                // Gerai database state
                let geraiState = {
                    currentNumber: {{ $activeQueue ? (int) preg_replace('/[^0-9]/', '', $activeQueue->queue_number) : 0 }},
                    remainingQueues: {{ $remainingCount }},
                    completedCount: {{ $completedCount }},
                    status: '{{ $status }}', // 'aktif', 'istirahat', 'nonaktif'
                    activeQueueId: {{ $activeQueue ? $activeQueue->id : 'null' }},
                    queueList: [
                        @foreach ($waitingQueues as $q)
                            {
                                id: {{ $q->id }},
                                code: '{{ $q->queue_number }}',
                                name: '{{ $q->user ? $q->user->name : 'Warga' }}',
                                nik: '{{ $q->user ? $q->user->nik : '-' }}',
                                service: '{{ $q->purpose }}',
                                docs: {!! $q->purpose && str_contains(strtolower($q->purpose), 'kk')
                                    ? '["KK", "Pengantar"]'
                                    : ($q->purpose && str_contains(strtolower($q->purpose), 'ktp')
                                        ? '["KK", "Pengantar", "KTP Lama"]'
                                        : '["KTP Lama"]') !!}
                            },
                        @endforeach
                    ]
                };

                // Toggle Instansi status via AJAX
                function toggleInstansiStatus() {
                    fetch('{{ route('gerai.department.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const btn = document.getElementById('btnInstansiStatus');
                                const badge = document.getElementById('instansiStatusBadge');
                                const dot = document.getElementById('instansiStatusDot');
                                const text = document.getElementById('instansiStatusText');

                                if (data.is_open) {
                                    btn.innerText = 'BUKA (Aktif)';
                                    btn.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs';

                                    badge.className =
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50';
                                    dot.className = 'w-1.5 h-1.5 rounded-full bg-green-500';
                                    text.innerText = 'Instansi: Buka';

                                    createToast('Status Instansi', 'Instansi Anda berhasil dibuka kembali.', 'success');
                                } else {
                                    btn.innerText = 'TUTUP (Terkunci)';
                                    btn.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all focus-visible:outline-none cursor-pointer bg-canvas dark:bg-surface-dark-elevated text-rose-600 dark:text-rose-400 shadow-xs';

                                    badge.className =
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 rounded-full text-xs font-bold border border-rose-200/50';
                                    dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
                                    text.innerText = 'Instansi: Tutup';

                                    createToast('Status Instansi', 'Instansi Anda berhasil ditutup.', 'warning');
                                }
                            } else {
                                createToast('Gagal', data.message || 'Gagal mengubah status instansi.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Eror', 'Terjadi kesalahan sistem.', 'danger');
                        });
                }

                // Set Loket status via AJAX
                function setLoketStatus(newStatus) {
                    fetch('{{ route('gerai.status') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                geraiState.status = data.status;

                                const badge = document.getElementById('loketStatusBadge');
                                const dot = document.getElementById('loketStatusDot');
                                const text = document.getElementById('loketStatusText');

                                const btnBuka = document.getElementById('btnStatusBuka');
                                const btnIstirahat = document.getElementById('btnStatusIstirahat');
                                const btnTutup = document.getElementById('btnStatusTutup');

                                // Reset button active classes
                                [btnBuka, btnIstirahat, btnTutup].forEach(b => {
                                    b.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all text-muted dark:text-on-dark-soft hover:bg-canvas/50 dark:hover:bg-white/5 focus-visible:outline-none cursor-pointer';
                                });

                                if (data.status === 'aktif') {
                                    badge.className =
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50';
                                    dot.className = 'w-1.5 h-1.5 rounded-full bg-green-500';
                                    text.innerText = 'Loket Buka (Aktif)';
                                    btnBuka.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-green-600 dark:text-green-400 shadow-xs focus-visible:outline-none cursor-pointer';

                                    document.getElementById('btnCallNext').removeAttribute('disabled');
                                    document.getElementById('btnCallNext').className =
                                        'w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer flex items-center justify-center gap-2 shadow-md';
                                    createToast('Status Loket', 'Loket dibuka kembali. Selamat melayani!', 'success');
                                } else if (data.status === 'istirahat') {
                                    badge.className =
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-200/50';
                                    dot.className = 'w-1.5 h-1.5 rounded-full bg-amber-500';
                                    text.innerText = 'Loket Sedang Istirahat';
                                    btnIstirahat.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-amber-600 dark:text-amber-400 shadow-xs focus-visible:outline-none cursor-pointer';

                                    document.getElementById('btnCallNext').setAttribute('disabled', 'true');
                                    document.getElementById('btnCallNext').className =
                                        'w-full h-12 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-semibold rounded-pill text-sm transition-all cursor-not-allowed flex items-center justify-center gap-2';
                                    createToast('Status Loket', 'Loket sedang beristirahat sementara.', 'warning');
                                } else { // nonaktif
                                    badge.className =
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 rounded-full text-xs font-bold border border-rose-200/50';
                                    dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
                                    text.innerText = 'Loket Tutup';
                                    btnTutup.className =
                                        'px-3 py-1.5 text-xs font-bold rounded-md transition-all bg-canvas dark:bg-surface-dark-elevated text-rose-600 dark:text-rose-400 shadow-xs focus-visible:outline-none cursor-pointer';

                                    document.getElementById('btnCallNext').setAttribute('disabled', 'true');
                                    document.getElementById('btnCallNext').className =
                                        'w-full h-12 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-semibold rounded-pill text-sm transition-all cursor-not-allowed flex items-center justify-center gap-2';
                                    createToast('Status Loket', 'Loket telah ditutup.', 'warning');
                                }
                            } else {
                                createToast('Gagal', data.message || 'Gagal mengubah status loket.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Koneksi Error', 'Terjadi masalah jaringan.', 'warning');
                        });
                }

                // Sound chime trigger
                function playBeep() {
                    const sound = document.getElementById('bellChime');
                    if (sound) {
                        sound.currentTime = 0;
                        sound.play().catch(e => console.log('Audio blocked.'));
                    }
                }

                // Call Next Queue via AJAX
                function callNextQueue() {
                    if (geraiState.status !== 'aktif') {
                        alert("Status loket sedang istirahat/tutup! Silakan ubah ke Buka terlebih dahulu.");
                        return;
                    }

                    fetch('{{ route('gerai.call-next') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const q = data.queue;
                                geraiState.activeQueueId = q.id;
                                geraiState.currentNumber = parseInt(q.queue_number.split('-')[1]);

                                // Hapus satu antrean dari state list menunggu
                                geraiState.queueList = geraiState.queueList.filter(item => item.id !== q.id);
                                geraiState.remainingQueues = geraiState.queueList.length;

                                // Play chime
                                playBeep();

                                // Update DOM
                                document.getElementById('activeCallNumber').innerText = q.queue_number;
                                document.getElementById('activeCallStatus').innerText = 'Sedang Dilayani';
                                document.getElementById('citizenName').innerText = q.user ? q.user.name : 'Warga';
                                document.getElementById('citizenNik').innerText = q.user ? q.user.nik : '-';
                                document.getElementById('citizenService').innerText = q.purpose || '';
                                document.getElementById('geraiStatRemaining').innerText = geraiState.remainingQueues;


                                createToast('Panggilan Sukses',
                                    `Memanggil nomor ${q.queue_number} (${q.user ? q.user.name : 'Warga'}) ke Loket.`,
                                    'success');
                            } else {
                                createToast('Antrean Kosong', data.message || 'Tidak ada antrean tersisa.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Error', 'Gagal memanggil antrean berikutnya.', 'warning');
                        });
                }

                // Recall Active Queue via AJAX
                function recallActiveQueue() {
                    if (!geraiState.activeQueueId) {
                        createToast('Gagal', 'Tidak ada antrean aktif untuk dipanggil ulang.', 'warning');
                        return;
                    }

                    fetch(`/api/queues/${geraiState.activeQueueId}/call`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                playBeep();
                                const activeNum = document.getElementById('activeCallNumber').innerText;
                                const activeName = document.getElementById('citizenName').innerText;
                                createToast('Panggilan Ulang', `Mengulang panggilan nomor ${activeNum} (${activeName}).`,
                                    'success');
                            } else {
                                createToast('Gagal', data.message || 'Gagal memanggil ulang.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Error', 'Gagal memproses panggilan ulang.', 'warning');
                        });
                }

                // Skip Active Queue via AJAX
                function skipActiveQueue() {
                    if (!geraiState.activeQueueId) {
                        createToast('Gagal', 'Tidak ada antrean aktif untuk dilewati.', 'warning');
                        return;
                    }

                    const activeNum = document.getElementById('activeCallNumber').innerText;
                    const name = document.getElementById('citizenName').innerText;
                    const service = document.getElementById('citizenService').innerText;

                    fetch(`/api/queues/${geraiState.activeQueueId}/skip`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                createToast('Antrean Dilewati', `Tiket ${activeNum} dilewati dan masuk daftar terlewat.`,
                                    'warning');

                                // Tambahkan tiket terlewat ke tabel skipped
                                const tbody = document.getElementById('geraiSkipListBody');

                                // Hapus row fallback jika ada
                                const fallbackRow = document.getElementById('noSkippedRow');
                                if (fallbackRow) fallbackRow.remove();

                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors';
                                tr.setAttribute('data-skipped-ticket', activeNum);

                                const qId = geraiState.activeQueueId;
                                tr.innerHTML = `
                    <td class="py-3 px-4 font-mono font-bold text-status-skipped">${activeNum}</td>
                    <td class="py-3 px-4 text-ink dark:text-white font-medium">${name}</td>
                    <td class="py-3 px-4 text-muted dark:text-on-dark-soft">${service}</td>
                    <td class="py-3 px-4 text-status-skipped font-bold">Terlewat</td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" onclick="recallSkipped(${qId}, '${activeNum}', '${name}', '${service}')" class="px-3 py-1.5 bg-primary/10 hover:bg-primary text-primary hover:text-white dark:text-accent-teal dark:hover:text-white dark:bg-accent-teal/10 rounded-md font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                            Panggil Balik
                        </button>
                    </td>
                `;
                                tbody.insertBefore(tr, tbody.firstChild);

                                // Reset Display ke idle
                                clearActiveDisplay();

                                // Cari apakah masih ada antrean berikutnya di lokal
                                if (geraiState.queueList.length > 0) {
                                    callNextQueue();
                                }
                            } else {
                                createToast('Gagal', data.message || 'Gagal melewati antrean.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Error', 'Terjadi kesalahan sistem.', 'warning');
                        });
                }

                // Selesaikan Pelayanan (Complete) via AJAX
                function completeActiveService() {
                    if (!geraiState.activeQueueId) {
                        createToast('Gagal', 'Tidak ada pelayanan aktif untuk diselesaikan.', 'warning');
                        return;
                    }

                    const activeNum = document.getElementById('activeCallNumber').innerText;
                    const name = document.getElementById('citizenName').innerText;

                    fetch(`/api/queues/${geraiState.activeQueueId}/finish`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                createToast('Pelayanan Selesai', `Tiket ${activeNum} (${name}) dinyatakan SUKSES dilayani.`,
                                    'success');

                                // Increment completedCount and update DOM
                                geraiState.completedCount++;
                                document.getElementById('geraiStatCompleted').innerText = geraiState.completedCount;


                                // Clear active display
                                clearActiveDisplay();

                                // Panggil antrean berikutnya jika ada
                                if (geraiState.queueList.length > 0) {
                                    callNextQueue();
                                }
                            } else {
                                createToast('Gagal', data.message || 'Gagal menyelesaikan pelayanan.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Error', 'Gagal memproses penyelesaian layanan.', 'warning');
                        });
                }

                // Panggil Balik antrean yang diskipped via AJAX
                function recallSkipped(queueId, code, name, service) {
                    if (geraiState.status !== 'aktif') {
                        alert("Status loket sedang istirahat/tutup! Silakan ubah ke Buka terlebih dahulu.");
                        return;
                    }

                    fetch(`/api/queues/${queueId}/call`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const q = data.queue;
                                geraiState.activeQueueId = q.id;
                                geraiState.currentNumber = parseInt(q.queue_number.split('-')[1]);

                                playBeep();

                                // Update DOM active display
                                document.getElementById('activeCallNumber').innerText = q.queue_number;
                                document.getElementById('activeCallStatus').innerText = 'Sedang Dilayani';
                                document.getElementById('citizenName').innerText = q.user ? q.user.name : 'Warga';
                                document.getElementById('citizenNik').innerText = q.user ? q.user.nik : '-';
                                document.getElementById('citizenService').innerText = q.purpose || '';

                                // Hapus baris dari skipped table
                                const row = document.querySelector(`tr[data-skipped-ticket="${q.queue_number}"]`);
                                if (row) {
                                    row.remove();
                                }

                                // Cek jika tabel skipped sekarang kosong
                                const tbody = document.getElementById('geraiSkipListBody');
                                if (tbody.children.length === 0) {
                                    const tr = document.createElement('tr');
                                    tr.id = 'noSkippedRow';
                                    tr.innerHTML =
                                        '<td colspan="5" class="py-4 text-center text-muted dark:text-on-dark-soft italic">Belum ada antrean terlewat hari ini.</td>';
                                    tbody.appendChild(tr);
                                }


                                createToast('Panggil Balik',
                                    `Memanggil kembali warga terlewat: ${q.queue_number} (${q.user ? q.user.name : 'Warga'})`,
                                    'success');
                            } else {
                                createToast('Gagal', data.message || 'Gagal memanggil balik.', 'warning');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            createToast('Error', 'Gagal memanggil balik antrean.', 'warning');
                        });
                }

                // Helper untuk mereset display ke status kosong/idle
                function clearActiveDisplay() {
                    geraiState.activeQueueId = null;
                    document.getElementById('activeCallNumber').innerText = '-';
                    document.getElementById('activeCallStatus').innerText = 'Belum Ada Panggilan';
                    document.getElementById('citizenName').innerText = 'Tidak ada pengunjung';
                    document.getElementById('citizenNik').innerText = '-';
                    document.getElementById('citizenService').innerText = '-';

                    const list = document.getElementById('documentChecklist');
                    if (list) {
                        list.innerHTML =
                            `<div class="text-center py-4 bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg text-xs text-muted dark:text-on-dark-soft italic">Belum ada antrean yang dipanggil.</div>`;
                    }
                }

                // Reset Checklist UI
                function resetChecklist(docs = []) {
                    const list = document.getElementById('documentChecklist');
                    if (!list) return;
                    list.innerHTML = '';

                    docs.forEach(doc => {
                        const label = document.createElement('label');
                        label.className =
                            'flex items-center gap-3 p-3 bg-surface-soft dark:bg-white/5 hover:bg-surface-strong dark:hover:bg-white/10 rounded-md border border-hairline dark:border-white/5 transition-all cursor-pointer';

                        label.innerHTML = `
                <input type="checkbox" class="w-4.5 h-4.5 border border-hairline dark:border-white/10 rounded-md bg-canvas text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                <span class="text-xs text-ink dark:text-white font-medium">${doc}</span>
            `;
                        list.appendChild(label);
                    });
                }

                // Toast Alert
                function createToast(title, message, type = 'success') {
                    const container = document.getElementById('toastContainer');
                    if (!container) return;

                    const toast = document.createElement('div');
                    let borderClr = 'border-green-500';
                    let bgClr = 'bg-white dark:bg-gray-800';
                    let iconHtml = '';

                    if (type === 'success') {
                        borderClr = 'border-l-4 border-green-500';
                        iconHtml =
                            `<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                    } else if (type === 'warning') {
                        borderClr = 'border-l-4 border-amber-500';
                        iconHtml =
                            `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
                    } else {
                        borderClr = 'border-l-4 border-blue-500';
                        iconHtml =
                            `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                    }

                    toast.className =
                        `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
                    toast.innerHTML = `
            <div class="shrink-0">${iconHtml}</div>
            <div class="flex-grow">
                <h5 class="text-xs font-bold text-ink dark:text-white font-display">${title}</h5>
                <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;

                    container.appendChild(toast);

                    setTimeout(() => {
                        toast.classList.remove('translate-y-2', 'opacity-0');
                    }, 50);

                    setTimeout(() => {
                        toast.classList.add('opacity-0', 'translate-y-[-10px]');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 4000);
                }


                // ─── FORWARD QUEUE MODAL ──────────────────────────────────────────────────
                function openForwardModal() {
                    const queueId = currentQueueId;
                    if (!queueId) {
                        createToast('Perhatian', 'Tidak ada antrean aktif yang dapat diopersikan.', 'warning');
                        return;
                    }
                    document.getElementById('forwardModal').classList.remove('hidden');
                    document.getElementById('forwardModal').classList.add('flex');
                }

                function closeForwardModal() {
                    document.getElementById('forwardModal').classList.add('hidden');
                    document.getElementById('forwardModal').classList.remove('flex');
                    document.getElementById('forwardDeptSelect').value = '';
                }

                async function confirmForwardQueue() {
                    const queueId = currentQueueId;
                    const deptId = document.getElementById('forwardDeptSelect').value;
                    if (!deptId) {
                        createToast('Perhatian', 'Pilih instansi tujuan terlebih dahulu.', 'warning');
                        return;
                    }

                    const btn = document.getElementById('btnConfirmForward');
                    btn.disabled = true;
                    btn.textContent = 'Memproses...';

                    try {
                        const res = await fetch(`/api/queues/${queueId}/forward`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                target_department_id: parseInt(deptId)
                            }),
                        });
                        const data = await res.json();
                        if (data.success) {
                            closeForwardModal();
                            createToast('Antrean Dioper', data.message, 'success');
                            setTimeout(() => window.location.reload(), 1400);
                        } else {
                            createToast('Gagal', data.message ?? 'Gagal mengoper antrean.', 'error');
                            btn.disabled = false;
                            btn.textContent = 'Konfirmasi Oper';
                        }
                    } catch (e) {
                        createToast('Error', 'Terjadi kesalahan jaringan.', 'error');
                        btn.disabled = false;
                        btn.textContent = 'Konfirmasi Oper';
                    }
                }
            </script>

            {{-- ─── FORWARD QUEUE MODAL ────────────────────────────────────────────────── --}}
            <div id="forwardModal"
                class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
                <div
                    class="bg-canvas dark:bg-surface-dark-elevated w-full max-w-md mx-4 rounded-2xl shadow-2xl border border-hairline dark:border-white/15 p-6 space-y-5 font-body">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-white font-display">Oper Antrean ke
                                Instansi Lain</h3>
                            <p class="text-xs text-muted dark:text-on-dark-soft mt-1">Pilih instansi tujuan. Warga akan
                                mendapatkan notifikasi perpindahan loket.</p>
                        </div>
                        <button onclick="closeForwardModal()"
                            class="text-muted dark:text-on-dark-soft hover:text-ink dark:hover:text-white transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div>
                        <label for="forwardDeptSelect"
                            class="block text-sm font-semibold text-ink dark:text-white mb-2">Instansi Tujuan</label>
                        <select id="forwardDeptSelect"
                            class="w-full bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-lg px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 transition-all">
                            <option value="">-- Pilih Instansi Tujuan --</option>
                            @isset($activeDepartments)
                                @foreach ($activeDepartments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} (Loket
                                        {{ $dept->nomor_loket }})</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button onclick="closeForwardModal()" type="button"
                            class="h-10 px-5 text-sm font-semibold text-muted dark:text-on-dark-soft hover:bg-black/5 dark:hover:bg-white/5 rounded-pill border border-hairline dark:border-white/10 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button onclick="confirmForwardQueue()" id="btnConfirmForward" type="button"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-pill text-sm transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-blue-500/50 cursor-pointer">
                            Konfirmasi Oper
                        </button>
                    </div>
                </div>
            </div>
    @endif
@endif
