@extends('layouts.private')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Breadcrumb / Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-display font-bold tracking-tight text-ink dark:text-white">Manajemen Profil</h2>
            <p class="text-sm text-muted dark:text-on-dark-soft mt-1 font-body">Lengkapi dan perbarui data diri Anda untuk mempermudah pelayanan antrean di MPP Kota Sawahlunto.</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('status'))
        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200/50 flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-semibold">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Alert Validation Errors -->
    @if($errors->any())
        <div class="p-4 rounded-lg bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-200/50 space-y-2">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-semibold">Terdapat beberapa kesalahan input:</p>
            </div>
            <ul class="list-disc list-inside text-xs pl-8 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content Grid -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- Left Column: Avatar & Account Info (Spans 4) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 p-6 shadow-xs flex flex-col items-center text-center">
                <h3 class="text-title-sm font-display font-bold text-ink dark:text-white mb-6 w-full text-left pb-3 border-b border-hairline dark:border-white/5">Foto Profil</h3>

                <!-- Interactive Avatar Upload widget with AlpineJS -->
                <div x-data="{ avatarPreview: '{{ $user->avatar_url }}' }" class="space-y-4 flex flex-col items-center">
                    <div class="relative group">
                        <img :src="avatarPreview" alt="Foto Profil" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-surface-soft dark:border-white/5 shadow-md">
                        
                        <label for="avatar-input" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                    </div>

                    <div class="flex flex-col items-center">
                        <label for="avatar-input" class="px-4 py-2 bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill text-xs font-semibold cursor-pointer transition-all duration-150 focus-within:ring-3 focus-within:ring-accent-teal">
                            <span>Pilih Foto Baru</span>
                            <input id="avatar-input" name="avatar" type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg"
                                   @change="avatarPreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft mt-2">Format: JPG, JPEG, PNG (Maks 2MB)</p>
                    </div>
                </div>

                <!-- Account Status Info -->
                <div class="w-full mt-8 pt-6 border-t border-hairline dark:border-white/5 space-y-3 text-left">
                    <div class="flex justify-between items-center text-xs font-body">
                        <span class="text-muted dark:text-on-dark-soft font-semibold">Tipe Akun:</span>
                        <span class="px-2 py-0.5 bg-primary/10 text-primary dark:text-accent-teal rounded-pill font-bold">{{ $user->role_label }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-body">
                        <span class="text-muted dark:text-on-dark-soft font-semibold">Terdaftar Sejak:</span>
                        <span class="text-ink dark:text-white font-medium">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-body">
                        <span class="text-muted dark:text-on-dark-soft font-semibold">Verifikasi NIK:</span>
                        @if($user->nik)
                            <span class="text-green-600 dark:text-green-400 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                Terverifikasi
                            </span>
                        @else
                            <span class="text-status-waiting font-bold flex items-center gap-1 animate-pulse">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Belum Lengkap
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Form & KTP (Spans 8) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 p-6 shadow-xs space-y-6">
                <h3 class="text-title-md font-display font-bold text-ink dark:text-white pb-3 border-b border-hairline dark:border-white/5">Informasi Data Diri</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Input -->
                    <div class="space-y-2">
                        <label for="name" class="block text-title-sm font-semibold text-ink dark:text-white">Nama Lengkap <span class="text-status-skipped">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full h-12 px-4 bg-canvas dark:bg-white/5 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-md text-body-md focus:border-2 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/12 transition-all @error('name') border-status-skipped @enderror">
                        @error('name')
                            <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number Input -->
                    <div class="space-y-2">
                        <label for="phone_number" class="block text-title-sm font-semibold text-ink dark:text-white">Nomor Handphone <span class="text-status-skipped">*</span></label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number ?? $user->phone_number) }}" required
                               class="w-full h-12 px-4 bg-canvas dark:bg-white/5 text-ink dark:text-white border border-hairline dark:border-white/10 rounded-md text-body-md focus:border-2 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/12 transition-all @error('phone_number') border-status-skipped @enderror"
                               placeholder="Contoh: 081234567890">
                        @error('phone_number')
                            <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIK Input (Read-only for Security) -->
                    <div class="space-y-2">
                        <label for="nik" class="block text-title-sm font-semibold text-ink dark:text-white">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" id="nik" value="{{ $user->nik ?? 'Belum Terisi' }}" disabled
                               class="w-full h-12 px-4 bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft/50 border border-hairline dark:border-white/10 rounded-md text-body-md cursor-not-allowed">
                        <p class="text-[11px] text-muted dark:text-on-dark-soft">NIK digunakan untuk verifikasi di Front Office dan tidak dapat diubah secara mandiri.</p>
                    </div>

                    <!-- Email Input (Read-only for Security) -->
                    <div class="space-y-2">
                        <label for="email" class="block text-title-sm font-semibold text-ink dark:text-white">Alamat Email</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled
                               class="w-full h-12 px-4 bg-surface-soft dark:bg-white/5 text-muted dark:text-on-dark-soft/50 border border-hairline dark:border-white/10 rounded-md text-body-md cursor-not-allowed">
                        <p class="text-[11px] text-muted dark:text-on-dark-soft font-body">Email digunakan sebagai identitas akun masuk (login) Anda.</p>
                    </div>

                    <!-- Priority Checkbox -->
                    <div class="col-span-1 md:col-span-2 space-y-2 pt-2">
                        <div class="flex items-start gap-3 p-4 bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-lg">
                            <input type="checkbox" id="is_priority" name="is_priority" value="1" {{ old('is_priority', $user->is_priority) ? 'checked' : '' }}
                                   class="w-5 h-5 text-primary dark:text-accent-teal border-hairline rounded focus:ring-primary dark:focus:ring-accent-teal cursor-pointer mt-0.5">
                            <div>
                                <label for="is_priority" class="block text-title-sm font-bold text-ink dark:text-white cursor-pointer select-none">
                                    Saya adalah Pengunjung Kelompok Rentan (Lansia, Ibu Hamil, & Disabilitas)
                                </label>
                                <p class="text-xs text-muted dark:text-on-dark-soft mt-1">
                                    Centang opsi ini jika Anda berusia ≥60 tahun (Lansia), sedang hamil, atau memiliki disabilitas fisik/sensorik. Anda akan mendapatkan prioritas pelayanan antrean. Status ini akan diverifikasi oleh petugas saat check-in.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto KTP Upload Widget (Spans full width) -->
                <div class="space-y-4 pt-4 border-t border-hairline dark:border-white/5">
                    <h4 class="text-title-sm font-display font-bold text-ink dark:text-white">Unggah Foto KTP</h4>
                    <p class="text-xs text-muted dark:text-on-dark-soft font-body">Unggah foto KTP fisik Anda untuk verifikasi identitas di loket pelayanan. Foto harus jelas dan terbaca.</p>

                    <div x-data="{ ktpPreview: '{{ $user->ktp_photo_url }}' }" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center bg-surface-soft dark:bg-white/5 p-4 rounded-lg border border-hairline dark:border-white/5">
                        <!-- Preview Card -->
                        <div class="flex items-center justify-center min-h-[150px] bg-canvas dark:bg-surface-dark rounded-lg border border-hairline dark:border-white/10 overflow-hidden p-2 relative group">
                            <template x-if="ktpPreview">
                                <img :src="ktpPreview" alt="Foto KTP Preview" class="max-h-36 object-contain rounded-sm shadow-xs transition-transform duration-300 group-hover:scale-105">
                            </template>
                            <template x-if="!ktpPreview">
                                <div class="text-center space-y-2 p-4">
                                    <svg class="w-12 h-12 text-muted-soft dark:text-on-dark-soft mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9a2 2 0 10-4 0 2 2 0 004 0zm-6 8a6 6 0 0110.89-3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 019 17z" />
                                    </svg>
                                    <p class="text-xs text-muted dark:text-on-dark-soft font-semibold font-body">Belum Ada Foto KTP</p>
                                </div>
                            </template>
                        </div>

                        <!-- Upload actions -->
                        <div class="space-y-3">
                            <label for="ktp-input" class="w-full h-11 flex items-center justify-center gap-2 px-6 bg-canvas hover:bg-surface-soft text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill text-xs font-semibold cursor-pointer transition-all focus-within:ring-3 focus-within:ring-accent-teal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Pilih Berkas KTP</span>
                                <input id="ktp-input" name="ktp_photo" type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg"
                                       @change="ktpPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                            <p class="text-[11px] text-muted dark:text-on-dark-soft pl-1">Format berkas: JPG, JPEG, PNG (Maks 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-hairline dark:border-white/5">
                    <a href="{{ route('dashboard') }}" class="h-11 flex items-center justify-center px-6 bg-surface-soft hover:bg-surface-strong text-ink dark:text-white dark:bg-white/5 dark:hover:bg-white/10 border border-hairline dark:border-white/10 rounded-pill text-xs font-semibold transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        Kembali ke Dashboard
                    </a>
                    <button type="submit" class="h-11 flex items-center justify-center px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill text-xs transition-all shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
