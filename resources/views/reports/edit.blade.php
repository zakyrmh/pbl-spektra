{{--
    Halaman: Ubah Rentang Tanggal Laporan (Front Office)
    Role   : Admin Front Office
    Route  : reports.edit (GET)
--}}
@extends('layouts.private')

@section('title', 'Ubah Laporan — MPP Kota Sawahlunto')

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
                <h1 class="text-2xl font-bold text-ink dark:text-white font-display">Ubah Laporan Pelayanan</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">
                    Modifikasi rentang tanggal laporan. Data rekap akan dihitung ulang secara otomatis.
                </p>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg animate-fade-in"
                role="alert" id="alert-error">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Memperbarui Laporan</p>
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

        {{-- Form Card --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm p-6 sm:p-8">
            <form action="{{ route('reports.update', $report->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Tanggal Mulai --}}
                    <div class="space-y-2">
                        <label for="start_date" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">
                            Tanggal Mulai
                        </label>
                        <input type="date" id="start_date" name="start_date"
                            value="{{ old('start_date', $report->start_date->format('Y-m-d')) }}"
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
                            value="{{ old('end_date', $report->end_date->format('Y-m-d')) }}"
                            class="w-full h-12 bg-surface-soft dark:bg-white/5 border text-ink dark:text-white rounded-md px-4 font-semibold focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal {{ $errors->has('end_date') ? 'border-status-skipped' : 'border-hairline dark:border-white/10' }}">
                        @error('end_date')
                            <p class="text-xs text-status-skipped font-semibold mt-1 font-body">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline dark:border-white/10">
                    <a href="{{ route('reports.index') }}"
                        class="h-11 px-5 flex items-center justify-center bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white rounded-pill text-xs font-bold transition-all border border-hairline dark:border-white/10">
                        Batal
                    </a>
                    <button type="submit"
                        class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs transition-all shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        Perbarui & Hitung Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
