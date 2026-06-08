@extends('layouts.private')

@section('title', 'Penerbitan Karcis Walk-In — MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-16" 
         x-data="{
             departments: @js($departments),
             selectedDepartmentId: '{{ old('department_id', '') }}',
             selectedServiceId: '{{ old('service_id', '') }}',
             selectedCounterId: '{{ old('counter_id', '') }}',
             
             get filteredServices() {
                 if (!this.selectedDepartmentId) return [];
                 let dept = this.departments.find(d => d.id == this.selectedDepartmentId);
                 return dept ? dept.services : [];
             },
             
             get filteredCounters() {
                 if (!this.selectedDepartmentId) return [];
                 let dept = this.departments.find(d => d.id == this.selectedDepartmentId);
                 return dept ? dept.counters : [];
             },
             
             get selectedDepartment() {
                 return this.departments.find(d => d.id == this.selectedDepartmentId) || null;
             },
             
             get selectedService() {
                 let services = this.filteredServices;
                 return services.find(s => s.id == this.selectedServiceId) || null;
             },

             get selectedCounter() {
                 let counters = this.filteredCounters;
                 return counters.find(c => c.id == this.selectedCounterId) || null;
             },
             
             resetService() {
                 this.selectedServiceId = '';
                 this.resetCounter();
             },
             
             resetCounter() {
                 this.selectedCounterId = '';
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

        {{-- Alerts --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-status-serving/10 border border-status-serving/30 rounded-lg print:hidden animate-pulse" role="alert">
                <svg class="w-5 h-5 text-status-serving shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-serving font-display">Penerbitan Berhasil</p>
                    <p class="text-sm text-green-800 dark:text-green-300 font-body mt-0.5">{!! session('success') !!}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()" class="shrink-0 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

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
            @php $ticket = session('ticket'); @endphp
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
                            {{ $ticket->queue_number }}
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold border border-green-200/50 print:hidden mt-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Waiting (Menunggu)
                        </span>
                    </div>

                    <div class="space-y-3.5 text-sm font-body">
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Nama Pengunjung</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $ticket->visitor->name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">NIK</span>
                            <span class="font-bold text-ink dark:text-white font-mono text-right">{{ $ticket->visitor->nik }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Instansi</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $ticket->counter->department->name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Loket Pelayanan</span>
                            <span class="font-bold text-primary dark:text-accent-teal text-right">{{ $ticket->counter->name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Jenis Layanan</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $ticket->service->name }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted dark:text-on-dark-soft font-medium">Waktu Cetak</span>
                            <span class="font-bold text-ink dark:text-white text-right">{{ $ticket->created_at->translatedFormat('d M Y · H:i') }}</span>
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
                                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" required
                                           placeholder="Contoh: 1373021408990002"
                                           class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 font-mono focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-bold text-ink dark:text-white font-display">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="Contoh: Ahmad Hidayat"
                                           class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-bold text-ink dark:text-white font-display">Nomor Telepon / WhatsApp</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
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
                                    <select id="department_id" name="department_id" x-model="selectedDepartmentId" @change="resetService()" required
                                            class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                        <option value="" disabled>-- Pilih Instansi / Lembaga --</option>
                                        <template x-for="dept in departments" :key="dept.id">
                                            <option :value="dept.id" x-text="dept.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            {{-- Service Select (Cascading) --}}
                            <div class="space-y-2" x-show="selectedDepartmentId" x-cloak>
                                <label for="service_id" class="block text-sm font-bold text-ink dark:text-white font-display">Jenis Layanan</label>
                                <div class="relative">
                                    <select id="service_id" name="service_id" x-model="selectedServiceId" required
                                            class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                        <option value="" disabled>-- Pilih Layanan --</option>
                                        <template x-for="svc in filteredServices" :key="svc.id">
                                            <option :value="svc.id" x-text="svc.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            {{-- Counter Select (Cascading) --}}
                            <div class="space-y-2" x-show="selectedDepartmentId" x-cloak>
                                <label for="counter_id" class="block text-sm font-bold text-ink dark:text-white font-display">Loket / Counter Pelayanan</label>
                                <div class="relative">
                                    <select id="counter_id" name="counter_id" x-model="selectedCounterId" required
                                            class="w-full h-11 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                        <option value="" disabled>-- Pilih Loket --</option>
                                        <template x-for="cnt in filteredCounters" :key="cnt.id">
                                            <option :value="cnt.id" x-text="`${cnt.name} (Loket ${cnt.counter_number})`"></option>
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
                     :class="selectedCounterId ? 'border-primary dark:border-accent-teal shadow-md' : ''">
                    <div class="bg-linear-to-r from-primary to-primary-hover px-5 py-3 text-white font-display text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>Preview Draf Karcis</span>
                        <span x-show="selectedCounterId" class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
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
                                <div x-show="selectedServiceId">
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Jenis Layanan</span>
                                    <span class="font-bold text-primary dark:text-accent-teal" x-text="selectedService ? selectedService.name : ''"></span>
                                </div>
                                <div x-show="selectedCounterId">
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Loket / Counter</span>
                                    <span class="font-bold text-ink dark:text-white" x-text="selectedCounter ? `${selectedCounter.name} (Loket ${selectedCounter.counter_number})` : ''"></span>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto format NIK input: only numbers, max 16 digits
            const nikField = document.getElementById('nik');
            if (nikField) {
                nikField.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 16);
                });
            }

            // Auto format phone input: only numbers, max 15 digits
            const phoneField = document.getElementById('phone');
            if (phoneField) {
                phoneField.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 15);
                });
            }
        });
    </script>
@endpush
