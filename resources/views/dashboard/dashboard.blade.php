@extends('layouts.app')

@section('title', 'Dashboard - MPP Kota Sawahlunto')

@section('base_content')
    @php
        $role = Auth::user()->role ?? 'pengunjung';
        if ($role === 'warga') $role = 'pengunjung';
    @endphp

    @if ($role === 'pengunjung')
        {{-- Pengunjung Dashboard (Mobile + Desktop Layout) --}}
        <div class="flex h-screen overflow-hidden w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <!-- Sidebar (Desktop) -->
            <aside class="hidden lg:flex flex-col w-72 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700/50 z-20">
                <!-- Logo -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/30 text-white font-bold text-xl">
                        M
                    </div>
                    <div>
                        <h2 class="font-bold text-lg leading-tight text-gray-900 dark:text-white">MPP Digital</h2>
                        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Kota Sawahlunto</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">Menu Utama</p>
                    <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M11.47 3.841a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.061l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 101.061 1.06l8.69-8.689z" />
                            <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                        Tiket Saya
                        <span class="ml-auto bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 py-0.5 px-2 rounded-md text-[10px] font-bold">1</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Layanan MPP
                    </a>

                    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Akun</p>
                    <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profil Saya
                    </a>
                </nav>

                <!-- Logout Desktop -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700/50">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-semibold hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 flex flex-col h-full overflow-y-auto bg-gray-50/50 dark:bg-gray-900/50 scroll-smooth">
                <!-- Topbar Desktop -->
                <header class="hidden lg:flex items-center justify-between p-6 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-10 border-b border-gray-100 dark:border-gray-800">
                    <div class="relative w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" class="block w-full p-3 pl-12 text-sm text-gray-900 border border-gray-200 rounded-2xl bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all outline-none" placeholder="Cari layanan, instansi, atau tiket..." required>
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="relative p-2.5 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 dark:text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </button>

                        <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-2"></div>

                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Civic Concierge' }}</p>
                                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Pengunjung</p>
                            </div>
                            <div class="w-11 h-11 rounded-full overflow-hidden bg-gray-200 border-2 border-white dark:border-gray-700 shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Civic Concierge') }}&background=random" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </header>

                <div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto w-full pb-28 lg:pb-8">
                    <!-- Mobile Header (Hidden on Desktop) -->
                    <div class="lg:hidden flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 border-2 border-white dark:border-gray-800 shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Civic Concierge') }}&background=random" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider">Selamat Pagi</p>
                                <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Civic Concierge' }}</h1>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 dark:text-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="p-2 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 shadow-sm border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Main Content Grid for Desktop -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Active Queue Card (Spans 2 cols on Desktop) -->
                        <div class="lg:col-span-2 bg-linear-to-br from-blue-600 to-blue-700 rounded-3xl p-6 md:p-8 text-white shadow-[0_10px_30px_rgba(37,99,235,0.3)] relative overflow-hidden group h-full flex flex-col justify-between">
                            <div class="flex justify-between items-start mb-6 relative z-10">
                                <div>
                                    <span class="bg-white/20 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider backdrop-blur-md border border-white/20 uppercase">Antrean Aktif</span>
                                    <h2 class="mt-4 text-xl md:text-2xl font-medium leading-snug max-w-62.5 md:max-w-sm">Layanan Paspor - Kanim Kelas I</h2>
                                </div>
                                <span class="bg-green-400/90 text-green-950 px-4 py-2 rounded-xl text-xs font-bold leading-tight text-center backdrop-blur-md shadow-sm">Sesuai<br>Jadwal</span>
                            </div>

                            <div class="flex justify-between items-end relative z-10 mt-auto">
                                <div>
                                    <p class="text-[11px] text-blue-200 uppercase tracking-widest mb-1 font-semibold">Kode Booking</p>
                                    <p class="text-5xl md:text-6xl font-extrabold tracking-tight">B-2904</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[11px] text-blue-200 uppercase tracking-widest mb-1 font-semibold">Estimasi Tunggu</p>
                                    <p class="text-3xl md:text-4xl font-bold flex items-center justify-end gap-3">
                                        <span class="w-3.5 h-3.5 bg-green-400 rounded-full animate-pulse shadow-[0_0_12px_rgba(74,222,128,0.8)]"></span>
                                        12 Menit
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between items-center relative z-10 border-t border-white/10 pt-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex -space-x-3">
                                        <img class="w-10 h-10 rounded-full border-2 border-blue-600 shadow-sm" src="https://ui-avatars.com/api/?name=Agus&background=random" alt="Avatar 1">
                                        <div class="w-10 h-10 rounded-full border-2 border-blue-600 bg-white text-blue-800 text-xs flex items-center justify-center font-bold shadow-sm z-10">+2</div>
                                    </div>
                                    <p class="text-xs text-blue-100 font-medium hidden sm:block">Sedang dalam antrean</p>
                                </div>
                                <button class="bg-white text-blue-700 px-6 py-3 rounded-full text-sm font-bold hover:bg-blue-50 active:scale-95 transition-all shadow-lg hover:shadow-xl">
                                    Lihat Detail Tiket
                                </button>
                            </div>

                            <!-- Decorative background shapes -->
                            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                            <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                        </div>

                        <!-- Stats / Promo Info (Desktop Only) -->
                        <div class="hidden lg:flex flex-col gap-6">
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 flex-1 flex flex-col justify-center relative overflow-hidden">
                                <div class="absolute right-0 bottom-0 opacity-5 dark:opacity-10 pointer-events-none w-32 h-32 translate-x-8 translate-y-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Jam Operasional</h3>
                                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">Buka</p>
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium mt-1">Tutup pada 15:00 WIB</p>
                            </div>
                            <div class="bg-linear-to-br from-orange-500 to-red-500 rounded-3xl p-6 shadow-sm text-white flex-1 flex flex-col justify-center relative overflow-hidden group cursor-pointer">
                                <div class="absolute right-0 top-0 opacity-20 pointer-events-none group-hover:scale-110 transition-transform duration-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-24 h-24 -mt-4 -mr-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.866 8.21 8.21 0 003 2.48z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold mb-1 z-10">Beri Rating</h3>
                                <p class="text-sm text-orange-100 z-10 mb-4 line-clamp-2">Bagaimana pengalaman Anda hari ini?</p>
                                <div class="flex gap-1 z-10">
                                    @for($i=1; $i<=5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-yellow-300 hover:text-yellow-100 transition-colors">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                    </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Akses Cepat Grid -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Akses Cepat</h3>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                            <!-- Card 1 -->
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 md:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 cursor-pointer hover:shadow-lg hover:-translate-y-1 hover:border-blue-200 dark:hover:border-blue-900 transition-all group">
                                <div class="w-12 h-12 md:w-14 md:h-14 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mb-4 md:mb-5 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 md:w-7 md:h-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm md:text-base mb-1">Booking Baru</h4>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Daftar layanan MPP</p>
                            </div>
                            <!-- Card 2 -->
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 md:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 cursor-pointer hover:shadow-lg hover:-translate-y-1 hover:border-orange-200 dark:hover:border-orange-900 transition-all group">
                                <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-50 dark:bg-orange-900/30 text-orange-500 rounded-2xl flex items-center justify-center mb-4 md:mb-5 group-hover:scale-110 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 md:w-7 md:h-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm md:text-base mb-1">Jadwal Saya</h4>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Lihat agenda reservasi</p>
                            </div>
                            <!-- Card 3 -->
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 md:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 cursor-pointer hover:shadow-lg hover:-translate-y-1 hover:border-green-200 dark:hover:border-green-900 transition-all group">
                                <div class="w-12 h-12 md:w-14 md:h-14 bg-green-50 dark:bg-green-900/30 text-green-600 rounded-2xl flex items-center justify-center mb-4 md:mb-5 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 md:w-7 md:h-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm md:text-base mb-1">Layanan MPP</h4>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Katalog instansi & jasa</p>
                            </div>
                            <!-- Card 4 -->
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 md:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 cursor-pointer hover:shadow-lg hover:-translate-y-1 hover:border-purple-200 dark:hover:border-purple-900 transition-all group">
                                <div class="w-12 h-12 md:w-14 md:h-14 bg-purple-50 dark:bg-purple-900/30 text-purple-600 rounded-2xl flex items-center justify-center mb-4 md:mb-5 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 md:w-7 md:h-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm md:text-base mb-1">Lokasi</h4>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Petunjuk jalan & peta</p>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Reservasi -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Reservasi</h3>
                            <a href="#" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors flex items-center gap-1">
                                Lihat Semua
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>

                        <!-- Mobile Cards List -->
                        <div class="lg:hidden space-y-3">
                            <!-- Item 1 -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 flex items-center justify-between shadow-sm border border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Pembaruan KTP-el</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">12 Jan 2024 &bull; Disdukcapil</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Selesai</span>
                                    <p class="text-xs text-gray-400 mt-1.5 font-medium">A-102</p>
                                </div>
                            </div>
                            <!-- Item 2 -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 flex items-center justify-between shadow-sm border border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Pajak Kendaraan</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">05 Jan 2024 &bull; Samsat</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Batal</span>
                                    <p class="text-xs text-gray-400 mt-1.5 font-medium">S-044</p>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Table List -->
                        <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-gray-700/50">
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px]">Layanan</th>
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px]">Instansi</th>
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px]">Tanggal</th>
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px]">Kode Booking</th>
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px] text-right">Status</th>
                                        <th class="pb-4 font-bold uppercase tracking-wider text-[11px] text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="py-4 font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:bg-white dark:group-hover:bg-gray-600 transition-colors shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                </svg>
                                            </div>
                                            Pembaruan KTP-el
                                        </td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300 font-medium">Disdukcapil</td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300">12 Jan 2024, 09:30</td>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">A-102</td>
                                        <td class="py-4 text-right">
                                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Selesai
                                            </span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="py-4 font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:bg-white dark:group-hover:bg-gray-600 transition-colors shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                                </svg>
                                            </div>
                                            Pajak Kendaraan
                                        </td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300 font-medium">Samsat</td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300">05 Jan 2024, 11:15</td>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">S-044</td>
                                        <td class="py-4 text-right">
                                            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Batal
                                            </span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="py-4 font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:bg-white dark:group-hover:bg-gray-600 transition-colors shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            Legalisir Ijazah
                                        </td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300 font-medium">Dinas Pendidikan</td>
                                        <td class="py-4 text-gray-600 dark:text-gray-300">28 Des 2023, 14:00</td>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">E-009</td>
                                        <td class="py-4 text-right">
                                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Selesai
                                            </span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Bottom Navigation Mobile -->
        <nav class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex justify-around items-center py-2 px-2 pb-safe shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-50">
            <a href="#" class="flex flex-col items-center gap-1 min-w-16 py-1">
                <div class="bg-blue-50 dark:bg-blue-900/30 w-14 h-8 rounded-full flex items-center justify-center transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400">
                        <path d="M11.47 3.841a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.061l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 101.061 1.06l8.69-8.689z" />
                        <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400">BERANDA</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 min-w-16 py-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <div class="w-14 h-8 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium">TIKET</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 min-w-16 py-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <div class="w-14 h-8 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium">INFO</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 min-w-16 py-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <div class="w-14 h-8 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium">PROFIL</span>
            </a>
        </nav>

    @elseif ($role === 'admin_fo')
        {{-- Admin FO Dashboard --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin Front Office</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem Antrean Digital MPP</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Modul Manajemen Antrean</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selamat datang. Modul pemanggilan dan manajemen antrean Front Office sedang dalam tahap pengembangan.</p>
                </div>
            </div>
        </div>

    @elseif ($role === 'admin_gerai')
        {{-- Admin Gerai Dashboard --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin Gerai</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem Antrean Digital MPP</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.999 2.999 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.999 2.999 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Modul Layanan Gerai</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selamat datang. Modul layanan khusus untuk instansi gerai Anda sedang dalam tahap pengembangan.</p>
                </div>
            </div>
        </div>

    @elseif ($role === 'super_admin')
        {{-- Super Admin Dashboard --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Super Admin</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem Antrean Digital MPP</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pusat Kendali Sistem</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selamat datang, Super Admin. Anda memiliki akses penuh untuk mengatur master data, instansi, dan manajemen user.</p>
                </div>
            </div>
        </div>

    @else
        {{-- Unknown Role --}}
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
            <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8 flex flex-col items-center justify-center min-h-[60vh]">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Role Tidak Dikenali</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">Silakan hubungi administrator untuk mengatur hak akses Anda.</p>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-medium hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        Kembali ke Login
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection

