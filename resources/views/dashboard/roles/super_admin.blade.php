{{-- Super Admin Dashboard --}}
<div class="space-y-6 pb-12">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider font-display">Live Monitoring Active</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Pusat Kendali & Kinerja MPP</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Pemantauan real-time arus kunjungan, efisiensi loket, dan performa gerai instansi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="window.location.reload()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-pill text-xs font-semibold border border-hairline dark:border-white/10 bg-canvas dark:bg-surface-dark-elevated hover:bg-surface-soft dark:hover:bg-white/5 text-ink dark:text-white transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                <svg class="w-4 h-4 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18" />
                </svg>
                <span>Refresh Data</span>
            </button>
        </div>
    </div>

    <!-- Top Cards Widget (Metrik Utama) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Kunjungan -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Kunjungan Hari Ini</p>
                    <h3 id="statTotalKunjungan" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">{{ $todayKunjunganCount }}</h3>
                </div>
                <div class="p-3 bg-primary/10 text-primary dark:text-accent-teal rounded-lg border border-primary/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10">
                <span class="{{ $kunjunganPercentage['is_increase'] ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400' }} font-bold flex items-center gap-0.5 font-mono">
                    @if ($kunjunganPercentage['is_increase'])
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    @endif
                    <span>{{ $kunjunganPercentage['formatted'] }}</span>
                </span>
                <span class="font-body">vs. harian</span>
            </div>
        </div>

        <!-- Card 2: Menunggu Konfirmasi FO -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Menunggu Konfirmasi FO</p>
                    <h3 id="statMenungguFO" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">{{ $menungguFoCount }}</h3>
                </div>
                <div class="p-3 bg-status-waiting/10 text-status-waiting rounded-lg border border-status-waiting/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10 font-body">
                <span class="{{ $foStatus['color'] }} font-bold flex items-center gap-0.5">
                    <span class="w-2 h-2 rounded-full {{ $foStatus['bg_dot'] }} {{ $foStatus['label'] !== 'Lancar' ? 'animate-pulse' : '' }}"></span>
                    <span>{{ $foStatus['label'] }}</span>
                </span>
                <span>Antrean di fo</span>
            </div>
        </div>

        <!-- Card 3: Sedang Dilayani -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Antrean Aktif di Gerai</p>
                    <h3 id="statSedangDilayani" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">{{ $totalAntreanGerai }}</h3>
                </div>
                <div class="p-3 bg-status-serving/10 text-status-serving rounded-lg border border-status-serving/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10 font-body">
                <span class="font-semibold text-ink dark:text-white font-mono">{{ $waitingCount }}</span>
                <span>menunggu,</span>
                <span class="font-semibold text-ink dark:text-white font-mono">{{ $servingCount }}</span>
                <span>sedang dilayani</span>
            </div>
        </div>

        <!-- Card 4: Gerai Aktif -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 bottom-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none translate-x-4 translate-y-4">
                <svg class="w-32 h-32 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                </svg>
            </div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Total Gerai Aktif</p>
                    <h3 id="statGeraiAktif" class="text-3xl font-extrabold text-ink dark:text-white mt-2 transition-all font-mono">{{ $activeGerai }} <span class="text-lg font-medium text-muted">/ {{ $totalGerai }}</span></h3>
                </div>
                <div class="p-3 bg-accent-teal/10 text-accent-teal rounded-lg border border-accent-teal/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-muted dark:text-on-dark-soft relative z-10">
                <div class="w-full bg-surface-soft dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-accent-teal h-full rounded-full" style="width: {{ $geraiPercentage }}%"></div>
                </div>
                <span class="shrink-0 text-[10px] font-bold text-accent-teal">{{ $geraiPercentage }}% Buka</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Line Chart: Tren Kedatangan -->
        <div class="lg:col-span-7 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between min-w-0">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display">Tren Kedatangan Pengunjung</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Membandingkan arus masuk Booking Online vs. On-site per jam</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-primary dark:bg-blue-500"></span>
                        <span class="text-[10px] text-muted font-bold uppercase font-display">Online</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-accent-teal"></span>
                        <span class="text-[10px] text-muted font-bold uppercase font-display">On-site</span>
                    </div>
                </div>
            </div>
            <div class="relative w-full h-80 min-h-[320px] overflow-hidden">
                <div id="chartTrenKedatangan" class="absolute inset-0 w-full h-full"></div>
            </div>
        </div>

        <!-- Bar Chart: Top Gerai -->
        <div class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col min-w-0">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Top Gerai Terpadat</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Instansi dengan volume antrean tertinggi hari ini</p>
            </div>
            <div class="flex-1 flex items-center justify-center relative w-full h-80 min-h-[320px] overflow-hidden">
                <div id="chartTopGerai" class="absolute inset-0 w-full h-full"></div>
            </div>
        </div>
    </div>

    <!-- Table & FO Widget Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Tabel Pemantauan Live Gerai -->
        <div class="lg:col-span-8 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-white font-display">Pemantauan Live Gerai</h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Metrik real-time keaktifan loket dan beban antrean instansi</p>
                </div>
                <span class="bg-primary/10 text-primary dark:text-accent-teal text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse font-display">
                    Auto Refreshing
                </span>
            </div>

            <div class="overflow-x-auto -mx-6">
                <table id="tblLiveGerai" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                            <th class="py-3 px-6">Nama Instansi</th>
                            <th class="py-3 px-4">Loket</th>
                            <th class="py-3 px-4">Menunggu</th>
                            <th class="py-3 px-4">Rerata Pelayanan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-hairline dark:divide-white/5">
                        @forelse ($liveDepartments as $dept)
                            @php
                                // P5: Cache koleksi antrean sekali — semua filter di bawah beroperasi
                                // pada objek Collection in-memory yang sama tanpa akses properti berulang.
                                $allQueues = $dept->queues;

                                $inisial         = $dept->inisial ?: substr($dept->name, 0, 6);
                                $waitingCountRow  = $allQueues->where('status', 'Checked-In')->count();
                                $servingCountRow  = $allQueues->where('status', 'Serving')->count();
                                $totalLiveAntrean = $waitingCountRow + $servingCountRow;

                                // P3: Hitung rata-rata waktu pelayanan dari transaksi yang sudah selesai.
                                // Guard negatif: hanya hitung jika completed_at >= called_at.
                                // Fallback null (bukan angka fiktif) jika belum ada data Completed hari ini.
                                $completedQueues  = $allQueues
                                    ->where('status', 'Completed')
                                    ->whereNotNull('called_at')
                                    ->whereNotNull('completed_at');
                                $totalServiceTime = 0;
                                $countCompleted   = 0;
                                foreach ($completedQueues as $q) {
                                    $called    = \Carbon\Carbon::parse($q->called_at);
                                    $completed = \Carbon\Carbon::parse($q->completed_at);
                                    if ($completed->greaterThanOrEqualTo($called)) {
                                        $totalServiceTime += $completed->diffInMinutes($called);
                                        $countCompleted++;
                                    }
                                }
                                // null = belum ada transaksi selesai (ditampilkan sebagai '—' di UI)
                                $avgServiceTime = $countCompleted > 0
                                    ? (int) round($totalServiceTime / $countCompleted)
                                    : null;

                                // Status kepadatan loket
                                if ($totalLiveAntrean >= 15) {
                                    $statusLabel = 'Padat';
                                    $statusClass = 'bg-status-skipped/10 dark:bg-status-skipped/25 text-status-skipped border border-status-skipped/25';
                                    $dotClass    = 'bg-status-skipped animate-pulse';
                                  } elseif ($totalLiveAntrean >= 4) {
                                      $statusLabel = 'Lancar';
                                      $statusClass = 'bg-status-serving/10 dark:bg-status-serving/25 text-status-serving border border-status-serving/25';
                                      $dotClass    = 'bg-status-serving';
                                  } else {
                                      $statusLabel = 'Kosong';
                                      $statusClass = 'bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft border border-hairline dark:border-white/5';
                                      $dotClass    = 'bg-muted';
                                  }
                              @endphp
                              <tr data-instansi="{{ $dept->name }}" class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                                  <td class="py-4 px-6 font-bold text-ink dark:text-white">
                                      <div class="flex items-center gap-3 font-display">
                                          <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0 border border-primary/20">
                                              {{ strtoupper($inisial) }}
                                          </div>
                                          <span>{{ $dept->name }}</span>
                                      </div>
                                  </td>
                                  <td class="py-4 px-4 font-medium text-muted dark:text-on-dark-soft font-body">
                                      Loket {{ $dept->nomor_loket }}
                                      @if($dept->is_open)
                                          <span class="text-[10px] bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 px-1.5 py-0.5 rounded border border-green-200/30">Aktif</span>
                                      @else
                                          <span class="text-[10px] bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-1.5 py-0.5 rounded border border-rose-200/30">Tutup</span>
                                      @endif
                                  </td>
                                  <td class="py-4 px-4 font-bold text-ink dark:text-white font-mono">
                                      <span class="queue-count">{{ $totalLiveAntrean }}</span> <span class="text-xs font-normal text-muted">orang</span>
                                  </td>
                                <td class="py-4 px-4 font-body">
                                    @if ($avgServiceTime !== null)
                                        <span class="text-muted dark:text-on-dark-soft">
                                            {{ $avgServiceTime }} Menit / Orang
                                        </span>
                                    @else
                                        {{-- P3: Tampilkan tanda strip jika belum ada transaksi selesai hari ini. --}}
                                        <span class="text-muted/40 dark:text-on-dark-soft/30 italic text-xs" title="Belum ada transaksi selesai hari ini">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="status-badge {{ $statusClass }} px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 font-display">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button onclick="tegurGerai('{{ $dept->name }}')" 
                                            class="btn-tegur px-3 py-1.5 {{ $totalLiveAntrean >= 15 ? 'bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:text-rose-700 border border-rose-100 dark:border-rose-900/30 cursor-pointer' : 'text-gray-400 dark:text-gray-500 border border-hairline dark:border-gray-700 cursor-not-allowed' }} rounded-lg text-xs font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-status-skipped/50"
                                            {{ $totalLiveAntrean >= 15 ? '' : 'disabled' }}>
                                        Tegur
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-muted font-body">
                                    Tidak ada data instansi terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Widget Booking Online (FO Efficiency) -->
        <div class="lg:col-span-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Alur Booking Online & FO</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Efisiensi petugas Front Office memproses verifikasi kode unik</p>
            </div>

            <!-- Gauge / Speed visual indicator -->
            <div class="py-6 flex flex-col items-center justify-center relative">
                @php
                    $percent = min(max(($avgFoCheckInTime - 0.5) / 5.5, 0), 1);
                    $dashoffset = 251.2 - (188.4 * $percent);
                @endphp
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <!-- SVG Gauge Arc -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle -->
                        <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-surface-strong dark:text-gray-750" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-linecap="round" />
                        <!-- Progress Circle -->
                        <circle id="gaugeProgressArc" cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-emerald-500" fill="transparent" stroke-dasharray="251.2" style="stroke-dashoffset: {{ $dashoffset }}" stroke-linecap="round" />
                    </svg>
                    <!-- Inner Content -->
                    <div class="absolute flex flex-col items-center justify-center text-center">
                        <span id="valCheckInTime" class="text-3xl font-extrabold text-ink dark:text-white font-mono">{{ number_format($avgFoCheckInTime, 1) }}</span>
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase font-display">Menit / Tiket</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    @if ($avgFoCheckInTime < 3.0)
                        <span id="badgeCheckInStatus" class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider font-display border border-emerald-200/50">
                            Efisien / Lancar
                        </span>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft mt-2 max-w-[220px] mx-auto leading-relaxed font-body">
                            Target check-in FO: <span class="font-bold text-ink dark:text-white">&lt; 3.0 menit</span>. Saat ini tidak terjadi penumpukan (bottleneck).
                        </p>
                    @elseif ($avgFoCheckInTime <= 5.0)
                        <span id="badgeCheckInStatus" class="bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider font-display border border-amber-200/50">
                            Menumpuk (Sedang)
                        </span>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft mt-2 max-w-[220px] mx-auto leading-relaxed font-body">
                            Waktu verifikasi meningkat. Petugas FO disarankan mempercepat validasi kode unik.
                        </p>
                    @else
                        <span id="badgeCheckInStatus" class="bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider font-display border border-rose-200/50">
                            BOTTLENECK!
                        </span>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft mt-2 max-w-[220px] mx-auto leading-relaxed font-body">
                            Terjadi antrean panjang di loket depan! Segera lakukan penambahan petugas bantuan FO.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Live feed events (simulates Laravel Reverb) -->
            <div class="border-t border-hairline dark:border-white/10 pt-4 mt-auto">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">Live Activity Feed</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                </div>
                <div id="liveActivityFeed" class="space-y-2 max-h-[110px] overflow-y-auto pr-1 text-[11px] text-muted dark:text-on-dark-soft font-mono">
                    @forelse ($liveLogs as $log)
                        <div class="flex items-start gap-1">
                            <span class="text-[10px] text-muted">{{ $log->created_at->format('H:i') }}</span>
                            <span class="text-primary dark:text-accent-teal font-semibold shrink-0">&bull; {{ $log->action }}:</span>
                            <span class="text-muted dark:text-on-dark-soft leading-tight">{{ $log->description }}</span>
                        </div>
                    @empty
                        <div class="flex items-start gap-1">
                            <span class="text-[10px] text-muted">{{ now()->format('H:i') }}</span>
                            <span class="text-ink dark:text-white font-semibold shrink-0">&bull; System:</span>
                            <span class="text-muted dark:text-on-dark-soft">WebSocket monitoring aktif.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apex Chart: Tren Kedatangan Pengunjung
        const onlineData = @json($chartTrenData['online']);
        const onsiteData = @json($chartTrenData['onsite']);
        
        // Filter out null/undefined values to compute max robustly
        const validValues = [...onlineData, ...onsiteData].filter(v => v !== null && v !== undefined);
        const maxVal = validValues.length > 0 ? Math.max(...validValues) : 0;
        
        let tickAmount = 5;
        let yaxisMax = undefined;
        
        if (maxVal === 0) {
            yaxisMax = 5;
            tickAmount = 5;
        } else if (maxVal <= 5) {
            yaxisMax = maxVal + 1;
            tickAmount = maxVal + 1;
        } else {
            yaxisMax = Math.ceil((maxVal + 1) / 5) * 5;
            tickAmount = 5;
        }

        const chartTrenOptions = {
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
            colors: [
                document.documentElement.classList.contains('dark') ? '#3b82f6' : '#1B4FA8', 
                '#29ABE2'
            ], // primary (or blue-500 in dark mode), accent-teal
            dataLabels: { enabled: false },
            series: [{
                name: 'Booking Online',
                data: onlineData
            }, {
                name: 'On-site (Langsung)',
                data: onsiteData
            }],
            xaxis: {
                categories: @json($chartTrenData['categories']),
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
                min: 0,
                max: yaxisMax,
                tickAmount: tickAmount,
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            markers: {
                size: 4,
                colors: [
                    document.documentElement.classList.contains('dark') ? '#3b82f6' : '#1B4FA8', 
                    '#29ABE2'
                ],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 6,
                }
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: true } },
                padding: {
                    right: 45,
                    left: 10
                }
            },
            legend: { show: false },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return val + " Pengunjung";
                    }
                }
            }
        };
        const chartTren = new ApexCharts(document.querySelector("#chartTrenKedatangan"), chartTrenOptions);
        chartTren.render();

        // Apex Chart: Top Gerai Terpadat
        const topValues = @json($chartTopGeraiData['values']);
        const topLabels = @json($chartTopGeraiData['labels']);
        const maxTopVal = Math.max(...topValues);

        const chartTopOptions = {
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
                    borderRadiusApplication: 'end',
                    barHeight: '55%',
                    distributed: false
                }
            },
            colors: ['#1B4FA8'], // Base color
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#29ABE2'], // Gradient end color (accent-teal)
                    inverseColors: false,
                    opacityFrom: 0.95,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                offsetX: 12,
                style: {
                    colors: [document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827'],
                    fontWeight: 700,
                    fontSize: '11px'
                },
                formatter: function (val, opt) {
                    return val > 0 ? val + " Antrean" : "";
                }
            },
            series: [{
                name: 'Volume Antrean',
                data: topValues
            }],
            xaxis: {
                categories: topLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    show: false
                },
                max: Math.max(maxTopVal, 5) // Set dynamic max to avoid giant bars for small numbers
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
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                y: {
                    formatter: function(val) {
                        return val + " Antrean";
                    }
                }
            }
        };
        const chartTop = new ApexCharts(document.querySelector("#chartTopGerai"), chartTopOptions);
        chartTop.render();

        // Dark mode adaptability for charts
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            chartTren.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: isDark ? '#374151' : '#f1f5f9' },
                tooltip: { theme: isDark ? 'dark' : 'light' },
                colors: [isDark ? '#3b82f6' : '#1B4FA8', '#29ABE2'],
                markers: {
                    colors: [isDark ? '#3b82f6' : '#1B4FA8', '#29ABE2']
                }
            });
            chartTop.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: isDark ? '#374151' : '#f1f5f9' },
                tooltip: { theme: isDark ? 'dark' : 'light' },
                dataLabels: {
                    style: {
                        colors: [isDark ? '#ffffff' : '#111827']
                    }
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        // Elements
        const liveActivityFeed = document.getElementById('liveActivityFeed');

        function addActivityFeed(user, action, timestamp = null) {
            const now = timestamp || new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const eventDiv = document.createElement('div');
            eventDiv.className = 'flex items-start gap-1 opacity-0 translate-y-1 transition-all duration-300';
            eventDiv.innerHTML = `
                <span class="text-[10px] text-muted">${hours}:${minutes}</span>
                <span class="text-primary dark:text-accent-teal font-semibold shrink-0">&bull; ${user}:</span>
                <span class="text-ink dark:text-white leading-tight">${action}</span>
            `;
            
            liveActivityFeed.prepend(eventDiv);
            setTimeout(() => {
                eventDiv.classList.remove('opacity-0', 'translate-y-1');
            }, 50);

            // Limit feed list to 6 items
            while (liveActivityFeed.children.length > 6) {
                liveActivityFeed.removeChild(liveActivityFeed.lastChild);
            }
        }

        function createToast(title, message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border shadow-lg border-hairline dark:border-white/10 flex gap-3 pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-300';
            
            let iconColor = 'text-primary dark:text-accent-teal';
            let iconSvg = `
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `;
            
            if (type === 'success') {
                iconColor = 'text-green-500';
                iconSvg = `
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;
            } else if (type === 'warning') {
                iconColor = 'text-status-waiting';
                iconSvg = `
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                `;
            }

            toast.innerHTML = `
                <div class="shrink-0 ${iconColor}">
                    ${iconSvg}
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-ink dark:text-white font-display">${title}</p>
                    <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-muted hover:text-ink dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            container.appendChild(toast);
            
            // Animation in
            setTimeout(() => {
                toast.classList.remove('translate-x-12', 'opacity-0');
            }, 50);

            // Animation out
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        window.tegurGerai = function(GeraiName) {
            createToast(
                `Nudge Dikirim ke ${GeraiName}`,
                `Peringatan kepadatan antrean telah diteruskan ke Admin Gerai.`,
                'warning'
            );
            addActivityFeed('Super Admin', `Mengirim teguran antrean padat ke gerai ${GeraiName}`);
            
            // Highlight the table row
            const row = document.querySelector(`tr[data-instansi="${GeraiName}"]`);
            if (row) {
                row.classList.add('bg-rose-50/50', 'dark:bg-rose-950/20', 'animate-pulse');
                setTimeout(() => {
                    row.classList.remove('bg-rose-50/50', 'dark:bg-rose-950/20', 'animate-pulse');
                }, 2000);
            }
        };


    });
</script>
@endpush
