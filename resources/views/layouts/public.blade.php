@extends('layouts.app')

@section('base_content')
<div class="min-h-screen bg-canvas text-body flex flex-col" x-data="{ mobileMenuOpen: false }">
    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-canvas/95 backdrop-blur-md border-b border-hairline h-16 flex items-center">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Left: Logos -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}" alt="Logo MPP Sawahlunto" class="h-9 w-auto object-contain">
                <div class="h-6 w-px bg-hairline"></div>
                <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto" class="h-9 w-auto object-contain">
                <span class="hidden lg:inline-block font-display font-bold text-ink text-body-md leading-tight">
                    MPP Sawahlunto
                </span>
            </a>

            <!-- Middle: Nav Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="#home" class="font-display font-semibold text-nav text-ink hover:text-primary transition-colors">Beranda</a>
                <a href="#live-monitor" class="font-display font-semibold text-nav text-muted hover:text-primary transition-colors">Live Monitor</a>
                <a href="#alur" class="font-display font-semibold text-nav text-muted hover:text-primary transition-colors">Alur</a>
                <a href="#fitur" class="font-display font-semibold text-nav text-muted hover:text-primary transition-colors">Fitur</a>
                <a href="#kontak" class="font-display font-semibold text-nav text-muted hover:text-primary transition-colors">Kontak</a>
            </nav>

            <!-- Right: CTA Button / Auth state -->
            <div class="hidden md:flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-canvas hover:bg-surface-soft text-ink border border-hairline font-display font-semibold text-button px-5 py-2.5 h-11 rounded-pill transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-5 py-2.5 h-11 rounded-pill transition-colors shadow-sm">
                        Daftar
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-canvas hover:bg-surface-soft text-ink border border-hairline font-display font-semibold text-button px-5 py-2.5 h-11 rounded-pill transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('booking.create') }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-5 py-2.5 h-11 rounded-pill transition-colors shadow-sm">
                        Ambil Antrean Online
                    </a>
                @endauth
            </div>

            <!-- Hamburger Button (Mobile) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-md hover:bg-surface-soft text-ink transition-colors" aria-label="Toggle Menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu (Drawer) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="absolute top-16 left-0 right-0 bg-canvas border-b border-hairline shadow-lg md:hidden z-40"
             style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-3">
                <a href="#home" @click="mobileMenuOpen = false" class="block font-display font-medium text-body-md text-ink hover:text-primary py-2">Beranda</a>
                <a href="#live-monitor" @click="mobileMenuOpen = false" class="block font-display font-medium text-body-md text-muted hover:text-primary py-2">Live Monitor</a>
                <a href="#alur" @click="mobileMenuOpen = false" class="block font-display font-medium text-body-md text-muted hover:text-primary py-2">Alur</a>
                <a href="#fitur" @click="mobileMenuOpen = false" class="block font-display font-medium text-body-md text-muted hover:text-primary py-2">Fitur</a>
                <a href="#kontak" @click="mobileMenuOpen = false" class="block font-display font-medium text-body-md text-muted hover:text-primary py-2">Kontak</a>
                <div class="pt-4 border-t border-hairline-soft flex flex-col gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center bg-canvas hover:bg-surface-soft text-ink border border-hairline font-display font-semibold text-button px-6 py-3 h-11 rounded-pill transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-6 py-3 h-11 rounded-pill transition-colors shadow-sm">
                            Daftar
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center bg-canvas hover:bg-surface-soft text-ink border border-hairline font-display font-semibold text-button px-6 py-3 h-11 rounded-pill transition-colors">
                            Dashboard
                        </a>
                        <a href="{{ route('booking.create') }}" class="w-full inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-on-primary font-display font-semibold text-button px-6 py-3 h-11 rounded-pill transition-colors shadow-sm">
                            Ambil Antrean Online
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-surface-dark text-on-dark-soft pt-16 pb-10 border-t border-surface-dark-elevated">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-12">
                <!-- Column 1: Info MPP -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp') }}" alt="Logo MPP Sawahlunto" class="h-10 w-auto object-contain brightness-0 invert">
                        <div class="h-6 w-px bg-surface-dark-elevated"></div>
                        <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Logo Kota Sawahlunto" class="h-10 w-auto object-contain brightness-0 invert">
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-on-dark text-body-md">Mal Pelayanan Publik</h4>
                        <p class="font-display font-semibold text-on-dark-soft text-body-sm">Kota Sawahlunto</p>
                    </div>
                    <p class="text-body-sm italic text-on-dark-soft/70">
                        "Sawahlunto, Kota Wisata Tambang yang Berbudaya"
                    </p>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h4 class="font-display font-bold text-on-dark text-title-sm mb-4">Navigasi</h4>
                    <ul class="space-y-3 text-body-sm">
                        <li><a href="#home" class="hover:text-on-dark transition-colors">Beranda</a></li>
                        <li><a href="#live-monitor" class="hover:text-on-dark transition-colors">Live Monitor</a></li>
                        <li><a href="#alur" class="hover:text-on-dark transition-colors">Alur Pelayanan</a></li>
                        <li><a href="#fitur" class="hover:text-on-dark transition-colors">Fitur Aplikasi</a></li>
                        <li><a href="#kontak" class="hover:text-on-dark transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Column 3: Jam Operasional -->
                <div>
                    <h4 class="font-display font-bold text-on-dark text-title-sm mb-4">Jam Operasional</h4>
                    <ul class="space-y-3 text-body-sm text-on-dark-soft/95">
                        <li class="flex justify-between border-b border-surface-dark-elevated pb-2">
                            <span>Senin - Kamis</span>
                            <span class="font-semibold text-on-dark">08.00 - 15.30 WIB</span>
                        </li>
                        <li class="flex justify-between border-b border-surface-dark-elevated pb-2">
                            <span>Jumat</span>
                            <span class="font-semibold text-on-dark">08.00 - 16.00 WIB</span>
                        </li>
                        <li class="text-xs text-on-dark-soft/60 italic pt-1">
                            *Sabtu, Minggu & Hari Libur Nasional Tutup
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Kontak & Alamat -->
                <div>
                    <h4 class="font-display font-bold text-on-dark text-title-sm mb-4">Kontak & Lokasi</h4>
                    <ul class="space-y-3 text-body-sm">
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-accent-teal shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. Jenderal Sudirman No. 1, Kota Sawahlunto, Sumatera Barat</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-accent-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="https://wa.me/628112345678" target="_blank" class="hover:text-on-dark transition-colors font-mono">+62 811-2345-678</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-accent-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:mpp@sawahluntokota.go.id" class="hover:text-on-dark transition-colors">mpp@sawahluntokota.go.id</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer: Copyright and Socials -->
            <div class="pt-8 border-t border-surface-dark-elevated flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-caption text-muted-soft">
                    Copyright &copy; 2026 PBL SPEKTRA / MPP Kota Sawahlunto. All rights reserved.
                </p>
                <div class="flex gap-4 text-caption">
                    <a href="#" class="hover:text-on-dark transition-colors">Kebijakan Privasi</a>
                    <span class="text-surface-dark-elevated">&bull;</span>
                    <a href="#" class="hover:text-on-dark transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
