@extends('layouts.private')

@section('title', 'Daftar Tunggu Gerai - MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-16">
        {{-- Header Banner --}}
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-white font-display tracking-tight">Daftar Tunggu Gerai</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">
                    Mengelola antrean masuk, kehadiran warga, dan pemulihan antrean untuk Instansi <span
                        class="font-semibold text-primary dark:text-accent-teal">{{ $department->name }}</span>
                </p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 rounded-lg text-sm font-body"
                role="alert">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>{!! session('success') !!}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 text-rose-800 dark:text-rose-300 rounded-lg text-sm font-body"
                role="alert">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>{!! session('error') !!}</div>
            </div>
        @endif

        {{-- Summary of Today's Quota (Schedules) --}}
        <div class="space-y-3">
            <h2 class="text-caption font-semibold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">
                Ringkasan Kuota Pelayanan Hari Ini</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($schedules as $sched)
                    <div
                        class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 shadow-xs flex justify-between items-center">
                        <div class="min-w-0 pr-2">
                            <span
                                class="text-caption font-semibold text-primary dark:text-accent-teal uppercase truncate block"
                                title="{{ $department->name }}">
                                {{ $department->name }}
                            </span>
                            <span
                                class="text-title-md font-bold text-ink dark:text-white mt-1 block">{{ $sched->session_name }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span
                                class="text-[10px] text-muted dark:text-on-dark-soft uppercase font-bold tracking-wider block">Terpakai</span>
                            <span class="text-title-lg font-bold text-primary dark:text-accent-teal font-mono">
                                {{ $sched->quota_used }} <span
                                    class="text-xs text-muted dark:text-on-dark-soft font-normal">/
                                    {{ $sched->quota_total }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-1 md:col-span-3 text-center py-8 text-muted dark:text-on-dark-soft bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10">
                        <svg class="w-8 h-8 mx-auto mb-2 text-muted-soft" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-caption font-semibold">Tidak ada jadwal pelayanan terdaftar hari ini.</span>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Filters and Search Form --}}
        <form method="GET" action="{{ route('admin.daftar-tunggu') }}"
            class="bg-canvas dark:bg-surface-dark-elevated p-4 rounded-lg border border-hairline dark:border-white/10 flex flex-wrap items-end gap-4 font-body">
            <div class="flex-1 min-w-sidebar">
                <label for="search" class="block text-title-sm font-semibold text-ink dark:text-white mb-2">Cari
                    Antrean</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Masukkan Kode Booking atau Nama Warga..."
                        class="w-full text-body-md bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md pl-10 pr-4 h-12 focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted dark:text-on-dark-soft">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.daftar-tunggu') }}"
                    class="h-12 px-5 text-button font-semibold text-muted dark:text-on-dark-soft hover:bg-black/5 dark:hover:bg-white/5 rounded-pill border border-hairline dark:border-white/10 flex items-center transition-all cursor-pointer">
                    Reset
                </a>
                <button type="submit"
                    class="h-12 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill flex items-center gap-2 text-sm focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal transition-all cursor-pointer">
                    Filter
                </button>
            </div>
        </form>

        {{-- Main Tabs Grid --}}
        <div x-data="{ activeTab: 'waiting' }"
            class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs p-6 space-y-6">
            {{-- Navigation Tabs --}}
            <div class="border-b border-hairline dark:border-white/10 flex flex-wrap gap-4 md:gap-8">
                <button type="button" @click="activeTab = 'waiting'"
                    :class="activeTab === 'waiting' ?
                        'border-primary text-primary dark:border-accent-teal dark:text-accent-teal' :
                        'border-transparent text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white'"
                    class="pb-3.5 px-1 border-b-2 font-display font-semibold text-sm transition-all focus:outline-none cursor-pointer flex items-center gap-2">
                    Menunggu (Waiting)
                    <span
                        class="px-2 py-0.5 rounded-full text-xs font-mono bg-status-waiting/10 text-status-waiting font-bold">{{ $waitingBookings->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'serving'"
                    :class="activeTab === 'serving' ?
                        'border-primary text-primary dark:border-accent-teal dark:text-accent-teal' :
                        'border-transparent text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white'"
                    class="pb-3.5 px-1 border-b-2 font-display font-semibold text-sm transition-all focus:outline-none cursor-pointer flex items-center gap-2">
                    Sedang Dilayani (Serving)
                    <span
                        class="px-2 py-0.5 rounded-full text-xs font-mono bg-status-serving/10 text-[#065F46] dark:text-green-400 font-bold">{{ $servingBookings->count() }}</span>
                </button>
            </div>

            {{-- Tab Contents --}}
            <div>
                {{-- TAB: Waiting --}}
                <div x-show="activeTab === 'waiting'" x-cloak class="space-y-4">
                    <div class="overflow-x-auto w-full">
                        <table
                            class="w-full text-left text-xs border-collapse rounded-lg overflow-hidden border border-hairline dark:border-white/10">
                            <thead>
                                <tr
                                    class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/10 text-muted dark:text-on-dark-soft uppercase text-title-sm font-semibold tracking-wider">
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Kode</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Nama Warga</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Jenis Layanan</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Waktu Hadir</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold text-right">Durasi Tunggu
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline dark:divide-white/5 font-body">
                                @forelse($waitingBookings as $bk)
                                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-4 py-4 font-mono font-bold text-primary dark:text-accent-teal text-title-sm shrink-0">
                                            {{ $bk->booking_code }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-ink dark:text-white text-body-sm">
                                                {{ $bk->user ? $bk->user->name : 'Warga' }}</div>
                                            <div class="text-caption text-muted dark:text-on-dark-soft font-mono mt-0.5">
                                                NIK: {{ $bk->user ? $bk->user->nik : '-' }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-body-sm text-ink dark:text-white font-medium">
                                                {{ $bk->purpose }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-body-sm text-ink dark:text-white font-semibold">
                                                {{ $bk->checked_in_at ? $bk->checked_in_at->format('H:i') . ' WIB' : '-' }}
                                            </div>
                                            <div class="text-[10px] text-muted dark:text-on-dark-soft mt-0.5">Sudah
                                                Verifikasi Dokumen</div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-waiting/12 text-status-waiting border border-status-waiting/15">
                                                <span class="w-2 h-2 rounded-full bg-status-waiting animate-pulse"></span>
                                                {{ $bk->checked_in_at ? $bk->checked_in_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 1]) : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="text-center py-12 text-muted dark:text-on-dark-soft font-medium font-body">
                                            Tidak ada warga yang sedang menunggu (waiting) hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB: Serving --}}
                <div x-show="activeTab === 'serving'" x-cloak class="space-y-4">
                    <div class="overflow-x-auto w-full">
                        <table
                            class="w-full text-left text-xs border-collapse rounded-lg overflow-hidden border border-hairline dark:border-white/10">
                            <thead>
                                <tr
                                    class="bg-surface-soft dark:bg-white/5 border-b border-hairline dark:border-white/10 text-muted dark:text-on-dark-soft uppercase text-title-sm font-semibold tracking-wider">
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Kode</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Nama Warga</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Jenis Layanan</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold">Waktu Dipanggil</th>
                                    <th class="px-4 py-3 font-display text-title-sm font-semibold text-right">Durasi Layanan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline dark:divide-white/5 font-body">
                                @forelse($servingBookings as $bk)
                                    <tr class="hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors">
                                        <td
                                            class="px-4 py-4 font-mono font-bold text-primary dark:text-accent-teal text-title-sm shrink-0">
                                            {{ $bk->booking_code }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-ink dark:text-white text-body-sm">
                                                {{ $bk->user ? $bk->user->name : 'Warga' }}</div>
                                            <div class="text-caption text-muted dark:text-on-dark-soft font-mono mt-0.5">
                                                NIK: {{ $bk->user ? $bk->user->nik : '-' }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-body-sm text-ink dark:text-white font-medium">
                                                {{ $bk->purpose }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-body-sm text-ink dark:text-white font-semibold">
                                                {{ $bk->called_at ? $bk->called_at->format('H:i') . ' WIB' : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-caption font-semibold bg-status-serving/12 text-[#065F46] dark:text-green-400 border border-status-serving/15">
                                                <span class="w-2 h-2 rounded-full bg-status-serving animate-pulse"></span>
                                                {{ $bk->called_at ? $bk->called_at->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 1]) : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="text-center py-12 text-muted dark:text-on-dark-soft font-medium font-body">
                                            Tidak ada warga yang sedang dilayani (serving) saat ini.
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
