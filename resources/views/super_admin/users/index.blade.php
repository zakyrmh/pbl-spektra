@extends('layouts.private')

@section('title', 'Manajemen Pengguna - MPP Kota Sawahlunto')

@section('content')
<div class="space-y-6 pb-10">

    {{-- ══════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola akun staf, instansi, dan hak akses sistem MPP Sawahlunto.</p>
        </div>
        <button
            type="button"
            id="btn-open-add-modal"
            onclick="openUserModal()"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-blue-500/20 transition-all duration-200 shrink-0"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Pengguna
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════ --}}
    @if(session('success'))
        <div id="flash-success" class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 text-sm font-medium shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
            <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-error" class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 text-sm font-medium shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
            <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400 hover:text-red-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Reset Password Flash (tampilkan password sementara) --}}
    @if(session('temp_password'))
        @php $tp = session('temp_password'); @endphp
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl px-5 py-4 shadow-sm">
            <div class="flex items-start gap-3 flex-1">
                <svg class="w-5 h-5 shrink-0 text-amber-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <div>
                    <p class="text-sm font-bold">Password sementara untuk <span class="text-amber-700">{{ $tp['user'] }}</span> berhasil direset!</p>
                    <p class="text-xs text-amber-700 mt-1">Sampaikan password di bawah ini kepada pengguna secara langsung. Password ini hanya ditampilkan sekali.</p>
                </div>
            </div>
            <div class="bg-white border border-amber-200 rounded-xl px-4 py-2 text-center shrink-0">
                <p class="text-[10px] text-amber-500 font-bold uppercase tracking-wider">Password Sementara</p>
                <p class="text-lg font-extrabold text-gray-900 font-mono tracking-widest" id="temp-password-display">{{ $tp['password'] }}</p>
                <button onclick="copyTempPassword()" class="text-[10px] text-blue-600 hover:underline font-semibold mt-0.5 flex items-center gap-1 mx-auto">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Salin
                </button>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         STAT CARDS / METRICS
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        {{-- Card: Total Pengguna --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pengguna</p>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-0.5 leading-none">{{ $totalUsers }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Seluruh akun di sistem</p>
            </div>
        </div>

        {{-- Card: Staf Online --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center gap-4">
            <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                </svg>
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full animate-ping opacity-75"></span>
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800"></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Staf Aktif (Online)</p>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-0.5 leading-none">{{ $activeStaff }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Login dalam 15 menit terakhir</p>
            </div>
        </div>

        {{-- Card: Total Instansi --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center gap-4">
            <div class="w-12 h-12 bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Instansi / Loket</p>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-0.5 leading-none">{{ $totalInstansi }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Entitas layanan terintegrasi</p>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         FILTER & SEARCH BAR
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" id="filter-form" class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- Pencarian --}}
                <div class="lg:col-span-2 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, email, atau NIK..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                    >
                </div>

                {{-- Filter Instansi --}}
                <div>
                    <select
                        name="instansi"
                        id="filter-instansi"
                        onchange="fetchUsers()"
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all appearance-none cursor-pointer"
                    >
                        <option value="">Semua Instansi</option>
                        @foreach(\App\Models\User::$instansiList as $key => $label)
                            <option value="{{ $key }}" {{ request('instansi') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Role --}}
                <div>
                    <select
                        name="role"
                        id="filter-role"
                        onchange="fetchUsers()"
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all appearance-none cursor-pointer"
                    >
                        <option value="">Semua Peran</option>
                        <option value="super_admin"  {{ request('role') === 'super_admin'  ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin_fo"     {{ request('role') === 'admin_fo'     ? 'selected' : '' }}>Admin Front Office</option>
                        <option value="admin_gerai"  {{ request('role') === 'admin_gerai'  ? 'selected' : '' }}>Operator Loket</option>
                        <option value="pengunjung"   {{ request('role') === 'pengunjung'   ? 'selected' : '' }}>Pengunjung</option>
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="flex gap-2">
                    <select
                        name="status"
                        id="filter-status"
                        onchange="fetchUsers()"
                        class="flex-1 px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all appearance-none cursor-pointer"
                    >
                        <option value="">Semua Status</option>
                        <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    {{-- Tombol reset filter --}}
                    <a href="{{ route('users.index') }}" id="btn-reset-filters" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors text-xs font-semibold" title="Reset Filter" style="display: {{ request()->hasAny(['search', 'instansi', 'role', 'status']) ? 'inline-flex' : 'none' }};">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>

            </div>

            {{-- Search Submit (terpicu saat tekan Enter) --}}
            <div class="mt-3 flex items-center justify-between">
                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Terapkan Pencarian
                </button>
                <div id="users-count-info" class="text-xs text-gray-400">
                    @if($users->total() > 0)
                        Menampilkan <strong class="text-gray-700 dark:text-gray-300">{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong> dari <strong class="text-gray-700 dark:text-gray-300">{{ $users->total() }}</strong> pengguna
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         TABEL PENGGUNA
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-sm md:overflow-visible overflow-hidden relative transition-all duration-200" id="users-table-container">
        @include('super_admin.users.table')
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════
     MODAL TAMBAH / EDIT PENGGUNA
════════════════════════════════════════════════════════════ --}}
<div
    id="user-modal"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden"
    aria-modal="true"
    role="dialog"
    aria-labelledby="modal-title"
>
    <div
        id="user-modal-card"
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-gray-700 transform scale-95 transition-all duration-200"
    >
        {{-- Modal Header --}}
        <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 z-10">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 dark:text-white" id="modal-title">Tambah Pengguna Baru</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" id="modal-subtitle">Isi formulir di bawah untuk membuat akun baru.</p>
            </div>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body: Form --}}
        <form id="user-form" method="POST" action="{{ route('users.store') }}" class="px-6 py-6 space-y-5" novalidate>
            @csrf
            <span id="form-method-spoofing"></span>

            {{-- Nama Lengkap --}}
            <div>
                <label for="f-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="f-name"
                    name="name"
                    required
                    autocomplete="name"
                    placeholder="Contoh: Budi Santoso, S.E."
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                >
            </div>

            {{-- Email --}}
            <div>
                <label for="f-email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="f-email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder="nama@instansi.go.id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                >
            </div>

            {{-- NIK & No Telp (grid) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="f-nik" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">NIK (16 digit)</label>
                    <input
                        type="text"
                        id="f-nik"
                        name="nik"
                        maxlength="16"
                        pattern="\d{16}"
                        placeholder="1372XXXXXXXXXXXX"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all font-mono"
                    >
                </div>
                <div>
                    <label for="f-no-telp" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Nomor Telepon</label>
                    <input
                        type="tel"
                        id="f-no-telp"
                        name="no_telp"
                        maxlength="15"
                        placeholder="08XXXXXXXXX"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                    >
                </div>
            </div>

            {{-- Role --}}
            <div>
                <label for="f-role" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Peran (Role) <span class="text-red-500">*</span>
                </label>
                <select
                    id="f-role"
                    name="role"
                    required
                    onchange="handleRoleChange(this.value)"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all appearance-none cursor-pointer"
                >
                    <option value="">— Pilih Peran —</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin_fo">Admin Front Office</option>
                    <option value="admin_gerai">Operator Loket</option>
                    <option value="pengunjung">Pengunjung</option>
                </select>
            </div>

            {{-- Instansi & Loket (muncul hanya saat role = admin_gerai) --}}
            <div id="field-instansi-loket" class="hidden space-y-4">
                <div class="p-4 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800/50 rounded-xl">
                    <p class="text-xs font-semibold text-violet-700 dark:text-violet-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Operator Loket wajib dipetakan ke instansi dan nomor loket spesifik.
                    </p>
                </div>
                <div>
                    <label for="f-instansi" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Instansi / Gerai <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="f-instansi"
                        name="instansi"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all appearance-none cursor-pointer"
                    >
                        <option value="">— Pilih Instansi —</option>
                        @foreach(\App\Models\User::$instansiList as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="f-nomor-loket" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Nomor Loket <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="f-nomor-loket"
                        name="nomor_loket"
                        maxlength="10"
                        placeholder="Contoh: L1, L2A, 03"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all font-mono"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div id="field-password">
                <label for="f-password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Password <span class="text-red-500" id="pw-required-marker">*</span>
                    <span class="text-[10px] text-gray-400 font-normal ml-1">(minimal 8 karakter)</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="f-password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="Masukkan password..."
                        class="w-full px-4 py-2.5 pr-24 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                    >
                    <button
                        type="button"
                        onclick="generatePassword()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-all"
                    >
                        Generate
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Klik <strong>Generate</strong> untuk membuat password acak yang aman.</p>
            </div>

            {{-- Validation errors dari Laravel --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 flex items-center gap-1.5">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            {{-- Tombol Submit --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-all">
                    Batal
                </button>
                <button type="submit" id="modal-submit-btn" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-blue-500/20 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span id="submit-label">Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
    // ── Live Search & Dynamic Filtering ─────────────────────────
    let debounceTimer;

    function fetchUsers(targetUrl = null) {
        const form = document.getElementById('filter-form');
        const container = document.getElementById('users-table-container');
        const countInfo = document.getElementById('users-count-info');
        const btnReset = document.getElementById('btn-reset-filters');
        
        // Show loading state (subtle micro-animation)
        container.classList.add('opacity-40');
        
        // Construct the URL
        let url;
        if (targetUrl) {
            url = new URL(targetUrl);
        } else {
            url = new URL(form.action || window.location.href);
            // Append inputs from form
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            }
        }
        
        // Set AJAX flag in query params so controller returns JSON
        url.searchParams.set('ajax', '1');
        
        // Toggle reset button visibility
        const hasFilters = url.searchParams.has('search') || url.searchParams.has('instansi') || url.searchParams.has('role') || url.searchParams.has('status');
        if (btnReset) {
            btnReset.style.display = hasFilters ? 'inline-flex' : 'none';
        }
        
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Update table container HTML
            container.innerHTML = data.html;
            
            // Update count info
            if (countInfo) {
                countInfo.innerHTML = data.info;
            }
            
            // Update browser URL (removing 'ajax' param for clean look)
            const cleanUrl = new URL(url.toString());
            cleanUrl.searchParams.delete('ajax');
            window.history.pushState({}, '', cleanUrl.toString());
        })
        .catch(error => {
            console.error('Error fetching users:', error);
        })
        .finally(() => {
            // Remove loading state
            container.classList.remove('opacity-40');
        });
    }

    // ── Alpine.js untuk dropdown aksi (diinisiasi manual jika Alpine belum ter-load) ──
    // Jika proyek tidak menggunakan Alpine.js, gunakan fallback vanilla JS di bawah
    document.addEventListener('DOMContentLoaded', function () {
        // Fallback untuk dropdown jika tidak ada Alpine.js (menggunakan event delegation)
        if (typeof window.Alpine === 'undefined') {
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[id^="action-btn-"]');
                if (btn) {
                    e.stopPropagation();
                    const parent = btn.closest('[x-data]');
                    const dropdown = parent ? parent.querySelector('[x-show]') : null;
                    if (dropdown) {
                        const isOpen = dropdown.style.display === 'block';
                        // Tutup semua dropdown lain
                        document.querySelectorAll('[x-show]').forEach(d => { d.style.display = 'none'; });
                        dropdown.style.display = isOpen ? 'none' : 'block';
                    }
                } else {
                    document.querySelectorAll('[x-show]').forEach(d => { d.style.display = 'none'; });
                }
            });
        }

        // Debounce search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchUsers();
                }, 300);
            });
        }

        // Intercept form submit
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchUsers();
            });
        }

        // Intercept pagination clicks (delegated)
        const tableContainer = document.getElementById('users-table-container');
        if (tableContainer) {
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    if (link.href.includes('page=') || link.href.includes('search=') || link.href.includes('role=') || link.href.includes('instansi=') || link.href.includes('status=')) {
                        e.preventDefault();
                        fetchUsers(link.href);
                    }
                }
            });
        }

        // Intercept reset filters click
        const btnReset = document.getElementById('btn-reset-filters');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('filter-form');
                if (form) {
                    form.reset();
                    document.getElementById('filter-instansi').value = '';
                    document.getElementById('filter-role').value = '';
                    document.getElementById('filter-status').value = '';
                    document.getElementById('search-input').value = '';
                }
                fetchUsers();
            });
        }

        // Auto-buka modal jika ada error validasi
        @if($errors->any())
            openUserModal();
        @endif
    });

    // ── Modal Management ──────────────────────────────────────
    let isEditMode = false;
    let editUserId = null;

    function openUserModal() {
        isEditMode = false;
        editUserId = null;
        resetForm();

        document.getElementById('modal-title').textContent    = 'Tambah Pengguna Baru';
        document.getElementById('modal-subtitle').textContent = 'Isi formulir di bawah untuk membuat akun baru.';
        document.getElementById('submit-label').textContent   = 'Simpan Pengguna';
        document.getElementById('user-form').action           = '{{ route("users.store") }}';
        document.getElementById('form-method-spoofing').innerHTML = '';
        document.getElementById('field-password').classList.remove('hidden');
        document.getElementById('f-password').required = true;
        document.getElementById('pw-required-marker').style.display = '';

        showModal();
    }

    function openEditModal(userId, userData) {
        isEditMode = true;
        editUserId = userId;
        resetForm();

        document.getElementById('modal-title').textContent    = 'Edit Pengguna';
        document.getElementById('modal-subtitle').textContent = 'Perbarui informasi akun pengguna.';
        document.getElementById('submit-label').textContent   = 'Simpan Perubahan';
        document.getElementById('user-form').action           = `/manajemen-pengguna/${userId}`;
        document.getElementById('form-method-spoofing').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Field password opsional saat edit
        document.getElementById('field-password').classList.add('hidden');
        document.getElementById('f-password').required = false;
        document.getElementById('pw-required-marker').style.display = 'none';

        // Populate fields
        document.getElementById('f-name').value    = userData.name    || '';
        document.getElementById('f-email').value   = userData.email   || '';
        document.getElementById('f-nik').value     = userData.nik     || '';
        document.getElementById('f-no-telp').value = userData.no_telp || '';
        document.getElementById('f-role').value    = userData.role    || '';

        handleRoleChange(userData.role, userData.instansi, userData.nomor_loket);

        showModal();
    }

    function showModal() {
        const overlay = document.getElementById('user-modal');
        const card    = document.getElementById('user-modal-card');
        overlay.classList.remove('hidden');
        setTimeout(() => {
            overlay.style.opacity = '1';
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 20);
        document.body.style.overflow = 'hidden';
    }

    function closeUserModal() {
        const overlay = document.getElementById('user-modal');
        const card    = document.getElementById('user-modal-card');
        overlay.style.opacity = '0';
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    // Tutup modal saat klik overlay
    document.getElementById('user-modal').addEventListener('click', function (e) {
        if (e.target === this) closeUserModal();
    });

    // Tutup modal dengan ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeUserModal();
    });

    // ── Role Change Handler ───────────────────────────────────
    function handleRoleChange(role, instansiVal = null, loketVal = null) {
        const fieldInstansiLoket = document.getElementById('field-instansi-loket');
        const fInstansi          = document.getElementById('f-instansi');
        const fLoket             = document.getElementById('f-nomor-loket');

        if (role === 'admin_gerai') {
            fieldInstansiLoket.classList.remove('hidden');
            fInstansi.required = true;
            fLoket.required    = true;
        } else {
            fieldInstansiLoket.classList.add('hidden');
            fInstansi.required = false;
            fLoket.required    = false;
        }

        if (instansiVal) fInstansi.value = instansiVal;
        if (loketVal)    fLoket.value    = loketVal;
    }

    // ── Reset Form ────────────────────────────────────────────
    function resetForm() {
        document.getElementById('user-form').reset();
        document.getElementById('field-instansi-loket').classList.add('hidden');
        document.getElementById('f-instansi').required = false;
        document.getElementById('f-nomor-loket').required = false;
    }

    // ── Generate Password ─────────────────────────────────────
    function generatePassword() {
        const chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$!';
        let password   = '';
        const length   = 12;
        const array    = new Uint32Array(length);
        window.crypto.getRandomValues(array);
        for (let i = 0; i < length; i++) {
            password += chars[array[i] % chars.length];
        }
        const pwField = document.getElementById('f-password');
        pwField.type  = 'text';
        pwField.value = password;
        setTimeout(() => { pwField.type = 'password'; }, 3000);
        pwField.focus();
    }

    // ── Copy Temp Password ────────────────────────────────────
    function copyTempPassword() {
        const text = document.getElementById('temp-password-display').textContent;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target.closest('button');
            const original = btn.innerHTML;
            btn.innerHTML = '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Tersalin!';
            btn.classList.add('text-emerald-600');
            setTimeout(() => { btn.innerHTML = original; btn.classList.remove('text-emerald-600'); }, 2000);
        });
    }
</script>
@endpush

@endsection
