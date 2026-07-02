{{--
    Halaman: Verifikasi & Check-In Booking Online
    Role   : Admin Front Office
    Route  : admin.fo.checkin (GET), admin.fo.checkin.verify (POST)
--}}
@extends('layouts.private')

@section('title', 'Verifikasi & Check-In — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 pb-16" x-data="{ rejectModalOpen: false, rejectReason: '' }">

        {{-- ═══════════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-serving opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-status-serving"></span>
                    </span>
                    <span class="text-[11px] font-bold text-status-serving uppercase tracking-widest font-display">Stasiun
                        Check-In Aktif</span>
                </div>
                <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Verifikasi & Check-In</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Scan atau ketik kode booking warga
                    untuk verifikasi kedatangan.</p>
            </div>
            <div class="text-xs text-muted dark:text-on-dark-soft font-mono bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-3 py-1.5 rounded-md"
                id="checkin-clock">
                Loading waktu...
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
         FLASH ALERTS
    ═══════════════════════════════════════════════════════════════ --}}



        {{-- Warning (status sudah diproses) --}}
        @if (session('warning'))
            <div class="flex items-start gap-3 p-4 bg-status-waiting/10 border border-status-waiting/30 rounded-lg"
                role="alert" id="alert-warning">
                <svg class="w-5 h-5 text-status-waiting shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-waiting font-display">Tidak Dapat Diproses</p>
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

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg"
                role="alert" id="alert-validation">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Kesalahan Validasi</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         SEARCH / SCAN INPUT — Ukuran besar untuk barcode scanner
    ═══════════════════════════════════════════════════════════════ --}}
        <div
            class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <h2 class="text-lg font-bold text-ink dark:text-white font-display">Scan atau Ketik Kode Booking / NIK</h2>
            </div>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mb-5">Arahkan scanner ke QR/barcode tiket warga,
                atau ketik manual kode booking (UUID) / NIK warga di kolom berikut.</p>

            <form action="{{ route('admin.fo.checkin.verify') }}" method="POST" id="formCheckin" autocomplete="off">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label for="booking_code" class="sr-only">Kode Booking / NIK Warga</label>
                        <input type="text" id="booking_code" name="booking_code" autofocus
                            value="{{ old('booking_code') }}" placeholder="Contoh: 550e8400-e29b-41d4-a716-446655440000 atau NIK 16 digit"
                            class="w-full h-16 text-xl font-mono font-semibold tracking-wide bg-surface-soft dark:bg-white/5 border-2 border-hairline dark:border-white/15 text-ink dark:text-white rounded-lg px-5 placeholder:text-muted-soft placeholder:text-base placeholder:font-normal focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-4 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    </div>
                    <button type="submit" id="btnVerify"
                        class="h-16 px-8 bg-primary hover:bg-primary-hover text-white font-bold rounded-lg text-base transition-all focus:outline-none focus:ring-4 focus:ring-primary/30 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Verifikasi
                    </button>
                </div>
                <p class="text-xs text-muted dark:text-on-dark-soft mt-2.5 font-body">
                    <span class="font-semibold">Tip:</span> Kolom ini siap menerima input dari hardware barcode/QR scanner
                    secara otomatis.
                </p>
            </form>
        </div>

        @php
            // Utamakan $booking dari view data (passed directly).
            // Jika dari redirect, ambil via booking_code_pending agar relasi
            // selalu berupa Eloquent object (bukan array hasil deserialisasi).
            if (isset($booking)) {
                $bk = $booking;
            } elseif (session('booking_code_pending')) {
                $bk = \App\Models\Queue::where('booking_code', session('booking_code_pending'))
                    ->with(['user', 'department'])
                    ->first();
            } else {
                $bk = null;
            }
            $isNikRequired = $nik_required ?? session('nik_required') ?? false;
        @endphp

        {{-- ═══════════════════════════════════════════════════════════════
         NIK REQUIRED PANEL — Alert Amber + Inline Form
         Tampil ketika booking ditemukan tapi NIK warga kosong
    ═══════════════════════════════════════════════════════════════ --}}
        @if ($isNikRequired && $bk)
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden"
                id="nik-panel">

                {{-- Amber Banner --}}
                <div
                    class="flex items-start gap-3 p-4 sm:p-5 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-700/40">
                    <div class="p-2 bg-amber-100 dark:bg-amber-800/30 rounded-lg shrink-0">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-300 font-display">Profil Belum Lengkap — NIK Kosong</p>
                        <p class="text-sm text-amber-700 dark:text-amber-400 font-body mt-0.5">
                            Data NIK warga <strong>{{ $bk->user->name }}</strong> belum terisi di sistem. Silakan minta KTP warga dan isikan NIK di bawah ini sebelum melanjutkan.
                        </p>
                    </div>
                </div>

                {{-- Booking Detail + NIK Form --}}
                <div class="p-5 sm:p-6 space-y-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Nama Warga</span>
                            <span class="font-semibold text-ink dark:text-white">{{ $bk->user->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Kode Booking</span>
                            <span class="font-mono font-semibold text-primary dark:text-accent-teal">{{ $bk->booking_code }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Tanggal</span>
                            <span class="text-ink dark:text-white">{{ $bk->booking_date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Status</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-[11px] font-bold border border-amber-200/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $bk->status }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-hairline dark:border-white/10">

                    {{-- Inline NIK Form --}}
                    <form action="{{ route('admin.fo.checkin.verify') }}" method="POST" id="formNikCheckin" autocomplete="off">
                        @csrf
                        <input type="hidden" name="booking_code" value="{{ $bk->booking_code }}">

                        <label for="nik_input" class="block text-sm font-bold text-ink dark:text-white font-display mb-2">
                            Masukkan NIK Warga (16 digit)
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <input type="text" id="nik_input" name="nik_input" maxlength="16"
                                    inputmode="numeric" pattern="\d{16}" value="{{ old('nik_input') }}"
                                    placeholder="Contoh: 1373021408990002" autofocus
                                    class="w-full h-14 text-lg font-mono font-semibold tracking-wider bg-surface-soft dark:bg-white/5 border-2 border-amber-300 dark:border-amber-600/50 text-ink dark:text-white rounded-lg px-5 placeholder:text-muted-soft placeholder:text-sm placeholder:font-normal focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-4 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                            </div>
                            <button type="submit"
                                class="h-14 px-6 bg-status-serving hover:bg-green-700 text-white font-bold rounded-lg text-sm transition-all focus:outline-none focus:ring-4 focus:ring-green-500/30 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan NIK & Lanjutkan
                            </button>
                        </div>
                        <p class="text-xs text-muted dark:text-on-dark-soft mt-2 font-body">
                            NIK akan tersimpan ke profil warga dan detail verifikasi akan ditampilkan kembali.
                        </p>
                    </form>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         VERIFICATION DETAIL PANEL — Tampil jika booking ditemukan & NIK valid
    ═══════════════════════════════════════════════════════════════ --}}
        @if (!$isNikRequired && $bk)
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden"
                id="verification-panel">
                
                {{-- Banner Instruksi --}}
                <div class="flex items-start gap-3 p-4 sm:p-5 bg-primary/5 border-b border-primary/15">
                    <div class="p-2 bg-primary/10 rounded-lg shrink-0">
                        <svg class="w-6 h-6 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-primary dark:text-accent-teal font-display">Langkah Verifikasi Fisik</p>
                        <p class="text-sm text-body dark:text-on-dark-soft font-body mt-0.5">
                            Silakan periksa dokumen fisik warga untuk memastikan keabsahan persyaratan layanan sebelum menyetujui.
                        </p>
                    </div>
                </div>

                {{-- Detail Data Warga & Layanan --}}
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-muted uppercase tracking-wider font-display">Informasi Pengunjung</h3>
                            <div class="bg-surface-soft dark:bg-white/5 p-4 rounded-lg space-y-3 border border-hairline dark:border-white/5">
                                <div class="flex justify-between">
                                    <span class="text-muted">Nama Lengkap</span>
                                    <span class="font-bold text-ink dark:text-white">{{ $bk->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted">NIK</span>
                                    <span class="font-mono font-bold text-ink dark:text-white">{{ $bk->user->nik }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted">Email</span>
                                    <span class="font-bold text-ink dark:text-white">{{ $bk->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-muted uppercase tracking-wider font-display">Tujuan Pelayanan</h3>
                            <div class="bg-surface-soft dark:bg-white/5 p-4 rounded-lg space-y-3 border border-hairline dark:border-white/5">
                                <div class="flex justify-between">
                                    <span class="text-muted">Instansi</span>
                                    <span class="font-bold text-ink dark:text-white">{{ $bk->department->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted">Layanan</span>
                                    <span class="font-bold text-primary dark:text-accent-teal">{{ $bk->purpose }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted">Status Booking</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-[11px] font-bold border border-amber-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span>{{ $bk->status === 'Booked' ? 'Pending' : $bk->status }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted">Jadwal & Sesi</span>
                                    <span class="font-bold text-ink dark:text-white">{{ $bk->booking_date->translatedFormat('d M Y') }} ({{ $bk->session_name ?? 'Umum' }})</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions block --}}
                    <div class="pt-6 border-t border-hairline dark:border-white/10 flex flex-col sm:flex-row justify-end gap-3">
                        {{-- Button Tolak --}}
                        <button type="button" 
                                @click="rejectModalOpen = true"
                                class="inline-flex h-11 items-center justify-center gap-1.5 px-6 bg-status-skipped/10 hover:bg-status-skipped text-status-skipped hover:text-white font-bold text-xs rounded-pill border border-status-skipped/20 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tolak / Batalkan
                        </button>

                        {{-- Button Setujui --}}
                        <form action="{{ route('admin.fo.checkin.approve', $bk) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full sm:w-auto h-11 px-8 bg-status-serving hover:bg-green-700 text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Setuju (Aktifkan & Cetak)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         CHECK-IN RESULT CARD — Ditampilkan setelah check-in sukses
    ═══════════════════════════════════════════════════════════════ --}}
        @if (session('checkin_result_id'))
            @php 
                // Re-fetch fresh dari DB agar relasi (user, department) selalu
                // berupa Eloquent object, bukan array hasil deserialisasi session.
                $cr = \App\Models\Queue::with(['user', 'department'])
                    ->find(session('checkin_result_id'));
                $queue = $cr;
            @endphp
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border-2 border-status-serving/40 shadow-sm overflow-hidden"
                id="checkin-result-card">

                <div class="flex items-center justify-between px-5 py-3 bg-status-serving/10 border-b border-status-serving/20 print:hidden">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold text-status-serving font-display">Check-In Berhasil & Tiket Diterbitkan</span>
                    </div>
                    <button onclick="window.print()" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-xs transition-all cursor-pointer flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Karcis
                    </button>
                </div>

                {{-- Thermal Ticket Print Container --}}
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="text-center space-y-2 pb-6 border-b border-dashed border-hairline dark:border-white/10">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Sawahlunto Logo" class="h-8 object-contain">
                            <div class="text-left">
                                <h4 class="text-[10px] font-bold uppercase tracking-wider font-display text-muted">Mal Pelayanan Publik</h4>
                                <h5 class="text-xs font-extrabold uppercase tracking-tight font-display text-ink dark:text-white">Kota Sawahlunto</h5>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">NOMOR ANTREAN</span>
                        <div class="text-5xl sm:text-6xl font-extrabold text-primary dark:text-accent-teal tracking-tight font-mono">
                            {{ $queue->queue_number ?? '-' }}
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50 print:hidden">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Waiting (Menunggu)
                        </span>
                    </div>

                    <div class="space-y-3.5 text-sm font-body">
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Nama Warga</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $cr->user->name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">NIK Warga</span>
                            <span class="font-bold text-ink dark:text-white font-mono text-right">{{ $cr->user->nik ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Instansi</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $queue->department->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Layanan</span>
                            <span class="font-bold text-primary dark:text-accent-teal text-right">{{ $queue->purpose ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Tanggal Check-In</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ ($queue->checked_in_at ?? $cr->checked_in_at)?->translatedFormat('d F Y · H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         PETUNJUK PENGGUNAAN
    ═══════════════════════════════════════════════════════════════ --}}
        <div
            class="bg-canvas dark:bg-surface-dark-elevated p-5 sm:p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm print:hidden">
            <h3 class="text-sm font-bold text-ink dark:text-white font-display mb-3">Petunjuk Penggunaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <div
                        class="shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        1</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Terima Kode / NIK</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Minta warga menunjukkan QR
                            code, kode booking (email/WA), atau menyebutkan NIK.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div
                        class="shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        2</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Scan / Ketik</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Arahkan scanner ke QR code,
                            atau ketik kode booking / NIK di kolom input.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div
                        class="shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        3</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Verifikasi & Cetak</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Sistem memvalidasi berkas. Pilih Setuju untuk menerbitkan antrean dan cetak karcis.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rejection Reason Modal Overlay --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none print:hidden"
             :class="rejectModalOpen ? 'opacity-100 pointer-events-auto' : ''"
             x-cloak>
            
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-6 md:p-8 max-w-md w-full border border-hairline dark:border-white/10 shadow-2xl transform scale-95 transition-transform duration-300 relative"
                 :class="rejectModalOpen ? 'scale-100' : 'scale-95'"
                 @click.away="rejectModalOpen = false">
                
                {{-- Close button --}}
                <button type="button" 
                        @click="rejectModalOpen = false" 
                        class="absolute top-4 right-4 text-muted hover:text-ink dark:hover:text-white p-1 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display mb-2">Tolak / Batalkan Booking</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mb-6 font-body">Harap berikan alasan mengapa berkas dokumen warga tidak valid atau tidak lengkap. Alasan akan dikirim ke email warga.</p>

                @if ($bk)
                {{-- Form input reason --}}
                <form action="{{ route('admin.fo.checkin.reject', $bk) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="reason" class="block text-sm font-bold text-ink dark:text-white font-display">
                            Alasan Penolakan
                        </label>
                        <textarea id="reason" 
                                  name="reason" 
                                  rows="3" 
                                  required 
                                  x-model="rejectReason"
                                  placeholder="Contoh: Dokumen persyaratan tidak asli, KTP kadaluarsa, berkas kurang lengkap..." 
                                  class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all"></textarea>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft font-body">Minimal 5 karakter.</p>
                    </div>

                    <div class="pt-4 border-t border-hairline dark:border-white/10 flex justify-end gap-3">
                        <button type="button" 
                                @click="rejectModalOpen = false" 
                                class="h-11 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="rejectReason.trim().length < 5"
                                :class="rejectReason.trim().length < 5 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                                class="h-11 px-6 bg-status-skipped text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tolak / Batalkan Booking
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>

        {{-- Custom print layout style --}}
        <style>
            @media print {
                body {
                    background: white !important;
                    color: black !important;
                }
                /* Hide everything except the ticket result card */
                aside, header, nav, main > div > div:not(#checkin-result-card), #checkin-result-card > div:first-child, form, button, hr {
                    display: none !important;
                }
                main {
                    padding: 0 !important;
                    margin: 0 !important;
                }
                .min-h-screen, .h-screen {
                    height: auto !important;
                    min-height: 0 !important;
                    padding-left: 0 !important;
                }
                #checkin-result-card {
                    border: none !important;
                    box-shadow: none !important;
                    padding: 0 !important;
                    margin: 0 auto !important;
                    max-width: 300px !important; /* Standard 80mm printer size width */
                    text-align: center !important;
                }
                #checkin-result-card * {
                    color: black !important;
                }
            }
        </style>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Live Clock ──────────────────────────────────────────────────
        function updateCheckinClock() {
            const d = new Date();
            const timeStr = d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateStr = d.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const el = document.getElementById('checkin-clock');
            if (el) el.textContent = `${dateStr} · ${timeStr}`;
        }

        // ── Auto-refocus ke kolom scan setelah alert ditampilkan ────────
        document.addEventListener('DOMContentLoaded', () => {
            updateCheckinClock();
            setInterval(updateCheckinClock, 1000);

            // Jika ada panel NIK, fokuskan ke input NIK
            const nikInput = document.getElementById('nik_input');
            if (nikInput) {
                nikInput.focus();
                return;
            }

            // Default: fokus ke kolom scan utama
            const scanInput = document.getElementById('booking_code');
            if (scanInput) scanInput.focus();
        });

        // ── NIK input: hanya angka, auto-submit setelah 16 digit ───────
        const nikField = document.getElementById('nik_input');
        if (nikField) {
            nikField.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 16);
            });
        }

        // ── Booking code: auto-submit ketika scanner mengirim Enter ─────
        const scanField = document.getElementById('booking_code');
        if (scanField) {
            scanField.addEventListener('keydown', (e) => {
                // Scanner biasanya mengirim Enter di akhir scan
                // Form sudah di-handle oleh default submit, tidak perlu JS tambahan
            });
        }
    </script>
@endpush
