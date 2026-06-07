@extends('layouts.private')

@section('title', 'Panggilan Antrean FO - MPP Kota Sawahlunto')

@section('content')
<div class="space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <span class="text-xs font-semibold text-primary dark:text-accent-teal uppercase tracking-wider font-display">Front Office Counter</span>
            </div>
            <h2 class="text-2xl font-bold text-ink dark:text-white mt-1 font-display">Operasional Panggilan Antrean</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body">Manajemen pemanggilan nomor antrean internal Front Office secara real-time.</p>
        </div>
        <div class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md" id="fo-live-clock">
            Loading waktu...
        </div>
    </div>

    <!-- Statistik Ringkas Loket FO -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Menunggu -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Menunggu</p>
                <h3 class="text-3xl font-extrabold text-status-waiting mt-1 font-mono">{{ $totalWaiting }}</h3>
                <p class="text-xs text-muted mt-0.5 font-body">Warga mengantre</p>
            </div>
            <div class="p-3 bg-amber-500/10 text-status-waiting rounded-lg border border-amber-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Selesai Dilayani -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Selesai Dilayani</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">{{ $totalServed }}</h3>
                <p class="text-xs text-muted mt-0.5 font-body">Berhasil diproses</p>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Terlewat / Skip -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-5 rounded-xl border border-hairline dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Terlewat</p>
                <h3 class="text-3xl font-extrabold text-status-skipped mt-1 font-mono">{{ $totalSkipped }}</h3>
                <p class="text-xs text-muted mt-0.5 font-body">Dilewati / tidak hadir</p>
            </div>
            <div class="p-3 bg-rose-500/10 text-status-skipped rounded-lg border border-rose-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Tampilan Utama Pemanggilan (Nomor Antrean Ekstra Besar) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Panel Monitor & Aksi (Spans 8 cols) -->
        <div class="lg:col-span-8 bg-canvas dark:bg-surface-dark-elevated p-8 rounded-xl border border-hairline dark:border-white/10 shadow-md flex flex-col justify-between min-h-[480px] relative overflow-hidden">
            <!-- Background Accent -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 dark:bg-accent-teal/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between z-10">
                <div>
                    <span class="text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">LOKET LAYANAN AKTIF</span>
                    <h3 class="text-lg font-bold text-ink dark:text-white mt-0.5 font-display">{{ $counter->name }}</h3>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary dark:text-accent-teal rounded-full text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>FO-Counter</span>
                </div>
            </div>

            <!-- Visual Nomor Antrean Ekstra Besar -->
            <div class="my-auto py-8 text-center z-10 flex flex-col items-center justify-center">
                <span class="text-xs font-semibold text-muted dark:text-on-dark-soft uppercase tracking-widest mb-2 font-display">SEDANG DILAYANI</span>
                <div class="relative inline-block transition-all duration-300 transform hover:scale-102">
                    @if ($currentQueue)
                        <h1 class="text-8xl md:text-9xl font-black text-primary dark:text-accent-teal tracking-tighter font-mono select-all focus:outline-none drop-shadow-sm transition-all duration-500 animate-pulse" id="active-queue-number">
                            {{ $currentQueue->queue_number }}
                        </h1>
                    @else
                        <h1 class="text-7xl md:text-8xl font-black text-muted-soft dark:text-on-dark-soft/30 tracking-tight font-mono select-none" id="active-queue-number">
                            KOSONG
                        </h1>
                    @endif
                </div>
                
                @if ($currentQueue)
                    <div class="mt-4 flex flex-col items-center">
                        <span class="text-sm font-semibold text-ink dark:text-white font-body">
                            {{ $currentQueue->visitor ? $currentQueue->visitor->name : ($currentQueue->booking ? $currentQueue->booking->user->name : 'Pengunjung Walk-In') }}
                        </span>
                        <span class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">
                            Tujuan: {{ $currentQueue->service ? $currentQueue->service->name : 'Layanan Informasi' }}
                        </span>
                    </div>
                @else
                    <p class="text-sm text-muted mt-4 font-body">Tekan "Panggil Berikutnya" untuk melayani antrean pertama.</p>
                @endif
            </div>

            <!-- Tombol Aksi Utama (Emerald, Amber, Rose) -->
            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-hairline dark:border-white/10 z-10">
                <!-- Lewati (Rose/Red) -->
                <form action="{{ route('admin.fo.call.skip') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" 
                        @if(!$currentQueue) disabled @endif
                        class="w-full h-14 flex flex-col items-center justify-center gap-1 bg-rose-600 hover:bg-rose-500 disabled:bg-rose-600/40 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all duration-150 shadow-md hover:shadow-lg focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-rose-500/50 cursor-pointer active:scale-98">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                        <span class="text-button text-xs tracking-wide">LEWATI</span>
                    </button>
                </form>

                <!-- Panggil Ulang (Amber) -->
                <form action="{{ route('admin.fo.call.recall') }}" method="POST" class="m-0" id="form-recall">
                    @csrf
                    <button type="submit" 
                        @if(!$currentQueue) disabled @endif
                        class="w-full h-14 flex flex-col items-center justify-center gap-1 bg-amber-500 hover:bg-amber-400 disabled:bg-amber-500/40 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all duration-150 shadow-md hover:shadow-lg focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-amber-500/50 cursor-pointer active:scale-98">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-button text-xs tracking-wide">PANGGIL ULANG</span>
                    </button>
                </form>

                <!-- Panggil Berikutnya (Emerald/Green) -->
                <form action="{{ route('admin.fo.call.next') }}" method="POST" class="m-0" id="form-next">
                    @csrf
                    <button type="submit" 
                        @if(!$nextQueue && !$currentQueue) disabled @endif
                        class="w-full h-14 flex flex-col items-center justify-center gap-1 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-600/40 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all duration-150 shadow-md hover:shadow-lg focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-emerald-500/50 cursor-pointer active:scale-98">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-button text-xs tracking-wide">PANGGIL BERIKUTNYA</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Daftar Tunggu & Antrean Berikutnya (Spans 4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Card Antrean Berikutnya -->
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm relative overflow-hidden flex flex-col justify-between h-48">
                <div>
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">ANTREAN BERIKUTNYA</span>
                    @if ($nextQueue)
                        <h4 class="text-4xl font-extrabold text-ink dark:text-white mt-2 font-mono tracking-tight">{{ $nextQueue->queue_number }}</h4>
                        <p class="text-xs font-bold text-primary dark:text-accent-teal mt-1 font-body">
                            {{ $nextQueue->visitor ? $nextQueue->visitor->name : ($nextQueue->booking ? $nextQueue->booking->user->name : 'Pengunjung Walk-In') }}
                        </p>
                        <p class="text-[10px] text-muted mt-0.5 font-body">Estimasi tunggu: ~{{ rand(5, 15) }} mnt</p>
                    @else
                        <h4 class="text-3xl font-extrabold text-muted-soft dark:text-on-dark-soft/20 mt-2 font-mono">TIDAK ADA</h4>
                        <p class="text-xs text-muted mt-1 font-body">Semua antrean FO telah terlayani hari ini.</p>
                    @endif
                </div>
                <div class="absolute right-4 bottom-4 opacity-5 text-ink dark:text-white font-mono text-8xl pointer-events-none select-none font-bold">NEXT</div>
            </div>

            <!-- Petunjuk Operasional & Audio Chime Controller -->
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm space-y-4">
                <h4 class="text-xs font-bold text-ink dark:text-white uppercase tracking-wider pb-2 border-b border-hairline dark:border-white/10 font-display">Petunjuk Petugas</h4>
                <ul class="text-xs text-muted dark:text-on-dark-soft space-y-2.5 list-inside list-disc font-body">
                    <li><strong class="text-ink dark:text-white">Panggil Berikutnya</strong> akan memproses antrean pertama dalam daftar tunggu dan otomatis menyelesaikan antrean aktif sebelumnya.</li>
                    <li><strong class="text-ink dark:text-white">Panggil Ulang</strong> memicu bel chime pemberitahuan loket untuk memanggil kembali warga yang bersangkutan.</li>
                    <li><strong class="text-ink dark:text-white">Lewati</strong> digunakan jika warga tidak kunjung hadir di loket depan dalam 3x pemanggilan.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Riwayat Operasional Antrean FO Hari Ini -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-hairline dark:border-white/10">
            <div>
                <h3 class="font-bold text-ink dark:text-white font-display">Riwayat & Daftar Antrean FO Hari Ini</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-body">Status seluruh antrean loket depan tertanggal hari ini.</p>
            </div>
            <span class="text-xs font-mono font-bold text-muted dark:text-on-dark-soft bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-2.5 py-1 rounded-md">
                {{ $counter->name }}
            </span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-hairline dark:border-white/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft text-[11px] font-bold uppercase tracking-wider border-b border-hairline dark:border-white/10">
                        <th class="py-3.5 px-6">No. Antrean</th>
                        <th class="py-3.5 px-6">Nama Pengunjung</th>
                        <th class="py-3.5 px-4">Layanan</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Waktu Dipanggil</th>
                        <th class="py-3.5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-hairline dark:divide-white/5">
                    @forelse ($historyQueues as $q)
                        <tr class="hover:bg-surface-soft/30 dark:hover:bg-white/5 transition-colors @if($q->status === 'Serving') bg-primary/5 dark:bg-accent-teal/5 border-l-4 border-primary dark:border-accent-teal @endif">
                            <td class="py-3 px-6 font-mono font-bold text-ink dark:text-white">{{ $q->queue_number }}</td>
                            <td class="py-3 px-6 font-bold text-ink dark:text-white">
                                {{ $q->visitor ? $q->visitor->name : ($q->booking ? $q->booking->user->name : 'Walk-In') }}
                            </td>
                            <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">
                                {{ $q->service ? $q->service->name : 'Umum' }}
                            </td>
                            <td class="py-3 px-4 text-muted dark:text-on-dark-soft">
                                {{ $q->booking_id ? 'Online Booking' : 'Walk-In' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">
                                {{ $q->called_at ? Carbon\Carbon::parse($q->called_at)->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-6">
                                @if ($q->status === 'Waiting')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/10 text-status-waiting rounded-full text-[10px] font-bold border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-status-waiting animate-pulse"></span>Menunggu
                                    </span>
                                @elseif ($q->status === 'Serving')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary/10 text-primary dark:text-accent-teal rounded-full text-[10px] font-bold border border-primary/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary dark:bg-accent-teal animate-ping"></span>Sedang Dilayani
                                    </span>
                                @elseif ($q->status === 'Completed')
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-bold border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                                        </span>
                                        @if ($q->booking_id === null && !$q->feedback)
                                            <a href="{{ route('feedback.create', ['queue_id' => $q->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary dark:text-accent-teal hover:bg-primary/20 rounded-full text-[10px] font-bold transition-all">
                                                Tulis Ulasan
                                            </a>
                                        @endif
                                    </div>
                                @elseif ($q->status === 'Skipped')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-500/10 text-status-skipped rounded-full text-[10px] font-bold border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-status-skipped"></span>Terlewat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-muted dark:text-on-dark-soft font-body">Belum ada antrean terdaftar untuk hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Elemen Audio HTML5 Tersembunyi untuk Chime -->
<audio id="chime-audio" class="hidden" src="{{ asset('audio/chime.wav') }}" preload="auto"></audio>

<!-- Toast Alert & Scripting -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

<script>
    // Live Clock
    function updateClock() {
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        const clockEl = document.getElementById('fo-live-clock');
        if (clockEl) {
            clockEl.innerText = `${dateStr} | ${timeStr}`;
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
        
        // Auto play chime if session has play_chime
        @if (session('play_chime'))
            triggerChime();
        @endif

        // Hook chime trigger on button click to have instant UI feedback
        const btnNext = document.querySelector('#form-next button');
        const btnRecall = document.querySelector('#form-recall button');

        if (btnNext) {
            btnNext.addEventListener('click', () => {
                triggerChime();
            });
        }
        if (btnRecall) {
            btnRecall.addEventListener('click', () => {
                triggerChime();
            });
        }
    });

    // Chime play function
    function triggerChime() {
        const audio = document.getElementById('chime-audio');
        if (audio) {
            audio.play().catch(e => {
                console.log('HTML5 Audio blocked or failed, falling back to Web Audio API: ' + e);
                // Fallback to synthesizing chime sound programmatically
                synthesizeChime();
            });
        } else {
            synthesizeChime();
        }
    }

    // Web Audio API Synthesized Chime (C5 -> E5)
    function synthesizeChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            
            const ctx = new AudioContext();
            
            // First note (C5 - 523.25 Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(523.25, ctx.currentTime);
            gain1.gain.setValueAtTime(0, ctx.currentTime);
            gain1.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.05);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
            
            osc1.start(ctx.currentTime);
            osc1.stop(ctx.currentTime + 0.4);
            
            // Second note (E5 - 659.25 Hz)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(659.25, ctx.currentTime + 0.12);
            gain2.gain.setValueAtTime(0, ctx.currentTime + 0.12);
            gain2.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.17);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.47);
            
            osc2.start(ctx.currentTime + 0.12);
            osc2.stop(ctx.currentTime + 0.5);
        } catch (e) {
            console.warn('Synthesizer blocked or not supported: ' + e);
        }
    }

    // Toast Generator
    @if (session('success'))
        createToast('Sukses', '{!! session('success') !!}', 'success');
    @endif
    @if (session('warning'))
        createToast('Peringatan', '{!! session('warning') !!}', 'warning');
    @endif
    @if (session('error'))
        createToast('Gagal', '{!! session('error') !!}', 'error');
    @endif

    function createToast(title, message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        let borderClr = 'border-l-4 border-emerald-500';
        let iconHtml = '';

        if (type === 'success') {
            borderClr = 'border-l-4 border-emerald-500';
            iconHtml = `<svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            borderClr = 'border-l-4 border-amber-500';
            iconHtml = `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            borderClr = 'border-l-4 border-rose-500';
            iconHtml = `<svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        toast.className = `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 bg-canvas dark:bg-surface-dark-elevated ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
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
        }, 5000);
    }
</script>
@endsection
