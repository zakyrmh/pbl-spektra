@extends('layouts.auth')

@section('title', 'Lupa Password - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-extrabold text-4xl leading-tight">
            Lupa Password?<br>Kami Bantu Pulihkan
        </h2>
        <p class="text-blue-200 text-sm mt-4 leading-relaxed max-w-sm">
            Masukkan email terdaftar Anda, kami akan mengirimkan tautan
            untuk membuat password baru dalam hitungan menit.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">Lupa Password</h1>
        <p class="text-sm text-gray-500 mt-1">Masukkan email Anda untuk menerima tautan reset password</p>
    </div>

    {{-- Alert sukses --}}
    @if (session('status'))
        <div class="mb-5 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

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
                    placeholder="contoh@email.com" autofocus
                    class="w-full pl-10 pr-4 py-3 text-sm border {{ $emailClass }} rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-md shadow-blue-200 transition-all duration-150 flex items-center justify-center gap-2">
            Kirim Tautan Reset
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21.75 9.75l-9.75 6.75-9.75-6.75M3 9.75h18v10.5a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 20.25V9.75z" />
            </svg>
        </button>
    </form>

    {{-- Kembali ke Login --}}
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke halaman Login
        </a>
    </div>
@endsection
