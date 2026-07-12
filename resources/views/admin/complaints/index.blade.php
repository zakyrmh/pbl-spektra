@extends('layouts.private')

@section('title', 'Manajemen Pengaduan Warga — MPP Kota Sawahlunto')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-16">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline dark:border-white/10 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-white font-display tracking-tight">Manajemen Pengaduan Warga</h1>
            <p class="text-sm text-muted dark:text-on-dark-soft font-body mt-1">Kelola dan perbarui status kendala atau pengaduan yang diajukan oleh pengunjung MPP Kota Sawahlunto.</p>
        </div>
    </div>



    <!-- Data Table Card -->
    <div class="bg-canvas dark:bg-surface-dark-elevated border border-hairline dark:border-white/10 rounded-lg overflow-hidden shadow-xs">
        <div class="p-6 border-b border-hairline-soft dark:border-white/5 flex items-center justify-between">
            <h3 class="font-bold text-ink dark:text-white font-display text-base">Daftar Pengaduan</h3>
            <span class="text-xs text-muted dark:text-on-dark-soft font-body font-bold">Total: {{ $complaints->total() }} aduan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-soft dark:bg-white/2 text-muted dark:text-on-dark-soft text-xs font-bold font-display uppercase tracking-wider">
                        <th class="py-3.5 px-6">Pengirim / NIK</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Subjek & Detail Aduan</th>
                        <th class="py-3.5 px-6">Tanggal Masuk</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft dark:divide-white/5 font-body text-sm text-body dark:text-on-dark-soft">
                    @forelse ($complaints as $complaint)
                        <tr class="hover:bg-surface-soft/40 dark:hover:bg-white/2 transition-colors">
                            <!-- Pengirim / NIK -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-ink dark:text-white font-display">
                                    {{ $complaint->user->name ?? 'Warga / Pengunjung' }}
                                </div>
                                <div class="text-xs text-muted dark:text-on-dark-soft font-mono mt-0.5">
                                    NIK: {{ $complaint->user->nik ?? '-' }}
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium font-display 
                                    @if($complaint->category === 'Pelayanan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($complaint->category === 'Fasilitas') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                    @elseif($complaint->category === 'Sistem/Teknis') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400
                                    @endif">
                                    {{ $complaint->category }}
                                </span>
                            </td>

                            <!-- Subjek & Detail -->
                            <td class="py-4 px-6 max-w-xs sm:max-w-md">
                                <div class="font-semibold text-ink dark:text-white font-display truncate" title="{{ $complaint->subject }}">
                                    {{ $complaint->subject }}
                                </div>
                                <div class="text-xs text-muted dark:text-on-dark-soft mt-1 leading-relaxed whitespace-pre-line">
                                    {{ $complaint->content }}
                                </div>
                            </td>

                            <!-- Tanggal Masuk -->
                            <td class="py-4 px-6 text-xs text-muted dark:text-on-dark-soft font-mono">
                                {{ $complaint->created_at->translatedFormat('d M Y, H:i') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6">
                                @if($complaint->status === 'Pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif($complaint->status === 'Processing')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Diproses
                                    </span>
                                @elseif($complaint->status === 'Resolved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-pill text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        {{ $complaint->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Ubah Status (Dropdown Form) -->
                            <td class="py-4 px-6 text-right">
                                <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-xs bg-canvas dark:bg-white/5 border border-hairline dark:border-white/15 text-ink dark:text-white rounded-md py-1.5 pl-2 pr-8 focus:border-primary dark:focus:border-accent-teal focus:outline-none focus:ring-2 focus:ring-primary/12 dark:focus:ring-accent-teal/20 transition-all cursor-pointer font-body">
                                        <option value="Pending" {{ $complaint->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Processing" {{ $complaint->status === 'Processing' ? 'selected' : '' }}>Diproses</option>
                                        <option value="Resolved" {{ $complaint->status === 'Resolved' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-muted dark:text-on-dark-soft">
                                <div class="w-12 h-12 bg-surface-soft dark:bg-white/5 text-muted rounded-full flex items-center justify-center mx-auto border border-hairline dark:border-white/5 mb-3 animate-pulse">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                    </svg>
                                </div>
                                <p class="font-bold text-ink dark:text-white font-display">Tidak Ada Pengaduan Warga</p>
                                <p class="text-xs mt-1">Saat ini belum ada pengaduan yang diajukan oleh pengunjung.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($complaints->hasPages())
            <div class="px-6 py-4 border-t border-hairline-soft dark:border-white/5 bg-surface-soft/20 dark:bg-white/1">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
