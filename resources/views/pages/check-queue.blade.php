@extends('layouts.public')

@section('title', 'Cek Status Antrean - MPP Kota Sawahlunto')

@section('content')
<section class="py-16 md:py-24 bg-surface-soft min-h-[calc(100vh-4rem-30rem)] flex items-center">
    <div class="max-w-md mx-auto w-full px-4">
        <!-- Card container -->
        <div class="bg-canvas border border-hairline rounded-xl p-8 shadow-sm">
            <h1 class="font-display font-bold text-ink text-title-lg mb-2">Cek Status Antrean</h1>
            <p class="text-body-sm text-muted mb-6">Masukkan kode booking atau nomor tiket digital Anda untuk memantau status secara langsung.</p>
            
            <form action="{{ route('public.check.process') }}" method="POST" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label for="queue_code" class="block font-display font-semibold text-title-sm text-ink">Nomor Antrean / Kode Booking</label>
                    <input type="text" 
                           id="queue_code" 
                           name="code" 
                           required 
                           placeholder="Contoh: A-024 atau BK-10293" 
                           class="block w-full px-4 py-3 border border-hairline rounded-md bg-canvas text-ink text-body-md focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary/12 transition-all">
                </div>
                
                <button type="submit" class="w-full inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-6 py-3 h-12 rounded-pill transition-colors shadow-sm cursor-pointer">
                    Cek Status Sekarang
                </button>
            </form>
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
