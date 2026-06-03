@extends('layouts.private')

@section('title', 'Berikan Ulasan - MPP Kota Sawahlunto')

@section('content')
<div class="max-w-xl mx-auto space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
        @if (Auth::user()->role instanceof \App\Enums\UserRole ? Auth::user()->role->value === 'admin_fo' : Auth::user()->role === 'admin_fo')
            <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 text-status-waiting rounded-full text-xs font-semibold w-fit mb-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Pengisian oleh Front Office (Atas Nama Warga)</span>
            </div>
        @endif
        <h2 class="text-2xl font-bold text-ink dark:text-white font-display">Berikan Ulasan & Feedback</h2>
        <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Ulasan Anda membantu kami meningkatkan kualitas pelayanan Mal Pelayanan Publik.</p>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm p-6 space-y-4 relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-24 h-24 bg-primary/5 dark:bg-accent-teal/5 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">NOMOR ANTREAN</span>
                <h4 class="text-3xl font-extrabold text-primary dark:text-accent-teal mt-1 font-mono tracking-tight">{{ $queue->queue_number }}</h4>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-widest font-display">TANGGAL</span>
                <p class="text-sm font-semibold text-ink dark:text-white mt-1 font-body">
                    {{ $queue->queue_date ? $queue->queue_date->format('d M Y') : '-' }}
                </p>
            </div>
        </div>

        <div class="border-t border-hairline dark:border-white/5 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-body">
            <div>
                <span class="text-muted dark:text-on-dark-soft block">Instansi</span>
                <span class="font-bold text-ink dark:text-white mt-0.5 block">
                    {{ ($queue->counter && $queue->counter->department) ? $queue->counter->department->name : '-' }}
                </span>
            </div>
            <div>
                <span class="text-muted dark:text-on-dark-soft block">Layanan</span>
                <span class="font-bold text-ink dark:text-white mt-0.5 block">
                    {{ $queue->service ? $queue->service->name : '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Feedback Form Card -->
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm p-6">
        <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="queue_id" value="{{ $queue->id }}">

            {{-- Interactive Rating Section --}}
            <div class="space-y-3 text-center">
                <label class="block text-sm font-bold text-ink dark:text-white uppercase tracking-wider font-display">Seberapa puas Anda dengan pelayanan kami?</label>
                
                <div x-data="{ rating: {{ old('rating', 0) }}, hoverRating: 0 }" class="flex flex-col items-center gap-3 py-2">
                    <div class="flex items-center gap-2">
                        <template x-for="star in 5">
                            <button type="button" 
                                    @click="rating = star" 
                                    @mouseover="hoverRating = star" 
                                    @mouseleave="hoverRating = 0"
                                    class="w-12 h-12 flex items-center justify-center text-gray-300 dark:text-gray-600 transition-all duration-150 transform hover:scale-110 focus:outline-none cursor-pointer"
                                    :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-amber-400 dark:text-amber-400' : 'text-gray-300 dark:text-gray-600'">
                                <svg class="w-10 h-10 stroke-current fill-current" viewBox="0 0 24 24">
                                    <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.136 9.21l8.2-1.192z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    <!-- Hidden input to store rating -->
                    <input type="hidden" name="rating" :value="rating">
                    
                    <div class="text-xs font-bold uppercase tracking-wider h-4">
                        <span x-show="rating === 1" class="text-rose-500">Sangat Kurang</span>
                        <span x-show="rating === 2" class="text-amber-500">Kurang</span>
                        <span x-show="rating === 3" class="text-yellow-500">Cukup</span>
                        <span x-show="rating === 4" class="text-blue-500">Baik</span>
                        <span x-show="rating === 5" class="text-emerald-500">Sangat Baik</span>
                    </div>
                </div>
                
                @error('rating')
                    <p class="text-xs text-status-skipped mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Comment Textarea Section --}}
            <div class="space-y-2">
                <label for="comment" class="block text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display">Komentar, Kritik & Saran</label>
                <div x-data="{ count: {{ strlen(old('comment', '')) }}, max: 1000 }">
                    <textarea id="comment" name="comment" rows="4" 
                              @input="count = $el.value.length"
                              placeholder="Bagikan pengalaman pelayanan Anda (opsional)..."
                              class="w-full bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/10 text-ink dark:text-white rounded-md p-3 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal placeholder:text-muted/60 leading-relaxed">{{ old('comment') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        @error('comment')
                            <p class="text-xs text-status-skipped font-semibold">{{ $message }}</p>
                        @else
                            <div></div>
                        @enderror
                        <span class="text-[10px] text-muted font-mono" x-text="count + '/' + max"></span>
                    </div>
                </div>
            </div>

            {{-- Submit Action Button --}}
            <div class="pt-4 flex gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 h-11 flex items-center justify-center bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill text-xs font-bold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 h-11 flex items-center justify-center bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs transition-all shadow-md hover:shadow-lg focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    Kirim Feedback
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
