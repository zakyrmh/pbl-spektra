@extends('layouts.auth')

@section('title', 'Lupa Password - MPP Kota Sawahlunto')

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
        <p class="text-xl font-bold text-gray-800 mt-0.5">Isi email untuk reset password</p>
        <p class="text-sm text-gray-500 mt-1">Kami akan mengirimkan tautan reset password ke email Anda</p>
    </div>

    {{-- card --}}
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl px-7 py-8">
        {{-- Alert sukses --}}
        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-2.5">
                {{ session('status') }}
            </div>
        @endif

        {{-- Heading --}}
        <div class="mb-5">
            <h2 class="text-base font-bold text-gray-800">Lupa Password</h2>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                Masukkan email Anda, kami akan kirimkan tautan reset password.
            </p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Email --}}
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
                    @php $emailClass = $errors->has('email') ? 'border-red-400' : 'border-gray-200'; @endphp
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="contoh@email.com" autofocus
                        class="w-full pl-10 pr-4 py-3 text-sm bg-gray-50 border {{ $emailClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Kirim --}}
            <button type="submit"
                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold rounded-2xl shadow-md shadow-blue-200 transition-all duration-150">
                Kirim Tautan Reset
            </button>
        </form>

        {{-- Kembali ke Login --}}
        <div class="mt-5 text-center">
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Login
            </a>
        </div>
    </div>
@endsection
