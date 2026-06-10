@extends('layouts.private')

@section('title', 'Log Pelayanan - MPP Kota Sawahlunto')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16">
    {{-- Header Banner --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white font-display tracking-tight">Log Pelayanan</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">
                Riwayat log pelayanan antrean lampau untuk Instansi <span class="font-semibold text-primary dark:text-accent-teal">{{ Auth::user()->department ? Auth::user()->department->name : '-' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.log-pelayanan.export', request()->all()) }}" 
               class="h-11 px-5 bg-canvas border border-hairline text-ink dark:text-white dark:border-white/15 hover:bg-surface-soft dark:hover:bg-white/10 font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Ekspor Excel/CSV
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

        <!-- Card Antrean Batal -->
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center relative overflow-hidden font-body">
            <div class="space-y-1">
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider font-display">Total Antrean Batal</span>
                <span class="text-3xl font-black text-ink dark:text-white block font-mono">{{ $totalCancelled }}</span>
                <span class="text-xs text-muted dark:text-on-dark-soft block">Batal/Dilewati (Cancelled)</span>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Search Code/Nama Warga -->
            <div class="col-span-1 sm:col-span-2 md:col-span-1">
                <label for="search" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Cari Antrean</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Kode Booking / Nama..."
                           class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md pl-10 pr-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted dark:text-on-dark-soft">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Date Range Picker: Mulai -->
            <div>
                <label for="start_date" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Tanggal Mulai</label>
                <input type="date" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
            </div>

            <!-- Date Range Picker: Akhir -->
            <div>
                <label for="end_date" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Tanggal Akhir</label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
            </div>

            <!-- Dropdown Jenis Layanan -->
            <div>
                <label for="service_id" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Jenis Layanan</label>
                <select id="service_id" 
                        name="service_id"
                        class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <option value="">Semua Layanan</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv->id }}" {{ request('service_id') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Dropdown Status -->
            <div>
                <label for="status" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Status</label>
                <select id="status" 
                        name="status"
                        class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 h-12 focus:outline-none focus:border-primary dark:focus:border-accent-teal focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <option value="">Semua Status Lampau</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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

    {{-- Main Table Grid --}}
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto font-body">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/15">
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Tanggal</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Kode Booking</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Nama Warga</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Keperluan</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Layanan</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Jam Datang</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Jam Selesai/Batal</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Status</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-ink dark:text-white font-display">Catatan / Alasan Batal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline dark:divide-white/10 text-body-sm text-ink dark:text-on-dark-soft">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-black/2 dark:hover:bg-white/2 transition-colors">
                            <td class="p-4 whitespace-nowrap">
                                {{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="p-4 font-mono font-bold text-primary dark:text-accent-teal whitespace-nowrap">
                                {{ $booking->booking_code }}
                            </td>
                            <td class="p-4">
                                {{ $booking->user ? $booking->user->name : '-' }}
                            </td>
                            <td class="p-4 max-w-xs truncate" title="{{ $booking->purpose }}">
                                {{ $booking->purpose ?? '—' }}
                            </td>
                            <td class="p-4">
                                {{ $booking->service ? $booking->service->name : '-' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                {{ $booking->checked_in_at ? $booking->checked_in_at->format('H:i:s') : '—' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                {{ in_array($booking->status, ['Completed', 'Cancelled']) && $booking->updated_at ? $booking->updated_at->format('H:i:s') : '—' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if ($booking->status === 'Completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-done/10 text-status-done border border-status-done/15">
                                        <span class="w-2 h-2 rounded-full bg-status-done"></span>
                                        Completed
                                    </span>
                                @elseif ($booking->status === 'Cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-skipped/10 text-status-skipped border border-status-skipped/15">
                                        <span class="w-2 h-2 rounded-full bg-status-skipped"></span>
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-waiting/10 text-status-waiting border border-status-waiting/15">
                                        <span class="w-2 h-2 rounded-full bg-status-waiting"></span>
                                        {{ $booking->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 max-w-xs truncate" title="{{ $booking->cancel_reason }}">
                                {{ $booking->cancel_reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-muted dark:text-on-dark-soft">
                                <svg class="w-12 h-12 mx-auto mb-3 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span class="text-base font-semibold block">Tidak ada log pelayanan ditemukan.</span>
                                <span class="text-xs mt-1 block">Silakan ubah filter pencarian atau tanggal Anda.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination links -->
        @if ($bookings->hasPages())
            <div class="p-4 border-t border-hairline dark:border-white/10">
                {{ $bookings->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
