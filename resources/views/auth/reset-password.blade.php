@extends('layouts.auth')

@section('title', 'Reset Password - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-extrabold text-4xl leading-tight">
            Buat Password Baru<br>Untuk Akun Anda
        </h2>
        <p class="text-blue-200 text-sm mt-4 leading-relaxed max-w-sm">
            Pastikan membuat password yang kuat dan mudah diingat.
            Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-8">
        <h1 class="text-display-sm font-bold text-ink font-display">Reset Password</h1>
        <p class="text-body-sm text-muted mt-1 font-body">Silakan masukkan password baru Anda di bawah ini</p>
    </div>

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-body-sm text-status-skipped bg-status-skipped/5 border border-status-skipped/20 rounded-lg px-4 py-3 font-body">
            {!! $errors->first() !!}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Hidden Token --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div>
            <label class="block text-title-sm font-semibold text-ink mb-2 font-body">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </span>
                @php $emailClass = $errors->has('email') ? 'border-status-skipped bg-status-skipped/5' : 'border-hairline bg-canvas'; @endphp
                <input type="email" name="email" id="email" value="{{ request()->email ?? old('email') }}"
                    readonly
                    class="w-full pl-10 pr-4 py-3 text-body-md border {{ $emailClass }} rounded-md text-muted bg-surface-soft focus:outline-none transition cursor-not-allowed font-body">
            </div>
            @error('email')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div>
            <label class="block text-title-sm font-semibold text-ink mb-2 font-body">Password Baru</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                @php $passwordClass = $errors->has('password') ? 'border-status-skipped bg-status-skipped/5' : 'border-hairline bg-canvas'; @endphp
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" autofocus
                    class="w-full pl-10 pr-11 py-3 text-body-md border {{ $passwordClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 focus:ring-primary/12 focus:border-primary transition font-body">
                <button type="button" onclick="togglePassword('password', 'eye-open-1', 'eye-closed-1')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-muted hover:text-ink transition-colors cursor-pointer">
                    <svg id="eye-open-1" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-1" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block text-title-sm font-semibold text-ink mb-2 font-body">Konfirmasi Password Baru</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Ulangi password baru"
                    class="w-full pl-10 pr-11 py-3 text-body-md border border-hairline bg-canvas rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 focus:ring-primary/12 focus:border-primary transition font-body">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-open-2', 'eye-closed-2')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-muted hover:text-ink transition-colors cursor-pointer">
                    <svg id="eye-open-2" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-2" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover active:scale-[0.98] text-white text-button font-semibold rounded-pill shadow-md transition-all duration-150 cursor-pointer mt-2">
            Simpan Password Baru
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </button>
    </form>

    {{-- Kembali ke Login --}}
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-body-sm text-muted hover:text-primary transition-colors font-semibold font-body">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Batal dan kembali ke halaman Login
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
