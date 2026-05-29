@php
    $role = Auth::user()->role;
    $role = $role instanceof \BackedEnum ? $role->value : $role;
    $menu = [];
    if ($role === 'pengunjung') {
        $menu = [
            ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Ambil Antrean', 'url' => '#', 'icon' => 'ticket'],
            ['title' => 'Riwayat Antrean', 'url' => '#', 'icon' => 'clock'],
            ['title' => 'Panduan Layanan', 'url' => '#', 'icon' => 'book-open'],
        ];
    } elseif ($role === 'admin_fo') {
        $menu = [
            ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Monitor Antrean', 'url' => '#', 'icon' => 'tv'],
            ['title' => 'Pencetakan Tiket', 'url' => '#', 'icon' => 'printer'],
            ['title' => 'Verifikasi & Check-In', 'url' => '#', 'icon' => 'check-circle'],
            ['title' => 'Panggilan Antrean FO', 'url' => '#', 'icon' => 'megaphone'],
        ];
    } elseif ($role === 'admin_gerai') {
        $menu = [
            ['title' => 'Dashboard Gerai', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Papan Panggil', 'url' => '#', 'icon' => 'tv'],
            ['title' => 'Daftar Tunggu Gerai', 'url' => '#', 'icon' => 'users'],
            ['title' => 'Log Pelayanan', 'url' => '#', 'icon' => 'clipboard-list'],
        ];
    } elseif ($role === 'super_admin') {
        $menu = [
            ['title' => 'Dashboard Utama', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Manajemen Pengguna', 'url' => route('users.index'), 'icon' => 'users'],
            ['title' => 'Konfigurasi Gerai', 'url' => '#', 'icon' => 'settings'],
            ['title' => 'Pengaturan Sistem', 'url' => '#', 'icon' => 'sliders'],
            ['title' => 'Laporan & Analitik', 'url' => '#', 'icon' => 'chart-pie'],
        ];
    }
@endphp

<aside
    class="fixed left-0 top-0 z-20 flex flex-col w-sidebar h-screen bg-surface-dark py-6 px-4 transition-all duration-300 md:flex">
    {{-- Brand --}}
    <div class="flex items-center mb-8 px-3 shrink-0">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2.5 hover:opacity-90 transition-opacity focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal rounded-md">
            <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}" alt="Logo MPP Sawahlunto"
                class="h-8 object-contain">
            <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto"
                class="h-8 object-contain">
        </a>
    </div>

    {{-- Menu --}}
    <div class="flex-1 overflow-y-auto space-y-1.5 custom-scrollbar">
        <div class="text-caption font-medium text-on-dark-soft/50 uppercase tracking-wider mb-3 px-3">Menu Utama</div>
        @foreach ($menu as $item)
            @php
                $isActive = request()->url() === $item['url'];
            @endphp
            <a href="{{ $item['url'] }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-md text-nav font-medium transition-all duration-200 border-l-3 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal {{ $isActive ? 'bg-accent-teal/15 text-accent-teal border-accent-teal' : 'text-on-dark-soft hover:bg-white/6 hover:text-white border-transparent' }}">

                <span class="shrink-0 transition-colors {{ $isActive ? 'text-accent-teal' : 'text-on-dark-soft/70' }}">
                    @if ($item['icon'] == 'home')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    @elseif($item['icon'] == 'users')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    @elseif($item['icon'] == 'settings' || $item['icon'] == 'sliders')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @elseif($item['icon'] == 'ticket')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    @elseif($item['icon'] == 'clock')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($item['icon'] == 'book-open')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    @elseif($item['icon'] == 'tv' || $item['icon'] == 'monitor')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    @elseif($item['icon'] == 'printer')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    @elseif($item['icon'] == 'check-circle')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($item['icon'] == 'megaphone')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    @elseif($item['icon'] == 'clipboard-list')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    @elseif($item['icon'] == 'chart-pie')
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    @endif
                </span>

                {{ $item['title'] }}
            </a>
        @endforeach
    </div>
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: var(--radius-sm);
    }

    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
    }
</style>
