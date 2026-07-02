@extends('layouts.private')

@section('title', 'Pengaturan Sistem — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-16">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-bold text-primary dark:text-accent-teal uppercase tracking-widest font-display">Pusat Konfigurasi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Pengaturan Sistem</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Kelola identitas aplikasi, running text pada layar monitor, serta konfigurasi real-time WebSocket.</p>
            </div>
        </div>



        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Memperbarui Pengaturan</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Configuration Form --}}
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- 1. Pengaturan Umum --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Pengaturan Umum & Identitas
                    </h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Atur identitas resmi aplikasi dan status operasional sistem.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="app_name" class="block text-sm font-bold text-ink dark:text-white font-display">Nama Aplikasi / Instansi</label>
                        <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? '') }}" required
                               class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Nama resmi yang digunakan pada header dan logo navigasi.</span>
                    </div>

                    <div class="space-y-2">
                        <label for="app_logo" class="block text-sm font-bold text-ink dark:text-white font-display">Path Logo Aplikasi</label>
                        <input type="text" id="app_logo" name="app_logo" value="{{ old('app_logo', $settings['app_logo'] ?? '') }}" required
                               class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                        <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Lokasi direktori aset berkas gambar logo aplikasi.</span>
                    </div>
                </div>

                <div class="space-y-2 max-w-md">
                    <label for="maintenance_mode" class="block text-sm font-bold text-ink dark:text-white font-display">Status Operasional (Mode Pemeliharaan)</label>
                    <select id="maintenance_mode" name="maintenance_mode" required
                            class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                        <option value="0" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' }}>Aktif (Sistem Berjalan Normal)</option>
                        <option value="1" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' }}>Mode Pemeliharaan (Maintenance Mode)</option>
                    </select>
                    <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Jika diaktifkan, warga tidak dapat melakukan booking baru di portal publik.</span>
                </div>
            </div>

            {{-- 2. Teks Berjalan & Monitor --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4" />
                        </svg>
                        Layar Monitor & Marquee Text (REQ-6.3)
                    </h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Atur tampilan teks berjalan pada monitor display antrean publik `/display`.</p>
                </div>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="marquee_text" class="block text-sm font-bold text-ink dark:text-white font-display">Teks Berjalan Monitor</label>
                        <textarea id="marquee_text" name="marquee_text" rows="3" required
                                  placeholder="Contoh: Selamat Datang di Mal Pelayanan Publik..."
                                  class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">{{ old('marquee_text', $settings['marquee_text'] ?? '') }}</textarea>
                        <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Teks pengumuman/imbauan yang akan terus berjalan di bagian bawah monitor.</span>
                    </div>

                    <div class="space-y-2 max-w-md">
                        <label for="marquee_active" class="block text-sm font-bold text-ink dark:text-white font-display">Status Running Text</label>
                        <select id="marquee_active" name="marquee_active" required
                                class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                            <option value="1" {{ old('marquee_active', $settings['marquee_active'] ?? '1') === '1' ? 'selected' : '' }}>Tampilkan Teks Berjalan</option>
                            <option value="0" {{ old('marquee_active', $settings['marquee_active'] ?? '1') === '0' ? 'selected' : '' }}>Sembunyikan Teks Berjalan</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. WebSocket & Broadcast --}}
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-white font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary dark:text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        WebSocket & Integrasi Real-Time
                    </h3>
                    <p class="text-xs text-muted dark:text-on-dark-soft mt-1 font-body">Hubungkan aplikasi dengan server WebSocket (Laravel Reverb / Pusher) untuk update antrean real-time tanpa reload.</p>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2 max-w-md">
                        <label for="websocket_enabled" class="block text-sm font-bold text-ink dark:text-white font-display">Aktifkan Layanan WebSocket</label>
                        <select id="websocket_enabled" name="websocket_enabled" required
                                class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                            <option value="1" {{ old('websocket_enabled', $settings['websocket_enabled'] ?? '1') === '1' ? 'selected' : '' }}>Aktifkan Koneksi Real-Time</option>
                            <option value="0" {{ old('websocket_enabled', $settings['websocket_enabled'] ?? '1') === '0' ? 'selected' : '' }}>Nonaktifkan (Gunakan Fallback Polling)</option>
                        </select>
                        <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Bila dinonaktifkan, aplikasi akan menggunakan AJAX polling untuk memperbarui monitor.</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                        <div class="space-y-2">
                            <label for="reverb_host" class="block text-sm font-bold text-ink dark:text-white font-display">WebSocket Host</label>
                            <input type="text" id="reverb_host" name="reverb_host" value="{{ old('reverb_host', $settings['reverb_host'] ?? '') }}" required
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                            <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Host server (contoh: 127.0.0.1 atau domain).</span>
                        </div>

                        <div class="space-y-2">
                            <label for="reverb_port" class="block text-sm font-bold text-ink dark:text-white font-display">WebSocket Port</label>
                            <input type="number" id="reverb_port" name="reverb_port" value="{{ old('reverb_port', $settings['reverb_port'] ?? '') }}" required min="1" max="65535"
                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                            <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Port WebSocket (default Reverb: 8080).</span>
                        </div>

                        <div class="space-y-2">
                            <label for="reverb_scheme" class="block text-sm font-bold text-ink dark:text-white font-display">WebSocket Scheme</label>
                            <select id="reverb_scheme" name="reverb_scheme" required
                                    class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                <option value="http" {{ old('reverb_scheme', $settings['reverb_scheme'] ?? 'http') === 'http' ? 'selected' : '' }}>HTTP (Tidak Terenkripsi)</option>
                                <option value="https" {{ old('reverb_scheme', $settings['reverb_scheme'] ?? 'http') === 'https' ? 'selected' : '' }}>HTTPS (SSL Secure)</option>
                            </select>
                            <span class="text-[11px] text-muted dark:text-on-dark-soft block font-body">Gunakan HTTPS untuk koneksi SSL yang aman di lingkungan produksi.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="h-11 px-8 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
