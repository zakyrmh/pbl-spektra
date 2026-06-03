@extends('layouts.private')

@section('title', 'Ambil Antrean Baru - MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8 pb-16" 
         x-data="{
             departments: @js($departments),
             schedules: @js($schedules),
             selectedDepartmentId: '{{ old('department_id', '') }}',
             selectedServiceId: '{{ old('service_id', '') }}',
             selectedScheduleId: '{{ old('schedule_id', '') }}',
             
             get filteredServices() {
                 if (!this.selectedDepartmentId) return [];
                 let dept = this.departments.find(d => d.id == this.selectedDepartmentId);
                 return dept ? dept.services : [];
             },
             
             get filteredSchedules() {
                 if (!this.selectedServiceId) return [];
                 return this.schedules.filter(s => s.service_id == this.selectedServiceId);
             },
             
             get selectedDepartment() {
                 return this.departments.find(d => d.id == this.selectedDepartmentId) || null;
             },
             
             get selectedService() {
                 let services = this.filteredServices;
                 return services.find(s => s.id == this.selectedServiceId) || null;
             },
             
             get selectedSchedule() {
                 let schedules = this.filteredSchedules;
                 return schedules.find(s => s.id == this.selectedScheduleId) || null;
             },
             
             formatDate(dateStr) {
                 if (!dateStr) return '';
                 const dateOnly = dateStr.split('T')[0];
                 const parts = dateOnly.split('-');
                 if (parts.length !== 3) return dateStr;
                 const year = parts[0];
                 const monthIndex = parseInt(parts[1], 10) - 1;
                 const day = parseInt(parts[2], 10);
                 
                 const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                 const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                 
                 const d = new Date(year, monthIndex, day);
                 return `${days[d.getDay()]}, ${day} ${months[monthIndex]} ${year}`;
             },
             
             resetService() {
                 this.selectedServiceId = '';
                 this.resetSchedule();
             },
             
             resetSchedule() {
                 this.selectedScheduleId = '';
             }
         }">
        
        {{-- Header --}}
        <div class="border-b border-hairline dark:border-white/10 pb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Ambil Nomor Antrean Mandiri</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Buat reservasi pelayanan MPP Sawahlunto secara online sebelum kedatangan Anda.</p>
        </div>

        {{-- Session Flash / Validation Alerts --}}
        @if ($errors->has('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Gagal Membuat Reservasi</p>
                    <p class="text-sm text-red-800 dark:text-red-300 font-body mt-0.5">{!! $errors->first('error') !!}</p>
                </div>
            </div>
        @endif

        @if ($errors->any() && !$errors->has('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg" role="alert">
                <svg class="w-5 h-5 text-status-skipped shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-status-skipped font-display">Kesalahan Input Form</p>
                    <ul class="text-sm text-red-800 dark:text-red-300 font-body mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Main Two-Column Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- LEFT: Booking Form (Spans 2 columns) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-canvas dark:bg-surface-dark-elevated p-6 sm:p-8 rounded-lg border border-hairline dark:border-white/10 shadow-sm">
                    
                    <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        {{-- Field 1: Department --}}
                        <div class="space-y-2">
                            <label for="department_id" class="block text-sm font-bold text-ink dark:text-white font-display">
                                1. Pilih Instansi / Lembaga
                            </label>
                            <div class="relative">
                                <select id="department_id" 
                                        name="department_id"
                                        x-model="selectedDepartmentId"
                                        @change="resetService()"
                                        class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                    <option value="" disabled>-- Pilih Instansi / Lembaga --</option>
                                    <template x-for="dept in departments" :key="dept.id">
                                        <option :value="dept.id" x-text="dept.name"></option>
                                    </template>
                                </select>
                            </div>
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body">Instansi penyedia layanan publik di lingkungan MPP Kota Sawahlunto.</p>
                        </div>

                        {{-- Field 2: Service (Cascading) --}}
                        <div class="space-y-2" x-cloak x-show="selectedDepartmentId">
                            <label for="service_id" class="block text-sm font-bold text-ink dark:text-white font-display">
                                2. Pilih Jenis Pelayanan
                            </label>
                            <div class="relative">
                                <select id="service_id" 
                                        name="service_id"
                                        x-model="selectedServiceId"
                                        @change="resetSchedule()"
                                        class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                    <option value="" disabled>-- Pilih Pelayanan --</option>
                                    <template x-for="svc in filteredServices" :key="svc.id">
                                        <option :value="svc.id" x-text="svc.name"></option>
                                    </template>
                                </select>
                            </div>
                            
                            {{-- Service Description Preview --}}
                            <div x-show="selectedService" class="p-3 bg-surface-soft dark:bg-white/5 rounded-md border border-hairline dark:border-white/5 mt-2">
                                <span class="text-[11px] font-bold text-muted dark:text-on-dark-soft uppercase font-display block">Deskripsi Layanan</span>
                                <p class="text-xs text-body dark:text-on-dark-soft mt-1 leading-relaxed font-body" x-text="selectedService ? selectedService.description : '-'"></p>
                            </div>
                        </div>

                        {{-- Field 3: Schedule Slot (Cascading) --}}
                        <div class="space-y-2" x-cloak x-show="selectedServiceId">
                            <label for="schedule_id" class="block text-sm font-bold text-ink dark:text-white font-display">
                                3. Pilih Hari & Sesi Waktu Pelayanan
                            </label>
                            <div class="relative">
                                <select id="schedule_id" 
                                        name="schedule_id"
                                        x-model="selectedScheduleId"
                                        class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                    <option value="" disabled>-- Pilih Jadwal & Sesi --</option>
                                    <template x-for="sch in filteredSchedules" :key="sch.id">
                                        <option :value="sch.id" 
                                                x-text="`${formatDate(sch.date)} - Sesi ${sch.session_name || 'Umum'} (Sisa ${sch.quota_total - sch.quota_used}/${sch.quota_total} Slot)`">
                                        </option>
                                    </template>
                                </select>
                            </div>

                            {{-- Quota indicator / alerts if empty schedules --}}
                            <div x-show="filteredSchedules.length === 0" class="p-3 bg-status-skipped/10 rounded-md border border-status-skipped/20">
                                <p class="text-xs text-status-skipped font-body font-semibold">Tidak ada jadwal pelayanan terbuka yang memiliki kuota tersedia untuk jenis layanan ini saat ini. Silakan coba lagi besok atau pilih layanan lain.</p>
                            </div>

                            <p class="text-xs text-muted dark:text-on-dark-soft font-body" x-show="filteredSchedules.length > 0">
                                Hanya jadwal dengan sisa kuota yang dapat dipilih. Sesi diatur untuk menghindari penumpukan ruang tunggu.
                            </p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4 border-t border-hairline dark:border-white/10" x-cloak x-show="selectedScheduleId">
                            <button type="submit" class="w-full sm:w-auto h-11 inline-flex items-center justify-center gap-2 px-8 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Buat Reservasi Antrean
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            {{-- RIGHT: Live Draft / Instructions Card --}}
            <div class="space-y-6">
                
                {{-- Live Ticket Draft Preview --}}
                <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden"
                     :class="selectedScheduleId ? 'border-primary dark:border-accent-teal shadow-md' : ''">
                    <div class="bg-linear-to-r from-primary to-primary-hover px-5 py-3 text-white font-display text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>Preview Tiket Anda</span>
                        <span x-show="selectedScheduleId" class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Empty State or Filled Data --}}
                        <template x-if="!selectedDepartmentId">
                            <div class="text-center py-8 space-y-3">
                                <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-muted rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed max-w-[180px] mx-auto">Lengkapi pilihan formulir di sebelah kiri untuk melihat draf tiket Anda.</p>
                            </div>
                        </template>

                        <template x-if="selectedDepartmentId">
                            <div class="space-y-4 text-sm font-body">
                                <div>
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Instansi</span>
                                    <span class="font-bold text-ink dark:text-white" x-text="selectedDepartment ? selectedDepartment.name : ''"></span>
                                </div>
                                
                                <div x-show="selectedServiceId">
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Jenis Pelayanan</span>
                                    <span class="font-bold text-primary dark:text-accent-teal" x-text="selectedService ? selectedService.name : ''"></span>
                                </div>
                                
                                <div x-show="selectedScheduleId" class="pt-3 border-t border-hairline dark:border-white/10 grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Hari & Tanggal</span>
                                        <span class="font-semibold text-ink dark:text-white" x-text="selectedSchedule ? formatDate(selectedSchedule.date) : ''"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Sesi Waktu</span>
                                        <span class="font-semibold text-ink dark:text-white" x-text="selectedSchedule ? `Sesi ${selectedSchedule.session_name}` : ''"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- General Rules Info Card --}}
                <div class="bg-canvas dark:bg-surface-dark-elevated p-6 rounded-lg border border-hairline dark:border-white/10 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-ink dark:text-white uppercase tracking-wider font-display border-b border-hairline dark:border-white/10 pb-2">Ketentuan Pelayanan</h3>
                    <ul class="space-y-2.5 text-xs text-muted dark:text-on-dark-soft font-body">
                        <li class="flex gap-2">
                            <span class="text-primary dark:text-accent-teal font-bold select-none shrink-0">•</span>
                            <span>Satu akun warga (NIK) hanya diperbolehkan memiliki maksimal <strong>1 booking aktif (Pending)</strong> per layanan per hari (BR-06).</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-primary dark:text-accent-teal font-bold select-none shrink-0">•</span>
                            <span>Tunjukkan tiket digital atau email konfirmasi kepada petugas Front Office di MPP untuk melakukan Check-In kedatangan.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-primary dark:text-accent-teal font-bold select-none shrink-0">•</span>
                            <span>Harap tiba di MPP Sawahlunto setidaknya <strong>15 menit</strong> sebelum sesi waktu yang Anda pilih dimulai.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

    </div>
@endsection
