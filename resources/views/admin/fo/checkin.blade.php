{{--
    Halaman: Verifikasi & Check-In Booking Online
    Role   : Admin Front Office
    Route  : admin.fo.checkin (GET), admin.fo.checkin.verify (POST)
--}}
@extends('layouts.private')

@section('title', 'Verifikasi & Check-In — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 pb-16">

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

        {{-- Success --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-status-serving/10 border border-status-serving/30 rounded-lg"
                role="alert" id="alert-success">
                <svg class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-serving font-display">Check-In Berhasil</p>
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

        {{-- Error --}}
        @if (session('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg"
                role="alert" id="alert-error">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Booking Tidak Ditemukan</p>
                    <p class="text-sm text-red-800 dark:text-red-300 font-body mt-0.5">{!! session('error') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()"
                    class="shrink-0 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

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
                <h2 class="text-lg font-bold text-ink dark:text-white font-display">Scan atau Ketik Kode Booking</h2>
            </div>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mb-5">Arahkan scanner ke QR/barcode tiket warga,
                atau ketik manual kode booking (UUID) di kolom berikut.</p>

            <form action="{{ route('admin.fo.checkin.verify') }}" method="POST" id="formCheckin" autocomplete="off">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label for="booking_code" class="sr-only">Kode Booking</label>
                        <input type="text" id="booking_code" name="booking_code" autofocus
                            value="{{ old('booking_code') }}" placeholder="Contoh: 550e8400-e29b-41d4-a716-446655440000"
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

        {{-- ═══════════════════════════════════════════════════════════════
         NIK REQUIRED PANEL — Alert Amber + Inline Form
         Tampil ketika booking ditemukan tapi NIK warga kosong
    ═══════════════════════════════════════════════════════════════ --}}
        @if (session('nik_required') && session('booking'))
            @php $bk = session('booking'); @endphp
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
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-300 font-display">Profil Belum Lengkap —
                            NIK Kosong</p>
                        <p class="text-sm text-amber-700 dark:text-amber-400 font-body mt-0.5">
                            Data NIK warga <strong>{{ $bk->user->name }}</strong> belum terisi di sistem. Silakan minta KTP
                            warga dan isikan NIK di bawah ini sebelum melanjutkan check-in.
                        </p>
                    </div>
                </div>

                {{-- Booking Detail + NIK Form --}}
                <div class="p-5 sm:p-6 space-y-5">
                    {{-- Detail Booking --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Nama
                                Warga</span>
                            <span class="font-semibold text-ink dark:text-white">{{ $bk->user->name }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Kode
                                Booking</span>
                            <span
                                class="font-mono font-semibold text-primary dark:text-accent-teal">{{ $bk->booking_code }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Tanggal</span>
                            <span
                                class="text-ink dark:text-white">{{ $bk->booking_date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Status</span>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-status-waiting/12 text-status-waiting rounded-full text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-waiting"></span>{{ $bk->status }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-hairline dark:border-white/10">

                    {{-- Inline NIK Form --}}
                    <form action="{{ route('admin.fo.checkin.verify') }}" method="POST" id="formNikCheckin"
                        autocomplete="off">
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
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan NIK & Check-In
                            </button>
                        </div>
                        <p class="text-xs text-muted dark:text-on-dark-soft mt-2 font-body">
                            NIK akan tersimpan ke profil warga dan check-in akan langsung diproses.
                        </p>
                    </form>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         CHECK-IN RESULT CARD — Ditampilkan setelah check-in sukses
    ═══════════════════════════════════════════════════════════════ --}}
        @if (session('checkin_result'))
            @php $cr = session('checkin_result'); @endphp
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border-2 border-status-serving/40 shadow-sm overflow-hidden"
                id="checkin-result-card">

                <div class="flex items-center gap-2 px-5 py-3 bg-status-serving/10 border-b border-status-serving/20">
                    <svg class="w-5 h-5 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-bold text-status-serving font-display">Hasil Check-In</span>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Nama
                                Warga</span>
                            <span class="font-semibold text-ink dark:text-white">{{ $cr->user->name }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Kode
                                Booking</span>
                            <span
                                class="font-mono font-semibold text-primary dark:text-accent-teal">{{ $cr->booking_code }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">NIK</span>
                            <span class="font-mono text-ink dark:text-white">{{ $cr->user->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-1">Waktu
                                Check-In</span>
                            <span
                                class="font-mono text-ink dark:text-white">{{ $cr->checked_in_at?->format('H:i:s') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-hairline dark:border-white/10 flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-status-serving/12 text-status-serving rounded-full text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-serving"></span>Checked-In
                        </span>
                        <span class="text-xs text-muted dark:text-on-dark-soft font-body">— Warga siap menunggu panggilan
                            antrean.</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════
         PETUNJUK PENGGUNAAN
    ═══════════════════════════════════════════════════════════════ --}}
        <div
            class="bg-canvas dark:bg-surface-dark-elevated p-5 sm:p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
            <h3 class="text-sm font-bold text-ink dark:text-white font-display mb-3">Petunjuk Penggunaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        1</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Terima Kode</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Minta warga menunjukkan QR
                            code atau kode booking dari email/WA.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        2</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Scan / Ketik</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Arahkan scanner ke QR code,
                            atau ketik kode manual di kolom input.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal flex items-center justify-center text-sm font-bold font-mono">
                        3</div>
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-white font-display">Verifikasi</p>
                        <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-0.5">Sistem memvalidasi data. Jika
                            NIK kosong, isi di tempat lalu check-in.</p>
                    </div>
                </div>
            </div>
        </div>
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
