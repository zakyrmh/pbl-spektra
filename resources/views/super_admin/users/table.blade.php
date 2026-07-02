<div class="overflow-x-auto md:overflow-visible">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-hairline">
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Pengguna</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Peran</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider hidden lg:table-cell font-display">Instansi / Gerai</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider hidden md:table-cell font-display">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider hidden xl:table-cell font-display">Last Login</th>
                <th class="px-5 py-3.5 text-right text-xs font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-hairline-soft">

            @forelse($users as $user)
                @php
                    // Badge class didelegasikan ke UserRole Enum via accessor
                    $isOnline = $user->isOnline();
                @endphp
                <tr class="hover:bg-surface-soft dark:hover:bg-white/5 transition-colors duration-150 group" id="user-row-{{ $user->id }}">

                    {{-- Kolom: Nama & Email --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                <div class="w-10 h-10 rounded-full bg-linear-to-br from-primary to-accent-teal flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                @if($isOnline && $user->is_active)
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-canvas dark:border-surface-dark-elevated rounded-full"></span>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-ink dark:text-white leading-tight font-display">{{ $user->name }}</p>
                                <p class="text-xs text-muted dark:text-on-dark-soft mt-0.5">{{ $user->email }}</p>
                                @if($user->nik)
                                    <p class="text-[10px] text-muted-soft dark:text-on-dark-soft/60 font-mono mt-0.5">NIK: {{ $user->nik }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Kolom: Peran — badge class dari UserRole Enum --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-sm text-[11px] font-bold {{ $user->role_badge_class }}">
                            {{ $user->role_label }}
                        </span>
                    </td>

                    {{-- Kolom: Instansi / Gerai --}}
                    <td class="px-5 py-4 hidden lg:table-cell">
                        @if($user->department)
                            <p class="text-xs font-semibold text-body dark:text-on-dark-soft">{{ $user->department->name }}</p>
                            @if($user->department && $user->department->nomor_loket)
                                <p class="text-[10px] text-muted mt-0.5">Loket {{ $user->department->nomor_loket }}</p>
                            @endif
                        @else
                            <span class="text-xs text-muted">—</span>
                        @endif
                    </td>

                    {{-- Kolom: Status --}}
                    <td class="px-5 py-4 hidden md:table-cell">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-bold bg-status-serving/10 text-green-800 dark:bg-status-serving/20 dark:text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-serving {{ $isOnline ? 'animate-pulse' : '' }}"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-bold bg-status-done/10 text-gray-800 dark:bg-status-done/20 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-done"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Kolom: Last Login --}}
                    <td class="px-5 py-4 hidden xl:table-cell">
                        @if($user->last_login_at)
                            <p class="text-xs text-body dark:text-on-dark-soft">{{ $user->last_login_at->format('d M Y') }}</p>
                            <p class="text-[10px] text-muted-soft dark:text-on-dark-soft/60 mt-0.5">{{ $user->last_login_at->format('H:i') }} WIB · {{ $user->last_login_at->diffForHumans() }}</p>
                        @else
                            <span class="text-xs text-muted-soft">Belum pernah login</span>
                        @endif
                    </td>

                    {{-- Kolom: Aksi --}}
                    <td class="px-5 py-4 text-right">
                        <div class="relative inline-block" x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-ink dark:text-white bg-surface-soft dark:bg-white/5 border border-hairline hover:bg-surface-strong dark:hover:bg-white/10 rounded-md transition-all duration-150 cursor-pointer"
                                id="action-btn-{{ $user->id }}"
                                aria-haspopup="true"
                                :aria-expanded="open"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                Aksi
                            </button>

                            {{-- Dropdown Menu --}}
                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-52 bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline shadow-xl z-30 overflow-hidden"
                                style="display:none;"
                                id="dropdown-{{ $user->id }}"
                            >
                                {{-- Edit --}}
                                <button
                                    onclick="openEditModal({{ $user->id }}, {{ json_encode(['name' => $user->name, 'nik' => $user->nik, 'email' => $user->email, 'no_telp' => $user->no_telp, 'role' => $user->role?->value, 'departments_id' => $user->departments_id, 'instansi' => $user->departments_id, 'nomor_loket' => $user->department?->nomor_loket]) }})"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-surface-soft dark:hover:bg-white/5 hover:text-primary dark:hover:text-accent-teal transition-colors text-left cursor-pointer"
                                >
                                    <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Profil & Peran
                                </button>

                                {{-- Toggle Status --}}
                                <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-surface-soft dark:hover:bg-white/5 hover:text-primary dark:hover:text-accent-teal transition-colors text-left cursor-pointer">
                                        @if($user->is_active)
                                            <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Nonaktifkan Akun
                                        @else
                                            <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Aktifkan Akun
                                        @endif
                                    </button>
                                </form>

                                {{-- Reset Password --}}
                                <form action="{{ route('users.reset-password', $user) }}" method="POST" onsubmit="return confirm('Reset password untuk {{ addslashes($user->name) }}? Password lama akan langsung diganti.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-surface-soft dark:hover:bg-white/5 hover:text-primary dark:hover:text-accent-teal transition-colors text-left cursor-pointer">
                                        <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        Reset Password
                                    </button>
                                </form>

                                {{-- Log Aktivitas (terhubung ke route nyata) --}}
                                <a href="{{ route('users.activity-log', $user) }}"
                                   class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-surface-soft dark:hover:bg-white/5 hover:text-primary dark:hover:text-accent-teal transition-colors">
                                    <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Log Aktivitas
                                </a>

                                {{-- Sesi Aktif --}}
                                @if($user->id !== auth()->id())
                                <a href="{{ route('users.sessions.index', $user) }}"
                                   class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-surface-soft dark:hover:bg-white/5 hover:text-primary dark:hover:text-accent-teal transition-colors">
                                    <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Sesi Aktif
                                </a>
                                @endif

                                <div class="border-t border-hairline my-1"></div>

                                {{-- Hapus --}}
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }} secara permanen dari sistem?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-status-skipped dark:text-red-400 hover:bg-status-skipped/10 transition-colors text-left cursor-pointer">
                                            <svg class="w-4 h-4 text-status-skipped" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus Pengguna
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-surface-soft dark:bg-white/5 rounded-lg border border-hairline flex items-center justify-center text-muted">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-ink dark:text-white font-display">Tidak ada pengguna ditemukan</p>
                            <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
                            <a href="{{ route('users.index') }}" class="text-xs text-primary hover:text-primary-hover hover:underline font-semibold font-display cursor-pointer">Reset semua filter</a>
                        </div>
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($users->hasPages())
    <div class="px-5 py-4 border-t border-hairline">
        {{ $users->links('pagination::tailwind') }}
    </div>
@endif
