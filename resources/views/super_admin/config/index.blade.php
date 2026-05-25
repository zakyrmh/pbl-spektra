@extends('layouts.private')

@section('title', 'Konfigurasi Gerai & Loket - MPP Sawahlunto')

@section('content')
<div class="space-y-6 pb-16" x-data="{ activeTab: '{{ request('tab', 'gerai') }}' }">

    {{-- Top Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card 1: Total Gerai --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between transition-all duration-300 hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Gerai Instansi</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white font-mono">{{ $totalDepartments }}</h3>
                <p class="text-xs text-gray-500 mt-1">Dinas/lembaga terintegrasi</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        {{-- Card 2: Total Loket --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between transition-all duration-300 hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Loket Aktif</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white font-mono">{{ $totalCountersActive }}</h3>
                <p class="text-xs text-gray-500 mt-1">Siap melayani pengunjung</p>
            </div>
            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- Card 3: Petugas Standby --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between transition-all duration-300 hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Petugas Standby</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white font-mono">{{ $totalStaffStandby }}</h3>
                <p class="text-xs text-gray-500 mt-1">Belum di-plot ke loket</p>
            </div>
            <div class="w-12 h-12 bg-violet-50 dark:bg-violet-900/30 rounded-full flex items-center justify-center text-violet-600 dark:text-violet-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <nav class="flex space-x-6 -mb-px" aria-label="Tabs">
            <button @click="activeTab = 'gerai'"
                :class="activeTab === 'gerai' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-4 px-1 border-b-2 font-semibold text-sm transition-all duration-200 focus:outline-none">
                Gerai / Instansi
            </button>
            <button @click="activeTab = 'loket'"
                :class="activeTab === 'loket' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-4 px-1 border-b-2 font-semibold text-sm transition-all duration-200 focus:outline-none">
                Loket Fisik
            </button>
            <button @click="activeTab = 'layanan'"
                :class="activeTab === 'layanan' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-4 px-1 border-b-2 font-semibold text-sm transition-all duration-200 focus:outline-none">
                Jenis Layanan
            </button>
        </nav>

        {{-- Action Button --}}
        <div>
            <button x-show="activeTab === 'gerai'" onclick="openAddGeraiModal()"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm transition-all duration-200 gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Gerai
            </button>
            <button x-show="activeTab === 'loket'" onclick="openAddLoketModal()"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm transition-all duration-200 gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Loket
            </button>
            <button x-show="activeTab === 'layanan'" onclick="openAddLayananModal()"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm transition-all duration-200 gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Layanan
            </button>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div>
        {{-- Tab 1: Gerai/Instansi --}}
        <div x-show="activeTab === 'gerai'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">Logo</th>
                            <th class="px-6 py-4">Nama Gerai</th>
                            <th class="px-6 py-4">Kode Prefix</th>
                            <th class="px-6 py-4 text-center">Jumlah Loket</th>
                            <th class="px-6 py-4 text-center">Jumlah Layanan</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse($departments as $dept)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4">
                                @if($dept->logo)
                                <img src="{{ asset('storage/' . $dept->logo) }}" alt="Logo {{ $dept->name }}" class="w-10 h-10 object-contain rounded-lg bg-gray-50 p-1">
                                @else
                                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($dept->name, 0, 2) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800 dark:text-white">
                                {{ $dept->name }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                                <span class="bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-lg text-xs">{{ $dept->inisial }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold font-mono">
                                {{ $dept->counters_count }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold font-mono">
                                {{ $dept->services_count }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $dept->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button onclick="openEditGeraiModal({{ json_encode($dept) }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                <form action="{{ route('config.departments.destroy', $dept) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Gerai ini? Semua loket dan layanan terkait akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold pl-1">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                Belum ada data Gerai terdaftar. Klik "Tambah Gerai" untuk menambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 2: Loket Fisik --}}
        <div x-show="activeTab === 'loket'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">Nama Loket</th>
                            <th class="px-6 py-4">Gerai Instansi</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Petugas Jaga</th>
                            <th class="px-6 py-4">Layanan Ditangani</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse($counters as $counter)
                        @php
                            $activeOfficer = $counter->users->first();
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800 dark:text-white">
                                {{ $counter->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $counter->department->name }}</span>
                                <span class="text-xs text-gray-400 block font-mono">Prefix: {{ $counter->department->inisial }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $counter->location ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($activeOfficer)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-violet-100 text-violet-700 rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ substr($activeOfficer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold block text-xs text-gray-800 dark:text-white leading-none">{{ $activeOfficer->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $activeOfficer->email }}</span>
                                    </div>
                                </div>
                                @else
                                <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-md font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Kosong / Standby</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse($counter->services as $svc)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                        {{ $svc->name }}
                                    </span>
                                    @empty
                                    <span class="text-xs text-gray-400">Melayani semua jenis</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('config.counters.toggle-status', $counter) }}" method="POST" class="inline-block" id="form-status-{{ $counter->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="document.getElementById('form-status-{{ $counter->id }}').submit()"
                                        class="text-xs font-semibold rounded-lg border-0 py-1 pl-2 pr-7 ring-1 ring-inset focus:ring-2 focus:ring-blue-600
                                        @if($counter->status === 'aktif')
                                            bg-green-50 text-green-700 ring-green-600/20
                                        @elseif($counter->status === 'istirahat')
                                            bg-amber-50 text-amber-700 ring-amber-600/20
                                        @else
                                            bg-red-50 text-red-700 ring-red-600/20
                                        @endif">
                                        <option value="aktif" {{ $counter->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="istirahat" {{ $counter->status === 'istirahat' ? 'selected' : '' }}>Istirahat</option>
                                        <option value="nonaktif" {{ $counter->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button onclick="openEditLoketModal({{ json_encode($counter) }}, {{ $activeOfficer ? $activeOfficer->id : 'null' }}, {{ json_encode($counter->services->pluck('id')) }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                <form action="{{ route('config.counters.destroy', $counter) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus loket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold pl-1">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                Belum ada data Loket terdaftar. Klik "Tambah Loket" untuk menambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 3: Jenis Layanan --}}
        <div x-show="activeTab === 'layanan'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">Nama Layanan</th>
                            <th class="px-6 py-4">Gerai Instansi</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse($services as $svc)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800 dark:text-white">
                                {{ $svc->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $svc->department->name }}</span>
                                <span class="text-xs text-gray-400 block font-mono">Prefix: {{ $svc->department->inisial }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-sm truncate">
                                {{ $svc->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button onclick="openEditLayananModal({{ json_encode($svc) }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                <form action="{{ route('config.services.destroy', $svc) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis layanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold pl-1">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                Belum ada data Layanan terdaftar. Klik "Tambah Layanan" untuk menambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────
     MODAL SYSTEM (Add/Edit dialogs)
     ───────────────────────────────────────────────────────────── --}}

{{-- Modal 1: Gerai (Department) --}}
<div id="gerai-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-gray-500/50 dark:bg-gray-900/70 transition-opacity" onclick="closeGeraiModal()"></div>
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-150 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="gerai-modal-title">Tambah Gerai Instansi</h3>
                <button onclick="closeGeraiModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="gerai-form" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="_method" id="gerai-form-method" value="POST">

                <div>
                    <label for="g-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Nama Instansi <span class="text-red-500">*</span></label>
                    <input type="text" id="g-name" name="name" required class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="e.g. Dinas Kependudukan dan Catatan Sipil">
                </div>

                <div>
                    <label for="g-inisial" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Kode Prefix Antrean <span class="text-red-500">*</span></label>
                    <input type="text" id="g-inisial" name="inisial" required maxlength="6" class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 font-mono uppercase" placeholder="e.g. DDK">
                    <p class="text-[10px] text-gray-400 mt-1">Kode unik max 6 karakter huruf untuk penomoran antrean (e.g. DDK-001).</p>
                </div>

                <div>
                    <label for="g-logo" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Logo Instansi</label>
                    <input type="file" id="g-logo" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[10px] text-gray-400 mt-1">Maksimum ukuran gambar 2MB.</p>
                </div>

                <div>
                    <label for="g-desc" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <textarea id="g-desc" name="description" rows="3" class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="Keterangan mengenai pelayanan gerai..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-150 dark:border-gray-700">
                    <button type="button" onclick="closeGeraiModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Loket (Counter) --}}
<div id="loket-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-gray-500/50 dark:bg-gray-900/70 transition-opacity" onclick="closeLoketModal()"></div>
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-lg p-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-150 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="loket-modal-title">Tambah Loket Fisik</h3>
                <button onclick="closeLoketModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="loket-form" method="POST" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="_method" id="loket-form-method" value="POST">

                <div>
                    <label for="l-department" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Gerai Instansi <span class="text-red-500">*</span></label>
                    <select id="l-department" name="department_id" required onchange="filterServicesByGerai(this.value)"
                        class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->inisial }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="l-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Nama Loket <span class="text-red-500">*</span></label>
                        <input type="text" id="l-name" name="name" required class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="e.g. Loket 1">
                    </div>
                    <div>
                        <label for="l-status" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Status Loket <span class="text-red-500">*</span></label>
                        <select id="l-status" name="status" required class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                            <option value="aktif">Aktif</option>
                            <option value="istirahat">Istirahat</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="l-location" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Lokasi / Meja</label>
                    <input type="text" id="l-location" name="location" class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="e.g. Lantai 1, Baris Kiri">
                </div>

                <div>
                    <label for="l-officer" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Plotting Petugas Jaga</label>
                    <select id="l-officer" name="officer_id" class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Kosong / Standby --</option>
                        @foreach($officers as $officer)
                        <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->email }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Petugas (role operator loket) yang dijadwalkan jaga di meja pelayanan ini.</p>
                </div>

                {{-- Service Mapping (Many-to-Many checklist) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Pemetaan Jenis Layanan</label>
                    <div class="border border-gray-250 dark:border-gray-600 rounded-lg p-3 max-h-40 overflow-y-auto space-y-2 dark:bg-gray-750" id="services-checklist-container">
                        @forelse($services as $svc)
                        <div class="flex items-start gap-2.5 service-checkbox-row" data-dept-id="{{ $svc->department_id }}">
                            <input type="checkbox" name="services[]" value="{{ $svc->id }}" id="svc-chk-{{ $svc->id }}"
                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500/20 mt-0.5">
                            <label for="svc-chk-{{ $svc->id }}" class="text-xs text-gray-700 dark:text-gray-300 select-none">
                                <span class="font-bold block leading-tight">{{ $svc->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $svc->department->name }}</span>
                            </label>
                        </div>
                        @empty
                        <span class="text-xs text-gray-400 italic block py-2 text-center">Silakan tambahkan jenis layanan terlebih dahulu.</span>
                        @endforelse
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1" id="service-helper-text">Pilih jenis layanan spesifik yang dilayani di loket ini. Kosongkan untuk melayani semua jenis.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-150 dark:border-gray-700">
                    <button type="button" onclick="closeLoketModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Layanan (Service) --}}
<div id="layanan-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-gray-500/50 dark:bg-gray-900/70 transition-opacity" onclick="closeLayananModal()"></div>
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-150 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="layanan-modal-title">Tambah Jenis Layanan</h3>
                <button onclick="closeLayananModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="layanan-form" method="POST" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="_method" id="layanan-form-method" value="POST">

                <div>
                    <label for="s-department" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Gerai Instansi <span class="text-red-500">*</span></label>
                    <select id="s-department" name="department_id" required
                        class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="s-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Nama Layanan <span class="text-red-500">*</span></label>
                    <input type="text" id="s-name" name="name" required class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="e.g. Pengambilan Dokumen & KK">
                </div>

                <div>
                    <label for="s-desc" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Deskripsi / Persyaratan</label>
                    <textarea id="s-desc" name="description" rows="3" class="w-full text-sm rounded-lg border border-gray-300 p-2.5 dark:bg-gray-750 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="Keterangan detail persyaratan atau alur layanan..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-150 dark:border-gray-700">
                    <button type="button" onclick="closeLayananModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script Pendukung Modals & Layanan Filter --}}
<script>
    // ── GERAI MODALS ─────────────────────────────────────────
    function openAddGeraiModal() {
        document.getElementById('gerai-modal-title').innerText = 'Tambah Gerai Instansi';
        document.getElementById('gerai-form').action = "{{ route('config.departments.store') }}";
        document.getElementById('gerai-form-method').value = 'POST';
        
        document.getElementById('g-name').value = '';
        document.getElementById('g-inisial').value = '';
        document.getElementById('g-desc').value = '';
        
        document.getElementById('gerai-modal').classList.remove('hidden');
    }

    function openEditGeraiModal(department) {
        document.getElementById('gerai-modal-title').innerText = 'Edit Gerai Instansi';
        document.getElementById('gerai-form').action = "/konfigurasi-gerai-loket/departments/" + department.id;
        document.getElementById('gerai-form-method').value = 'PUT';
        
        document.getElementById('g-name').value = department.name;
        document.getElementById('g-inisial').value = department.inisial;
        document.getElementById('g-desc').value = department.description || '';
        
        document.getElementById('gerai-modal').classList.remove('hidden');
    }

    function closeGeraiModal() {
        document.getElementById('gerai-modal').classList.add('hidden');
    }

    // ── LOKET MODALS ─────────────────────────────────────────
    function openAddLoketModal() {
        document.getElementById('loket-modal-title').innerText = 'Tambah Loket Fisik';
        document.getElementById('loket-form').action = "{{ route('config.counters.store') }}";
        document.getElementById('loket-form-method').value = 'POST';
        
        document.getElementById('l-department').value = '';
        document.getElementById('l-name').value = '';
        document.getElementById('l-location').value = '';
        document.getElementById('l-status').value = 'aktif';
        document.getElementById('l-officer').value = '';
        
        // Reset checkboxes
        const checkboxes = document.querySelectorAll('#services-checklist-container input[type="checkbox"]');
        checkboxes.forEach(chk => chk.checked = false);

        // Show all checkbox options initially
        const rows = document.querySelectorAll('.service-checkbox-row');
        rows.forEach(r => r.style.display = 'flex');

        document.getElementById('loket-modal').classList.remove('hidden');
    }

    function openEditLoketModal(counter, officerId, serviceIds) {
        document.getElementById('loket-modal-title').innerText = 'Edit Loket Fisik';
        document.getElementById('loket-form').action = "/konfigurasi-gerai-loket/counters/" + counter.id;
        document.getElementById('loket-form-method').value = 'PUT';
        
        document.getElementById('l-department').value = counter.department_id;
        document.getElementById('l-name').value = counter.name;
        document.getElementById('l-location').value = counter.location || '';
        document.getElementById('l-status').value = counter.status;
        document.getElementById('l-officer').value = officerId || '';
        
        // Checklist services
        const checkboxes = document.querySelectorAll('#services-checklist-container input[type="checkbox"]');
        checkboxes.forEach(chk => {
            chk.checked = serviceIds.includes(parseInt(chk.value));
        });

        // Filter service checkboxes based on the parent department of counter
        filterServicesByGerai(counter.department_id);

        document.getElementById('loket-modal').classList.remove('hidden');
    }

    function closeLoketModal() {
        document.getElementById('loket-modal').classList.add('hidden');
    }

    /**
     * Filter list checkbox jenis layanan agar HANYA menampilkan layanan milik instansi terpilih.
     */
    function filterServicesByGerai(departmentId) {
        const rows = document.querySelectorAll('.service-checkbox-row');
        const helperText = document.getElementById('service-helper-text');
        
        if (!departmentId) {
            // Jika kosong, tampilkan semua opsi
            rows.forEach(row => {
                row.style.display = 'flex';
            });
            helperText.innerText = 'Pilih jenis layanan spesifik yang dilayani di loket ini. Kosongkan untuk melayani semua jenis.';
            return;
        }

        let matchCount = 0;
        rows.forEach(row => {
            const rowDeptId = row.getAttribute('data-dept-id');
            if (rowDeptId === departmentId.toString()) {
                row.style.display = 'flex';
                matchCount++;
            } else {
                row.style.display = 'none';
                // Uncheck if hidden to prevent submitting services of other departments
                row.querySelector('input[type="checkbox"]').checked = false;
            }
        });

        if (matchCount === 0) {
            helperText.innerHTML = '<span class="text-red-500 font-semibold">Peringatan: Instansi ini belum memiliki jenis layanan terdaftar.</span> Silakan tambahkan layanan di tab "Jenis Layanan" terlebih dahulu.';
        } else {
            helperText.innerText = 'Menampilkan ' + matchCount + ' jenis layanan milik instansi terpilih. Kosongkan untuk melayani semua jenis.';
        }
    }

    // ── LAYANAN MODALS ───────────────────────────────────────
    function openAddLayananModal() {
        document.getElementById('layanan-modal-title').innerText = 'Tambah Jenis Layanan';
        document.getElementById('layanan-form').action = "{{ route('config.services.store') }}";
        document.getElementById('layanan-form-method').value = 'POST';
        
        document.getElementById('s-department').value = '';
        document.getElementById('s-name').value = '';
        document.getElementById('s-desc').value = '';
        
        document.getElementById('layanan-modal').classList.remove('hidden');
    }

    function openEditLayananModal(service) {
        document.getElementById('layanan-modal-title').innerText = 'Edit Jenis Layanan';
        document.getElementById('layanan-form').action = "/konfigurasi-gerai-loket/services/" + service.id;
        document.getElementById('layanan-form-method').value = 'PUT';
        
        document.getElementById('s-department').value = service.department_id;
        document.getElementById('s-name').value = service.name;
        document.getElementById('s-desc').value = service.description || '';
        
        document.getElementById('layanan-modal').classList.remove('hidden');
    }

    function closeLayananModal() {
        document.getElementById('layanan-modal').classList.add('hidden');
    }
</script>
@endsection
