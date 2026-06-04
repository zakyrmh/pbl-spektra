@extends('layouts.app')

@section('base_content')
    <div class="flex flex-col lg:flex-row lg:h-screen lg:overflow-hidden">

        {{-- Kolom Kiri — Hero (Desktop only) --}}
        <div class="hidden lg:flex lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative flex-col justify-between p-12 overflow-hidden shrink-0"
            style="background: linear-gradient(135deg, var(--color-surface-dark) 0%, var(--color-primary) 50%, var(--color-primary-hover) 100%);">

            {{-- Background image overlay --}}
            <div class="absolute inset-0 bg-cover bg-center opacity-15"
                style="background-image: url('https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1200');">
            </div>
            <div class="absolute inset-0"
                style="background: linear-gradient(135deg, rgba(16,24,38,0.85) 0%, rgba(27,79,168,0.75) 100%);">
            </div>

            {{-- Konten Hero — berbeda tiap halaman --}}
            <div class="relative z-10 flex flex-col h-full justify-between">

                {{-- Logo --}}
                <div class="flex items-center gap-6 p-4">
                    {{-- Container Logo - Clean and Flat, no glassmorphism --}}
                    <div
                        class="flex items-center gap-3 bg-canvas border border-hairline rounded-lg p-2.5 shadow-sm">
                        {{-- Logo Kota --}}
                        <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto"
                            class="w-10 h-10 object-contain">

                        {{-- Divider --}}
                        <div class="w-[2px] h-10 bg-hairline"></div>

                        {{-- Logo MPP --}}
                        <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}"
                            alt="Logo Mal Pelayanan Publik Kota Sawahlunto" class="w-10 h-10 object-contain">
                    </div>

                    {{-- Teks --}}
                    <span class="text-white font-bold text-3xl tracking-tight drop-shadow-sm font-display">
                        MPP Sawahlunto
                    </span>
                </div>

                {{-- Hero Content per halaman --}}
                @yield('auth_hero')

                {{-- Stats + Status --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-white font-extrabold text-2xl font-display">30+</p>
                            <p class="text-on-dark-soft text-caption uppercase tracking-wider font-body">Layanan Terpadu</p>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div>
                            <p class="text-white font-extrabold text-2xl font-display">24/7</p>
                            <p class="text-on-dark-soft text-caption uppercase tracking-wider font-body">Sistem Antrean Digital</p>
                        </div>
                    </div>
                    <div
                        class="inline-flex items-center gap-2 bg-white/5 border border-white/10 text-white text-xs font-semibold px-4 py-2 rounded-pill font-body">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Sistem Berjalan Normal
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan — Form --}}
        <div
            class="flex-1 flex flex-col items-center px-6 py-10 lg:py-12 bg-canvas lg:overflow-y-auto lg:h-screen">

            {{-- Wrapper to center vertically if content fits, starts from top if overflows --}}
            <div class="my-auto w-full max-w-md flex flex-col">
                {{-- Mobile: Logo (hanya tampil di mobile) --}}
                <div class="flex lg:hidden flex-col items-center mb-6 text-center">
                    <div class="flex items-center gap-3 bg-canvas border border-hairline rounded-lg p-2.5 shadow-sm mb-3">
                        {{-- Logo Kota --}}
                        <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto"
                            class="w-10 h-10 object-contain">

                        {{-- Divider --}}
                        <div class="w-[2px] h-10 bg-hairline"></div>

                        {{-- Logo MPP --}}
                        <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}"
                            alt="Logo Mal Pelayanan Publik Kota Sawahlunto" class="w-10 h-10 object-contain">
                    </div>
                    <h1 class="text-title-lg font-bold text-primary font-display">MPP Sawahlunto</h1>
                </div>

                {{-- Form Content --}}
                <div class="w-full">
                    @yield('auth_content')
                </div>

                {{-- Footer --}}
                <div class="mt-8 text-center space-y-3">
                    <div class="flex items-center justify-center gap-4 text-muted">
                        <span class="inline-flex items-center gap-1.5 text-caption font-body">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Data Aman
                        </span>
                        <span class="w-px h-3 bg-hairline"></span>
                        <span class="inline-flex items-center gap-1.5 text-caption font-body">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                            </svg>
                            SSL Terenkripsi
                        </span>
                    </div>
                    <p class="text-caption text-muted font-body">
                        <a href="#" class="hover:text-ink transition-colors">Pusat Bantuan</a>
                        <span class="mx-1.5">·</span>
                        <a href="#" class="hover:text-ink transition-colors">Kebijakan Privasi</a>
                        <span class="mx-1.5">·</span>
                        <a href="#" class="hover:text-ink transition-colors">Panduan Pengguna</a>
                    </p>
                    <p class="text-caption text-muted-soft font-body">&copy; {{ date('Y') }} MPP Sawahlunto. Melayani dengan Hati.</p>
                </div>
            </div>
        </div>

    </div>
@endsection
