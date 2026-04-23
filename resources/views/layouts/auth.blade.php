@extends('layouts.app')

@section('base_content')
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10"
        style="background: linear-gradient(160deg, #eef3fb 0%, #dce8f8 50%, #e8f0fc 100%);">
        @yield('auth_content')

        {{-- Status Badge --}}
        <div class="mt-6">
            <span
                class="inline-flex items-center gap-2 bg-white/80 backdrop-blur border border-green-200 text-green-700 text-xs font-semibold px-4 py-1.5 rounded-full shadow-sm">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Sistem Antrean Aktif
            </span>
        </div>

        {{-- Info Cards --}}
        <div class="grid grid-cols-2 gap-3 mt-4 w-full max-w-sm">
            <div class="bg-white/70 backdrop-blur rounded-2xl p-4 shadow-sm border border-white">
                <div class="text-amber-600 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" d="M12 8v4m0 4h.01" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-800">Pusat Bantuan</p>
                <p class="text-[11px] text-gray-500 leading-snug mt-0.5">Panduan penggunaan aplikasi &amp; layanan MPP.</p>
            </div>
            <div class="bg-white/70 backdrop-blur rounded-2xl p-4 shadow-sm border border-white">
                <div class="text-green-600 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-800">Keamanan Data</p>
                <p class="text-[11px] text-gray-500 leading-snug mt-0.5">Data Anda dienkripsi sesuai standar pemerintah.</p>
            </div>
        </div>

        {{-- Footer --}}
        <p class="mt-6 text-[11px] text-gray-400 text-center">
            &copy; {{ date('Y') }} MPP SAWAHLUNTO
            <span class="mx-1">·</span>
            <a href="#" class="hover:text-gray-600 transition-colors">Privasi</a>
            <span class="mx-1">·</span>
            <a href="#" class="hover:text-gray-600 transition-colors">Syarat</a>
            <span class="mx-1">·</span>
            <a href="#" class="hover:text-gray-600 transition-colors">Kontak</a>
        </p>
    </div>
@endsection
