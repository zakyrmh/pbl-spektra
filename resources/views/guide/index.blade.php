@extends('layouts.private')

@section('title', 'Panduan Layanan & Persyaratan MPP — Kota Sawahlunto')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-16">
    <!-- Header -->
    <div class="border-b border-hairline dark:border-white/10 pb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Panduan Layanan & Persyaratan</h1>
        <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Cari syarat dokumen dan informasi sebelum melakukan kunjungan atau check-in di gerai MPP Kota Sawahlunto.</p>
    </div>

    <!-- Accordion & Search container -->
    <div x-data="{ 
        activeAccordion: null,
        searchQuery: '',
        instansi: [
            {
                id: 'disdukcapil',
                name: 'Disdukcapil (Dinas Kependudukan dan Pencatatan Sipil)',
                inisial: 'DDK',
                description: 'Melayani urusan administrasi kependudukan dan pencatatan sipil warga.',
                services: [
                    {
                        name: 'Perekaman & Pencetakan KTP-el',
                        requirements: [
                            'Telah berusia minimal 17 tahun atau sudah menikah.',
                            'Membawa Fotokopi Kartu Keluarga (KK) terbaru.',
                            'Membawa KTP-el lama yang rusak, atau Surat Keterangan Kehilangan dari Kepolisian jika KTP hilang.'
                        ]
                    },
                    {
                        name: 'Pembuatan & Perubahan Kartu Keluarga (KK)',
                        requirements: [
                            'Membawa Kartu Keluarga (KK) asli yang lama.',
                            'Fotokopi Buku Nikah / Akta Perkawinan / Akta Cerai (jika ada perubahan status).',
                            'Surat Keterangan Pindah Datang (SKPWNI) bagi warga yang pindah domisili.'
                        ]
                    },
                    {
                        name: 'Pencatatan Akta Kelahiran Baru',
                        requirements: [
                            'Surat Keterangan Lahir asli dari Bidan, Dokter, atau Rumah Sakit.',
                            'Buku Nikah / Akta Perkawinan orang tua (fotokopi terlegalisir).',
                            'Fotokopi KTP-el orang tua & fotokopi KTP-el 2 orang saksi.',
                            'Fotokopi Kartu Keluarga (KK) sebagai tempat pendaftaran nama anak.'
                        ]
                    }
                ]
            },
            {
                id: 'bank-nagari',
                name: 'Bank Nagari (Bank Pembangunan Daerah Sumatera Barat)',
                inisial: 'BNR',
                description: 'Layanan transaksi keuangan daerah, tabungan, kredit, dan perbankan lainnya.',
                services: [
                    {
                        name: 'Pembukaan Rekening Tabungan Baru (Sikoci / Simpeda)',
                        requirements: [
                            'Membawa KTP-el asli pemilik rekening yang masih berlaku.',
                            'Fotokopi NPWP (jika ada).',
                            'Mengisi formulir permohonan pembukaan rekening bank.',
                            'Setoran awal minimal sesuai ketentuan produk (Sikoci: Rp 100.000 / Simpeda: Rp 50.000).'
                        ]
                    },
                    {
                        name: 'Pengajuan Kredit Usaha Rakyat (KUR)',
                        requirements: [
                            'Fotokopi KTP-el pemohon dan KTP-el suami/istri (jika sudah menikah).',
                            'Fotokopi Kartu Keluarga (KK) & Buku Nikah / Surat Cerai.',
                            'Surat Keterangan Usaha (SKU) resmi dari Kelurahan / Kecamatan setempat.',
                            'Fotokopi dokumen jaminan berupa sertifikat tanah (SHM) atau BPKB kendaraan.'
                        ]
                    },
                    {
                        name: 'Ganti Kartu ATM / Buku Tabungan Rusak',
                        requirements: [
                            'Membawa kartu ATM / Buku Tabungan yang rusak (jika hilang, wajib melampirkan Surat Kehilangan dari Kepolisian).',
                            'Membawa KTP-el asli pemilik rekening.',
                            'Dikenakan biaya administrasi kartu/buku baru sesuai dengan tarif ketentuan bank.'
                        ]
                    }
                ]
            }
        ],
        matchesQuery(item) {
            if (!this.searchQuery) return true;
            const query = this.searchQuery.toLowerCase();
            const inName = item.name.toLowerCase().includes(query);
            const inInisial = item.inisial.toLowerCase().includes(query);
            const inDesc = item.description.toLowerCase().includes(query);
            const inServices = item.services.some(s => s.name.toLowerCase().includes(query));
            return inName || inInisial || inDesc || inServices;
        }
    }" class="space-y-6">

        <!-- Search input with icon -->
        <div class="relative max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                <svg class="w-5 h-5 text-muted-soft dark:text-on-dark-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari instansi atau nama layanan..." 
                   class="w-full h-11 pl-10 pr-4 bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md text-sm focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all font-body">
        </div>

        <!-- Accordion List -->
        <div class="space-y-4">
            <template x-for="(item, index) in instansi" :key="item.id">
                <div x-show="matchesQuery(item)"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="border border-hairline dark:border-white/10 rounded-lg overflow-hidden bg-canvas dark:bg-surface-dark-elevated shadow-xs hover:shadow-sm transition-all duration-300">
                    
                    <!-- Accordion Trigger Button -->
                    <button type="button" 
                            @click="activeAccordion = (activeAccordion === index ? null : index)"
                            class="w-full py-4 px-6 flex justify-between items-center text-left font-bold text-ink dark:text-white font-display text-base transition-colors hover:bg-surface-soft dark:hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-teal cursor-pointer">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded bg-primary/10 dark:bg-accent-teal/15 text-xs text-primary dark:text-accent-teal font-mono font-black" x-text="item.inisial"></span>
                            <span x-text="item.name" class="font-display font-bold"></span>
                        </div>
                        <svg class="w-5 h-5 text-muted transition-transform duration-300 shrink-0" 
                             :class="activeAccordion === index ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Accordion Collapsible Content -->
                    <div x-show="activeAccordion === index" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="px-6 pb-6 border-t border-hairline-soft dark:border-white/5 pt-4 space-y-5"
                         x-cloak>
                        
                        <p class="text-xs text-muted dark:text-on-dark-soft italic font-body" x-text="item.description"></p>

                        <!-- Services & Requirements Sub-sections -->
                        <div class="space-y-4">
                            <template x-for="service in item.services" :key="service.name">
                                <div class="bg-surface-soft dark:bg-white/2 rounded-md p-4 space-y-2 border border-hairline/40 dark:border-white/5">
                                    <h4 class="text-sm font-bold text-primary dark:text-accent-teal font-display" x-text="service.name"></h4>
                                    
                                    <span class="block text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider font-display">Persyaratan Dokumen:</span>
                                    <ul class="list-disc list-inside space-y-1 pl-1">
                                        <template x-for="req in service.requirements" :key="req">
                                            <li class="text-xs text-body dark:text-on-dark-soft font-body leading-relaxed pl-1" x-text="req"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty state when no search matches -->
        <div x-show="instansi.filter(item => matchesQuery(item)).length === 0" 
             class="text-center py-12 bg-canvas dark:bg-surface-dark-elevated border border-hairline dark:border-white/10 rounded-lg space-y-3">
            <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-muted rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-ink dark:text-white font-display">Instansi atau Layanan Tidak Ditemukan</p>
            <p class="text-xs text-muted dark:text-on-dark-soft font-body max-w-xs mx-auto">Silakan gunakan kata kunci lain (misalnya: 'KTP', 'ATM', 'KUR', dsb).</p>
        </div>

    </div>
</div>
@endsection
