@extends('layouts.private')

@section('title', 'Riwayat Antrean - MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8 pb-16">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Riwayat & Status Antrean</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Pantau antrean aktif Anda atau lihat kembali riwayat pelayanan sebelumnya.</p>
            </div>
            <div>
                <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Ambil Antrean Baru
                </a>
            </div>
        </div>

        {{-- Session Flash Alerts --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-status-serving/10 border border-status-serving/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-serving font-display">Berhasil</p>
                    <p class="text-sm text-green-800 dark:text-green-300 font-body mt-0.5">{!! session('success') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()" class="shrink-0 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Kesalahan</p>
                    <p class="text-sm text-red-800 dark:text-red-300 font-body mt-0.5">{!! session('error') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()" class="shrink-0 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Bookings Section --}}
        @php
            $activeBookings = $bookings->filter(fn($b) => in_array($b->status, ['Pending', 'Checked-In', 'Confirmed', 'Serving']));
            $pastBookings = $bookings->filter(fn($b) => !in_array($b->status, ['Pending', 'Checked-In', 'Confirmed', 'Serving']));
        @endphp

        <div class="space-y-12">
            
            {{-- ACTIVE BOOKINGS --}}
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-ink dark:text-white font-display tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-primary dark:bg-accent-teal rounded-full"></span>
                    Antrean Aktif
                    <span class="text-xs font-normal text-muted dark:text-on-dark-soft bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-2 py-0.5 rounded-md font-mono">{{ $activeBookings->count() }}</span>
                </h2>

                @if($activeBookings->isEmpty())
                    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs p-8 text-center space-y-4">
                        <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 text-primary dark:text-accent-teal rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-ink dark:text-white font-display">Tidak ada antrean aktif</h3>
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body mt-1 max-w-sm mx-auto">Anda tidak memiliki booking antrean aktif hari ini. Ambil nomor antrean untuk mulai mendapatkan pelayanan.</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($activeBookings as $booking)
                            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between">
                                {{-- Card Header --}}
                                <div class="p-5 border-b border-hairline dark:border-white/15 space-y-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-mono font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block">KODE BOOKING</span>
                                            <span class="font-mono font-bold text-primary dark:text-accent-teal text-sm bg-primary/5 dark:bg-accent-teal/10 px-2.5 py-1 rounded-md">{{ $booking->booking_code }}</span>
                                        </div>
                                        
                                        {{-- Dynamic Status Badge --}}
                                        @php
                                            $statusClass = match($booking->status) {
                                                'Pending' => 'bg-amber-500/12 text-amber-800 dark:text-amber-400 border border-amber-500/20',
                                                'Checked-In', 'Confirmed' => 'bg-primary/12 text-primary dark:text-accent-teal border border-primary/20',
                                                'Serving' => 'bg-green-500/12 text-green-800 dark:text-green-400 border border-green-500/20',
                                                default => 'bg-gray-500/10 text-muted dark:text-on-dark-soft border border-gray-500/10',
                                            };
                                            $statusText = match($booking->status) {
                                                'Pending' => 'Menunggu Check-In',
                                                'Checked-In', 'Confirmed' => 'Terkonfirmasi FO',
                                                'Serving' => 'Sedang Dilayani',
                                                default => $booking->status,
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-muted dark:text-on-dark-soft font-display uppercase tracking-wider">Instansi / Departemen</h3>
                                        <h4 class="text-base font-bold text-ink dark:text-white font-display mt-0.5">{{ $booking->service->department->name }}</h4>
                                        <p class="text-xs text-primary dark:text-accent-teal font-semibold mt-0.5 font-body">{{ $booking->service->name }}</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-xs bg-surface-soft dark:bg-white/5 p-3 rounded-lg border border-hairline dark:border-white/5">
                                        <div>
                                            <span class="block text-muted dark:text-on-dark-soft font-semibold mb-0.5 font-display">Tanggal Pelayanan</span>
                                            <span class="font-bold text-ink dark:text-white font-body">{{ $booking->booking_date->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-muted dark:text-on-dark-soft font-semibold mb-0.5 font-display">Sesi Waktu</span>
                                            <span class="font-bold text-ink dark:text-white font-body">{{ $booking->schedule->session_name ?? 'Fleksibel' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Footer / Action --}}
                                <div class="bg-surface-soft dark:bg-white/5 px-5 py-4 border-t border-hairline dark:border-white/10 flex items-center justify-between gap-4">
                                    <span class="text-xs text-muted dark:text-on-dark-soft font-body flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Dibuat {{ $booking->created_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('booking.show', $booking) }}" class="inline-flex h-9 items-center justify-center gap-1.5 px-4 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-pill shadow-xs hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                        Lihat Tiket
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- PAST BOOKINGS --}}
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-ink dark:text-white font-display tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-muted dark:bg-on-dark-soft rounded-full"></span>
                    Riwayat Antrean Lampau
                    <span class="text-xs font-normal text-muted dark:text-on-dark-soft bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 px-2 py-0.5 rounded-md font-mono">{{ $pastBookings->count() }}</span>
                </h2>

                @if($pastBookings->isEmpty())
                    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs p-8 text-center space-y-2">
                        <p class="text-sm text-muted dark:text-on-dark-soft font-body">Belum ada riwayat pelayanan yang tercatat.</p>
                    </div>
                @else
                    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-soft dark:bg-white/5 text-xs text-muted dark:text-on-dark-soft font-bold uppercase tracking-wider font-display border-b border-hairline dark:border-white/15">
                                        <th class="py-4 px-6">Tanggal</th>
                                        <th class="py-4 px-6">Kode Booking</th>
                                        <th class="py-4 px-6">Instansi & Layanan</th>
                                        <th class="py-4 px-6">Sesi</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-hairline dark:divide-white/10 text-sm font-body text-body dark:text-on-dark">
                                    @foreach($pastBookings as $booking)
                                        <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/2 transition-colors">
                                            <td class="py-4 px-6 font-semibold whitespace-nowrap">
                                                {{ $booking->booking_date->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-4 px-6 font-mono font-bold tracking-tight text-ink dark:text-white whitespace-nowrap">
                                                {{ substr($booking->booking_code, 0, 8) }}...
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-ink dark:text-white">{{ $booking->service->department->name }}</div>
                                                <div class="text-xs text-muted dark:text-on-dark-soft mt-0.5">{{ $booking->service->name }}</div>
                                            </td>
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                {{ $booking->schedule->session_name ?? '-' }}
                                            </td>
                                            <td class="py-4 px-6 whitespace-nowrap">
                                                @php
                                                    $statusClass = match($booking->status) {
                                                        'Completed' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-700',
                                                        'Skipped' => 'bg-red-500/12 text-red-800 dark:text-red-400 border border-red-500/20',
                                                        'Cancelled' => 'bg-gray-500/10 text-muted dark:text-on-dark-soft border border-gray-500/10',
                                                        default => 'bg-gray-500/10 text-muted dark:text-on-dark-soft border border-gray-500/10',
                                                    };
                                                    $statusText = match($booking->status) {
                                                        'Completed' => 'Selesai',
                                                        'Skipped' => 'Terlewat',
                                                        'Cancelled' => 'Dibatalkan',
                                                        default => $booking->status,
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    {{ $statusText }}
                                                </span>
                                                @if($booking->status === 'Cancelled' && $booking->cancel_reason)
                                                    <div class="text-[11px] text-status-skipped dark:text-red-400 mt-1 font-medium italic max-w-[150px] truncate" title="{{ $booking->cancel_reason }}">
                                                        Alasan: {{ $booking->cancel_reason }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                <a href="{{ route('booking.show', $booking) }}" class="inline-flex items-center justify-center gap-1 text-xs font-bold text-primary dark:text-accent-teal hover:underline focus-visible:outline-none focus-visible:underline h-9 px-3 hover:bg-primary/5 dark:hover:bg-accent-teal/5 rounded-pill transition-colors cursor-pointer">
                                                    Detail
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>
@endsection
