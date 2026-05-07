@extends('layouts.auth')

@section('title', 'Login - MPP Kota Sawahlunto')

@section('auth_content')
    <div class="flex flex-col items-center mb-6 text-center">
        <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-700" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9.75L12 3l9 6.75V21M9 21v-6h6v6" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-blue-800 tracking-tight">MPP Sawahlunto</h1>
        <p class="text-xl font-bold text-gray-800 mt-0.5">Selamat Datang Kembali</p>
        <p class="text-sm text-gray-500 mt-1">Silakan masuk untuk melanjutkan layanan</p>
    </div>

    {{-- card --}}
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl px-7 py-8">
        {{-- Alert error --}}
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Alert sukses --}}
        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-2.5">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
            @csrf

            {{-- NIK / Email --}}
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">
                    Alamat Email
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="contoh@email.com" autofocus
                        @php $emailClass = $errors->has('email') ? 'border-red-400' : 'border-gray-200'; @endphp
                        class="w-full pl-10 pr-4 py-3 text-sm bg-gray-50 border {{ $emailClass }} rounded-xl ...">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                        Password
                    </label>
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
                    <input type="password" name="password" id="password" placeholder="••••••••"
                        @php $passwordClass = $errors->has('password') ? 'border-red-400' : 'border-gray-200'; @endphp
                        class="w-full pl-10 pr-11 py-3 text-sm bg-gray-50 border {{ $passwordClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        {{-- Eye Open --}}
                        <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{-- Eye Closed --}}
                        <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Masuk --}}
            <button type="submit"
                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold rounded-2xl shadow-md shadow-blue-200 transition-all duration-150 mt-2">
                Masuk
            </button>
        </form>

        {{-- Divider + Daftar --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">Belum punya akun?</p>
            <a href="{{ route('register') }}"
                class="mt-2 flex items-center justify-center w-full py-3 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-bold rounded-2xl transition-all duration-150">
                Daftar Sekarang
            </a>
        </div>
    </div>
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
