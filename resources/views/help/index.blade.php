@extends('layouts.private')

@section('title', 'Pusat Bantuan & Pengaduan — MPP Kota Sawahlunto')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-16">
    <!-- Header -->
    <div class="border-b border-hairline dark:border-white/10 pb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Pusat Bantuan & Pengaduan</h1>
        <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Kami siap membantu Anda. Sampaikan kendala Anda melalui form pengaduan atau hubungi kami langsung via WhatsApp.</p>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Form Pengaduan Warga (Spans 7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-xs">
                
                <h3 class="text-lg font-bold text-ink dark:text-white font-display mb-6">Formulir Pengaduan Warga</h3>

                <form action="{{ route('customer.help.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Subjek Kendala -->
                    <div class="space-y-2">
                        <label for="subject" class="block text-sm font-bold text-ink dark:text-white font-display">Subjek Kendala</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required 
                               placeholder="Contoh: Kode booking tidak bisa discan / data tidak sesuai"
                               class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all font-body">
                        @error('subject')
                            <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-bold text-ink dark:text-white font-display">Kategori Pengaduan</label>
                        <select id="category" name="category" required 
                                class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer font-body">
                            <option value="" disabled selected class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">-- Pilih Kategori --</option>
                            <option value="Pelayanan" {{ old('category') == 'Pelayanan' ? 'selected' : '' }} class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">Pelayanan Loket / Petugas</option>
                            <option value="Fasilitas" {{ old('category') == 'Fasilitas' ? 'selected' : '' }} class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">Fasilitas / Sarana Prasarana</option>
                            <option value="Sistem/Teknis" {{ old('category') == 'Sistem/Teknis' ? 'selected' : '' }} class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">Kendala Aplikasi / Sistem / Scan QR</option>
                            <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }} class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">Lainnya</option>
                        </select>
                        @error('category')
                            <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Isi Aduan -->
                    <div class="space-y-2">
                        <label for="content" class="block text-sm font-bold text-ink dark:text-white font-display">Detail Isi Aduan</label>
                        <textarea id="content" name="content" rows="6" required 
                                  placeholder="Jelaskan secara rinci kendala atau keluhan yang Anda hadapi..."
                                  class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all font-body">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full sm:w-auto h-11 inline-flex items-center justify-center gap-2 px-8 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Pengaduan
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <!-- RIGHT COLUMN: WhatsApp Chat Card (Spans 5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- WhatsApp Chat Card -->
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-xs overflow-hidden">
                <div class="bg-linear-to-r from-emerald-600 to-green-500 px-6 py-5 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center">
                            <!-- WhatsApp SVG Logo -->
                            <svg class="w-6 h-6 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.504-5.718-1.465L0 24zm6.26-4.521c1.616.958 3.197 1.488 4.792 1.489 5.54 0 10.05-4.505 10.05-10.056-.002-2.67-1.04-5.18-2.928-7.07C16.345 1.95 13.843.91 11.2.91 5.659.91 1.15 5.415 1.15 10.966c-.001 1.705.474 3.284 1.39 4.767l-1.01 3.693 3.777-.99zM16.57 14.86c-.29-.145-1.722-.85-1.99-.947-.267-.097-.463-.146-.658.146-.195.292-.756.947-.927 1.14-.17.195-.34.22-.63.074-2.92-1.458-4.805-4.307-5.466-5.447-.195-.337-.02-.519.146-.687.152-.15.34-.397.51-.595.17-.198.226-.34.34-.567.113-.227.057-.425-.028-.595-.085-.17-.658-1.587-.902-2.172-.237-.57-.497-.49-.658-.498-.16-.008-.34-.01-.513-.01-.173 0-.455.064-.693.302-.237.237-.906.885-.906 2.158 0 1.273.928 2.502 1.056 2.67.127.17 1.8 2.748 4.362 3.854.61.263 1.085.42 1.458.538.613.195 1.172.167 1.613.1.492-.074 1.722-.704 1.966-1.385.244-.68.244-1.264.17-1.385-.07-.12-.266-.193-.556-.34z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-base font-display">Layanan Chat WhatsApp</h4>
                            <p class="text-xs text-white/90 font-body">Respon Cepat & Interaktif</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <p class="text-xs text-body dark:text-on-dark-soft leading-relaxed font-body">
                        Apakah Anda membutuhkan bantuan darurat atau ingin berbicara langsung dengan tim customer service kami? Klik tombol di bawah ini untuk memulai chat WhatsApp resmi MPP Kota Sawahlunto.
                    </p>

                    <div class="bg-surface-soft dark:bg-white/2 rounded-md p-4 space-y-2 border border-hairline/40 dark:border-white/5">
                        <span class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Jam Layanan Chat:</span>
                        <p class="text-xs text-ink dark:text-white font-body font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary dark:text-accent-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Senin – Jumat | 08:00 – 15:30 WIB
                        </p>
                        <p class="text-[10px] text-muted dark:text-on-dark-soft font-body leading-normal">
                            Pertanyaan di luar jam operasional akan dijawab pada hari kerja berikutnya.
                        </p>
                    </div>

                    <!-- WhatsApp CTA Button -->
                    <div>
                        <a href="https://wa.me/628116600122?text=Halo%20Admin%20MPP%20Sawahlunto%20saya%20butuh%20bantuan%20mengenai%20layanan%20antrean..." 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="w-full h-11 inline-flex items-center justify-center gap-2 px-6 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-emerald-500/20 cursor-pointer text-sm">
                            <!-- Mini WhatsApp Icon -->
                            <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.515-.501-.669-.507-.148-.006-.32-.008-.49-.008-.17 0-.445.064-.678.297-.234.232-.893.874-.893 2.134 0 1.261.916 2.48 1.042 2.648.127.169 1.802 2.752 4.362 3.854.61.263 1.085.42 1.458.538.614.194 1.172.167 1.613.1.493-.072 1.757-.719 2.03-1.412.274-.693.274-1.287.193-1.41-.081-.124-.275-.172-.573-.321m-5.444 6.786C9.172 21.168 5.636 17.632 5.636 13.5c0-4.132 3.536-7.668 7.668-7.668 4.132 0 7.668 3.536 7.668 7.668 0 4.132-3.536 7.668-7.668 7.668M12 1.75C5.973 1.75.917 6.806.917 12.833c0 2.13.611 4.17 1.757 5.922L.958 24l5.378-1.408c1.696.953 3.619 1.45 5.664 1.458 6.027 0 11.083-5.056 11.083-11.083C23.083 6.806 18.027 1.75 12 1.75"/>
                            </svg>
                            Mulai Chat WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- General Help Card -->
            <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display border-b border-hairline dark:border-white/10 pb-2">Informasi Tambahan</h3>
                <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed">
                    Setiap pengaduan tertulis yang Anda kirimkan melalui formulir di sebelah kiri akan tercatat pada database lokal kami dan ditindaklanjuti secara serius oleh administrator MPP Kota Sawahlunto. Anda dapat memantau notifikasi dashboard Anda secara berkala untuk pembaruan status aduan Anda.
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
