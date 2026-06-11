@extends('layouts.private')

@section('title', 'Pembatalan Booking — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16" 
         x-data="{ 
             modalOpen: false,
             actionUrl: '',
             bookingCode: '',
             userName: '',
             serviceName: '',
             reason: '',
             openCancelModal(url, code, name, service) {
                 this.actionUrl = url;
                 this.bookingCode = code;
                 this.userName = name;
                 this.serviceName = service;
                 this.reason = '';
                 this.modalOpen = true;
             }
         }">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-skipped opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-status-skipped"></span>
                    </span>
                    <span class="text-[11px] font-bold text-status-skipped uppercase tracking-widest font-display">Stasiun Pembatalan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Manajemen Pembatalan Booking</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Daftar booking online berstatus "Menunggu" yang dapat dibatalkan secara manual oleh Front Office.</p>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-status-serving/10 border border-status-serving/30 rounded-lg animate-pulse" role="alert">
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

        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Memproses</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Search Card --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
            <form action="{{ route('admin.fo.bookings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Cari berdasarkan NIK, Nama Warga, atau Kode Booking..."
                           class="w-full h-11 text-sm bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-xs shadow-xs hover:shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5 shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.fo.bookings.index') }}" class="h-11 px-4 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all flex items-center justify-center shrink-0">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Bookings Table Card --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-soft dark:bg-white/5 text-xs text-muted dark:text-on-dark-soft font-bold uppercase tracking-wider font-display border-b border-hairline dark:border-white/15">
                            <th class="py-4 px-6">Kode Booking</th>
                            <th class="py-4 px-6">Identitas Warga</th>
                            <th class="py-4 px-6">Tujuan Layanan</th>
                            <th class="py-4 px-6">Tanggal & Sesi</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline dark:divide-white/10 text-sm font-body text-body dark:text-on-dark">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/2 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="font-mono font-bold text-primary dark:text-accent-teal text-xs bg-primary/5 dark:bg-accent-teal/10 px-2 py-1 rounded-md">
                                        {{ $booking->booking_code }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-ink dark:text-white">{{ $booking->user->name }}</div>
                                    <div class="text-xs text-muted dark:text-on-dark-soft mt-0.5 font-mono">NIK: {{ $booking->user->nik ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-ink dark:text-white">{{ $booking->department->name }}</div>
                                    <div class="text-xs text-muted dark:text-on-dark-soft mt-0.5">{{ $booking->purpose }}</div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-semibold">{{ $booking->booking_date->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-muted dark:text-on-dark-soft mt-0.5">Sesi: {{ $booking->session_name ?? 'Umum' }}</div>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" 
                                            @click="openCancelModal('{{ route('admin.fo.bookings.cancel', $booking) }}', '{{ $booking->booking_code }}', '{{ $booking->user->name }}', '{{ $booking->purpose }}')"
                                            class="inline-flex h-9 items-center justify-center gap-1 px-4 bg-status-skipped/10 hover:bg-status-skipped text-status-skipped hover:text-white font-bold text-xs rounded-pill border border-status-skipped/20 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Batalkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-muted dark:text-on-dark-soft font-body">
                                    <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3 border border-hairline dark:border-white/5">
                                        <svg class="w-6 h-6 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold">Tidak Ada Data Booking Pending</span>
                                    <p class="text-xs mt-1">Daftar kosong atau tidak ada booking pending yang cocok dengan pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cancellation Modal Overlay --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none"
             :class="modalOpen ? 'opacity-100 pointer-events-auto' : ''"
             x-cloak>
            
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl p-6 md:p-8 max-w-md w-full border border-hairline dark:border-white/10 shadow-2xl transform scale-95 transition-transform duration-300 relative"
                 :class="modalOpen ? 'scale-100' : 'scale-95'"
                 @click.away="modalOpen = false">
                
                {{-- Close button --}}
                <button type="button" 
                        @click="modalOpen = false" 
                        class="absolute top-4 right-4 text-muted hover:text-ink dark:hover:text-white p-1 rounded-full hover:bg-surface-soft dark:hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="font-extrabold text-xl text-ink dark:text-white leading-tight font-display mb-2">Konfirmasi Pembatalan</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft mb-6 font-body">Anda akan membatalkan reservasi antrean berikut. Aksi ini tidak dapat diurungkan.</p>

                {{-- Target Booking Detail Card --}}
                <div class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 p-4 rounded-lg text-xs space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span class="text-muted font-medium">Kode Booking</span>
                        <span class="font-mono font-bold text-primary dark:text-accent-teal" x-text="bookingCode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted font-medium">Nama Warga</span>
                        <span class="font-bold text-ink dark:text-white" x-text="userName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted font-medium">Layanan</span>
                        <span class="font-bold text-ink dark:text-white" x-text="serviceName"></span>
                    </div>
                </div>

                {{-- Form input reason --}}
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="reason" class="block text-sm font-bold text-ink dark:text-white font-display">
                            Alasan Pembatalan
                        </label>
                        <textarea id="reason" 
                                  name="reason" 
                                  rows="3" 
                                  required 
                                  x-model="reason"
                                  placeholder="Contoh: Dokumen persyaratan tidak lengkap, atau atas permohonan warga..." 
                                  class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all"></textarea>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft font-body">Minimal 5 karakter. Alasan ini akan dicantumkan pada notifikasi email warga.</p>
                    </div>

                    <div class="pt-4 border-t border-hairline dark:border-white/10 flex justify-end gap-3">
                        <button type="button" 
                                @click="modalOpen = false" 
                                class="h-11 px-5 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold rounded-pill text-xs border border-hairline dark:border-white/10 transition-all cursor-pointer">
                            Kembali
                        </button>
                        <button type="submit" 
                                :disabled="reason.trim().length < 5"
                                :class="reason.trim().length < 5 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                                class="h-11 px-6 bg-status-skipped text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Batalkan Booking
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection
