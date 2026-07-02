{{--
    Halaman: Buat Laporan Baru (Front Office)
    Role   : Admin Front Office
    Route  : reports.create (GET)
--}}
@extends('layouts.private')

@section('title', 'Buat Laporan Baru — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6 pb-16">

        {{-- Header & Navigasi Balik --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}"
                class="w-10 h-10 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-full transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Buat Laporan Pelayanan</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                    Pilih rentang tanggal untuk mengompilasi statistik dan kinerja antrean.
                </p>
            </div>
        </div>



        {{-- Form Card --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm p-6 sm:p-8">
            <form action="{{ route('reports.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Tanggal Mulai --}}
                    <div class="space-y-2">
                        <label for="start_date" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                            Tanggal Mulai
                        </label>
                        <input type="date" id="start_date" name="start_date"
                            value="{{ old('start_date', now()->format('Y-m-d')) }}"
                            class="w-full h-12 bg-surface-soft dark:bg-white/5 border text-ink dark:text-white rounded-md px-4 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal {{ $errors->has('start_date') ? 'border-status-skipped' : 'border-hairline dark:border-white/10' }}">
                        @error('start_date')
                            <p class="text-xs text-status-skipped font-semibold mt-1 font-body">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="space-y-2">
                        <label for="end_date" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                            Tanggal Akhir
                        </label>
                        <input type="date" id="end_date" name="end_date"
                            value="{{ old('end_date', now()->format('Y-m-d')) }}"
                            class="w-full h-12 bg-surface-soft dark:bg-white/5 border text-ink dark:text-white rounded-md px-4 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal {{ $errors->has('end_date') ? 'border-status-skipped' : 'border-hairline dark:border-white/10' }}">
                        @error('end_date')
                            <p class="text-xs text-status-skipped font-semibold mt-1 font-body">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan Penting --}}
                <div class="p-4 bg-primary/5 dark:bg-accent-teal/5 border border-primary/10 dark:border-accent-teal/10 rounded-lg space-y-1">
                    <p class="text-xs font-bold text-primary dark:text-accent-teal font-display uppercase tracking-wider">
                        Aturan Pembuatan Laporan
                    </p>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed">
                        Sistem akan memindai seluruh data antrean (kunjungan warga, loket yang aktif, durasi waktu layan, dan status penyelesaian) pada rentang tanggal tersebut. Laporan <strong>gagal dibuat</strong> jika tidak terdapat aktivitas antrean pada periode terpilih.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline dark:border-white/10">
                    <a href="{{ route('reports.index') }}"
                        class="h-11 px-5 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white rounded-pill text-xs font-bold transition-all border border-hairline dark:border-white/10">
                        Batal
                    </a>
                    <button type="submit"
                        class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs transition-all shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        Proses & Buat Draft Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
