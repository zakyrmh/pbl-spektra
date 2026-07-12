@extends('layouts.private')

@section('title', 'Sesi Aktif: ' . $user->name . ' - MPP Sawahlunto')

@section('content')
<div class="space-y-6 pb-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 mb-1">
                <a href="{{ route('users.index') }}" class="hover:text-blue-600 transition-colors">Manajemen Pengguna</a>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 dark:text-gray-300">Sesi Aktif</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Manajemen Sesi</h1>
            <div class="flex items-center gap-2 mt-1">
                <div class="w-7 h-7 rounded-full bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                    &middot; {{ $user->email }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($sessions->isNotEmpty())
                <form action="{{ route('users.sessions.destroy-all', $user) }}" method="POST"
                      onsubmit="return confirm('Hentikan SEMUA sesi aktif {{ addslashes($user->name) }}? User akan otomatis ter-logout dari semua perangkat.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 active:scale-95 rounded-xl shadow-sm hover:shadow-red-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Hentikan Semua Sesi ({{ $sessions->count() }})
                    </button>
                </form>
            @endif
            <a href="{{ route('users.activity-log', $user) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Log Aktivitas
            </a>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>



    {{-- Info banner --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl px-5 py-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Peringatan: Aksi Destruktif</p>
                <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                    Menghentikan sesi akan memaksa pengguna logout dari perangkat tersebut secara instan.
                    Gunakan fitur ini hanya jika ada indikasi akun diakses tanpa izin atau untuk keperluan keamanan.
                </p>
            </div>
        </div>
    </div>

    {{-- Session Cards --}}
    @if($sessions->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-sm py-20 flex flex-col items-center gap-3 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-400">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada sesi aktif</p>
            <p class="text-xs text-gray-400">Pengguna ini tidak sedang login dari perangkat mana pun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($sessions as $session)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $session->is_recent ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-gray-200 dark:border-gray-700/50' }} shadow-sm p-5 relative overflow-hidden">

                    {{-- Online indicator --}}
                    @if($session->is_recent)
                        <div class="absolute top-4 right-4 flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Online
                        </div>
                    @else
                        <div class="absolute top-4 right-4 text-[10px] font-bold text-gray-400">Offline</div>
                    @endif

                    {{-- Device info --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center shrink-0 text-gray-500 dark:text-gray-400">
                            @if($session->browser_info['device'] === 'Mobile')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @elseif($session->browser_info['device'] === 'Tablet')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $session->browser_info['browser'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->browser_info['os'] }} &middot; {{ $session->browser_info['device'] }}</p>
                        </div>
                    </div>

                    {{-- Session details --}}
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 font-semibold">IP Address</span>
                            <span class="font-mono text-gray-700 dark:text-gray-300">{{ $session->ip_address ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 font-semibold">Aktivitas Terakhir</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $session->last_activity_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 font-semibold">Waktu</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $session->last_activity_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Revoke button --}}
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('users.sessions.destroy', [$user, $session->id]) }}" method="POST"
                              onsubmit="return confirm('Hentikan sesi ini? User akan dipaksa logout dari perangkat ini.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 active:scale-95 rounded-xl transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Hentikan Sesi Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
