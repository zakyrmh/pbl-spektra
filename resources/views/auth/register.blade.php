@extends('layouts.auth')

@section('title', 'Daftar Akun - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-extrabold text-4xl leading-tight">
            Daftar & Nikmati<br>Layanan Tanpa Antre
        </h2>
        <p class="text-blue-200 text-sm mt-4 leading-relaxed max-w-sm">
            Buat akun sekali, gunakan selamanya. Reservasi layanan MPP dari mana saja
            tanpa harus datang dan menunggu lama.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi data diri untuk mengakses layanan MPP</p>
    </div>

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </span>
                @php $nameClass = $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap" autofocus
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $nameClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('name')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- NIK --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIK</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                    </svg>
                </span>
                @php $nikClass = $errors->has('nik') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="text" name="nik" id="nik" value="{{ old('nik') }}"
                    placeholder="16 digit NIK sesuai KTP" maxlength="16"
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $nikClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('nik')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </span>
                @php $emailClass = $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    placeholder="contoh@email.com"
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $emailClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- No. HP --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor HP</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3" />
                    </svg>
                </span>
                @php $noTelpClass = $errors->has('no_telp') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}"
                    placeholder="Contoh: 081234567890" maxlength="15"
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $noTelpClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('no_telp')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                @php $passwordClass = $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50'; @endphp
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                    class="w-full pl-10 pr-11 py-3 text-sm border {{ $passwordClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <button type="button" onclick="togglePassword('password', 'eye-open-1', 'eye-closed-1')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eye-open-1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none"
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

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Ulangi password"
                    class="w-full pl-10 pr-11 py-3 text-sm border border-gray-300 bg-gray-50 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-open-2', 'eye-closed-2')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="eye-open-2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-md shadow-blue-200 transition-all duration-150 mt-2">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Sudah punya akun? Login
        </a>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, eyeOpenId, eyeClosedId) {
            const input = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);
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
