@extends('layouts.auth')

@section('title', 'Daftar Akun - MPP Kota Sawahlunto')

@section('auth_hero')
    <div>
        <h2 class="text-white font-bold text-display-lg leading-tight font-display">
            Daftar & Nikmati<br>Layanan Tanpa Antre
        </h2>
        <p class="text-on-dark-soft text-body-md mt-4 leading-relaxed max-w-sm font-body">
            Buat akun sekali, gunakan selamanya. Reservasi layanan MPP dari mana saja
            tanpa harus datang dan menunggu lama.
        </p>
    </div>
@endsection

@section('auth_content')
    <div class="mb-6">
        <h1 class="text-display-sm font-bold text-ink font-display">Buat Akun Baru</h1>
        <p class="text-body-md text-muted mt-2 font-body">Lengkapi data diri untuk mengakses layanan MPP</p>
    </div>

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="mb-5 text-body-md text-status-skipped bg-status-skipped/10 border border-status-skipped/20 rounded-lg px-4 py-3 font-body">
            {!! $errors->first() !!}
        </div>
    @endif

    <form action="{{ route('register.process') }}" method="POST" class="space-y-4"
        x-data="{ 
            password: '', 
            password_confirmation: '', 
            showRules: false,
            get hasMinLength() { return this.password.length >= 8; },
            get hasLetter() { return /[a-zA-Z]/.test(this.password); },
            get hasNumber() { return /[0-9]/.test(this.password); },
            get hasSymbol() { return /[^a-zA-Z0-9]/.test(this.password); },
            get strengthScore() {
                let score = 0;
                if (this.hasMinLength) score++;
                if (this.hasLetter) score++;
                if (this.hasNumber) score++;
                if (this.hasSymbol) score++;
                return score;
            }
        }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block text-title-sm font-semibold text-ink mb-2 font-body">Nama Lengkap</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </span>
                @php 
                    $nameClass = $errors->has('name') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap" autofocus required
                    class="w-full pl-10 pr-4 py-3 text-body-md {{ $nameClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
            </div>
            @error('name')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- NIK --}}
        <div>
            <label for="nik" class="block text-title-sm font-semibold text-ink mb-2 font-body">NIK</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                    </svg>
                </span>
                @php 
                    $nikClass = $errors->has('nik') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="text" name="nik" id="nik" value="{{ old('nik') }}"
                    placeholder="16 digit NIK sesuai KTP" maxlength="16" required
                    class="w-full pl-10 pr-4 py-3 text-body-md {{ $nikClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
            </div>
            @error('nik')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

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
                    placeholder="contoh@email.com" required
                    class="w-full pl-10 pr-4 py-3 text-body-md {{ $emailClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
            </div>
            @error('email')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- No. HP --}}
        <div>
            <label for="phone_number" class="block text-title-sm font-semibold text-ink mb-2 font-body">Nomor HP</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3" />
                    </svg>
                </span>
                @php 
                    $phone_numberClass = $errors->has('phone_number') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                    placeholder="Contoh: 081234567890" maxlength="15" required
                    class="w-full pl-10 pr-4 py-3 text-body-md {{ $phone_numberClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
            </div>
            @error('phone_number')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-title-sm font-semibold text-ink mb-2 font-body">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                @php 
                    $passwordClass = $errors->has('password') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required
                    x-model="password"
                    @focus="showRules = true"
                    class="w-full pl-10 pr-11 py-3 text-body-md {{ $passwordClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
                <button type="button" onclick="togglePassword('password', 'eye-open-1', 'eye-closed-1')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-muted hover:text-ink transition-colors cursor-pointer"
                    aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg id="eye-open-1" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-1" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-caption text-status-skipped font-body">{{ $message }}</p>
            @enderror

            {{-- Password Strength & Rules Checker --}}
            <div x-show="showRules || password.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="p-4 bg-surface-soft border border-hairline rounded-lg space-y-3 mt-2"
                 x-cloak>
                
                {{-- Strength Bar --}}
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="text-caption font-semibold text-muted">Kekuatan Kata Sandi:</span>
                        <span class="text-caption font-bold" 
                              :class="{
                                  'text-muted': strengthScore === 0,
                                  'text-status-skipped': strengthScore === 1,
                                  'text-status-waiting': strengthScore === 2,
                                  'text-status-called': strengthScore === 3,
                                  'text-status-serving': strengthScore === 4
                              }"
                              x-text="strengthScore === 0 ? 'Sangat Lemah' : 
                                      strengthScore === 1 ? 'Lemah' : 
                                      strengthScore === 2 ? 'Sedang' : 
                                      strengthScore === 3 ? 'Kuat' : 'Sangat Kuat'">
                        </span>
                    </div>
                    <div class="grid grid-cols-4 gap-1 h-1.5 rounded-full overflow-hidden bg-surface-strong">
                        <div class="h-full rounded-full transition-all duration-300" 
                             :class="{
                                 'bg-status-skipped': strengthScore === 1,
                                 'bg-status-waiting': strengthScore >= 2 && strengthScore < 3,
                                 'bg-status-called': strengthScore >= 3 && strengthScore < 4,
                                 'bg-status-serving': strengthScore >= 4,
                                 'bg-transparent': strengthScore < 1
                             }"></div>
                        <div class="h-full rounded-full transition-all duration-300" 
                             :class="{
                                 'bg-status-waiting': strengthScore === 2,
                                 'bg-status-called': strengthScore >= 3 && strengthScore < 4,
                                 'bg-status-serving': strengthScore >= 4,
                                 'bg-transparent': strengthScore < 2
                             }"></div>
                        <div class="h-full rounded-full transition-all duration-300" 
                             :class="{
                                 'bg-status-called': strengthScore === 3,
                                 'bg-status-serving': strengthScore >= 4,
                                 'bg-transparent': strengthScore < 3
                             }"></div>
                        <div class="h-full rounded-full transition-all duration-300" 
                             :class="{
                                 'bg-status-serving': strengthScore >= 4,
                                 'bg-transparent': strengthScore < 4
                             }"></div>
                    </div>
                </div>

                {{-- Rules Checklist --}}
                <ul class="space-y-1.5 text-caption font-body">
                    <li class="flex items-center gap-2 transition-colors duration-200" 
                        :class="hasMinLength ? 'text-status-serving font-medium' : 'text-muted'">
                        <span class="shrink-0 transition-transform duration-300" :class="hasMinLength ? 'scale-110' : ''">
                            <svg x-show="hasMinLength" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <svg x-show="!hasMinLength" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </span>
                        Minimal 8 karakter
                    </li>
                    <li class="flex items-center gap-2 transition-colors duration-200" 
                        :class="hasLetter ? 'text-status-serving font-medium' : 'text-muted'">
                        <span class="shrink-0 transition-transform duration-300" :class="hasLetter ? 'scale-110' : ''">
                            <svg x-show="hasLetter" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <svg x-show="!hasLetter" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </span>
                        Mengandung minimal satu huruf
                    </li>
                    <li class="flex items-center gap-2 transition-colors duration-200" 
                        :class="hasNumber ? 'text-status-serving font-medium' : 'text-muted'">
                        <span class="shrink-0 transition-transform duration-300" :class="hasNumber ? 'scale-110' : ''">
                            <svg x-show="hasNumber" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <svg x-show="!hasNumber" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </span>
                        Mengandung minimal satu angka
                    </li>
                    <li class="flex items-center gap-2 transition-colors duration-200" 
                        :class="hasSymbol ? 'text-status-serving font-medium' : 'text-muted'">
                        <span class="shrink-0 transition-transform duration-300" :class="hasSymbol ? 'scale-110' : ''">
                            <svg x-show="hasSymbol" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <svg x-show="!hasSymbol" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </span>
                        Mengandung minimal satu simbol
                    </li>
                </ul>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-title-sm font-semibold text-ink mb-2 font-body">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-muted-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5M5.25 10.5h13.5A1.5 1.5 0 0120.25 12v7.5A1.5 1.5 0 0118.75 21H5.25A1.5 1.5 0 013.75 19.5V12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </span>
                @php 
                    $passwordConfirmationClass = $errors->has('password') 
                        ? 'border-2 border-status-skipped bg-status-skipped/[0.02] focus:ring-status-skipped/12 focus:border-status-skipped' 
                        : 'border border-hairline bg-canvas focus:ring-primary/12 focus:border-primary focus:border-2'; 
                @endphp
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Ulangi password" required
                    x-model="password_confirmation"
                    class="w-full pl-10 pr-11 py-3 text-body-md {{ $passwordConfirmationClass }} rounded-md text-ink placeholder-muted-soft focus:outline-none focus:ring-3 transition font-body">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-open-2', 'eye-closed-2')"
                    class="absolute inset-y-0 right-3.5 flex items-center text-muted hover:text-ink transition-colors cursor-pointer"
                    aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
                    <svg id="eye-open-2" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg id="eye-closed-2" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>

            {{-- Password Match Indicator --}}
            <div x-show="password_confirmation.length > 0" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 transform -translate-y-1"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-2 text-caption font-body flex items-center gap-1.5"
                 :class="password === password_confirmation ? 'text-status-serving' : 'text-status-skipped'"
                 x-cloak>
                <span class="shrink-0">
                    <svg x-show="password === password_confirmation" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <svg x-show="password !== password_confirmation" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-status-skipped" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </span>
                <span x-text="password === password_confirmation ? 'Password cocok' : 'Konfirmasi password tidak cocok'"></span>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-primary hover:bg-primary-hover active:scale-[0.98] text-white text-button font-semibold rounded-pill shadow-sm transition-all duration-150 mt-2 cursor-pointer">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-body-md text-muted hover:text-primary transition-colors font-semibold font-body">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
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
