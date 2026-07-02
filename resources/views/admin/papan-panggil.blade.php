@extends('layouts.private')

@section('title', 'Papan Panggil Instansi - MPP Kota Sawahlunto')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16">
    {{-- Header Banner --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white font-display tracking-tight">Papan Panggil Instansi</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">
                Mengelola antrean warga untuk Instansi <span class="font-semibold text-primary dark:text-accent-teal">{{ $department->name }}</span>
            </p>
        </div>
        @if($counter)
            <div class="px-4 py-2 bg-primary/10 dark:bg-accent-teal/10 border border-primary/20 dark:border-accent-teal/20 rounded-pill text-xs font-semibold text-primary dark:text-accent-teal">
                Loket Aktif: {{ $counter->name }}
            </div>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 rounded-lg text-sm" role="alert">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 text-rose-800 dark:text-rose-300 rounded-lg text-sm" role="alert">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 text-rose-800 dark:text-rose-300 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Now Serving Panel (Left, 7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between min-h-[420px]">
                <div class="space-y-4">
                    <div class="border-b border-hairline dark:border-white/10 pb-3 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-ink dark:text-white font-display">Sedang Dilayani (Now Serving)</h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold font-display {{ $activeBooking ? 'bg-status-serving/12 text-[#065F46] dark:text-green-400 border border-status-serving/15' : 'bg-status-closed/10 text-body dark:text-on-dark-soft border border-status-closed/15' }}">
                            <span class="w-2 h-2 rounded-full {{ $activeBooking ? 'bg-status-serving animate-pulse' : 'bg-status-closed' }}"></span>
                            {{ $activeBooking ? 'Aktif' : 'Idle' }}
                        </span>
                    </div>

                    @if($activeBooking)
                        {{-- Large Active Booking Ticket details --}}
                        <div class="bg-surface-soft dark:bg-white/5 border border-hairline dark:border-white/5 rounded-lg p-6 flex flex-col space-y-4 relative overflow-hidden">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display block">Kode Booking</span>
                                    <span class="text-display-lg font-bold text-primary dark:text-accent-teal font-mono tracking-tight my-1 block">{{ $activeBooking->booking_code }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display block mb-1">Status Tiket</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-status-called/12 text-primary dark:text-accent-teal border border-status-called/15 rounded-pill text-caption font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-status-called animate-pulse"></span>
                                        {{ $activeBooking->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-hairline dark:border-white/10">
                                <div>
                                    <span class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display block">Nama Pengunjung</span>
                                    <span class="font-semibold text-ink dark:text-white text-body-md">{{ $activeBooking->user ? $activeBooking->user->name : 'Warga' }}</span>
                                    <span class="text-caption text-muted dark:text-on-dark-soft block font-mono">NIK: {{ $activeBooking->user ? $activeBooking->user->nik : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display block">Waktu Check-In</span>
                                    <span class="font-semibold text-ink dark:text-white text-body-sm">
                                        {{ $activeBooking->checked_in_at ? $activeBooking->checked_in_at->format('H:i') . ' WIB' : '-' }}
                                    </span>
                                    <span class="text-caption text-muted dark:text-on-dark-soft block">
                                        {{ $activeBooking->checked_in_at ? $activeBooking->checked_in_at->translatedFormat('d F Y') : 'Belum Check-In' }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-hairline dark:border-white/10">
                                <span class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display block">Keperluan / Keterangan</span>
                                <p class="text-body-sm text-ink dark:text-white font-body bg-canvas dark:bg-surface-dark-elevated p-3 rounded-md border border-hairline dark:border-white/5 mt-1 leading-relaxed">
                                    {{ $activeBooking->purpose }}
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- Empty state --}}
                        <div class="flex flex-col items-center justify-center py-12 text-center bg-surface-soft dark:bg-white/5 rounded-lg border border-dashed border-hairline dark:border-white/10">
                            <div class="w-16 h-16 bg-canvas dark:bg-white/5 text-muted rounded-full flex items-center justify-center mb-3 border border-hairline dark:border-white/5">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-ink dark:text-white font-display">Belum Ada Antrean Aktif</h3>
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body max-w-[280px] mt-1">Silakan klik "Panggil Berikutnya" untuk melayani antrean berikutnya.</p>
                        </div>
                    @endif
                </div>

                {{-- Action Controls --}}
                <div class="pt-6 border-t border-hairline dark:border-white/10 mt-6">
                    <h3 class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display mb-3">Kontrol Antrean</h3>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('admin.papan-panggil.next') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-xs transition-all cursor-pointer inline-flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                                Panggil Berikutnya
                            </button>
                        </form>

                        @if($activeBooking)
                            <form action="{{ route('admin.papan-panggil.complete', $activeBooking->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="h-11 px-6 bg-status-serving/10 hover:bg-status-serving/20 text-status-serving border border-status-serving/20 font-semibold rounded-pill shadow-xs transition-all cursor-pointer inline-flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai (Completed)
                                </button>
                            </form>

                            {{-- Button trigger skip modal / accordion --}}
                            <button type="button" 
                                    x-data="{}"
                                    @click="$dispatch('open-skip-panel')"
                                    class="h-11 px-6 bg-status-skipped/10 hover:bg-status-skipped/20 text-status-skipped border border-status-skipped/20 font-semibold rounded-pill shadow-xs transition-all cursor-pointer inline-flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Lewati (Cancelled)
                            </button>
                        @else
                            <button type="button" disabled class="h-11 px-6 bg-surface-strong dark:bg-white/5 text-muted-soft dark:text-on-dark-soft/30 font-semibold rounded-pill cursor-not-allowed inline-flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Selesai (Completed)
                            </button>
                            <button type="button" disabled class="h-11 px-6 bg-surface-strong dark:bg-white/5 text-muted-soft dark:text-on-dark-soft/30 font-semibold rounded-pill cursor-not-allowed inline-flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Lewati (Cancelled)
                            </button>
                        @endif
                    </div>

                    {{-- Collapsible Skip Form --}}
                    @if($activeBooking)
                        <div x-data="{ open: false }" 
                             x-on:open-skip-panel.window="open = !open"
                             x-show="open" 
                             x-cloak
                             class="mt-4 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 rounded-lg space-y-3">
                            <h4 class="text-title-sm font-bold text-rose-800 dark:text-rose-400">Lewati / Batalkan Antrean Aktif</h4>
                            <form action="{{ route('admin.papan-panggil.skip', $activeBooking->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="space-y-1">
                                    <label for="cancel_reason" class="block text-title-sm font-semibold text-rose-700 dark:text-rose-300 mb-2">Alasan Lewati / Batal</label>
                                    <textarea id="cancel_reason" 
                                              name="cancel_reason" 
                                              rows="2" 
                                              required 
                                              placeholder="Tulis alasan mengapa warga dilewati (min. 5 karakter)..."
                                              class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all"></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="open = false" class="px-3 h-8 text-caption font-semibold text-muted dark:text-on-dark-soft hover:bg-black/5 dark:hover:bg-white/5 rounded-md transition-all cursor-pointer">Batal</button>
                                    <button type="submit" class="px-4 h-8 bg-status-skipped hover:bg-red-700 text-white text-caption font-semibold rounded-md shadow-xs transition-all cursor-pointer">Konfirmasi Lewati</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Live-feed Table (Right, 5 cols) --}}
        <div class="lg:col-span-5 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex flex-col justify-between min-h-[420px]">
            <div class="space-y-4 w-full">
                <div class="border-b border-hairline dark:border-white/10 pb-3 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-ink dark:text-white font-display">Daftar Sisa Antrean Hari Ini</h2>
                    <span class="inline-flex px-2 py-0.5 bg-primary/10 dark:bg-accent-teal/10 rounded-md text-caption font-semibold text-primary dark:text-accent-teal font-mono tracking-wider">
                        {{ $sisaBookings->count() }} Antrean
                    </span>
                </div>

                {{-- Live feed queue-table --}}
                <div class="overflow-x-auto w-full max-h-[360px] overflow-y-auto pr-1">
                    <table class="w-full text-left text-xs border-collapse rounded-lg overflow-hidden border border-hairline dark:border-white/10">
                        <thead>
                            <tr class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/10 text-muted dark:text-on-dark-soft uppercase text-title-sm font-semibold tracking-wider">
                                <th class="px-4 py-3 font-display text-title-sm font-semibold">Kode</th>
                                <th class="px-4 py-3 font-display text-title-sm font-semibold">Warga</th>
                                <th class="px-4 py-3 font-display text-right text-title-sm font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline dark:divide-white/5 font-body">
                            @forelse($sisaBookings as $bk)
                                <tr class="border-b border-hairline-soft dark:border-white/5 hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3.5 font-mono font-bold text-primary dark:text-accent-teal text-title-sm">{{ $bk->booking_code }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-ink dark:text-white text-body-sm">{{ $bk->user ? $bk->user->name : 'Warga' }}</div>
                                        <div class="text-caption text-muted dark:text-on-dark-soft truncate max-w-[180px]">{{ $bk->purpose }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold {{ $bk->status === 'Checked-In' ? 'bg-status-called/12 text-primary dark:text-accent-teal border border-status-called/15' : 'bg-status-waiting/12 text-amber-800 dark:text-amber-400 border border-status-waiting/15' }}">
                                            <span class="w-2 h-2 rounded-full {{ $bk->status === 'Checked-In' ? 'bg-status-called animate-pulse' : 'bg-status-waiting' }}"></span>
                                            {{ $bk->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-12 text-muted dark:text-on-dark-soft">
                                        Tidak ada sisa antrean aktif hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
