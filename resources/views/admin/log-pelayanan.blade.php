@extends('layouts.private')

@section('title', 'Log Pelayanan - MPP Kota Sawahlunto')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16">
    {{-- Header Banner --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white font-display tracking-tight">Log Pelayanan</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">
                Riwayat log pelayanan antrean lampau untuk Instansi <span class="font-semibold text-primary dark:text-accent-teal">{{ isset($department) ? $department->name : (Auth::user()->department ? Auth::user()->department->name : '-') }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.log-pelayanan.export', request()->all()) }}" 
               class="h-11 px-5 bg-canvas dark:bg-white/5 border border-hairline text-ink dark:text-white dark:border-white/15 hover:bg-surface-soft dark:hover:bg-white/10 font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Cards Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Antrean Sukses -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden font-body">
            <div class="space-y-1">
                <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider font-display">Total Antrean Sukses</span>
                <span class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $totalSuccess }}</span>
                <span class="text-xs text-muted dark:text-on-dark-soft block">Selesai dilayani (Completed)</span>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-950/20 text-green-600 dark:text-green-400 rounded-lg">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card Antrean Terlewat -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden font-body">
            <div class="space-y-1">
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider font-display">Total Antrean Terlewat</span>
                <span class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $totalSkipped }}</span>
                <span class="text-xs text-muted dark:text-on-dark-soft block">Skipped (Terlewat)</span>
            </div>
            <div class="p-3 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 rounded-lg">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Filters and Search Form --}}
    <form method="GET" action="{{ route('admin.log-pelayanan') }}" class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 space-y-4 font-body">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="col-span-1 sm:col-span-2 md:col-span-1">
                <label for="search" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Cari</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Kode Antrean / Nama Warga..."
                           class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md pl-10 pr-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted dark:text-on-dark-soft">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Date Range: Mulai -->
            <div>
                <label for="start_date" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Tanggal Mulai</label>
                <input type="text" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ $filters['start_date'] ?? '' }}"
                       placeholder="dd/mm/yyyy"
                       class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
            </div>

            <!-- Date Range: Akhir -->
            <div>
                <label for="end_date" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Tanggal Akhir</label>
                <input type="text" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ $filters['end_date'] ?? '' }}"
                       placeholder="dd/mm/yyyy"
                       class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
            </div>

            <!-- Dropdown Status -->
            <div>
                <label for="status" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Status</label>
                <select id="status" 
                        name="status"
                        class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <option value="">Semua Status Lampau</option>
                    <option value="Completed" {{ ($filters['status'] ?? '') === 'Completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="Skipped"   {{ ($filters['status'] ?? '') === 'Skipped'   ? 'selected' : '' }}>Skipped (Terlewat)</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.log-pelayanan') }}" class="h-11 px-5 text-button font-semibold text-muted dark:text-on-dark-soft hover:bg-black/5 dark:hover:bg-white/5 rounded-pill border border-hairline dark:border-white/10 flex items-center transition-all cursor-pointer">
                Reset
            </a>
            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer">
                Terapkan Filter
            </button>
        </div>
    </form>

    {{-- Main Table --}}
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto font-body">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/15">
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Tanggal</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">No. Antrean</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Nama Warga</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Keperluan</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Jam Dipanggil</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Jam Selesai</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Durasi</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Status</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline dark:divide-white/10 text-body-sm text-ink dark:text-on-dark-soft">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-black/2 dark:hover:bg-white/2 transition-colors">
                            <td class="p-4 whitespace-nowrap text-xs text-muted dark:text-on-dark-soft">
                                {{ $log->booking_date_formatted }}
                            </td>
                            <td class="p-4 font-mono font-bold text-primary dark:text-accent-teal whitespace-nowrap">
                                {{ $log->queue_number }}
                            </td>
                            <td class="p-4 font-semibold">
                                {{ $log->visitor_name ?? '—' }}
                            </td>
                            <td class="p-4 max-w-xs truncate" title="{{ $log->purpose }}">
                                {{ $log->purpose ?? '—' }}
                            </td>
                            <td class="p-4 whitespace-nowrap font-mono text-xs">
                                {{ $log->called_at_formatted ?? '—' }}
                            </td>
                            <td class="p-4 whitespace-nowrap font-mono text-xs">
                                {{ $log->completed_at_formatted ?? '—' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if ($log->duration_label)
                                    <span class="px-2 py-0.5 bg-surface-soft dark:bg-white/5 text-ink dark:text-white rounded text-xs font-mono font-bold">
                                        {{ $log->duration_label }}
                                    </span>
                                @else
                                    <span class="text-muted dark:text-on-dark-soft text-xs">—</span>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if ($log->status === 'Completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-done/10 text-status-done border border-status-done/15">
                                        <span class="w-2 h-2 rounded-full bg-status-done"></span>
                                        Selesai
                                    </span>
                                @elseif ($log->status === 'Skipped')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/15">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Terlewat
                                    </span>
                                @elseif ($log->status === 'Cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-skipped/10 text-status-skipped border border-status-skipped/15">
                                        <span class="w-2 h-2 rounded-full bg-status-skipped"></span>
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-waiting/10 text-status-waiting border border-status-waiting/15">
                                        <span class="w-2 h-2 rounded-full bg-status-waiting"></span>
                                        {{ $log->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 max-w-xs truncate text-xs text-muted dark:text-on-dark-soft" title="{{ $log->cancel_reason }}">
                                {{ $log->cancel_reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-muted dark:text-on-dark-soft">
                                <svg class="w-12 h-12 mx-auto mb-3 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span class="text-base font-semibold block">Tidak ada log pelayanan ditemukan.</span>
                                <span class="text-xs mt-1 block">Silakan ubah filter pencarian atau rentang tanggal Anda.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination links -->
        @if ($logs->hasPages())
            <div class="p-4 border-t border-hairline dark:border-white/10">
                {{ $logs->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            placeholder: "dd/mm/yyyy"
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            placeholder: "dd/mm/yyyy"
        });
    });
</script>
@endpush
