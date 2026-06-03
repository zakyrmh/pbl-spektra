@extends('layouts.private')

@section('title', 'Tiket Antrean — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-md mx-auto space-y-6 pb-16 print:p-0 print:max-w-full print:bg-white print:text-black">
        
        {{-- Navigation back button (Hidden in print) --}}
        <div class="flex items-center justify-between print:hidden">
            <a href="{{ route('booking.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-muted hover:text-ink dark:hover:text-white transition-colors focus-visible:outline-none focus-visible:underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Riwayat
            </a>
            
            <button onclick="window.print()" class="inline-flex h-9 items-center justify-center gap-1.5 px-4 bg-surface-soft hover:bg-surface-strong dark:bg-white/5 dark:hover:bg-white/10 text-ink dark:text-white font-semibold text-xs rounded-pill border border-hairline dark:border-white/10 transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Tiket
            </button>
        </div>

        {{-- Ticket Container --}}
        <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-lg overflow-hidden print:border-none print:shadow-none">
            
            {{-- Ticket Header --}}
            <div class="bg-linear-to-r from-primary to-primary-hover p-6 text-white text-center space-y-2 relative">
                <div class="flex items-center justify-center gap-2">
                    <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Sawahlunto Logo" class="h-8 object-contain">
                    <div class="text-left">
                        <h2 class="text-xs font-bold uppercase tracking-wider font-display text-white/90">Mal Pelayanan Publik</h2>
                        <h3 class="text-sm font-extrabold uppercase tracking-tight font-display">Kota Sawahlunto</h3>
                    </div>
                </div>
                <div class="absolute -bottom-3 left-0 right-0 flex justify-between px-4 overflow-hidden pointer-events-none select-none print:hidden">
                    @for ($i = 0; $i < 12; $i++)
                        <span class="w-4 h-4 bg-surface-soft dark:bg-surface-dark rounded-full shrink-0"></span>
                    @endfor
                </div>
            </div>

            {{-- Ticket Content --}}
            <div class="p-6 md:p-8 space-y-6 pt-8">
                
                {{-- Queue Number Section (If Checked-In) or Status Badge --}}
                <div class="text-center space-y-2">
                    @if($booking->queue)
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Nomor Antrean Anda</span>
                        <div class="text-4xl sm:text-5xl font-extrabold text-primary dark:text-accent-teal tracking-tight font-mono">
                            {{ $booking->queue->queue_number }}
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                            Telah Terkonfirmasi FO
                        </span>
                    @else
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Status Booking</span>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-full text-sm font-bold border border-amber-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Menunggu Check-In FO
                        </div>
                    @endif
                </div>

                {{-- QR Code Area --}}
                <div class="flex flex-col items-center space-y-3">
                    <div class="bg-white p-4 rounded-lg border border-hairline inline-block shadow-inner mx-auto relative group">
                        <svg class="w-40 h-40 mx-auto" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Positioning Squares -->
                            <rect x="0" y="0" width="25" height="25" fill="#1e293b" />
                            <rect x="3" y="3" width="19" height="19" fill="#ffffff" />
                            <rect x="6" y="6" width="13" height="13" fill="#1B4FA8" />

                            <rect x="75" y="0" width="25" height="25" fill="#1e293b" />
                            <rect x="78" y="3" width="19" height="19" fill="#ffffff" />
                            <rect x="81" y="6" width="13" height="13" fill="#1B4FA8" />

                            <rect x="0" y="75" width="25" height="25" fill="#1e293b" />
                            <rect x="3" y="78" width="19" height="19" fill="#ffffff" />
                            <rect x="6" y="81" width="13" height="13" fill="#1B4FA8" />

                            <!-- Small alignment squares -->
                            <rect x="70" y="70" width="10" height="10" fill="#1e293b" />
                            <rect x="72" y="72" width="6" height="6" fill="#ffffff" />
                            <rect x="74" y="74" width="2" height="2" fill="#1B4FA8" />

                            <!-- Randomly scattered blocks mimicking QR patterns -->
                            <rect x="30" y="2" width="10" height="4" fill="#1e293b" />
                            <rect x="45" y="5" width="8" height="5" fill="#1e293b" />
                            <rect x="60" y="3" width="5" height="15" fill="#1B4FA8" />
                            <rect x="35" y="12" width="12" height="6" fill="#1e293b" />
                            
                            <rect x="2" y="30" width="15" height="5" fill="#1e293b" />
                            <rect x="25" y="28" width="6" height="12" fill="#1B4FA8" />
                            <rect x="38" y="32" width="20" height="8" fill="#1e293b" />
                            <rect x="65" y="25" width="8" height="12" fill="#1e293b" />
                            
                            <rect x="5" y="50" width="12" height="6" fill="#1B4FA8" />
                            <rect x="25" y="48" width="15" height="10" fill="#1e293b" />
                            <rect x="48" y="45" width="25" height="5" fill="#1e293b" />
                            <rect x="80" y="35" width="12" height="15" fill="#1B4FA8" />
                            
                            <rect x="35" y="65" width="15" height="15" fill="#1e293b" />
                            <rect x="55" y="60" width="10" height="10" fill="#1B4FA8" />
                            <rect x="68" y="55" width="8" height="8" fill="#1e293b" />
                            
                            <rect x="30" y="85" width="25" height="6" fill="#1B4FA8" />
                            <rect x="60" y="82" width="6" height="12" fill="#1e293b" />
                            
                            <!-- Custom logo in the middle -->
                            <rect x="40" y="40" width="20" height="20" fill="#ffffff" rx="2" />
                            <circle cx="50" cy="50" r="8" fill="#1B4FA8" />
                            <circle cx="50" cy="50" r="5" fill="#ffffff" />
                        </svg>
                    </div>
                    <span class="text-[10px] text-muted font-body tracking-normal text-center">Scan kode ini di barcode reader stasiun check-in Front Office</span>
                </div>

                {{-- Booking UUID & Copy Action --}}
                <div class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 p-4 rounded-lg text-center space-y-1.5"
                     x-data="{ copied: false }">
                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">KODE BOOKING</span>
                    <div class="text-base font-extrabold text-ink dark:text-white tracking-wider font-mono select-all">{{ $booking->booking_code }}</div>
                    
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $booking->booking_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary dark:text-accent-teal hover:underline focus:outline-none cursor-pointer mt-1 print:hidden">
                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <span x-show="!copied">Salin Kode Booking</span>
                        <span x-show="copied" class="text-status-serving flex items-center gap-1 font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Tersalin!
                        </span>
                    </button>
                </div>

                {{-- Ticket Details --}}
                <div class="space-y-3.5 border-t border-dashed border-hairline dark:border-white/15 pt-6 text-sm font-body">
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Nama Warga</span>
                        <span class="font-bold text-ink dark:text-white text-right">{{ $booking->user->name }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">NIK Warga</span>
                        <span class="font-bold text-ink dark:text-white font-mono text-right">{{ $booking->user->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Instansi</span>
                        <span class="font-bold text-ink dark:text-white text-right">{{ $booking->service->department->name }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Pelayanan</span>
                        <span class="font-bold text-primary dark:text-accent-teal text-right">{{ $booking->service->name }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Tanggal</span>
                        <span class="font-bold text-ink dark:text-white text-right">{{ $booking->booking_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted dark:text-on-dark-soft font-medium">Sesi</span>
                        <span class="font-bold text-ink dark:text-white text-right">{{ $booking->schedule->session_name ?? '-' }}</span>
                    </div>
                    
                    @if(!$booking->queue)
                    <div class="flex justify-between gap-4 pt-3 border-t border-hairline dark:border-white/10">
                        <span class="text-muted dark:text-on-dark-soft font-medium flex items-center gap-1">
                            Estimasi Urutan
                            <span class="relative group cursor-help select-none print:hidden">
                                <svg class="w-3.5 h-3.5 text-muted hover:text-ink dark:hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none text-center font-normal leading-normal">Jumlah antrean berstatus pending sebelum tiket ini pada tanggal pelayanan yang sama.</span>
                            </span>
                        </span>
                        <span class="font-bold text-status-waiting text-right">Ke-{{ $estimatedPosition }} di antrean</span>
                    </div>
                    @endif
                </div>

                {{-- Important notes (Hidden in print) --}}
                <div class="bg-amber-500/5 dark:bg-amber-400/5 border border-amber-500/15 p-4 rounded-lg space-y-1.5 print:hidden">
                    <h4 class="text-xs font-bold text-amber-800 dark:text-amber-400 font-display flex items-center gap-1">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        PENTING: Langkah Konfirmasi
                    </h4>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400/90 leading-relaxed font-body">
                        Tiket ini belum sah dipanggil sebelum Anda melakukan <strong>Check-In di loket Front Office MPP</strong> dengan memindai kode QR di atas. Datang paling lambat 15 menit sebelum sesi dimulai.
                    </p>
                </div>

            </div>
            
            {{-- Ticket Footer --}}
            <div class="bg-surface-soft dark:bg-white/2 px-6 py-4 text-center border-t border-hairline dark:border-white/10">
                <span class="text-[10px] text-muted dark:text-on-dark-soft font-body font-medium">MPP Kota Sawahlunto · Layanan Civic-Digital</span>
            </div>

        </div>

    </div>

    {{-- Custom print layout style --}}
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            /* Hide private layout elements like sidebar, header, etc */
            aside, header, main > div > div.print\:hidden {
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
            /* Reset ticket styles for simple paper print */
            .bg-canvas, .dark\:bg-surface-dark-elevated {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
            }
            .text-ink, .text-muted, .text-body, .dark\:text-white, .dark\:text-on-dark-soft {
                color: black !important;
            }
            .bg-linear-to-r {
                background: #1B4FA8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .text-white {
                color: white !important;
            }
            .bg-surface-soft {
                background: #f3f4f6 !important;
            }
        }
    </style>
@endsection
