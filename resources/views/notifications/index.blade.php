@extends('layouts.private')

@section('title', 'Pusat Notifikasi - MPP Kota Sawahlunto')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-16">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-xl border border-hairline dark:border-white/10 shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-ink dark:text-white font-display">Pusat Notifikasi</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Pantau pemberitahuan pelayanan dan aktivitas antrean Anda.</p>
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary dark:text-accent-teal rounded-full text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.07 6.07 0 01-2-4.707V9a6 6 0 00-6-6 6 6 0 00-6 6v1.793a6.07 6.07 0 01-2 4.707v3.158c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Info Antrean</span>
            </span>
        </div>
    </div>

    <!-- Notification List Container -->
    <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border border-hairline dark:border-white/10 shadow-sm overflow-hidden">
        <div class="divide-y divide-hairline dark:divide-white/5">
            @forelse ($notifications as $notif)
                <a href="{{ route('notifications.show', $notif) }}" 
                   class="block p-5 sm:p-6 transition-all duration-150 hover:bg-surface-soft/40 dark:hover:bg-white/5 group relative {{ is_null($notif->read_at) ? 'bg-primary/5 dark:bg-primary/10' : '' }}">
                    
                    {{-- Unread Left Accent Bar --}}
                    @if (is_null($notif->read_at))
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary dark:bg-accent-teal"></div>
                    @endif

                    <div class="flex gap-4">
                        {{-- Icon --}}
                        <div class="shrink-0">
                            @if (is_null($notif->read_at))
                                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary dark:text-accent-teal flex items-center justify-center border border-primary/20 animate-pulse">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.07 6.07 0 01-2-4.707V9a6 6 0 00-6-6 6 6 0 00-6 6v1.793a6.07 6.07 0 01-2 4.707v3.158c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-surface-soft dark:bg-white/5 text-muted flex items-center justify-center border border-hairline dark:border-white/5">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.25 0l-2.25 1.5" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-sm text-ink dark:text-white group-hover:text-primary dark:group-hover:text-accent-teal transition-colors font-display">
                                    {{ $notif->title }}
                                </h4>
                                <span class="text-[11px] text-muted dark:text-on-dark-soft font-mono">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-body dark:text-on-dark-soft font-body leading-relaxed">
                                {{ $notif->message }}
                            </p>
                            @if (is_null($notif->read_at))
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary dark:text-accent-teal group-hover:underline">
                                        Isi Ulasan Pelayanan
                                        <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <!-- Empty State -->
                <div class="p-16 text-center space-y-4">
                    <div class="w-16 h-16 bg-surface-soft dark:bg-white/5 text-muted-soft dark:text-on-dark-soft/20 rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-base text-ink dark:text-white font-display">Belum Ada Notifikasi</h4>
                        <p class="text-xs text-muted dark:text-on-dark-soft max-w-sm mx-auto font-body">Anda belum menerima pemberitahuan pelayanan baru saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination Links --}}
        @if ($notifications->hasPages())
            <div class="p-4 bg-surface-soft/30 dark:bg-white/2 border-t border-hairline dark:border-white/5">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
