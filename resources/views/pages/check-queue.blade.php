@extends('layouts.public')

@section('title', 'Cek Status Antrean - MPP Kota Sawahlunto')

@section('content')
<section class="py-16 md:py-24 bg-surface-soft min-h-[calc(100vh-4rem-30rem)] flex items-center">
    <div class="max-w-md mx-auto w-full px-4">
        <!-- Card container -->
        <div class="bg-canvas border border-hairline rounded-xl p-8 shadow-sm">
            <h1 class="font-display font-bold text-ink text-title-lg mb-2">Cek Status Antrean</h1>
            <p class="text-body-sm text-muted mb-6">Masukkan kode booking atau nomor tiket digital Anda untuk memantau status secara langsung.</p>
            
            @if (session('error'))
                <div class="mb-5 p-4 rounded-md bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-900/50 text-body-sm flex gap-2">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{!! session('error') !!}</span>
                </div>
            @endif

            <form action="{{ route('public.check.process') }}" method="POST" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label for="queue_code" class="block font-display font-semibold text-title-sm text-ink">Nomor Antrean / Kode Booking</label>
                    <input type="text" 
                           id="queue_code" 
                           name="code" 
                           required 
                           value="{{ old('code') }}"
                           placeholder="Contoh: A-024 atau BK-10293" 
                           class="block w-full px-4 py-3 border border-hairline rounded-md bg-canvas text-ink text-body-md focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary/12 transition-all">
                </div>
                
                <button type="submit" class="w-full inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-6 py-3 h-12 rounded-pill transition-colors shadow-sm cursor-pointer">
                    Cek Status Sekarang
                </button>
            </form>

            @if (isset($searched) && $searched && isset($queue))
                <div class="mt-8 p-6 bg-surface-soft border border-hairline rounded-lg space-y-4">
                    <h2 class="font-display font-bold text-ink text-body-lg border-b border-hairline-soft pb-2">Status Antrean Saat Ini</h2>
                    <div class="grid grid-cols-2 gap-3 text-body-sm">
                        <span class="text-muted">Nomor Antrean:</span>
                        <span class="font-bold text-primary text-right">{{ $queue->queue_number }}</span>
                        
                        <span class="text-muted">Instansi:</span>
                        <span class="font-semibold text-ink text-right">{{ $queue->department?->name ?? '-' }}</span>

                        <span class="text-muted">Keperluan / Layanan:</span>
                        <span class="font-semibold text-ink text-right">{{ $queue->purpose ?? '-' }}</span>

                        <span class="text-muted">Status:</span>
                        <span class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                @if($queue->status === 'Serving') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif(in_array($queue->status, ['Checked-In', 'Booked'])) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 @endif">
                                {{ $queue->status }}
                            </span>
                        </span>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Back to Home Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-body-sm text-muted hover:text-primary transition-colors inline-flex items-center gap-1 justify-center">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</section>
@endsection
