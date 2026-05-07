@extends('layouts.app')

@section('base_content')
    <div class="flex flex-col lg:flex-row lg:h-screen lg:overflow-hidden">

        {{-- Kolom Kiri — Hero (Desktop only) --}}
        <div class="hidden lg:flex lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative flex-col justify-between p-12 overflow-hidden shrink-0"
            style="background: linear-gradient(135deg, #0f2d6b 0%, #1a4fa0 50%, #1e63c9 100%);">

            {{-- Background image overlay --}}
            <div class="absolute inset-0 bg-cover bg-center opacity-20"
                style="background-image: url('https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1200');">
            </div>
            <div class="absolute inset-0"
                style="background: linear-gradient(135deg, rgba(15,45,107,0.85) 0%, rgba(30,99,201,0.75) 100%);">
            </div>

            {{-- Konten Hero — berbeda tiap halaman --}}
            <div class="relative z-10 flex flex-col h-full justify-between">

                {{-- Logo --}}
                <div class="flex items-center gap-6 p-4">
                    {{-- Container Logo - Jauh Lebih Terang --}}
                    <div
                        class="flex items-center gap-3 bg-white backdrop-blur-md border border-white/40 rounded-3xl p-3 shadow-2xl">
                        {{-- Logo Kota - Dibuat Lebih Besar --}}
                        <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto"
                            class="w-10 h-10 object-contain">

                        {{-- Divider --}}
                        <div class="w-[2px] h-10 bg-black/10"></div>

                        {{-- Logo MPP - Dibuat Lebih Besar --}}
                        <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}"
                            alt="Logo Mal Pelayanan Publik Kota Sawahlunto" class="w-10 h-10 object-contain">
                    </div>

                    {{-- Teks --}}
                    <span class="text-white font-bold text-3xl tracking-tight drop-shadow-xl">
                        MPP Sawahlunto
                    </span>
                </div>

                {{-- Hero Content per halaman --}}
                @yield('auth_hero')

                {{-- Stats + Status --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-white font-extrabold text-2xl">30+</p>
                            <p class="text-blue-200 text-xs uppercase tracking-widest">Layanan Terpadu</p>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div>
                            <p class="text-white font-extrabold text-2xl">24/7</p>
                            <p class="text-blue-200 text-xs uppercase tracking-widest">Sistem Antrean Digital</p>
                        </div>
                    </div>
                    <div
                        class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-semibold px-4 py-2 rounded-full">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Sistem Berjalan Normal
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan — Form --}}
        <div
            class="flex-1 flex flex-col justify-center lg:justify-start items-center px-6 py-10 lg:py-12 bg-white lg:overflow-y-auto lg:h-screen">

            {{-- Mobile: Logo (hanya tampil di mobile) --}}
            <div class="flex lg:hidden flex-col items-center mb-6 text-center">
                <div class="flex items-center gap-3 p-3">
                    {{-- Logo Kota - Dibuat Lebih Besar --}}
                    <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto"
                        class="w-10 h-10 object-contain">

                    {{-- Divider --}}
                    <div class="w-[2px] h-10 bg-black/10"></div>

                    {{-- Logo MPP - Dibuat Lebih Besar --}}
                    <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}"
                        alt="Logo Mal Pelayanan Publik Kota Sawahlunto" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-xl font-extrabold text-blue-800">MPP Sawahlunto</h1>
            </div>

            {{-- Form Content --}}
            <div class="w-full max-w-md">
                @yield('auth_content')
            </div>

            {{-- Footer --}}
            <div class="mt-8 text-center space-y-3">
                <div class="flex items-center justify-center gap-4 text-gray-400">
                    <span class="inline-flex items-center gap-1.5 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        Data Aman
                    </span>
                    <span class="w-px h-3 bg-gray-200"></span>
                    <span class="inline-flex items-center gap-1.5 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                        </svg>
                        SSL Terenkripsi
                    </span>
                </div>
                <p class="text-[11px] text-gray-400">
                    <a href="#" class="hover:text-gray-600 transition-colors">Pusat Bantuan</a>
                    <span class="mx-1.5">·</span>
                    <a href="#" class="hover:text-gray-600 transition-colors">Kebijakan Privasi</a>
                    <span class="mx-1.5">·</span>
                    <a href="#" class="hover:text-gray-600 transition-colors">Panduan Pengguna</a>
                </p>
                <p class="text-[11px] text-gray-300">&copy; {{ date('Y') }} MPP Sawahlunto. Melayani dengan Hati.</p>
            </div>
        </div>

    </div>
@endsection
