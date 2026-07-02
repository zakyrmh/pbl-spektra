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
                <div class="mt-8 text-center space-y-3" x-data="{ activeModal: null }">
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
                        <button type="button" @click="activeModal = 'help'" class="hover:text-ink transition-colors cursor-pointer">Pusat Bantuan</button>
                        <span class="mx-1.5">·</span>
                        <button type="button" @click="activeModal = 'privacy'" class="hover:text-ink transition-colors cursor-pointer">Kebijakan Privasi</button>
                        <span class="mx-1.5">·</span>
                        <button type="button" @click="activeModal = 'guide'" class="hover:text-ink transition-colors cursor-pointer">Panduan Pengguna</button>
                    </p>
                    <p class="text-caption text-muted-soft font-body">&copy; {{ date('Y') }} MPP Sawahlunto. Melayani dengan Hati.</p>

                    <!-- Modal Backdrop & Wrapper -->
                    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 text-left"
                         x-show="activeModal !== null"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         x-cloak>
                        
                        <!-- Backdrop overlay -->
                        <div class="fixed inset-0 bg-ink/50 dark:bg-black/70 backdrop-blur-sm transition-opacity" @click="activeModal = null"></div>

                        <!-- Modal Content Box -->
                        <div class="relative bg-canvas dark:bg-gray-900 border border-hairline dark:border-white/10 w-full max-w-[480px] rounded-xl shadow-xl p-8 transform transition-all z-10 flex flex-col"
                             x-show="activeModal !== null"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                            
                            <!-- Close Button (button-icon styled) -->
                            <button @click="activeModal = null" 
                                    class="absolute top-8 right-8 w-10 h-10 rounded-full bg-surface-card dark:bg-gray-800 border border-hairline dark:border-white/10 flex items-center justify-center text-muted hover:text-ink dark:hover:text-white transition-colors cursor-pointer" 
                                    aria-label="Tutup modal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Header -->
                            <div class="pr-12">
                                <h3 class="text-display-sm font-semibold tracking-tight font-display text-ink dark:text-white flex items-center gap-2.5">
                                    <!-- Dynamic Icon based on activeModal -->
                                    <template x-if="activeModal === 'guide'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </template>
                                    <template x-if="activeModal === 'privacy'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </template>
                                    <template x-if="activeModal === 'help'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                        </svg>
                                    </template>
                                    <span x-text="activeModal === 'guide' ? 'Panduan Pengguna' : activeModal === 'privacy' ? 'Kebijakan Privasi' : 'Pusat Bantuan'"></span>
                                </h3>
                            </div>

                            <!-- Body -->
                            <div class="mt-6 text-body-md text-body dark:text-gray-200 overflow-y-auto max-h-[60vh] font-body text-left leading-relaxed">
                                
                                <!-- Panduan Pengguna Content -->
                                <div x-show="activeModal === 'guide'" class="space-y-4">
                                    <p>Berikut adalah langkah mudah untuk menggunakan sistem Mal Pelayanan Publik (MPP) digital:</p>
                                    <ol class="space-y-4 pr-1">
                                        <li class="flex gap-4 items-start">
                                            <span class="shrink-0 w-7 h-7 rounded-full bg-primary/10 dark:bg-accent-teal/20 text-primary dark:text-accent-teal flex items-center justify-center font-bold text-caption font-mono">1</span>
                                            <div>
                                                <h4 class="text-title-sm font-semibold text-ink dark:text-white">Langkah Login/Daftar</h4>
                                                <p class="text-muted text-body-sm leading-relaxed mt-1">Gunakan alamat email dan password Anda untuk masuk, atau daftar jika belum memiliki akun dengan mengisi NIK dan email valid.</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-4 items-start">
                                            <span class="shrink-0 w-7 h-7 rounded-full bg-primary/10 dark:bg-accent-teal/20 text-primary dark:text-accent-teal flex items-center justify-center font-bold text-caption font-mono">2</span>
                                            <div>
                                                <h4 class="text-title-sm font-semibold text-ink dark:text-white">Booking Antrean</h4>
                                                <p class="text-muted text-body-sm leading-relaxed mt-1">Tentukan jadwal kunjungan, isi data pemohon, dan lakukan booking tiket antrean online.</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-4 items-start">
                                            <span class="shrink-0 w-7 h-7 rounded-full bg-primary/10 dark:bg-accent-teal/20 text-primary dark:text-accent-teal flex items-center justify-center font-bold text-caption font-mono">3</span>
                                            <div>
                                                <h4 class="text-title-sm font-semibold text-ink dark:text-white">Pilih Hari dan sesi</h4>
                                                <p class="text-muted text-body-sm leading-relaxed mt-1">Pilih hari dan sesi kunjungan sesuai dengan ketersediaan.</p>
                                            </div>
                                        </li>
                                        <li class="flex gap-4 items-start">
                                            <span class="shrink-0 w-7 h-7 rounded-full bg-primary/10 dark:bg-accent-teal/20 text-primary dark:text-accent-teal flex items-center justify-center font-bold text-caption font-mono">4</span>
                                            <div>
                                                <h4 class="text-title-sm font-semibold text-ink dark:text-white">Pantau Antrean Real-time</h4>
                                                <p class="text-muted text-body-sm leading-relaxed mt-1">Lihat nomor antrean yang sedang dilayani saat ini secara real-time dari aplikasi sebelum Anda berangkat ke lokasi.</p>
                                            </div>
                                        </li>
                                    </ol>
                                </div>

                                <!-- Kebijakan Privasi Content -->
                                <div x-show="activeModal === 'privacy'" class="space-y-4">
                                    <p class="text-title-sm font-semibold text-ink dark:text-white">Komitmen Keamanan Data Warga</p>
                                    <p class="text-body-md text-body dark:text-gray-300">Mal Pelayanan Publik (MPP) Kota Sawahlunto berkomitmen penuh untuk melindungi informasi pribadi Anda. Berikut adalah ringkasan kebijakan privasi kami:</p>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <p class="text-body-sm text-muted leading-relaxed"><strong class="text-ink dark:text-white font-semibold">Verifikasi NIK Terenkripsi:</strong> NIK Anda dikumpulkan semata-mata untuk verifikasi identitas resmi pemohon layanan publik dan disimpan menggunakan enkripsi standar industri.</p>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <p class="text-body-sm text-muted leading-relaxed"><strong class="text-ink dark:text-white font-semibold">Kerahasiaan Alamat Email:</strong> Email warga hanya digunakan untuk mengirimkan pendaftaran akun, link reset password, pemberitahuan tiket antrean, dan tidak pernah disebarluaskan ke pihak ketiga.</p>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <p class="text-body-sm text-muted leading-relaxed"><strong class="text-ink dark:text-white font-semibold">Akses Terbatas:</strong> Hanya petugas berwenang dari instansi tujuan pelayanan Anda yang dapat melihat data pengajuan guna memproses permohonan Anda.</p>
                                        </li>
                                    </ul>
                                    <p class="text-caption text-muted-soft italic mt-4">Dengan mendaftar dan menggunakan platform kami, Anda setuju bahwa data yang diberikan digunakan semata-mata demi kelancaran administrasi pelayanan publik.</p>
                                </div>

                                <!-- Pusat Bantuan Content -->
                                <div x-show="activeModal === 'help'" class="space-y-4">
                                    <p>Butuh bantuan lebih lanjut atau menghadapi kendala teknis? Tim customer service kami siap melayani Anda:</p>
                                    <div class="grid grid-cols-1 gap-3.5 mt-2">
                                        <!-- WhatsApp -->
                                        <div class="flex items-center gap-4 p-4 bg-surface-soft dark:bg-gray-800/50 border border-hairline dark:border-white/10 rounded-lg">
                                            <span class="w-10 h-10 rounded-full bg-surface-card dark:bg-gray-800 border border-hairline dark:border-white/10 flex items-center justify-center text-status-serving shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 10.742c.07.13.141.26.211.39a13.064 13.064 0 005.107 5.107c.13.07.26.14.39.211l.452-.452a1.5 1.5 0 011.53-.322l2.259.753a1.5 1.5 0 011.018 1.417v2.241a1.5 1.5 0 01-1.5 1.5c-9.665 0-17.5-7.835-17.5-17.5a1.5 1.5 0 011.5-1.5h2.241a1.5 1.5 0 011.417 1.018l.753 2.259a1.5 1.5 0 01-.322 1.53l-.452.452z" />
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="text-caption font-semibold text-muted block">WhatsApp CS</span>
                                                <a href="https://wa.me/628123456789" target="_blank" class="text-body-md font-bold text-ink dark:text-white hover:text-primary transition-colors font-mono">+62 812-3456-789</a>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="flex items-center gap-4 p-4 bg-surface-soft dark:bg-gray-800/50 border border-hairline dark:border-white/10 rounded-lg">
                                            <span class="w-10 h-10 rounded-full bg-surface-card dark:bg-gray-800 border border-hairline dark:border-white/10 flex items-center justify-center text-primary shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="text-caption font-semibold text-muted block">E-mail Resmi</span>
                                                <a href="mailto:mpp@sawahlunto.go.id" class="text-body-md font-bold text-ink dark:text-white hover:text-primary transition-colors">mpp@sawahlunto.go.id</a>
                                            </div>
                                        </div>

                                        <!-- Jam Operasional -->
                                        <div class="flex items-center gap-4 p-4 bg-surface-soft dark:bg-gray-800/50 border border-hairline dark:border-white/10 rounded-lg">
                                            <span class="w-10 h-10 rounded-full bg-surface-card dark:bg-gray-800 border border-hairline dark:border-white/10 flex items-center justify-center text-status-waiting shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-status-waiting" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="text-caption font-semibold text-muted block">Jam Operasional Pelayanan</span>
                                                <p class="text-body-md font-bold text-ink dark:text-white">Senin - Jumat | 08:00 - 15:30 WIB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Footer (tombol primary kanan) -->
                            <div class="mt-8 flex justify-end">
                                <button @click="activeModal = null" 
                                        class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-button transition-colors active:scale-[0.98] duration-150 cursor-pointer">
                                    Tutup
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
