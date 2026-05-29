@extends('layouts.private')

@section('title', 'Log Aktivitas: ' . $user->name . ' - MPP Sawahlunto')

@section('content')
<div class="space-y-6 pb-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 mb-1">
                <a href="{{ route('users.index') }}" class="hover:text-blue-600 transition-colors">Manajemen Pengguna</a>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 dark:text-gray-300">Log Aktivitas</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Log Aktivitas</h1>
            <div class="flex items-center gap-2 mt-1">
                <div class="w-7 h-7 rounded-full bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                    &middot; {{ $user->email }}
                    &middot; <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold {{ $user->role_badge_class }}">{{ $user->role_label }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.sessions.index', $user) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-violet-700 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 dark:hover:bg-violet-900/30 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Lihat Sesi Aktif
            </a>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Timeline Log --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-sm overflow-hidden">

        @if($logs->isEmpty())
            <div class="py-20 flex flex-col items-center gap-3 text-center px-6">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada log aktivitas</p>
                <p class="text-xs text-gray-400">Aktivitas akan muncul saat user melakukan aksi di sistem.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @foreach($logs as $log)
                    @php
                        $eventConfig = match($log->event) {
                            'login'          => ['icon' => 'login',    'color' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400'],
                            'logout'         => ['icon' => 'logout',   'color' => 'text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400'],
                            'user_created'   => ['icon' => 'plus',     'color' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400'],
                            'user_updated'   => ['icon' => 'edit',     'color' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400'],
                            'status_toggled' => ['icon' => 'toggle',   'color' => 'text-violet-600 bg-violet-50 dark:bg-violet-900/20 dark:text-violet-400'],
                            'password_reset' => ['icon' => 'key',      'color' => 'text-orange-600 bg-orange-50 dark:bg-orange-900/20 dark:text-orange-400'],
                            'user_deleted'   => ['icon' => 'delete',   'color' => 'text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400'],
                            'session_revoked'=> ['icon' => 'session',  'color' => 'text-pink-600 bg-pink-50 dark:bg-pink-900/20 dark:text-pink-400'],
                            default          => ['icon' => 'default',  'color' => 'text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400'],
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/20 transition-colors">

                        {{-- Event Icon --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $eventConfig['color'] }}">
                            @if($eventConfig['icon'] === 'login')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            @elseif($eventConfig['icon'] === 'logout')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            @elseif($eventConfig['icon'] === 'plus')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            @elseif($eventConfig['icon'] === 'edit')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            @elseif($eventConfig['icon'] === 'toggle')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                            @elseif($eventConfig['icon'] === 'key')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            @elseif($eventConfig['icon'] === 'delete')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            @elseif($eventConfig['icon'] === 'session')
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>

                        {{-- Event Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->description }}</p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                        @if($log->causer)
                                            <span class="flex items-center gap-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                Oleh: <strong class="text-gray-700 dark:text-gray-300">{{ $log->causer->name }}</strong>
                                            </span>
                                        @endif
                                        @if($log->ip_address)
                                            <span class="flex items-center gap-1 text-[11px] text-gray-400 font-mono">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                                                {{ $log->ip_address }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('d M Y, H:i') }} WIB</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            {{-- Properties Diff (before/after) --}}
                            @if($log->properties && isset($log->properties['before'], $log->properties['after']))
                                <details class="mt-2 group">
                                    <summary class="inline-flex items-center gap-1 text-[11px] text-blue-600 dark:text-blue-400 hover:underline cursor-pointer font-semibold list-none">
                                        <svg class="w-3 h-3 group-open:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        Lihat perubahan data
                                    </summary>
                                    <div class="mt-2 overflow-x-auto">
                                        <table class="w-full text-[11px] border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                                    <th class="px-3 py-1.5 text-left text-gray-500 font-bold">Field</th>
                                                    <th class="px-3 py-1.5 text-left text-red-500 font-bold">Sebelum</th>
                                                    <th class="px-3 py-1.5 text-left text-emerald-600 font-bold">Sesudah</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                @foreach($log->properties['after'] as $field => $newVal)
                                                    @php $oldVal = $log->properties['before'][$field] ?? null; @endphp
                                                    @if((string)$oldVal !== (string)$newVal)
                                                        <tr>
                                                            <td class="px-3 py-1.5 font-mono text-gray-600 dark:text-gray-400">{{ $field }}</td>
                                                            <td class="px-3 py-1.5 text-red-600 dark:text-red-400 font-mono">{{ $oldVal ?? '—' }}</td>
                                                            <td class="px-3 py-1.5 text-emerald-700 dark:text-emerald-400 font-mono">{{ $newVal ?? '—' }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </details>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $logs->links('pagination::tailwind') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
