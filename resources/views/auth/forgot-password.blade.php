@extends('layouts.auth')

@section('title', 'Lupa Password - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-bold text-display-lg leading-tight font-display">
            Lupa Password?<br>Kami Bantu Pulihkan
        </h2>
        <p class="text-on-dark-soft text-body-md mt-4 leading-relaxed max-w-sm font-body">
            Masukkan email terdaftar Anda, kami akan mengirimkan tautan
            untuk membuat password baru dalam hitungan menit.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-8">
        <h1 class="text-display-sm font-bold text-ink font-display">Lupa Password</h1>
        <p class="text-body-md text-muted mt-2 font-body">Masukkan email Anda untuk menerima tautan reset password</p>
    </div>

    {{-- Alert sukses --}}
    @if (session('status'))
        <div class="mb-5 text-body-md text-status-serving bg-status-serving/10 border border-status-serving/20 rounded-lg px-4 py-3 font-body">
            {{ session('status') }}
        </div>
    @endif

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-body-md text-status-skipped bg-status-skipped/10 border border-status-skipped/20 rounded-lg px-4 py-3 font-body">
            {!! $errors->first() !!}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-title-sm font-semibold text-ink mb-2 font-body">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </span>
                @php 
                    $emailClass = $errors->has('email') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    placeholder="contoh@email.com" autofocus required
                    class="w-full pl-10 pr-4 py-3 text-body-md {{ $emailClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
            </div>
            @error('email')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover active:scale-[0.98] text-white text-button font-semibold rounded-pill shadow-sm transition-all duration-150 cursor-pointer">
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
            class="inline-flex items-center gap-1.5 text-body-md text-muted hover:text-primary transition-colors font-semibold font-body">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke halaman Login
        </a>
    </div>
@endsection
