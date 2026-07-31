@extends('layouts.private')

@section('title', 'Ambil Antrean Baru - MPP Kota Sawahlunto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8 pb-16" 
         x-data="{
             departments: @js($departments),
             sessions: @js($sessions ?? ['Sesi 1', 'Sesi 2']),
             selectedDepartmentId: '{{ old('department_id', '') }}',
             nextDepartmentIds: @js(old('next_department_ids', [])),
             keperluan: '{{ old('keperluan', '') }}',
             selectedDate: '{{ old('booking_date', '') }}',
             selectedSession: '{{ old('session_name', '') }}',
             isPriority: @js($isUserPriority ?? false),
             
             get availableDates() {
                 if (!this.selectedDepartmentId) return [];
                 let dept = this.departments.find(d => d.id == this.selectedDepartmentId);
                 if (!dept) return [];
                 
                 // Fallback/Default: generate next 3 working days dynamically
                 let dates = [];
                 let current = new Date();
                 while (dates.length < 3) {
                     let day = current.getDay();
                     if (day !== 0 && day !== 6) { // Skip Sunday (0) and Saturday (6)
                         let yyyy = current.getFullYear();
                         let mm = String(current.getMonth() + 1).padStart(2, '0');
                         let dd = String(current.getDate()).padStart(2, '0');
                         dates.push(`${yyyy}-${mm}-${dd}`);
                     }
                     current.setDate(current.getDate() + 1);
                 }
                 return dates;
             },
             
             get selectedDepartment() {
                 return this.departments.find(d => d.id == this.selectedDepartmentId) || null;
             },

             get availableNextDepartments() {
                 if (!this.selectedDepartmentId) return [];
                 return this.departments.filter(d => d.id != this.selectedDepartmentId);
             },

             toggleNextDept(deptId) {
                 const idStr = String(deptId);
                 if (this.nextDepartmentIds.includes(idStr)) {
                     this.nextDepartmentIds = this.nextDepartmentIds.filter(id => id !== idStr);
                 } else {
                     this.nextDepartmentIds.push(idStr);
                 }
             },

             get selectedNextDepartments() {
                 return this.departments.filter(d => this.nextDepartmentIds.includes(String(d.id)));
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
             
             resetDate() {
                 this.selectedDate = '';
                 this.selectedSession = '';
                 this.nextDepartmentIds = this.nextDepartmentIds.filter(id => id != this.selectedDepartmentId);
             },
             
             isSessionDisabled(sessionName) {
                 if (!this.selectedDate) return false;
                 const today = new Date();
                 const yyyy = today.getFullYear();
                 const mm = String(today.getMonth() + 1).padStart(2, '0');
                 const dd = String(today.getDate()).padStart(2, '0');
                 const todayStr = `${yyyy}-${mm}-${dd}`;
                 
                 if (this.selectedDate === todayStr) {
                     const currentHour = today.getHours();
                     if (sessionName === 'Sesi 1' && currentHour >= 12) {
                         return true;
                     }
                     if (sessionName === 'Sesi 2' && currentHour >= 15) {
                         return true;
                     }
                 }
                 return false;
             }
         }">
         
        {{-- Header --}}
        <div class="border-b border-hairline dark:border-white/10 pb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Ambil Nomor Antrean Mandiri</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Buat reservasi pelayanan MPP Sawahlunto secara online sebelum kedatangan Anda.</p>
        </div>

        @if($hasActiveBooking)
            <div class="flex items-start gap-3 p-4 bg-amber-500/10 border border-amber-500/30 rounded-lg shadow-xs" role="alert">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-400 font-display">Antrean Aktif Terdeteksi</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400/80 font-body mt-0.5">Anda masih memiliki tiket/booking aktif. Anda tetap dapat mengakses halaman ini untuk melihat simulasi pilihan tiket, namun tombol <strong>Buat Reservasi Antrean</strong> telah dinonaktifkan.</p>
                </div>
            </div>
        @endif

        {{-- Session Flash / Validation Alerts --}}
        @if ($errors->has('error'))
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg animate-pulse" role="alert">
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
            <div class="flex items-start gap-3 p-4 bg-status-skipped/10 border border-status-skipped/30 rounded-lg animate-pulse" role="alert">
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
                                        @change="resetDate()"
                                        class="w-full h-12 text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md px-4 pr-10 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer">
                                    <option value="" disabled class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white">-- Pilih Instansi / Lembaga --</option>
                                    <template x-for="dept in departments" :key="dept.id">
                                        <option :value="dept.id" x-text="dept.name" class="bg-canvas dark:bg-surface-dark-elevated text-ink dark:text-white"></option>
                                    </template>
                                </select>
                            </div>
                            @error('department_id')
                                <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body">Instansi penyedia layanan publik utama di lingkungan MPP Kota Sawahlunto.</p>
                        </div>

                        {{-- Multi-Gerai Waterfall Section --}}
                        <div class="space-y-2 pt-2 border-t border-dashed border-hairline dark:border-white/10" x-cloak x-show="selectedDepartmentId">
                            <label class="text-sm font-bold text-ink dark:text-white font-display flex items-center justify-between">
                                <span>Pilih Gerai Lanjutan (Waterfall Queue)</span>
                                <span class="text-[11px] font-normal px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-accent-teal rounded-full">Multi-Layanan</span>
                            </label>
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body mb-2">
                                Pilih gerai tambahan jika Anda butuh pelayanan di >1 gerai. Tiket ke gerai lanjutan akan <strong>diterbitkan otomatis</strong> setelah gerai pertama selesai.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="dept in availableNextDepartments" :key="dept.id">
                                    <label class="flex items-center gap-2.5 p-3 rounded-lg border border-hairline dark:border-white/10 bg-surface-soft/50 dark:bg-white/5 cursor-pointer hover:border-primary/50 dark:hover:border-accent-teal/50 transition-all"
                                           :class="nextDepartmentIds.includes(String(dept.id)) ? 'border-primary dark:border-accent-teal bg-primary/5 dark:bg-accent-teal/10 font-bold' : ''">
                                        <input type="checkbox" 
                                               :value="dept.id" 
                                               :checked="nextDepartmentIds.includes(String(dept.id))"
                                               @change="toggleNextDept(dept.id)"
                                               class="w-4 h-4 text-primary dark:text-accent-teal border-hairline rounded focus:ring-primary cursor-pointer">
                                        <span class="text-xs text-ink dark:text-white" x-text="dept.name"></span>
                                    </label>
                                </template>
                            </div>
                            <template x-for="id in nextDepartmentIds" :key="id">
                                <input type="hidden" name="next_department_ids[]" :value="id">
                            </template>
                        </div>

                        {{-- Field 2: Keperluan --}}
                        <div class="space-y-2">
                            <label for="keperluan" class="block text-sm font-bold text-ink dark:text-white font-display">
                                2. Ketik Keperluan
                            </label>
                            <div class="relative">
                                <textarea id="keperluan" 
                                          name="keperluan"
                                          x-model="keperluan"
                                          rows="3"
                                          placeholder="Ketik keperluan kunjungan Anda secara detail..."
                                          required
                                          class="w-full text-sm bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md p-4 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-3 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all font-body"></textarea>
                            </div>
                            @error('keperluan')
                                <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-muted dark:text-on-dark-soft font-body">Misal: Pengurusan KTP hilang, legalisir KK, dll.</p>
                        </div>

                        {{-- Field 3: Booking Date & Session --}}
                        <div class="space-y-4" x-cloak x-show="selectedDepartmentId">
                            <label class="block text-sm font-bold text-ink dark:text-white font-display">
                                3. Pilih Tanggal dan Sesi Booking
                            </label>
                            
                            {{-- Hidden Form Inputs --}}
                            <input type="hidden" name="booking_date" :value="selectedDate">
                            <input type="hidden" name="session_name" :value="selectedSession">
                            
                            {{-- Date Badges --}}
                            <div class="text-xs font-body text-muted dark:text-on-dark-soft">
                                <span class="font-semibold block mb-2 text-ink dark:text-white">Pilih Tanggal:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="d in availableDates" :key="d">
                                        <button type="button"
                                                @click="selectedDate = d; selectedSession = ''" 
                                                class="px-3 py-1.5 bg-surface-soft hover:bg-primary/10 dark:bg-white/5 dark:hover:bg-accent-teal/15 rounded text-[11px] font-mono cursor-pointer transition-colors border border-hairline dark:border-white/10"
                                                :class="selectedDate === d ? 'border-primary dark:border-accent-teal text-primary dark:text-accent-teal bg-primary/5 dark:bg-accent-teal/5 font-bold' : ''">
                                            <span x-text="formatDate(d)"></span>
                                        </button>
                                    </template>
                                    <template x-if="availableDates.length === 0">
                                        <p class="text-status-skipped font-semibold">Tidak ada jadwal pelayanan terbuka dengan kuota tersedia saat ini.</p>
                                    </template>
                                </div>
                                @error('booking_date')
                                    <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Session Badges --}}
                            <div class="text-xs font-body text-muted dark:text-on-dark-soft" x-show="selectedDate">
                                <span class="font-semibold block mb-2 text-ink dark:text-white">Pilih Sesi Pelayanan:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="s in sessions" :key="s">
                                        <button type="button"
                                                @click="selectedSession = s" 
                                                :disabled="isSessionDisabled(s)"
                                                class="px-3 py-1.5 rounded text-[11px] font-mono cursor-pointer transition-colors border border-hairline dark:border-white/10"
                                                :class="isSessionDisabled(s) 
                                                    ? 'opacity-40 cursor-not-allowed bg-gray-200 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500'
                                                    : (selectedSession === s 
                                                        ? 'border-primary dark:border-accent-teal text-primary dark:text-accent-teal bg-primary/5 dark:bg-accent-teal/5 font-bold' 
                                                        : 'bg-surface-soft hover:bg-primary/10 dark:bg-white/5 dark:hover:bg-accent-teal/15')">
                                            <span x-text="s"></span>
                                        </button>
                                    </template>
                                </div>
                                @error('session_name')
                                    <p class="text-xs text-status-skipped mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4 border-t border-hairline dark:border-white/10" x-cloak x-show="selectedDepartmentId && selectedDate && selectedSession">
                            @if($hasActiveBooking)
                                <button type="button" disabled class="w-full sm:w-auto h-11 inline-flex items-center justify-center gap-2 px-8 bg-gray-300 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500 font-semibold rounded-pill cursor-not-allowed border border-hairline dark:border-white/5">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Tombol Dinonaktifkan (Ada Tiket Aktif)
                                </button>
                            @else
                                <button type="submit" class="w-full sm:w-auto h-11 inline-flex items-center justify-center gap-2 px-8 bg-primary hover:bg-primary-hover text-white font-semibold rounded-pill shadow-md hover:shadow-lg transition-all focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Buat Reservasi Antrean
                                </button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>

            {{-- RIGHT: Live Draft / Instructions Card --}}
            <div class="space-y-6">
                
                {{-- Live Ticket Draft Preview --}}
                <div class="bg-canvas dark:bg-surface-dark-elevated rounded-lg border border-hairline dark:border-white/10 shadow-sm overflow-hidden"
                     :class="(selectedDate && selectedSession) ? 'border-primary dark:border-accent-teal shadow-md' : ''">
                    <div class="bg-linear-to-r from-primary to-primary-hover px-5 py-3 text-white font-display text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>Preview Tiket Anda</span>
                        <span x-show="selectedDate" class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
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
                                <p class="text-xs text-muted dark:text-on-dark-soft font-body leading-relaxed max-w-45 mx-auto">Lengkapi pilihan formulir di sebelah kiri untuk melihat draf tiket Anda.</p>
                            </div>
                        </template>

                        <template x-if="selectedDepartmentId">
                            <div class="space-y-4 text-sm font-body">
                                <div x-show="isPriority" class="p-2.5 bg-amber-500/10 text-amber-800 dark:text-accent-gold border border-amber-500/20 rounded font-bold text-xs flex items-center gap-1.5 uppercase">
                                    <svg class="w-4.5 h-4.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Layanan Prioritas Kelompok Rentan
                                </div>

                                <div>
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Instansi Utama</span>
                                    <span class="font-bold text-ink dark:text-white" x-text="selectedDepartment ? selectedDepartment.name : ''"></span>
                                </div>

                                <div x-show="selectedNextDepartments.length > 0" class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg space-y-1.5">
                                    <span class="text-[10px] font-bold text-blue-600 dark:text-accent-teal uppercase tracking-wider block font-display">Alur Kunjungan Multi-Gerai</span>
                                    <div class="text-xs text-ink dark:text-white space-y-1">
                                        <div class="flex items-center gap-1.5 font-bold text-primary dark:text-accent-teal">
                                            <span class="w-4 h-4 rounded-full bg-primary/20 text-primary text-[10px] flex items-center justify-center font-mono">1</span>
                                            <span x-text="selectedDepartment ? selectedDepartment.name : ''"></span>
                                            <span class="text-[9px] px-1.5 py-0.5 bg-primary/10 text-primary rounded font-mono">Pertama</span>
                                        </div>
                                        <template x-for="(dept, idx) in selectedNextDepartments" :key="dept.id">
                                            <div class="flex items-center gap-1.5 text-muted dark:text-on-dark-soft pl-0.5">
                                                <span class="w-4 h-4 rounded-full bg-gray-200 dark:bg-white/10 text-muted dark:text-white text-[10px] flex items-center justify-center font-mono" x-text="idx + 2"></span>
                                                <span x-text="dept.name"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 bg-blue-500/10 text-blue-600 dark:text-accent-teal rounded font-mono">Terusan (On Hold)</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <div x-show="keperluan">
                                    <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Keperluan</span>
                                    <span class="font-bold text-ink dark:text-white wrap-break-word" x-text="keperluan"></span>
                                </div>
                                
                                <div x-show="selectedDate" class="pt-3 border-t border-hairline dark:border-white/10 space-y-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Hari & Tanggal</span>
                                        <span class="font-semibold text-primary dark:text-accent-teal" x-text="formatDate(selectedDate)"></span>
                                    </div>
                                    <div x-show="selectedSession">
                                        <span class="text-[10px] font-bold text-muted dark:text-on-dark-soft uppercase tracking-wider block font-display">Sesi Pelayanan</span>
                                        <span class="font-semibold text-primary dark:text-accent-teal" x-text="selectedSession"></span>
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
