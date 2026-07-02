@extends('layouts.private')

@section('title', 'Penerbitan Karcis Walk-In — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16" 
         x-data="{
             departments: @js($departments),
             selectedDepartmentId: '{{ old('department_id', '') }}',
             
             get selectedDepartment() {
                 return this.departments.find(d => d.id == this.selectedDepartmentId) || null;
             }
         }">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6 print:hidden">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                    </span>
                    <span class="text-[11px] font-bold text-primary dark:text-accent-teal uppercase tracking-widest font-display">Stasiun Walk-In</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Penerbitan Karcis Walk-In</h1>
                <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-0.5">Daftarkan pengunjung mandiri di lokasi dan terbitkan nomor antrean fisik.</p>
            </div>
        </div>



        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg print:hidden" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Memproses Tiket</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Main Result / Receipt Karcis (Tampil Setelah Sukses Diterbitkan) --}}
        @if (session('ticket'))
            @php
                $ticket = session('ticket');
                $ticketCreatedAt = data_get($ticket, 'created_at') ?? now();
                if (is_string($ticketCreatedAt)) {
                    $ticketCreatedAt = \Carbon\Carbon::parse($ticketCreatedAt);
                }
            @endphp
            <div class="bg-canvas dark:bg-surface-dark-elevated rounded-xl border-2 border-status-serving/45 shadow-lg overflow-hidden max-w-md mx-auto" id="ticket-receipt-card">
                
                <div class="flex items-center justify-between px-5 py-3.5 bg-status-serving/10 border-b border-status-serving/20 print:hidden">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-status-serving" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold text-status-serving font-display">Karcis Diterbitkan</span>
                    </div>
                    <button onclick="window.print()" class="h-9 px-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-xs transition-all cursor-pointer flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Karcis
                    </button>
                </div>

                {{-- Receipt Thermal Content --}}
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="text-center space-y-2 pb-6 border-b border-dashed border-hairline dark:border-white/15">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <img src="{{ asset('images/Logo Kota Sawahlunto.webp') }}" alt="Sawahlunto Logo" class="h-8 object-contain">
                            <div class="text-left">
                                <h4 class="text-[10px] font-bold uppercase tracking-wider font-display text-muted">Mal Pelayanan Publik</h4>
                                <h5 class="text-xs font-extrabold uppercase tracking-tight font-display text-ink dark:text-white">Kota Sawahlunto</h5>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">NOMOR ANTREAN</span>
                        <div class="text-5xl sm:text-6xl font-extrabold text-primary dark:text-accent-teal tracking-tight font-mono">
                            {{ data_get($ticket, 'queue_number') }}
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50 print:hidden mt-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Waiting (Menunggu)
                        </span>
                    </div>

                    <div class="space-y-3.5 text-sm font-body">
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Nama Pengunjung</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ data_get($ticket, 'user.name') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">NIK</span>
                            <span class="font-bold text-ink dark:text-white font-mono text-right">{{ data_get($ticket, 'user.nik') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Instansi</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ data_get($ticket, 'department.name') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Loket Pelayanan</span>
                            <span class="font-bold text-primary dark:text-accent-teal text-right">Loket {{ data_get($ticket, 'department.nomor_loket') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Keperluan</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ data_get($ticket, 'purpose') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Waktu Cetak</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $ticketCreatedAt->translatedFormat('d M Y · H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Section (Hidden in print) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start print:hidden">
            
            {{-- Form Column --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
                    <form action="{{ route('admin.fo.ticket.store') }}" method="POST" class="space-y-6" autocomplete="off">
                        @csrf

                        {{-- Visitor Details Section --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-ink dark:text-white uppercase tracking-wider font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Identitas Pengunjung Walk-In
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="nik" class="block text-sm font-bold text-ink dark:text-white font-display">NIK Warga</label>
                                    <div class="flex gap-2">
                                        <div class="relative grow">
                                            <input type="text" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" required
                                                   placeholder="Contoh: 1373021408990002"
                                                   class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                                        </div>
                                        <button type="button" id="btn-cari-nik"
                                                class="px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-md transition-all cursor-pointer flex items-center justify-center gap-1.5 h-11 border border-primary/10 shadow-sm shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <span id="btn-cari-text">Cari</span>
                                        </button>
                                        <button type="button" id="btn-reset-nik"
                                                class="hidden px-4 bg-status-skipped/10 hover:bg-status-skipped/25 text-status-skipped text-xs font-bold rounded-md transition-all cursor-pointer flex items-center justify-center gap-1.5 h-11 border border-status-skipped/20 shadow-sm shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Reset</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-bold text-ink dark:text-white font-display">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="Contoh: Ahmad Hidayat"
                                           class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="no_telp" class="block text-sm font-bold text-ink dark:text-white font-display">Nomor Telepon / WhatsApp</label>
                                <input type="text" id="no_telp" name="phone" value="{{ old('phone') }}" required
                                       placeholder="Contoh: 081234567890"
                                       class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label for="purpose" class="block text-sm font-bold text-ink dark:text-white font-display">Keperluan Kunjungan / Pengurusan</label>
                                <textarea id="purpose" name="purpose" rows="3" required
                                          placeholder="Tulis alasan kedatangan warga secara rinci..."
                                          class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-3 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">{{ old('purpose') }}</textarea>
                            </div>
                        </div>

                        {{-- Service Destination Section --}}
                        <div class="space-y-4 pt-4 border-t border-hairline dark:border-white/10">
                            <h3 class="text-sm font-bold text-ink dark:text-white uppercase tracking-wider font-display border-b border-hairline dark:border-white/10 pb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Destinasi Pelayanan & Gerai
                            </h3>
                            
                            {{-- Department Select --}}
                            <div class="space-y-2">
                                <label for="department_id" class="block text-sm font-bold text-ink dark:text-white font-display">Instansi / Lembaga</label>
                                <div class="relative">
                                    <select id="department_id" name="department_id" x-model="selectedDepartmentId" required
                                            class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                        <option value="" disabled>-- Pilih Instansi / Lembaga --</option>
                                        <template x-for="dept in departments" :key="dept.id">
                                            <option :value="dept.id" x-text="dept.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4 border-t border-hairline dark:border-white/10 flex justify-end">
                            <button type="submit"
                                    class="h-11 px-8 bg-primary hover:bg-primary-hover text-white font-bold rounded-pill text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Simpan & Terbitkan Antrean
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Draft Preview Column --}}
            <div class="space-y-6">
                <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden"
                     :class="selectedDepartmentId ? 'border-primary dark:border-accent-teal shadow-md' : ''">
                    <div class="bg-linear-to-r from-primary to-primary-hover px-5 py-3 text-white font-display text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>Preview Draf Karcis</span>
                        <span x-show="selectedDepartmentId" class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                    </div>
                    
                    <div class="p-6 space-y-4 text-sm font-body">
                        <template x-if="!selectedDepartmentId">
                            <div class="text-center py-8 space-y-3">
                                <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-muted rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed max-w-[180px] mx-auto">Lengkapi destinasi gerai untuk melihat draf karcis.</p>
                            </div>
                        </template>
 
                        <template x-if="selectedDepartmentId">
                            <div class="space-y-4">
                                <div>
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Instansi Tujuan</span>
                                    <span class="font-bold text-ink dark:text-white" x-text="selectedDepartment ? selectedDepartment.name : ''"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Nomor Loket</span>
                                    <span class="font-bold text-ink dark:text-white" x-text="selectedDepartment ? 'Loket ' + selectedDepartment.nomor_loket : ''"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Guidance Card --}}
                <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-3">
                    <h4 class="text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display border-b border-hairline dark:border-white/10 pb-2">Ketentuan Karcis Walk-In</h4>
                    <ul class="space-y-2 text-xs text-muted dark:text-on-dark-soft font-body list-disc list-inside">
                        <li>Diterbitkan hanya untuk warga yang datang langsung.</li>
                        <li>Format nomor urut disesuaikan dengan kode inisial instansi.</li>
                        <li>Pastikan printer thermal aktif dan terkoneksi ke komputer FO.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Thermal Ticket Print Styles --}}
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            /* Hide private layout elements like sidebar, header, forms */
            aside, header, nav, main > div > div:not(#ticket-receipt-card), #ticket-receipt-card > div:first-child, form, button, hr {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
            }
            .min-h-screen, .h-screen {
                height: auto !important;
                min-height: 0 !important;
                padding-left: 0 !important;
            }
            #ticket-receipt-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                max-width: 300px !important; /* Standard 80mm printer size width */
                text-align: center !important;
            }
            #ticket-receipt-card * {
                color: black !important;
            }
        }
    </style>
    <!-- Notification Toast Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toast Alert
            function createToast(title, message, type = 'success') {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const toast = document.createElement('div');
                let borderClr = 'border-green-500';
                let bgClr = 'bg-white dark:bg-gray-800';
                let iconHtml = '';

                if (type === 'success') {
                    borderClr = 'border-l-4 border-green-500';
                    iconHtml = `<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                } else if (type === 'warning') {
                    borderClr = 'border-l-4 border-amber-500';
                    iconHtml = `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
                } else {
                    borderClr = 'border-l-4 border-blue-500';
                    iconHtml = `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                }

                toast.className = `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
                toast.innerHTML = `
                    <div class="shrink-0">${iconHtml}</div>
                    <div class="flex-grow">
                        <h5 class="text-xs font-bold text-ink dark:text-white font-display">${title}</h5>
                        <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                `;

                container.appendChild(toast);
                
                // Trigger reflow & animate in
                setTimeout(() => {
                    if (toast.isConnected) {
                        toast.classList.remove('translate-y-2', 'opacity-0');
                    }
                }, 50);

                // Auto remove after 4s
                setTimeout(() => {
                    if (toast.isConnected) {
                        toast.classList.add('opacity-0', 'translate-y-[-10px]');
                        setTimeout(() => {
                            if (toast.isConnected) {
                                toast.remove();
                            }
                        }, 300);
                    }
                }, 4000);
            }

            // Auto format NIK input: only numbers, max 16 digits
            const nikField = document.getElementById('nik');
            if (nikField) {
                nikField.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 16);
                });
            }

            // Auto format phone input: only numbers, max 15 digits
            const phoneField = document.getElementById('no_telp');
            if (phoneField) {
                phoneField.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 15);
                });
            }

            // NIK lookup using Fetch API
            const btnCari = document.getElementById('btn-cari-nik');
            const btnReset = document.getElementById('btn-reset-nik');
            const nameField = document.getElementById('name');
            const cariText = document.getElementById('btn-cari-text');

            if (btnCari && nikField && nameField && phoneField) {
                btnCari.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const nikValue = nikField.value.trim();

                    if (nikValue.length !== 16) {
                        createToast('Format Salah', 'NIK harus terdiri dari 16 digit.', 'warning');
                        return;
                    }

                    // Set loading state
                    btnCari.disabled = true;
                    if (cariText) cariText.innerText = 'Mencari...';

                    try {
                        const response = await fetch(`/api/fo/visitors/check-nik?nik=${nikValue}`);
                        
                        if (response.ok) {
                            const result = await response.json();
                            const visitor = result.data;

                            if (visitor && visitor.is_found) {
                                // Populate fields
                                nameField.value = visitor.name || '';
                                phoneField.value = visitor.no_telp || '';

                                // Set read-only and styles
                                nameField.readOnly = true;
                                phoneField.readOnly = true;
                                nameField.classList.add('bg-gray-100', 'dark:bg-white/10', 'opacity-75');
                                phoneField.classList.add('bg-gray-100', 'dark:bg-white/10', 'opacity-75');

                                // Toggle buttons
                                btnCari.classList.add('hidden');
                                if (btnReset) btnReset.classList.remove('hidden');

                                createToast('Warga Ditemukan', `Data warga ${visitor.name} berhasil dimuat secara otomatis.`, 'success');
                            } else {
                                handleNotFound();
                            }
                        } else {
                            // Status 404 or others
                            const errData = await response.json().catch(() => ({}));
                            const msg = errData.message || 'Warga baru, silakan isi data manual.';
                            handleNotFound(msg);
                        }
                    } catch (error) {
                        console.error('Lookup NIK error:', error);
                        createToast('Kesalahan Koneksi', 'Gagal menghubungi server untuk verifikasi NIK.', 'warning');
                        btnCari.disabled = false;
                        if (cariText) cariText.innerText = 'Cari';
                    }
                });
            }

            function handleNotFound(msg = 'Warga baru, silakan isi data manual.') {
                // Ensure editable and clean loading state
                nameField.readOnly = false;
                phoneField.readOnly = false;
                nameField.classList.remove('bg-gray-100', 'dark:bg-white/10', 'opacity-75');
                phoneField.classList.remove('bg-gray-100', 'dark:bg-white/10', 'opacity-75');

                btnCari.disabled = false;
                if (cariText) cariText.innerText = 'Cari';
                
                // Keep buttons in search state
                btnCari.classList.remove('hidden');
                if (btnReset) btnReset.classList.add('hidden');

                createToast('Warga Baru', msg, 'info');
                nameField.focus();
            }

            if (btnReset && nikField && nameField && phoneField) {
                btnReset.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Clear inputs
                    nikField.value = '';
                    nameField.value = '';
                    phoneField.value = '';

                    // Unlock fields
                    nameField.readOnly = false;
                    phoneField.readOnly = false;
                    nameField.classList.remove('bg-gray-100', 'dark:bg-white/10', 'opacity-75');
                    phoneField.classList.remove('bg-gray-100', 'dark:bg-white/10', 'opacity-75');

                    // Toggle buttons
                    btnCari.disabled = false;
                    if (cariText) cariText.innerText = 'Cari';
                    btnCari.classList.remove('hidden');
                    btnReset.classList.add('hidden');

                    createToast('Formulir Direset', 'Semua kolom identitas telah dibersihkan.', 'info');
                    nikField.focus();
                });
            }
        });
    </script>
@endpush
