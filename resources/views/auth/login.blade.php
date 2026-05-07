@extends('layouts.auth')

@section('title', 'Login - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-extrabold text-4xl leading-tight">
            Pelayanan Publik yang<br>Bermartabat & Modern
        </h2>
        <p class="text-blue-200 text-sm mt-4 leading-relaxed max-w-sm">
            Menghubungkan warga Sawahlunto dengan layanan pemerintah melalui
            teknologi terintegrasi yang transparan, cepat, dan terpercaya.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">Selamat Datang Kembali</h1>
        <p class="text-sm text-gray-500 mt-1">Sistem Antrean Digital Mall Pelayanan Publik</p>
    </div>

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Alert sukses --}}
    @if (session('status'))
        <div class="mb-5 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                NIK atau Email
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </span>
                @php $emailClass = $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    placeholder="Masukkan NIK atau email Anda" autofocus
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $emailClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                <a href="{{ route('password.request') }}"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Lupa Password?
                </a>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                @php $passwordClass = $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="password" name="password" id="password" placeholder="••••••••"
                    class="w-full pl-10 pr-11 py-3 text-sm border {{ $passwordClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <button type="button" onclick="togglePassword()"
                    class="absolute inset-y-0 right-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember"
                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="remember" class="text-sm text-gray-600">Ingat saya di perangkat ini</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-md shadow-blue-200 transition-all duration-150 flex items-center justify-center gap-2">
            Masuk
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400 font-medium">ATAU</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    {{-- Daftar --}}
    <a href="{{ route('register') }}"
        class="flex items-center justify-center w-full py-3 border-2 border-gray-300 hover:border-blue-400 text-gray-700 hover:text-blue-600 text-sm font-semibold rounded-xl transition-all duration-150">
        Daftar Sekarang
    </a>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
@endpush
