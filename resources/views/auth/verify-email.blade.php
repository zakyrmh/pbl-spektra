@extends('layouts.auth')

@section('title', 'Verifikasi Email - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-bold text-display-lg leading-tight font-display">
            Verifikasi Email<br>Langkah Terakhir
        </h2>
        <p class="text-on-dark-soft text-body-md mt-4 leading-relaxed max-w-sm font-body">
            Satu langkah lagi untuk dapat menggunakan layanan kami. Silakan verifikasi email Anda untuk melanjutkan ke dashboard.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-8">
        <h1 class="text-display-sm font-bold text-ink font-display">Verifikasi Email</h1>
        <p class="text-body-md text-muted mt-2 font-body">Tautan verifikasi telah dikirimkan ke alamat email Anda.</p>
    </div>

    {{-- Alert sukses pengiriman ulang --}}
    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 text-body-md text-status-serving bg-status-serving/10 border border-status-serving/20 rounded-lg px-4 py-3 font-body animate-pulse">
            Tautan verifikasi baru telah dikirimkan ke alamat email Anda. Silakan periksa kembali email Anda.
        </div>
    @endif



    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-body-md text-status-skipped bg-status-skipped/10 border border-status-skipped/20 rounded-lg px-4 py-3 font-body">
            {!! $errors->first() !!}
        </div>
    @endif

    <div class="space-y-6 font-body text-body-md text-muted">
        <p class="leading-relaxed">
            Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda:
            <strong class="text-ink break-all font-semibold">{{ $email ?? session('unverified_email') }}</strong>
        </p>
        <p class="leading-relaxed">
            Jika Anda tidak menerima email tersebut, silakan klik tombol di bawah ini untuk mengirimkan ulang tautan verifikasi.
        </p>
    </div>

    <form action="{{ route('verification.send') }}" method="POST" class="mt-6 space-y-5">
        @csrf

        {{-- Submit --}}
        <button type="submit"
            class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover active:scale-[0.98] text-white text-button font-semibold rounded-pill shadow-sm transition-all duration-150 cursor-pointer">
            Kirim Ulang Email Verifikasi
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
        </button>
    </form>

    {{-- Kembali ke Login --}}
    <div class="mt-8 text-center border-t border-hairline pt-6">
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
